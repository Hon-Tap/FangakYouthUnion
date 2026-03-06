<?php
// about.php
$pageTitle = "About - Fangak Youth Union";
// Adjust this path if your header location is different
include_once __DIR__ . "/../app/views/layouts/header.php";

// Fallback for baseUrl if not set in header
if(!isset($baseUrl)) $baseUrl = '/public/';

// --- DATA: CURRENT LEADERS ---
$current_leaders = [
    ["name"=>"Mawich Duoth Gatluak","role"=>"Chairman","img"=>"images/mawich.jpg","desc"=>"Leads FYU’s strategic vision.","bio"=>"Mawich is a passionate leader with experience in community organizing and youth empowerment. He drives FYU’s strategic direction and partnerships."],
    ["name"=>"Tap Kuol Khor","role"=>"Secretary","img"=>"images/tap.jpg","desc"=>"Oversees communication.","bio"=>"Tap ensures smooth communication across teams, coordinates events, and documents FYU’s work."],
    ["name"=>"Mayiel Bol Deng","role"=>"Speaker","img"=>"images/farming.jpg","desc"=>"Facilitates dialogues.","bio"=>"Mayiel is an active youth advocate who facilitates dialogues and public engagements."],
    ["name"=>"Nyaluit Jany","role"=>"Project Coordinator","img"=>"images/FYU-LOGO.jpg","desc"=>"Coordinates field programs.","bio"=>"Nyaluak leads initiatives in youth empowerment and coordinates field-level program delivery."],
    ["name"=>"Lam Par Malual","role"=>"Treasurer","img"=>"images/FYU-LOGO.jpg","desc"=>"Manages finances.","bio"=>"Lam ensures fiscal responsibility and transparent reporting across FYU projects."],
    ["name"=>"Gatjiok Puok","role"=>"Deputy Chairman","img"=>"images/FYU-LOGO.jpg","desc"=>"Oversees program design.","bio"=>"Gatjiok manages monitoring & evaluation for FYU programs and ensures impact-focused design."]
];

// --- DATA: PREVIOUS LEADERS ---
$previous_leaders = [
    ["name"=>"Deng Riek koryom","role"=>"Former Chairman (2011-2013)","img"=>"images/FYU-LOGO.jpg","desc"=>"Guided the union through post-conflict recovery.","bio"=>"Led the FYU from 2020 to 2024, focusing on rebuilding community trust and establishing foundational programs in education and health."],
    ["name"=>"Puok Bol Par","role"=>"Former Chariman (2013-2022)","img"=>"images/FYU-LOGO.jpg","desc"=>"Established the union's administrative framework.","bio"=>"As the inaugural secretary, she developed the communication channels and administrative protocols that the union relies on today."],
    ["name"=>"Nin Deng Wang","role"=>"Former Chairman (2022-2025)","img"=>"images/NinDeng.jpg","desc"=>"Managed foundational grants and budgets.","bio"=>"Oversaw the union's finances during its critical growth phase, implementing a system of accountability."]
];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Old+Standard+TT:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

<style>
    /* --- CSS VARIABLES & RESET --- */
    :root {
        --primary: #0f5132; /* Deep Official Green */
        --primary-dark: #083621;
        --accent: #d4a017; /* Gold/Bronze for prestige */
        --text-main: #1a1a1a;
        --text-muted: #555;
        --bg-body: #ffffff;
        --bg-soft: #f8f9fa;
        --card-radius: 12px;
        --font-serif: 'Old Standard TT', serif;
        --font-sans: 'Poppins', sans-serif;
        --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
    .btn-reset { background: none; border: none; cursor: pointer; padding: 0; }
    
    /* --- ANIMATIONS --- */
    .fade-up { opacity: 0; transform: translateY(30px); transition: opacity 0.8s ease, transform 0.8s ease; }
    .fade-up.in-view { opacity: 1; transform: translateY(0); }

    /* --- HERO SECTION --- */
    .hero {
        position: relative;
        height: 70vh;
        min-height: 500px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        background-color: var(--primary-dark);
        /* Simple parallax effect */
        background-image: url('<?= $baseUrl ?>images/Ludo.jpeg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed; 
    }
    .hero::after {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0.4) 0%, rgba(15,81,50,0.85) 100%);
    }
    .hero-content { position: relative; z-index: 2; max-width: 800px; padding: 0 20px; }
    .hero h1 {
        font-size: clamp(2.5rem, 5vw, 4.5rem);
        color: #fff; margin-bottom: 20px;
        font-weight: 700; text-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    .hero p {
        font-size: 1.25rem; opacity: 0.95; margin-bottom: 32px; font-weight: 300;
    }
    .btn {
        display: inline-flex; align-items: center; gap: 10px;
        padding: 14px 32px; border-radius: 50px;
        font-weight: 600; text-decoration: none; transition: var(--transition);
        font-size: 0.95rem; letter-spacing: 0.5px;
    }
    .btn-primary { background: #fff; color: var(--primary); }
    .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
    .btn-outline { border: 1px solid rgba(255,255,255,0.6); color: #fff; margin-left: 12px; }
    .btn-outline:hover { background: rgba(255,255,255,0.1); border-color: #fff; }

    /* --- SECTION STYLING --- */
    .section { padding: 100px 0; }
    .section-title { font-size: 2.5rem; margin-bottom: 16px; text-align: center; }
    .section-subtitle {
        text-align: center; max-width: 600px; margin: 0 auto 60px;
        color: var(--text-muted); font-size: 1.1rem;
    }

    /* --- STORY & TIMELINE --- */
    .story-grid {
        display: grid; grid-template-columns: 1fr; gap: 60px;
    }
    @media(min-width: 900px) { .story-grid { grid-template-columns: 1fr 1fr; align-items: start; } }

    .story-text { font-size: 1.05rem; color: #444; }
    .story-text p { margin-bottom: 24px; }
    .story-text .highlight { font-size: 1.25rem; font-family: var(--font-serif); color: var(--primary); font-weight: 700; line-height: 1.4; margin-bottom: 32px; display: block; border-left: 4px solid var(--accent); padding-left: 20px; }

    /* Modern Timeline */
    .timeline { position: relative; padding-left: 30px; border-left: 2px solid #e0e0e0; margin-top: 40px; }
    .timeline-item { position: relative; margin-bottom: 40px; }
    .timeline-item:last-child { margin-bottom: 0; }
    .timeline-dot {
        position: absolute; left: -36px; top: 0;
        width: 14px; height: 14px; border-radius: 50%;
        background: var(--bg-body); border: 3px solid var(--primary);
        transition: var(--transition);
    }
    .timeline-item:hover .timeline-dot { background: var(--primary); transform: scale(1.3); }
    .timeline-year { font-weight: 700; color: var(--primary); font-size: 1.1rem; display: block; margin-bottom: 4px; }
    .timeline-desc { font-size: 0.95rem; color: var(--text-muted); }

    .story-image-wrapper { position: relative; }
    .story-img {
        width: 100%; border-radius: var(--card-radius);
        box-shadow: var(--shadow-lg); transition: transform 0.5s ease;
    }
    .story-image-wrapper:hover .story-img { transform: scale(1.02); }

    /* --- KPI STATS --- */
    .kpi-section { margin-top: -60px; position: relative; z-index: 10; margin-bottom: 80px; }
    .kpi-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        background: white; border-radius: 20px;
        box-shadow: var(--shadow-lg); overflow: hidden;
    }
    .kpi-card {
        padding: 40px 20px; text-align: center;
        border-right: 1px solid #f0f0f0;
        position: relative; overflow: hidden;
    }
    .kpi-card:last-child { border-right: none; }
    .kpi-number { font-size: 3rem; font-weight: 700; color: var(--primary); font-family: var(--font-sans); display: block; margin-bottom: 8px; }
    .kpi-label { text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; color: var(--text-muted); font-weight: 600; }

    /* --- TEAM GRID --- */
    .team-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px; margin-bottom: 40px;
    }
    .leader-card {
        background: #fff; border-radius: var(--card-radius); overflow: hidden;
        box-shadow: var(--shadow-sm); border: 1px solid #eee;
        transition: var(--transition); cursor: pointer; position: relative;
    }
    .leader-card:hover { transform: translateY(-10px); box-shadow: var(--shadow-lg); }
    .leader-img-box { height: 260px; overflow: hidden; position: relative; }
    .leader-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
    .leader-card:hover .leader-img { transform: scale(1.1); }
    
    .leader-info { padding: 24px; text-align: left; }
    .leader-name { font-size: 1.2rem; font-weight: 600; margin-bottom: 4px; color: var(--text-main); }
    .leader-role { color: var(--primary); font-size: 0.9rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; }
    .leader-desc { font-size: 0.9rem; color: var(--text-muted); line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    /* "More" Logic */
    .hidden-initially { display: none; }
    .toggle-btn-container { text-align: center; margin-top: 20px; }
    
    /* --- PREVIOUS LEADERS (Condensed) --- */
    .prev-leaders-section { background: var(--bg-soft); padding: 80px 0; border-top: 1px solid #eaeaea; }
    .prev-leader-card { display: flex; align-items: center; gap: 20px; background: #fff; padding: 20px; border-radius: var(--card-radius); box-shadow: var(--shadow-sm); cursor: pointer; transition: var(--transition); }
    .prev-leader-card:hover { box-shadow: 0 10px 20px rgba(0,0,0,0.08); transform: translateX(5px); }
    .prev-thumb { width: 70px; height: 70px; border-radius: 50%; object-fit: cover; filter: grayscale(100%); transition: 0.3s; }
    .prev-leader-card:hover .prev-thumb { filter: grayscale(0%); }
    .prev-info h4 { font-size: 1.1rem; margin-bottom: 2px; }
    .prev-info span { font-size: 0.85rem; color: var(--text-muted); }

    /* --- MODAL --- */
    .modal-backdrop {
        position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px);
        z-index: 9999; display: flex; align-items: center; justify-content: center;
        opacity: 0; pointer-events: none; transition: opacity 0.3s ease;
    }
    .modal-backdrop.active { opacity: 1; pointer-events: auto; }
    
    .modal-content {
        background: #fff; width: 90%; max-width: 800px; max-height: 90vh; overflow-y: auto;
        border-radius: 16px; padding: 0; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        transform: translateY(20px) scale(0.95); transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: flex; flex-direction: column;
    }
    @media(min-width: 768px) { .modal-content { flex-direction: row; } }
    
    .modal-backdrop.active .modal-content { transform: translateY(0) scale(1); }
    
    .modal-img-col { flex: 2; min-height: 300px; }
    .modal-img { width: 100%; height: 100%; object-fit: cover; }
    .modal-text-col { flex: 3; padding: 40px; position: relative; }
    
    .modal-close {
        position: absolute; top: 20px; right: 20px;
        width: 36px; height: 36px; border-radius: 50%; background: #f0f0f0;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; color: #333; transition: 0.2s;
    }
    .modal-close:hover { background: #e0e0e0; color: #000; }

    /* --- TESTIMONIALS --- */
    .testimonials { background: var(--primary-dark); color: #fff; padding: 80px 0; }
    .testimonials h2 { color: #fff; }
    .test-scroll {
        display: flex; gap: 24px; overflow-x: auto; padding-bottom: 30px;
        scroll-snap-type: x mandatory; -ms-overflow-style: none; scrollbar-width: none;
    }
    .test-scroll::-webkit-scrollbar { display: none; }
    .test-card {
        flex: 0 0 350px; scroll-snap-align: start;
        background: rgba(255,255,255,0.1); backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.15);
        padding: 30px; border-radius: 16px;
    }
    .test-quote { font-family: var(--font-serif); font-size: 1.2rem; font-style: italic; margin-bottom: 20px; opacity: 0.9; }
    .test-author { font-weight: 600; color: var(--accent); }

    /* --- PARTNERS --- */
    .partners-grid { display: flex; flex-wrap: wrap; justify-content: center; gap: 40px; opacity: 0.6; margin-top: 40px; }
    .partner-logo { height: 50px; filter: grayscale(100%); transition: 0.3s; }
    .partner-logo:hover { filter: grayscale(0%); transform: scale(1.1); opacity: 1; }

    /* Responsive */
    @media(max-width: 768px) {
        .hero h1 { font-size: 2.5rem; }
        .kpi-grid { grid-template-columns: 1fr 1fr; }
        .test-card { flex: 0 0 280px; }
    }
</style>

<section class="hero fade-up">
    <div class="hero-content">
        <h1>Building a Resilient Fangak</h1>
        <p>We are a coalition of young leaders dedicated to education, peacebuilding, and cultural revival in South Sudan.</p>
        <div>
            <a href="#leadership" class="btn btn-primary">Meet the Team</a>
            <a href="<?= $baseUrl ?>joinus.php" class="btn btn-outline">Join the Union</a>
        </div>
    </div>
</section>

<div class="container fade-up">
    <div class="kpi-section">
        <div class="kpi-grid">
            <div class="kpi-card">
                <span class="kpi-number" data-target="1210">0</span>
                <span class="kpi-label">Active Members</span>
            </div>
            <div class="kpi-card">
                <span class="kpi-number" data-target="25">0</span>
                <span class="kpi-label">Projects Completed</span>
            </div>
            <div class="kpi-card">
                <span class="kpi-number" data-target="14">0</span>
                <span class="kpi-label">Years of Service</span>
            </div>
            <div class="kpi-card">
                <span class="kpi-number" data-target="50">0</span>
                <span class="kpi-label">Communities Served</span>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="story-grid">
            <div class="story-text fade-up">
                <h2 class="section-title" style="text-align: left;">Our Story</h2>
                <span class="highlight">From a small gathering of concerned youth to a cornerstone of community development in Fangak County.</span>
                
                <p>The Fangak Youth Union (FYU) is a voluntary, non-political organization formed by the youth of Fangak County. We recognized that the youth—often the most affected by conflict and lack of opportunity—must be the agents of change.</p>
                <p>Despite limited external support, we have successfully organized cultural celebrations, inter-county sports tournaments, and critical health outreach programs. Our mission is to unite young people through dialogue, education, and shared responsibility.</p>
                
                

[Image of timeline graphic showing FYU milestones]


                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <span class="timeline-year">2011</span>
                        <span class="timeline-desc">FYU founded in Fangak County.</span>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <span class="timeline-year">2012</span>
                        <span class="timeline-desc">First community emergency response</span>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <span class="timeline-year">2022</span>
                        <span class="timeline-desc">Emergency flood response through <b>In-kind </b>services</span>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <span class="timeline-year">2025</span>
                        <span class="timeline-desc">Major flood emergency response initiative.</span>
                    </div>
                </div>
            </div>

            <div class="story-image-wrapper fade-up">
                <img src="<?= $baseUrl ?>images/FYU-LOGO.jpg" alt="FYU Activities" class="story-img">
            </div>
        </div>
    </div>
</section>

<section id="leadership" class="section" style="background: #fafafa;">
    <div class="container">
        <h2 class="section-title fade-up">Current Leadership</h2>
        <p class="section-subtitle fade-up">The dedicated executive team steering FYU towards a brighter future (2025 - Present).</p>
        
        <div class="team-grid fade-up" id="currentTeamGrid">
            <?php foreach($current_leaders as $index => $leader): 
                $hiddenClass = ($index >= 3) ? 'hidden-initially' : '';
            ?>
                <div class="leader-card <?= $hiddenClass ?>" 
                     onclick='openModal(<?= json_encode($leader) ?>)'>
                    <div class="leader-img-box">
                        <img src="<?= $baseUrl . $leader['img'] ?>" alt="<?= htmlspecialchars($leader['name']) ?>" class="leader-img">
                    </div>
                    <div class="leader-info">
                        <h3 class="leader-name"><?= htmlspecialchars($leader['name']) ?></h3>
                        <p class="leader-role"><?= htmlspecialchars($leader['role']) ?></p>
                        <p class="leader-desc"><?= htmlspecialchars($leader['desc']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if(count($current_leaders) > 3): ?>
        <div class="toggle-btn-container fade-up">
            <button id="toggleTeamBtn" class="btn btn-primary" style="background: var(--primary); color: white;">
                Show Full Team <i class="fas fa-chevron-down" style="margin-left:8px"></i>
            </button>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="prev-leaders-section">
    <div class="container">
        <h3 class="section-title fade-up" style="font-size: 1.8rem;">Honorary Past Leaders</h3>
        <p class="section-subtitle fade-up">Acknowledging those who laid the foundation (2012 - 2024).</p>

        <div class="team-grid fade-up">
            <?php foreach($previous_leaders as $leader): ?>
                <div class="prev-leader-card" onclick='openModal(<?= json_encode($leader) ?>)'>
                    <img src="<?= $baseUrl . $leader['img'] ?>" alt="" class="prev-thumb">
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
        <p class="section-subtitle" style="color: rgba(255,255,255,0.7);">How FYU is changing lives in the community.</p>
        
        <div class="test-scroll">
            <div class="test-card">
                <p class="test-quote">"FYU has transformed my life by giving me the chance to participate in community projects and grow as a leader."</p>
                <p class="test-author">- Mawien G.</p>
            </div>
            <div class="test-card">
                <p class="test-quote">"The programs organized by FYU are both inspiring and impactful for the youth. We finally have a voice."</p>
                <p class="test-author">- Nyachot T.</p>
            </div>
            <div class="test-card">
                <p class="test-quote">"I learned practical business skills in the 2022 bootcamp that helped me start a small shop in my village."</p>
                <p class="test-author">- Rata P.</p>
            </div>
            <div class="test-card">
                <p class="test-quote">"Transparency and unity are what define this union. It is a home for all of us."</p>
                <p class="test-author">- Deng N.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container text-center fade-up">
        <h2 class="section-title">Our Partners</h2>
        <p class="section-subtitle">Collaborating for a sustainable future.</p>
        
        <div class="partners-grid">
            <img src="<?= $baseUrl ?>images/partner1.png" class="partner-logo" alt="Partner">
            <img src="<?= $baseUrl ?>images/partner2.png" class="partner-logo" alt="Partner">
            <img src="<?= $baseUrl ?>images/partner3.png" class="partner-logo" alt="Partner">
        </div>
        <div style="margin-top: 40px;">
            <a href="<?= $baseUrl ?>partner-register.php" class="btn btn-primary" style="background:var(--text-main); color:white;">Become a Partner</a>
        </div>
    </div>
</section>

<div id="leaderModal" class="modal-backdrop">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal()">&times;</button>
        <div class="modal-img-col">
            <img id="mImg" src="" alt="" class="modal-img">
        </div>
        <div class="modal-text-col">
            <h2 id="mName" style="margin-bottom: 5px;"></h2>
            <p id="mRole" style="color: var(--primary); font-weight: 600; margin-bottom: 20px; text-transform:uppercase;"></p>
            <p id="mBio" style="color: #555; line-height: 1.8;"></p>
        </div>
    </div>
</div>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Fade Up Animation (Intersection Observer)
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));

    // 2. KPI Counter Animation
    const counters = document.querySelectorAll('.kpi-number');
    const counterObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = +counter.getAttribute('data-target');
                let count = 0;
                const updateCount = () => {
                    const increment = target / 50; // Speed
                    if(count < target) {
                        count += increment;
                        counter.innerText = Math.ceil(count);
                        requestAnimationFrame(updateCount);
                    } else {
                        counter.innerText = target;
                    }
                };
                updateCount();
                observer.unobserve(counter);
            }
        });
    }, { threshold: 0.5 });
    counters.forEach(c => counterObserver.observe(c));

    // 3. Show More Team Logic
    const toggleBtn = document.getElementById('toggleTeamBtn');
    if(toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const hiddenItems = document.querySelectorAll('.hidden-initially');
            const isExpanded = this.getAttribute('data-expanded') === 'true';

            if(!isExpanded) {
                hiddenItems.forEach(item => {
                    item.style.display = 'block';
                    // Small timeout to allow display:block to render before adding opacity for fade effect if you added css transition
                });
                this.innerHTML = 'Show Less <i class="fas fa-chevron-up" style="margin-left:8px"></i>';
                this.setAttribute('data-expanded', 'true');
            } else {
                hiddenItems.forEach(item => item.style.display = 'none');
                this.innerHTML = 'Show Full Team <i class="fas fa-chevron-down" style="margin-left:8px"></i>';
                this.setAttribute('data-expanded', 'false');
                // Scroll back to top of team section slightly
                document.getElementById('leadership').scrollIntoView({behavior: 'smooth'});
            }
        });
    }
});

// 4. Modal Logic (Global functions)
const modal = document.getElementById('leaderModal');
const mImg = document.getElementById('mImg');
const mName = document.getElementById('mName');
const mRole = document.getElementById('mRole');
const mBio = document.getElementById('mBio');

function openModal(data) {
    mImg.src = "<?= $baseUrl ?>" + data.img; // Ensure base url is prepended
    mName.textContent = data.name;
    mRole.textContent = data.role;
    mBio.textContent = data.bio;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeModal() {
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

// Close on background click
modal.addEventListener('click', (e) => {
    if(e.target === modal) closeModal();
});

// Close on Escape key
document.addEventListener('keydown', (e) => {
    if(e.key === 'Escape' && modal.classList.contains('active')) closeModal();
});
</script>