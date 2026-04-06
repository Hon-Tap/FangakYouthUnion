<?php

declare(strict_types=1);

/**
 * Optimized Database Connection for Railway & Local Environments
 * * This version prioritizes the MYSQL_URL connection string which is 
 * the most stable method for Railway internal networking.
 */

$pdo = null;

/**
 * Helper to fetch environment variables from any source
 */
function get_db_env(string $key, $default = null) {
    $val = getenv($key) ?: ($_ENV[$key] ?? ($_SERVER[$key] ?? $default));
    return ($val !== '' && $val !== false) ? $val : $default;
}

// 1. Check for Railway's "All-in-one" Connection String (Highest Reliability)
$mysqlUrl = get_db_env('MYSQL_URL') ?? get_db_env('DATABASE_URL');

if ($mysqlUrl) {
    try {
        // Parse the URL: mysql://user:pass@host:port/dbname
        $components = parse_url($mysqlUrl);
        
        $DB_HOST = $components['host'] ?? '127.0.0.1';
        $DB_PORT = $components['port'] ?? 3306;
        $DB_USER = $components['user'] ?? 'root';
        $DB_PASS = $components['pass'] ?? '';
        $DB_NAME = ltrim($components['path'] ?? 'fyu', '/');
        
        error_log("DB: Attempting connection via MYSQL_URL");
    } catch (Exception $e) {
        error_log("DB: Failed to parse MYSQL_URL");
    }
} else {
    // 2. Fallback to individual variables
    $DB_HOST = get_db_env('MYSQLHOST') ?: get_db_env('DB_HOST', '127.0.0.1');
    $DB_PORT = (int)(get_db_env('MYSQLPORT') ?: get_db_env('DB_PORT', 3306));
    $DB_NAME = get_db_env('MYSQLDATABASE') ?: get_db_env('DB_DATABASE', 'fyu');
    $DB_USER = get_db_env('MYSQLUSER') ?: get_db_env('DB_USERNAME', 'root');
    $DB_PASS = get_db_env('MYSQLPASSWORD') ?: get_db_env('DB_PASSWORD', '');
    
    error_log("DB: Attempting connection via individual ENV variables");
}

try {
    $dsn = sprintf(
        "mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4",
        $DB_HOST,
        $DB_PORT,
        $DB_NAME
    );

    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 5,
    ]);

    // Health check
    $pdo->query("SELECT 1");
    error_log("DB STATUS: SUCCESS (Connected to $DB_NAME)");

} catch (PDOException $e) {
    error_log("DB STATUS: CONNECTION FAILED");
    error_log("PDO ERROR: " . $e->getMessage());
    
    // Set $pdo to null so calling scripts can handle the failure gracefully
    $pdo = null;
}