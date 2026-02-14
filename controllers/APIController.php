<?php

namespace Controllers;

use Model\Barbero;
use Model\Cita;
use Model\CitaServicio;
use Model\Notificacion;
use Model\Servicio;

class APIController {
    public static function index() {
        $servicios = Servicio::all();
        echo json_encode($servicios);
    }

    public static function barberos() {
        $barbershopId = getBarbershopId();
        if($barbershopId) {
            $barberos = Barbero::activosPorBarbershop($barbershopId);
        } else {
            $barberos = [];
        }
        echo json_encode($barberos);
    }

    public static function guardar() {

        // Calcular turno
        $barbershopId = getBarbershopId();
        $turno = null;
        if($barbershopId) {
            $turno = Cita::siguienteTurno($_POST['fecha'], $barbershopId);
        }

        // Almacena la Cita y devuelve el ID
        $cita = new Cita([
            'fecha' => $_POST['fecha'],
            'hora' => $_POST['hora'],
            'usuarioId' => $_POST['usuarioId'],
            'barbero_id' => !empty($_POST['barberoId']) ? $_POST['barberoId'] : null,
            'estado' => 'pendiente',
            'metodo_pago' => $_POST['metodoPago'] ?? null,
            'pago_estado' => 'pendiente',
            'turno' => $turno,
            'barbershop_id' => $barbershopId
        ]);
        $resultado = $cita->guardar();

        $id = $resultado['id'];

        // Almacena los Servicios con el ID de la Cita
        $idServicios = explode(",", $_POST['servicios']);
        foreach($idServicios as $idServicio) {
            $args = [
                'citaId' => $id,
                'servicioId' => $idServicio
            ];
            $citaServicio = new CitaServicio($args);
            $citaServicio->guardar();
        }

        // Notificar al barbero si fue seleccionado
        if(!empty($_POST['barberoId'])) {
            $barbero = Barbero::find($_POST['barberoId']);
            if($barbero) {
                $notificacion = new Notificacion([
                    'usuario_id' => $barbero->usuario_id,
                    'barbershop_id' => $barbershopId,
                    'tipo' => 'nueva_cita',
                    'titulo' => 'Nueva Cita Asignada',
                    'mensaje' => "Tienes una nueva cita el {$_POST['fecha']} a las {$_POST['hora']}",
                    'cita_id' => $id
                ]);
                $notificacion->guardar();
            }
        }

        echo json_encode(['resultado' => $resultado, 'turno' => $turno]);
    }

    public static function eliminar() {

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $cita = Cita::find($id);
            $cita->eliminar();
            header('Location:' . $_SERVER['HTTP_REFERER']);
        }
    }

    // Obtener citas del usuario logueado
    public static function misCitas() {
        session_start();
        isAuth();

        $usuarioId = $_SESSION['id'];
        $citas = Cita::citasDelUsuario($usuarioId);
        echo json_encode($citas);
    }

    // Cancelar cita del usuario
    public static function cancelarCita() {
        session_start();
        isAuth();

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $cita = Cita::find($id);

            // Verificar que la cita pertenece al usuario
            if(!$cita || $cita->usuarioId != $_SESSION['id']) {
                echo json_encode(['resultado' => false, 'mensaje' => 'No autorizado']);
                return;
            }

            // Verificar que la cita es futura (al menos 1 hora antes)
            $fechaHoraCita = strtotime("{$cita->fecha} {$cita->hora}");
            $ahora = time();
            if($fechaHoraCita - $ahora < 3600) {
                echo json_encode(['resultado' => false, 'mensaje' => 'Solo puedes cancelar con al menos 1 hora de anticipación']);
                return;
            }

            $cita->estado = 'cancelada';
            $resultado = $cita->actualizar();

            // Notificar al barbero
            if($cita->barbero_id) {
                $barbero = Barbero::find($cita->barbero_id);
                if($barbero) {
                    $notificacion = new Notificacion([
                        'usuario_id' => $barbero->usuario_id,
                        'barbershop_id' => $cita->barbershop_id,
                        'tipo' => 'cita_cancelada',
                        'titulo' => 'Cita Cancelada',
                        'mensaje' => "La cita del {$cita->fecha} a las {$cita->hora} fue cancelada por el cliente",
                        'cita_id' => $id
                    ]);
                    $notificacion->guardar();
                }
            }

            echo json_encode(['resultado' => $resultado]);
        }
    }

    // Modificar cita del usuario
    public static function modificarCita() {
        session_start();
        isAuth();

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $cita = Cita::find($id);

            // Verificar que la cita pertenece al usuario
            if(!$cita || $cita->usuarioId != $_SESSION['id']) {
                echo json_encode(['resultado' => false, 'mensaje' => 'No autorizado']);
                return;
            }

            // Verificar que faltan al menos 2 horas
            $fechaHoraCita = strtotime("{$cita->fecha} {$cita->hora}");
            $ahora = time();
            if($fechaHoraCita - $ahora < 7200) {
                echo json_encode(['resultado' => false, 'mensaje' => 'Solo puedes modificar con al menos 2 horas de anticipación']);
                return;
            }

            $barberoAnteriorId = $cita->barbero_id;

            // Actualizar campos
            if(!empty($_POST['fecha'])) $cita->fecha = $_POST['fecha'];
            if(!empty($_POST['hora'])) $cita->hora = $_POST['hora'];
            if(isset($_POST['barberoId'])) $cita->barbero_id = !empty($_POST['barberoId']) ? $_POST['barberoId'] : null;

            $resultado = $cita->actualizar();

            // Notificar al barbero nuevo si cambió
            if($cita->barbero_id && $cita->barbero_id != $barberoAnteriorId) {
                $barbero = Barbero::find($cita->barbero_id);
                if($barbero) {
                    $notificacion = new Notificacion([
                        'usuario_id' => $barbero->usuario_id,
                        'barbershop_id' => $cita->barbershop_id,
                        'tipo' => 'cita_reasignada',
                        'titulo' => 'Nueva Cita Reasignada',
                        'mensaje' => "Se te asignó una cita el {$cita->fecha} a las {$cita->hora}",
                        'cita_id' => $id
                    ]);
                    $notificacion->guardar();
                }

                // Notificar al barbero anterior
                if($barberoAnteriorId) {
                    $barberoAnterior = Barbero::find($barberoAnteriorId);
                    if($barberoAnterior) {
                        $notificacion = new Notificacion([
                            'usuario_id' => $barberoAnterior->usuario_id,
                            'barbershop_id' => $cita->barbershop_id,
                            'tipo' => 'cita_reasignada',
                            'titulo' => 'Cita Reasignada',
                            'mensaje' => "La cita del {$cita->fecha} a las {$cita->hora} fue reasignada a otro barbero",
                            'cita_id' => $id
                        ]);
                        $notificacion->guardar();
                    }
                }
            }

            echo json_encode(['resultado' => $resultado]);
        }
    }

    // Obtener notificaciones del usuario
    public static function notificaciones() {
        session_start();
        isAuth();

        $notificaciones = Notificacion::noLeidasPorUsuario($_SESSION['id']);
        echo json_encode($notificaciones);
    }

    // Marcar notificación como leída
    public static function leerNotificacion() {
        session_start();
        isAuth();

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $notificacion = Notificacion::find($id);

            if($notificacion && $notificacion->usuario_id == $_SESSION['id']) {
                $notificacion->leida = 1;
                $resultado = $notificacion->actualizar();
                echo json_encode(['resultado' => $resultado]);
            } else {
                echo json_encode(['resultado' => false]);
            }
        }
    }
}
