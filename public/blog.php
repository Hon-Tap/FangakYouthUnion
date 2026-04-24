<?php
// blog.php – Pro Editorial News Layout for FYU
declare(strict_types=1);

// 1. CONFIGURATION & DATABASE
require_once __DIR__ . "/../app/config/db.php";

// ===================================================================
// IMAGE HELPER FUNCTION
// ===================================================================
function getImagePath($imageName): string {
    $baseUrl = $GLOBALS['baseUrl'] ?? '/public/';
    $folder = 'uploads/news/';
    $defaultLogo = 'FYU-LOGO.jpg';

    if (empty($imageName)) {
        return $baseUrl . $folder . $defaultLogo;
    }

    if (strpos((string)$imageName, 'http') === 0) {
        return (string)$imageName;
    }

    return $baseUrl . $folder . htmlspecialchars((string)$imageName);
}

// ===================================================================
// 2. API HANDLER (AJAX REQUESTS)
// ===================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
    header("Content-Type: application/json");
    if (!$pdo) {
        echo json_encode(['status' => 'error', 'message' => 'Database unavailable.']);
        exit;
    }

    $response = ['status' => 'error', 'message' => 'Invalid action'];

    try {
        switch ($_POST['action']) {
            case 'subscribe':
                $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
                if (!$email) {
                    $response['message'] = "Please enter a valid email address.";
                    break;
                }
                $chk = $pdo->prepare("SELECT id FROM subscribers WHERE email = ?");
                $chk->execute([$email]);
                if ($chk->fetch()) {
                    $response['message'] = "You are already subscribed!";
                    break;
                }
                $stmt = $pdo->prepare("INSERT INTO subscribers (email) VALUES (?)");
                if ($stmt->execute([$email])) {
                    $response = ['status' => 'success', 'message' => 'Welcome to our community!'];
                }
                break;

            case 'add_comment':
                $postId = (int)($_POST['post_id'] ?? 0);
                $name   = trim(strip_tags($_POST['user_name'] ?? ''));
                $email  = trim(strip_tags($_POST['user_email'] ?? ''));
                $body   = trim(strip_tags($_POST['comment_body'] ?? ''));

                if ($postId <= 0 || $name === '' || $body === '') {
                    $response['message'] = "Name and comment are required.";
                    break;
                }

                $sql = "INSERT INTO blog_comments (post_id, user_name, user_email, comment_body, created_at) VALUES (:pid, :name, :email, :body, NOW())";
                if ($pdo->prepare($sql)->execute(['pid' => $postId, 'name' => $name, 'email' => $email, 'body' => $body])) {
                    $response = [
                        'status' => 'success',
                        'message' => 'Comment posted successfully!',
                        'comment' => [
                            'user_name' => htmlspecialchars($name),
                            'comment_body' => nl2br(htmlspecialchars($body)),
                            'created_at' => 'Just Now'
                        ]
                    ];
                }
                break;
        }
    } catch (Throwable $e) {
        error_log("Blog AJAX error: " . $e->getMessage());
        $response['message'] = "Server error.";
    }
    echo json_encode($response);
    exit;
}

// ===================================================================
// 3. PAGE LOGIC (DATA FETCHING)
// ===================================================================
$postId = (int)($_GET['post'] ?? 0);
$viewMode = ($postId > 0) ? 'single' : 'index';

$currentPost = null;
$featuredPost = null;
$latestGrid = [];
$relatedPosts = [];
$comments = [];
$pageTitle = "News Hub - Fangak Youth Union";

// Get current URL for sharing capabilities
$currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

if ($pdo) {
    try {
        if ($viewMode === 'single') {
            $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
            $stmt->execute([$postId]);
            $currentPost = $stmt->fetch();

            if ($currentPost) {
                $cStmt = $pdo->prepare("SELECT * FROM blog_comments WHERE post_id = ? ORDER BY created_at DESC");
                $cStmt->execute([$postId]);
                $comments = $cStmt->fetchAll();
                
                $rStmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id != ? ORDER BY created_at DESC LIMIT 3");
                $rStmt->execute([$postId]);
                $relatedPosts = $rStmt->fetchAll();
                $pageTitle = htmlspecialchars($currentPost['title']) . " - FYU";
            } else {
                header("Location: blog.php");
                exit;
            }
        } else {
            // Fetch top 7 posts for the visual grid
            $stmt = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 7");
            $allPosts = $stmt->fetchAll();
            if (!empty($allPosts)) {
                $featuredPost = $allPosts[0];
                $latestGrid = array_slice($allPosts, 1);
            }
        }
    } catch (Throwable $e) { error_log("Blog query error: " . $e->getMessage()); }
}

include_once __DIR__ . "/../app/views/layouts/header.php";
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Inter:wght@300;400;500;600;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
    :root {
        --fyu-green: #064e3b;
        --fyu-green-hover: #04382a;
        --fyu-gold: #d97706;
        --text-dark: #111827;
        --text-body: #374151;
        --text-light: #6b7280;
        --bg-soft: #f9fafb;
        --border-color: #e5e7eb;
        --font-heading: 'Playfair Display', serif;
        --font-sans: 'Inter', sans-serif;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body { background-color: #fff; color: var(--text-body); font-family: var(--font-sans); -webkit-font-smoothing: antialiased; margin: 0; padding: 0; }
    a { text-decoration: none; color: inherit; transition: var(--transition); }

    /* --- READING PROGRESS BAR --- */
    .reading-progress { 
        position: fixed; 
        top: 0; left: 0; 
        width: 0%; height: 5px; 
        background: var(--fyu-gold); 
        z-index: 9999; 
        transition: width 0.15s ease-out;
        box-shadow: 0 0 10px rgba(217, 119, 6, 0.5);
    }

    /* --- BEAUTIFIED COMPONENTS --- */
    .section-label { 
        font-size: 0.85rem; 
        font-weight: 800; 
        text-transform: uppercase; 
        letter-spacing: 0.15em; 
        color: var(--fyu-gold); 
        display: inline-block; 
        margin-bottom: 1rem; 
    }
    
    .news-card { display: flex; flex-direction: column; height: 100%; cursor: pointer; group; }
    
    /* Improved Image Handling */
    .img-container { 
        position: relative; 
        overflow: hidden; 
        border-radius: 12px; 
        background: var(--bg-soft); 
        width: 100%; 
        isolation: isolate; 
    }
    .img-container img { 
        width: 100%; 
        height: 100%; 
        object-fit: cover; 
        object-position: center 20%;
        transition: transform 0.8s cubic-bezier(0.165, 0.84, 0.44, 1); 
    }
    .news-card:hover .img-container img { transform: scale(1.05); }

    /* --- INDEX: HERO BENTO GRID --- */
    .bento-container { max-width: 1300px; margin: 3rem auto; padding: 0 1.5rem; display: grid; grid-template-columns: 1.6fr 1fr; gap: 3rem; }
    
    .hero-main .img-container { aspect-ratio: 16/9; margin-bottom: 1.5rem; box-shadow: var(--shadow-md); }
    .hero-main h1 { font-family: var(--font-heading); font-size: clamp(2rem, 4vw, 3.5rem); line-height: 1.15; margin-bottom: 1rem; color: var(--text-dark); transition: var(--transition); }
    .hero-main:hover h1 { color: var(--fyu-green); }
    .hero-main p { font-size: 1.125rem; color: var(--text-light); line-height: 1.7; max-width: 95%; }

    .hero-side { display: flex; flex-direction: column; gap: 0; }
    .hero-side-header { font-size: 1.25rem; font-family: var(--font-heading); font-weight: 900; color: var(--text-dark); border-bottom: 2px solid var(--text-dark); padding-bottom: 0.5rem; margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 0.05em;}
    
    .side-item { display: grid; grid-template-columns: 100px 1fr; gap: 1.25rem; padding: 1.25rem 0; border-bottom: 1px solid var(--border-color); align-items: center; }
    .side-item:last-child { border-bottom: none; }
    .side-item .img-container { aspect-ratio: 1/1; border-radius: 8px; }
    .side-item h3 { font-size: 1.1rem; font-weight: 700; color: var(--text-dark); line-height: 1.4; transition: var(--transition); }
    .side-item:hover h3 { color: var(--fyu-green); }

    /* --- LATEST POSTS GRID --- */
    .latest-grid-section { background: var(--bg-soft); padding: 5rem 0; margin-top: 4rem; border-top: 1px solid var(--border-color); }
    .grid-wrapper { max-width: 1300px; margin: 0 auto; padding: 0 1.5rem; }
    .fyu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2.5rem; }
    
    .grid-card .img-container { aspect-ratio: 4/3; margin-bottom: 1.5rem; box-shadow: var(--shadow-sm); }
    .grid-card h3 { font-family: var(--font-heading); font-size: 1.5rem; color: var(--text-dark); line-height: 1.3; margin-bottom: 0.75rem; }
    .grid-card:hover h3 { color: var(--fyu-green); }

    /* --- SINGLE POST --- */
    .article-container { max-width: 860px; margin: 5rem auto; padding: 0 1.5rem; }
    .article-header { text-align: left; margin-bottom: 2.5rem; }
    .article-header h1 { 
        font-family: var(--font-heading); 
        font-size: clamp(2.5rem, 5vw, 4.5rem); 
        margin: 0.5rem 0 1.5rem 0; 
        line-height: 1.1; 
        color: var(--text-dark); 
        letter-spacing: -0.02em;
    }
    .article-meta { 
        font-weight: 500; 
        color: var(--text-light); 
        font-size: 1rem; 
        display: flex; 
        align-items: center; 
        gap: 12px; 
        border-top: 1px solid var(--border-color);
        padding-top: 1.5rem;
    }
    .article-meta img { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; }
    
    .hero-img-full { 
        width: 100%; 
        max-height: 65vh; 
        object-fit: cover; 
        object-position: center 20%;
        border-radius: 16px; 
        margin-bottom: 4rem; 
        box-shadow: var(--shadow-lg); 
    }
    
    .article-content { 
        font-size: 1.1875rem; 
        line-height: 1.85; 
        color: var(--text-body); 
    }
    .article-content p { margin-bottom: 2rem; }
    
    /* Creative Editorial Text Alignment & Drop Cap */
    .article-content p:first-of-type::first-letter { 
        font-family: var(--font-heading); 
        font-size: 6rem; 
        float: left; 
        line-height: 0.75; 
        padding: 0.15em 0.1em 0 0; 
        color: var(--text-dark); 
        font-weight: 900;
    }
    .article-content img { 
        max-width: 100%; 
        height: auto; 
        border-radius: 12px; 
        margin: 2.5rem 0; 
        box-shadow: var(--shadow-sm);
    }
    .article-content h2, .article-content h3 {
        font-family: var(--font-heading);
        color: var(--text-dark);
        margin-top: 3rem;
        margin-bottom: 1.5rem;
        line-height: 1.3;
    }

    /* --- SHARE BAR --- */
    .share-bar { 
        margin-top: 5rem; 
        padding: 2rem 0; 
        border-top: 1px solid var(--border-color); 
        border-bottom: 1px solid var(--border-color); 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        flex-wrap: wrap; 
        gap: 1.5rem; 
    }
    .share-buttons { display: flex; gap: 0.75rem; align-items: center; }
    .share-btn { 
        display: inline-flex; align-items: center; justify-content: center; 
        width: 44px; height: 44px; border-radius: 50%; 
        background: var(--bg-soft); color: var(--text-dark); 
        font-size: 1.2rem; border: 1px solid var(--border-color); 
        transition: var(--transition); cursor: pointer; 
    }
    .share-btn:hover { background: var(--fyu-green); color: white; border-color: var(--fyu-green); transform: translateY(-3px); box-shadow: var(--shadow-md); }
    .copy-btn { padding: 0.5rem 1.25rem; border-radius: 25px; font-size: 0.9rem; font-weight: 600; width: auto; }

    /* --- SIDEBAR & FORMS --- */
    .sidebar-widget { background: white; border: 1px solid var(--border-color); border-radius: 16px; padding: 2.5rem; box-shadow: var(--shadow-sm); }
    .btn-fyu { 
        background: var(--fyu-green); color: white; border: none; 
        padding: 1rem 1.5rem; border-radius: 8px; font-weight: 600; 
        cursor: pointer; transition: var(--transition); 
        display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; font-size: 1rem; 
    }
    .btn-fyu:hover { background: var(--fyu-green-hover); transform: translateY(-2px); box-shadow: var(--shadow-md); }
    .btn-fyu:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }
    
    .form-input { 
        width: 100%; padding: 1rem 1.25rem; border: 1px solid var(--border-color); 
        border-radius: 8px; background: var(--bg-soft); font-family: var(--font-sans); 
        transition: var(--transition); font-size: 1rem;
    }
    .form-input:focus { outline: none; border-color: var(--fyu-green); background: white; box-shadow: 0 0 0 4px rgba(6, 78, 59, 0.1); }

    /* --- TOAST NOTIFICATIONS --- */
    .toast-container { position: fixed; bottom: 2rem; right: 2rem; z-index: 50; display: flex; flex-direction: column; gap: 1rem; }
    .toast { 
        padding: 1.25rem 1.5rem; border-radius: 12px; background: white; 
        box-shadow: var(--shadow-lg); border-left: 5px solid var(--fyu-green); 
        font-weight: 500; display: flex; align-items: center; gap: 1rem; 
        transform: translateX(120%); transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
    }
    .toast.show { transform: translateX(0); }
    .toast.error { border-left-color: #ef4444; }

    @media (max-width: 1024px) {
        .bento-container { grid-template-columns: 1fr; }
        .hero-main h1 { font-size: 2.75rem; }
        .hero-main .img-container { aspect-ratio: 4/3; }
    }
    @media (max-width: 768px) {
        .hero-img-full { max-height: 40vh; margin-bottom: 2rem; }
        .article-content p:first-of-type::first-letter { font-size: 4.5rem; }
        .article-header h1 { font-size: 2.25rem; }
    }
</style>

<div class="main-wrapper">
    <div class="toast-container" id="toastContainer"></div>

    <?php if ($viewMode === 'index'): ?>
        <section class="bento-container">
            <?php if ($featuredPost): ?>
            <div class="hero-main">
                <a href="?post=<?= $featuredPost['id'] ?>" class="news-card">
                    <span class="section-label"><?= htmlspecialchars($featuredPost['category'] ?? 'Major Update') ?></span>
                    <h1><?= htmlspecialchars($featuredPost['title']) ?></h1>
                    <div class="img-container">
                        <img src="<?= getImagePath($featuredPost['image']) ?>" alt="Featured">
                    </div>
                    <p><?= htmlspecialchars(strip_tags(substr($featuredPost['description'], 0, 220))) ?>...</p>
                </a>
            </div>
            <?php endif; ?>

            <div class="hero-side">
                <div class="hero-side-header">Trending Now</div>
                <?php foreach (array_slice($latestGrid, 0, 4) as $post): ?>
                <a href="?post=<?= $post['id'] ?>" class="side-item">
                    <div class="img-container">
                        <img src="<?= getImagePath($post['image']) ?>" alt="Thumbnail">
                    </div>
                    <div>
                        <span class="section-label" style="font-size: 0.65rem; margin-bottom:0.4rem;"><i class="fa-regular fa-clock"></i> <?= date('M d', strtotime($post['created_at'])) ?></span>
                        <h3><?= htmlspecialchars($post['title']) ?></h3>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="latest-grid-section">
            <div class="grid-wrapper">
                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 4rem; flex-wrap: wrap; gap: 2rem;">
                    <div>
                        <span class="section-label">Latest from FYU</span>
                        <h2 style="font-family: var(--font-heading); font-size: 2.75rem; margin:0; color: var(--text-dark);">Recent Dispatches</h2>
                    </div>
                    <div class="sidebar-widget" style="margin:0; padding: 1.5rem; width: 100%; max-width: 420px;">
                        <h4 style="margin-top:0; margin-bottom:15px; font-family: var(--font-heading); font-size: 1.3rem; color: var(--text-dark);">Get The FYU Briefing</h4>
                        <form id="newsletterForm" onsubmit="submitNewsletter(event)" style="display:flex; gap:10px;">
                            <input type="email" name="email" class="form-input" placeholder="Your email address" required style="padding: 0.75rem 1rem;">
                            <button type="submit" class="btn-fyu" style="padding: 0.75rem 1.5rem;"><i class="fa-solid fa-paper-plane"></i></button>
                        </form>
                    </div>
                </div>

                <div class="fyu-grid">
                    <?php foreach (array_slice($latestGrid, 4) as $post): ?>
                    <article>
                        <a href="?post=<?= $post['id'] ?>" class="grid-card news-card">
                            <div class="img-container">
                                <img src="<?= getImagePath($post['image']) ?>" loading="lazy" alt="Post Image">
                            </div>
                            <span class="section-label"><?= htmlspecialchars($post['category'] ?? 'Community') ?></span>
                            <h3><?= htmlspecialchars($post['title']) ?></h3>
                            <p style="color: var(--text-light); font-size: 1rem; line-height: 1.6;">
                                <?= htmlspecialchars(strip_tags(substr($post['description'], 0, 110))) ?>...
                            </p>
                        </a>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

    <?php elseif ($viewMode === 'single' && $currentPost): 
        $encodedUrl = urlencode($currentUrl);
        $encodedTitle = urlencode($currentPost['title']);
    ?>
        <div class="reading-progress" id="progressBar"></div>
        
        <article class="article-container">
            <header class="article-header">
                <span class="section-label"><?= htmlspecialchars($currentPost['category'] ?? 'Achievement') ?></span>
                <h1><?= htmlspecialchars($currentPost['title']) ?></h1>
                
                <div class="article-meta">
                    <span style="color: var(--text-dark); font-weight: 700;">By <?= htmlspecialchars($currentPost['author'] ?? 'Fangak Youth Union Editorial Board') ?></span>
                    <span style="color: var(--border-color);">•</span>
                    <span><?= date('F d, Y', strtotime($currentPost['created_at'])) ?></span>
                </div>
            </header>

            <img src="<?= getImagePath($currentPost['image']) ?>" class="hero-img-full" alt="Hero">

            <div class="article-content" id="articleBody">
                <?= $currentPost['description'] ?>
            </div>

            <div class="share-bar">
                <button class="btn-fyu" style="background: white; color: var(--text-dark); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm);" onclick="window.history.back()">
                    <i class="fa-solid fa-arrow-left"></i> Back to Hub
                </button>
                
                <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                    <span style="font-weight: 800; font-size: 0.75rem; color: var(--text-light); letter-spacing: 0.15em;">SHARE STORY:</span>
                    <div class="share-buttons">
                        <a href="https://twitter.com/intent/tweet?url=<?= $encodedUrl ?>&text=<?= $encodedTitle ?>" target="_blank" class="share-btn" title="Share on X">
                            <i class="fa-brands fa-x-twitter"></i>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $encodedUrl ?>" target="_blank" class="share-btn" title="Share on Facebook">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $encodedUrl ?>&title=<?= $encodedTitle ?>" target="_blank" class="share-btn" title="Share on LinkedIn">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                        <a href="https://api.whatsapp.com/send?text=<?= $encodedTitle ?>%20<?= $encodedUrl ?>" target="_blank" class="share-btn" title="Share on WhatsApp">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                        
                        <button class="share-btn" id="nativeShareBtn" style="display:none;" title="Share via Device">
                            <i class="fa-solid fa-share-nodes"></i>
                        </button>
                        
                        <button class="share-btn copy-btn" onclick="copyUrl()" title="Copy URL">
                            <i class="fa-regular fa-copy" style="margin-right: 6px;"></i> Link
                        </button>
                    </div>
                </div>
            </div>

            <section style="margin-top: 5rem;">
                <h3 style="font-family: var(--font-heading); font-size: 2.25rem; color: var(--text-dark); margin-bottom: 2.5rem; border-bottom: 3px solid var(--fyu-green); padding-bottom: 1rem; display: inline-block;">
                    Conversation (<?= count($comments) ?>)
                </h3>
                
                <div id="commentList" style="margin-bottom: 4rem;">
                    <?php if(empty($comments)): ?>
                        <p style="color: var(--text-light); font-style: italic; font-size: 1.1rem;" id="noCommentsMsg">Be the first to share your thoughts.</p>
                    <?php endif; ?>
                    
                    <?php foreach($comments as $cmt): ?>
                    <div style="padding: 2rem; background: var(--bg-soft); border-radius: 12px; margin-bottom: 1.5rem; box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <strong style="font-size: 1.15rem; color: var(--text-dark);"><?= htmlspecialchars($cmt['user_name']) ?></strong>
                            <span style="font-size: 0.85rem; color: var(--text-light); font-weight: 500;"><i class="fa-regular fa-calendar"></i> <?= date('M d, Y', strtotime($cmt['created_at'])) ?></span>
                        </div>
                        <div style="color: var(--text-body); line-height: 1.8; font-size: 1.05rem;"><?= nl2br(htmlspecialchars($cmt['comment_body'])) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="sidebar-widget" style="padding: 3rem;">
                    <h4 style="margin-top: 0; font-family: var(--font-heading); font-size: 1.75rem; color: var(--text-dark); margin-bottom: 2rem;">Leave a thought</h4>
                    <form id="commentForm" onsubmit="submitComment(event)">
                        <input type="hidden" name="post_id" value="<?= $currentPost['id'] ?>">
                        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                            <input type="text" name="user_name" class="form-input" placeholder="Your Name" required>
                            <input type="email" name="user_email" class="form-input" placeholder="Email (Kept Private)">
                        </div>
                        <textarea name="comment_body" class="form-input" rows="6" placeholder="Join the discussion..." style="margin-bottom:1.5rem; resize: vertical;" required></textarea>
                        <button type="submit" class="btn-fyu" id="commentBtn" style="width: auto; padding: 1rem 2rem;">
                            Post Comment <i class="fa-solid fa-paper-plane" style="margin-left: 5px;"></i>
                        </button>
                    </form>
                </div>
            </section>
        </article>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>

<script>
    // --- UI/UX Enhancements --- //

    // 1. Toast Notification System
    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        
        const icon = type === 'success' ? '<i class="fa-solid fa-circle-check" style="color:var(--fyu-green); font-size: 1.2rem;"></i>' : '<i class="fa-solid fa-circle-exclamation" style="color:#ef4444; font-size: 1.2rem;"></i>';
        toast.innerHTML = `${icon} <span>${message}</span>`;
        
        container.appendChild(toast);
        
        // Trigger reflow to ensure animation runs
        toast.offsetHeight; 
        toast.classList.add('show');
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400);
        }, 4000);
    }

    // 2. Smooth Reading Progress Bar
    if (document.getElementById('progressBar')) {
        const progressBar = document.getElementById('progressBar');
        window.addEventListener('scroll', () => {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            progressBar.style.width = scrolled + "%";
        });
    }

    // 3. Share Functionality
    function copyUrl() {
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            showToast('Link copied to clipboard!');
        }).catch(err => {
            console.error('Could not copy text: ', err);
            showToast('Failed to copy link', 'error');
        });
    }

    // Enable Native Share if supported (Mobile devices usually)
    const nativeBtn = document.getElementById('nativeShareBtn');
    if (nativeBtn && navigator.share) {
        nativeBtn.style.display = 'inline-flex';
        nativeBtn.addEventListener('click', async () => {
            try {
                await navigator.share({
                    title: document.title,
                    url: window.location.href
                });
            } catch (err) {
                console.log('User cancelled share or error:', err);
            }
        });
    }

    // --- Form Submissions --- //

    function submitNewsletter(e) {
        e.preventDefault();
        const data = new FormData(e.target);
        data.append('action', 'subscribe');
        
        const btn = e.target.querySelector('button');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        btn.disabled = true;

        fetch('blog.php', { method: 'POST', body: data })
        .then(r => r.json()).then(d => {
            showToast(d.message, d.status);
            if(d.status === 'success') e.target.reset();
        }).catch(() => {
            showToast('Network error occurred.', 'error');
        }).finally(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }

    function submitComment(e) {
        e.preventDefault();
        const btn = document.getElementById('commentBtn');
        const list = document.getElementById('commentList');
        const noMsg = document.getElementById('noCommentsMsg');
        
        const data = new FormData(e.target);
        data.append('action', 'add_comment');
        
        const originalHtml = btn.innerHTML;
        btn.disabled = true; 
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Posting...';

        fetch('blog.php', { method: 'POST', body: data })
        .then(r => r.json()).then(d => {
            if(d.status === 'success') {
                showToast(d.message, 'success');
                if(noMsg) noMsg.remove();
                
                const html = `<div style="padding: 2rem; background: var(--bg-soft); border-radius: 12px; margin-bottom: 1.5rem; box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); opacity:0; transform:translateY(20px); transition: all 0.5s ease;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <strong style="font-size: 1.15rem; color: var(--text-dark);">${d.comment.user_name}</strong>
                        <span style="font-size: 0.85rem; color: var(--text-light); font-weight: 500;"><i class="fa-regular fa-clock"></i> ${d.comment.created_at}</span>
                    </div>
                    <div style="color: var(--text-body); line-height: 1.8; font-size: 1.05rem;">${d.comment.comment_body}</div>
                </div>`;
                
                list.insertAdjacentHTML('beforeend', html);
                setTimeout(() => { 
                    list.lastElementChild.style.opacity = "1"; 
                    list.lastElementChild.style.transform = "translateY(0)"; 
                }, 50);
                e.target.reset();
            } else {
                showToast(d.message, 'error');
            }
        }).catch(() => {
            showToast('Network error occurred.', 'error');
        }).finally(() => {
            btn.disabled = false; 
            btn.innerHTML = originalHtml;
        });
    }
</script>