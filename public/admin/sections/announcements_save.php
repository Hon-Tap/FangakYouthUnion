<?php
// admin/sections/announcements_save.php
declare(strict_types=1);

require_once __DIR__ . '/../../../app/config/db.php';

$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
$title = trim($_POST['title'] ?? '');
$body = trim($_POST['body'] ?? '');
$starts_at = $_POST['starts_at'] ?: null;
$ends_at = $_POST['ends_at'] ?: null;
$is_published = isset($_POST['is_published']) ? (int)$_POST['is_published'] : 0;

// Basic validation
if ($title === '' || $body === '') {
    http_response_code(400);
    echo "Missing title or body";
    exit;
}

if ($id) {
    $stmt = $pdo->prepare("UPDATE announcements SET title = ?, body = ?, starts_at = ?, ends_at = ?, is_published = ? WHERE id = ?");
    $stmt->execute([$title, $body, $starts_at, $ends_at, $is_published, $id]);
    echo "updated";
    exit;
}

$stmt = $pdo->prepare("INSERT INTO announcements (title, body, starts_at, ends_at, is_published) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$title, $body, $starts_at, $ends_at, $is_published]);
echo "created";
