<?php
require_once 'includes/app.php';

use Model\Barbero;

echo "=== Verificación de Barberos en BD ===\n\n";

// Verificar barberos para barbershop_id = 1
$barberos = Barbero::activosPorBarbershop(1);

echo "Barberos para barbershop_id = 1:\n";
echo "Total encontrados: " . count($barberos) . "\n";

if(count($barberos) > 0) {
    echo "\n✓ Barberos disponibles:\n";
    foreach($barberos as $barbero) {
        echo "  - {$barbero['nombre']} ({$barbero['especialidad']})\n";
    }
    echo "\n¡Los barberos están disponibles en la API!\n";
} else {
    echo "\n✗ No se encontraron barberos en la BD.\n";
    echo "\nDebes ejecutar el siguiente comando de SQL es tu cliente MySQL:\n";
    echo "\nmysql -u root -proot appsalon\n";
    echo "source insert_barberos_prueba.sql;\n\n";
}

echo "\nDone.\n";

