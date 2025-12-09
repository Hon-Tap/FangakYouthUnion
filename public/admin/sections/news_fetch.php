<?php
declare(strict_types=1);
require_once __DIR__ . '/../../../app/config/db.php';

$id = isset($_GET['id']) && ctype_digit($_GET['id']) ? (int)$_GET['id'] : 0;

header('Content-Type: application/json');

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        echo json_encode(['success' => false, 'message' => 'Post not found']);
        exit;
    }

    // Prepare the Image URL using a ROOT-RELATIVE path
    $imageName = !empty($post['image']) ? $post['image'] : null;
    $imageUrl  = null;
    
    if ($imageName) {
        // CORRECTION: Use the root-relative path '/uploads/news/'
        // The browser can resolve this path regardless of the admin page depth.
        $imageUrl = '/uploads/news/' . $imageName;
    }

    // Return formatted post
    echo json_encode([
        'success' => true,
        'post' => [
            'id'            => $post['id'],
            'title'         => $post['title'] ?? '',
            'subheading'    => $post['subheading'] ?? '',
            'author'        => $post['author'] ?? 'Unknown',
            'description'   => $post['description'] ?? '',
            'content'       => $post['content'] ?? '',
            'category'      => $post['category'] ?? 'Uncategorized',
            
            'image'         => $imageName,
            // THE CRUCIAL FIX IS HERE:
            'image_url'     => $imageUrl, 
            
            'created_at'    => isset($post['created_at']) ? date('Y-m-d H:i', strtotime($post['created_at'])) : ''
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}