<?php
declare(strict_types=1);

$pageTitle = "Contact Us – Fangak Youth Union";

// Assumes Tailwind CSS setup from header.php is active.
include_once __DIR__ . "/../app/views/layouts/header.php";

// Assuming $pdo is available after including db.php.
// If it's not, you might need to adjust the path or ensure db.php includes it.
if (!isset($pdo)) {
    include_once __DIR__ . "/../app/config/db.php";
}

/* reCAPTCHA keys - Replace with actual keys */
$recaptchaSecret = 'YOUR_SECRET_KEY';
$recaptchaSiteKey = 'YOUR_SITE_KEY';

/* AJAX form handler (Unmodified logic) */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'contact_form') {

    if (
        empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
    ) {
        http_response_code(403);
        exit;
    }

    header('Content-Type: application/json');

    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $captcha = $_POST['g-recaptcha-response'] ?? '';

    // Validation checks (unmodified for brevity)
    if (!$name || !$email || !$message) {
        echo json_encode(['status'=>'error','message'=>'All fields are required.']);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status'=>'error','message'=>'Please enter a valid email address.']);
        exit;
    }
    if (!$captcha) {
        echo json_encode(['status'=>'error','message'=>'Please verify that you are not a robot.']);
        exit;
    }

    // reCAPTCHA verification (unmodified for brevity)
    $verify = @file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$captcha}"
    );
    $captchaSuccess = json_decode($verify ?: '{}', true);

    if (empty($captchaSuccess['success'])) {
        echo json_encode(['status'=>'error','message'=>'Captcha verification failed.']);
        exit;
    }

    // Database insertion (unmodified for brevity)
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO contacts (name, email, message, created_at)
             VALUES (:n, :e, :m, NOW())"
        );
        $stmt->execute([
            ':n' => $name,
            ':e' => $email,
            ':m' => $message
        ]);

        echo json_encode(['status'=>'success','message'=>'Thank you! Your message has been sent.']);
    } catch (Throwable $e) {
        // Log the error for debugging
        error_log("Contact Form DB Error: " . $e->getMessage());
        echo json_encode(['status'=>'error','message'=>'Something went wrong. Please try again later.']);
    }
    exit;
}
?>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<style>
    /* Unique Input Design: Bottom border focus effect 
    This overrides the default rounded-xl border-gray-300 provided by Tailwind
    for a more unique, clean look that highlights FYU colors on focus.
    */
    .fyu-input-style {
        border-radius: 0.75rem; /* rounded-xl */
        border: 1px solid #d1d5db; /* border-gray-300 */
        transition: all 0.2s ease-in-out;
        box-shadow: none;
    }
    /* Focus State - Unique Border */
    .fyu-input-style:focus {
        border-color: #1f7a4b; /* fyu-primary */
        box-shadow: 0 0 0 4px rgba(31, 122, 75, 0.25); /* Subtle green ring */
        outline: none;
        background-color: #f7fcf8; /* Lightest green background on focus */
    }
    
    /* Unique Focus for Textarea */
    textarea.fyu-input-style {
        resize: vertical;
        min-height: 120px;
    }
    
    /* Icon Styling for input fields */
    .input-group {
        position: relative;
    }
    .input-group i {
        position: absolute;
        top: 50%;
        left: 1rem;
        transform: translateY(-50%);
        color: #9ca3af; /* Gray-400 */
        transition: color 0.2s ease;
    }
    .input-group input:focus + i, 
    .input-group textarea:focus + i {
        color: #1f7a4b; /* fyu-primary on focus */
    }
    .input-group input, .input-group textarea {
        padding-left: 2.5rem; /* Space for the icon */
    }
</style>

<main>
    <div class="bg-fyu-dark pt-20 pb-16 text-white overflow-hidden relative">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;"></div>
        <div class="max-w-7xl mx-auto px-4 relative z-10 text-center">
            <h1 class="text-4xl md:text-5xl font-serif font-bold mb-4">
                Connect with FYU
            </h1>
            <p class="max-w-2xl mx-auto text-white/90 text-lg">
                Your voice is crucial to our mission. Reach out to the Fangak Youth Union leadership today.
            </p>
        </div>
    </div>

    <section class="max-w-7xl mx-auto px-4 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

            <div class="space-y-10">
                <h2 class="text-3xl font-bold text-gray-800 border-l-4 border-fyu-gold pl-4">
                    Our Details
                </h2>
                
                <p class="text-gray-600 text-lg max-w-md">
                    We welcome partnerships, ideas, and inquiries from youth, partners, and stakeholders.
                </p>

                <div class="space-y-6">
                    
                    <div class="flex items-start gap-4 p-5 bg-white rounded-xl shadow-md border border-gray-100">
                        <i class="fa-solid fa-envelope text-fyu-gold text-2xl mt-1"></i>
                        <div>
                            <div class="font-bold text-fyu-dark text-lg">General Inquiries</div>
                            <a href="mailto:info@fangakyouth.org" class="text-fyu-primary hover:text-fyu-dark transition">
                                info@fangakyouth.org
                            </a>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-5 bg-white rounded-xl shadow-md border border-gray-100">
                        <i class="fa-solid fa-phone-volume text-fyu-gold text-2xl mt-1"></i>
                        <div>
                            <div class="font-bold text-fyu-dark text-lg">Phone Support (South Sudan)</div>
                            <a href="tel:+211924509160" class="text-fyu-primary hover:text-fyu-dark transition">
                                +211 924 509 160
                            </a>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-5 bg-white rounded-xl shadow-md border border-gray-100">
                        <i class="fa-solid fa-location-dot text-fyu-gold text-2xl mt-1"></i>
                        <div>
                            <div class="font-bold text-fyu-dark text-lg">Headquarters</div>
                            <p class="text-gray-600">Juba, Central Equatoria, South Sudan</p>
                        </div>
                    </div>
                </div>

                <div class="h-64 bg-gray-200 rounded-xl overflow-hidden shadow-inner flex items-center justify-center text-gray-500">
                    [Map Embed Placeholder]
                </div>
            </div>

            <div class="lg:sticky lg:top-24 h-fit">
                <div class="bg-white rounded-2xl shadow-xl p-8 md:p-10 border border-gray-100">
                    <h2 class="text-2xl font-semibold text-fyu-dark mb-8 text-center">
                        Send Us a Direct Message
                    </h2>

                    <form id="contactForm" class="space-y-6">
                        <input type="hidden" name="action" value="contact_form">

                        <div class="input-group">
                            <label for="name" class="sr-only">Full Name</label>
                            <input type="text" name="name" id="name" placeholder="Your Full Name" required
                                class="fyu-input-style w-full px-4 py-3 placeholder:text-gray-500">
                            <i class="fa-solid fa-user"></i>
                        </div>

                        <div class="input-group">
                            <label for="email" class="sr-only">Email Address</label>
                            <input type="email" name="email" id="email" placeholder="Your Email Address" required
                                class="fyu-input-style w-full px-4 py-3 placeholder:text-gray-500">
                            <i class="fa-solid fa-at"></i>
                        </div>

                        <div class="input-group">
                            <label for="message" class="sr-only">Message</label>
                            <textarea name="message" id="message" rows="5" placeholder="Your Message or Inquiry" required
                                class="fyu-input-style w-full px-4 py-3 placeholder:text-gray-500"></textarea>
                            <i class="fa-solid fa-comment-dots" style="top: 1.75rem; transform: translateY(0);"></i>
                        </div>

                        <div class="flex justify-center pt-2">
                            <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($recaptchaSiteKey) ?>"></div>
                        </div>

                        <button type="submit"
                            class="w-full py-3 rounded-xl bg-fyu-primary text-white font-bold text-lg
                            hover:bg-fyu-dark transition duration-300 shadow-md shadow-fyu-primary/30 transform hover:-translate-y-0.5">
                            Submit Inquiry <i class="fa-solid fa-paper-plane ml-2"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

</main>

<div id="toast"
     class="fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-2xl transition-all duration-300 transform translate-x-full opacity-0 z-50 min-w-[250px]">
</div>

<script>
const form = document.getElementById('contactForm');
const toast = document.getElementById('toast');
const submitButton = form.querySelector('button[type="submit"]');

form.addEventListener('submit', e => {
    e.preventDefault();

    // Disable button and show loading state
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Sending...';

    const data = new FormData(form);

    fetch('', {
        method: 'POST',
        body: data,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(res => {
        // Update toast content and style
        toast.textContent = res.message;
        
        if (res.status === 'success') {
            toast.className = 'fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-2xl transition-all duration-300 z-50 bg-fyu-primary text-white';
            form.reset();
            if (window.grecaptcha) grecaptcha.reset();
        } else {
            toast.className = 'fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-2xl transition-all duration-300 z-50 bg-red-600 text-white';
        }

        // Show toast
        toast.classList.remove('translate-x-full', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');

        // Hide toast after 4 seconds
        setTimeout(() => {
            toast.classList.remove('translate-x-0', 'opacity-100');
            toast.classList.add('translate-x-full', 'opacity-0');
        }, 4000);
    })
    .catch(() => {
        toast.textContent = 'Network error. Please try again.';
        toast.className = 'fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-2xl transition-all duration-300 z-50 bg-red-600 text-white translate-x-0 opacity-100';
        setTimeout(() => toast.classList.add('translate-x-full', 'opacity-0'), 4000);
    })
    .finally(() => {
        // Re-enable button
        submitButton.disabled = false;
        submitButton.innerHTML = 'Submit Inquiry <i class="fa-solid fa-paper-plane ml-2"></i>';
    });
});
</script>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>