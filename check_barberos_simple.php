<?php
// Script SIMPLIFICADO para verificar datos de barberos
require __DIR__ . '/includes/app.php';

echo "BARBEROS EN LA BASE DE DATOS:\n\n";

$query = "SELECT u.id, u.nombre, u.apellido
          FROM usuarios u
          INNER JOIN barberos b ON b.usuario_id = u.id
          WHERE b.activo = 1
          ORDER BY u.id";

$stmt = $db->prepare($query);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Total encontrado: " . count($usuarios) . "\n\n";

foreach ($usuarios as $u) {
    echo "ID {$u['id']}: {$u['nombre']} {$u['apellido']}\n";
}

// Probar el método del modelo
echo "\n\nPROBANDO Barbero::activosPorBarbershop(1):\n\n";
use Model\Barbero;
$barberos = Barbero::activosPorBarbershop(1);
echo "Total retornado: " . count($barberos) . "\n\n";

foreach ($barberos as $b) {
    echo "ID {$b['id']}: {$b['nombre']} - {$b['especialidad']}\n";
}
