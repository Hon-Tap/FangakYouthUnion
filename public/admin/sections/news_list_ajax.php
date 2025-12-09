<?php
declare(strict_types=1);
require_once __DIR__ . '/../../../app/config/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM blog_posts ORDER BY id ASC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $news = array_map(function($post) {
        return [
            'id'          => $post['id'],
            'title'       => $post['title'] ?? '',
            'subheading'  => $post['subheading'] ?? '',
            'author'      => $post['author'] ?? 'Unknown',
            'description' => $post['description'] ?? '',
            'content'     => $post['content'] ?? '',
            'category'    => $post['category'] ?? 'Uncategorized',
            'image'       => !empty($post['image']) ? $post['image'] : null,
            'created_at'  => isset($post['created_at']) ? date('Y-m-d H:i', strtotime($post['created_at'])) : ''
        ];
    }, $rows);

} catch (PDOException $e) {
    $news = [];
}

// Return JSON
header('Content-Type: application/json');
echo json_encode($news);
