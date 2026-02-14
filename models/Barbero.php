<?php

namespace Model;

class Barbero extends ActiveRecord {
    protected static $tabla = 'barberos';
    protected static $columnasDB = ['id', 'usuario_id', 'barbershop_id', 'especialidad', 'activo'];

    public $id;
    public $usuario_id;
    public $barbershop_id;
    public $especialidad;
    public $activo;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->usuario_id = $args['usuario_id'] ?? '';
        $this->barbershop_id = $args['barbershop_id'] ?? '';
        $this->especialidad = $args['especialidad'] ?? '';
        $this->activo = $args['activo'] ?? '1';
    }

    public static function activosPorBarbershop($barbershopId) {
        $query = "SELECT b.id, CONCAT(u.nombre, ' ', u.apellido) as nombre, b.especialidad "
                . "FROM barberos b "
                . "JOIN usuarios u ON u.id = b.usuario_id "
                . "WHERE b.barbershop_id = ? AND b.activo = 1";
        $stmt = self::$db->prepare($query);
        $stmt->execute([$barbershopId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
