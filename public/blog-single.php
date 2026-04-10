<?php
declare(strict_types=1);
include_once __DIR__ . "/../app/config/db.php"; 

// Get slug from URL
$slug = $_GET['slug'] ?? null;

if (!$slug || !isset($pdo)) {
    header("Location: blog.php");
    exit;
}

// 1. Fetch Main Post - Ensure the query explicitly selects the 'content' column
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

// 2. Safely handle BLOB content
// If the content is a stream resource, convert it to a string. Otherwise, use as is.
$articleContent = is_resource($post['content']) ? stream_get_contents($post['content']) : ($post['content'] ?? '');

// 3. Data Prep for View
$pageTitle = htmlspecialchars($post['title']);
$wordCount = str_word_count(strip_tags($articleContent));
$readingTime = max(1, (int)ceil($wordCount / 200));
$baseUrl = "/images/";

// 4. Sidebar Data
$recentPosts = $pdo->prepare("SELECT title, slug, featured_image FROM blog_posts WHERE slug != ? ORDER BY created_at DESC LIMIT 3");
$recentPosts->execute([$slug]);

$categories = $pdo->query("SELECT name, slug FROM categories ORDER BY name ASC")->fetchAll();

$commentStmt = $pdo->prepare("SELECT user_name, comment_body, created_at FROM blog_comments WHERE post_id = ? ORDER BY created_at DESC");
$commentStmt->execute([$post['id']]);
$comments = $commentStmt->fetchAll();

include_once __DIR__ . "/../app/views/layouts/header.php";
?>

<style>
    :root {
        --primary: #0b6026;
        --text-main: #1e293b;
        --text-meta: #64748b;
        --bg: #f9fafb;
    }

    /* Layout */
    .main-wrapper { max-width: 1100px; margin: 40px auto; padding: 0 20px; display: grid; grid-template-columns: 1fr 320px; gap: 40px; }
    
    /* Content Styling */
    .article-title { font-family: 'Crimson Pro', serif; font-size: 2.8rem; line-height: 1.2; margin-bottom: 20px; }
    .content-body { 
        font-family: 'Crimson Pro', serif; font-size: 1.2rem; line-height: 1.8; 
        text-align: justify; text-justify: inter-word; hyphens: auto; 
    }
    .content-body p { margin-bottom: 1.5rem; }

    /* Sidebar & Widgets */
    .sidebar { position: sticky; top: 20px; }
    .widget { background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; margin-bottom: 20px; }
    .widget-title { font-size: 0.9rem; font-weight: 700; text-transform: uppercase; margin-bottom: 15px; color: var(--text-meta); }

    @media (max-width: 900px) { .main-wrapper { grid-template-columns: 1fr; } }
</style>

<div id="progress-wrap" style="position:fixed; top:0; width:100%; height:3px;"><div id="progress-bar" style="height:100%; background:var(--primary); width:0%;"></div></div>

<main class="main-wrapper">
    <article>
        <header style="margin-bottom: 30px;">
            <a href="blog.php?category=<?= $post['category_slug'] ?>" style="color:var(--primary); font-weight:700; text-decoration:none; text-transform:uppercase; font-size:0.8rem;"><?= $post['category_name'] ?></a>
            <h1 class="article-title"><?= $pageTitle ?></h1>
            <div style="color:var(--text-meta); font-size:0.9rem;">
                <?= date("M d, Y", strtotime($post['created_at'])) ?> • <?= $readingTime ?> min read
            </div>
        </header>

        <img src="<?= $post['featured_image'] ? $baseUrl . $post['featured_image'] : $baseUrl . 'default.jpg' ?>" style="width:100%; height:400px; object-fit:cover; border-radius:8px; margin-bottom:30px;">

        <div class="content-body" id="post-content">
            <?= $post['content'] ?>
        </div>
    </article>

    <aside class="sidebar">
        <div class="widget" id="toc-widget" style="display:none;">
            <h3 class="widget-title">Contents</h3>
            <nav id="toc-container"></nav>
        </div>
        
        <div class="widget">
            <h3 class="widget-title">Recent Stories</h3>
            <?php while($row = $recentPosts->fetch()): ?>
                <a href="blog.php?slug=<?= $row['slug'] ?>" style="display:block; margin-bottom:15px; text-decoration:none; color:inherit;">
                    <strong style="display:block;"><?= $row['title'] ?></strong>
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

    // Simple TOC
    const headings = document.querySelectorAll('#post-content h2');
    if (headings.length > 0) {
        document.getElementById('toc-widget').style.display = 'block';
        headings.forEach((h, i) => {
            h.id = 'sec-' + i;
            document.getElementById('toc-container').innerHTML += `<a href="#sec-${i}" style="display:block; color:var(--text-main); margin-bottom:5px; text-decoration:none;">${h.innerText}</a>`;
        });
    }
</script>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>