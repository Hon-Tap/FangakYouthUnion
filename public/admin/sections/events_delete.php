<?php
// admin/sections/events_delete.php
declare(strict_types=1);

require_once __DIR__ . '/../../../app/config/db.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo "Missing ID";
    exit;
}

$id = (int)$_GET['id'];

// fetch image to remove
$stmt = $pdo->prepare("SELECT image FROM events WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// delete db row
$stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
$stmt->execute([$id]);

// remove file if exists
if ($row && !empty($row['image'])) {
    $file = __DIR__ . '/../../../uploads/events/' . $row['image'];
    if (file_exists($file)) unlink($file);
}

echo "deleted";
