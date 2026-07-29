<?php
/**
 * Global Header Layout - Enterprise Edition
 * Fangak Youth Union (FYU)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Config & Active State Helpers
$pageTitle   = $pageTitle ?? "Fangak Youth Union | Peace, Unity & Development";
$baseUrl     = $baseUrl ?? "/";
$currentPage = basename($_SERVER['SCRIPT_NAME']);

function navActive($page, $current) {
    return $current === $page
        ? 'text-fyu-gold font-semibold after:w-full'
        : 'text-gray-200 hover:text-fyu-gold after:w-0 hover:after:w-full';
}

function navActiveMobile($page, $current) {
    return $current === $page
        ? 'bg-fyu-gold/15 text-fyu-gold border-l-4 border-fyu-gold font-semibold pl-4'
        : 'text-gray-300 hover:bg-white/5 border-l-4 border-transparent hover:text-white pl-4';
}

$navItems = [
    'index.php'   => 'Home',
    'about.php'   => 'About',
    'project.php' => 'Projects',
    'blog.php'    => 'Blog',
    'events.php'  => 'Events',
    'contact.php' => 'Contact'
];
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="Fangak Youth Union - Empowering the next generation through peace, unity, and sustainable community development.">
    <meta name="author" content="Fangak Youth Union">
    
    <!-- Open Graph / Social Media -->
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="Empowering the next generation through sustainable community development.">
    <meta property="og:image" content="<?= $baseUrl ?>images/FYU-LOGO.jpg">
    <meta property="og:type" content="website">

    <!-- Favicon Suite -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $baseUrl ?>favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $baseUrl ?>favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $baseUrl ?>favicon_io/favicon-16x16.png">
    <link rel="manifest" href="<?= $baseUrl ?>favicon_io/site.webmanifest">
    <link rel="shortcut icon" href="<?= $baseUrl ?>favicon_io/favicon.ico">

    <!-- Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Old+Standard+TT:ital,wght@0,400;0,700;1,400&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        fyu: {
                            darker: '#082117',
                            dark:   '#0f3d2a',
                            primary:'#1f7a4b',
                            light:  '#3aa76a',
                            gold:   '#d4a017',
                            goldHover: '#c19012'
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
        
        /* Modern Hamburger Component */
        .hamburger {
            width: 28px;
            height: 20px;
            position: relative;
            cursor: pointer;
            z-index: 70;
        }
        .hamburger span {
            display: block;
            position: absolute;
            height: 2px;
            width: 100%;
            background: #ffffff;
            border-radius: 2px;
            opacity: 1;
            left: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .hamburger span:nth-child(1) { top: 0px; }
        .hamburger span:nth-child(2) { top: 9px; }
        .hamburger span:nth-child(3) { top: 18px; }

        .hamburger.open span:nth-child(1) {
            top: 9px;
            transform: rotate(135deg);
        }
        .hamburger.open span:nth-child(2) {
            opacity: 0;
            transform: translateX(-20px);
        }
        .hamburger.open span:nth-child(3) {
            top: 9px;
            transform: rotate(-135deg);
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 antialiased selection:bg-fyu-gold selection:text-fyu-darker min-h-screen pt-20">

<!-- Top Announcement & Security Bar -->
<header id="mainHeader" class="fixed top-0 inset-x-0 z-50 bg-fyu-darker/95 backdrop-blur-md border-b border-white/10 shadow-lg transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">

            <!-- Brand Logo & Title -->
            <a href="<?= $baseUrl ?>" class="flex items-center gap-3.5 group focus:outline-none focus:ring-2 focus:ring-fyu-gold/50 rounded-lg p-1">
                <div class="relative w-12 h-12 rounded-full border-2 border-fyu-gold/80 overflow-hidden shadow-[0_0_12px_rgba(212,160,23,0.35)] transition-all duration-300 group-hover:scale-105 group-hover:shadow-[0_0_20px_rgba(212,160,23,0.6)]">
                    <img src="<?= $baseUrl ?>images/FYU-LOGO.jpg" alt="Fangak Youth Union Logo" class="w-full h-full object-cover">
                </div>
                <div class="leading-tight">
                    <div class="font-serif font-bold text-lg md:text-xl text-white tracking-wide group-hover:text-fyu-gold transition-colors duration-300">
                        Fangak Youth Association
                    </div>
                    <div class="text-[10px] tracking-[0.22em] text-fyu-gold/90 uppercase font-medium">
                        Unity & Progress
                    </div>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center gap-8 text-[15px]" aria-label="Main Navigation">
                <?php foreach($navItems as $url => $label): ?>
                    <a href="<?= $baseUrl . $url ?>" 
                       class="relative py-2 font-medium transition-colors duration-300 <?= navActive($url, $currentPage) ?> after:absolute after:bottom-0 after:left-0 after:h-[2px] after:bg-fyu-gold after:transition-all after:duration-300">
                        <?= $label ?>
                    </a>
                <?php endforeach; ?>

                <!-- User Session Action Button -->
                <div class="pl-4 border-l border-white/15 flex items-center gap-3">
                    <?php if (!empty($_SESSION['user_id'])): ?>
                        <a href="<?= $baseUrl ?>member_dashboard.php" 
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-fyu-gold hover:text-fyu-darker text-white border border-white/20 hover:border-fyu-gold rounded-full font-semibold transition-all duration-300 text-sm shadow-sm">
                            <i class="fa-solid fa-gauge-high text-xs"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="<?= $baseUrl ?>logout.php" title="Sign Out" class="text-gray-300 hover:text-red-400 p-2 transition-colors">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </a>
                    <?php else: ?>
                        <a href="<?= $baseUrl ?>register.php" 
                           class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-fyu-gold to-amber-500 hover:from-amber-400 hover:to-fyu-gold text-fyu-darker rounded-full font-bold text-sm shadow-md shadow-fyu-gold/20 hover:shadow-fyu-gold/40 hover:-translate-y-0.5 transition-all duration-300">
                            <span>Register</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </nav>

            <!-- Mobile Hamburger Trigger -->
            <div class="lg:hidden flex items-center gap-3">
                <button id="menuBtn" class="p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-fyu-gold/50" aria-label="Toggle Navigation Menu" aria-expanded="false">
                    <div class="hamburger">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>
            </div>

        </div>
    </div>
</header>

<!-- Mobile Menu Drawer Overlay -->
<div id="mobileOverlay" class="fixed inset-0 bg-fyu-darker/80 backdrop-blur-md z-[60] opacity-0 pointer-events-none transition-opacity duration-300"></div>

<!-- Mobile Off-Canvas Drawer -->
<aside id="mobileMenu" class="fixed right-0 top-0 h-full w-[300px] bg-fyu-dark shadow-2xl z-[65] transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col border-l border-white/10" aria-label="Mobile Navigation">
    
    <!-- Drawer Header -->
    <div class="flex items-center justify-between p-6 border-b border-white/10 bg-fyu-darker/50">
        <div class="flex items-center gap-3">
            <img src="<?= $baseUrl ?>images/FYU-LOGO.jpg" alt="FYU Logo" class="w-8 h-8 rounded-full border border-fyu-gold">
            <span class="font-serif font-bold text-lg text-white">Navigation</span>
        </div>
        <button id="closeDrawerBtn" class="text-gray-400 hover:text-white p-1 text-lg">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <!-- Drawer Links -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <?php foreach($navItems as $url => $label): ?>
            <a href="<?= $baseUrl . $url ?>" class="block py-3 rounded-lg text-base font-medium transition-all duration-200 <?= navActiveMobile($url, $currentPage) ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Drawer Footer / Auth Actions -->
    <div class="p-6 border-t border-white/10 bg-fyu-darker/80">
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="<?= $baseUrl ?>member_dashboard.php" class="flex items-center justify-center gap-2 w-full py-3 bg-white/10 text-white rounded-xl mb-3 font-semibold hover:bg-white/20 transition">
                <i class="fa-solid fa-gauge-high"></i>
                Dashboard
            </a>
            <a href="<?= $baseUrl ?>logout.php" class="flex items-center justify-center gap-2 w-full py-3 border border-red-500/40 text-red-400 rounded-xl font-medium hover:bg-red-500/10 transition">
                <i class="fa-solid fa-right-from-bracket"></i>
                Logout
            </a>
        <?php else: ?>
            <a href="<?= $baseUrl ?>register.php" class="flex items-center justify-center gap-2 w-full py-3 bg-fyu-gold text-fyu-darker font-bold rounded-xl hover:bg-amber-400 transition shadow-lg shadow-fyu-gold/20">
                <span>Register Account</span>
                <i class="fa-solid fa-user-plus text-xs"></i>
            </a>
        <?php endif; ?>
    </div>
</aside>

<!-- Header & Menu Script -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const menuBtn = document.getElementById('menuBtn');
        const closeDrawerBtn = document.getElementById('closeDrawerBtn');
        const overlay = document.getElementById('mobileOverlay');
        const mobileMenu = document.getElementById('mobileMenu');
        const hamburger = menuBtn.querySelector('.hamburger');
        let isMenuOpen = false;

        function toggleMenu() {
            isMenuOpen = !isMenuOpen;
            
            hamburger.classList.toggle('open', isMenuOpen);
            menuBtn.setAttribute('aria-expanded', isMenuOpen);
            
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

        menuBtn.addEventListener('click', toggleMenu);
        closeDrawerBtn.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', toggleMenu);

        // Escape Key Listener
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isMenuOpen) {
                toggleMenu();
            }
        });
    });
</script>