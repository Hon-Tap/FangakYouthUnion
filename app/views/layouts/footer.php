<?php
// footer.php - Modern, Professional Footer Component
// This file assumes $baseUrl is defined in the calling scope, or uses a fallback.
$baseUrl = $baseUrl ?? "/FangakYouthUnion/public/";
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
    /* --- FOOTER DESIGN SYSTEM --- */
    :root {
        /* Reusing Deep Green and Gold from Project/About pages */
        --primary-dark: #0f5132;        /* Deep Official Green */
        --primary-light: #146c43;
        --accent-gold: #d4a017;         /* Gold/Bronze */
        --footer-bg: #1a1a1a;           /* Darker, professional base */
        --footer-text-light: #e0e0e0;
        --footer-text-muted: #999;
        --footer-border: #333;
        --radius-sm: 8px;
        --font-sans: 'Inter', sans-serif;
    }

    .fyu-footer {
        background-color: var(--footer-bg);
        color: var(--footer-text-light);
        padding: 80px 24px 20px;
        font-family: var(--font-sans);
        line-height: 1.6;
        border-top: 4px solid var(--primary-dark); /* Subtle green border accent */
    }

    .fyu-footer-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1.5fr; /* About wider than others */
        max-width: 1280px;
        margin: 0 auto;
        gap: 40px;
        padding-bottom: 40px;
    }

    /* --- LOGO & ABOUT --- */
    .footer-brand {
        margin-bottom: 20px;
    }

    .footer-logo {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: var(--footer-text-light);
        font-weight: 700;
        font-size: 1.5rem;
        transition: color 0.3s;
    }

    .footer-logo img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 2px solid var(--accent-gold);
    }

    .footer-brand p {
        color: var(--footer-text-muted);
        margin-top: 15px;
        font-size: 0.95rem;
    }

    /* --- HEADINGS & LINKS --- */
    .fyu-footer h3 {
        font-size: 1.15rem;
        color: var(--accent-gold); /* Gold accent for headings */
        margin-bottom: 20px;
        font-weight: 600;
        border-left: 3px solid var(--primary-dark);
        padding-left: 10px;
    }

    .fyu-footer ul {
        list-style: none;
        padding: 0;
    }

    .fyu-footer ul li {
        margin-bottom: 12px;
    }

    .fyu-footer ul li a {
        color: var(--footer-text-light);
        text-decoration: none;
        font-size: 0.95rem;
        transition: color 0.3s, padding-left 0.3s;
        display: inline-block;
    }

    .fyu-footer ul li a:hover {
        color: var(--accent-gold);
        padding-left: 5px;
    }

    /* --- CONTACT INFO --- */
    .contact-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 15px;
        gap: 12px;
        font-size: 0.95rem;
        color: var(--footer-text-light);
    }

    .contact-item i {
        color: var(--primary-light);
        font-size: 1.2rem;
        padding-top: 3px;
    }

    .contact-item a {
        color: var(--footer-text-light);
    }
    .contact-item a:hover {
        color: var(--accent-gold);
    }

    /* --- SOCIAL ICONS (The "Pro" Look) --- */
    .footer-social-links {
        display: flex;
        gap: 15px;
        margin-top: 25px;
    }

    .footer-social-links a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: var(--radius-sm);
        background: var(--primary-dark); /* Solid primary color background */
        color: var(--footer-text-light);
        font-size: 1.1rem;
        transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94); /* Smooth interaction */
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    .footer-social-links a:hover {
        background: var(--accent-gold); /* Gold hover effect */
        color: var(--footer-bg); /* Dark text on gold */
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 8px 16px rgba(212, 160, 23, 0.4);
    }

    /* --- COPYRIGHT BAR --- */
    .footer-bottom {
        padding-top: 20px;
        border-top: 1px solid var(--footer-border);
        text-align: center;
        font-size: 0.8rem;
        color: var(--footer-text-muted);
        margin-top: 40px;
    }

    /* --- RESPONSIVENESS --- */
    @media (max-width: 1024px) {
        .fyu-footer-grid {
            grid-template-columns: 1fr 1fr; /* 2 columns tablet */
        }
        .footer-brand { grid-column: 1 / -1; } /* About spans full width */
    }

    @media (max-width: 600px) {
        .fyu-footer-grid {
            grid-template-columns: 1fr; /* 1 column mobile */
            text-align: center;
        }
        .fyu-footer h3 {
             border-left: none; 
             padding-left: 0;
             border-bottom: 2px solid var(--primary-dark);
             padding-bottom: 5px;
             display: inline-block;
        }
        .footer-logo { justify-content: center; }
        .footer-brand p { text-align: center; }
        .contact-item { justify-content: center; align-items: center; }
        .footer-social-links { justify-content: center; }
    }
</style>

<footer class="fyu-footer">
    <div class="fyu-footer-grid">

        <div class="footer-brand">
            <a href="<?= $baseUrl ?>index.php" class="footer-logo">
                <img src="<?= $baseUrl ?>images/FYU-LOGO.jpg" alt="FYU Logo">
                <span>Fangak Youth Union</span>
            </a>
            <p>
                <b>Empowering youth</b>, fostering innovation, and building a stronger, resilient community across Fangak County through targeted development projects.
            </p>
        </div>

        <div class="footer-links">
            <h3>Quick Navigation</h3>
            <ul>
                <li><a href="<?= $baseUrl ?>index.php"><i class="fa-solid fa-house-chimney" style="margin-right:5px;"></i> Home</a></li>
                <li><a href="<?= $baseUrl ?>about.php"><i class="fa-solid fa-people-group" style="margin-right:5px;"></i> About Us</a></li>
                <li><a href="<?= $baseUrl ?>project.php"><i class="fa-solid fa-lightbulb" style="margin-right:5px;"></i> Our Projects</a></li>
                <li><a href="<?= $baseUrl ?>blog.php"><i class="fa-solid fa-pen-to-square" style="margin-right:5px;"></i> News & Blog</a></li>
                <li><a href="<?= $baseUrl ?>contact.php"><i class="fa-solid fa-handshake" style="margin-right:5px;"></i> Get Involved</a></li>
                <?php if (!empty($_SESSION['user'])): ?>
                    <li><a href="<?= $baseUrl ?>member_dashboard.php"><i class="fa-solid fa-chart-line" style="margin-right:5px;"></i> Dashboard</a></li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="footer-contact">
            <h3>Reach Out</h3>
            
            <div class="contact-item">
                <i class="fa-solid fa-location-dot"></i> 
                <span>Juba, South Sudan<br> (Field Ops in Fangak)</span>
            </div>
            
            <div class="contact-item">
                <i class="fa-solid fa-envelope"></i> 
                <a href="mailto:info@fangakyouth.org">info@fangakyouth.org</a>
            </div>
            
            <div class="contact-item">
                <i class="fa-solid fa-phone"></i> 
                <a href="tel:+211912345678">+211 912345678</a>
            </div>
            
            <div class="footer-social-links">
                <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="#" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        &copy; <?= date("Y"); ?> Fangak Youth Union. All Rights Reserved. | <a href="<?= $baseUrl ?>privacy.php" style="color:var(--footer-text-muted); text-decoration:underline;">Privacy Policy</a>
    </div>
</footer>

<script>
    // Advanced Footer Fade-in on Scroll (A subtle UI/UX enhancement)
    document.addEventListener("DOMContentLoaded", () => {
        const footer = document.querySelector('.fyu-footer');
        // Initial state for animation
        footer.style.opacity = 0;
        footer.style.transform = 'translateY(50px)';
        footer.style.transition = 'opacity 0.8s ease-out, transform 0.8s ease-out';
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if(entry.isIntersecting){
                    footer.style.opacity = 1;
                    footer.style.transform = 'translateY(0)';
                    observer.unobserve(footer); // Stop observing once visible
                }
            });
        }, { threshold: 0.1 }); // Trigger when 10% of footer is visible
        
        observer.observe(footer);
    });
</script>