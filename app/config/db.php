<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Database Connection for Railway / MySQL
|--------------------------------------------------------------------------
|
| Supported env formats:
| 1. Full URL:
|    - MYSQL_PUBLIC_URL
|    - MYSQL_URL
|    - DATABASE_URL
|
| 2. Individual variables:
|    - MYSQLHOST
|    - MYSQLPORT
|    - MYSQLUSER
|    - MYSQLPASSWORD
|    - MYSQLDATABASE
|
*/

$pdo = null;
$conn = null;

/**
 * Safely get an environment variable from getenv, $_ENV, or $_SERVER.
 */
function env_value(string $key, $default = null)
{
    $value = getenv($key);

    if ($value !== false && $value !== null && $value !== '') {
        return $value;
    }

    if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
        return $_ENV[$key];
    }

    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
        return $_SERVER[$key];
    }

    return $default;
}

/**
 * Parse a database URL into connection parts.
 */
function parse_database_url(string $url): array
{
    $parts = parse_url($url);

    if ($parts === false) {
        return [
            'host' => null,
            'port' => 3306,
            'dbname' => null,
            'user' => null,
            'pass' => null,
        ];
    }

    return [
        'host'   => $parts['host'] ?? null,
        'port'   => $parts['port'] ?? 3306,
        'dbname' => isset($parts['path']) ? ltrim($parts['path'], '/') : null,
        'user'   => $parts['user'] ?? null,
        'pass'   => $parts['pass'] ?? null,
    ];
}

/*
|--------------------------------------------------------------------------
| Try URL-style variables first
|--------------------------------------------------------------------------
*/
$databaseUrl =
    env_value('MYSQL_PUBLIC_URL') ??
    env_value('MYSQL_URL') ??
    env_value('DATABASE_URL');

$DB_HOST = null;
$DB_PORT = 3306;
$DB_NAME = null;
$DB_USER = null;
$DB_PASS = null;

if ($databaseUrl) {
    $parsed = parse_database_url($databaseUrl);

    $DB_HOST = $parsed['host'];
    $DB_PORT = $parsed['port'];
    $DB_NAME = $parsed['dbname'];
    $DB_USER = $parsed['user'];
    $DB_PASS = $parsed['pass'];

    error_log('DB debug: using URL-based database config');
} else {
    /*
    |--------------------------------------------------------------------------
    | Fallback to individual Railway MySQL variables
    |--------------------------------------------------------------------------
    */
    $DB_HOST = env_value('MYSQLHOST');
    $DB_PORT = (int) env_value('MYSQLPORT', 3306);
    $DB_NAME = env_value('MYSQLDATABASE');
    $DB_USER = env_value('MYSQLUSER');
    $DB_PASS = env_value('MYSQLPASSWORD');

    error_log('DB debug: URL vars missing, trying MYSQLHOST/MYSQLPORT/MYSQLUSER/MYSQLPASSWORD/MYSQLDATABASE');
}

/*
|--------------------------------------------------------------------------
| Debug logs
|--------------------------------------------------------------------------
*/
error_log('DB debug: host=' . ($DB_HOST ?: 'missing'));
error_log('DB debug: port=' . ($DB_PORT ?: 'missing'));
error_log('DB debug: db=' . ($DB_NAME ?: 'missing'));
error_log('DB debug: user=' . ($DB_USER ?: 'missing'));
error_log('DB debug: pass=' . ($DB_PASS ? '[set]' : 'missing'));

/*
|--------------------------------------------------------------------------
| Create PDO connection
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
            ]
        );

        $conn = $pdo;

        error_log('DB debug: connection success');
    } catch (Throwable $e) {
        error_log('Database connection failed: ' . $e->getMessage());

        $pdo = null;
        $conn = null;
    }
} else {
    error_log('Database env vars missing or incomplete.');
}