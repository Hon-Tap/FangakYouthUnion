<?php

// blog-single.php – Dedicated Single Blog Post View (PDO-Based)
declare(strict_types=1);

// Database connection (PDO) - Assuming db.php provides a $pdo object
include_once __DIR__ . "/../app/config/db.php"; 

// ===================================================================
// DATA FETCHING (PDO)
// ===================================================================

$slug = $_GET['slug'] ?? null;

if (!$slug || !isset($pdo)) {
    // Redirect to index if no slug or database not connected
    header("Location: blog.php");
    exit;
}

// 1. Fetch main post by slug
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
    $pageTitle = "Post Not Found";
    include_once __DIR__ . "/../app/views/layouts/header.php";
    echo '<div style="max-width: 800px; margin: 100px auto; text-align: center;">';
    echo '<h1 style="color: #c0392b;">404 Post Not Found 😢</h1>';
    echo '<p>The article you are looking for does not exist or has been moved.</p>';
    echo '<a href="blog.php" style="color: #0b6026;">Go back to the blog home</a>';
    echo '</div>';
    include_once __DIR__ . "/../app/views/layouts/footer.php";
    exit;
}

$pageTitle = htmlspecialchars($post['title']) . " - Fangak Youth Union";

// 2. Fetch Recent Posts (Sidebar)
$recentStmt = $pdo->prepare("
    SELECT p.title, p.slug, p.featured_image, c.name AS category_name
    FROM blog_posts p 
    JOIN categories c ON p.category_id = c.id 
    WHERE p.slug != :current_slug 
    ORDER BY p.created_at DESC 
    LIMIT 4
");
$recentStmt->execute(['current_slug' => $slug]);
$recentPosts = $recentStmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Fetch Categories (Sidebar)
$categoryStmt = $pdo->query("SELECT id, name, slug FROM categories ORDER BY name ASC");
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Fetch Comments for the Post
$commentStmt = $pdo->prepare("
    SELECT user_name, comment_body, created_at 
    FROM blog_comments 
    WHERE post_id = :id 
    ORDER BY created_at DESC
");
$commentStmt->execute(['id' => $post['id']]);
$comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);

// Base URL for images (Adjust this based on your actual setup)
$baseUrl = "/images/"; 

// ===================================================================
// HTML OUTPUT
// ===================================================================

include_once __DIR__ . "/../app/views/layouts/header.php";
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    /* VARIABLES (Consistent with previous rewrite) */
    :root {
        --color-primary: #0f5132; /* Dark Green */
        --color-accent: #d4a017;  /* Gold/Yellow */
        --color-dark: #1a1a1a;
        --color-light: #ffffff;
        --color-muted: #666;
        --color-surface: #f8f9fa;
        --color-border: #e5e7eb;
        --radius-card: 12px;
        --shadow-elevation: 0 4px 15px rgba(0,0,0,0.08);
        --max-w: 1200px;
    }

    /* ===== BLOG SINGLE PAGE LAYOUT ===== */
    .blog-single-container {
        display: grid;
        grid-template-columns: 2.5fr 1fr; /* Main content wider than sidebar */
        gap: 40px;
        padding: 60px 20px;
        max-width: var(--max-w);
        margin: auto;
    }

    .blog-main { padding-bottom: 40px; }
    .blog-sidebar { 
        display: flex; 
        flex-direction: column; 
        gap: 30px; 
    }

    @media (max-width: 900px) {
        .blog-single-container { grid-template-columns: 1fr; }
    }

    /* ARTICLE STYLES */
    .blog-post-header img {
        width: 100%;
        height: 400px;
        border-radius: var(--radius-card);
        margin-bottom: 30px;
        object-fit: cover;
    }

    .blog-post h1 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(2rem, 4vw, 3.5rem);
        font-weight: 700;
        margin-bottom: 15px;
        color: var(--color-dark);
        line-height: 1.1;
    }

    .blog-post .meta {
        color: var(--color-muted);
        font-size: 0.95rem;
        margin-bottom: 30px;
        display: flex;
        gap: 20px;
    }
    .blog-post .meta i { color: var(--color-accent); margin-right: 5px; }

    .blog-post-content p {
        line-height: 1.8;
        font-size: 1.1rem;
        color: var(--color-dark);
        margin-bottom: 20px;
    }
    .blog-post-content img { max-width: 100%; height: auto; border-radius: 10px; margin: 20px 0; }

    /* SIDEBAR STYLES (WIDGETS) */
    .sidebar-widget {
        background: var(--color-surface); 
        padding: 24px; 
        border-radius: var(--radius-card); 
        border: 1px solid var(--color-border);
    }
    
    .sidebar-widget h3 {
        font-size: 1.3rem;
        margin-bottom: 15px;
        border-bottom: 2px solid var(--color-primary);
        padding-bottom: 8px;
        color: var(--color-primary);
    }

    /* Recent Posts Card */
    .blog-card-small {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
        align-items: center;
        padding-bottom: 15px;
        border-bottom: 1px dotted var(--color-border);
    }
    .blog-card-small:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }

    .blog-card-small img {
        width: 90px;
        height: 70px;
        object-fit: cover;
        border-radius: 8px;
        flex-shrink: 0;
    }

    .blog-card-small a {
        color: var(--color-dark);
        text-decoration: none;
        font-weight: 600;
        font-size: 1rem;
        display: block;
        line-height: 1.4;
    }

    .blog-card-small a:hover {
        color: var(--color-primary);
    }

    /* Categories List */
    .blog-categories {
        list-style: none;
        padding: 0;
    }

    .blog-categories li {
        margin-bottom: 8px;
        padding: 5px 0;
    }

    .blog-categories a {
        color: var(--color-primary);
        text-decoration: none;
        display: block;
    }

    .blog-categories a:hover {
        text-decoration: underline;
    }
    
    /* Comments Section */
    .comments-area {
        margin-top: 50px;
        padding-top: 30px;
        border-top: 1px solid var(--color-border);
    }
    .comment-list { list-style: none; padding: 0; margin: 20px 0; }
    .comment-item { 
        padding: 15px 20px; background: var(--color-surface); 
        border: 1px solid var(--color-border);
        border-radius: 8px; margin-bottom: 15px; 
    }
    .comment-item strong { display: block; font-size: 1.1rem; color: var(--color-primary); }
    .comment-item .comment-meta { font-size: 0.8rem; margin-top: 2px; margin-bottom: 10px; }
    .comment-form textarea { width: 100%; padding: 12px; border: 1px solid var(--color-border); border-radius: 8px; margin-bottom: 10px; background: var(--color-light); color: var(--color-dark); }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px; }
    .form-row input { padding: 10px; border: 1px solid var(--color-border); border-radius: 8px; background: var(--color-light); color: var(--color-dark); width: 100%; }
    .btn-submit { background: var(--color-primary); color: var(--color-light); padding: 10px 15px; border: none; border-radius: 6px; cursor: pointer; transition: background 0.2s; }
    .btn-submit:hover { background: var(--color-accent); }


    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, #1f7a4b, #0f5132); /* Use primary colors */
        padding: 60px 20px;
        color: #fff;
        text-align: center;
    }

    .cta-section h2 {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        margin-bottom: 15px;
    }

    .cta-section a {
        background: #fff;
        color: var(--color-primary);
        padding: 14px 32px;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        display: inline-block;
        transition: background 0.3s ease, transform 0.2s;
    }

    .cta-section a:hover {
        background: #f0f0f0;
        transform: translateY(-2px);
    }
</style>

<div class="blog-single-container">
    <div class="blog-main">
        <article class="blog-post">
            
            <header class="blog-post-header">
                 <img src="<?= $post['featured_image'] ? $baseUrl . htmlspecialchars($post['featured_image']) : $baseUrl . 'default-post.jpg' ?>" 
                      alt="<?= htmlspecialchars($post['title']) ?>">
                 <h1><?= htmlspecialchars($post['title']) ?></h1>
                 <div class="meta">
                    <span><i class="fa-solid fa-tag"></i> <a href="blog.php?category=<?= htmlspecialchars($post['category_slug']) ?>" style="color: inherit; text-decoration: underline;"><?= htmlspecialchars($post['category_name']) ?></a></span>
                    <span><i class="fa-solid fa-user-pen"></i> By <?= htmlspecialchars($post['author'] ?: 'FYU Contributor') ?></span>
                    <span><i class="fa-regular fa-calendar-days"></i> <?= date("F j, Y", strtotime($post['created_at'])) ?></span>
                </div>
            </header>
            
            <div class="blog-post-content">
                <?= $post['content'] ?>
                </div>
        </article>
        
        <section class="comments-area">
            <h3><i class="fa-solid fa-comments"></i> Reader Comments (<?= count($comments) ?>)</h3>
            
            <ul id="commentList" class="comment-list">
                <?php if ($comments): foreach($comments as $comment): ?>
                <li class="comment-item">
                    <strong><?= htmlspecialchars($comment['user_name']) ?></strong>
                    <div class="meta comment-meta">
                        <span><i class="fa-solid fa-clock"></i> <?= date('d M Y', strtotime($comment['created_at'])) ?></span>
                    </div>
                    <p><?= nl2br(htmlspecialchars($comment['comment_body'])) ?></p>
                </li>
                <?php endforeach; else: ?>
                <li style="color:var(--color-muted); padding: 15px;">No comments yet. Be the first to share your thoughts!</li>
                <?php endif; ?>
            </ul>
            
            <h4>Leave a Reply</h4>
            <form id="commentForm" class="comment-form" onsubmit="handleComment(event)">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <div class="form-row">
                    <input type="text" name="user_name" placeholder="Name *" required>
                    <input type="email" name="user_email" placeholder="Email (private) *" required>
                </div>
                <textarea name="comment_body" rows="4" placeholder="Write your thought *" required></textarea>
                <button type="submit" class="btn-submit">Post Comment</button>
                <div id="commentMsg" style="margin-top: 10px; font-size: 0.9rem;"></div>
            </form>
        </section>
    </div>

    <aside class="blog-sidebar">
        <div class="sidebar-widget">
            <h3><i class="fa-solid fa-newspaper"></i> Recent Posts</h3>
            <?php foreach ($recentPosts as $recent): ?>
                <div class="blog-card-small">
                    <img src="<?= $recent['featured_image'] ? $baseUrl . htmlspecialchars($recent['featured_image']) : $baseUrl . 'default-post.jpg' ?>" 
                         alt="<?= htmlspecialchars($recent['title']) ?>">
                    <div><a href="blog.php?slug=<?= htmlspecialchars($recent['slug']) ?>"><?= htmlspecialchars($recent['title']) ?></a></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="sidebar-widget">
            <h3><i class="fa-solid fa-list-ul"></i> Categories</h3>
            <ul class="blog-categories">
                <li><a href="blog.php">All Articles</a></li>
                <?php foreach ($categories as $cat): ?>
                    <li><a href="blog.php?category=<?= htmlspecialchars($cat['slug'] ?? $cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        </aside>
</div>

<section class="cta-section">
    <h2>Want More Insights?</h2>
    <p>Subscribe to our blog to receive the latest news and articles on our initiatives.</p>
    <a href="contact.php">Subscribe Now <i class="fa-solid fa-arrow-right"></i></a>
</section>

<?php
// Note: PDO connections are usually persistent and don't need an explicit close() in procedural scripts, but if you need to close:
// $pdo = null; 
include_once __DIR__ . "/../app/views/layouts/footer.php";
?>

<script>
    // AJAX for handling comments - RE-INCLUDED from the original blog.php rewrite for consistency.
    function handleComment(e) {
        e.preventDefault();
        const form = e.target;
        const data = new FormData(form);
        data.append('action', 'add_comment'); // Assuming action is handled in blog.php

        const msg = document.getElementById('commentMsg');
        const submitBtn = form.querySelector('button[type="submit"]');

        msg.textContent = 'Posting...';
        msg.style.color = 'var(--color-muted)';
        submitBtn.disabled = true;

        // Send POST request to the main blog handler (blog.php)
        fetch('blog.php', { method: 'POST', body: data })
        .then(res => res.json())
        .then(res => {
            msg.textContent = res.message;
            msg.style.color = res.status === 'success' ? 'var(--color-primary)' : 'red';
            submitBtn.disabled = false;

            if(res.status === 'success') {
                const commentList = document.getElementById('commentList');
                // Create a new comment element
                const li = document.createElement('li');
                li.className = 'comment-item';
                li.innerHTML = `<strong>${res.comment.user_name} (Just now)</strong>
                                <div class="meta comment-meta"><span><i class="fa-solid fa-clock"></i> Just now</span></div>
                                <p>${res.comment.comment_body}</p>`;
                commentList.prepend(li); // Add to the top
                form.reset();
            }
        })
        .catch(() => {
            msg.textContent = 'Network error. Failed to post comment.';
            msg.style.color = 'red';
            submitBtn.disabled = false;
        });
    }
</script>