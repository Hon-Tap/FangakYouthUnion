<?php

declare(strict_types=1);

/**
 * Database Connection (Railway + Local)
 * Stable, predictable, production-safe
 */

$pdo = null;

/**
 * Safe ENV reader
 */
function env(string $key, $default = null)
{
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

    return ($value !== false && $value !== '') ? $value : $default;
}

try {

    /*
    ===============================
    PRIORITY 1 — MYSQL_URL / DATABASE_URL
    ===============================
    */

    $databaseUrl = env('MYSQL_URL') ?? env('DATABASE_URL');

    if ($databaseUrl) {

        $parts = parse_url($databaseUrl);

        if ($parts === false) {
            throw new Exception('Invalid MYSQL_URL');
        }

        $host = $parts['host'] ?? '127.0.0.1';
        $port = $parts['port'] ?? 3306;
        $user = $parts['user'] ?? 'root';
        $pass = $parts['pass'] ?? '';
        $name = ltrim($parts['path'] ?? '/fyu', '/');

        error_log("DB: Using MYSQL_URL connection");

    }

    /*
    ===============================
    PRIORITY 2 — Individual variables
    ===============================
    */

    else {

        $host = env('MYSQLHOST', '127.0.0.1');
        $port = (int) env('MYSQLPORT', 3306);
        $name = env('MYSQLDATABASE', 'fyu');
        $user = env('MYSQLUSER', 'root');
        $pass = env('MYSQLPASSWORD', '');

        error_log("DB: Using individual environment variables");

    }

    /*
    ===============================
    CREATE PDO CONNECTION
    ===============================
    */

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $host,
        $port,
        $name
    );

    $pdo = new PDO(
        $dsn,
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => 5,
        ]
    );

    /*
    ===============================
    HEALTH CHECK
    ===============================
    */

    $pdo->query("SELECT 1");

    error_log("DB STATUS: CONNECTED to {$name}");

} catch (Throwable $e) {

    error_log("DB STATUS: CONNECTION FAILED");
    error_log("DB ERROR: " . $e->getMessage());

    $pdo = null;

}