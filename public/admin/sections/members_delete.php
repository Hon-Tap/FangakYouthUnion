<?php
// admin/members_delete.php
declare(strict_types=1);

include __DIR__ . "/../includes/admin_auth.php";
include_once __DIR__ . "/../../../app/config/db.php";

if (empty($_GET['id'])) {
    die("Missing ID.");
}

$id = $_GET['id'];

// 1. Fetch the photo path first
$stmt = $pdo->prepare("SELECT photo FROM members WHERE id = ?");
$stmt->execute([$id]);
$photo = $stmt->fetchColumn();

// 2. Erase the physical file from the server
if ($photo) {
    $photo_path_clean = str_replace('public/', '', $photo);
    $file_path = __DIR__ . "/../../../public/" . ltrim($photo_path_clean, '/');
    if (file_exists($file_path) && is_file($file_path)) {
        unlink($file_path);
    }
}

// 3. Delete the database row
$stmt = $pdo->prepare("DELETE FROM members WHERE id = ?");
$stmt->execute([$id]);

header("Location: members.php?success=deleted");
exit;