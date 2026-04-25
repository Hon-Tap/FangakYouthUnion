<?php
declare(strict_types=1);

require_once __DIR__ . "/../app/config/db.php";

$pageTitle = "Contact Us – Fangak Youth Union";
$recaptchaSecret = '6LeBa7AsAAAAALLIHdQNHjlvtfpc5wj9Jv1agEZZ';
$recaptchaSiteKey = '6LeBa7AsAAAAAIm_D0xJEOKKXPRDkFoRh50z96xt';

/* AJAX HANDLER */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'contact_form') {
    header('Content-Type: application/json');
    
    if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Forbidden access.']);
        exit;
    }

    $name    = filter_var(trim($_POST['name'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
    $email   = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $subject = filter_var(trim($_POST['subject'] ?? 'General Inquiry'), FILTER_SANITIZE_SPECIAL_CHARS);
    $message = filter_var(trim($_POST['message'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
    $token   = $_POST['g-recaptcha-response'] ?? '';

    if (!$name || !$email || !$message) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
        exit;
    }

    // reCAPTCHA Verification
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['secret' => $recaptchaSecret, 'response' => $token]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $captchaResult = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (!$captchaResult['success'] || $captchaResult['score'] < 0.5) {
        echo json_encode(['status' => 'error', 'message' => 'Security check failed.']);
        exit;
    }

    try {
        // FIXED: Using your actual table name 'messages' and column names from your screenshot
        $sql = "INSERT INTO messages (sender_name, sender_email, subject, message) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $email, $subject, $message]);
        
        echo json_encode(['status' => 'success', 'message' => 'Message sent successfully!']);
    } catch (Throwable $e) {
        error_log("DB Error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Database error. Please try again.']);
    }
    exit;
}

include_once __DIR__ . "/../app/views/layouts/header.php";
?>

<script src="https://www.google.com/recaptcha/api.js?render=<?= $recaptchaSiteKey ?>"></script>

<style>
    :root { --fyu-green: #1f7a4b; --fyu-gold: #d4af37; }
    
    .contact-hero { background: linear-gradient(135deg, #062c1a 0%, #1f7a4b 100%); }
    
    /* Improved Floating Labels */
    .input-group { position: relative; margin-top: 1.5rem; }
    .form-input {
        width: 100%; padding: 12px 0; font-size: 1rem; border: none;
        border-bottom: 2px solid #e5e7eb; background: transparent; transition: 0.3s;
    }
    .form-input:focus { outline: none; border-bottom-color: var(--fyu-green); }
    
    .input-label {
        position: absolute; left: 0; top: 12px; color: #9ca3af;
        transition: 0.3s; pointer-events: none;
    }
    /* Fixed the overlap: label moves up when input has text or is focused */
    .form-input:focus ~ .input-label,
    .form-input:not(:placeholder-shown) ~ .input-label {
        top: -12px; font-size: 0.85rem; color: var(--fyu-green); font-weight: bold;
    }

    .glass-sidebar {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.4);
    }
</style>

<main class="bg-gray-50 min-h-screen">
    <div class="contact-hero py-24 text-white">
        <div class="max-w-7xl mx-auto px-6">
            <h1 class="text-5xl font-bold mb-4">Let's Connect</h1>
            <p class="text-white/80 text-lg">Unity and Progress for the Fangak Community.</p>
        </div>
    </div>

    <section class="max-w-7xl mx-auto px-6 -mt-16 pb-24">
        <div class="grid lg:grid-cols-12 gap-8">
            <div class="lg:col-span-4 space-y-6">
                <div class="glass-sidebar p-8 rounded-3xl shadow-lg">
                    <h3 class="font-bold text-xl mb-6 border-l-4 border-fyu-gold pl-4">Contact Info</h3>
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-green-100 text-green-700 rounded-xl"><i class="fas fa-envelope"></i></div>
                            <div><p class="text-xs text-gray-500 uppercase font-bold">Email</p><p class="font-medium">info@fangakyouth.org</p></div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-yellow-100 text-yellow-700 rounded-xl"><i class="fas fa-phone"></i></div>
                            <div><p class="text-xs text-gray-500 uppercase font-bold">Phone</p><p class="font-medium">+211 924 509 160</p></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8">
                <div class="bg-white rounded-3xl p-10 shadow-xl">
                    <form id="fyuContactForm" class="space-y-8">
                        <input type="hidden" name="action" value="contact_form">
                        
                        <div class="grid md:grid-cols-2 gap-8">
                            <div class="input-group">
                                <input type="text" name="name" id="name" placeholder=" " required class="form-input">
                                <label class="input-label">Full Name</label>
                            </div>
                            <div class="input-group">
                                <input type="email" name="email" id="email" placeholder=" " required class="form-input">
                                <label class="input-label">Email Address</label>
                            </div>
                        </div>

                        <div class="input-group">
                            <input type="text" name="subject" id="subject" placeholder=" " class="form-input">
                            <label class="input-label">Subject (Optional)</label>
                        </div>

                        <div class="input-group">
                            <textarea name="message" id="message" rows="4" placeholder=" " required class="form-input resize-none"></textarea>
                            <label class="input-label">How can we help?</label>
                        </div>

                        <div class="flex items-center justify-between">
                            <p class="text-xs text-gray-400 max-w-xs italic">Securely protected by Google reCAPTCHA</p>
                            <button type="submit" id="submitBtn" class="bg-green-700 hover:bg-green-800 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg flex items-center gap-2">
                                Send Message <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<div id="fyuToast" class="fixed bottom-10 left-1/2 -translate-x-1/2 px-6 py-3 rounded-xl text-white font-bold opacity-0 transition-all duration-300 z-50"></div>

<script>
document.getElementById('fyuContactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = 'Sending...';

    grecaptcha.ready(() => {
        grecaptcha.execute('<?= $recaptchaSiteKey ?>', {action: 'submit'}).then((token) => {
            const formData = new FormData(this);
            formData.append('g-recaptcha-response', token);

            fetch('', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                showToast(data.message, data.status === 'success' ? '#1f7a4b' : '#dc2626');
                if(data.status === 'success') this.reset();
            })
            .catch(() => showToast('Network Error', '#dc2626'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = 'Send Message <i class="fas fa-paper-plane"></i>';
            });
        });
    });
});

function showToast(msg, color) {
    const t = document.getElementById('fyuToast');
    t.innerText = msg; t.style.backgroundColor = color;
    t.classList.remove('opacity-0');
    setTimeout(() => t.classList.add('opacity-0'), 4000);
}
</script>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>