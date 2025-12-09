<?php
declare(strict_types=1);

require __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/../../app/config/db.php";

// Validate inputs
$site_name = trim($_POST['site_name'] ?? '');
$contact_email = trim($_POST['contact_email'] ?? '');

if ($site_name === '' || $contact_email === '') {
    header("Location: settings.php?error=Please fill all fields.");
    exit;
}

$logo = null;

// Handle logo upload
if (!empty($_FILES['logo']['name'])) {
    $allowed = ['jpg','jpeg','png','webp','gif'];

    $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        header("Location: settings.php?error=Invalid file type.");
        exit;
    }

    $filename = time() . "_" . uniqid() . "." . $ext;
    $filepath = __DIR__ . "/../../../uploads/" . $filename;

    if (!move_uploaded_file($_FILES['logo']['tmp_name'], $filepath)) {
        header("Location: settings.php?error=Failed to upload logo.");
        exit;
    }

    $logo = $filename;
}

// Update settings
try {
    if ($logo) {
        $stmt = $pdo->prepare("
            UPDATE settings 
            SET site_name=:name, contact_email=:email, logo=:logo 
            LIMIT 1
        ");

        $stmt->execute([
            'name' => $site_name,
            'email' => $contact_email,
            'logo' => $logo
        ]);
    } else {
        $stmt = $pdo->prepare("
            UPDATE settings 
            SET site_name=:name, contact_email=:email 
            LIMIT 1
        ");

        $stmt->execute([
            'name' => $site_name,
            'email' => $contact_email
        ]);
    }

    header("Location: settings.php?success=Settings updated successfully.");
    exit;

} catch (Exception $e) {
    header("Location: settings.php?error=Server error occurred.");
    exit;
}
