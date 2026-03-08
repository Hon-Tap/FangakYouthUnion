<?php
/**
 * Global Header Layout
 * Fangak Youth Union
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = $pageTitle ?? "Fangak Youth Union";

/**
 * Base URL
 * Root-relative so it works everywhere
 */
$baseUrl = "/";

/**
 * Detect current page
 */
$current_page = basename($_SERVER['SCRIPT_NAME']);

/**
 * Active nav helpers
 */
function navActive($page, $current) {
    return $current === $page
        ? 'text-fyu-gold border-b-2 border-fyu-gold'
        : 'hover:text-fyu-gold';
}

function navActiveMobile($page, $current) {
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

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Old+Standard+TT:wght@400;700&display=swap" rel="stylesheet">

<!-- Icons -->
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

<!-- Tailwind -->
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

<!-- ================= HEADER ================= -->

<header class="fixed top-0 inset-x-0 z-50 bg-gradient-to-r from-fyu-dark via-fyu-primary to-fyu-dark shadow-lg">

<div class="max-w-7xl mx-auto px-4">

<div class="flex items-center justify-between h-20">

<!-- Logo -->
<a href="<?= $baseUrl ?>" class="flex items-center gap-3">

<div class="w-11 h-11 rounded-full overflow-hidden border-2 border-white">
<img src="/images/FYU-LOGO.jpg" alt="FYU Logo"
class="w-full h-full object-cover">
</div>

<div class="leading-tight text-white">
<div class="font-serif font-bold text-lg">
Fangak Youth Union
</div>
<div class="text-[11px] tracking-widest opacity-80">
UNITY & PROGRESS
</div>
</div>

</a>

<!-- Desktop Nav -->
<nav class="hidden lg:flex items-center gap-8 text-sm font-medium text-white">

<a href="/index.php"
class="<?= navActive('index.php',$current_page) ?>">Home</a>

<a href="/about.php"
class="<?= navActive('about.php',$current_page) ?>">About</a>

<a href="/project.php"
class="<?= navActive('project.php',$current_page) ?>">Projects</a>

<a href="/blog.php"
class="<?= navActive('blog.php',$current_page) ?>">Blog</a>

<a href="/events.php"
class="<?= navActive('events.php',$current_page) ?>">Events</a>

<a href="/contact.php"
class="<?= navActive('contact.php',$current_page) ?>">Contact</a>

<?php if (!empty($_SESSION['user_id'])): ?>

<a href="/member_dashboard.php"
class="ml-4 px-5 py-2 bg-white text-fyu-primary rounded-full font-semibold hover:bg-fyu-gold hover:text-fyu-dark transition">

Dashboard

</a>

<?php else: ?>

<a href="/register.php"
class="ml-4 px-5 py-2 bg-fyu-gold text-fyu-dark rounded-full font-semibold hover:bg-white transition">

Join Us

</a>

<?php endif; ?>

</nav>

<!-- Mobile Button -->
<button id="menuBtn"
class="lg:hidden text-white text-2xl">

<i class="fa-solid fa-bars"></i>

</button>

</div>
</div>

</header>


<!-- ============= MOBILE MENU ============= -->

<div id="mobileOverlay"
class="fixed inset-0 bg-black/60 z-[60] hidden">

<aside class="absolute right-0 top-0 h-full w-72 bg-white shadow-xl flex flex-col">

<div class="flex items-center justify-between p-5 bg-fyu-dark text-white">

<span class="font-serif font-bold text-lg">Menu</span>

<button id="closeMenu" class="text-xl">
<i class="fa-solid fa-xmark"></i>
</button>

</div>


<nav class="flex-1 p-4 space-y-1">

<a href="/index.php"
class="block px-4 py-3 rounded <?= navActiveMobile('index.php',$current_page) ?>">
Home
</a>

<a href="/about.php"
class="block px-4 py-3 rounded <?= navActiveMobile('about.php',$current_page) ?>">
About
</a>

<a href="/project.php"
class="block px-4 py-3 rounded <?= navActiveMobile('project.php',$current_page) ?>">
Projects
</a>

<a href="/blog.php"
class="block px-4 py-3 rounded <?= navActiveMobile('blog.php',$current_page) ?>">
Blog
</a>

<a href="/events.php"
class="block px-4 py-3 rounded <?= navActiveMobile('events.php',$current_page) ?>">
Events
</a>

<a href="/contact.php"
class="block px-4 py-3 rounded <?= navActiveMobile('contact.php',$current_page) ?>">
Contact
</a>

</nav>


<div class="p-4 border-t">

<?php if (!empty($_SESSION['user_id'])): ?>

<a href="/member_dashboard.php"
class="block w-full text-center py-3 bg-fyu-primary text-white rounded-lg mb-2">
Dashboard
</a>

<a href="/logout.php"
class="block w-full text-center py-3 border rounded-lg text-red-600">
Logout
</a>

<?php else: ?>

<a href="/register.php"
class="block w-full text-center py-3 bg-fyu-primary text-white rounded-lg mb-2">
Register
</a>

<a href="/login.php"
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

overlay.onclick = (e) => {
  if (e.target === overlay) closeMenu.onclick();
};

</script>