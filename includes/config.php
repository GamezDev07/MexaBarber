<?php

// Cargar variables de entorno desde .env
function loadEnv($filePath = __DIR__ . '/../.env') {
    if (!file_exists($filePath)) {
        die('Error: Archivo .env no encontrado');
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue; // Ignorar comentarios
        if (strpos($line, '=') === false) continue; // Ignorar líneas sin =
        
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        if (!getenv($key)) {
            putenv("$key=$value");
        }
    }
}

// Cargar configuración
loadEnv();

// Constantes de configuración
define('APP_URL', getenv('APP_URL'));
define('APP_ENV', getenv('APP_ENV'));
define('APP_DEBUG', getenv('APP_DEBUG') === 'true');

// Database
define('DB_HOST', getenv('DB_HOST'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASSWORD', getenv('DB_PASSWORD'));
define('DB_NAME', getenv('DB_NAME'));

// Mail
define('MAIL_HOST', getenv('MAIL_HOST'));
define('MAIL_PORT', getenv('MAIL_PORT'));
define('MAIL_USERNAME', getenv('MAIL_USERNAME'));
define('MAIL_PASSWORD', getenv('MAIL_PASSWORD'));
define('MAIL_FROM', getenv('MAIL_FROM'));

// Stripe (para después)
define('STRIPE_PUBLIC_KEY', getenv('STRIPE_PUBLIC_KEY'));
define('STRIPE_SECRET_KEY', getenv('STRIPE_SECRET_KEY'));
define('STRIPE_WEBHOOK_SECRET', getenv('STRIPE_WEBHOOK_SECRET'));
