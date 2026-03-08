<?php

$DB_HOST = $_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST') ?? 'mysql.railway.internal';
$DB_PORT = $_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT') ?? '3306';
$DB_NAME = $_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') ?? '';
$DB_USER = $_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER') ?? '';
$DB_PASS = $_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD') ?? '';

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    $pdo = null;
}