<?php

$DB_HOST = $_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST') ?? 'mysql.railway.internal';
$DB_PORT = $_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT') ?? '3306';
$DB_NAME = $_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') ?? '';
$DB_USER = $_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER') ?? '';
$DB_PASS = $_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD') ?? '';

$pdo = null;
$conn = null;

error_log("DB debug: host=" . ($DB_HOST ?: 'missing'));
error_log("DB debug: port=" . ($DB_PORT ?: 'missing'));
error_log("DB debug: db=" . ($DB_NAME ?: 'missing'));
error_log("DB debug: user=" . ($DB_USER ?: 'missing'));
error_log("DB debug: pass=" . ($DB_PASS !== '' ? '[set]' : 'missing'));

if ($DB_HOST && $DB_PORT && $DB_NAME && $DB_USER) {
    try {
        $pdo = new PDO(
            "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4",
            $DB_USER,
            $DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
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