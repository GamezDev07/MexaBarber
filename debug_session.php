<?php
// Debug script to check session and API response
session_start();

echo "=== SESSION DEBUG ===\n";
echo "Session ID: " . session_id() . "\n";
echo "Session barbershop_id: " . (isset($_SESSION['barbershop_id']) ? $_SESSION['barbershop_id'] : 'NOT SET') . "\n";
echo "Session user ID: " . (isset($_SESSION['id']) ? $_SESSION['id'] : 'NOT SET') . "\n";
echo "Session user name: " . (isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'NOT SET') . "\n";
echo "Session login: " . (isset($_SESSION['login']) ? 'true' : 'false') . "\n";
echo "\n";

// Load app
require __DIR__ . '/includes/app.php';

use Model\Barbero;
use Model\Usuario;

echo "=== DATABASE DEBUG ===\n";

// Check if user Jorge Campos exists
$usuario = Usuario::where('nombre', 'Jorge');
if ($usuario) {
    echo "User found: Jorge {$usuario->apellido}\n";
    echo "User ID: {$usuario->id}\n";
    echo "User barbershop_id: " . ($usuario->barbershop_id ?? 'NULL') . "\n\n";
} else {
    echo "User 'Jorge' not found\n\n";
}

// Test with different barbershop IDs
echo "=== TESTING BARBERO QUERIES ===\n";

// Test with barbershop_id = 1
echo "\n1. Testing with barbershop_id = 1:\n";
$barberos1 = Barbero::activosPorBarbershop(1);
echo "   Found: " . count($barberos1) . " barberos\n";
foreach ($barberos1 as $b) {
    echo "   - ID: {$b['id']}, Nombre: {$b['nombre']}\n";
}

// Test with barbershop_id = NULL
echo "\n2. Testing with barbershop_id = NULL:\n";
$barberos_null = Barbero::activosPorBarbershop(null);
echo "   Found: " . count($barberos_null) . " barberos\n";

// Get all barberos regardless of barbershop
echo "\n3. Getting ALL barberos from database:\n";
$query = "SELECT b.id, b.barbershop_id, CONCAT(u.nombre, ' ', u.apellido) as nombre, b.especialidad, b.activo 
          FROM barberos b 
          JOIN usuarios u ON u.id = b.usuario_id";
$stmt = Barbero::$db->prepare($query);
$stmt->execute();
$all_barberos = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "   Total barberos in DB: " . count($all_barberos) . "\n";
foreach ($all_barberos as $b) {
    echo "   - ID: {$b['id']}, Barbershop: {$b['barbershop_id']}, Nombre: {$b['nombre']}, Activo: {$b['activo']}\n";
}

echo "\n=== RECOMMENDATION ===\n";
if (count($barberos1) > 0) {
    echo "✅ Barberos exist with barbershop_id=1\n";
    echo "⚠️  Problem: User session probably doesn't have barbershop_id set\n";
    echo "💡 Solution: User needs to logout and login again\n";
} else {
    echo "❌ No barberos found with barbershop_id=1\n";
    echo "💡 Need to check barberos table\n";
}
