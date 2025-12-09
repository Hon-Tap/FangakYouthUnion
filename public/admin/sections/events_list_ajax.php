<?php
// admin/sections/events_list_ajax.php
declare(strict_types=1);

require_once __DIR__ . '/../../../app/config/db.php';

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($row ?: null);
    exit;
}

// Return all events ordered by date (soonest first)
$stmt = $pdo->query("SELECT id, project_id, title, event_date, description, location, start_date, end_date, status, image, created_at FROM events ORDER BY COALESCE(event_date, created_at) ASC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($rows);
