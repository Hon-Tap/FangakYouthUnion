<?php

// blog.php – Advanced Editorial News Layout
declare(strict_types=1);

// 1. CONFIGURATION & DATABASE
require_once __DIR__ . "/../app/config/db.php";

// ===================================================================
// IMAGE HELPER FUNCTION
// ===================================================================
function getImagePath($imageName): string {
    // Adding a leading slash / makes it look from the root of the domain
    $folder = '/uploads/news/'; 
    
    if (!empty($imageName)) {
        return $folder . htmlspecialchars((string)$imageName);
    }
    return '/uploads/news/FYO-LOGO.jpg';
}

// ===================================================================
// 2. API HANDLER (AJAX REQUESTS)
// ===================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
    header("Content-Type: application/json");

    if (!$pdo) {
        echo json_encode(['status' => 'error', 'message' => 'Database is currently unavailable.']);
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
                    try {
                        $pdo->prepare("INSERT INTO admin_notifications (type, message, link) VALUES ('subscription', ?, '/admin/subscribers')")
                            ->execute(["New subscriber: $email"]);
                    } catch (Throwable $e) { error_log("Blog sub err: " . $e->getMessage()); }
                } else {
                    $response['message'] = "Unable to save your subscription right now.";
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
                $stmt = $pdo->prepare($sql);

                if ($stmt->execute(['pid' => $postId, 'name' => $name, 'email' => $email, 'body' => $body])) {
                    try {
                        $pdo->prepare("INSERT INTO admin_notifications (type, message, link, created_at) VALUES ('comment', :msg, :link, NOW())")
                            ->execute(['msg' => "New comment from $name on Post #$postId", 'link' => "/admin/comments.php?post_id=$postId"]);
                    } catch (Throwable $e) { error_log("Blog comment err: " . $e->getMessage()); }

                    $response = [
                        'status' => 'success',
                        'message' => 'Comment posted successfully!',
                        'comment' => [
                            'user_name'    => htmlspecialchars($name),
                            'comment_body' => nl2br(htmlspecialchars($body)),
                            'created_at'   => 'Just Now'
                        ]
                    ];
                } else {
                    $response['message'] = "Database error. Please try again.";
                }
                break;
        }
    } catch (Throwable $e) {
        error_log("Blog AJAX error: " . $e->getMessage());
        $response['message'] = "Server error. Please try again later.";
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
$secondaryPosts = [];
$listPosts = [];
$relatedPosts = [];
$comments = [];
$pageTitle = "News Hub - Fangak Youth Union";

if ($pdo) {
    try {
        if ($viewMode === 'single') {
            $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
            $stmt->execute([$postId]);
            $currentPost = $stmt->fetch();

            if ($currentPost) {
                // Fetch Comments
                $cStmt = $pdo->prepare("SELECT * FROM blog_comments WHERE post_id = ? ORDER BY created_at DESC");
                $cStmt->execute([$postId]);
                $comments = $cStmt->fetchAll();
                
                // Fetch Related Contents (excluding current post)
                $rStmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id != ? ORDER BY created_at DESC LIMIT 3");
                $rStmt->execute([$postId]);
                $relatedPosts = $rStmt->fetchAll();

                $pageTitle = htmlspecialchars($currentPost['title']) . " - FYU News";
            } else {
                header("Location: blog.php");
                exit;
            }
        } else {
            $stmt = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 15");
            $allPosts = $stmt->fetchAll();

            if (!empty($allPosts)) {
                $featuredPost = $allPosts[0];
                $secondaryPosts = array_slice($allPosts, 1, 2); // Next 2 for the top grid
                $listPosts = array_slice($allPosts, 3); // The rest
            }
        }
    } catch (Throwable $e) {
        error_log("Blog query error: " . $e->getMessage());
    }
}

include_once __DIR__ . "/../app/views/layouts/header.php";
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
    /* --- ADVANCED EDITORIAL CSS --- */
    :root {
        --color-brand: #0f5132;
        --color-brand-light: #ecfdf5;
        --color-text-main: #111827;
        --color-text-muted: #4b5563;
        --color-border: #e5e7eb;
        --color-bg: #ffffff;
        --color-bg-alt: #f9fafb;
        --font-headline: 'Merriweather', serif;
        --font-body: 'Roboto', sans-serif;
    }
    
    body { background: var(--color-bg); color: var(--color-text-main); font-family: var(--font-body); line-height: 1.6; margin: 0; }
    h1, h2, h3, h4, h5, h6 { font-family: var(--font-headline); margin: 0; color: var(--color-text-main); }
    a { text-decoration: none; color: inherit; transition: color 0.2s; }
    a:hover { color: var(--color-brand); }
    img { max-width: 100%; height: auto; display: block; object-fit: cover; }
    
    .news-container { max-width: 1280px; margin: 0 auto; padding: 0 1.5rem; }

    /* Breaking News Banner */
    .breaking-banner { background: var(--color-brand); color: white; padding: 0.75rem 1.5rem; display: flex; align-items: center; gap: 1rem; font-weight: 500; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2rem; }
    .breaking-banner .pulse { width: 8px; height: 8px; background: #fbbf24; border-radius: 50%; box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.7); animation: pulse 2s infinite; }
    @keyframes pulse { 0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.7); } 70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(251, 191, 36, 0); } 100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(251, 191, 36, 0); } }

    /* Common UI Elements */
    .category-tag { color: var(--color-brand); font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; display: block; }
    .news-date { color: var(--color-text-muted); font-size: 0.85rem; display: block; margin-top: 0.5rem; }
    
    .img-hover-wrap { overflow: hidden; position: relative; }
    .img-hover-wrap img { transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94); width: 100%; }
    .news-card:hover .img-hover-wrap img { transform: scale(1.05); }

    /* --- INDEX LAYOUT: BENTO GRID --- */
    .top-stories-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-bottom: 3rem; padding-bottom: 3rem; border-bottom: 2px solid var(--color-border); }
    .lead-story .headline { font-size: 2.5rem; line-height: 1.2; margin: 1rem 0; font-weight: 900; }
    .lead-story p { font-size: 1.15rem; color: var(--color-text-muted); margin-bottom: 1rem; }
    .lead-story .img-hover-wrap { height: 450px; margin-bottom: 1.5rem; }

    .secondary-stories { display: flex; flex-direction: column; gap: 2rem; }
    .secondary-story { display: grid; grid-template-columns: 1fr; gap: 1rem; }
    .secondary-story .img-hover-wrap { height: 200px; }
    .secondary-story .headline { font-size: 1.25rem; line-height: 1.3; }

    /* Lower Section Grid */
    .main-content-layout { display: grid; grid-template-columns: 3fr 1fr; gap: 3rem; }
    
    .news-list { display: flex; flex-direction: column; gap: 2rem; }
    .news-list-item { display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; padding-bottom: 2rem; border-bottom: 1px solid var(--color-border); align-items: center; }
    .news-list-item .img-hover-wrap { height: 180px; }
    .news-list-item .headline { font-size: 1.5rem; margin-bottom: 0.5rem; }
    
    /* Sidebar Widgets */
    .widget-title { font-size: 1.25rem; border-left: 4px solid var(--color-brand); padding-left: 0.75rem; margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 1px; }
    .sidebar-box { background: var(--color-bg-alt); padding: 1.5rem; margin-bottom: 2rem; border-top: 4px solid var(--color-text-main); }
    .newsletter-input { width: 100%; padding: 0.75rem; margin-bottom: 0.75rem; border: 1px solid var(--color-border); box-sizing: border-box; font-family: var(--font-body); }
    .btn-editorial { display: block; width: 100%; background: var(--color-text-main); color: white; text-align: center; padding: 0.75rem; font-weight: 700; border: none; cursor: pointer; transition: background 0.2s; text-transform: uppercase; letter-spacing: 0.5px; }
    .btn-editorial:hover { background: var(--color-brand); }

    /* --- SINGLE POST LAYOUT --- */
    .article-header { text-align: center; max-width: 900px; margin: 3rem auto 2rem; }
    .article-headline { font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 900; line-height: 1.1; margin-bottom: 1.5rem; }
    .article-meta { display: flex; justify-content: center; gap: 1rem; align-items: center; font-size: 0.9rem; color: var(--color-text-muted); border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border); padding: 1rem 0; margin-bottom: 3rem; }
    
    .article-hero { width: 100%; max-height: 600px; object-fit: cover; margin-bottom: 3rem; }
    
    .article-body { max-width: 760px; margin: 0 auto; font-size: 1.15rem; line-height: 1.8; color: #374151; font-family: var(--font-headline); }
    .article-body p { margin-bottom: 1.5rem; }
    .article-body p:first-of-type::first-letter { font-size: 4rem; float: left; line-height: 0.8; padding-right: 0.5rem; color: var(--color-text-main); font-weight: 900; }

    /* Related Content Grid */
    .related-section { margin-top: 4rem; padding-top: 3rem; border-top: 2px solid var(--color-text-main); background: var(--color-bg-alt); padding: 4rem 1.5rem; }
    .related-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; max-width: 1280px; margin: 0 auto; }
    .related-card .img-hover-wrap { height: 200px; margin-bottom: 1rem; }
    .related-card .headline { font-size: 1.2rem; }

    /* Comments Section */
    .comments-wrapper { max-width: 760px; margin: 4rem auto; }
    .comment-block { padding: 1.5rem 0; border-bottom: 1px solid var(--color-border); }
    .comment-author { font-weight: 700; font-family: var(--font-body); }
    .comment-text { font-family: var(--font-body); margin-top: 0.5rem; }
    
    .comment-form { background: var(--color-bg-alt); padding: 2rem; margin-top: 3rem; border-top: 4px solid var(--color-brand); }
    .form-group { margin-bottom: 1rem; }
    .form-control { width: 100%; padding: 0.75rem; border: 1px solid var(--color-border); font-family: var(--font-body); box-sizing: border-box; }

    /* Responsive */
    @media (max-width: 1024px) {
        .top-stories-grid, .main-content-layout { grid-template-columns: 1fr; }
        .secondary-stories { flex-direction: row; }
    }
    @media (max-width: 768px) {
        .secondary-stories { flex-direction: column; }
        .news-list-item { grid-template-columns: 1fr; }
        .news-list-item .img-hover-wrap { height: 250px; }
        .lead-story .headline { font-size: 2rem; }
    }
</style>

<div class="breaking-banner">
    <div class="pulse"></div>
    <span>Latest Updates from South Sudan & The Union</span>
</div>

<div class="news-container">

    <?php if ($viewMode === 'index'): ?>
        
        <?php if ($featuredPost): ?>
        <section class="top-stories-grid">
            <article class="news-card lead-story">
                <a href="?post=<?= $featuredPost['id'] ?>">
                    <div class="img-hover-wrap">
                        <img src="<?= getImagePath($featuredPost['image']) ?>" alt="<?= htmlspecialchars($featuredPost['title']) ?>">
                    </div>
                    <span class="category-tag"><?= htmlspecialchars($featuredPost['category'] ?? 'Headlines') ?></span>
                    <h2 class="headline"><?= htmlspecialchars($featuredPost['title']) ?></h2>
                    <p><?= htmlspecialchars($featuredPost['subheading'] ?? strip_tags(substr($featuredPost['description'], 0, 150))) ?>...</p>
                    <span class="news-date"><?= date('F d, Y', strtotime($featuredPost['created_at'])) ?></span>
                </a>
            </article>

            <div class="secondary-stories">
                <?php foreach ($secondaryPosts as $post): ?>
                <article class="news-card secondary-story">
                    <a href="?post=<?= $post['id'] ?>">
                        <div class="img-hover-wrap">
                            <img src="<?= getImagePath($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                        </div>
                        <div style="padding-top: 1rem;">
                            <span class="category-tag"><?= htmlspecialchars($post['category'] ?? 'Regional') ?></span>
                            <h3 class="headline"><?= htmlspecialchars($post['title']) ?></h3>
                            <span class="news-date"><?= date('M d, Y', strtotime($post['created_at'])) ?></span>
                        </div>
                    </a>
                </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <section class="main-content-layout">
            <div class="news-list">
                <h3 class="widget-title">More Stories</h3>
                <?php if (empty($listPosts) && !$featuredPost): ?>
                    <p>No stories found.</p>
                <?php else: ?>
                    <?php foreach ($listPosts as $post): ?>
                    <article class="news-card news-list-item">
                        <a href="?post=<?= $post['id'] ?>">
                            <div class="img-hover-wrap">
                                <img src="<?= getImagePath($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy">
                            </div>
                        </a>
                        <div>
                            <span class="category-tag"><?= htmlspecialchars($post['category'] ?? 'Community') ?></span>
                            <a href="?post=<?= $post['id'] ?>">
                                <h3 class="headline"><?= htmlspecialchars($post['title']) ?></h3>
                            </a>
                            <p style="color: var(--color-text-muted); font-size: 1rem; margin-top: 0.5rem;">
                                <?= htmlspecialchars(strip_tags(substr($post['description'], 0, 120))) ?>...
                            </p>
                            <span class="news-date"><?= date('M d, Y', strtotime($post['created_at'])) ?></span>
                        </div>
                    </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <aside class="editorial-sidebar">
                <div class="sidebar-box">
                    <h3 class="widget-title" style="border-left-color: #d97706;">Regional Focus</h3>
                    <p style="font-size: 0.95rem; color: var(--color-text-muted); margin-bottom: 1rem;">
                        Deep dives into the socio-economic and cultural developments shaping South Sudan today.
                    </p>
                    <ul style="list-style: none; padding: 0; margin: 0; font-weight: 500; display:flex; flex-direction: column; gap: 0.75rem;">
                        <li><a href="#">&rarr; Juba Tech Initiatives</a></li>
                        <li><a href="#">&rarr; Fangak Community Reports</a></li>
                        <li><a href="#">&rarr; Digital Transformation in SS</a></li>
                        <li><a href="#">&rarr; National Health Reforms</a></li>
                    </ul>
                </div>

                <div class="sidebar-box">
                    <h3 class="widget-title">The FYU Briefing</h3>
                    <p style="font-size: 0.9rem; margin-bottom: 1rem; color: var(--color-text-muted);">Get essential news and community updates delivered straight to your inbox.</p>
                    <form id="newsletterForm" onsubmit="submitNewsletter(event)">
                        <input type="email" name="email" class="newsletter-input" placeholder="Email address" required>
                        <button type="submit" class="btn-editorial">Sign Up</button>
                        <div id="newsletterMsg" style="margin-top: 0.5rem; font-size: 0.85rem; font-weight: 500;"></div>
                    </form>
                </div>
            </aside>
        </section>

    <?php elseif ($viewMode === 'single' && $currentPost): ?>
        
        <article>
            <header class="article-header">
                <span class="category-tag" style="font-size: 1rem;"><?= htmlspecialchars($currentPost['category'] ?? 'Focus') ?></span>
                <h1 class="article-headline"><?= htmlspecialchars($currentPost['title']) ?></h1>
                
                <div class="article-meta">
                    <span><strong>By <?= htmlspecialchars($currentPost['author'] ?? 'FYU Editorial Board') ?></strong></span>
                    <span>•</span>
                    <span><?= date('F d, Y', strtotime($currentPost['created_at'])) ?></span>
                    <span>•</span>
                    <span><i class="fa-solid fa-share-nodes" style="cursor:pointer;" title="Share"></i></span>
                </div>
            </header>

            <img src="<?= getImagePath($currentPost['image']) ?>" alt="<?= htmlspecialchars($currentPost['title']) ?>" class="article-hero">

            <div class="article-body">
                <?= $currentPost['description'] // Assuming HTML content is safely stored ?>
            </div>
        </article>

        <?php if (!empty($relatedPosts)): ?>
        <section class="related-section">
            <h2 class="widget-title" style="text-align: center; border-left: none; margin-bottom: 3rem;">Read Next</h2>
            <div class="related-grid">
                <?php foreach ($relatedPosts as $rPost): ?>
                <article class="news-card related-card">
                    <a href="?post=<?= $rPost['id'] ?>">
                        <div class="img-hover-wrap">
                            <img src="<?= getImagePath($rPost['image']) ?>" alt="<?= htmlspecialchars($rPost['title']) ?>" loading="lazy">
                        </div>
                        <span class="category-tag"><?= htmlspecialchars($rPost['category'] ?? 'Related') ?></span>
                        <h3 class="headline"><?= htmlspecialchars($rPost['title']) ?></h3>
                    </a>
                </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <div class="comments-wrapper">
            <h3 class="widget-title" style="border-left-color: var(--color-text-main);">Join the Discussion (<?= count($comments) ?>)</h3>
            
            <div id="commentList">
                <?php if ($comments): foreach($comments as $cmt): ?>
                    <div class="comment-block">
                        <div style="display:flex; justify-content:space-between; align-items: baseline;">
                            <span class="comment-author"><?= htmlspecialchars($cmt['user_name']) ?></span>
                            <span class="news-date" style="margin:0;"><?= date('M d, Y', strtotime($cmt['created_at'])) ?></span>
                        </div>
                        <div class="comment-text"><?= nl2br(htmlspecialchars($cmt['comment_body'])) ?></div>
                    </div>
                <?php endforeach; else: ?>
                    <p style="color: var(--color-text-muted);">Be the first to share your perspective on this story.</p>
                <?php endif; ?>
            </div>

            <div class="comment-form">
                <h4 style="margin-bottom: 1.5rem; font-family: var(--font-body); text-transform: uppercase;">Post a Comment</h4>
                <form id="commentForm" onsubmit="submitComment(event)">
                    <input type="hidden" name="post_id" value="<?= $currentPost['id'] ?>">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <input type="text" name="user_name" class="form-control" placeholder="Full Name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="user_email" class="form-control" placeholder="Email Address" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea name="comment_body" class="form-control" rows="5" placeholder="Your comment..." required></textarea>
                    </div>
                    <button type="submit" class="btn-editorial" id="commentBtn" style="width: auto; padding: 0.75rem 2rem;">Submit Comment</button>
                    <div id="commentMsg" style="margin-top: 15px; font-weight: 500;"></div>
                </form>
            </div>
        </div>

    <?php endif; ?>

</div>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>

<script>
    // Handles Newsletter
    function submitNewsletter(e) {
        e.preventDefault();
        const form = e.target;
        const msg = document.getElementById('newsletterMsg');
        const btn = form.querySelector('button');
        const data = new FormData(form);
        data.append('action', 'subscribe');
        
        btn.disabled = true; btn.textContent = '...';
        
        fetch('blog.php', { method: 'POST', body: data })
        .then(r => r.json())
        .then(d => {
            msg.textContent = d.message;
            msg.style.color = d.status === 'success' ? '#0f5132' : '#dc2626';
            btn.disabled = false; btn.textContent = 'SIGN UP';
            if(d.status === 'success') form.reset();
        });
    }

    // Handles Comments
    function submitComment(e) {
        e.preventDefault();
        const form = e.target;
        const msg = document.getElementById('commentMsg');
        const btn = document.getElementById('commentBtn');
        const list = document.getElementById('commentList');
        const data = new FormData(form);
        data.append('action', 'add_comment');

        btn.disabled = true; btn.textContent = 'POSTING...';

        fetch('blog.php', { method: 'POST', body: data })
        .then(r => r.json())
        .then(d => {
            msg.textContent = d.message;
            msg.style.color = d.status === 'success' ? '#0f5132' : '#dc2626';
            btn.disabled = false; btn.textContent = 'SUBMIT COMMENT';
            if(d.status === 'success') {
                const div = document.createElement('div');
                div.className = 'comment-block';
                div.innerHTML = `
                    <div style="display:flex; justify-content:space-between; align-items: baseline;">
                        <span class="comment-author">${d.comment.user_name}</span>
                        <span class="news-date" style="margin:0;">Just Now</span>
                    </div>
                    <div class="comment-text">${d.comment.comment_body}</div>
                `;
                // Remove the "Be the first" text if it exists
                if(list.querySelector('p')) list.innerHTML = '';
                list.prepend(div);
                form.reset();
            }
        });
    }
</script>