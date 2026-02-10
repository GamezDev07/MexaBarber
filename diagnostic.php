<?php
/**
 * Script de Diagnóstico - AppSalón
 * Verifica todos los requisitos necesarios para ejecutar la aplicación
 * 
 * Uso: php diagnostic.php
 */

echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║  AppSalón - Diagnostic Check                          ║\n";
echo "║  " . date('Y-m-d H:i:s') . "                        ║\n";
echo "╚════════════════════════════════════════════════════════╝\n\n";

$checks = [
    'PHP Version' => version_compare(PHP_VERSION, '8.0.0') >= 0,
    'mysqli Extension' => extension_loaded('mysqli'),
    '.env File' => file_exists('.env'),
    'config.php File' => file_exists('includes/config.php'),
    'public Directory' => is_dir('public'),
    'vendor Directory' => is_dir('vendor'),
    'public/build/css' => is_dir('public/build/css'),
    'public/build/js' => is_dir('public/build/js'),
];

echo "📋 REQUISITOS INSTALADOS\n";
echo str_repeat("─", 58) . "\n";

foreach ($checks as $name => $status) {
    $icon = $status ? '✅' : '❌';
    $status_text = $status ? 'OK' : 'FALTA';
    printf("%-30s %s %s\n", $name, $icon, $status_text);
}

echo "\n📦 VERSIONES\n";
echo str_repeat("─", 58) . "\n";
printf("%-30s: %s\n", "PHP Version", PHP_VERSION);
printf("%-30s: %s\n", "JSON Extension", extension_loaded('json') ? 'Sí' : 'No');
printf("%-30s: %s\n", "PDO Extension", extension_loaded('pdo') ? 'Sí' : 'No');

echo "\n🗄️  BASE DE DATOS\n";
echo str_repeat("─", 58) . "\n";

// Cargar configuración
require_once 'includes/config.php';

$db_host = DB_HOST;
$db_user = DB_USER;
$db_name = DB_NAME;

printf("%-30s: %s\n", "Host", $db_host);
printf("%-30s: %s\n", "Usuario", $db_user);
printf("%-30s: %s\n", "Base de Datos", $db_name);

// Intentar conexión
$conn = @mysqli_connect(
    DB_HOST,
    DB_USER,
    DB_PASSWORD,
    DB_NAME
);

if ($conn) {
    echo "\n✅ Conexión MySQL: EXITOSA\n";
    
    // Verificar tablas
    $tables = [];
    $result = mysqli_query($conn, "SHOW TABLES");
    
    if ($result) {
        while ($row = mysqli_fetch_row($result)) {
            $tables[] = $row[0];
        }
        
        echo "\n📊 TABLAS ENCONTRADAS (" . count($tables) . ")\n";
        echo str_repeat("─", 58) . "\n";
        
        $expected_tables = ['usuarios', 'citas', 'servicios', 'cita_servicio'];
        foreach ($expected_tables as $table) {
            $icon = in_array($table, $tables) ? '✅' : '❌';
            printf("%s %-30s\n", $icon, $table);
        }
    }
    
    mysqli_close($conn);
} else {
    echo "\n❌ Conexión MySQL: FALLIDA\n";
    echo "\nErroro: " . mysqli_connect_error() . "\n";
    echo "\n⚠️  SOLUCIONES:\n";
    echo "  1. Asegúrate de que MySQL esté corriendo\n";
    echo "  2. Verifica credenciales en .env\n";
    echo "  3. Importa appsalon_mvc_php.sql\n";
}

echo "\n🔐 CONFIGURACIÓN\n";
echo str_repeat("─", 58) . "\n";

$env_vars = [
    'DB_HOST' => DB_HOST,
    'APP_ENV' => APP_ENV,
    'APP_URL' => APP_URL,
    'MAIL_HOST' => MAIL_HOST,
];

foreach ($env_vars as $key => $value) {
    if ($key === 'MAIL_PASSWORD') {
        $value = '***' . substr($value, -4);
    }
    printf("%-30s: %s\n", $key, $value);
}

echo "\n🚀 SERVIDOR\n";
echo str_repeat("─", 58) . "\n";

// Intentar conectar al servidor local
$server_check = @fsockopen('127.0.0.1', 8000, $errno, $errstr, 1);
if ($server_check) {
    echo "✅ Servidor PHP activo en http://localhost:8000\n";
    fclose($server_check);
} else {
    echo "⏳ Servidor PHP no está activo\n";
    echo "   Para iniciar: php -S 127.0.0.1:8000 -t public\n";
}

echo "\n📝 RECOMENDACIONES\n";
echo str_repeat("─", 58) . "\n";

$issues = [];

if (!file_exists('.env')) {
    $issues[] = "⚠️  Copia .env.example a .env";
}

if (!file_exists('vendor/autoload.php')) {
    $issues[] = "⚠️  Ejecuta: composer install";
}

if (!is_dir('public/build/css') || !file_exists('public/build/css/app.css')) {
    $issues[] = "⚠️  Compila assets: npm run dev";
}

if (!$conn) {
    $issues[] = "❌ Importa BD: appsalon_mvc_php.sql";
}

if (!$server_check) {
    $issues[] = "⏳ Inicia servidor: php -S 127.0.0.1:8000 -t public";
}

if (empty($issues)) {
    echo "✅ ¡Todo está configurado correctamente!\n";
    echo "   Accede a: http://localhost:8000\n";
} else {
    echo "Pending items:\n";
    foreach ($issues as $issue) {
        echo "  " . $issue . "\n";
    }
}

echo "\n" . str_repeat("─", 58) . "\n";
echo "Diagnóstico completado\n\n";
