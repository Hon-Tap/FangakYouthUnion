<?php
// admin/sections/events_save.php
declare(strict_types=1);

require_once __DIR__ . '/../../../app/config/db.php';

$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
$project_id = isset($_POST['project_id']) && $_POST['project_id'] !== '' ? (int)$_POST['project_id'] : null;
$title = trim($_POST['title'] ?? '');
$event_date = $_POST['event_date'] ?: null;
$description = trim($_POST['description'] ?? '');
$location = trim($_POST['location'] ?? '');
$start_date = $_POST['start_date'] ?: null;
$end_date = $_POST['end_date'] ?: null;
$status = $_POST['status'] ?? 'Upcoming';

$uploadDir = __DIR__ . '/../../../uploads/events/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// handle image upload
$imageName = null;
if (!empty($_FILES['image']['name'])) {
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $imageName = uniqid('evt_', true) . "." . $ext;
    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
}

// basic validation
if ($title === '') {
    http_response_code(400);
    echo "Missing title";
    exit;
}

if ($id) {
    // if image uploaded, update image field too
    if ($imageName) {
        $stmt = $pdo->prepare("UPDATE events SET project_id=?, title=?, event_date=?, description=?, location=?, start_date=?, end_date=?, status=?, image=? WHERE id=?");
        $stmt->execute([$project_id, $title, $event_date, $description, $location, $start_date, $end_date, $status, $imageName, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE events SET project_id=?, title=?, event_date=?, description=?, location=?, start_date=?, end_date=?, status=? WHERE id=?");
        $stmt->execute([$project_id, $title, $event_date, $description, $location, $start_date, $end_date, $status, $id]);
    }
    echo "updated";
    exit;
}

// insert new
$stmt = $pdo->prepare("INSERT INTO events (project_id, title, event_date, description, location, start_date, end_date, status, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$project_id, $title, $event_date, $description, $location, $start_date, $end_date, $status, $imageName]);

echo "created";
