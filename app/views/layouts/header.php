<?php
// header.php – Improved, Consistent & Ultra-Responsive

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = $pageTitle ?? "Fangak Youth Union";

/* Base URL */
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$projectFolder = "/FangakYouthUnion/public/";
$baseUrl = $protocol . $host . $projectFolder;

$current_page = basename($_SERVER['PHP_SELF']);

function isActive($page, $current) {
    return $current === $page
        ? 'text-fyu-gold border-b-2 border-fyu-gold'
        : 'hover:text-fyu-gold';
}

function isActiveMobile($page, $current) {
    return $current === $page
        ? 'bg-fyu-dark text-white font-semibold'
        : 'text-gray-700 hover:bg-gray-100';
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?></title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Old+Standard+TT:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>

<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        fyu: {
          dark: '#0f3d2a',
          primary: '#1f7a4b',
          light: '#3aa76a',
          gold: '#d4a017'
        }
      },
      fontFamily: {
        sans: ['Poppins', 'sans-serif'],
        serif: ['Old Standard TT', 'serif']
      }
    }
  }
}
</script>

<style>
body { font-family: 'Poppins', sans-serif; }
</style>
</head>

<body class="bg-gray-50 min-h-screen pt-20">

<!-- HEADER -->
<header class="fixed top-0 inset-x-0 z-50 bg-gradient-to-r from-fyu-dark via-fyu-primary to-fyu-dark shadow-lg">
  <div class="max-w-7xl mx-auto px-4">
    <div class="flex items-center justify-between h-20">

      <!-- LOGO -->
      <a href="<?= $baseUrl ?>index.php" class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-full overflow-hidden border-2 border-white">
          <img src="<?= $baseUrl ?>images/FYU-LOGO.jpg" class="w-full h-full object-cover" alt="FYU Logo">
        </div>
        <div class="leading-tight text-white">
          <div class="font-serif font-bold text-lg">Fangak Youth Union</div>
          <div class="text-[11px] tracking-widest opacity-80">UNITY & PROGRESS</div>
        </div>
      </a>

      <!-- DESKTOP NAV -->
      <nav class="hidden lg:flex items-center gap-8 text-sm font-medium text-white">
        <a href="<?= $baseUrl ?>index.php" class="<?= isActive('index.php', $current_page) ?>">Home</a>
        <a href="<?= $baseUrl ?>about.php" class="<?= isActive('about.php', $current_page) ?>">About</a>
        <a href="<?= $baseUrl ?>project.php" class="<?= isActive('project.php', $current_page) ?>">Projects</a>
        <a href="<?= $baseUrl ?>blog.php" class="<?= isActive('blog.php', $current_page) ?>">Blog</a>
        <a href="<?= $baseUrl ?>events.php" class="<?= isActive('events.php', $current_page) ?>">Events</a>
        <a href="<?= $baseUrl ?>contact.php" class="<?= isActive('contact.php', $current_page) ?>">Contact</a>

        <?php if (!empty($_SESSION['user_id'])): ?>
          <a href="<?= $baseUrl ?>member_dashboard.php"
             class="ml-4 px-5 py-2 bg-white text-fyu-primary rounded-full font-semibold hover:bg-fyu-gold hover:text-fyu-dark transition">
            Dashboard
          </a>
        <?php else: ?>
          <a href="<?= $baseUrl ?>register.php"
             class="ml-4 px-5 py-2 bg-fyu-gold text-fyu-dark rounded-full font-semibold hover:bg-white transition">
            Join Us
          </a>
        <?php endif; ?>
      </nav>

      <!-- MOBILE BUTTON -->
      <button id="menuBtn" class="lg:hidden text-white text-2xl">
        <i class="fa-solid fa-bars"></i>
      </button>
    </div>
  </div>
</header>

<!-- MOBILE OVERLAY -->
<div id="mobileOverlay" class="fixed inset-0 bg-black/60 z-[60] hidden">
  <aside id="mobileMenu"
         class="absolute right-0 top-0 h-full w-72 bg-white shadow-xl flex flex-col">

    <div class="flex items-center justify-between p-5 bg-fyu-dark text-white">
      <span class="font-serif font-bold text-lg">Menu</span>
      <button id="closeMenu" class="text-xl"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <nav class="flex-1 p-4 space-y-1">
      <a href="<?= $baseUrl ?>index.php" class="block px-4 py-3 rounded <?= isActiveMobile('index.php', $current_page) ?>">Home</a>
      <a href="<?= $baseUrl ?>about.php" class="block px-4 py-3 rounded <?= isActiveMobile('about.php', $current_page) ?>">About</a>
      <a href="<?= $baseUrl ?>project.php" class="block px-4 py-3 rounded <?= isActiveMobile('project.php', $current_page) ?>">Projects</a>
      <a href="<?= $baseUrl ?>blog.php" class="block px-4 py-3 rounded <?= isActiveMobile('blog.php', $current_page) ?>">Blog</a>
      <a href="<?= $baseUrl ?>events.php" class="block px-4 py-3 rounded <?= isActiveMobile('events.php', $current_page) ?>">Events</a>
      <a href="<?= $baseUrl ?>contact.php" class="block px-4 py-3 rounded <?= isActiveMobile('contact.php', $current_page) ?>">Contact</a>
    </nav>

    <div class="p-4 border-t">
      <?php if (!empty($_SESSION['user_id'])): ?>
        <a href="<?= $baseUrl ?>member_dashboard.php"
           class="block w-full text-center py-3 bg-fyu-primary text-white rounded-lg mb-2">
          Dashboard
        </a>
        <a href="<?= $baseUrl ?>logout.php"
           class="block w-full text-center py-3 border rounded-lg text-red-600">
          Logout
        </a>
      <?php else: ?>
        <a href="<?= $baseUrl ?>register.php"
           class="block w-full text-center py-3 bg-fyu-primary text-white rounded-lg mb-2">
          Register
        </a>
        <a href="<?= $baseUrl ?>login.php"
           class="block w-full text-center py-3 text-fyu-dark">
          Login
        </a>
      <?php endif; ?>
    </div>
  </aside>
</div>

<script>
const menuBtn = document.getElementById('menuBtn');
const closeMenu = document.getElementById('closeMenu');
const overlay = document.getElementById('mobileOverlay');

menuBtn.onclick = () => {
  overlay.classList.remove('hidden');
  document.body.style.overflow = 'hidden';
};

closeMenu.onclick = () => {
  overlay.classList.add('hidden');
  document.body.style.overflow = '';
};

overlay.onclick = e => {
  if (e.target === overlay) closeMenu.onclick();
};
</script>
