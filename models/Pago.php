<?php

namespace Model;

class Pago extends ActiveRecord {
    protected static $tabla = 'pagos';
    protected static $columnasDB = ['id', 'cita_id', 'barbershop_id', 'monto', 'metodo', 'estado', 'referencia_externa', 'datos_pago'];

    public $id;
    public $cita_id;
    public $barbershop_id;
    public $monto;
    public $metodo;
    public $estado;
    public $referencia_externa;
    public $datos_pago;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->cita_id = $args['cita_id'] ?? null;
        $this->barbershop_id = $args['barbershop_id'] ?? '';
        $this->monto = $args['monto'] ?? '0';
        $this->metodo = $args['metodo'] ?? '';
        $this->estado = $args['estado'] ?? 'pendiente';
        $this->referencia_externa = $args['referencia_externa'] ?? '';
        $this->datos_pago = $args['datos_pago'] ?? '';
    }
}
