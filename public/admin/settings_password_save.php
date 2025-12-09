<?php
declare(strict_types=1);

require __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../../../app/config/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

$current_password = trim($_POST['current_password'] ?? '');
$new_password = trim($_POST['new_password'] ?? '');
$confirm_password = trim($_POST['confirm_password'] ?? '');

if ($current_password === '' || $new_password === '' || $confirm_password === '') {
    header("Location: settings_password.php?error=All fields are required.");
    exit;
}

if ($new_password !== $confirm_password) {
    header("Location: settings_password.php?error=Passwords do not match.");
    exit;
}

try {
    // Get admin user
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $_SESSION['admin_id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        header("Location: settings_password.php?error=Admin not found.");
        exit;
    }

    // Verify old password
    if (!password_verify($current_password, $admin['password'])) {
        header("Location: settings_password.php?error=Wrong current password.");
        exit;
    }

    // Update password
    $hashed = password_hash($new_password, PASSWORD_DEFAULT);

    $update = $pdo->prepare("UPDATE admins SET password = :pass WHERE id = :id");
    $update->execute([
        'pass' => $hashed,
        'id'   => $_SESSION['admin_id']
    ]);

    header("Location: settings_password.php?success=Password updated.");
    exit;

} catch (Exception $e) {
    header("Location: settings_password.php?error=Server error.");
    exit;
}
