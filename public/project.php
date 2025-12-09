<?php
// projects.php - Professional, Scalable, Mobile-First
$pageTitle = "Projects - Fangak Youth Union";

// Adjust paths based on your directory structure
include_once __DIR__ . "/../app/views/layouts/header.php";
require_once __DIR__ . "/../app/config/db.php";

// ===================================================================
// 1. HELPER FUNCTIONS (The Fix for Images & Logic)
// ===================================================================

/**
 * Smart Image Path Handler
 * Handles both "filename.jpg" and "uploads/projects/filename.jpg"
 */
function get_project_img($dbValue) {
    // Default placeholder if empty
    if (empty($dbValue)) return 'images/default-project.jpg'; 

    // 1. Check if the DB value is already a full path
    if (strpos($dbValue, 'uploads/projects/') !== false) {
        return $dbValue;
    }

    // 2. If it's just a filename, prepend the folder
    return 'uploads/projects/' . $dbValue;
}

/**
 * Calculate Progress Percentage based on Dates
 */
function calculate_progress($start_date, $end_date) {
    if (!$start_date || !$end_date) return 0;

    $start = strtotime($start_date);
    $end   = strtotime($end_date);
    $now   = time();

    if ($now < $start) return 0;   // Hasn't started
    if ($now > $end) return 100;   // Finished

    $total_duration = $end - $start;
    $elapsed = $now - $start;

    if ($total_duration <= 0) return 100; // Avoid division by zero

    return round(($elapsed / $total_duration) * 100);
}

// ===================================================================
// 2. DATA FETCHING
// ===================================================================
$projects = [];
try {
    // Check if table exists to prevent crashes
    $check = $pdo->query("SHOW TABLES LIKE 'projects'");
    if ($check->rowCount() > 0) {
        $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
}
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
    :root {
        --color-primary: #0f5132;    /* Deep Official Green */
        --color-accent: #d4a017;     /* Gold/Bronze */
        --color-bg: #f8f9fa;
        --color-card: #ffffff;
        --text-main: #1f2937;
        --text-muted: #6b7280;
        --radius-lg: 16px;
        --radius-sm: 8px;
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.08);
        --shadow-lg: 0 15px 30px -5px rgba(0,0,0,0.12);
    }

    /* --- RESET & LAYOUT --- */
    body { background: var(--color-bg); color: var(--text-main); font-family: 'Inter', sans-serif; }
    .main-container { max-width: 1280px; margin: 0 auto; padding: 0 24px 80px; }
    h1, h2, h3 { font-family: 'Playfair Display', serif; }

    /* --- HERO SECTION --- */
    .project-hero {
        position: relative;
        padding: 80px 20px;
        margin: 24px 0 40px;
        border-radius: var(--radius-lg);
        background: linear-gradient(rgba(15, 81, 50, 0.9), rgba(15, 81, 50, 0.8)), url('images/FYU-donates.jpg');
        background-size: cover;
        background-position: center;
        text-align: center;
        color: white;
        box-shadow: var(--shadow-md);
    }
    .hero-title { font-size: 3rem; margin: 0 0 16px; font-weight: 700; }
    .hero-subtitle { font-size: 1.1rem; opacity: 0.95; font-weight: 300; max-width: 700px; margin: 0 auto; }

    /* --- CONTROLS --- */
    .controls-wrapper {
        display: flex; flex-wrap: wrap; gap: 20px; 
        justify-content: space-between; align-items: center;
        margin-bottom: 40px; background: white; padding: 15px 24px;
        border-radius: 50px; box-shadow: var(--shadow-md);
    }
    .filter-tabs { display: flex; gap: 10px; overflow-x: auto; }
    .filter-btn {
        background: transparent; border: none; padding: 8px 20px;
        border-radius: 50px; font-weight: 600; color: var(--text-muted); cursor: pointer;
        transition: all 0.2s; font-size: 0.9rem;
    }
    .filter-btn:hover, .filter-btn.active { background: var(--color-primary); color: white; }

    .search-box { position: relative; min-width: 250px; }
    .search-input {
        width: 100%; padding: 10px 20px 10px 40px;
        border: 1px solid #e5e7eb; border-radius: 50px;
        outline: none; transition: border 0.2s;
    }
    .search-input:focus { border-color: var(--color-accent); }
    .search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; }

    /* --- PROJECT GRID --- */
    .grid-layout { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; }
    
    .project-card {
        background: var(--color-card); border-radius: var(--radius-lg);
        overflow: hidden; box-shadow: var(--shadow-md);
        display: flex; flex-direction: column; height: 100%;
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer; border: 1px solid rgba(0,0,0,0.03);
    }
    .project-card:hover { transform: translateY(-8px); box-shadow: var(--shadow-lg); }

    /* Image Area */
    .card-media { position: relative; height: 240px; overflow: hidden; }
    .card-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
    .project-card:hover .card-img { transform: scale(1.05); }
    
    .status-badge {
        position: absolute; top: 15px; right: 15px;
        background: rgba(255,255,255,0.95); color: var(--color-primary);
        padding: 5px 12px; border-radius: 50px;
        font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    /* Content Area */
    .card-content { padding: 24px; display: flex; flex-direction: column; flex: 1; }
    .card-date { font-size: 0.85rem; color: var(--color-accent); font-weight: 600; margin-bottom: 8px; }
    .card-title { font-size: 1.4rem; margin: 0 0 10px; line-height: 1.3; color: var(--color-primary); }
    .card-excerpt { color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px; flex: 1; }

    /* Progress Bar */
    .progress-wrap { margin-top: auto; }
    .progress-meta { display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; }
    .progress-bar { height: 6px; background: #e9ecef; border-radius: 10px; overflow: hidden; }
    .progress-fill { height: 100%; background: var(--color-accent); border-radius: 10px; }

    .card-action { 
        margin-top: 20px; padding-top: 15px; border-top: 1px solid #f3f4f6;
        color: var(--color-primary); font-weight: 600; font-size: 0.9rem;
        display: flex; align-items: center; gap: 8px;
    }

    /* --- MODAL --- */
    .modal-backdrop {
        position: fixed; inset: 0; z-index: 9999;
        background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(5px);
        display: flex; align-items: center; justify-content: center;
        opacity: 0; pointer-events: none; transition: opacity 0.3s;
    }
    .modal-backdrop.active { opacity: 1; pointer-events: auto; }

    .modal-panel {
        background: #fff; width: 95%; max-width: 900px; max-height: 90vh;
        border-radius: 20px; overflow: hidden; display: flex; flex-direction: column;
        box-shadow: 0 25px 50px rgba(0,0,0,0.25);
        transform: scale(0.95); transition: transform 0.3s;
    }
    .modal-backdrop.active .modal-panel { transform: scale(1); }

    .modal-scroll { overflow-y: auto; display: flex; flex-direction: column; height: 100%; }
    @media(min-width: 768px) { .modal-scroll { flex-direction: row; } }

    .modal-visual { flex: 1.5; min-height: 300px; position: relative; }
    .modal-visual img { width: 100%; height: 100%; object-fit: cover; }
    .modal-close {
        position: absolute; top: 15px; left: 15px;
        width: 36px; height: 36px; background: rgba(0,0,0,0.5); color: white;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: background 0.2s;
    }
    .modal-close:hover { background: rgba(0,0,0,0.8); }

    .modal-details { flex: 2; padding: 40px; }
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0; border-bottom: 1px solid #eee; padding-bottom: 20px; }
    .stat-item h4 { font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px; }
    .stat-item p { font-weight: 600; color: var(--text-main); }
    
    /* Empty State */
    .empty-state { grid-column: 1 / -1; text-align: center; padding: 60px; border: 2px dashed #e5e7eb; border-radius: var(--radius-lg); }

    @media(max-width: 768px) {
        .controls-wrapper { flex-direction: column; align-items: stretch; border-radius: 20px; }
        .hero-title { font-size: 2.2rem; }
        .modal-scroll { flex-direction: column; }
    }
</style>

<div class="main-container">

    <header class="project-hero">
        <h1 class="hero-title">Our Initiatives</h1>
        <p class="hero-subtitle">Driving change through infrastructure, education, and community development across Fangak County.</p>
    </header>

    <div class="controls-wrapper">
        <div class="filter-tabs">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="new">New</button>
            <button class="filter-btn" data-filter="current">Ongoing</button>
            <button class="filter-btn" data-filter="completed">Completed</button>
        </div>
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="Find a project...">
        </div>
    </div>

    <div class="grid-layout" id="projectGrid">
        <?php if (!empty($projects)): ?>
            <?php foreach($projects as $p): 
                // --- PREPARE DATA ---
                $title = htmlspecialchars($p['title']);
                $status = htmlspecialchars($p['status'] ?? 'New');
                
                // Description: strip tags for card, keep HTML for modal
                $descRaw = $p['description'];
                $descExcerpt = mb_strimwidth(strip_tags($descRaw), 0, 120, '...');
                
                // Image Fix
                $img = get_project_img($p['image']);
                
                // Dates & Progress
                $startDate = $p['start_date'] ? date('M d, Y', strtotime($p['start_date'])) : 'TBD';
                $endDate = $p['end_date'] ? date('M d, Y', strtotime($p['end_date'])) : 'TBD';
                $progress = calculate_progress($p['start_date'], $p['end_date']);
                
                // Location (Mocked as it's not in DB, but good for UI)
                $location = "Fangak County"; 
            ?>
            
            <article class="project-card" 
                     data-status="<?= strtolower($status) ?>" 
                     data-title="<?= strtolower($title) ?>">
                
                <div class="card-media">
                    <img src="<?= $img ?>" alt="<?= $title ?>" class="card-img" loading="lazy">
                    <span class="status-badge"><?= $status ?></span>
                </div>

                <div class="card-content">
                    <div class="card-date">
                        <i class="fa-regular fa-clock"></i> <?= $startDate ?>
                    </div>
                    
                    <h3 class="card-title"><?= $title ?></h3>
                    <p class="card-excerpt"><?= $descExcerpt ?></p>

                    <div class="js-full-data" style="display:none;">
                        <div class="d-desc"><?= $descRaw ?></div>
                        <div class="d-start"><?= $startDate ?></div>
                        <div class="d-end"><?= $endDate ?></div>
                        <div class="d-location"><?= $location ?></div>
                        <div class="d-progress"><?= $progress ?>%</div>
                    </div>

                    <div class="progress-wrap">
                        <div class="progress-meta">
                            <span>Completion</span>
                            <span><?= $progress ?>%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $progress ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="card-action">
                        See Details <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fa-solid fa-layer-group" style="font-size: 3rem; color: #ddd; margin-bottom: 15px;"></i>
                <h3>No Projects Found</h3>
                <p>We are currently updating our project database.</p>
            </div>
        <?php endif; ?>

        <div id="noResults" class="empty-state" style="display:none;">
            <h3>No matches found</h3>
            <p>Try adjusting your search filters.</p>
        </div>
    </div>
</div>

<div id="projectModal" class="modal-backdrop">
    <div class="modal-panel">
        <div class="modal-scroll">
            <div class="modal-visual">
                <img id="mImg" src="" alt="Project Image">
                <div class="modal-close" onclick="closeModal()">
                    <i class="fa-solid fa-xmark"></i>
                </div>
            </div>
            
            <div class="modal-details">
                <span id="mStatus" style="color:var(--color-accent); font-weight:700; text-transform:uppercase; font-size:0.85rem; letter-spacing:1px;">Status</span>
                <h2 id="mTitle" style="font-size:2.5rem; line-height:1.1; margin:10px 0 20px; color:var(--color-primary);">Project Title</h2>
                
                <div class="detail-grid">
                    <div class="stat-item">
                        <h4>Start Date</h4>
                        <p id="mStart">Nov 20, 2025</p>
                    </div>
                    <div class="stat-item">
                        <h4>Target End</h4>
                        <p id="mEnd">Nov 30, 2025</p>
                    </div>
                    <div class="stat-item">
                        <h4>Location</h4>
                        <p id="mLocation">Fangak</p>
                    </div>
                    <div class="stat-item">
                        <h4>Progress</h4>
                        <p id="mProgress">0%</p>
                    </div>
                </div>

                <div style="font-size:1.05rem; line-height:1.8; color:#444; margin-bottom:30px;">
                    <h4 style="font-family:serif; font-size:1.5rem; margin-bottom:10px;">About this Initiative</h4>
                    <div id="mDesc"></div>
                </div>

                

[Image of Project Gantt Chart]


                <a href="<?= $baseUrl ?>contact.php" style="display:inline-block; background:var(--color-primary); color:white; padding:12px 30px; border-radius:50px; text-decoration:none; font-weight:600; transition:background 0.2s;">
                    Support This Project
                </a>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // 1. FILTER & SEARCH
    const grid = document.getElementById('projectGrid');
    const cards = Array.from(document.querySelectorAll('.project-card'));
    const searchInput = document.getElementById('searchInput');
    const filterBtns = document.querySelectorAll('.filter-btn');
    const noResults = document.getElementById('noResults');

    function filterProjects() {
        const term = searchInput.value.toLowerCase();
        const activeBtn = document.querySelector('.filter-btn.active');
        const activeCategory = activeBtn ? activeBtn.getAttribute('data-filter') : 'all';
        let visibleCount = 0;

        cards.forEach(card => {
            const title = card.getAttribute('data-title').toLowerCase();
            const status = card.getAttribute('data-status').toLowerCase(); // e.g., 'new', 'current'

            const matchesSearch = title.includes(term);
            const matchesFilter = (activeCategory === 'all') || (status === activeCategory);

            if (matchesSearch && matchesFilter) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        noResults.style.display = visibleCount === 0 ? 'block' : 'none';
    }

    searchInput.addEventListener('input', filterProjects);
    filterBtns.forEach(btn => btn.addEventListener('click', () => {
        filterBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        filterProjects();
    }));

    // 2. MODAL LOGIC
    const modal = document.getElementById('projectModal');
    const els = {
        img: document.getElementById('mImg'),
        title: document.getElementById('mTitle'),
        status: document.getElementById('mStatus'),
        desc: document.getElementById('mDesc'),
        start: document.getElementById('mStart'),
        end: document.getElementById('mEnd'),
        loc: document.getElementById('mLocation'),
        prog: document.getElementById('mProgress')
    };

    grid.addEventListener('click', e => {
        const card = e.target.closest('.project-card');
        if (!card) return;

        // Visuals
        els.img.src = card.querySelector('.card-img').src;
        els.title.innerText = card.querySelector('.card-title').innerText;
        els.status.innerText = card.querySelector('.status-badge').innerText;

        // Hidden Data
        const data = card.querySelector('.js-full-data');
        els.desc.innerHTML = data.querySelector('.d-desc').innerHTML;
        els.start.innerText = data.querySelector('.d-start').innerText;
        els.end.innerText = data.querySelector('.d-end').innerText;
        els.loc.innerText = data.querySelector('.d-location').innerText;
        els.prog.innerText = data.querySelector('.d-progress').innerText;

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    });

    window.closeModal = function() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    modal.addEventListener('click', e => {
        if (e.target === modal) closeModal();
    });
    
    document.addEventListener('keydown', e => {
        if(e.key === "Escape") closeModal();
    });
});
</script>