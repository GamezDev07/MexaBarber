<?php
// Direct API test - simulates what the frontend calls
session_start();

echo "=== DIRECT API TEST ===\n\n";

echo "1. Session State:\n";
echo "   - Session ID: " . session_id() . "\n";
echo "   - Login: " . (isset($_SESSION['login']) ? 'YES' : 'NO') . "\n";
echo "   - barbershop_id in session: ";
var_dump($_SESSION['barbershop_id'] ?? 'NOT SET');
echo "\n";

require __DIR__ . '/../includes/app.php';
use Model\Barbero;

echo "2. Testing getBarbershopId():\n";
$result = getBarbershopId();
echo "   - Result: ";
var_dump($result);
echo "   - Is truthy? " . ($result ? 'YES' : 'NO') . "\n";
echo "   - Type: " . gettype($result) . "\n\n";

echo "3. Testing Barbero query:\n";
if ($result) {
    $barberos = Barbero::activosPorBarbershop($result);
    echo "   - Found: " . count($barberos) . " barberos\n";
} else {
    echo "   - SKIPPED because barbershopId is falsy!\n";
}

echo "\n4. Testing with hardcoded 1:\n";
$barberos = Barbero::activosPorBarbershop(1);
echo "   - Found: " . count($barberos) . " barberos\n";

echo "\n=== DIAGNOSIS ===\n";
if (isset($_SESSION['barbershop_id'])) {
    if ($_SESSION['barbershop_id'] === null) {
        echo "❌ Problem: barbershop_id is SET but is NULL\n";
        echo "   The ?? operator doesn't help because the key EXISTS\n";
    } elseif ($_SESSION['barbershop_id'] === 0 || $_SESSION['barbershop_id'] === '0') {
        echo "❌ Problem: barbershop_id is 0 (falsy in if statement)\n";
    } else {
        echo "✅ barbershop_id looks good: " . $_SESSION['barbershop_id'] . "\n";
    }
} else {
    echo "❌ Problem: barbershop_id is not set in session\n";
}
