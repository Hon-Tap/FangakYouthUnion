<?php
// admin/sections/projects_list_ajax.php
declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../../app/config/db.php';

// -----------------------------------
// HELPER: Normalize Image Path
// -----------------------------------
$baseUrl = '/public/'; // Adjust if your base URL differs
function get_project_img($baseUrl, $path) {
    if (empty($path)) return $baseUrl . 'images/FYU-donates.jpg';
    if (filter_var($path, FILTER_VALIDATE_URL)) return $path;
    return $baseUrl . ltrim($path, '/');
}

// -----------------------------------
// FETCH SINGLE PROJECT (for modal edit)
// -----------------------------------
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($project) {
        $project['image_url'] = get_project_img($baseUrl, $project['image'] ?? '');
    }

    echo json_encode($project ?: []);
    exit;
}

// -----------------------------------
// FETCH ALL PROJECTS (for dashboard display)
// -----------------------------------
$stmt = $pdo->query("SELECT * FROM projects ORDER BY sort_order ASC, id DESC");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Add normalized image URLs for frontend
foreach ($projects as &$p) {
    $p['image_url'] = get_project_img($baseUrl, $p['image'] ?? '');
    $p['status'] = strtolower($p['status'] ?? 'current'); // for filter matching
}

echo json_encode($projects);
