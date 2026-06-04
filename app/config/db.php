<?php

declare(strict_types=1);

/**
 * Database Connection
 * Supports:
 * - Railway MySQL
 * - Local Development
 */

$pdo = null;

/**
 * Safe environment variable reader
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
     * Railway Variables
     */
    $host = env('MYSQLHOST');
    $port = env('MYSQLPORT', 3306);
    $database = env('MYSQLDATABASE');
    $username = env('MYSQLUSER');
    $password = env('MYSQLPASSWORD');

    /**
     * Local fallback (XAMPP/WAMP)
     */
    if (!$host || !$database || !$username) {

        error_log('DB: Falling back to local database configuration');

        $host = '127.0.0.1';
        $port = 3306;
        $database = 'fangak_youth_union';
        $username = 'root';
        $password = '';
    }

    /**
     * Validate required credentials
     */
    if (
        empty($host) ||
        empty($database) ||
        empty($username)
    ) {
        throw new Exception('Database environment variables are missing.');
    }

    /**
     * PDO DSN
     */
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $host,
        (int)$port,
        $database
    );

    /**
     * Create PDO Connection
     */
    $pdo = new PDO($dsn, $username, $password, [

        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

        PDO::ATTR_EMULATE_PREPARES => false,

        PDO::ATTR_TIMEOUT => 5,

        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);

    /**
     * Health Check
     */
    $pdo->query("SELECT 1");

    error_log('DB STATUS: CONNECTED SUCCESSFULLY');

} catch (Throwable $e) {

    /**
     * Log Detailed Error
     */
    error_log('================ DATABASE ERROR ================');
    error_log('Message: ' . $e->getMessage());
    error_log('File: ' . $e->getFile());
    error_log('Line: ' . $e->getLine());
    error_log('================================================');

    /**
     * Prevent fatal crash
     */
    $pdo = null;

    /**
     * Optional user-friendly message
     * Remove in production if desired
     */
    die('Database connection failed. Please try again later.');
}