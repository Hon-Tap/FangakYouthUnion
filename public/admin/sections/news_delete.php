<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../app/config/db.php';

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) {
    header('Location: news.php');
    exit;
}

$id = (int)$_GET['id'];

// delete record and image file
try {
    // get image name
    $stmt = $pdo->prepare("SELECT image FROM blog_posts WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $image = $row['image'] ?? null;
        $pdo->prepare("DELETE FROM blog_posts WHERE id = :id")->execute(['id' => $id]);

        if (!empty($image)) {
            $uploadsDir = realpath(__DIR__ . '/../../uploads');
            $path = $uploadsDir . DIRECTORY_SEPARATOR . $image;
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }
} catch (PDOException $e) {
    die("Error deleting: " . $e->getMessage());
}

header('Location: news.php');
exit;
