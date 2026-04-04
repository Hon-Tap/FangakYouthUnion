<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Unified Database Connection (Railway + Local)
|--------------------------------------------------------------------------
| - Uses PDO
| - Supports DB_* and MYSQL* variables
| - Works locally and on Railway
| - Deterministic logging
| - Fail-fast diagnostics
|--------------------------------------------------------------------------
*/

$pdo = null;

/*
|--------------------------------------------------------------------------
| Safe environment reader
|--------------------------------------------------------------------------
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

/*
|--------------------------------------------------------------------------
| Detect environment
|--------------------------------------------------------------------------
*/

$isRailway = env_value('RAILWAY_ENVIRONMENT') !== null;

/*
|--------------------------------------------------------------------------
| Load configuration (priority order)
|--------------------------------------------------------------------------
| 1) DB_* (preferred)
| 2) MYSQL* (fallback)
| 3) Local defaults
|--------------------------------------------------------------------------
*/

$DB_HOST =
    env_value('DB_HOST')
    ?? env_value('MYSQLHOST')
    ?? '127.0.0.1';

$DB_PORT =
    (int) (
        env_value('DB_PORT')
        ?? env_value('MYSQLPORT')
        ?? 3306
    );

$DB_NAME =
    env_value('DB_DATABASE')
    ?? env_value('MYSQLDATABASE')
    ?? 'fyu';

$DB_USER =
    env_value('DB_USERNAME')
    ?? env_value('MYSQLUSER')
    ?? 'root';

$DB_PASS =
    env_value('DB_PASSWORD')
    ?? env_value('MYSQLPASSWORD')
    ?? '';

/*
|--------------------------------------------------------------------------
| Debug logging (safe)
|--------------------------------------------------------------------------
*/

error_log("=== DB CONFIG ===");

error_log("ENV=" . ($isRailway ? "Railway" : "Local"));
error_log("HOST=" . ($DB_HOST ?: 'missing'));
error_log("PORT=" . ($DB_PORT ?: 'missing'));
error_log("DB=" . ($DB_NAME ?: 'missing'));
error_log("USER=" . ($DB_USER ?: 'missing'));
error_log("PASS=" . ($DB_PASS ? "[set]" : "missing"));

/*
|--------------------------------------------------------------------------
| DNS Resolution Test
|--------------------------------------------------------------------------
*/

$resolved = gethostbyname($DB_HOST);

error_log("=== DNS TEST ===");
error_log("HOST=" . $DB_HOST);
error_log("RESOLVED=" . $resolved);

if ($resolved === $DB_HOST) {
    error_log("WARNING: HOST NOT RESOLVED");
}

/*
|--------------------------------------------------------------------------
| Attempt Database Connection
|--------------------------------------------------------------------------
*/

try {

    if (!$DB_HOST || !$DB_NAME || !$DB_USER) {
        throw new Exception("Missing required database configuration");
    }

    $dsn = sprintf(
        "mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4",
        $DB_HOST,
        $DB_PORT,
        $DB_NAME
    );

    $pdo = new PDO(
        $dsn,
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 5,
        ]
    );

    /*
    |--------------------------------------------------------------------------
    | Lightweight health check
    |--------------------------------------------------------------------------
    */

    $pdo->query("SELECT 1");

    error_log("DB STATUS: CONNECTED");

} catch (Throwable $e) {

    error_log("DB STATUS: FAILED");
    error_log("ERROR TYPE: " . get_class($e));
    error_log("ERROR MESSAGE: " . $e->getMessage());

    $pdo = null;
}