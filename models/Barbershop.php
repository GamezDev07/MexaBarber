<?php

namespace Model;

class Barbershop extends ActiveRecord {
    protected static $tabla = 'barbershops';
    protected static $columnasDB = ['id', 'nombre', 'slug', 'email', 'telefono', 'direccion', 'logo', 'activo'];

    public $id;
    public $nombre;
    public $slug;
    public $email;
    public $telefono;
    public $direccion;
    public $logo;
    public $activo;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->slug = $args['slug'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->direccion = $args['direccion'] ?? '';
        $this->logo = $args['logo'] ?? '';
        $this->activo = $args['activo'] ?? '1';
    }
}
