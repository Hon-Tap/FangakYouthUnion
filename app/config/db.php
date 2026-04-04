<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Reliable Database Connection (Railway / Local)
|--------------------------------------------------------------------------
*/

$pdo = null;
$conn = null;

/*
|--------------------------------------------------------------------------
| Safe environment variable reader
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
| Load variables with safe fallbacks
|--------------------------------------------------------------------------
*/

$DB_HOST = env_value('MYSQLHOST', 'mysql');
$DB_PORT = (int) env_value('MYSQLPORT', 3306);
$DB_NAME = env_value('MYSQLDATABASE', 'railway');
$DB_USER = env_value('MYSQLUSER', 'root');
$DB_PASS = env_value('MYSQLPASSWORD', '');

/*
|--------------------------------------------------------------------------
| Debug visibility (safe)
|--------------------------------------------------------------------------
*/

error_log("DB CONFIG CHECK:");
error_log("HOST=" . ($DB_HOST ?: 'missing'));
error_log("PORT=" . ($DB_PORT ?: 'missing'));
error_log("DB=" . ($DB_NAME ?: 'missing'));
error_log("USER=" . ($DB_USER ?: 'missing'));
error_log("PASS=" . ($DB_PASS ? '[set]' : 'missing'));

/*
|--------------------------------------------------------------------------
| Attempt connection
|--------------------------------------------------------------------------
*/

try {

    if (!$DB_HOST || !$DB_NAME || !$DB_USER) {
        throw new Exception("Missing required DB configuration values");
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

    $conn = $pdo;

    error_log("DB STATUS: CONNECTION SUCCESS");

} catch (Throwable $e) {

    error_log("DB STATUS: CONNECTION FAILED");
    error_log($e->getMessage());

    $pdo = null;
    $conn = null;
}