<?php
// admin/sections/projects_delete.php
declare(strict_types=1);

require_once __DIR__ . '/../../../app/config/db.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo "Missing ID";
    exit;
}

$id = (int)$_GET['id'];

// Fetch old image to delete
$stmt = $pdo->prepare("SELECT image FROM projects WHERE id=?");
$stmt->execute([$id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

// Delete DB record
$stmt = $pdo->prepare("DELETE FROM projects WHERE id=?");
$stmt->execute([$id]);

// Remove physical image
if ($project && !empty($project['image'])) {
    $filePath = __DIR__ . '/../../../uploads/projects/' . $project['image'];
    if (file_exists($filePath)) unlink($filePath);
}

echo "deleted";
