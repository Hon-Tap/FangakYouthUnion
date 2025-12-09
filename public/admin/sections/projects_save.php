<?php
/**
 * projects_save.php – Clean, Debuggable, Reliable Upload Handler
 */
declare(strict_types=1);

header('Content-Type: application/json');

// -----------------------------------
// DB
// -----------------------------------
require_once __DIR__ . '/../../../app/config/db.php';

// -----------------------------------
// INPUTS
// -----------------------------------
$id          = $_POST['id'] ?? null;
$title       = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$status      = $_POST['status'] ?? 'current'; // Match modal/project.php
$featured    = isset($_POST['featured']) ? (int)$_POST['featured'] : 0;
$start_date  = $_POST['start_date'] ?: null;
$end_date    = $_POST['end_date'] ?: null;

// -----------------------------------
// UPLOAD PATH
// -----------------------------------
$uploadDir = __DIR__ . '/../../../public/uploads/projects/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// -----------------------------------
// IMAGE HANDLING
// -----------------------------------
$imageName = null;
if (!empty($_FILES['image']['name'])) {
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if ($ext) {
        $imageName = uniqid('proj_', true) . '.' . $ext;
        $targetPath = $uploadDir . $imageName;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            echo json_encode(['success' => false, 'message' => 'Image upload failed']);
            exit;
        }
    }
}

// -----------------------------------
// UPDATE EXISTING PROJECT
// -----------------------------------
if ($id) {
    try {
        if ($imageName) {
            // Remove old image
            $old = $pdo->prepare("SELECT image FROM projects WHERE id=?");
            $old->execute([$id]);
            $oldImage = $old->fetchColumn();
            if ($oldImage && file_exists($uploadDir . $oldImage)) unlink($uploadDir . $oldImage);

            $stmt = $pdo->prepare("
                UPDATE projects
                SET title=?, description=?, status=?, featured=?, start_date=?, end_date=?, image=?
                WHERE id=?
            ");
            $stmt->execute([$title, $description, $status, $featured, $start_date, $end_date, $imageName, $id]);
        } else {
            $stmt = $pdo->prepare("
                UPDATE projects
                SET title=?, description=?, status=?, featured=?, start_date=?, end_date=?
                WHERE id=?
            ");
            $stmt->execute([$title, $description, $status, $featured, $start_date, $end_date, $id]);
        }

        echo json_encode(['success' => true, 'message' => 'Project updated']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// -----------------------------------
// INSERT NEW PROJECT
// -----------------------------------
try {
    $stmt = $pdo->prepare("
        INSERT INTO projects (title, description, status, featured, start_date, end_date, image)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$title, $description, $status, $featured, $start_date, $end_date, $imageName]);
    echo json_encode(['success' => true, 'message' => 'Project created']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
