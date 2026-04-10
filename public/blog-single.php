<?php
declare(strict_types=1);

// Database connection (Assuming $pdo is initialized in db.php)
include_once __DIR__ . "/../app/config/db.php"; 

$slug = $_GET['slug'] ?? null;

if (!$slug || !isset($pdo)) {
    header("Location: blog.php");
    exit;
}

// 1. Fetch Main Post
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
    // 404 Error Handling
    http_response_code(404);
    $pageTitle = "Post Not Found";
    include_once __DIR__ . "/../app/views/layouts/header.php";
    echo '<div class="error-container"><h1>404</h1><p>Article not found.</p><a href="blog.php">Return to Blog</a></div>';
    include_once __DIR__ . "/../app/views/layouts/footer.php";
    exit;
}

// Data Prep
$pageTitle = htmlspecialchars($post['title']);
$wordCount = str_word_count(strip_tags($post['content']));
$readingTime = max(1, (int)ceil($wordCount / 200));
$baseUrl = "/images/"; 

// 2. Fetch Sidebar Data (Recent & Categories)
$recentPosts = $pdo->prepare("SELECT title, slug, featured_image FROM blog_posts WHERE slug != ? ORDER BY created_at DESC LIMIT 3");
$recentPosts->execute([$slug]);

$categories = $pdo->query("SELECT name, slug FROM categories ORDER BY name ASC")->fetchAll();

// 3. Fetch Comments
$commentStmt = $pdo->prepare("SELECT user_name, comment_body, created_at FROM blog_comments WHERE post_id = ? ORDER BY created_at DESC");
$commentStmt->execute([$post['id']]);
$comments = $commentStmt->fetchAll();

include_once __DIR__ . "/../app/views/layouts/header.php";
?>

<link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:ital,wght@0,400;0,600;1,400&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    :root {
        --primary: #0b6026;
        --primary-soft: rgba(11, 96, 38, 0.08);
        --text-main: #1e293b;
        --text-light: #64748b;
        --bg-body: #fdfdfd;
        --white: #ffffff;
        --shadow: 0 10px 30px -12px rgba(0,0,0,0.08);
        --radius: 12px;
    }

    body {
        background-color: var(--bg-body);
        font-family: 'Inter', sans-serif;
        color: var(--text-main);
        line-height: 1.6;
    }

    /* Reading Progress */
    #progress-wrap {
        position: fixed; top: 0; left: 0; width: 100%; height: 4px; z-index: 1000;
    }
    #progress-bar { height: 100%; background: var(--primary); width: 0%; }

    .main-wrapper {
        max-width: 1200px; margin: 40px auto; padding: 0 20px;
        display: grid; grid-template-columns: 1fr 350px; gap: 50px;
    }

    @media (max-width: 1024px) { .main-wrapper { grid-template-columns: 1fr; } }

    /* Article Styling */
    .article-header { margin-bottom: 40px; }
    .cat-tag { 
        color: var(--primary); text-transform: uppercase; font-weight: 700; 
        font-size: 0.8rem; letter-spacing: 1px; text-decoration: none;
    }
    .article-title { 
        font-family: 'Crimson Pro', serif; font-size: clamp(2.5rem, 5vw, 3.8rem); 
        line-height: 1.1; margin: 15px 0; font-weight: 600;
    }
    .meta-info { display: flex; align-items: center; gap: 20px; color: var(--text-light); font-size: 0.9rem; }
    
    .featured-img { 
        width: 100%; height: 500px; object-fit: cover; 
        border-radius: var(--radius); margin-bottom: 40px; box-shadow: var(--shadow);
    }

    .content-body { 
        font-family: 'Crimson Pro', serif; font-size: 1.3rem; line-height: 1.8; color: #333;
    }
    .content-body p { margin-bottom: 1.5rem; }
    .content-body h2 { margin-top: 2rem; font-family: 'Inter', sans-serif; font-weight: 700; }

    /* Drop Cap */
    .content-body > p:first-of-type::first-letter {
        float: left; font-size: 4.5rem; line-height: 0.7; padding: 8px 10px 0 0;
        color: var(--primary); font-weight: 800;
    }

    /* Sidebar Widgets */
    .sidebar { position: sticky; top: 100px; align-self: start; }
    .widget { 
        background: var(--white); padding: 25px; border-radius: var(--radius); 
        box-shadow: var(--shadow); margin-bottom: 30px; border: 1px solid #f1f5f9;
    }
    .widget-title { font-weight: 700; font-size: 1rem; margin-bottom: 20px; text-transform: uppercase; }

    .toc-link { display: block; padding: 8px 0; color: var(--text-light); text-decoration: none; font-size: 0.95rem; }
    .toc-link:hover { color: var(--primary); }

    /* Comments Section */
    .comment-card { padding: 20px 0; border-bottom: 1px solid #eee; }
    .comment-avatar { 
        width: 40px; height: 40px; background: var(--primary-soft); 
        color: var(--primary); border-radius: 50%; display: grid; place-items: center; font-weight: 700;
    }

    .form-input { 
        width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; 
        margin-bottom: 15px; font-family: inherit;
    }
    .btn-submit { 
        background: var(--primary); color: white; border: none; 
        padding: 12px 30px; border-radius: 8px; cursor: pointer; font-weight: 600;
    }
</style>

<div id="progress-wrap"><div id="progress-bar"></div></div>

<main class="main-wrapper">
    <div class="article-container">
        <nav style="margin-bottom: 20px;">
            <a href="blog.php" style="text-decoration:none; color:var(--text-light); font-size:0.9rem;">
                <i class="fa-solid fa-arrow-left"></i> Back to Blog
            </a>
        </nav>

        <article>
            <header class="article-header">
                <a href="blog.php?category=<?= $post['category_slug'] ?>" class="cat-tag"><?= $post['category_name'] ?></a>
                <h1 class="article-title"><?= htmlspecialchars($post['title']) ?></h1>
                
                <div class="meta-info">
                    <span><i class="fa-regular fa-calendar"></i> <?= date("M d, Y", strtotime($post['created_at'])) ?></span>
                    <span><i class="fa-regular fa-clock"></i> <?= $readingTime ?> min read</span>
                    <span><i class="fa-regular fa-user"></i> FYU Editor</span>
                </div>
            </header>

            <img src="<?= $post['featured_image'] ? $baseUrl . $post['featured_image'] : $baseUrl . 'default.jpg' ?>" class="featured-img" alt="Featured Image">

            <div class="content-body" id="post-content">
                <?= $post['content'] ?>
            </div>
        </article>

        <section class="widget" style="margin-top: 60px;">
            <h3 class="widget-title">Thoughts (<?= count($comments) ?>)</h3>
            <div id="comment-list">
                <?php foreach($comments as $c): ?>
                    <div class="comment-card">
                        <div style="display:flex; gap:15px; align-items:center; margin-bottom:10px;">
                            <div class="comment-avatar"><?= strtoupper($c['user_name'][0]) ?></div>
                            <div>
                                <h4 style="margin:0; font-size:1rem;"><?= htmlspecialchars($c['user_name']) ?></h4>
                                <small style="color:var(--text-light)"><?= date("M d, Y", strtotime($c['created_at'])) ?></small>
                            </div>
                        </div>
                        <p style="margin:0; font-size:1rem; color:#444;"><?= nl2br(htmlspecialchars($c['comment_body'])) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #eee;">
                <h4 style="margin-bottom:20px;">Leave a Comment</h4>
                <form id="commentForm">
                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                        <input type="text" name="user_name" class="form-input" placeholder="Your Name" required>
                        <input type="email" name="user_email" class="form-input" placeholder="Your Email" required>
                    </div>
                    <textarea name="comment_body" class="form-input" rows="5" placeholder="Join the discussion..." required></textarea>
                    <button type="submit" class="btn-submit">Publish Comment</button>
                </form>
            </div>
        </section>
    </div>

    <aside class="sidebar">
        <div class="widget" id="toc-widget" style="display:none;">
            <h3 class="widget-title">In this article</h3>
            <nav id="toc-container"></nav>
        </div>

        <div class="widget">
            <h3 class="widget-title">Recent Stories</h3>
            <?php while($row = $recentPosts->fetch()): ?>
                <a href="blog.php?slug=<?= $row['slug'] ?>" style="display:flex; gap:12px; text-decoration:none; margin-bottom:15px; align-items:center;">
                    <img src="<?= $baseUrl . $row['featured_image'] ?>" style="width:60px; height:60px; border-radius:8px; object-fit:cover;">
                    <span style="font-size:0.9rem; color:var(--text-main); font-weight:600; line-height:1.3;"><?= $row['title'] ?></span>
                </a>
            <?php endwhile; ?>
        </div>

        <div class="widget">
            <h3 class="widget-title">Categories</h3>
            <div style="display:flex; flex-wrap:wrap; gap:8px;">
                <?php foreach($categories as $cat): ?>
                    <a href="blog.php?category=<?= $cat['slug'] ?>" style="padding:6px 12px; background:var(--primary-soft); color:var(--primary); border-radius:20px; text-decoration:none; font-size:0.85rem; font-weight:600;">
                        <?= $cat['name'] ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </aside>
</main>

<script>
    // 1. Reading Progress Bar
    window.addEventListener('scroll', () => {
        const winScroll = document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        document.getElementById("progress-bar").style.width = scrolled + "%";
    });

    // 2. Auto-Generate Table of Contents
    const content = document.getElementById('post-content');
    const tocContainer = document.getElementById('toc-container');
    const headings = content.querySelectorAll('h2');

    if (headings.length > 0) {
        document.getElementById('toc-widget').style.display = 'block';
        headings.forEach((h2, index) => {
            const id = `section-${index}`;
            h2.id = id;
            const link = document.createElement('a');
            link.href = `#${id}`;
            link.className = 'toc-link';
            link.innerText = h2.innerText;
            tocContainer.appendChild(link);
        });
    }

    // 3. Simple Smooth Scroll
    document.querySelectorAll('.toc-link').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({ behavior: 'smooth' });
        });
    });
</script>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>