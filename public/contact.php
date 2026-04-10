<?php
declare(strict_types=1);

/* 1. CONFIGURATION & LOGIC */
require_once __DIR__ . "/../app/config/db.php";

$pageTitle = "Contact Us – Fangak Youth Union";

// Replace these with your Google reCAPTCHA v3 keys
$recaptchaSecret = '6LeBa7AsAAAAALLIHdQNHjlvtfpc5wj9Jv1agEZZ';
$recaptchaSiteKey = '6LeBa7AsAAAAAIm_D0xJEOKKXPRDkFoRh50z96xt';

/* AJAX HANDLER */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'contact_form') {
    header('Content-Type: application/json');
    
    // Check if it's an AJAX request
    if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Forbidden access.']);
        exit;
    }

    $name    = filter_var(trim($_POST['name'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
    $email   = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $message = filter_var(trim($_POST['message'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
    $token   = $_POST['g-recaptcha-response'] ?? '';

    if (!$name || !$email || !$message) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
        exit;
    }

    // Verify reCAPTCHA v3
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['secret' => $recaptchaSecret, 'response' => $token]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $captchaResult = json_decode($response, true);

    if (!$captchaResult['success'] || $captchaResult['score'] < 0.5) {
        echo json_encode(['status' => 'error', 'message' => 'Security check failed. Please try again.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$name, $email, $message]);
        echo json_encode(['status' => 'success', 'message' => 'Your message has been received. We will be in touch shortly.']);
    } catch (Throwable $e) {
        error_log("Contact Error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'A server error occurred. Please try again later.']);
    }
    exit;
}

include_once __DIR__ . "/../app/views/layouts/header.php";
?>

<script src="https://www.google.com/recaptcha/api.js?render=<?= $recaptchaSiteKey ?>"></script>

<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .floating-input:focus-within label {
        transform: translateY(-1.5rem) scale(0.85);
        color: #1f7a4b;
    }
    .form-input {
        background: transparent;
        border: none;
        border-bottom: 2px solid #e5e7eb;
        transition: all 0.3s ease;
    }
    .form-input:focus {
        border-bottom-color: #1f7a4b;
        outline: none;
    }
</style>

<main class="bg-gray-50 min-h-screen font-sans">
    <div class="bg-fyu-dark py-24 relative overflow-hidden text-white">
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Ccircle cx=\'2\' cy=\'2\' r=\'1\' fill=\'white\'/%3E%3C/svg%3E');"></div>
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <h1 class="text-5xl font-serif font-bold mb-4 tracking-tight">Get in Touch</h1>
            <p class="text-fyu-accent/80 text-lg max-w-xl">Have a question or want to support our mission? Our team is ready to listen.</p>
        </div>
    </div>

    <section class="max-w-7xl mx-auto px-6 -mt-12 pb-24">
        <div class="grid lg:grid-cols-12 gap-12">
            
            <div class="lg:col-span-4 space-y-8" data-aos="fade-right">
                <div class="glass-card p-8 rounded-[2rem] shadow-xl shadow-fyu-dark/5">
                    <h3 class="text-fyu-dark font-bold text-xl mb-8 border-l-4 border-fyu-gold pl-4">Contact Channels</h3>
                    
                    <div class="space-y-8">
                        <div class="flex gap-5">
                            <div class="w-12 h-12 rounded-2xl bg-fyu-accent flex items-center justify-center text-fyu-primary shrink-0">
                                <i class="fa-solid fa-paper-plane"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Email</p>
                                <a href="mailto:info@fangakyouth.org" class="text-gray-800 font-semibold hover:text-fyu-primary transition">info@fangakyouth.org</a>
                            </div>
                        </div>

                        <div class="flex gap-5">
                            <div class="w-12 h-12 rounded-2xl bg-fyu-accent flex items-center justify-center text-fyu-primary shrink-0">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Phone</p>
                                <a href="tel:+211924509160" class="text-gray-800 font-semibold">+211 924 509 160</a>
                            </div>
                        </div>

                        <div class="flex gap-5">
                            <div class="w-12 h-12 rounded-2xl bg-fyu-accent flex items-center justify-center text-fyu-primary shrink-0">
                                <i class="fa-solid fa-location-arrow"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Office</p>
                                <p class="text-gray-800 font-semibold">Juba, South Sudan</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-fyu-primary p-8 rounded-[2rem] text-white shadow-xl">
                    <h4 class="font-bold mb-2">Connect Digitally</h4>
                    <p class="text-white/70 text-sm mb-6">Follow us for real-time updates on our community projects.</p>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8" data-aos="fade-up">
                <div class="bg-white rounded-[2.5rem] p-10 md:p-16 shadow-2xl shadow-fyu-dark/10 border border-gray-100">
                    <form id="fyuContactForm" class="space-y-12">
                        <input type="hidden" name="action" value="contact_form">
                        
                        <div class="grid md:grid-cols-2 gap-10">
                            <div class="relative floating-input">
                                <label for="name" class="absolute left-0 top-3 text-gray-400 transition-all pointer-events-none">Full Name</label>
                                <input type="text" name="name" id="name" required class="form-input w-full py-3 text-gray-900 font-medium">
                            </div>
                            <div class="relative floating-input">
                                <label for="email" class="absolute left-0 top-3 text-gray-400 transition-all pointer-events-none">Email Address</label>
                                <input type="email" name="email" id="email" required class="form-input w-full py-3 text-gray-900 font-medium">
                            </div>
                        </div>

                        <div class="relative floating-input">
                            <label for="message" class="absolute left-0 top-3 text-gray-400 transition-all pointer-events-none">Your Message</label>
                            <textarea name="message" id="message" rows="4" required class="form-input w-full py-3 text-gray-900 font-medium resize-none"></textarea>
                        </div>

                        <div class="flex items-center justify-between gap-6 pt-4">
                            <p class="text-xs text-gray-400 max-w-xs">
                                Protected by reCAPTCHA. Your data is handled according to our <a href="#" class="underline">Privacy Policy</a>.
                            </p>
                            <button type="submit" id="submitBtn" class="bg-fyu-dark hover:bg-fyu-primary text-white px-10 py-4 rounded-2xl font-bold transition-all duration-300 shadow-xl hover:-translate-y-1 flex items-center gap-3">
                                Send Message <i class="fa-solid fa-paper-plane text-xs opacity-50"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </section>
</main>

<div id="fyuToast" class="fixed bottom-10 left-1/2 -translate-x-1/2 px-8 py-4 rounded-2xl shadow-2xl font-bold text-white transition-all duration-500 opacity-0 translate-y-10 z-[100] pointer-events-none"></div>

<script>
document.getElementById('fyuContactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const btn = document.getElementById('submitBtn');
    const toast = document.getElementById('fyuToast');
    
    // Initial UI change
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Processing...';

    grecaptcha.ready(function() {
        grecaptcha.execute('<?= $recaptchaSiteKey ?>', {action: 'submit'}).then(function(token) {
            const formData = new FormData(form);
            formData.append('g-recaptcha-response', token);

            fetch('', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                showToast(data.message, data.status === 'success' ? '#1f7a4b' : '#dc2626');
                if(data.status === 'success') form.reset();
            })
            .catch(() => showToast('Network error. Please try again.', '#dc2626'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = 'Send Message <i class="fa-solid fa-paper-plane text-xs opacity-50"></i>';
            });
        });
    });
});

function showToast(msg, color) {
    const t = document.getElementById('fyuToast');
    t.innerText = msg;
    t.style.backgroundColor = color;
    t.classList.remove('opacity-0', 'translate-y-10');
    t.classList.add('opacity-100', 'translate-y-0');
    setTimeout(() => {
        t.classList.add('opacity-0', 'translate-y-10');
        t.classList.remove('opacity-100', 'translate-y-0');
    }, 5000);
}
</script>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>