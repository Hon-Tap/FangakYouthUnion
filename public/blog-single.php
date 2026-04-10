<?php
declare(strict_types=1);
include_once __DIR__ . "/../app/config/db.php"; 

// 1. Get and validate slug
$slug = $_GET['slug'] ?? null;

if (!$slug || !isset($pdo)) {
    header("Location: blog.php");
    exit;
}

// 2. Fetch Main Post
$postStmt = $pdo->prepare("
    SELECT p.*, c.name AS category_name, c.slug AS category_slug 
    FROM blog_posts p 
    JOIN categories c ON p.category_id = c.id 
    WHERE p.slug = :slug 
    LIMIT 1
");
$postStmt->execute(['slug' => $slug]);
$post = $postStmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    http_response_code(404);
    include_once __DIR__ . "/../app/views/layouts/header.php";
    echo '<div class="main-wrapper" style="padding:100px 0; text-align:center;"><h1>404</h1><p>Article not found.</p></div>';
    include_once __DIR__ . "/../app/views/layouts/footer.php";
    exit;
}

// 3. Process Content (Handles both string and stream BLOBs)
$articleContent = is_resource($post['content']) ? stream_get_contents($post['content']) : ($post['content'] ?? '');

// 4. Data Preparation
$pageTitle = htmlspecialchars($post['title']);
$wordCount = str_word_count(strip_tags($articleContent));
$readingTime = max(1, (int)ceil($wordCount / 200));
$baseUrl = "/images/"; // Ensure this matches your image storage directory

// 5. Sidebar Data
$recentPosts = $pdo->prepare("SELECT title, slug, featured_image FROM blog_posts WHERE slug != ? ORDER BY created_at DESC LIMIT 3");
$recentPosts->execute([$slug]);

include_once __DIR__ . "/../app/views/layouts/header.php";
?>

<style>
    :root { --primary: #0b6026; --text-main: #1e293b; --text-meta: #64748b; }
    .main-wrapper { max-width: 1100px; margin: 40px auto; padding: 0 20px; display: grid; grid-template-columns: 1fr 320px; gap: 40px; }
    .article-title { font-family: 'Crimson Pro', serif; font-size: 2.8rem; line-height: 1.2; margin-bottom: 20px; }
    .content-body { font-family: 'Crimson Pro', serif; font-size: 1.2rem; line-height: 1.8; text-align: justify; }
    .content-body p { margin-bottom: 1.5rem; }
    .sidebar { position: sticky; top: 20px; }
    .widget { background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; margin-bottom: 20px; }
    .widget-title { font-size: 0.9rem; font-weight: 700; text-transform: uppercase; margin-bottom: 15px; color: var(--text-meta); }
    @media (max-width: 900px) { .main-wrapper { grid-template-columns: 1fr; } }
</style>

<div id="progress-wrap" style="position:fixed; top:0; width:100%; height:3px;"><div id="progress-bar" style="height:100%; background:var(--primary); width:0%;"></div></div>

<main class="main-wrapper">
    <article>
        <header style="margin-bottom: 30px;">
            <a href="blog.php?category=<?= htmlspecialchars($post['category_slug']) ?>" style="color:var(--primary); font-weight:700; text-decoration:none; text-transform:uppercase; font-size:0.8rem;"><?= htmlspecialchars($post['category_name']) ?></a>
            <h1 class="article-title"><?= $pageTitle ?></h1>
            <div style="color:var(--text-meta); font-size:0.9rem;">
                <?= date("M d, Y", strtotime($post['created_at'])) ?> • <?= $readingTime ?> min read
            </div>
        </header>

        <img src="<?= htmlspecialchars($post['featured_image'] ? $baseUrl . $post['featured_image'] : $baseUrl . 'default.jpg') ?>" style="width:100%; height:400px; object-fit:cover; border-radius:8px; margin-bottom:30px;" alt="<?= $pageTitle ?>">

        <div class="content-body" id="post-content">
            <?= $articleContent ?>
        </div>
    </article>

    <aside class="sidebar">
        <div class="widget">
            <h3 class="widget-title">Recent Stories</h3>
            <?php while($row = $recentPosts->fetch()): ?>
                <a href="blog.php?slug=<?= htmlspecialchars($row['slug']) ?>" style="display:block; margin-bottom:15px; text-decoration:none; color:inherit;">
                    <strong style="display:block;"><?= htmlspecialchars($row['title']) ?></strong>
                </a>
            <?php endwhile; ?>
        </div>
    </aside>
</main>

<script>
    // Progress Bar
    window.addEventListener('scroll', () => {
        let winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        let height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        document.getElementById("progress-bar").style.width = (winScroll / height) * 100 + "%";
    });
</script>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>