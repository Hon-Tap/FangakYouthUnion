<?php
declare(strict_types=1);

/**
 * news_save.php
 * Handles BOTH Add + Edit from modal
 */

require_once __DIR__ . '/../../../app/config/db.php';

// Redirect helper
function go_back() {
    header("Location: news.php");
    exit;
}

// Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    go_back();
}

// Collect fields
$id          = isset($_POST['id']) && ctype_digit($_POST['id']) ? (int)$_POST['id'] : null;
$title       = trim($_POST['title'] ?? '');
$subheading  = trim($_POST['subheading'] ?? '');
$author      = trim($_POST['author'] ?? '');
$description = trim($_POST['description'] ?? '');
$content     = trim($_POST['content'] ?? '');
$category    = trim($_POST['category'] ?? 'Uncategorized');

// Basic validation
if ($title === '') {
    die("Title is required.");
}

// ------------------------------
// IMAGE UPLOAD HANDLING
// ------------------------------
$imageFilename = null;

// Uploads dir: Goes up 3 levels to root, then into uploads/news
// Example: C:/xampp/htdocs/FYU/uploads/news
$uploadsDir = realpath(__DIR__ . '/../../../uploads/news');

// Create folder if it doesn't exist
if (!$uploadsDir) {
    $created = mkdir(__DIR__ . '/../../../uploads/news', 0755, true);
    if ($created) {
        $uploadsDir = realpath(__DIR__ . '/../../../uploads/news');
    } else {
        die("Failed to create uploads directory.");
    }
}

if (!empty($_FILES['image']['name'])) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    $tmp = $_FILES['image']['tmp_name'];
    $orig = basename($_FILES['image']['name']);
    $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed, true)) {
        die("Invalid image format.");
    }

    // 1. Sanitize the name (remove spaces/symbols) but NO random numbers
    // "My Photo.jpg" becomes "MyPhoto.jpg"
    $safeName = preg_replace('/[^a-zA-Z0-9_\-.]/', '', $orig);
    
    // 2. Assign strictly the safe name (no time prefix)
    $imageFilename = $safeName;

    $destPath = $uploadsDir . DIRECTORY_SEPARATOR . $imageFilename;

    if (!move_uploaded_file($tmp, $destPath)) {
        die("Failed to upload image to: " . $destPath);
    }
}

try {

    // ---------------------------------------------------
    // UPDATE EXISTING NEWS (Edit Mode)
    // ---------------------------------------------------
    if ($id) {
        // fetch existing image
        $stmt = $pdo->prepare("SELECT image FROM blog_posts WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$existing) {
            die("News item not found.");
        }

        $oldImage = $existing['image'];

        // if no new image uploaded -> keep old
        if ($imageFilename === null) {
            $imageFilename = $oldImage;
        } 
        // if new image uploaded -> delete old one (optional logic)
        // Note: If the new name is the SAME as the old name, we don't delete to avoid errors.
        else {
             if (!empty($oldImage) && $oldImage !== $imageFilename) {
                $oldPath = $uploadsDir . DIRECTORY_SEPARATOR . $oldImage;
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
        }

        // update
        $sql = "UPDATE blog_posts
                SET title = :title,
                    subheading = :subheading,
                    author = :author,
                    description = :description,
                    content = :content,
                    category = :category,
                    image = :image
                WHERE id = :id";

        $params = [
            'title'       => $title,
            'subheading'  => $subheading,
            'author'      => $author,
            'description' => $description,
            'content'     => $content,
            'category'    => $category,
            'image'       => $imageFilename,
            'id'          => $id
        ];

        $pdo->prepare($sql)->execute($params);
    }

    // ---------------------------------------------------
    // INSERT NEW NEWS (Add Mode)
    // ---------------------------------------------------
    else {
        $sql = "INSERT INTO blog_posts
                    (title, subheading, author, description, content, category, image, created_at)
                VALUES 
                    (:title, :subheading, :author, :description, :content, :category, :image, NOW())";

        $params = [
            'title'       => $title,
            'subheading'  => $subheading,
            'author'      => $author,
            'description' => $description,
            'content'     => $content,
            'category'    => $category,
            'image'       => $imageFilename
        ];

        $pdo->prepare($sql)->execute($params);
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

go_back();