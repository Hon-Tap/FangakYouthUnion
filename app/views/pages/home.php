<?php
$pageTitle = "Home - Fangak Youth Union";
// include_once __DIR__ . "views/layouts/header.php";
?>

<!-- ================= FLOATING JOIN BUTTON ================= -->
<a href="<?= $baseUrl ?>joinus.php" class="floating-btn" aria-label="Join Fangak Youth Union">Join Us</a>

<!-- ================= HERO SECTION ================= -->
<section class="hero" aria-label="Fangak Youth Union hero">
    <div class="parallax-bg" style="background-image:url('<?= $baseUrl ?>images/FYU-LOGO.jpg');" aria-hidden="true"></div>
    <div class="hero-overlay" aria-hidden="true"></div>
    <div class="hero-container">
        <div class="hero-text" data-anim="fade-up">
            <h1>Welcome to <span>Fangak Youth Union</span></h1>
            <p>Empowering youth, building community, and creating a better future through innovation and projects.</p>
            <div class="hero-actions">
                <a class="hero-btn" href="<?= $baseUrl ?>joinus.php">Join Us Today</a>
                <a class="secondary-btn" href="#mission">See Our Mission</a>
            </div>
        </div>
        <div class="hero-card" aria-hidden="true" data-anim="zoom-in">
            <img src="<?= $baseUrl ?>images/FYU-LOGO.jpg" alt="FYU logo" class="hero-side-img">
        </div>
    </div>
    <a class="scroll-arrow" href="#mission" aria-label="Scroll to mission"><span class="material-icons" aria-hidden="true">keyboard_arrow_down</span></a>
</section>

<!-- ================= MISSION SECTION ================= -->
<section id="mission" class="section">
    <div class="container section-header" data-anim="fade-up">
        <h2>Our Mission</h2>
        <p>We empower youth with skills, resources, and opportunities to lead innovative projects that create lasting impact in Fangak and beyond.</p>
    </div>
    <div class="container cards-grid" role="list">
        <article class="card" role="listitem" tabindex="0" data-anim="fade-up" data-delay="0">
            <div class="icon"><i class="fa-solid fa-people-group" aria-hidden="true"></i></div>
            <h3>Unity & Leadership</h3>
            <p>Fostering strong leadership to build a united and empowered generation.</p>
        </article>
        <article class="card" role="listitem" tabindex="0" data-anim="fade-up" data-delay="100">
            <div class="icon"><i class="fa-solid fa-book-open" aria-hidden="true"></i></div>
            <h3>Education</h3>
            <p>Breaking barriers through education, mentorship, and lifelong learning.</p>
        </article>
        <article class="card" role="listitem" tabindex="0" data-anim="fade-up" data-delay="200">
            <div class="icon"><i class="fa-solid fa-hand-holding-heart" aria-hidden="true"></i></div>
            <h3>Community Service</h3>
            <p>Driving change through service and grassroots development projects.</p>
        </article>
    </div>
</section>

<!-- ================= VISION SECTION ================= -->
<section class="section alt-bg">
    <div class="container section-header" data-anim="fade-up">
        <h2>Our Vision</h2>
        <p>A united and empowered generation of youth transforming Fangak and South Sudan through creativity, leadership, and sustainable development.</p>
    </div>
    <div class="container cards-grid">
        <article class="card" role="listitem" tabindex="0" data-anim="fade-up" data-delay="0">
            <div class="icon"><i class="fa-solid fa-eye" aria-hidden="true"></i></div>
            <h3>Innovation in Action</h3>
            <p>Implementing creative solutions that drive progress and empower communities.</p>
        </article>
        <article class="card" role="listitem" tabindex="0" data-anim="fade-up" data-delay="100">
            <div class="icon"><i class="fa-solid fa-handshake" aria-hidden="true"></i></div>
            <h3>Collaboration</h3>
            <p>Partnering with stakeholders for collective impact and sustainable development.</p>
        </article>
        <article class="card" role="listitem" tabindex="0" data-anim="fade-up" data-delay="200">
            <div class="icon"><i class="fa-solid fa-globe" aria-hidden="true"></i></div>
            <h3>Global Perspective</h3>
            <p>Fostering awareness and solutions that can scale beyond local communities.</p>
        </article>
    </div>
</section>

<!-- ================= PROJECTS SECTION ================= -->
<section class="section">
    <div class="container section-header" data-anim="fade-up">
        <h2>Our Projects</h2>
        <p>Explore our ongoing projects that drive real impact and inspire community transformation.</p>
    </div>
    <div class="container projects-grid">
        <figure class="project-card" tabindex="0" data-src="<?= $baseUrl ?>images/project1.jpg" data-title="Youth Skills Development" data-desc="Equipping young people with digital and vocational skills for a better future." data-anim="fade-up" data-delay="0">
            <img src="<?= $baseUrl ?>images/project1.jpg" alt="Youth Skills Development project" class="project-img">
            <figcaption class="project-info">
                <h3>Youth Skills Development</h3>
                <p>Equipping young people with digital and vocational skills for a better future.</p>
            </figcaption>
        </figure>
        <figure class="project-card" tabindex="0" data-src="<?= $baseUrl ?>images/FloTra.jpg" data-title="Flood Response Initiative" data-desc="Supporting displaced families with sustainable rebuilding efforts." data-anim="fade-up" data-delay="80">
            <img src="<?= $baseUrl ?>images/FloTra.jpg" alt="Flood response initiative" class="project-img">
            <figcaption class="project-info">
                <h3>Flood Response Initiative</h3>
                <p>Supporting displaced families with sustainable rebuilding efforts.</p>
            </figcaption>
        </figure>
        <figure class="project-card" tabindex="0" data-src="<?= $baseUrl ?>images/SACOM.jpg" data-title="Access to Education" data-desc="Providing resources and mentorship for children in rural communities." data-anim="fade-up" data-delay="160">
            <img src="<?= $baseUrl ?>images/SACOM.jpg" alt="Access to education project" class="project-img">
            <figcaption class="project-info">
                <h3>Access to Education</h3>
                <p>Providing resources and mentorship for children in rural communities.</p>
            </figcaption>
        </figure>
    </div>
</section>

<!-- ================= VALUES SECTION ================= -->
<section class="section alt-bg">
    <div class="container section-header" data-anim="fade-up">
        <h2>Why Choose FYU?</h2>
        <p>We stand for integrity, impact, and inclusivity in every initiative we undertake.</p>
    </div>
    <div class="container cards-grid">
        <article class="card" tabindex="0" data-anim="fade-up" data-delay="0">
            <div class="icon"><i class="fa-solid fa-lightbulb"></i></div>
            <h3>Innovation</h3>
            <p>Turning creative ideas into sustainable community solutions.</p>
        </article>
        <article class="card" tabindex="0" data-anim="fade-up" data-delay="100">
            <div class="icon"><i class="fa-solid fa-users"></i></div>
            <h3>Inclusivity</h3>
            <p>Bringing together diverse voices and perspectives for shared progress.</p>
        </article>
        <article class="card" tabindex="0" data-anim="fade-up" data-delay="200">
            <div class="icon"><i class="fa-solid fa-handshake-angle"></i></div>
            <h3>Partnership</h3>
            <p>Collaborating with stakeholders for amplified results.</p>
        </article>
        <article class="card" tabindex="0" data-anim="fade-up" data-delay="300">
            <div class="icon"><i class="fa-solid fa-seedling"></i></div>
            <h3>Sustainability</h3>
            <p>Building projects that continue to benefit future generations.</p>
        </article>
    </div>
</section>

<!-- ================= JOIN / CTA SECTION ================= -->
<section class="joinus-section">
    <div class="joinus-container container">
        <div class="joinus-grid">
            <div class="join-text" data-anim="fade-right">
                <h2>Be Part of the Change</h2>
                <p>Join us in empowering youth and transforming our communities. Your skills and energy matter.</p>
            </div>
            <div class="join-actions" data-anim="fade-left">
                <a class="hero-btn" href="<?= $baseUrl ?>joinus.php">Join the Movement</a>
            </div>
        </div>
    </div>
</section>

<!-- <?php include_once __DIR__ . "/layouts/footer.php"; ?> -->

<!-- ================= ADVANCED CSS ================= -->
<style>
:root {
    --bg: #f5f7fa;
    --card: #ffffff;
    --text: #222;
    --muted: #6b7280;
    --accent: #38a159;
    --accent-dark: #2f8f4d;
    --radius: 16px;
    --shadow-light: 4px 4px 15px rgba(0,0,0,0.08), -4px -4px 15px rgba(255,255,255,0.7);
    --shadow-dark: 10px 10px 30px rgba(0,0,0,0.1), -10px -10px 30px rgba(255,255,255,0.65);
    --transition: all 0.3s cubic-bezier(.2,.9,.3,1);
}

body {
    margin: 0;
    font-family: 'Nunito', sans-serif;
    background: var(--bg);
    color: var(--text);
}

/* CONTAINERS */
.container {
    width: 90%;
    max-width: 1200px;
    margin: 0 auto;
}

/* HERO */
.hero {
    position: relative;
    min-height: 70vh;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    padding: 40px 0;
}

.parallax-bg {
    position: absolute; inset:0;
    background-size: cover;
    background-position:center;
    transform: translateZ(0);
    filter: brightness(0.45) saturate(0.95);
    transition: transform 0.3s ease-out;
}

.hero-overlay {
    position: absolute; inset:0;
    background: linear-gradient(180deg, rgba(0,0,0,0.3), rgba(0,0,0,0.45));
}

.hero-container {
    display: flex;
    justify-content: space-between;
    align-items:center;
    width: 100%;
    max-width: 1200px;
    flex-wrap: wrap;
}

.hero-text { color: #fff; max-width: 600px; }
.hero-text h1 { font-size: 3rem; margin-bottom: 12px; }
.hero-text h1 span { color: var(--accent); }
.hero-text p { font-size: 1.1rem; line-height:1.5; margin-bottom: 20px; }

.hero-btn, .secondary-btn {
    padding: 12px 22px;
    border-radius: 12px;
    font-weight: 600;
    transition: var(--transition);
}
.hero-btn { background: var(--accent); color:#fff; box-shadow: 0 8px 20px rgba(56,161,89,0.2); }
.hero-btn:hover { background: var(--accent-dark); transform: translateY(-3px); }
.secondary-btn { border:1px solid rgba(255,255,255,0.2); color:#fff; background:transparent; }
.secondary-btn:hover { background: rgba(255,255,255,0.1); transform: translateY(-2px); }

.hero-card {
    border-radius: var(--radius);
    overflow: hidden;
    background: rgba(255,255,255,0.06);
    box-shadow: var(--shadow-dark);
}
.hero-side-img { width:100%; height:100%; object-fit:cover; filter: brightness(0.75); transition: transform 0.4s; }
.hero-card:hover .hero-side-img { transform: scale(1.05); }

/* CARDS */
.cards-grid {
    display:grid;
    grid-template-columns: repeat(auto-fit,minmax(260px,1fr));
    gap: 24px;
}

.card {
    background: var(--card);
    border-radius: var(--radius);
    padding: 24px;
    box-shadow: var(--shadow-light);
    transition: var(--transition);
}
.card:hover { transform: translateY(-8px); box-shadow: var(--shadow-dark); }
.card .icon { font-size: 22px; width:56px; height:56px; border-radius:12px; background: rgba(56,161,89,0.1); display:flex; align-items:center; justify-content:center; margin-bottom:12px; }
.card h3 { margin:0 0 8px; font-weight:700; }
.card p { margin:0; color: var(--muted); }

/* PROJECTS */
.projects-grid { display:grid; grid-template-columns: repeat(auto-fit,minmax(300px,1fr)); gap:24px; }
.project-card { border-radius: var(--radius); overflow:hidden; background: var(--card); box-shadow: var(--shadow-light); cursor:pointer; transition: var(--transition); }
.project-card:hover { transform: translateY(-8px); box-shadow: var(--shadow-dark); }
.project-img { width:100%; height:220px; object-fit:cover; transition: var(--transition); }
.project-card:hover .project-img { transform: scale(1.06); filter: brightness(0.9); }
.project-info { padding:16px; background:linear-gradient(180deg, rgba(255,255,255,1), rgba(255,255,255,0.98)); }

/* JOIN CTA */
.joinus-section { background: linear-gradient(135deg, var(--accent), var(--accent-dark)); color:#fff; padding:64px 0; }
.joinus-grid { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:24px; padding:24px; background: rgba(255,255,255,0.05); border-radius: var(--radius); }

/* FLOATING BUTTON */
.floating-btn { position:fixed; bottom:24px; right:24px; background: var(--accent); color:#fff; padding:14px 24px; border-radius:999px; font-weight:600; box-shadow:0 8px 20px rgba(56,161,89,0.3); z-index:9999; transition: var(--transition); }
.floating-btn:hover { transform:translateY(-4px); background:var(--accent-dark); }

/* RESPONSIVE */
@media(max-width:980px) { .hero-container { flex-direction:column-reverse; } }
@media(max-width:640px) { .hero-text h1 { font-size:2rem; } .project-img{ height:180px; } }

/* ================= ANIMATIONS ================= */
[data-anim] { opacity:0; transform: translateY(24px); transition: all 0.6s ease-out; }
[data-anim].in-view { opacity:1; transform:translateY(0); }
</style>

<!-- ================= ADVANCED JS ================= -->
<script>
// Parallax Background
(function(){
    const bg = document.querySelector('.parallax-bg');
    if(bg){
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY;
            const offset = Math.min(scrolled*0.12, 120);
            bg.style.transform = `translateY(${offset}px) scale(1.02)`;
        }, {passive:true});
    }
})();

// Fade-in Animation on Scroll
(function(){
    const animEls = document.querySelectorAll('[data-anim]');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if(entry.isIntersecting){
                const el = entry.target;
                const delay = el.dataset.delay || 0;
                setTimeout(()=> el.classList.add('in-view'), delay);
            }
        });
    }, {threshold:0.1});
    animEls.forEach(el => observer.observe(el));
})();

// Modal Project Preview
(function(){
    const modal = document.getElementById('projectModal');
    if(!modal) return;
    const modalImg = modal.querySelector('.modal-img');
    const modalTitle = modal.querySelector('#modalTitle');
    const modalDesc = modal.querySelector('#modalDesc');
    const modalClose = modal.querySelector('.modal-close');
    document.querySelectorAll('.project-card').forEach(card=>{
        card.addEventListener('click', ()=>{
            modalImg.src = card.dataset.src || '';
            modalTitle.textContent = card.dataset.title || '';
            modalDesc.textContent = card.dataset.desc || '';
            modal.classList.add('show');
            document.documentElement.style.overflow = 'hidden';
        });
    });
    modalClose.addEventListener('click', ()=>{
        modal.classList.remove('show');
        document.documentElement.style.overflow = '';
    });
    modal.addEventListener('click', e=>{ if(e.target===modal) modalClose.click(); });
})();
</script>
