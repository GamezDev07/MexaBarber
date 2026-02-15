<?php
// Script PHP para actualizar nombres de barberos
require __DIR__ . '/includes/app.php';

echo "=== ACTUALIZANDO NOMBRES DE BARBEROS ===\n\n";

// Mapeo de correcciones
$correcciones = [
    'raul.garcia@barber.com' => ['nombre' => 'Raúl', 'apellido' => 'García López'],
    'juan.martinez@barber.com' => ['nombre' => 'Juan', 'apellido' => 'Martínez Rodríguez'],
    'pedro.lopez@barber.com' => ['nombre' => 'Pedro', 'apellido' => 'López Sánchez'],
];

$actualizados = 0;

foreach ($correcciones as $email => $datos) {
    $query = "UPDATE usuarios SET nombre = ?, apellido = ? WHERE email = ?";
    $stmt = $db->prepare($query);
    $resultado = $stmt->execute([$datos['nombre'], $datos['apellido'], $email]);

    if ($resultado && $stmt->rowCount() > 0) {
        echo "✅ Actualizado: {$datos['nombre']} {$datos['apellido']} ({$email})\n";
        $actualizados++;
    } else {
        echo "⚠️  No se encontró o no se actualizó: {$email}\n";
    }
}

echo "\n--- Resumen ---\n";
echo "Total actualizados: $actualizados\n\n";

// Verificar los cambios
echo "=== BARBEROS DESPUÉS DE LA ACTUALIZACIÓN ===\n\n";
use Model\Barbero;
$barberos = Barbero::activosPorBarbershop(1);

foreach ($barberos as $b) {
    echo "✂️  {$b['nombre']} - {$b['especialidad']}\n";
}

echo "\n=== COMPLETADO ===\n";
