<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Unified Database Connection (Railway + Local)
|--------------------------------------------------------------------------
| Uses PDO
| Reads Railway environment variables automatically
| Falls back to local defaults if not present
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
| Detect Railway automatically
|--------------------------------------------------------------------------
*/

$isRailway = env_value('RAILWAY_ENVIRONMENT') !== null;

/*
|--------------------------------------------------------------------------
| Load variables
|--------------------------------------------------------------------------
*/

$DB_HOST = env_value('MYSQLHOST', '127.0.0.1');
$DB_PORT = (int) env_value('MYSQLPORT', 3306);
$DB_NAME = env_value('MYSQLDATABASE', 'fyu');
$DB_USER = env_value('MYSQLUSER', 'root');
$DB_PASS = env_value('MYSQLPASSWORD', '');

/*
|--------------------------------------------------------------------------
| Debug (safe for production logs)
|--------------------------------------------------------------------------
*/

error_log("=== DB CONFIG ===");
error_log("ENV=" . ($isRailway ? "Railway" : "Local"));
error_log("HOST=" . $DB_HOST);
error_log("PORT=" . $DB_PORT);
error_log("DB=" . $DB_NAME);
error_log("USER=" . $DB_USER);
error_log("PASS=" . ($DB_PASS ? "[set]" : "missing"));

/*
|--------------------------------------------------------------------------
| Connect
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
            PDO::ATTR_TIMEOUT => 5
        ]
    );

    error_log("DB STATUS: CONNECTED");

} catch (Throwable $e) {

    error_log("DB STATUS: FAILED");
    error_log($e->getMessage());

    $pdo = null;
}