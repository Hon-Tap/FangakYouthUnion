<?php
// header.php - Modern & Responsive Navigation Component

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Page title
$pageTitle = $pageTitle ?? "Fangak Youth Union";

// Dynamic Base URL Detection
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$projectFolder = "/FangakYouthUnion/public/";
$baseUrl = $protocol . $host . $projectFolder;

// Helper function for active link detection
$current_page = basename($_SERVER['PHP_SELF']);
function is_active($page, $current) {
    return $current === $page ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


    <link rel="stylesheet" href="<?= $baseUrl ?>css/style.css">

    <style>
        /* --- DESIGN TOKENS --- */
        :root {
            --color-primary: #0f5132;           /* Deep Official Green */
            --color-accent: #d4a017;            /* Subtle Gold/Yellow */
            --color-light: #ffffff;
            --color-dark: #1a1a1a;
            --navbar-height: 70px;
            --navbar-shrink-height: 55px;
            --transition-speed: 0.4s;
            --radius-btn: 25px;
        }

        /* Global Reset */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', var(--font-sans);
            line-height: 1.6;
            color: var(--color-dark);
            background: #f9f9f9;
            padding-top: var(--navbar-height); /* Ensure content starts below the fixed navbar */
        }

        /* --- NAVBAR STYLING --- */
        .navbar {
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            /* Initial state: Transparent background over hero section (assuming) */
            background: rgba(15, 81, 50, 0.9); /* Semi-transparent Green */
            backdrop-filter: blur(5px);
            transition: all var(--transition-speed) ease-in-out;
            box-shadow: 0 0 0 rgba(0,0,0,0);
        }
        
        /* Shrink State: Solid color, smaller, more pronounced shadow */
        .navbar.shrink {
            background: var(--color-light); /* Solid White on scroll */
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .navbar.shrink .logo-text, .navbar.shrink .menu-toggle {
            color: var(--color-dark);
        }
        .navbar.shrink .nav-links a {
             color: var(--color-dark); /* Dark links on white background */
        }
        .navbar.shrink .nav-links a:hover {
             color: var(--color-primary);
        }


        .navbar .container {
            width: 90%;
            max-width: 1280px;
            margin: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: var(--navbar-height);
            padding: 0;
            transition: height var(--transition-speed);
        }
        .navbar.shrink .container {
            height: var(--navbar-shrink-height);
        }

        /* Logo Styling */
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .logo img { 
            height: 50px; 
            width: 50px; 
            border-radius: 50%; 
            transition: 0.3s; 
        }
        .navbar.shrink .logo img { 
            height: 35px; 
            width: 35px; 
            border: 2px solid var(--color-primary); /* Add border on shrink */
        }
        .logo-text {
            color: var(--color-light);
            font-weight: 700;
            font-size: 1.3rem;
            transition: color var(--transition-speed);
        }

        /* Desktop Navigation Links */
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        .nav-links a {
            color: var(--color-light); /* Light text on dark initial bar */
            text-decoration: none;
            font-weight: 500;
            position: relative;
            padding: 5px 0;
            transition: color var(--transition-speed);
        }
        
        /* Active/Hover Underline Effect */
        .nav-links a::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0%;
            height: 3px;
            background: var(--color-accent); /* Use gold accent for underline */
            border-radius: 2px;
            transition: width var(--transition-speed);
        }
        .nav-links a:hover::after,
        .nav-links a.active::after { 
            width: 100%; 
        }
        .nav-links a:hover,
        .nav-links a.active { 
            color: var(--color-accent); 
        }
        
        /* Button Style (Register/Logout) */
        .nav-links .btn {
            background: var(--color-accent);
            color: var(--color-dark);
            padding: 0.6rem 1.25rem;
            border-radius: var(--radius-btn);
            font-weight: 600;
            transition: background 0.2s ease, transform 0.2s ease;
            text-decoration: none !important; /* Override underline */
            border: 2px solid transparent;
        }
        .nav-links .btn:hover { 
            background: var(--color-primary); 
            color: var(--color-light);
            transform: translateY(-2px); 
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            border-color: var(--color-accent);
        }
        .nav-links .btn::after {
            content: none; /* Remove underline from button */
        }
        
        /* Mobile Menu Toggle */
        .menu-toggle { 
            display: none; 
            background: none; 
            border: none; 
            font-size: 1.8rem; 
            color: var(--color-light); /* Light on initial bar */
            cursor: pointer; 
            transition: color var(--transition-speed);
        }
        
        /* --- MOBILE NAVIGATION PANEL --- */
        .mobile-nav {
            position: fixed;
            top: 0; right: -300px; /* Start off-screen */
            height: 100vh;
            width: 280px;
            background: var(--color-primary); /* Use primary color for mobile menu */
            display: flex;
            flex-direction: column;
            padding: 30px 20px;
            gap: 1.2rem;
            transition: right 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55); /* Bounce transition */
            z-index: 1050;
            box-shadow: -5px 0 15px rgba(0,0,0,0.3);
        }
        .mobile-nav.show { right: 0; }
        
        .mobile-nav a { 
            color: var(--color-light); 
            font-size: 1.1rem; 
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .mobile-nav a:hover { 
            background: rgba(255, 255, 255, 0.1); 
            color: var(--color-accent);
        }
        .mobile-nav .btn { 
            margin-top: 10px;
            text-align: center; 
            background: var(--color-accent);
            color: var(--color-dark);
        }
        .mobile-nav .btn:hover {
            background: var(--color-light);
        }
        
        .mobile-close { 
            align-self: flex-end; 
            font-size: 2.2rem; 
            cursor: pointer; 
            color: var(--color-light); 
            margin-bottom: 20px;
        }
        
        .overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            display: none;
            z-index: 1040;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .overlay.show { display: block; opacity: 1; }
        
        /* --- MEDIA QUERIES --- */
        @media (max-width: 992px) {
            .nav-links { display: none; }
            .menu-toggle { display: block; }
            .navbar.shrink .logo-text { font-size: 1.1rem; }
        }
        @media (max-width: 480px) {
            .logo-text { display: none; } /* Hide text on small screens */
            .navbar .container { width: 95%; }
            .mobile-nav { width: 90%; }
        }
    </style>
</head>

<body>
    <header class="navbar" id="navbar">
        <div class="container">
            <a href="<?= $baseUrl ?>index.php" class="logo">
                <img src="<?= $baseUrl ?>images/FYU-LOGO.jpg" alt="FYU Logo">
                <span class="logo-text">Fangak Youth Union-<b>FYU</b></span>
            </a>

            <nav class="nav-links">
                <a href="<?= $baseUrl ?>index.php" class="<?= is_active('index.php', $current_page) ?>">Home</a>
                <a href="<?= $baseUrl ?>about.php" class="<?= is_active('about.php', $current_page) ?>">About Us</a>
                <a href="<?= $baseUrl ?>project.php" class="<?= is_active('project.php', $current_page) ?>">Projects</a>
                <a href="<?= $baseUrl ?>blog.php" class="<?= is_active('blog.php', $current_page) ?>">Blog</a>
                <a href="<?= $baseUrl ?>contact.php" class="<?= is_active('contact.php', $current_page) ?>">Contact</a>

                <?php if (!empty($_SESSION['user'])): ?>
                    <a href="<?= $baseUrl ?>member_dashboard.php" class="btn">Dashboard</a>
                    <a href="<?= $baseUrl ?>logout.php" class="btn">Logout</a>
                <?php else: ?>
                    <a href="<?= $baseUrl ?>register.php" class="btn">Register</a>
                <?php endif; ?>
            </nav>

            <button class="menu-toggle" id="menuToggle"><i class="fa-solid fa-bars"></i></button>
        </div>
    </header>

    <div class="mobile-nav" id="mobileNav">
        <span class="mobile-close" id="mobileClose"><i class="fa-solid fa-xmark"></i></span>
        
        <a href="<?= $baseUrl ?>index.php" class="<?= is_active('index.php', $current_page) ?>">
            <i class="fa-solid fa-house-chimney"></i> Home
        </a>
        <a href="<?= $baseUrl ?>about.php" class="<?= is_active('about.php', $current_page) ?>">
            <i class="fa-solid fa-people-group"></i> About Us
        </a>
        <a href="<?= $baseUrl ?>project.php" class="<?= is_active('project.php', $current_page) ?>">
            <i class="fa-solid fa-lightbulb"></i> Projects
        </a>
        <a href="<?= $baseUrl ?>blog.php" class="<?= is_active('blog.php', $current_page) ?>">
            <i class="fa-solid fa-pen-to-square"></i> Blog
        </a>
        <a href="<?= $baseUrl ?>contact.php" class="<?= is_active('contact.php', $current_page) ?>">
            <i class="fa-solid fa-envelope-open-text"></i> Contact
        </a>

        <hr style="border-top: 1px solid rgba(255,255,255,0.2); margin: 5px 0;">

        <?php if (!empty($_SESSION['user'])): ?>
            <a href="<?= $baseUrl ?>member_dashboard.php" class="btn">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>
            <a href="<?= $baseUrl ?>logout.php" class="btn">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
            </a>
        <?php else: ?>
            <a href="<?= $baseUrl ?>register.php" class="btn">
                <i class="fa-solid fa-user-plus"></i> Register
            </a>
        <?php endif; ?>
    </div>

    <div class="overlay" id="overlay"></div>

    <script>
        const menuToggle = document.getElementById("menuToggle");
        const mobileNav = document.getElementById("mobileNav");
        const mobileClose = document.getElementById("mobileClose");
        const overlay = document.getElementById("overlay");
        const navbar = document.getElementById("navbar");

        // Mobile Menu Functionality
        menuToggle.addEventListener("click", () => {
            mobileNav.classList.add("show");
            overlay.classList.add("show");
            document.body.style.overflow = 'hidden'; // Disable background scrolling
        });

        const closeMenu = () => {
            mobileNav.classList.remove("show");
            overlay.classList.remove("show");
            document.body.style.overflow = ''; // Re-enable scrolling
        };

        mobileClose.addEventListener("click", closeMenu);
        overlay.addEventListener("click", closeMenu);
        
        // Close menu when a link is clicked (to ensure smooth navigation)
        document.querySelectorAll('#mobileNav a').forEach(link => {
            link.addEventListener('click', closeMenu);
        });

        // Navbar Shrink on Scroll (Refined UI/UX)
        window.addEventListener("scroll", () => {
            // Use a higher threshold for a noticeable 'reveal' effect
            if (window.pageYOffset > 100) { 
                navbar.classList.add("shrink");
            } else {
                navbar.classList.remove("shrink");
            }
        });
    </script>