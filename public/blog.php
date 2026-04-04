<?php

// blog.php – Fixed Image Paths & Modern UI
declare(strict_types=1);

// 1. CONFIGURATION & DATABASE
require_once __DIR__ . "/../app/config/db.php";

// ===================================================================
// IMAGE HELPER FUNCTION
// ===================================================================
function getImagePath($imageName): string {
    $folder = 'uploads/news/';

    if (!empty($imageName)) {
        return $folder . htmlspecialchars((string)$imageName);
    }

    return 'uploads/news/default.jpg';
}

// ===================================================================
// 2. API HANDLER (AJAX REQUESTS)
// ===================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
    header("Content-Type: application/json");

    if (!$pdo) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database is currently unavailable.'
        ]);
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
                    $response = [
                        'status' => 'success',
                        'message' => 'Welcome to our community!'
                    ];

                    try {
                        $pdo->prepare("INSERT INTO admin_notifications (type, message, link) VALUES ('subscription', ?, '/admin/subscribers')")
                            ->execute(["New subscriber: $email"]);
                    } catch (Throwable $e) {
                        error_log("Blog subscription notification error: " . $e->getMessage());
                    }
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

                $sql = "INSERT INTO blog_comments (post_id, user_name, user_email, comment_body, created_at)
                        VALUES (:pid, :name, :email, :body, NOW())";
                $stmt = $pdo->prepare($sql);

                if ($stmt->execute([
                    'pid'   => $postId,
                    'name'  => $name,
                    'email' => $email,
                    'body'  => $body
                ])) {
                    try {
                        $notifMsg = "New comment from $name on Post #$postId";
                        $notifLink = "/admin/comments.php?post_id=$postId";

                        $pdo->prepare("INSERT INTO admin_notifications (type, message, link, created_at)
                                       VALUES ('comment', :msg, :link, NOW())")
                            ->execute([
                                'msg'  => $notifMsg,
                                'link' => $notifLink
                            ]);
                    } catch (Throwable $e) {
                        error_log("Blog comment notification error: " . $e->getMessage());
                    }

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
$listPosts = [];
$comments = [];
$pageTitle = "Blog & Stories - Fangak Youth Union";

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
                $pageTitle = htmlspecialchars($currentPost['title']) . " - FYU Blog";
            } else {
                header("Location: blog.php");
                exit;
            }
        } else {
            $stmt = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 10");
            $allPosts = $stmt->fetchAll();

            if (!empty($allPosts)) {
                $featuredPost = $allPosts[0];
                $listPosts = array_slice($allPosts, 1);
            }
        }
    } catch (Throwable $e) {
        error_log("Blog query error: " . $e->getMessage());
        $currentPost = null;
        $featuredPost = null;
        $listPosts = [];
        $comments = [];
    }
} else {
    error_log("Blog page: PDO connection unavailable.");
}

include_once __DIR__ . "/../app/views/layouts/header.php";
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
    /* --- CSS (Same Modern Design) --- */
    :root {
        --color-primary: #0f5132;
        --color-accent: #d97706;
        --color-text: #1f2937;
        --color-muted: #6b7280;
        --color-bg: #f9fafb;
        --color-card: #ffffff;
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
        --radius-lg: 1rem;
    }
    body { background: var(--color-bg); color: var(--color-text); font-family: 'Inter', sans-serif; line-height: 1.6; }
    h1, h2, h3 { font-family: 'Playfair Display', serif; color: #111827; }
    a { text-decoration: none; color: inherit; }
    img { max-width: 100%; height: auto; display: block; }
    .container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }
    
    .badge { padding: 0.25rem 0.75rem; background: #ecfdf5; color: var(--color-primary); border-radius: 999px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
    .btn { display: inline-flex; align-items: center; gap: 0.5rem; background: var(--color-primary); color: white; padding: 0.75rem 1.5rem; border-radius: 999px; font-weight: 500; border: none; cursor: pointer; transition: all 0.2s; }
    .btn:hover { background: #064e3b; }

    /* Layouts */
    .blog-header { text-align: center; padding: 4rem 0 3rem; }
    .blog-header h1 { font-size: 3rem; margin-bottom: 1rem; }
    
    /* Featured Card */
    .featured-card { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 2rem; background: var(--color-card); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-md); margin-bottom: 4rem; }
    .featured-img-wrap { height: 100%; min-height: 400px; }
    .featured-img { width: 100%; height: 100%; object-fit: cover; }
    .featured-content { padding: 3rem; display: flex; flex-direction: column; justify-content: center; }

    /* Grid */
    .content-grid { display: grid; grid-template-columns: 2.5fr 1fr; gap: 3rem; margin-bottom: 4rem; }
    .posts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem; }
    
    .post-card { background: var(--color-card); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-md); display: flex; flex-direction: column; height: 100%; transition: transform 0.3s; }
    .post-card:hover { transform: translateY(-5px); }
    .card-img { height: 220px; object-fit: cover; }
    .card-body { padding: 1.5rem; flex: 1; display: flex; flex-direction: column; }
    .card-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.75rem; }
    .read-more { color: var(--color-primary); font-weight: 600; margin-top: auto; }

    /* Sidebar */
    .sidebar-widget { background: var(--color-card); padding: 1.5rem; border-radius: var(--radius-lg); margin-bottom: 2rem; border: 1px solid #e5e7eb; }
    .newsletter-input { width: 100%; padding: 0.75rem; margin-bottom: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; }

    /* Single & Comments */
    .article-img { width: 100%; max-height: 500px; object-fit: cover; border-radius: var(--radius-lg); margin-bottom: 3rem; }
    .comments-section { max-width: 800px; margin: 4rem auto 0; padding-top: 3rem; border-top: 1px solid #e5e7eb; }
    .comment-item { padding: 1.5rem; background: white; border-radius: 0.5rem; border: 1px solid #f3f4f6; margin-bottom: 1rem; }
    .form-input, .form-textarea { width: 100%; padding: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.5rem; margin-bottom: 1rem; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

    @media (max-width: 768px) {
        .featured-card, .content-grid, .form-row { grid-template-columns: 1fr; }
        .featured-img-wrap { min-height: 250px; }
    }
</style>

<div class="container">

    <?php if ($viewMode === 'index'): ?>
        
        <header class="blog-header">
            <span class="badge">Community Updates</span>
            <h1>Fangak Stories</h1>
            <p>News, events, and stories from the heart of our union.</p>
        </header>

        <?php if ($featuredPost): ?>
        <a href="?post=<?= $featuredPost['id'] ?>" class="featured-card">
            <div class="featured-img-wrap">
                <img src="<?= getImagePath($featuredPost['image']) ?>" 
                     alt="<?= htmlspecialchars($featuredPost['title']) ?>" class="featured-img">
            </div>
            <div class="featured-content">
                <div style="color:var(--color-muted); margin-bottom:0.5rem;">
                    <?= date('F d, Y', strtotime($featuredPost['created_at'])) ?>
                </div>
                <h2 style="font-size: 2rem; margin-bottom: 1rem;"><?= htmlspecialchars($featuredPost['title']) ?></h2>
                <p style="color: var(--color-muted); font-size: 1.1rem; margin-bottom: 2rem;">
                    <?= htmlspecialchars($featuredPost['subheading']) ?>
                </p>
                <span class="btn" style="width: fit-content;">Read Full Story</span>
            </div>
        </a>
        <?php endif; ?>

        <div class="content-grid">
            <main>
                <div class="posts-grid">
                    <?php if (empty($listPosts) && !$featuredPost): ?>
                        <p>No stories found.</p>
                    <?php else: ?>
                        <?php foreach ($listPosts as $post): ?>
                        <article class="post-card">
                            <a href="?post=<?= $post['id'] ?>">
                                <img src="<?= getImagePath($post['image']) ?>" 
                                     alt="<?= htmlspecialchars($post['title']) ?>" class="card-img" loading="lazy">
                            </a>
                            <div class="card-body">
                                <div style="color:var(--color-muted); font-size:0.875rem; margin-bottom:0.5rem;">
                                    <?= date('M d, Y', strtotime($post['created_at'])) ?>
                                </div>
                                <h3 class="card-title">
                                    <a href="?post=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a>
                                </h3>
                                <p style="color:#666; font-size:0.95rem; margin-bottom:1.5rem; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;">
                                    <?= htmlspecialchars(strip_tags($post['description'])) ?>
                                </p>
                                <a href="?post=<?= $post['id'] ?>" class="read-more">Read Article &rarr;</a>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </main>

            <aside>
                <div class="sidebar-widget">
                    <h3 style="font-size:1.1rem; font-weight:700; color:var(--color-primary); margin-bottom:1rem;">Newsletter</h3>
                    <form id="newsletterForm" onsubmit="submitNewsletter(event)">
                        <input type="email" name="email" class="newsletter-input" placeholder="Your email address" required>
                        <button type="submit" class="btn" style="width: 100%; justify-content: center;">Subscribe</button>
                        <div id="newsletterMsg" style="margin-top: 0.5rem; font-size: 0.85rem;"></div>
                    </form>
                </div>
            </aside>
        </div>

    <?php elseif ($viewMode === 'single' && $currentPost): ?>
        
        <article style="padding: 4rem 0;">
            <header style="text-align:center; margin-bottom:3rem;">
                <span class="badge" style="margin-bottom: 1rem;"><?= htmlspecialchars($currentPost['category'] ?? 'Update') ?></span>
                <h1 style="font-size: clamp(2.5rem, 5vw, 3.5rem);"><?= htmlspecialchars($currentPost['title']) ?></h1>
                <div style="color:var(--color-muted); margin-top:1rem;">
                    <?= date('F d, Y', strtotime($currentPost['created_at'])) ?> • By <?= htmlspecialchars($currentPost['author'] ?? 'Admin') ?>
                </div>
            </header>

            <img src="<?= getImagePath($currentPost['image']) ?>" 
                 alt="<?= htmlspecialchars($currentPost['title']) ?>" class="article-img">

            <div style="max-width: 800px; margin: 0 auto; font-size: 1.125rem; line-height: 1.8; color: #374151;">
                <?= $currentPost['description'] ?> </div>

            <div class="comments-section">
                <h3 style="margin-bottom: 2rem;">Discussion (<?= count($comments) ?>)</h3>
                <ul id="commentList" style="list-style:none; padding:0; margin-bottom:3rem;">
                    <?php if ($comments): foreach($comments as $cmt): ?>
                        <li class="comment-item">
                            <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
                                <span style="font-weight:700; color:var(--color-primary);"><?= htmlspecialchars($cmt['user_name']) ?></span>
                                <span style="font-size:0.85rem; color:var(--color-muted);"><?= date('M d, Y', strtotime($cmt['created_at'])) ?></span>
                            </div>
                            <div style="color: #4b5563;"><?= nl2br(htmlspecialchars($cmt['comment_body'])) ?></div>
                        </li>
                    <?php endforeach; else: ?>
                        <li style="text-align: center; color: #999;">No comments yet.</li>
                    <?php endif; ?>
                </ul>

                <div class="sidebar-widget" style="background: #f8fafc;">
                    <h4 style="margin-bottom: 1rem;">Leave a Reply</h4>
                    <form id="commentForm" onsubmit="submitComment(event)">
                        <input type="hidden" name="post_id" value="<?= $currentPost['id'] ?>">
                        <div class="form-row">
                            <input type="text" name="user_name" class="form-input" placeholder="Name" required>
                            <input type="email" name="user_email" class="form-input" placeholder="Email" required>
                        </div>
                        <textarea name="comment_body" class="form-textarea" rows="4" placeholder="Comment" required></textarea>
                        <button type="submit" class="btn" id="commentBtn">Post Comment</button>
                        <div id="commentMsg" style="margin-top: 10px; font-weight: 600;"></div>
                    </form>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 3rem;">
                <a href="blog.php" style="color: var(--color-muted); font-weight: 500;">&larr; Back to all stories</a>
            </div>
        </article>

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
            msg.style.color = d.status === 'success' ? 'green' : 'red';
            btn.disabled = false; btn.textContent = 'Subscribe';
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

        btn.disabled = true; btn.textContent = 'Posting...';

        fetch('blog.php', { method: 'POST', body: data })
        .then(r => r.json())
        .then(d => {
            msg.textContent = d.message;
            msg.style.color = d.status === 'success' ? '#0f5132' : 'red';
            btn.disabled = false; btn.textContent = 'Post Comment';
            if(d.status === 'success') {
                const li = document.createElement('li');
                li.className = 'comment-item';
                li.style.borderLeft = '4px solid #0f5132';
                li.innerHTML = `<strong>${d.comment.user_name}</strong> <span style='font-size:0.8rem; float:right;'>Just Now</span><br>${d.comment.comment_body}`;
                list.prepend(li);
                form.reset();
            }
        });
    }
</script>