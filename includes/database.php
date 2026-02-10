<?php

try {
    // Detección automática del tipo de BD
    $db_port = getenv('DB_PORT') ?: (getenv('DB_DRIVER') === 'pgsql' ? 5432 : 3306);
    $db_driver = getenv('DB_DRIVER') ?: 'mysql';
    
    if ($db_driver === 'pgsql') {
        // PostgreSQL (Supabase)
        $dsn = "pgsql:host=" . DB_HOST . ";port=" . $db_port . ";dbname=" . DB_NAME;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
    } else {
        // MySQL (Local)
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
    }
    
    $db = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
    
} catch (PDOException $e) {
    echo "Error: No se pudo conectar a la base de datos.";
    echo "<br>Detalles: " . $e->getMessage();
    exit;
}
