<?php
// about.php
$pageTitle = "About - Fangak Youth Union";
// Adjust this path if your header location is different
include_once __DIR__ . "/../app/views/layouts/header.php";

// Fallback for baseUrl if not set in header
if(!isset($baseUrl)) $baseUrl = '/public/';

// --- DATA: CURRENT LEADERS ---
// --- DATA: CURRENT LEADERS ---
$current_leaders = [

    [
        "name" => "Mawich Duoth Gatluak",
        "role" => "Chairman",
        "img"  => "images/mawich.jpg",
        "desc" => "Leads FYU’s strategic vision and direction.",
        "bio"  => "Mawich is a passionate leader with strong experience in community organizing and youth empowerment. He drives FYU’s strategic direction, partnerships, and institutional growth."
    ],

    [
        "name" => "Tap Kuol Khor",
        "role" => "Secretary",
        "img"  => "images/tap.jpg",
        "desc" => "Oversees communication and documentation.",
        "bio"  => "Tap ensures smooth communication across teams, coordinates meetings and events, and maintains accurate records of FYU’s activities and decisions."
    ],

    [
        "name" => "Mayiel Bol Deng",
        "role" => "Speaker",
        "img"  => "assets/images/mayiel.jpg",
        "desc" => "Facilitates dialogues and public engagement.",
        "bio"  => "Mayiel is an active youth advocate who leads discussions, moderates forums, and promotes constructive dialogue among youth and community stakeholders."
    ],

    [
        "name" => "Nyaluit Jany",
        "role" => "Project Coordinator & Advisor on Gender Affairs",
        "img"  => "assets/images/nyaluit.jpg",
        "desc" => "Coordinates field programs and promotes gender inclusion.",
        "bio"  => "Nyaluit leads youth empowerment initiatives, coordinates field-level program delivery, and serves as Advisor on Gender Affairs, ensuring gender equality and inclusive participation across FYU programs."
    ],

    [
        "name" => "Lam Par Malual",
        "role" => "Treasurer",
        "img"  => "assets/images/Lam.jpg",
        "desc" => "Manages finances and financial accountability.",
        "bio"  => "Lam ensures fiscal responsibility, transparent budgeting, and accurate financial reporting across all FYU projects and operations."
    ],

    [
        "name" => "Gatjiok Puok",
        "role" => "Deputy Chairman",
        "img"  => "assets/images/gatjok.jpg",
        "desc" => "Supports leadership and oversees program implementation.",
        "bio"  => "Gatjiok assists the Chairman in leadership responsibilities, oversees program design and monitoring, and ensures activities align with FYU’s mission and impact goals."
    ],

    [
        "name" => "Akoch Guek",
        "role" => "Advisor",
        "img"  => "assets/images/Akoch.jpg",
        "desc" => "Provides strategic guidance and program support.",
        "bio"  => "Akoch brings extensive experience in peacebuilding and youth development, offering strategic advice and technical support to strengthen FYU’s initiatives and partnerships."
    ]

];
// --- DATA: PREVIOUS LEADERS ---
$previous_leaders = [
    ["name"=>"Deng Riek koryom","role"=>"Former Chairman (2011-2013)","img"=>"assets/images/Deng.jpeg","desc"=>"Guided the union through post-conflict recovery.","bio"=>"Led the FYU from 2020 to 2024, focusing on rebuilding community trust and establishing foundational programs in education and health."],
    ["name"=>"Puok Bol Par","role"=>"Former Chariman (2013-2022)","img"=>"assets/images/Puok.jpeg","desc"=>"Established the union's administrative framework.","bio"=>"As the inaugural secretary, she developed the communication channels and administrative protocols that the union relies on today."],
    ["name"=>"Nin Deng Wang","role"=>"Former Chairman (2022-2025)","img"=>"assets/images/NinDeng.jpg","desc"=>"Managed foundational grants and budgets.","bio"=>"Oversaw the union's finances during its critical growth phase, implementing a system of accountability."]
];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Old+Standard+TT:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

<style>
    /* --- CSS VARIABLES & RESET --- */
    :root {
        --primary: #0f5132; 
        --primary-dark: #083621;
        --primary-light: #e8f3ee;
        --accent: #d4a017; 
        --text-main: #2b2b2b;
        --text-muted: #666;
        --bg-body: #fcfcfc;
        --bg-soft: #f4f7f6;
        --card-radius: 16px;
        --font-serif: 'Old Standard TT', serif;
        --font-sans: 'Poppins', sans-serif;
        --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        --transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: var(--font-sans);
        color: var(--text-main);
        background: var(--bg-body);
        line-height: 1.7;
        overflow-x: hidden;
    }

    h1, h2, h3, h4 { font-family: var(--font-serif); color: var(--primary-dark); line-height: 1.2; }
    
    .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
    
    /* --- ANIMATIONS --- */
    .fade-up { opacity: 0; transform: translateY(40px); transition: opacity 0.8s ease, transform 0.8s ease; }
    .fade-up.in-view { opacity: 1; transform: translateY(0); }

    /* --- HERO SECTION --- */
    .hero {
        position: relative;
        height: 60vh;
        min-height: 450px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        background-color: var(--primary-dark);
        background-image: url('<?= $baseUrl ?>images/Ludo.jpeg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed; 
    }
    .hero::after {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(8,54,33,0.9) 0%, rgba(15,81,50,0.7) 100%);
    }
    .hero-content { position: relative; z-index: 2; max-width: 800px; padding: 0 20px; }
    .hero h1 {
        font-size: clamp(2.5rem, 5vw, 4rem);
        color: #fff; margin-bottom: 20px;
        font-weight: 700; letter-spacing: -0.5px;
    }
    .hero p {
        font-size: 1.2rem; color: rgba(255,255,255,0.9); margin-bottom: 35px; font-weight: 300;
    }
    .btn {
        display: inline-flex; align-items: center; gap: 10px;
        padding: 14px 36px; border-radius: 50px;
        font-weight: 500; text-decoration: none; transition: var(--transition);
        font-size: 1rem; border: none; cursor: pointer;
    }
    .btn-primary { background: var(--accent); color: #fff; box-shadow: 0 4px 15px rgba(212, 160, 23, 0.3); }
    .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(212, 160, 23, 0.5); background: #c29215; }
    .btn-outline { border: 2px solid rgba(255,255,255,0.8); color: #fff; margin-left: 12px; background: transparent; }
    .btn-outline:hover { background: #fff; color: var(--primary-dark); }

    /* --- COMMON SECTION STYLING --- */
    .section { padding: 100px 0; }
    .section-title { font-size: 2.8rem; margin-bottom: 20px; text-align: center; position: relative; }
    .section-title::after {
        content: ''; display: block; width: 60px; height: 3px;
        background: var(--accent); margin: 15px auto 0; border-radius: 2px;
    }
    .section-subtitle {
        text-align: center; max-width: 650px; margin: 0 auto 60px;
        color: var(--text-muted); font-size: 1.1rem;
    }

    /* --- STORY SECTION --- */
    .story-grid { display: grid; grid-template-columns: 1fr; gap: 60px; align-items: center; }
    @media(min-width: 900px) { .story-grid { grid-template-columns: 1.1fr 0.9fr; } }
    .story-text p { margin-bottom: 20px; font-size: 1.05rem; }
    .story-text .highlight { 
        display: block; font-size: 1.4rem; font-family: var(--font-serif); 
        color: var(--primary); font-weight: 700; line-height: 1.4; 
        margin-bottom: 30px; border-left: 4px solid var(--accent); 
        padding-left: 24px; background: var(--primary-light); padding: 20px; border-radius: 0 12px 12px 0;
    }
    .story-img-wrapper { position: relative; border-radius: 20px; overflow: hidden; box-shadow: var(--shadow-lg); }
    .story-img-wrapper::before {
        content: ''; position: absolute; inset: 0; border: 2px solid rgba(255,255,255,0.2); z-index: 2; border-radius: 20px;
    }
    .story-img { width: 100%; display: block; transition: transform 0.7s ease; }
    .story-img-wrapper:hover .story-img { transform: scale(1.05); }

    /* --- MODERN TIMELINE --- */
    .timeline-section { background: var(--bg-soft); padding: 80px 0; }
    .timeline { position: relative; max-width: 900px; margin: 0 auto; padding: 40px 0; }
    .timeline::before {
        content: ''; position: absolute; top: 0; bottom: 0; left: 50%;
        width: 4px; background: var(--primary-light); transform: translateX(-50%); border-radius: 4px;
    }
    .timeline-item { position: relative; width: 50%; padding: 0 50px 50px 0; clear: both; }
    .timeline-item:nth-child(odd) { float: left; text-align: right; }
    .timeline-item:nth-child(even) { float: right; text-align: left; padding: 0 0 50px 50px; margin-top: 40px; }
    .timeline-item::after {
        content: ''; position: absolute; top: 0; right: -10px; width: 20px; height: 20px;
        background: var(--accent); border: 4px solid var(--bg-soft); border-radius: 50%;
        box-shadow: 0 0 0 4px var(--primary-light); transition: var(--transition); z-index: 2;
    }
    .timeline-item:nth-child(even)::after { left: -10px; }
    .timeline-item:hover::after { background: var(--primary); transform: scale(1.3); }
    
    .timeline-content { 
        background: #fff; padding: 24px; border-radius: 12px; 
        box-shadow: var(--shadow-sm); transition: var(--transition);
        position: relative;
    }
    .timeline-item:hover .timeline-content { box-shadow: var(--shadow-lg); transform: translateY(-5px); }
    .timeline-year { 
        display: inline-block; font-size: 1.2rem; font-weight: 700; 
        color: var(--primary); margin-bottom: 8px; font-family: var(--font-serif);
    }
    .timeline-desc { color: var(--text-muted); font-size: 0.95rem; }

    /* Timeline Mobile */
    @media(max-width: 768px) {
        .timeline::before { left: 20px; }
        .timeline-item { width: 100%; float: none; text-align: left; padding: 0 0 40px 50px !important; margin-top: 0 !important; }
        .timeline-item::after { left: 10px !important; }
    }

    /* --- TEAM GRID --- */
    .team-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 40px; margin-bottom: 50px;
    }
    .leader-card {
        background: #fff; border-radius: var(--card-radius); overflow: hidden;
        box-shadow: var(--shadow-md); transition: var(--transition); 
        cursor: pointer; position: relative; border: 1px solid #f0f0f0;
    }
    .leader-card:hover { transform: translateY(-10px); box-shadow: var(--shadow-lg); border-color: var(--primary-light); }
    .leader-img-box { height: 300px; overflow: hidden; position: relative; }
    .leader-img-box::after {
        content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 50%;
        background: linear-gradient(to top, rgba(0,0,0,0.5), transparent); opacity: 0; transition: var(--transition);
    }
    .leader-card:hover .leader-img-box::after { opacity: 1; }
    .leader-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease; }
    .leader-card:hover .leader-img { transform: scale(1.08); }
    
    .leader-info { padding: 24px; position: relative; background: #fff; }
    .leader-name { font-size: 1.3rem; font-weight: 600; margin-bottom: 6px; color: var(--primary-dark); }
    .leader-role { 
        display: inline-block; padding: 4px 12px; background: var(--primary-light); 
        color: var(--primary); font-size: 0.85rem; font-weight: 500; 
        border-radius: 20px; margin-bottom: 15px;
    }
    .leader-desc { font-size: 0.95rem; color: var(--text-muted); line-height: 1.6; }

    /* "More" Logic */
    .hidden-initially { display: none; }
    .toggle-btn-container { text-align: center; margin-top: 20px; }

    /* --- PREVIOUS LEADERS (List Style) --- */
    .prev-leaders-section { padding: 80px 0; background: #fff; }
    .prev-leaders-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; }
    .prev-leader-card { 
        display: flex; align-items: center; gap: 20px; background: var(--bg-soft); 
        padding: 20px; border-radius: 12px; cursor: pointer; transition: var(--transition); border: 1px solid transparent;
    }
    .prev-leader-card:hover { background: #fff; box-shadow: var(--shadow-md); border-color: var(--primary-light); transform: translateX(5px); }
    .prev-thumb { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; filter: grayscale(100%); transition: 0.4s; border: 2px solid #ddd; }
    .prev-leader-card:hover .prev-thumb { filter: grayscale(0%); border-color: var(--accent); }
    .prev-info h4 { font-size: 1.15rem; margin-bottom: 4px; font-family: var(--font-sans); }
    .prev-info span { font-size: 0.85rem; color: var(--accent); font-weight: 500; }

    /* --- ENHANCED MODAL --- */
    .modal-backdrop {
        position: fixed; inset: 0; background: rgba(8, 54, 33, 0.4); backdrop-filter: blur(8px);
        z-index: 9999; display: flex; align-items: center; justify-content: center;
        opacity: 0; visibility: hidden; transition: all 0.4s ease;
    }
    .modal-backdrop.active { opacity: 1; visibility: visible; }
    
    .modal-content {
        background: #fff; width: 90%; max-width: 850px; max-height: 90vh; overflow-y: auto;
        border-radius: 24px; padding: 0; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3);
        transform: translateY(40px) scale(0.95); transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: flex; flex-direction: column; position: relative;
    }
    @media(min-width: 768px) { .modal-content { flex-direction: row; } }
    .modal-backdrop.active .modal-content { transform: translateY(0) scale(1); }
    
    .modal-img-col { flex: 2; min-height: 350px; background: #f0f0f0; }
    .modal-img { width: 100%; height: 100%; object-fit: cover; }
    .modal-text-col { flex: 3; padding: 50px 40px; display: flex; flex-direction: column; justify-content: center; }
    
    .modal-close {
        position: absolute; top: 20px; right: 20px; z-index: 10;
        width: 40px; height: 40px; border-radius: 50%; background: #fff; border: 1px solid #eee;
        display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-sm);
        font-size: 1.2rem; color: #333; transition: var(--transition); cursor: pointer;
    }
    .modal-close:hover { background: var(--primary); color: #fff; transform: rotate(90deg); border-color: var(--primary); }

    /* --- TESTIMONIALS --- */
    .testimonials { background: var(--primary-dark); color: #fff; padding: 100px 0; }
    .testimonials .section-title, .testimonials .section-title::after { color: #fff; background: var(--accent); }
    .test-scroll {
        display: flex; gap: 30px; overflow-x: auto; padding: 20px 0 40px;
        scroll-snap-type: x mandatory; scrollbar-width: none;
    }
    .test-scroll::-webkit-scrollbar { display: none; }
    .test-card {
        flex: 0 0 380px; scroll-snap-align: start;
        background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
        padding: 40px; border-radius: 20px; transition: var(--transition);
    }
    .test-card:hover { background: rgba(255,255,255,0.1); transform: translateY(-5px); }
    .test-quote { font-family: var(--font-serif); font-size: 1.25rem; font-style: italic; margin-bottom: 24px; opacity: 0.9; line-height: 1.6; }
    .test-author { font-weight: 600; color: var(--accent); display: flex; align-items: center; gap: 10px; }
    .test-author::before { content: ''; width: 30px; height: 2px; background: var(--accent); display: inline-block; }

    /* --- PARTNERS --- */
    .partners-section { background: var(--bg-soft); }
    .partners-grid { display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 60px; margin-top: 50px; }
    .partner-logo { height: 60px; filter: grayscale(100%) opacity(0.6); transition: var(--transition); }
    .partner-logo:hover { filter: grayscale(0%) opacity(1); transform: scale(1.05); }

</style>

<section class="hero fade-up">
    <div class="hero-content">
        <h1>Building a Resilient Fangak</h1>
        <p>We are a coalition of young leaders dedicated to education, peacebuilding, and cultural revival in South Sudan.</p>
        <div>
            <a href="#leadership" class="btn btn-primary">Meet the Team</a>
            <a href="<?= $baseUrl ?>register.php" class="btn btn-outline">Join the Union</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="story-grid">
            <div class="story-text fade-up">
                <h2 class="section-title" style="text-align: left;">Our Story</h2>
                <span class="highlight">From a small gathering of concerned youth to a cornerstone of community development in Fangak County.</span>
                <p>The Fangak Youth Union (FYU) is a voluntary, non-political organization formed by the youth of Fangak County. We recognized that the youth—often the most affected by conflict and lack of opportunity—must be the agents of change.</p>
                <p>Despite limited external support, we have successfully organized cultural celebrations, inter-county sports tournaments, and critical health outreach programs. Our mission is to unite young people through dialogue, education, and shared responsibility.</p>
            </div>
            <div class="story-img-wrapper fade-up">
                <img src="<?= $baseUrl ?>images/FYU-LOGO.jpg" alt="FYU Activities" class="story-img">
            </div>
        </div>
    </div>
</section>

<section class="timeline-section">
    <div class="container fade-up">
        <h2 class="section-title">Our Journey</h2>
        <p class="section-subtitle">Key milestones that have shaped our impact over the years.</p>
        
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-content">
                    <span class="timeline-year">2011</span>
                    <h4 style="margin-bottom: 8px;">Foundation</h4>
                    <p class="timeline-desc">FYU founded in Fangak County to give youth a unified voice.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <span class="timeline-year">2012</span>
                    <h4 style="margin-bottom: 8px;">First Mobilization</h4>
                    <p class="timeline-desc">Coordinated our first successful community emergency response initiative.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <span class="timeline-year">2022</span>
                    <h4 style="margin-bottom: 8px;">In-Kind Services</h4>
                    <p class="timeline-desc">Emergency flood response activated through grassroots in-kind services.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <span class="timeline-year">2025</span>
                    <h4 style="margin-bottom: 8px;">Major Expansion</h4>
                    <p class="timeline-desc">Launched comprehensive flood emergency and educational recovery programs.</p>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
</section>

<section id="leadership" class="section">
    <div class="container">
        <h2 class="section-title fade-up">Executive Leadership</h2>
        <p class="section-subtitle fade-up">The dedicated team steering FYU towards a brighter future (2025 - Present).</p>
        
        <div class="team-grid fade-up" id="currentTeamGrid">
            <?php foreach($current_leaders as $index => $leader): 
                $hiddenClass = ($index >= 3) ? 'hidden-initially' : '';
            ?>
                <div class="leader-card <?= $hiddenClass ?>" onclick='openModal(<?= json_encode($leader) ?>)'>
                    <div class="leader-img-box">
                        <img src="<?= $baseUrl . $leader['img'] ?>" alt="<?= htmlspecialchars($leader['name']) ?>" class="leader-img">
                    </div>
                    <div class="leader-info">
                        <span class="leader-role"><?= htmlspecialchars($leader['role']) ?></span>
                        <h3 class="leader-name"><?= htmlspecialchars($leader['name']) ?></h3>
                        <p class="leader-desc"><?= htmlspecialchars($leader['desc']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if(count($current_leaders) > 3): ?>
        <div class="toggle-btn-container fade-up">
            <button id="toggleTeamBtn" class="btn btn-outline" style="color: var(--primary); border-color: var(--primary);">
                View Entire Board <i class="fas fa-chevron-down" style="margin-left:8px"></i>
            </button>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="prev-leaders-section">
    <div class="container">
        <h3 class="section-title fade-up" style="font-size: 2.2rem;">Honorary Past Leaders</h3>
        <p class="section-subtitle fade-up">Acknowledging the pioneers who laid our foundation (2012 - 2024).</p>

        <div class="prev-leaders-grid fade-up">
            <?php foreach($previous_leaders as $leader): ?>
                <div class="prev-leader-card" onclick='openModal(<?= json_encode($leader) ?>)'>
                    <img src="<?= $baseUrl . $leader['img'] ?>" alt="<?= htmlspecialchars($leader['name']) ?>" class="prev-thumb">
                    <div class="prev-info">
                        <h4><?= htmlspecialchars($leader['name']) ?></h4>
                        <span><?= htmlspecialchars($leader['role']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="testimonials">
    <div class="container fade-up">
        <h2 class="section-title">Voices of Impact</h2>
        <p class="section-subtitle" style="color: rgba(255,255,255,0.7);">Real stories from the communities and youth we serve.</p>
        
        <div class="test-scroll">
            <div class="test-card">
                <p class="test-quote">"FYU has transformed my life by giving me the chance to participate in community projects and grow as a leader."</p>
                <p class="test-author">Mawien G.</p>
            </div>
            <div class="test-card">
                <p class="test-quote">"The programs organized by FYU are both inspiring and impactful for the youth. We finally have a voice."</p>
                <p class="test-author">Nyachot T.</p>
            </div>
            <div class="test-card">
                <p class="test-quote">"I learned practical business skills in the 2022 bootcamp that helped me start a small shop in my village."</p>
                <p class="test-author">Rata P.</p>
            </div>
            <div class="test-card">
                <p class="test-quote">"Transparency and unity are what define this union. It is a home for all of us."</p>
                <p class="test-author">Deng N.</p>
            </div>
        </div>
    </div>
</section>

<section class="section partners-section">
    <div class="container text-center fade-up">
        <h2 class="section-title">Our Partners</h2>
        <p class="section-subtitle">Collaborating across borders for a sustainable future.</p>
        
        <div class="partners-grid">
            <img src="<?= $baseUrl ?>images/partner1.png" class="partner-logo" alt="Partner">
            <img src="<?= $baseUrl ?>images/partner2.png" class="partner-logo" alt="Partner">
            <img src="<?= $baseUrl ?>images/partner3.png" class="partner-logo" alt="Partner">
        </div>
        <div style="margin-top: 60px;">
            <a href="<?= $baseUrl ?>partner-register.php" class="btn btn-primary">Become a Partner</a>
        </div>
    </div>
</section>

<div id="leaderModal" class="modal-backdrop">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal()" aria-label="Close Modal">
            <i class="fas fa-times"></i>
        </button>
        <div class="modal-img-col">
            <img id="mImg" src="" alt="Leader Photo" class="modal-img">
        </div>
        <div class="modal-text-col">
            <span id="mRole" style="display:inline-block; padding: 6px 14px; background: var(--primary-light); color: var(--primary); border-radius: 20px; font-weight: 600; font-size: 0.9rem; margin-bottom: 15px; width: fit-content;"></span>
            <h2 id="mName" style="margin-bottom: 20px; font-size: 2.2rem; font-family: var(--font-sans);"></h2>
            <p id="mBio" style="color: var(--text-muted); font-size: 1.05rem;"></p>
        </div>
    </div>
</div>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Intersection Observer for Fade-Up Animations
    const observerOptions = { threshold: 0.1, rootMargin: "0px 0px -50px 0px" };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
                observer.unobserve(entry.target); // Run once
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));

    // 2. Show More/Less Team Logic
    const toggleBtn = document.getElementById('toggleTeamBtn');
    if(toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const hiddenItems = document.querySelectorAll('.hidden-initially');
            const isExpanded = this.getAttribute('data-expanded') === 'true';

            if(!isExpanded) {
                hiddenItems.forEach(item => {
                    item.style.display = 'block';
                    // Small delay to trigger CSS layout recalculation for smooth rendering
                    setTimeout(() => { item.style.opacity = '1'; }, 10);
                });
                this.innerHTML = 'View Less <i class="fas fa-chevron-up" style="margin-left:8px"></i>';
                this.setAttribute('data-expanded', 'true');
            } else {
                hiddenItems.forEach(item => {
                    item.style.display = 'none';
                    item.style.opacity = '0';
                });
                this.innerHTML = 'View Entire Board <i class="fas fa-chevron-down" style="margin-left:8px"></i>';
                this.setAttribute('data-expanded', 'false');
                document.getElementById('leadership').scrollIntoView({behavior: 'smooth', block: 'start'});
            }
        });
    }
});

// 3. Robust Modal Logic
const modal = document.getElementById('leaderModal');
const mImg = document.getElementById('mImg');
const mName = document.getElementById('mName');
const mRole = document.getElementById('mRole');
const mBio = document.getElementById('mBio');

function openModal(data) {
    // Populate data
    mImg.src = "<?= $baseUrl ?>" + data.img;
    mName.textContent = data.name;
    mRole.textContent = data.role;
    mBio.textContent = data.bio;
    
    // Show Modal
    modal.classList.add('active');
    document.body.style.overflow = 'hidden'; // Lock background scrolling
}

function closeModal() {
    modal.classList.remove('active');
    document.body.style.overflow = ''; // Restore background scrolling
    
    // Clear image src after transition to prevent flicker on next open
    setTimeout(() => {
        if(!modal.classList.contains('active')) mImg.src = "";
    }, 400); 
}

// Close on background click
modal.addEventListener('click', (e) => {
    if(e.target === modal) closeModal();
});

// Close on Escape key
document.addEventListener('keydown', (e) => {
    if(e.key === 'Escape' && modal.classList.contains('active')) {
        closeModal();
    }
});
</script>