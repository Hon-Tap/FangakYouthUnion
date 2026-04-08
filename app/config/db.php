<?php

declare(strict_types=1);

/**
 * Database Connection (Universal Railway + Local)
 */

$pdo = null;

/**
 * Safe ENV reader - Checks multiple possible naming conventions
 */
function env(string $key, $default = null)
{
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    return ($value !== false && $value !== '') ? $value : $default;
}

try {
    // 1. Check for Full URL String first (Standard Railway)
    $databaseUrl = env('MYSQL_URL') ?? env('DATABASE_URL');

    if ($databaseUrl) {
        $parts = parse_url($databaseUrl);
        if ($parts === false) {
            throw new Exception('Invalid Database URL format');
        }

        $host = $parts['host'] ?? '127.0.0.1';
        $port = $parts['port'] ?? 3306;
        $user = $parts['user'] ?? 'root';
        $pass = $parts['pass'] ?? '';
        $name = ltrim($parts['path'] ?? '/railway', '/');

        error_log("DB: Using URL-based connection");
    } 
    else {
        // 2. Fallback to Individual Variables (Checks both DB_ and MYSQL_ prefixes)
        $host = env('DB_HOST')     ?? env('MYSQLHOST')     ?? '127.0.0.1';
        $port = env('DB_PORT')     ?? env('MYSQLPORT')     ?? 3306;
        $name = env('DB_DATABASE') ?? env('MYSQLDATABASE') ?? 'railway';
        $user = env('DB_USERNAME') ?? env('MYSQLUSER')     ?? 'root';
        $pass = env('DB_PASSWORD') ?? env('MYSQLPASSWORD') ?? '';

        error_log("DB: Using individual environment variables (Host: $host)");
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $host,
        (int)$port,
        $name
    );

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 5,
    ]);

    // Health Check
    $pdo->query("SELECT 1");
    error_log("DB STATUS: CONNECTED SUCCESSFUL");

} catch (Throwable $e) {
    error_log("DB STATUS: CONNECTION FAILED");
    error_log("DB ERROR: " . $e->getMessage());
    $pdo = null;
}