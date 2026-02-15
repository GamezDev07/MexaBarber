<?php
// Test script to verify barberos API
require __DIR__ . '/includes/app.php';

use Model\Barbero;

echo "=== Testing Barberos API ===\n\n";

// Start session to simulate logged-in user
session_start();
$_SESSION['barbershop_id'] = 1; // Set barbershop_id

echo "1. Testing Barbero::activosPorBarbershop(1):\n";
$barberos = Barbero::activosPorBarbershop(1);

echo "Found " . count($barberos) . " barberos\n\n";

if (count($barberos) > 0) {
    echo "Barberos details:\n";
    foreach($barberos as $barbero) {
        echo "  - ID: {$barbero['id']}, Nombre: {$barbero['nombre']}, Especialidad: {$barbero['especialidad']}\n";
    }
} else {
    echo "No barberos found!\n";
}

echo "\n=== Test Complete ===\n";
