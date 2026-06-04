<?php

declare(strict_types=1);

/**
 * Database Connection
 * Railway + Local Support
 */

$pdo = null;

/**
 * Safe ENV reader
 */
function env(string $key, $default = null)
{
    $value = $_ENV[$key]
        ?? $_SERVER[$key]
        ?? getenv($key);

    return ($value !== false && $value !== '')
        ? $value
        : $default;
}

try {

    /**
     * PRIORITY:
     * Use DB_* variables from Railway PHP service
     */
    $host = env('DB_HOST');
    $port = env('DB_PORT', 3306);
    $database = env('DB_DATABASE');
    $username = env('DB_USERNAME');
    $password = env('DB_PASSWORD');

    /**
     * Fallback to MYSQL* variables
     * if DB_* are unavailable
     */
    if (!$host) {

        $host = env('MYSQLHOST');
        $port = env('MYSQLPORT', 3306);
        $database = env('MYSQLDATABASE');
        $username = env('MYSQLUSER');
        $password = env('MYSQLPASSWORD');
    }

    /**
     * Local Development Fallback
     */
    if (!$host || !$database || !$username) {

        error_log('DB: Using local database fallback');

        $host = '127.0.0.1';
        $port = 3306;
        $database = 'fangak_youth_union';
        $username = 'root';
        $password = '';
    }

    /**
     * Validate configuration
     */
    if (
        empty($host) ||
        empty($database) ||
        empty($username)
    ) {
        throw new Exception('Database environment variables are missing.');
    }

    /**
     * Build DSN
     */
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $host,
        (int)$port,
        $database
    );

    /**
     * Create PDO connection
     */
    $pdo = new PDO($dsn, $username, $password, [

        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

        PDO::ATTR_EMULATE_PREPARES => false,

        PDO::ATTR_TIMEOUT => 5,

        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);

    /**
     * Connection Health Check
     */
    $pdo->query("SELECT 1");

    error_log('DB STATUS: CONNECTED SUCCESSFULLY');

} catch (Throwable $e) {

    error_log('================ DATABASE ERROR ================');
    error_log('Message: ' . $e->getMessage());
    error_log('File: ' . $e->getFile());
    error_log('Line: ' . $e->getLine());
    error_log('================================================');

    $pdo = null;

    die('Database connection failed. Please try again later.');
}