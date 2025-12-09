<?php
// admin/sections/announcements_delete.php
declare(strict_types=1);

require_once __DIR__ . '/../../../app/config/db.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo "Missing ID";
    exit;
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
$stmt->execute([$id]);

echo "deleted";
