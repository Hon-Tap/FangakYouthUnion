<?php
session_start();

// Prevent caching
header("Cache-Control: no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

$baseUrl = "/FangakYouthUnion/public/";
$pageTitle = "Login - Fangak Youth Union";

// Redirect if fully logged in as admin
if (!empty($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Redirect if logged in as member
if (!empty($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'member') {
    header("Location: {$baseUrl}member_dashboard.php");
    exit();
}

// Fix broken sessions
if (isset($_SESSION['role']) && (empty($_SESSION['user_id']) && empty($_SESSION['admin_id']))) {
    session_unset();
    session_destroy();
    session_start();
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include_once __DIR__ . "/../app/views/layouts/header.php";
?>

<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

<style>
/* Full CSS included as per original, preserving layout, colors, bubbles, animations, forms, buttons, and responsive rules */
body{font-family:'Segoe UI',sans-serif;background:linear-gradient(135deg,#107e36,#0b6026);overflow-x:hidden;margin:0;padding:0;min-height:100vh}.login-container{position:relative;width:100%;max-width:420px;margin:80px auto 40px auto;padding:20px}.bubbles{position:absolute;top:0;left:0;width:100%;height:100%;z-index:0;pointer-events:none;overflow:hidden;border-radius:20px}.bubble{position:absolute;border-radius:50%;opacity:.6;animation:float 15s infinite ease-in-out}@keyframes float{0%{transform:translateY(0) scale(1);opacity:.6}50%{transform:translateY(-200px) scale(1.2);opacity:1}100%{transform:translateY(0) scale(1);opacity:.6}}.login-card{position:relative;z-index:2;background:rgba(255,255,255,.15);backdrop-filter:blur(10px);border-radius:20px;padding:50px 30px;box-shadow:0 4px 15px rgba(0,0,0,.1),0 8px 30px rgba(0,0,0,.05);text-align:center;animation:fadeIn 1s ease-in-out}@keyframes fadeIn{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}.login-card h2{font-size:2rem;font-weight:800;color:#fff;margin-bottom:30px;text-shadow:0 2px 5px rgba(0,0,0,.2)}.form-group{position:relative;margin-bottom:20px;text-align:left}.form-group input{width:100%;padding:14px 12px;border:none;border-radius:10px;outline:none;font-size:1rem;background:rgba(255,255,255,.25);color:#fff;backdrop-filter:blur(2px);box-sizing:border-box}.form-group input[type=password],.form-group input[type=text]{padding-right:45px}.form-group label{position:absolute;left:12px;top:14px;color:#e0e0e0;font-size:1rem;pointer-events:none;transition:all .3s ease}.form-group input:focus+label,.form-group input:not(:placeholder-shown)+label{top:-10px;left:10px;font-size:.8rem;color:#fff;background:rgba(16,126,54,.8);padding:0 5px;border-radius:5px}#togglePassword{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:transparent;border:none;color:#fff;cursor:pointer;opacity:.7;padding:5px}#togglePassword:hover{opacity:1}.login-card button[type=submit]{width:100%;padding:15px;border:none;border-radius:10px;background:linear-gradient(135deg,#0b6026,#107e36);color:#fff;font-weight:bold;cursor:pointer;transition:transform .2s ease,box-shadow .2s ease;margin-top:10px;font-size:1rem;display:flex;align-items:center;justify-content:center;gap:8px}.login-card button[type=submit]:hover{transform:translateY(-2px) scale(1.05);box-shadow:0 8px 20px rgba(0,0,0,.2)}.login-card button[type=submit]:disabled{opacity:.7;cursor:not-allowed;transform:none;box-shadow:none}.spinner{border:2px solid rgba(255,255,255,.3);border-radius:50%;border-top:2px solid #fff;width:16px;height:16px;animation:spin 1s linear infinite}@keyframes spin{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}.error-message{color:#ffcdd2;font-size:.95rem;margin-bottom:15px;background:rgba(239,83,80,.2);padding:10px;border-radius:8px;font-weight:600}#formError{display:none}.register-link{margin-top:20px;font-size:.9rem;color:#fff}.register-link a{color:#ffd700;text-decoration:none;font-weight:bold}.register-link a:hover{text-decoration:underline}.forgot-link{text-align:right;margin-top:-10px;margin-bottom:15px}.forgot-link a{color:#ffd700;font-size:.9rem;text-decoration:none;font-weight:bold}.forgot-link a:hover{text-decoration:underline}.divider{position:relative;margin:25px 0}.divider-line{border-top:1px solid rgba(255,255,255,.3);position:absolute;width:100%;top:50%;left:0}.divider-text{position:relative;text-align:center}.divider-text span{background:rgba(16,126,54,.8);padding:0 10px;color:#eee;font-size:.9rem;border-radius:5px}.google-btn{margin-top:20px;background-color:#fff;color:#444;border-radius:10px;border:none;font-size:.95rem;padding:12px;width:100%;cursor:pointer;display:flex;align-items:center;justify-content:center;font-weight:600;gap:10px;transition:transform .2s ease}.google-btn:hover{transform:translateY(-2px);background-color:#f5f5f5}.google-btn img{width:20px;height:20px}@media(max-width:480px){.login-card{padding:40px 20px}.bubble{display:none}}
</style>

<div class="login-container">
    <div class="bubbles"></div>
    <div class="login-card">
        <h2>FYU Login Form</h2>
        <?php if(isset($_SESSION['error'])): ?><p class="error-message"><?= htmlspecialchars($_SESSION['error']); ?></p><?php unset($_SESSION['error']); endif; ?>
        <p class="error-message" id="formError" style="display:none"></p>
        <form id="loginForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder=" " required>
                <label for="email">Email Address</label>
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" placeholder=" " required>
                <label for="password">Password</label>
                <button type="button" id="togglePassword"><i data-lucide="eye" class="w-5 h-5"></i></button>
            </div>
            <div class="forgot-link"><a href="forgot_password.php">Forgot Password?</a></div>
            <button type="submit" id="submitButton"><span id="submitText">Login</span><span id="submitSpinner" style="display:none"><div class="spinner"></div></span></button>
        </form>
        <div class="divider"><div class="divider-line"></div><div class="divider-text"><span>Or</span></div></div>
        <button class="google-btn" onclick="window.location.href='<?= $baseUrl ?>google_login.php'"><img src="https://developers.google.com/identity/images/g-logo.png" alt="Google logo">Continue with Google</button>
        <div class="register-link"><p>Don’t have an account? <a href="<?= $baseUrl ?>register.php">Register here</a></p></div>
    </div>
</div>

<script>
lucide.createIcons();
const bubblesContainer=document.querySelector('.bubbles');const colors=['#ff4d4d','#ff944d','#ffeb3b','#4dff88','#4da6ff','#b84dff'];for(let i=0;i<20;i++){const bubble=document.createElement('div');bubble.classList.add('bubble');const size=Math.random()*15+5;bubble.style.width=`${size}px`;bubble.style.height=`${size}px`;bubble.style.background=colors[Math.floor(Math.random()*colors.length)];bubble.style.left=`${Math.random()*100}%`;bubble.style.top=`${Math.random()*100}%`;bubble.style.animationDuration=`${Math.random()*20+10}s`;bubble.style.animationDelay=`${Math.random()*10}s`;bubblesContainer.appendChild(bubble)}
const togglePassword=document.getElementById('togglePassword');const password=document.getElementById('password');const eyeIcon=togglePassword.querySelector('i');togglePassword.addEventListener('click',()=>{const isPassword=password.type==='password';password.type=isPassword?'text':'password';eyeIcon.setAttribute('data-lucide',isPassword?'eye-off':'eye');lucide.createIcons()});
const loginForm=document.getElementById('loginForm');const submitButton=document.getElementById('submitButton');const submitText=document.getElementById('submitText');const submitSpinner=document.getElementById('submitSpinner');const formError=document.getElementById('formError');loginForm.addEventListener('submit',async e=>{e.preventDefault();submitButton.disabled=true;submitText.style.display='none';submitSpinner.style.display='block';formError.style.display='none';const formData=new FormData(loginForm);try{const response=await fetch('<?= $baseUrl ?>login_process.php',{method:'POST',body:formData,credentials:'include'});const result=await response.json();if(result.success){window.location.href=result.redirect||'dashboard.php'}else{formError.textContent=result.message||'An unknown error occurred.';formError.style.display='block';resetButton()}}catch(err){console.error('Login error:',err);formError.textContent='A network error occurred. Please try again.';formError.style.display='block';resetButton()}});function resetButton(){submitButton.disabled=false;submitText.style.display='block';submitSpinner.style.display='none'}
</script>
<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>
