<?php
// Revamped modern contact page — contact.php
// NOTE: Replace placeholder reCAPTCHA keys as needed.

declare(strict_types=1);

$pageTitle = "Connect With Us - Fangak Youth Union";

include_once __DIR__ . "/../app/views/layouts/header.php";
include_once __DIR__ . "/../app/config/db.php";

$recaptchaSecret = 'YOUR_SECRET_KEY';
$recaptchaSiteKey = 'YOUR_SITE_KEY';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'contact_form') {
    if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
        http_response_code(403); exit;
    }

    header('Content-Type: application/json');

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $messageText = trim($_POST['message'] ?? '');
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

    if (!$name || !$email || !$messageText) {
        echo json_encode(['status'=>'error','message'=>'Please fill out all required fields.']); exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status'=>'error','message'=>'Enter a valid email address.']); exit;
    }
    if (empty($recaptchaResponse)) {
        echo json_encode(['status'=>'error','message'=>'reCAPTCHA verification required.']); exit;
    }

    $verify = @file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$recaptchaSecret."&response=".$recaptchaResponse);
    $captchaSuccess = json_decode($verify ?: '{"success": false}', true);

    if (!$captchaSuccess['success']) {
        echo json_encode(['status'=>'error','message'=>'CAPTCHA failed. Try again.']); exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message, created_at) VALUES (:n,:e,:m,NOW())");
        $stmt->execute([':n'=>$name, ':e'=>$email, ':m'=>$messageText]);
        echo json_encode(['status'=>'success','message'=>'Message delivered successfully.']);
    } catch (Exception $e) {
        echo json_encode(['status'=>'error','message'=>'Database error occurred.']);
    }
    exit;
}
?>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<style>
:root {
    --bg: #0d0f11;
    --card: rgba(255,255,255,0.06);
    --border: rgba(255,255,255,0.12);
    --primary: #10b981;
    --primary-dark: #0f8f68;
    --text-light: #e5e7eb;
    --text-dim: #9ca3af;
    --radius: 20px;
    --transition: .35s;
}
body { background: var(--bg); }

.hero {
    padding: 160px 20px 120px;
    text-align: center;
    background: radial-gradient(circle at 30% 20%, #064e3b, #000);
    color: white;
    clip-path: polygon(0 0,100% 0,100% 85%,0 100%);
}
.hero h1 {
    font-size: 3.6rem;
    font-weight: 900;
    letter-spacing: -1px;
}
.hero p { max-width: 650px; margin: 15px auto; opacity: .85; }

.wrapper {
    max-width: 1200px;
    margin: -70px auto 80px;
    padding: 20px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 35px;
}

.card {
    background: var(--card);
    border: 1px solid var(--border);
    backdrop-filter: blur(20px);
    padding: 40px;
    border-radius: var(--radius);
    transition: var(--transition);
}
.card:hover { transform: translateY(-8px); }

.contact-title { font-size: 1.8rem; font-weight: 700; color: var(--primary); margin-bottom: 20px; }

.info-block { margin-bottom: 25px; }
.info-block label { color: var(--text-dim); font-size: .9rem; }
.info-block p, .info-block a { color: var(--text-light); font-size: 1rem; }
.info-block a:hover { color: var(--primary); }

form .input-box {
    position: relative;
    margin-bottom: 22px;
}
.input-box input,
.input-box textarea {
    width: 100%;
    padding: 16px 18px 16px 48px;
    background: rgba(255,255,255,0.12);
    border: 1px solid transparent;
    border-radius: var(--radius);
    color: white;
    resize: none;
}
.input-box input:focus,
.input-box textarea:focus {
    border-color: var(--primary);
    outline: none;
    background: rgba(255,255,255,0.18);
}
.input-box i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--primary);
}

button.submit-btn {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border: none;
    border-radius: var(--radius);
    font-weight: 700;
    color: white;
    font-size: 1.1rem;
    cursor: pointer;
    transition: var(--transition);
}
button.submit-btn:hover {
    transform: translateY(-3px);
    background: linear-gradient(135deg, var(--primary-dark), var(--primary));
}

.toast {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: var(--primary);
    padding: 16px 22px;
    color:white;
    border-radius: var(--radius);
    opacity: 0;
    transform: translateY(20px);
    transition: var(--transition);
    z-index: 9999;
}
.toast.show { opacity:1; transform:translateY(0); }

@media(max-width: 900px){
    .wrapper { grid-template-columns: 1fr; }
}
</style>

<section class="hero">
    <p style="text-transform: uppercase; letter-spacing:3px; opacity:.7;">We are here for you</p>
    <h1>Connect With Fangak Youth Union</h1>
    <p>Your voice matters. Reach out for support, collaboration, or community engagement.</p>
</section>

<div class="wrapper">
    <div class="card">
        <h2 class="contact-title">Contact Information</h2>
        <div class="info-block">
            <label>Email</label>
            <p><a href="mailto:info@fangakyouth.org">info@fangakyouth.org</a></p>
        </div>
        <div class="info-block">
            <label>Phone</label>
            <p><a href="tel:+211924509160">+211 924 509 160</a></p>
        </div>
        <div class="info-block">
            <label>Location</label>
            <p>Juba, Central Equatoria, South Sudan</p>
        </div>
        <div class="info-block">
            <label>Social Links</label>
            <p>
                <a href="#">Facebook</a> · 
                <a href="#">Twitter</a> ·
                <a href="#">Instagram</a>
            </p>
        </div>
    </div>

    <div class="card">
        <h2 class="contact-title">Send a Message to FYU</h2>
        <form id="contactForm">
            <div class="input-box">
                <input type="text" name="name" placeholder="Full Name" required>
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="input-box">
                <input type="email" name="email" placeholder="Email Address" required>
                <i class="fa-solid fa-at"></i>
            </div>
            <div class="input-box">
                <textarea name="message" rows="5" placeholder="Write your message..." required></textarea>
                <i class="fa-solid fa-comment"></i>
            </div>
            <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($recaptchaSiteKey) ?>"></div>
            <button class="submit-btn" type="submit">Send Message</button>
        </form>
    </div>
</div>

<div id="toast" class="toast"></div>

<script>
const form = document.getElementById('contactForm');
const toast = document.getElementById('toast');

form.addEventListener('submit', function(e){
    e.preventDefault();

    const formData = new FormData(form);
    formData.append('action', 'contact_form');

    fetch('', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        toast.textContent = data.message;
        toast.style.background = data.status === 'success' ? 'var(--primary)' : '#dc2626';
        toast.classList.add('show');
        setTimeout(()=> toast.classList.remove('show'), 4000);

        if (data.status === 'success') form.reset();
        if (grecaptcha?.reset) grecaptcha.reset();
    })
    .catch(()=>{
        toast.textContent = 'Network error.';
        toast.style.background = '#b91c1c';
        toast.classList.add('show');
        setTimeout(()=> toast.classList.remove('show'), 4000);
    });
});
</script>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>
