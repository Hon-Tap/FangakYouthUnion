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
    echo '<h1 style="color: #c0392b; font-family: \'Playfair Display\', serif;">404 Post Not Found 😢</h1>';
    echo '<p>The article you are looking for does not exist or has been moved.</p>';
    echo '<a href="blog.php" style="color: #0b6026; font-weight: bold; text-decoration: underline;">Go back to the blog home</a>';
    echo '</div>';
    include_once __DIR__ . "/../app/views/layouts/footer.php";
    exit;
}

$pageTitle = htmlspecialchars($post['title']) . " - Fangak Youth Union";

// Calculate Estimated Reading Time (Average reading speed: 200 words per minute)
$wordCount = str_word_count(strip_tags($post['content']));
$readingTime = ceil($wordCount / 200);
if ($readingTime < 1) $readingTime = 1;

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

// Base URL for images
$baseUrl = "/images/"; 

// ===================================================================
// HTML OUTPUT
// ===================================================================

include_once __DIR__ . "/../app/views/layouts/header.php";
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;1,400&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    :root {
        --color-primary: #0b6026; 
        --color-primary-dark: #064418;
        --color-dark: #1a1a1a;
        --color-text: #334155;
        --color-muted: #64748b;
        --color-surface: #ffffff;
        --color-bg: #f8fafc;
        --color-border: #e2e8f0;
        --radius-card: 16px;
        --max-w: 1100px;
    }

    body {
        background-color: var(--color-bg);
        font-family: 'Inter', sans-serif; /* Base UI font */
    }

    /* ===== READING PROGRESS BAR ===== */
    .progress-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: transparent;
        z-index: 9999;
    }
    .progress-bar {
        height: 100%;
        background: var(--color-primary);
        width: 0%;
        transition: width 0.1s ease-out;
    }

    /* ===== LAYOUT ===== */
    .blog-single-container {
        display: grid;
        grid-template-columns: 2fr 1fr; 
        gap: 60px;
        padding: 60px 20px;
        max-width: var(--max-w);
        margin: auto;
    }

    @media (max-width: 900px) {
        .blog-single-container { grid-template-columns: 1fr; gap: 40px; }
    }

    /* ===== MAIN ARTICLE ===== */
    .blog-post {
        background: var(--color-surface);
        padding: 40px;
        border-radius: var(--radius-card);
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }

    @media (max-width: 768px) {
        .blog-post { padding: 20px; }
    }

    .blog-post-header img {
        width: 100%;
        height: 450px;
        border-radius: 12px;
        margin-bottom: 30px;
        object-fit: cover;
    }

    .blog-category-badge {
        display: inline-block;
        background: rgba(11, 96, 38, 0.1);
        color: var(--color-primary);
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 15px;
        text-decoration: none;
    }

    .blog-post h1 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(2.2rem, 4vw, 3.5rem);
        font-weight: 800;
        margin-bottom: 20px;
        color: var(--color-dark);
        line-height: 1.15;
    }

    .post-meta-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid var(--color-border);
        border-bottom: 1px solid var(--color-border);
        padding: 15px 0;
        margin-bottom: 40px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .post-author-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .author-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: var(--color-primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .author-details {
        display: flex;
        flex-direction: column;
    }

    .author-name { font-weight: 600; color: var(--color-dark); font-size: 1rem; }
    .post-date-time { font-size: 0.85rem; color: var(--color-muted); }

    .social-share {
        display: flex;
        gap: 10px;
    }

    .social-share a {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--color-bg);
        color: var(--color-text);
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid var(--color-border);
    }

    .social-share a:hover {
        background: var(--color-primary);
        color: white;
        border-color: var(--color-primary);
    }

    /* EDITORIAL CONTENT STYLING */
    .blog-post-content {
        font-family: 'Lora', serif;
        font-size: 1.18rem;
        line-height: 1.9;
        color: #2c3e50;
    }

    /* Drop Cap for the first paragraph */
    .blog-post-content > p:first-of-type::first-letter {
        float: left;
        font-size: 4rem;
        line-height: 0.8;
        padding-top: 4px;
        padding-right: 12px;
        padding-left: 3px;
        font-family: 'Playfair Display', serif;
        color: var(--color-primary);
        font-weight: 800;
    }

    .blog-post-content p { margin-bottom: 25px; }
    
    .blog-post-content h2, 
    .blog-post-content h3 {
        font-family: 'Playfair Display', serif;
        color: var(--color-dark);
        margin-top: 40px;
        margin-bottom: 15px;
        font-weight: 700;
    }

    .blog-post-content img {
        width: 100%;
        height: auto;
        border-radius: 12px;
        margin: 30px 0;
    }

    .blog-post-content blockquote {
        font-style: italic;
        font-size: 1.3rem;
        border-left: 4px solid var(--color-primary);
        padding-left: 20px;
        margin: 30px 0;
        color: var(--color-primary-dark);
        background: rgba(11, 96, 38, 0.03);
        padding: 20px;
        border-radius: 0 8px 8px 0;
    }

    /* ===== SIDEBAR (STICKY) ===== */
    .blog-sidebar { 
        display: flex; 
        flex-direction: column; 
        gap: 30px; 
        position: sticky;
        top: 80px; /* Sticks nicely when scrolling */
        align-self: start;
    }

    .sidebar-widget {
        background: var(--color-surface); 
        padding: 25px; 
        border-radius: var(--radius-card); 
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    
    .sidebar-widget h3 {
        font-family: 'Inter', sans-serif;
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 20px;
        color: var(--color-dark);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Recent Posts Card */
    .blog-card-small {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
        align-items: center;
    }
    .blog-card-small:last-child { margin-bottom: 0; }

    .blog-card-small img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        flex-shrink: 0;
    }

    .blog-card-small a {
        color: var(--color-dark);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .blog-card-small a:hover { color: var(--color-primary); }
    .blog-card-small .post-date { font-size: 0.8rem; color: var(--color-muted); margin-top: 5px; display: block; }

    /* Categories List */
    .blog-categories { list-style: none; padding: 0; margin: 0; }
    .blog-categories li { margin-bottom: 10px; }
    .blog-categories a {
        color: var(--color-text);
        text-decoration: none;
        display: flex;
        justify-content: space-between;
        padding: 8px 12px;
        background: var(--color-bg);
        border-radius: 6px;
        transition: all 0.2s;
        font-size: 0.95rem;
    }
    .blog-categories a:hover { background: var(--color-primary); color: white; }
    
    /* ===== COMMENTS SECTION ===== */
    .comments-area {
        background: var(--color-surface);
        padding: 40px;
        border-radius: var(--radius-card);
        margin-top: 40px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }

    .comments-area h3 {
        font-family: 'Playfair Display', serif;
        font-size: 1.8rem;
        margin-bottom: 30px;
        color: var(--color-dark);
    }

    .comment-item { 
        padding: 20px 0;
        border-bottom: 1px solid var(--color-border);
    }
    .comment-item:last-child { border-bottom: none; }
    
    .comment-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }
    
    .comment-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        background: #e2e8f0; color: #64748b;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold;
    }

    .comment-item strong { display: block; font-size: 1.05rem; color: var(--color-dark); }
    .comment-item .comment-meta { font-size: 0.8rem; color: var(--color-muted); }
    .comment-item p { font-size: 1rem; color: var(--color-text); line-height: 1.6; }

    /* Comment Form */
    .comment-form-wrap { margin-top: 40px; background: var(--color-bg); padding: 30px; border-radius: 12px; }
    .comment-form-wrap h4 { font-family: 'Playfair Display', serif; font-size: 1.4rem; margin-bottom: 20px; }
    
    .comment-form textarea, .comment-form input { 
        width: 100%; padding: 14px; border: 1px solid var(--color-border); 
        border-radius: 8px; font-family: 'Inter', sans-serif;
        background: white; margin-bottom: 15px; box-sizing: border-box;
    }
    .comment-form textarea:focus, .comment-form input:focus {
        outline: none; border-color: var(--color-primary);
    }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    
    .btn-submit { 
        background: var(--color-primary); color: white; 
        padding: 14px 24px; border: none; border-radius: 8px; 
        font-weight: 600; cursor: pointer; transition: background 0.2s; 
    }
    .btn-submit:hover { background: var(--color-primary-dark); }

</style>

<div class="progress-container">
    <div class="progress-bar" id="readingProgressBar"></div>
</div>

<div class="blog-single-container">
    <div class="blog-main">
        <article class="blog-post">
            
            <header class="blog-post-header">
                <a href="blog.php?category=<?= htmlspecialchars($post['category_slug']) ?>" class="blog-category-badge">
                    <?= htmlspecialchars($post['category_name']) ?>
                </a>
                
                <h1><?= htmlspecialchars($post['title']) ?></h1>
                
                <div class="post-meta-row">
                    <div class="post-author-info">
                        <div class="author-avatar"><i class="fa-solid fa-user"></i></div>
                        <div class="author-details">
                            <span class="author-name"><?= htmlspecialchars($post['author'] ?: 'FYU Desk') ?></span>
                            <span class="post-date-time">
                                <?= date("M j, Y", strtotime($post['created_at'])) ?> • <?= $readingTime ?> min read
                            </span>
                        </div>
                    </div>
                    
                    <div class="social-share">
                        <a href="#" title="Share on Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" title="Share on Twitter/X"><i class="fa-brands fa-x-twitter"></i></a>
                        <a href="#" title="Share on LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#" title="Copy Link" onclick="navigator.clipboard.writeText(window.location.href); alert('Link copied!'); return false;"><i class="fa-solid fa-link"></i></a>
                    </div>
                </div>

                <img src="<?= $post['featured_image'] ? $baseUrl . htmlspecialchars($post['featured_image']) : $baseUrl . 'default-post.jpg' ?>" 
                     alt="<?= htmlspecialchars($post['title']) ?>">
            </header>
            
            <div class="blog-post-content" id="articleContent">
                <?= $post['content'] ?>
            </div>
        </article>
        
        <section class="comments-area">
            <h3>Reader Comments (<?= count($comments) ?>)</h3>
            
            <ul id="commentList" class="comment-list" style="list-style: none; padding: 0;">
                <?php if ($comments): foreach($comments as $comment): ?>
                <li class="comment-item">
                    <div class="comment-header">
                        <div class="comment-avatar"><?= strtoupper(substr($comment['user_name'], 0, 1)) ?></div>
                        <div>
                            <strong><?= htmlspecialchars($comment['user_name']) ?></strong>
                            <div class="comment-meta"><?= date('F j, Y \a\t g:i a', strtotime($comment['created_at'])) ?></div>
                        </div>
                    </div>
                    <p><?= nl2br(htmlspecialchars($comment['comment_body'])) ?></p>
                </li>
                <?php endforeach; else: ?>
                <li style="color:var(--color-muted); padding: 15px 0;">No comments yet. Be the first to start the conversation!</li>
                <?php endif; ?>
            </ul>
            
            <div class="comment-form-wrap">
                <h4>Leave a Reply</h4>
                <form id="commentForm" class="comment-form" onsubmit="handleComment(event)">
                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                    <div class="form-row">
                        <input type="text" name="user_name" placeholder="Full Name" required>
                        <input type="email" name="user_email" placeholder="Email Address (Will not be published)" required>
                    </div>
                    <textarea name="comment_body" rows="5" placeholder="Share your thoughts on this article..." required></textarea>
                    <button type="submit" class="btn-submit">Post Comment</button>
                    <div id="commentMsg" style="margin-top: 10px; font-size: 0.95rem; font-weight: 500;"></div>
                </form>
            </div>
        </section>
    </div>

    <aside class="blog-sidebar">
        <div class="sidebar-widget">
            <h3>Trending Now</h3>
            <?php foreach ($recentPosts as $recent): ?>
                <div class="blog-card-small">
                    <img src="<?= $recent['featured_image'] ? $baseUrl . htmlspecialchars($recent['featured_image']) : $baseUrl . 'default-post.jpg' ?>" 
                         alt="<?= htmlspecialchars($recent['title']) ?>">
                    <div>
                        <a href="blog.php?slug=<?= htmlspecialchars($recent['slug']) ?>"><?= htmlspecialchars($recent['title']) ?></a>
                        <span class="post-date"><?= htmlspecialchars($recent['category_name']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="sidebar-widget">
            <h3>Topics</h3>
            <ul class="blog-categories">
                <li><a href="blog.php">All Articles <i class="fa-solid fa-chevron-right" style="font-size: 0.8rem; margin-top:4px;"></i></a></li>
                <?php foreach ($categories as $cat): ?>
                    <li><a href="blog.php?category=<?= htmlspecialchars($cat['slug'] ?? $cat['name']) ?>">
                        <?= htmlspecialchars($cat['name']) ?> 
                        <i class="fa-solid fa-chevron-right" style="font-size: 0.8rem; margin-top:4px;"></i>
                    </a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </aside>
</div>

<?php
include_once __DIR__ . "/../app/views/layouts/footer.php";
?>

<script>
    // SCROLL PROGRESS INDICATOR LOGIC
    window.onscroll = function() { updateProgressBar() };

    function updateProgressBar() {
        var winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        var height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        var scrolled = (winScroll / height) * 100;
        document.getElementById("readingProgressBar").style.width = scrolled + "%";
    }

    // AJAX COMMENT HANDLING
    function handleComment(e) {
        e.preventDefault();
        const form = e.target;
        const data = new FormData(form);
        data.append('action', 'add_comment'); 

        const msg = document.getElementById('commentMsg');
        const submitBtn = form.querySelector('button[type="submit"]');

        msg.textContent = 'Publishing comment...';
        msg.style.color = 'var(--color-muted)';
        submitBtn.disabled = true;

        fetch('blog.php', { method: 'POST', body: data })
        .then(res => res.json())
        .then(res => {
            msg.textContent = res.message;
            msg.style.color = res.status === 'success' ? 'var(--color-primary)' : '#c0392b';
            submitBtn.disabled = false;

            if(res.status === 'success') {
                const commentList = document.getElementById('commentList');
                const initial = res.comment.user_name.charAt(0).toUpperCase();
                
                // Remove the "No comments yet" li if it exists
                if(commentList.children[0] && commentList.children[0].textContent.includes("No comments yet")) {
                    commentList.innerHTML = '';
                }

                const li = document.createElement('li');
                li.className = 'comment-item';
                li.innerHTML = `
                    <div class="comment-header">
                        <div class="comment-avatar">${initial}</div>
                        <div>
                            <strong>${res.comment.user_name}</strong>
                            <div class="comment-meta">Just now</div>
                        </div>
                    </div>
                    <p>${res.comment.comment_body}</p>
                `;
                commentList.prepend(li);
                form.reset();
                setTimeout(() => { msg.textContent = ''; }, 4000);
            }
        })
        .catch(() => {
            msg.textContent = 'Network error. Failed to post comment.';
            msg.style.color = '#c0392b';
            submitBtn.disabled = false;
        });
    }
</script>