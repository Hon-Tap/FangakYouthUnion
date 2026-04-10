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
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
    :root {
        --fyu-green: #064e3b;
        --fyu-gold: #d97706;
        --text-dark: #111827;
        --text-light: #6b7280;
        --bg-soft: #fbfbfb;
        --font-heading: 'Playfair Display', serif;
        --font-sans: 'Inter', sans-serif;
    }

    body { background-color: #fff; color: var(--text-dark); font-family: var(--font-sans); -webkit-font-smoothing: antialiased; }

    /* --- BEAUTIFIED COMPONENTS --- */
    .section-label { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.15em; color: var(--fyu-gold); display: block; margin-bottom: 0.5rem; }
    
    .img-container { position: relative; overflow: hidden; border-radius: 8px; background: #eee; }
    .img-container img { transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1); width: 100%; height: 100%; object-fit: cover; }
    .news-card:hover .img-container img { transform: scale(1.04); }

    /* --- INDEX: HERO BENTO GRID --- */
    .bento-container { max-width: 1300px; margin: 2rem auto; padding: 0 1.5rem; display: grid; grid-template-columns: 1.5fr 1fr; gap: 2rem; }
    
    .hero-main .img-container { height: 500px; margin-bottom: 1.5rem; border-radius: 12px; }
    .hero-main h1 { font-family: var(--font-heading); font-size: 3rem; line-height: 1.1; margin-bottom: 1rem; }
    .hero-main p { font-size: 1.1rem; color: var(--text-light); line-height: 1.6; max-width: 90%; }

    .hero-side { display: flex; flex-direction: column; gap: 1.5rem; }
    .side-item { display: grid; grid-template-columns: 120px 1fr; gap: 1rem; padding-bottom: 1.5rem; border-bottom: 1px solid #f0f0f0; }
    .side-item .img-container { height: 100px; border-radius: 6px; }
    .side-item h3 { font-size: 1.1rem; font-weight: 700; line-height: 1.3; }

    /* --- LATEST POSTS GRID (Replaces "More Stories") --- */
    .latest-grid-section { background: var(--bg-soft); padding: 5rem 0; margin-top: 4rem; }
    .grid-wrapper { max-width: 1300px; margin: 0 auto; padding: 0 1.5rem; }
    .fyu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2.5rem; }
    .grid-card .img-container { height: 220px; margin-bottom: 1.25rem; }
    .grid-card h3 { font-family: var(--font-heading); font-size: 1.4rem; margin-bottom: 0.75rem; }

    /* --- SINGLE POST --- */
    .article-container { max-width: 800px; margin: 4rem auto; padding: 0 1.5rem; }
    .article-header { text-align: center; margin-bottom: 3rem; }
    .article-header h1 { font-family: var(--font-heading); font-size: clamp(2.5rem, 5vw, 4rem); margin: 1rem 0; line-height: 1; }
    .article-meta { font-weight: 600; color: var(--text-light); text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; }
    
    .hero-img-full { width: 100%; max-height: 600px; border-radius: 16px; object-fit: cover; margin-bottom: 4rem; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
    .article-content { font-size: 1.25rem; line-height: 1.8; color: #333; }
    .article-content p { margin-bottom: 2rem; }
    .article-content p:first-of-type::first-letter { font-size: 5rem; float: left; line-height: 0.7; padding: 0.5rem 0.75rem 0 0; font-family: var(--font-heading); color: var(--fyu-green); }

    /* Sidebar Refinement */
    .sidebar-widget { background: white; border: 1px solid #eee; border-radius: 12px; padding: 2rem; margin-bottom: 2rem; }
    .btn-fyu { background: var(--fyu-green); color: white; border: none; padding: 1rem; width: 100%; border-radius: 6px; font-weight: 700; cursor: pointer; transition: 0.3s; }
    .btn-fyu:hover { background: var(--text-dark); transform: translateY(-2px); }

    @media (max-width: 1024px) {
        .bento-container { grid-template-columns: 1fr; }
        .hero-main h1 { font-size: 2.2rem; }
    }
</style>

<div class="main-wrapper">
    <?php if ($viewMode === 'index'): ?>
        <section class="bento-container">
            <?php if ($featuredPost): ?>
            <div class="hero-main news-card">
                <a href="?post=<?= $featuredPost['id'] ?>">
                    <div class="img-container">
                        <img src="<?= getImagePath($featuredPost['image']) ?>" alt="Featured">
                    </div>
                    <span class="section-label"><?= htmlspecialchars($featuredPost['category'] ?? 'Major Update') ?></span>
                    <h1><?= htmlspecialchars($featuredPost['title']) ?></h1>
                    <p><?= htmlspecialchars(strip_tags(substr($featuredPost['description'], 0, 180))) ?>...</p>
                </a>
            </div>
            <?php endif; ?>

            <div class="hero-side">
                <h2 class="section-label" style="color: var(--text-dark); border-bottom: 2px solid var(--fyu-green); padding-bottom: 5px; align-self: flex-start;">Trending Now</h2>
                <?php foreach (array_slice($latestGrid, 0, 4) as $post): ?>
                <a href="?post=<?= $post['id'] ?>" class="side-item news-card">
                    <div class="img-container">
                        <img src="<?= getImagePath($post['image']) ?>" alt="Thumbnail">
                    </div>
                    <div>
                        <span class="section-label" style="font-size: 0.65rem;"><?= date('M d', strtotime($post['created_at'])) ?></span>
                        <h3><?= htmlspecialchars($post['title']) ?></h3>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="latest-grid-section">
            <div class="grid-wrapper">
                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3rem;">
                    <div>
                        <span class="section-label">Latest from FYU</span>
                        <h2 style="font-family: var(--font-heading); font-size: 2.5rem;">Recent Dispatches</h2>
                    </div>
                    <div class="sidebar-widget" style="margin:0; padding: 1.5rem; width: 350px;">
                        <h4 style="margin-bottom:10px">The FYU Briefing</h4>
                        <form id="newsletterForm" onsubmit="submitNewsletter(event)" style="display:flex; gap:5px;">
                            <input type="email" name="email" placeholder="Email" style="flex:1; padding:10px; border:1px solid #ddd; border-radius:4px;" required>
                            <button type="submit" class="btn-fyu" style="width:auto; padding:10px 20px;">Join</button>
                        </form>
                        <div id="newsletterMsg" style="font-size: 0.8rem; margin-top:5px;"></div>
                    </div>
                </div>

                <div class="fyu-grid">
                    <?php foreach (array_slice($latestGrid, 4) as $post): ?>
                    <article class="grid-card news-card">
                        <a href="?post=<?= $post['id'] ?>">
                            <div class="img-container">
                                <img src="<?= getImagePath($post['image']) ?>" alt="Post Image">
                            </div>
                            <span class="section-label"><?= htmlspecialchars($post['category'] ?? 'Community') ?></span>
                            <h3><?= htmlspecialchars($post['title']) ?></h3>
                            <p style="color: var(--text-light); font-size: 0.95rem;">
                                <?= htmlspecialchars(strip_tags(substr($post['description'], 0, 100))) ?>...
                            </p>
                        </a>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

    <?php elseif ($viewMode === 'single' && $currentPost): ?>
        <article class="article-container">
            <header class="article-header">
                <span class="section-label"><?= htmlspecialchars($currentPost['category'] ?? 'Insight') ?></span>
                <h1><?= htmlspecialchars($currentPost['title']) ?></h1>
                <div class="article-meta">
                    By <?= htmlspecialchars($currentPost['author'] ?? 'FYU Media') ?> &bull; 
                    <?= date('F d, Y', strtotime($currentPost['created_at'])) ?>
                </div>
            </header>

            <img src="<?= getImagePath($currentPost['image']) ?>" class="hero-img-full" alt="Hero">

            <div class="article-content">
                <?= $currentPost['description'] ?>
            </div>

            <div style="margin-top: 5rem; padding-top: 2rem; border-top: 1px solid #eee; display: flex; justify-content: space-between;">
                <button class="btn-fyu" style="width: auto;" onclick="window.history.back()">Back to Hub</button>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <span style="font-weight: 800; font-size: 0.7rem; color: var(--text-light);">SHARE THIS STORY</span>
                    <a href="#" style="font-size: 1.2rem;"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#" style="font-size: 1.2rem;"><i class="fa-brands fa-facebook"></i></a>
                </div>
            </div>

            <section style="margin-top: 5rem;">
                <h3 style="font-family: var(--font-heading); font-size: 2rem; margin-bottom: 2rem;">Conversation (<?= count($comments) ?>)</h3>
                <div id="commentList">
                    <?php foreach($comments as $cmt): ?>
                    <div style="padding: 1.5rem 0; border-bottom: 1px solid #f0f0f0;">
                        <div style="font-weight: 800; margin-bottom: 5px;"><?= htmlspecialchars($cmt['user_name']) ?></div>
                        <div style="color: var(--text-light); line-height: 1.6;"><?= nl2br(htmlspecialchars($cmt['comment_body'])) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="sidebar-widget" style="background: var(--bg-soft); margin-top: 3rem;">
                    <h4 style="margin-bottom: 1.5rem;">Leave a thought</h4>
                    <form id="commentForm" onsubmit="submitComment(event)">
                        <input type="hidden" name="post_id" value="<?= $currentPost['id'] ?>">
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom:10px;">
                            <input type="text" name="user_name" placeholder="Your Name" style="padding:12px; border:1px solid #ddd; border-radius:4px;" required>
                            <input type="email" name="user_email" placeholder="Email (Private)" style="padding:12px; border:1px solid #ddd; border-radius:4px;">
                        </div>
                        <textarea name="comment_body" rows="4" placeholder="Join the discussion..." style="width:100%; padding:12px; border:1px solid #ddd; border-radius:4px; margin-bottom:10px;" required></textarea>
                        <button type="submit" class="btn-fyu" id="commentBtn">Post Comment</button>
                        <div id="commentMsg" style="margin-top:10px; font-weight:600;"></div>
                    </form>
                </div>
            </section>
        </article>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>

<script>
    function submitNewsletter(e) {
        e.preventDefault();
        const msg = document.getElementById('newsletterMsg');
        const data = new FormData(e.target);
        data.append('action', 'subscribe');
        fetch('blog.php', { method: 'POST', body: data })
        .then(r => r.json()).then(d => {
            msg.textContent = d.message;
            msg.style.color = d.status === 'success' ? '#064e3b' : '#dc2626';
            if(d.status === 'success') e.target.reset();
        });
    }

    function submitComment(e) {
        e.preventDefault();
        const btn = document.getElementById('commentBtn');
        const list = document.getElementById('commentList');
        const data = new FormData(e.target);
        data.append('action', 'add_comment');
        btn.disabled = true; btn.textContent = 'Posting...';

        fetch('blog.php', { method: 'POST', body: data })
        .then(r => r.json()).then(d => {
            if(d.status === 'success') {
                const html = `<div style="padding: 1.5rem 0; border-bottom: 1px solid #f0f0f0; opacity:0; transform:translateY(10px); transition: 0.5s all ease;">
                    <div style="font-weight: 800; margin-bottom: 5px;">${d.comment.user_name}</div>
                    <div style="color: var(--text-light); line-height: 1.6;">${d.comment.comment_body}</div>
                </div>`;
                list.insertAdjacentHTML('afterbegin', html);
                setTimeout(() => { list.firstChild.style.opacity = "1"; list.firstChild.style.transform = "translateY(0)"; }, 10);
                e.target.reset();
            }
            document.getElementById('commentMsg').textContent = d.message;
            btn.disabled = false; btn.textContent = 'Post Comment';
        });
    }
</script>