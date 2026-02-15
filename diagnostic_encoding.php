<?php
// Script para verificar y arreglar encoding de barberos
require __DIR__ . '/includes/app.php';

echo "=== DIAGNÓSTICO DE ENCODING UTF-8 ===\n\n";

// 1. Verificar datos actuales
echo "1. DATOS ACTUALES EN LA BASE DE DATOS:\n";
$query = "SELECT u.id, u.nombre, u.apellido, 
          HEX(u.nombre) as hex_nombre, 
          CHAR_LENGTH(u.nombre) as char_length,
          LENGTH(u.nombre) as byte_length
          FROM usuarios u
          WHERE id IN (SELECT usuario_id FROM barberos)
          ORDER BY u.id";
$stmt = $db->prepare($query);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($usuarios as $u) {
    echo "  ID: {$u['id']}\n";
    echo "  Nombre: {$u['nombre']} {$u['apellido']}\n";
    echo "  HEX: {$u['hex_nombre']}\n";
    echo "  Chars: {$u['char_length']}, Bytes: {$u['byte_length']}\n";
    echo "  ---\n";
}

// 2. Verificar configuración de MySQL
echo "\n2. CONFIGURACIÓN DE MYSQL:\n";
$stmt = $db->query("SHOW VARIABLES LIKE 'character_set%'");
$vars = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($vars as $var) {
    echo "  {$var['Variable_name']}: {$var['Value']}\n";
}

// 3. Verificar tabla usuarios
echo "\n3. ESTRUCTURA DE TABLA USUARIOS:\n";
$stmt = $db->query("SHOW CREATE TABLE usuarios");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo $result['Create Table'] . "\n";

echo "\n=== FIN DEL DIAGNÓSTICO ===\n";
