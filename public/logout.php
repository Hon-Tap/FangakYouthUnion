<?php
// logout.php
declare(strict_types=1);

session_start();

// --- Preserve role (optional, just for clarity if needed later) ---
$role = $_SESSION['role'] ?? null;

// --- Clear all session data ---
$_SESSION = [];

// --- Destroy the session cookie (for security) ---
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// --- Finally, destroy the session itself ---
session_destroy();

// --- Start a new session for flash message ---
session_start();
$_SESSION['success'] = "You have been logged out successfully.";

// --- Redirect to shared login page ---
header("Location: /FangakYouthUnion/public/login.php");
exit();
