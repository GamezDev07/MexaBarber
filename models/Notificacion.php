<?php

namespace Model;

class Notificacion extends ActiveRecord {
    protected static $tabla = 'notificaciones';
    protected static $columnasDB = ['id', 'usuario_id', 'barbershop_id', 'tipo', 'titulo', 'mensaje', 'leida', 'cita_id'];

    public $id;
    public $usuario_id;
    public $barbershop_id;
    public $tipo;
    public $titulo;
    public $mensaje;
    public $leida;
    public $cita_id;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->usuario_id = $args['usuario_id'] ?? '';
        $this->barbershop_id = $args['barbershop_id'] ?? '';
        $this->tipo = $args['tipo'] ?? '';
        $this->titulo = $args['titulo'] ?? '';
        $this->mensaje = $args['mensaje'] ?? '';
        $this->leida = $args['leida'] ?? '0';
        $this->cita_id = $args['cita_id'] ?? null;
    }

    public static function noLeidasPorUsuario($usuarioId) {
        $query = "SELECT * FROM notificaciones WHERE usuario_id = ? AND leida = 0 ORDER BY created_at DESC";
        $stmt = self::$db->prepare($query);
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
