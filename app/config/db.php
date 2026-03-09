<?php

/*
|--------------------------------------------------------------------------
| Database Connection (Railway Compatible)
|--------------------------------------------------------------------------
| This file creates a PDO connection using Railway environment variables.
| It supports both:
|   1. MYSQLHOST style variables
|   2. DATABASE_URL connection string
*/

$pdo = null;
$conn = null;

/*
|--------------------------------------------------------------------------
| Attempt 1: Railway MySQL variables
|--------------------------------------------------------------------------
*/

$DB_HOST = $_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST') ?? null;
$DB_PORT = $_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT') ?? '3306';
$DB_NAME = $_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') ?? null;
$DB_USER = $_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER') ?? null;
$DB_PASS = $_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD') ?? null;

/*
|--------------------------------------------------------------------------
| Attempt 2: DATABASE_URL fallback
|--------------------------------------------------------------------------
*/

if (!$DB_HOST && getenv('DATABASE_URL')) {

    $databaseUrl = getenv('DATABASE_URL');
    $db = parse_url($databaseUrl);

    $DB_HOST = $db['host'] ?? null;
    $DB_PORT = $db['port'] ?? '3306';
    $DB_USER = $db['user'] ?? null;
    $DB_PASS = $db['pass'] ?? null;
    $DB_NAME = isset($db['path']) ? ltrim($db['path'], '/') : null;
}

/*
|--------------------------------------------------------------------------
| Debug Logging
|--------------------------------------------------------------------------
*/

error_log("DB debug: host=" . ($DB_HOST ?: 'missing'));
error_log("DB debug: port=" . ($DB_PORT ?: 'missing'));
error_log("DB debug: db=" . ($DB_NAME ?: 'missing'));
error_log("DB debug: user=" . ($DB_USER ?: 'missing'));
error_log("DB debug: pass=" . ($DB_PASS ? '[set]' : 'missing'));

/*
|--------------------------------------------------------------------------
| Create PDO Connection
|--------------------------------------------------------------------------
*/

if ($DB_HOST && $DB_PORT && $DB_NAME && $DB_USER) {

    try {

        $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";

        $pdo = new PDO(
            $dsn,
            $DB_USER,
            $DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false
            ]
        );

        $conn = $pdo;

        error_log("DB debug: connection success");

    } catch (Throwable $e) {

        error_log("Database connection failed: " . $e->getMessage());

        $pdo = null;
        $conn = null;
    }

} else {

    error_log("Database env vars missing or incomplete.");
}