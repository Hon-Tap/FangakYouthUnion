<?php
declare(strict_types=1);
require_once __DIR__ . "/../app/config/db.php";

// --- HELPER FUNCTIONS ---
function getImagePath(?string $imageName): string {
    $path = 'uploads/news/' . ($imageName ?: 'default.jpg');
    return file_exists(__DIR__ . '/' . $path) ? $path : 'uploads/news/default.php'; 
}

// --- API HANDLER ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
    header("Content-Type: application/json");
    $response = ['status' => 'error', 'message' => 'Unexpected error.'];

    try {
        if ($_POST['action'] === 'subscribe') {
            $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
            if (!$email) throw new Exception("Invalid email address.");

            $stmt = $pdo->prepare("INSERT IGNORE INTO subscribers (email) VALUES (?)");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $pdo->prepare("INSERT INTO admin_notifications (type, message) VALUES ('subscription', ?)")
                    ->execute(["New subscriber: $email"]);
                $response = ['status' => 'success', 'message' => 'Welcome to the community!'];
            } else {
                $response = ['status' => 'info', 'message' => 'You are already subscribed!'];
            }
        }

        if ($_POST['action'] === 'add_comment') {
            $postId = (int)$_POST['post_id'];
            $name = htmlspecialchars(trim($_POST['user_name']));
            $body = htmlspecialchars(trim($_POST['comment_body']));

            if (empty($name) || empty($body)) throw new Exception("All fields are required.");

            $stmt = $pdo->prepare("INSERT INTO blog_comments (post_id, user_name, comment_body, created_at) VALUES (?, ?, ?, NOW())");
            if ($stmt->execute([$postId, $name, $body])) {
                $response = [
                    'status' => 'success',
                    'message' => 'Comment posted!',
                    'comment' => ['name' => $name, 'body' => nl2br($body), 'date' => 'Just now']
                ];
            }
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
    echo json_encode($response);
    exit;
}

// --- DATA FETCHING ---
$postId = (int)($_GET['post'] ?? 0);
$viewMode = ($postId > 0) ? 'single' : 'index';
$posts = [];

if ($viewMode === 'single') {
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->execute([$postId]);
    $currentPost = $stmt->fetch();
    if (!$currentPost) header("Location: blog.php");

    $cStmt = $pdo->prepare("SELECT * FROM blog_comments WHERE post_id = ? ORDER BY created_at DESC");
    $cStmt->execute([$postId]);
    $comments = $cStmt->fetchAll();
} else {
    $posts = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC")->fetchAll();
}

include_once __DIR__ . "/../app/views/layouts/header.php";
?>

<style>
    :root { --fyu-green: #0f5132; --fyu-gold: #d97706; }
    .blog-container { max-width: 1100px; margin: 40px auto; padding: 0 20px; font-family: 'Inter', sans-serif; }
    .hero-title { font-family: 'Playfair Display', serif; font-size: 3.5rem; text-align: center; margin-bottom: 10px; }
    
    /* Post Cards */
    .post-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px; margin-top: 50px; }
    .fyu-card { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.05); transition: 0.3s; border: 1px solid #eee; }
    .fyu-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
    .fyu-card img { width: 100%; height: 200px; object-fit: cover; }
    .fyu-card-content { padding: 20px; }
    
    /* Article Style */
    .article-header { text-align: center; max-width: 800px; margin: 0 auto 40px; }
    .article-body { line-height: 1.8; font-size: 1.15rem; color: #333; max-width: 800px; margin: 0 auto; }
</style>

<div class="blog-container">
    <?php if ($viewMode === 'index'): ?>
        <div class="article-header">
            <h1 class="hero-title">Fangak Union Blog</h1>
            <p>Voices, News, and Progress from our community.</p>
        </div>

        <div class="post-grid">
            <?php foreach ($posts as $post): ?>
                <article class="fyu-card">
                    <img src="<?= getImagePath($post['image']) ?>" alt="Post Image">
                    <div class="fyu-card-content">
                        <small style="color: var(--fyu-gold); font-weight: bold;"><?= date('M d, Y', strtotime($post['created_at'])) ?></small>
                        <h3 style="margin: 10px 0;"><?= htmlspecialchars($post['title']) ?></h3>
                        <p style="font-size: 0.9rem; color: #666;"><?= substr(strip_tags($post['description']), 0, 100) ?>...</p>
                        <a href="?post=<?= $post['id'] ?>" style="color: var(--fyu-green); font-weight: 600; text-decoration: none;">Read More →</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="article-header">
            <a href="blog.php" style="text-decoration:none; color:#666;">← Back to Stories</a>
            <h1 style="margin-top:20px;"><?= htmlspecialchars($currentPost['title']) ?></h1>
        </div>
        <img src="<?= getImagePath($currentPost['image']) ?>" style="width:100%; border-radius:20px; margin-bottom:40px;">
        <div class="article-body"><?= $currentPost['description'] ?></div>
    <?php endif; ?>
</div>