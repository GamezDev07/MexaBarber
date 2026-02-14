<?php

namespace Model;

class Cita extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'citas';
    protected static $columnasDB = ['id', 'fecha', 'hora', 'usuarioId', 'barbero_id', 'estado', 'metodo_pago', 'pago_estado', 'pago_referencia', 'turno', 'barbershop_id'];

    public $id;
    public $fecha;
    public $hora;
    public $usuarioId;
    public $barbero_id;
    public $estado;
    public $metodo_pago;
    public $pago_estado;
    public $pago_referencia;
    public $turno;
    public $barbershop_id;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->fecha = $args['fecha'] ?? '';
        $this->hora = $args['hora'] ?? '';
        $this->usuarioId = $args['usuarioId'] ?? '';
        $this->barbero_id = $args['barbero_id'] ?? null;
        $this->estado = $args['estado'] ?? 'pendiente';
        $this->metodo_pago = $args['metodo_pago'] ?? null;
        $this->pago_estado = $args['pago_estado'] ?? 'pendiente';
        $this->pago_referencia = $args['pago_referencia'] ?? null;
        $this->turno = $args['turno'] ?? null;
        $this->barbershop_id = $args['barbershop_id'] ?? null;
    }

    public static function citasDelUsuario($usuarioId) {
        $query = "SELECT c.id, c.fecha, c.hora, c.estado, c.metodo_pago, c.pago_estado, c.turno, "
                . "CONCAT(u.nombre, ' ', u.apellido) as barbero_nombre, "
                . "COALESCE(string_agg(s.nombre, ', '), '') as servicios, "
                . "COALESCE(SUM(s.precio), 0) as total "
                . "FROM citas c "
                . "LEFT JOIN barberos b ON b.id = c.barbero_id "
                . "LEFT JOIN usuarios u ON u.id = b.usuario_id "
                . "LEFT JOIN citasservicios cs ON cs.citaId = c.id "
                . "LEFT JOIN servicios s ON s.id = cs.servicioId "
                . "WHERE c.usuarioId = ? AND c.estado IN ('pendiente', 'confirmada') "
                . "GROUP BY c.id, c.fecha, c.hora, c.estado, c.metodo_pago, c.pago_estado, c.turno, u.nombre, u.apellido "
                . "ORDER BY c.fecha ASC, c.hora ASC";
        $stmt = self::$db->prepare($query);
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function siguienteTurno($fecha, $barbershopId) {
        $query = "SELECT COALESCE(MAX(turno), 0) + 1 as siguiente FROM citas WHERE fecha = ? AND barbershop_id = ?";
        $stmt = self::$db->prepare($query);
        $stmt->execute([$fecha, $barbershopId]);
        $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $resultado['siguiente'];
    }
}
