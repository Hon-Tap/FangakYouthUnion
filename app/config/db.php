<?php

$DB_HOST = $_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST') ?? 'mysql.railway.internal';
$DB_PORT = $_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT') ?? '3306';
$DB_NAME = $_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') ?? '';
$DB_USER = $_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER') ?? '';
$DB_PASS = $_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD') ?? '';

$conn = null;

try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, (int)$DB_PORT);

    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        $conn = null;
    } else {
        $conn->set_charset("utf8mb4");
    }
} catch (Throwable $e) {
    error_log("Database connection failed: " . $e->getMessage());
    $conn = null;
}