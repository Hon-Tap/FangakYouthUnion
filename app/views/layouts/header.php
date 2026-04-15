<?php

/**
 * Global Header Layout - PRO VERSION
 * Fangak Youth Union
 */

$pageTitle = $pageTitle ?? "Fangak Youth Union";

/**
 * Base URL
 * Change this only if your site runs in a subfolder
 * Example: /fyu/
 */
$baseUrl = "/";

/**
 * Detect current page
 */
$current_page = basename($_SERVER['SCRIPT_NAME']);

/**
 * Render Favicon Links
 * Assumes files are in:
 * /public/favicon_io/
 * OR
 * /favicon_io/
 */
function renderFavicons() {

    global $baseUrl;

    $path = $baseUrl . "favicon_io/";

    return '
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="'.$path.'apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="'.$path.'favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="'.$path.'favicon-16x16.png">
    <link rel="manifest" href="'.$path.'site.webmanifest">
    <link rel="shortcut icon" href="'.$path.'favicon.ico">
    ';
}

/**
 * Active nav helpers with sleek animated underlines
 */
function navActive($page, $current) {

    return $current === $page
        ? 'text-fyu-gold after:w-full'
        : 'text-gray-200 hover:text-white after:w-0 hover:after:w-full';

}

function navActiveMobile($page, $current) {

    return $current === $page
        ? 'bg-fyu-primary/20 text-fyu-gold border-l-4 border-fyu-gold font-semibold'
        : 'text-gray-300 hover:bg-white/5 border-l-4 border-transparent hover:text-white';

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<meta http-equiv="X-UA-Compatible" content="IE=edge">

<title><?= htmlspecialchars($pageTitle) ?></title>

<meta name="description" content="Fangak Youth Union - Peace, Unity & Development">

<meta name="author" content="Fangak Youth Union">

<?= renderFavicons(); ?>

<!-- Tailwind / CSS -->
<link rel="stylesheet" href="<?= $baseUrl ?>css/styles.css">

</head>

<body>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?></title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Old+Standard+TT:wght@400;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

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
          gold: '#d4a017',
          darker: '#0a291c'
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

/* Custom Animated Hamburger Menu */
.hamburger {
    width: 30px;
    height: 20px;
    position: relative;
    cursor: pointer;
    z-index: 70; /* Above overlay */
}
.hamburger span {
    display: block;
    position: absolute;
    height: 2px;
    width: 100%;
    background: white;
    border-radius: 2px;
    opacity: 1;
    left: 0;
    transition: .25s ease-in-out;
}
.hamburger span:nth-child(1) { top: 0px; }
.hamburger span:nth-child(2) { top: 9px; }
.hamburger span:nth-child(3) { top: 18px; }

/* Hamburger Active State (The 'X') */
.hamburger.open span:nth-child(1) {
    top: 9px;
    transform: rotate(135deg);
}
.hamburger.open span:nth-child(2) {
    opacity: 0;
    left: -20px;
}
.hamburger.open span:nth-child(3) {
    top: 9px;
    transform: rotate(-135deg);
}
</style>

</head>

<body class="bg-gray-50 min-h-screen pt-20">

<header class="fixed top-0 inset-x-0 z-50 bg-fyu-darker/90 backdrop-blur-md border-b border-white/10 shadow-lg transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">

            <a href="<?= $baseUrl ?>" class="flex items-center gap-3 group">
                <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-fyu-gold shadow-[0_0_10px_rgba(212,160,23,0.3)] transition-transform duration-300 group-hover:scale-105 group-hover:shadow-[0_0_15px_rgba(212,160,23,0.6)]">
                    <img src="/images/FYU-LOGO.jpg" alt="FYU Logo" class="w-full h-full object-cover">
                </div>
                <div class="leading-tight text-white">
                    <div class="font-serif font-bold text-lg tracking-wide group-hover:text-fyu-gold transition-colors duration-300">
                        Fangak Youth Union
                    </div>
                    <div class="text-[10px] tracking-[0.2em] text-gray-300 uppercase font-medium">
                        Unity & Progress
                    </div>
                </div>
            </a>

            <nav class="hidden lg:flex items-center gap-7 text-[15px] font-medium">
                
                <?php 
                $navItems = [
                    'index.php' => 'Home',
                    'about.php' => 'About',
                    'project.php' => 'Projects',
                    'blog.php' => 'Blog',
                    'events.php' => 'Events',
                    'contact.php' => 'Contact'
                ];
                
                foreach($navItems as $url => $label): ?>
                    <a href="/<?= $url ?>" class="relative py-2 transition-colors duration-300 <?= navActive($url, $current_page) ?> after:absolute after:bottom-0 after:left-0 after:h-[2px] after:bg-fyu-gold after:transition-all after:duration-300">
                        <?= $label ?>
                    </a>
                <?php endforeach; ?>

                <div class="pl-4 border-l border-white/20">
                    <?php if (!empty($_SESSION['user_id'])): ?>
                        <a href="/member_dashboard.php" class="px-6 py-2.5 bg-white/10 text-white border border-white/20 rounded-full font-semibold hover:bg-fyu-gold hover:text-fyu-darker hover:border-fyu-gold transition-all duration-300 shadow-sm">
                            Dashboard
                        </a>
                    <?php else: ?>
                        <a href="/register.php" class="px-6 py-2.5 bg-gradient-to-r from-fyu-gold to-yellow-500 text-fyu-darker rounded-full font-semibold hover:shadow-[0_0_15px_rgba(212,160,23,0.5)] hover:-translate-y-0.5 transition-all duration-300">
                            Register
                        </a>
                    <?php endif; ?>
                </div>
            </nav>

            <div class="lg:hidden flex items-center">
                <div id="menuBtn" class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>

        </div>
    </div>
</header>

<div id="mobileOverlay" class="fixed inset-0 bg-fyu-darker/80 backdrop-blur-sm z-[60] opacity-0 pointer-events-none transition-opacity duration-300"></div>

<aside id="mobileMenu" class="fixed right-0 top-0 h-full w-[280px] bg-fyu-dark shadow-2xl z-[65] transform translate-x-full transition-transform duration-400 ease-in-out flex flex-col border-l border-white/10">
    
    <div class="flex items-center justify-between p-6 border-b border-white/10">
        <span class="font-serif font-bold text-xl text-white">Menu</span>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <?php foreach($navItems as $url => $label): ?>
            <a href="/<?= $url ?>" class="block px-4 py-3 rounded-r-lg transition-colors <?= navActiveMobile($url, $current_page) ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="p-6 border-t border-white/10 bg-fyu-darker">
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="/member_dashboard.php" class="block w-full text-center py-3 bg-white/10 text-white rounded-lg mb-3 hover:bg-white/20 transition">
                Dashboard
            </a>
            <a href="/logout.php" class="block w-full text-center py-3 border border-red-500/50 text-red-400 rounded-lg hover:bg-red-500/10 transition">
                Logout
            </a>
        <?php else: ?>
            <a href="/register.php" class="block w-full text-center py-3 bg-fyu-gold text-fyu-darker font-bold rounded-lg mb-3 hover:bg-yellow-400 transition shadow-lg shadow-fyu-gold/20">
                Register
            </a>
        <?php endif; ?>
    </div>
</aside>

<script>
    const menuBtn = document.getElementById('menuBtn');
    const overlay = document.getElementById('mobileOverlay');
    const mobileMenu = document.getElementById('mobileMenu');
    let isMenuOpen = false;

    function toggleMenu() {
        isMenuOpen = !isMenuOpen;
        
        // Animate Hamburger
        menuBtn.classList.toggle('open');
        
        if (isMenuOpen) {
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100');
            mobileMenu.classList.remove('translate-x-full');
            document.body.style.overflow = 'hidden';
        } else {
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            mobileMenu.classList.add('translate-x-full');
            document.body.style.overflow = '';
        }
    }

    menuBtn.onclick = toggleMenu;
    overlay.onclick = toggleMenu;
</script>