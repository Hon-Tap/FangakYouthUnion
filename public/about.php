<?php
// about.php
$pageTitle = "About Us - Fangak Youth Union";
include_once __DIR__ . "/../app/views/layouts/header.php";

if (!isset($baseUrl)) $baseUrl = '/public/';

// --- DATA: CURRENT LEADERS ---
$current_leaders = [
    [
        "name" => "Mawich Duoth Gatluak",
        "role" => "Chairman",
        "img"  => "images/MawichD.jpg",
        "desc" => "Leads FYA’s strategic vision and overall organizational direction.",
        "bio"  => "Mawich is a passionate leader with extensive experience in community organizing and youth empowerment. He drives FYA’s strategic vision, key partnerships, and long-term institutional growth across South Sudan."
    ],
    [
        "name" => "Tap Kuol Khor",
        "role" => "Secretary General",
        "img"  => "images/tap.jpg",
        "desc" => "Oversees administrative operations, communication, and union records.",
        "bio"  => "<h3>Role Overview</h3><p>The Secretary General serves as the chief administrative officer of the Fangak Youth Association. In this senior leadership position, Tap Kuol Khor is responsible for steering day-to-day operations and ensuring Executive Committee resolutions are effectively executed.</p><h3>Key Responsibilities</h3><ul><li><strong>Executive Management:</strong> Oversee all active programs and resolutions adopted by leadership.</li><li><strong>Record-Keeping:</strong> Serve as custodian of the official records and membership registry.</li></ul><h3>A Message from the Secretary General</h3><p><em>\"My role is fundamentally about service and institutional memory. I am dedicated to maintaining a secretariat that operates with high transparency and professionalism.\"</em></p>"
    ],
    [
        "name" => "Mayiel Bol Deng",
        "role" => "Speaker",
        "img"  => "assets/images/mayiel.jpg",
        "desc" => "Facilitates civic dialogues, moderates forums, and drives engagement.",
        "bio"  => "Mayiel is an active youth advocate who leads public discussions, moderates community forums, and promotes constructive dialogue among youth and local stakeholders."
    ],
    [
        "name" => "Puok Dar Gai",
        "role" => "Advisor for Legal Affairs",
        "img"  => "assets/images/PuokDar.jpeg",
        "desc" => "Provides statutory guidance and ensures full legal compliance.",
        "bio"  => "Puok Dar Gai brings extensive experience in legal matters and community law, advising FYA on compliance strategies and legal awareness initiatives across Fangak County."
    ],
    [
        "name" => "Nyaluit Jany",
        "role" => "Advisor for Peace & Reconciliation",
        "img"  => "assets/images/nyaluit.jpg",
        "desc" => "Guides grassroots peacebuilding and inter-communal reconciliation.",
        "bio"  => "Nyaluit brings years of experience in conflict resolution and social healing, advising FYA on strategic frameworks that foster long-term peace and unity."
    ],
    [
        "name" => "Lam Par Malual",
        "role" => "Treasurer",
        "img"  => "assets/images/Lam.jpg",
        "desc" => "Manages financial accountability, budgets, and reporting.",
        "bio"  => "Lam ensures fiscal integrity, transparent budgeting, and rigorous financial audit systems across all FYA operations and project funds."
    ],
    [
        "name" => "Gatjok Puok",
        "role" => "Deputy Chairman",
        "img"  => "assets/images/gatjok.jpg",
        "desc" => "Supports executive leadership and program execution.",
        "bio"  => "Gatjok assists the Chairman in executive governance, supervises project implementation, and ensures all operations strictly align with the Association’s core mission."
    ],
    [
        "name" => "Akoch Guek",
        "role" => "Advisor for Social Welfare",
        "img"  => "assets/images/Akoch.jpeg",
        "desc" => "Spearheads social welfare strategies and vulnerable youth initiatives.",
        "bio"  => "Akoch specializes in social work and youth development, providing guidance on social safety nets and community intervention programs tailored for vulnerable groups."
    ],
    [
        "name" => "Chris Bamoum Gai",
        "role" => "Secretary for PR & Information",
        "img"  => "assets/images/Chris.jpg",
        "desc" => "Directs public relations, media engagement, and communications.",
        "bio"  => "Chris leads external messaging, media relations, and public campaigns to raise awareness for FYA’s ongoing missions and strategic goals."
    ]
];

// --- DATA: PREVIOUS LEADERS ---
$previous_leaders = [
    [
        "name" => "Deng Riek Koryom",
        "role" => "Former Chairman (2011 - 2013)",
        "img" => "assets/images/Deng.jpeg",
        "desc" => "Guided the association through early post-conflict recovery.",
        "bio" => "Pioneered foundational youth engagement programs and established community trust during crucial recovery years."
    ],
    [
        "name" => "Puok Bol Par",
        "role" => "Former Chairman (2013 - 2022)",
        "img" => "assets/images/Puok.jpeg",
        "desc" => "Built the initial administrative framework and structure.",
        "bio" => "Established core administrative protocols, formal membership structures, and long-term operating frameworks."
    ],
    [
        "name" => "Nin Deng Wang",
        "role" => "Former Chairman (2022 - 2025)",
        "img" => "assets/images/NinDeng.jpg",
        "desc" => "Managed key institutional growth and grant expansion.",
        "bio" => "Expanded organizational reach, implemented strict fiscal accountability measures, and secured key community program grants."
    ]
];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">

<style>
    :root {
        --primary: #064e3b;
        --primary-light: #059669;
        --primary-subtle: #ecfdf5;
        --accent: #d97706;
        --accent-hover: #b45309;
        --dark-bg: #032d23;
        --text-heading: #0f172a;
        --text-body: #475569;
        --text-muted: #64748b;
        --bg-main: #f8fafc;
        --bg-card: #ffffff;
        --border-color: #e2e8f0;
        --radius-lg: 20px;
        --radius-md: 12px;
        --shadow-sm: 0 2px 4px rgba(0,0,0,0.02);
        --shadow-md: 0 10px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.01);
        --shadow-lg: 0 20px 30px -10px rgba(0,0,0,0.08);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --font-serif: 'Playfair Display', Georgia, serif;
        --font-sans: 'Plus Jakarta Sans', -apple-system, sans-serif;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    
    body {
        font-family: var(--font-sans);
        color: var(--text-body);
        background: var(--bg-main);
        line-height: 1.6;
        overflow-x: hidden;
    }

    h1, h2, h3, h4 {
        color: var(--text-heading);
        font-weight: 700;
        line-height: 1.2;
    }

    .serif-title { font-family: var(--font-serif); }
    .container { max-width: 1240px; margin: 0 auto; padding: 0 24px; }

    /* Fade Animations */
    .fade-up {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.7s ease-out, transform 0.7s ease-out;
    }
    .fade-up.in-view {
        opacity: 1;
        transform: translateY(0);
    }

    /* --- HERO SECTION --- */
    .hero {
        position: relative;
        padding: 140px 0 100px;
        background-color: var(--dark-bg);
        background-image: radial-gradient(circle at 80% 20%, rgba(5, 150, 105, 0.15) 0%, transparent 40%),
                          url('<?= $baseUrl ?>images/FishingNets.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        color: #fff;
        text-align: center;
    }
    .hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(3, 45, 35, 0.88) 0%, rgba(3, 45, 35, 0.95) 100%);
    }
    .hero-container { position: relative; z-index: 2; max-width: 850px; margin: 0 auto; }
    .badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 16px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #6ee7b7;
        margin-bottom: 24px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .hero h1 {
        font-size: clamp(2.5rem, 5vw, 4.2rem);
        color: #ffffff;
        margin-bottom: 24px;
        letter-spacing: -1px;
    }
    .hero p {
        font-size: 1.2rem;
        color: rgba(255, 255, 255, 0.85);
        margin-bottom: 40px;
        font-weight: 400;
    }
    .btn-group { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 14px 32px;
        border-radius: 50px;
        font-size: 0.95rem;
        font-weight: 600;
        text-decoration: none;
        transition: var(--transition);
        cursor: pointer;
        border: none;
    }
    .btn-primary {
        background: var(--accent);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(217, 119, 6, 0.4);
    }
    .btn-primary:hover {
        background: var(--accent-hover);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(217, 119, 6, 0.6);
    }
    .btn-secondary {
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.25);
    }
    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
    }

    /* --- STATS OVERLAY --- */
    .stats-bar {
        margin-top: -50px;
        position: relative;
        z-index: 10;
    }
    .stats-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        padding: 30px 40px;
        box-shadow: var(--shadow-lg);
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 30px;
        border: 1px solid var(--border-color);
        text-align: center;
    }
    .stat-item h3 {
        font-size: 2.2rem;
        color: var(--primary);
        font-weight: 800;
        margin-bottom: 4px;
    }
    .stat-item p {
        font-size: 0.9rem;
        color: var(--text-muted);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* --- SECTION HEADERS --- */
    .section { padding: 90px 0; }
    .section-header { text-align: center; max-width: 650px; margin: 0 auto 60px; }
    .section-tag {
        color: var(--primary-light);
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        display: block;
        margin-bottom: 10px;
    }
    .section-title {
        font-size: 2.4rem;
        margin-bottom: 16px;
        letter-spacing: -0.5px;
    }
    .section-subtitle {
        color: var(--text-muted);
        font-size: 1.05rem;
    }

    /* --- PILLARS --- */
    .pillars-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 30px;
    }
    .pillar-card {
        background: var(--bg-card);
        padding: 40px 32px;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }
    .pillar-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 4px;
        background: transparent;
        transition: var(--transition);
    }
    .pillar-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-lg);
        border-color: transparent;
    }
    .pillar-card:hover::before { background: var(--primary-light); }
    .pillar-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: var(--primary-subtle);
        color: var(--primary-light);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        margin-bottom: 24px;
        transition: var(--transition);
    }
    .pillar-card:hover .pillar-icon {
        background: var(--primary-light);
        color: #ffffff;
    }
    .pillar-title { font-size: 1.35rem; margin-bottom: 12px; }
    .pillar-desc { color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; }

    /* --- STORY SECTION --- */
    .story-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        overflow: hidden;
        box-shadow: var(--shadow-md);
    }
    .story-grid {
        display: grid;
        grid-template-columns: 1fr;
        align-items: center;
    }
    @media(min-width: 992px) {
        .story-grid { grid-template-columns: 1.1fr 0.9fr; }
    }
    .story-content { padding: 60px 50px; }
    .story-highlight {
        font-size: 1.25rem;
        color: var(--primary);
        font-weight: 600;
        line-height: 1.5;
        margin-bottom: 24px;
        padding-left: 20px;
        border-left: 4px solid var(--accent);
    }
    .story-content p {
        color: var(--text-body);
        margin-bottom: 18px;
        font-size: 1.02rem;
    }
    .story-img-container {
        height: 100%;
        min-height: 380px;
        position: relative;
    }
    .story-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* --- TIMELINE --- */
    .timeline-wrapper { position: relative; max-width: 860px; margin: 0 auto; }
    .timeline-wrapper::before {
        content: '';
        position: absolute;
        top: 0; bottom: 0; left: 50%;
        width: 2px;
        background: var(--border-color);
        transform: translateX(-50%);
    }
    .timeline-row {
        display: flex;
        justify-content: flex-end;
        padding-bottom: 40px;
        position: relative;
        width: 50%;
    }
    .timeline-row:nth-child(odd) {
        align-self: flex-start;
        text-align: right;
        padding-right: 40px;
    }
    .timeline-row:nth-child(even) {
        margin-left: 50%;
        justify-content: flex-start;
        text-align: left;
        padding-left: 40px;
        padding-right: 0;
    }
    .timeline-dot {
        position: absolute;
        top: 20px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: var(--primary-light);
        border: 4px solid #ffffff;
        box-shadow: 0 0 0 3px var(--primary-subtle);
        z-index: 2;
    }
    .timeline-row:nth-child(odd) .timeline-dot { right: -8px; }
    .timeline-row:nth-child(even) .timeline-dot { left: -8px; }
    .timeline-box {
        background: #ffffff;
        padding: 24px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
    }
    .timeline-box:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }
    .timeline-year {
        display: inline-block;
        padding: 2px 10px;
        background: var(--primary-subtle);
        color: var(--primary-light);
        font-weight: 700;
        border-radius: 20px;
        font-size: 0.85rem;
        margin-bottom: 10px;
    }

    @media(max-width: 768px) {
        .timeline-wrapper::before { left: 20px; }
        .timeline-row { width: 100% !important; margin-left: 0 !important; padding-left: 50px !important; text-align: left !important; }
        .timeline-row:nth-child(odd) .timeline-dot,
        .timeline-row:nth-child(even) .timeline-dot { left: 12px; }
    }

    /* --- TEAM CARDS --- */
    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 30px;
    }
    .leader-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        overflow: hidden;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        cursor: pointer;
    }
    .leader-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
    }
    .leader-img-wrapper {
        height: 280px;
        overflow: hidden;
        position: relative;
        background: #f1f5f9;
    }
    .leader-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .leader-card:hover .leader-img { transform: scale(1.05); }
    .leader-details { padding: 24px; }
    .leader-badge {
        display: inline-block;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--primary-light);
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .leader-name { font-size: 1.25rem; margin-bottom: 8px; }
    .leader-desc { color: var(--text-muted); font-size: 0.9rem; line-height: 1.5; }

    .hidden-leader { display: none; }
    .toggle-container { text-align: center; margin-top: 40px; }

    /* --- PAST LEADERS --- */
    .past-leaders-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }
    .past-card {
        display: flex;
        align-items: center;
        gap: 16px;
        background: #ffffff;
        padding: 16px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        cursor: pointer;
        transition: var(--transition);
    }
    .past-card:hover {
        transform: translateX(4px);
        border-color: var(--primary-light);
        box-shadow: var(--shadow-md);
    }
    .past-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        object-fit: cover;
        filter: grayscale(80%);
        transition: var(--transition);
    }
    .past-card:hover .past-avatar { filter: grayscale(0%); }
    .past-info h4 { font-size: 1rem; margin-bottom: 2px; }
    .past-info p { font-size: 0.82rem; color: var(--accent); font-weight: 600; }

    /* --- MODERN MODAL --- */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(8px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: var(--transition);
        padding: 20px;
    }
    .modal-backdrop.active { opacity: 1; visibility: visible; }
    
    .modal-card {
        background: #ffffff;
        width: 100%;
        max-width: 800px;
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-lg);
        display: flex;
        flex-direction: column;
        position: relative;
        transform: scale(0.95) translateY(20px);
        transition: var(--transition);
        max-height: 90vh;
    }
    @media(min-width: 768px) {
        .modal-card { flex-direction: row; }
    }
    .modal-backdrop.active .modal-card {
        transform: scale(1) translateY(0);
    }
    .modal-media {
        flex: 1;
        min-height: 280px;
        background: #f1f5f9;
        position: relative;
    }
    .modal-media img {
        width: 100%; height: 100%; object-fit: cover;
    }
    .modal-body {
        flex: 1.3;
        padding: 40px;
        overflow-y: auto;
    }
    .modal-close-btn {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #ffffff;
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        transition: var(--transition);
    }
    .modal-close-btn:hover { background: var(--bg-main); transform: rotate(90deg); }
    .modal-role-badge {
        display: inline-block;
        padding: 4px 12px;
        background: var(--primary-subtle);
        color: var(--primary-light);
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.8rem;
        margin-bottom: 12px;
    }
    .modal-bio-text h3 { font-size: 1.1rem; margin: 16px 0 8px; }
    .modal-bio-text ul { padding-left: 20px; margin-bottom: 12px; }
    .modal-bio-text p { color: var(--text-body); font-size: 0.95rem; line-height: 1.6; margin-bottom: 12px; }
</style>

<!-- Hero Section -->
<section class="hero fade-up">
    <div class="container hero-container">
        <span class="badge-pill"><i class="fas fa-shield-halved"></i> Official Leadership Portal</span>
        <h1 class="serif-title">Building a Resilient Fangak Youth Community</h1>
        <p>A unified coalition of dedicated leaders championing education, non-violent peacebuilding, civic empowerment, and sustainable community revival in South Sudan.</p>
        <div class="btn-group">
            <a href="#leadership" class="btn btn-primary">Executive Leadership <i class="fas fa-arrow-down"></i></a>
            <a href="<?= $baseUrl ?>register.php" class="btn btn-secondary">Join the Union</a>
        </div>
    </div>
</section>

<!-- Quick Stats Bar -->
<div class="container stats-bar fade-up">
    <div class="stats-card">
        <div class="stat-item">
            <h3>2011</h3>
            <p>Year Established</p>
        </div>
        <div class="stat-item">
            <h3>9+</h3>
            <p>Active Executive Board Members</p>
        </div>
        <div class="stat-item">
            <h3>100%</h3>
            <p>Community Driven Initiatives</p>
        </div>
    </div>
</div>

<!-- Core Pillars -->
<section class="section">
    <div class="container fade-up">
        <div class="section-header">
            <span class="section-tag">Our Strategic Foundation</span>
            <h2 class="section-title serif-title">Core Operating Pillars</h2>
            <p class="section-subtitle">Guiding our collective focus towards long-term developmental growth and community stability.</p>
        </div>

        <div class="pillars-grid">
            <div class="pillar-card">
                <div class="pillar-icon"><i class="fas fa-graduation-cap"></i></div>
                <h3 class="pillar-title">Education & Vocational Skills</h3>
                <p class="pillar-desc">Unlocking opportunity through supported access to learning environments, academic mentoring, and high-impact skills training.</p>
            </div>
            <div class="pillar-card">
                <div class="pillar-icon"><i class="fas fa-handshake"></i></div>
                <h3 class="pillar-title">Peacebuilding & Social Unity</h3>
                <p class="pillar-desc">Preventing localized conflict through structured inter-communal dialogue, youth sports activities, and civic engagement forums.</p>
            </div>
            <div class="pillar-card">
                <div class="pillar-icon"><i class="fas fa-seedling"></i></div>
                <h3 class="pillar-title">Resilience & Flood Relief</h3>
                <p class="pillar-desc">Deploying rapid community-level responses, emergency aid coordination, and environmental adaptation solutions.</p>
            </div>
        </div>
    </div>
</section>

<!-- Story Section -->
<section class="section" style="background: #ffffff; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
    <div class="container fade-up">
        <div class="story-card">
            <div class="story-grid">
                <div class="story-content">
                    <span class="section-tag">Who We Are</span>
                    <h2 class="section-title serif-title" style="margin-bottom: 20px;">Our Journey & Mandate</h2>
                    <div class="story-highlight">
                        "From a localized youth collective to a pillar of peace and developmental advocacy in Fangak County."
                    </div>
                    <p>The Fangak Youth Association (FYA) is a non-political, voluntary institution established to represent the aspirations of young people across Fangak. Recognizing that youth are essential stewards of peace, we organize structured civic interventions, flood relief efforts, and cultural exchanges.</p>
                    <p>Despite persistent environmental challenges, our leadership team continues to forge key partnerships to deliver vital local assistance and uphold communal unity.</p>
                </div>
                <div class="story-img-container">
                    <img src="<?= $baseUrl ?>images/FYA-LOGO.png" alt="Fangak Youth Association" class="story-img">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Timeline Section -->
<section class="section">
    <div class="container fade-up">
        <div class="section-header">
            <span class="section-tag">Milestones</span>
            <h2 class="section-title serif-title">Key Historical Achievements</h2>
            <p class="section-subtitle">Tracking our progress and commitment to community building over time.</p>
        </div>

        <div class="timeline-wrapper">
            <div class="timeline-row">
                <div class="timeline-dot"></div>
                <div class="timeline-box">
                    <span class="timeline-year">2011</span>
                    <h4>Foundation</h4>
                    <p style="font-size:0.9rem; color:var(--text-muted); margin-top:4px;">Union structured to provide youth with a unified advocacy platform.</p>
                </div>
            </div>
            <div class="timeline-row">
                <div class="timeline-dot"></div>
                <div class="timeline-box">
                    <span class="timeline-year">2012</span>
                    <h4>Community Mobilization</h4>
                    <p style="font-size:0.9rem; color:var(--text-muted); margin-top:4px;">Organized our first cross-county peace forums and outreach networks.</p>
                </div>
            </div>
            <div class="timeline-row">
                <div class="timeline-dot"></div>
                <div class="timeline-box">
                    <span class="timeline-year">2022</span>
                    <h4>Emergency Flood Relief</h4>
                    <p style="font-size:0.9rem; color:var(--text-muted); margin-top:4px;">Mobilized emergency volunteer teams for localized flood relief.</p>
                </div>
            </div>
            <div class="timeline-row">
                <div class="timeline-dot"></div>
                <div class="timeline-box">
                    <span class="timeline-year">2025</span>
                    <h4>Program Expansion</h4>
                    <p style="font-size:0.9rem; color:var(--text-muted); margin-top:4px;">Expanded institutional scope to support comprehensive educational projects.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Active Leadership Grid -->
<section id="leadership" class="section" style="background: #ffffff; border-top: 1px solid var(--border-color);">
    <div class="container fade-up">
        <div class="section-header">
            <span class="section-tag">Governance</span>
            <h2 class="section-title serif-title">Executive Committee</h2>
            <p class="section-subtitle">Meet the team driving our current initiatives and strategic vision.</p>
        </div>

        <div class="team-grid" id="currentTeamGrid">
            <?php foreach($current_leaders as $index => $leader): 
                $hiddenClass = ($index >= 3) ? 'hidden-leader' : '';
            ?>
                <div class="leader-card <?= $hiddenClass ?>" onclick='openModal(<?= json_encode($leader) ?>)'>
                    <div class="leader-img-wrapper">
                        <img src="<?= $baseUrl . $leader['img'] ?>" alt="<?= htmlspecialchars($leader['name']) ?>" class="leader-img">
                    </div>
                    <div class="leader-details">
                        <span class="leader-badge"><?= htmlspecialchars($leader['role']) ?></span>
                        <h3 class="leader-name"><?= htmlspecialchars($leader['name']) ?></h3>
                        <p class="leader-desc"><?= htmlspecialchars($leader['desc']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if(count($current_leaders) > 3): ?>
        <div class="toggle-container">
            <button id="toggleTeamBtn" class="btn btn-secondary" style="color: var(--primary); border-color: var(--border-color);">
                View Full Executive Board <i class="fas fa-chevron-down" style="margin-left: 6px;"></i>
            </button>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Past Leaders -->
<section class="section">
    <div class="container fade-up">
        <div class="section-header">
            <span class="section-tag">Legacy</span>
            <h2 class="section-title serif-title">Honorary Former Chairmen</h2>
            <p class="section-subtitle">Acknowledging those who laid the foundation of the union in previous years.</p>
        </div>

        <div class="past-leaders-grid">
            <?php foreach($previous_leaders as $leader): ?>
                <div class="past-card" onclick='openModal(<?= htmlspecialchars(json_encode($leader), ENT_QUOTES, "UTF-8") ?>)'>
                    <img src="<?= $baseUrl . $leader['img'] ?>" alt="<?= htmlspecialchars($leader['name']) ?>" class="past-avatar">
                    <div class="past-info">
                        <h4><?= htmlspecialchars($leader['name']) ?></h4>
                        <p><?= htmlspecialchars($leader['role']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Modal -->
<div id="leaderModal" class="modal-backdrop">
    <div class="modal-card">
        <button class="modal-close-btn" onclick="closeModal()" aria-label="Close modal">
            <i class="fas fa-times"></i>
        </button>
        <div class="modal-media">
            <img id="mImg" src="" alt="Leader Portrait">
        </div>
        <div class="modal-body">
            <span id="mRole" class="modal-role-badge"></span>
            <h2 id="mName" class="serif-title" style="font-size: 1.8rem; margin-bottom: 16px;"></h2>
            <div id="mBio" class="modal-bio-text"></div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Intersection Observer for Smooth Fade-Up
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));

    // 2. Expand/Collapse Executive Team
    const toggleBtn = document.getElementById('toggleTeamBtn');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const hiddenItems = document.querySelectorAll('.hidden-leader');
            const isExpanded = this.getAttribute('data-expanded') === 'true';

            if (!isExpanded) {
                hiddenItems.forEach(item => {
                    item.style.display = 'block';
                    setTimeout(() => { item.style.opacity = '1'; }, 10);
                });
                this.innerHTML = 'Collapse View <i class="fas fa-chevron-up" style="margin-left:6px;"></i>';
                this.setAttribute('data-expanded', 'true');
            } else {
                hiddenItems.forEach(item => {
                    item.style.display = 'none';
                    item.style.opacity = '0';
                });
                this.innerHTML = 'View Full Executive Board <i class="fas fa-chevron-down" style="margin-left:6px;"></i>';
                this.setAttribute('data-expanded', 'false');
                document.getElementById('leadership').scrollIntoView({ behavior: 'smooth' });
            }
        });
    }
});

// 3. Leader Modal Functions
const modal = document.getElementById('leaderModal');
const mImg = document.getElementById('mImg');
const mName = document.getElementById('mName');
const mRole = document.getElementById('mRole');
const mBio = document.getElementById('mBio');

function openModal(data) {
    mImg.src = "<?= $baseUrl ?>" + data.img;
    mName.textContent = data.name;
    mRole.textContent = data.role;
    mBio.innerHTML = data.bio; // Renders unescaped HTML bio safely

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

modal.addEventListener('click', (e) => {
    if (e.target === modal) closeModal();
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal.classList.contains('active')) closeModal();
});
</script>