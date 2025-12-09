<?php
// login_process.php — Robust AJAX + non-AJAX login handler
// - Supports application/x-www-form-urlencoded, multipart/form-data, and JSON bodies
// - Uses prepared statements, strong error-handling and clear JSON responses for fetch()
// - Regenerates session ID on login, rotates CSRF token
// - Toggle DEBUG to show errors during development

declare(strict_types=1);

// -----------------------------
// Development debug toggle
// -----------------------------
const DEBUG = true;
if (DEBUG) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// -----------------------------
// Start session and common init
// -----------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Allow callers to require cookies to be sent for session to work
// Note: If you're calling this from fetch(), use credentials: 'include'

// -----------------------------
// Helper: send JSON and exit
// -----------------------------
function send_json(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload);
    exit;
}

// -----------------------------
// Detect AJAX (XMLHttpRequest / fetch)
// -----------------------------
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Also treat requests that accept JSON as AJAX-friendly
$acceptsJson = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;

// -----------------------------
// Require DB connection
// -----------------------------
require_once __DIR__ . "/../app/config/db.php"; // should set $conn (mysqli)

if (!isset($conn) || !$conn instanceof mysqli) {
    if ($isAjax || $acceptsJson) {
        send_json(['success' => false, 'message' => 'Server configuration error (DB).'], 500);
    }

    // Non-AJAX fallback
    $_SESSION['error'] = 'Server configuration error (DB).';
    header('Location: /');
    exit;
}

// -----------------------------
// Allow JSON body payloads (fetch with application/json)
// -----------------------------
$rawInput = file_get_contents('php://input');
$body = $_POST;
if ($rawInput) {
    $decoded = json_decode($rawInput, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $body = array_merge($body, $decoded);
    }
}

// -----------------------------
// 1. Validate request method
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($isAjax || $acceptsJson) {
        send_json(['success' => false, 'message' => 'Invalid request method. Use POST.'], 405);
    }
    $_SESSION['error'] = 'Invalid request method.';
    header('Location: /');
    exit;
}

// -----------------------------
// 2. Validate CSRF token (if using forms)
// -----------------------------
$csrfInPost = $body['csrf_token'] ?? null;
$sessionCsrf = $_SESSION['csrf_token'] ?? null;
if ($sessionCsrf === null) {
    // No CSRF in session — maybe session cookie not provided by fetch(). Fail early with helpful message.
    if ($isAjax || $acceptsJson) {
        send_json([
            'success' => false,
            'message' => 'Session missing. Make sure your request includes cookies (fetch: credentials: "include").'
        ], 401);
    }

    $_SESSION['error'] = 'Session missing. Please enable cookies.';
    header('Location: /');
    exit;
}

if (!is_string($csrfInPost) || hash_equals((string)$sessionCsrf, (string)$csrfInPost) === false) {
    if ($isAjax || $acceptsJson) {
        send_json(['success' => false, 'message' => 'Session expired or invalid request. Refresh and try again.'], 403);
    }
    $_SESSION['error'] = 'Session expired or invalid request.';
    header('Location: /login.php');
    exit;
}

// -----------------------------
// 3. Sanitize & validate input
// -----------------------------
$email = isset($body['email']) ? filter_var(trim($body['email']), FILTER_SANITIZE_EMAIL) : '';
$password = isset($body['password']) ? trim($body['password']) : '';

if ($email === '' || $password === '') {
    if ($isAjax || $acceptsJson) {
        send_json(['success' => false, 'message' => 'Please enter both email and password.'], 422);
    }
    $_SESSION['error'] = 'Please enter both email and password.';
    header('Location: /login.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    if ($isAjax || $acceptsJson) {
        send_json(['success' => false, 'message' => 'Invalid email address.'], 422);
    }
    $_SESSION['error'] = 'Invalid email address.';
    header('Location: /login.php');
    exit;
}

// -----------------------------
// 4. Prepare & execute DB query
// -----------------------------
$query = "SELECT id, email, password, role, full_name FROM users WHERE email = ? LIMIT 1";
if (!($stmt = $conn->prepare($query))) {
    if ($isAjax || $acceptsJson) {
        send_json(['success' => false, 'message' => 'Database error.'], 500);
    }
    $_SESSION['error'] = 'Database error.';
    header('Location: /login.php');
    exit;
}

$stmt->bind_param('s', $email);
if (!$stmt->execute()) {
    if ($isAjax || $acceptsJson) {
        send_json(['success' => false, 'message' => 'Database execution error.'], 500);
    }
    $_SESSION['error'] = 'Database execution error.';
    header('Location: /login.php');
    exit;
}

$result = $stmt->get_result();
if (!$result || $result->num_rows !== 1) {
    if ($isAjax || $acceptsJson) {
        send_json(['success' => false, 'message' => 'No account found with that email address.'], 404);
    }
    $_SESSION['error'] = 'No account found with that email address.';
    header('Location: /login.php');
    exit;
}

$user = $result->fetch_assoc();

// -----------------------------
// 5. Verify password
// -----------------------------
$storedHash = $user['password'] ?? '';
if (!is_string($storedHash) || !password_verify($password, $storedHash)) {
    if ($isAjax || $acceptsJson) {
        send_json(['success' => false, 'message' => 'Incorrect password.'], 401);
    }
    $_SESSION['error'] = 'Incorrect password.';
    header('Location: /login.php');
    exit;
}

// Optional: If using password_needs_rehash, rehash and update DB
if (password_needs_rehash($storedHash, PASSWORD_DEFAULT)) {
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    $up = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
    if ($up) {
        $up->bind_param('si', $newHash, $user['id']);
        $up->execute();
        $up->close();
    }
}

// -----------------------------
// 6. Successful login -> regenerate session & set session vars
// -----------------------------
session_regenerate_id(true);

// rotate csrf token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = strtolower($user['role'] ?? '');
$_SESSION['full_name'] = $user['full_name'] ?? '';

// -----------------------------
// 7. Determine redirect
// -----------------------------
$baseUrl = '/FangakYouthUnion/public/'; // adjust to your deployment
$role = $_SESSION['role'];
$redirect = null;
switch ($role) {
    case 'admin':
        $redirect = $baseUrl . 'dashboard.php';
        break;
    case 'member':
        $redirect = $baseUrl . 'member_dashboard.php';
        break;
    default:
        // Unknown role
        if ($isAjax || $acceptsJson) {
            send_json(['success' => false, 'message' => 'Unknown role assigned to this account.'], 403);
        }
        $_SESSION['error'] = 'Unknown role assigned to this account.';
        header('Location: ' . $baseUrl);
        exit;
}

// -----------------------------
// 8. Final response
// -----------------------------
if ($isAjax || $acceptsJson) {
    send_json([
        'success' => true,
        'message' => 'Login successful.',
        'redirect' => $redirect
    ], 200);
}

// Non-AJAX: display a short transitional page and redirect
$_SESSION['success'] = 'Login successful.';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Logging in…</title>
    <style>
        body{display:flex;align-items:center;justify-content:center;height:100vh;font-family:Inter,system-ui,Segoe UI,Roboto,-apple-system;}
        .wrap{display:flex;align-items:center;gap:16px}
        .spinner{border:4px solid #f3f3f3;border-top:4px solid #0b6a3a;border-radius:50%;width:48px;height:48px;animation:spin 1s linear infinite}
        @keyframes spin{to{transform:rotate(360deg)}}
    </style>
</head>
<body>
    <div class="wrap">
        <div class="spinner" role="img" aria-label="loading"></div>
        <div>Logging in — redirecting…</div>
    </div>
    <script>
        // Ensure client-side redirect happens after a tiny delay
        setTimeout(function(){ window.location.replace(<?= json_encode($redirect) ?>); }, 600);
    </script>
</body>
</html>
