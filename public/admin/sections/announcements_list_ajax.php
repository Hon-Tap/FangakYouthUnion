<?php
// admin/sections/announcements_list_ajax.php
declare(strict_types=1);

require_once __DIR__ . '/../../../app/config/db.php';

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT id, title, body, starts_at, ends_at, is_published, created_at FROM announcements WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($row ?: null);
    exit;
}

$stmt = $pdo->query("SELECT id, title, body, starts_at, ends_at, is_published, created_at FROM announcements ORDER BY created_at DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($rows);
