<?php
declare(strict_types=1);

/**
 * project.php 
 * A High-Performance, Professional Project Portfolio for FYU
 */

require_once __DIR__ . "/../app/config/db.php";

/* --- UTILITIES --- */

function get_project_image(?string $path): string {
    if (!$path) return 'assets/images/placeholder-project.jpg';
    return (strpos($path, 'uploads/') === 0) ? $path : "uploads/projects/" . ltrim($path, '/');
}

function get_progress(string $start, string $end): int {
    $s = strtotime($start);
    $e = strtotime($end);
    $n = time();
    if ($n < $s) return 0;
    if ($n > $e) return 100;
    return (int) round((($n - $s) / ($e - $s)) * 100);
}

/* --- DATA FETCHING --- */
$projects = [];
try {
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("DB Error in Projects: " . $e->getMessage());
}

$pageTitle = "Initiatives – Fangak Youth Union";
include_once __DIR__ . "/../app/views/layouts/header.php";
?>

<style>
    :root {
        --fyu-green: #0a4028;
        --fyu-gold: #c59d5f;
        --fyu-bg: #fcfcfd;
        --glass: rgba(255, 255, 255, 0.8);
    }

    body { background-color: var(--fyu-bg); font-family: 'Inter', sans-serif; }

    /* Hero Styling */
    .hero-gradient {
        background: linear-gradient(135deg, var(--fyu-green) 0%, #155e3e 100%);
    }

    /* Filter Pills */
    .filter-pills::-webkit-scrollbar { display: none; }
    .filter-pill {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #e5e7eb;
    }
    .filter-pill.active {
        background: var(--fyu-green);
        color: white;
        border-color: var(--fyu-green);
        box-shadow: 0 4px 12px rgba(10, 64, 40, 0.2);
    }

    /* Project Cards */
    .project-card {
        transition: transform 0.4s ease, box-shadow 0.4s ease;
    }
    .project-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
    }

    /* Professional Progress Bar */
    .progress-track { background: #edf2f0; height: 6px; border-radius: 10px; overflow: hidden; }
    .progress-fill { 
        height: 100%; 
        background: linear-gradient(90deg, var(--fyu-gold), #e6b97a);
        transition: width 1s ease-out;
    }

    /* Modal Animation */
    #projectModal { transition: opacity 0.3s ease; }
    .modal-content-box {
        transform: translateY(20px);
        transition: transform 0.3s ease;
    }
    #projectModal.active .modal-content-box { transform: translateY(0); }
</style>

<main class="min-h-screen">
    
    <header class="hero-gradient pt-24 pb-32 text-white relative overflow-hidden">
        <div class="absolute right-0 top-0 w-1/3 h-full opacity-10 pointer-events-none">
            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                <path fill="#FFF" d="M44.7,-76.4C58.1,-69.2,69.2,-56.1,76.4,-41.4C83.6,-26.7,86.9,-10.3,85.2,5.6C83.5,21.5,76.8,36.9,67,50.1C57.2,63.3,44.3,74.3,29.7,79.5C15.1,84.7,-1.2,84.1,-17.1,79.9C-33,75.7,-48.5,67.9,-60.8,56.1C-73.1,44.3,-82.2,28.5,-85.5,11.8C-88.8,-4.9,-86.3,-22.5,-78.3,-37.6C-70.3,-52.7,-56.8,-65.3,-41.8,-71.8C-26.8,-78.3,-13.4,-78.7,1.4,-81C16.1,-83.4,31.3,-83.6,44.7,-76.4Z" transform="translate(100 100)" />
            </svg>
        </div>
        
        <div class="max-w-7xl mx-auto px-6 relative z-10 text-center">
            <h1 class="text-4xl md:text-6xl font-serif font-bold mb-6 tracking-tight">Our Projects</h1>
            <p class="max-w-2xl mx-auto text-green-100 text-lg md:text-xl font-light leading-relaxed">
                Building a resilient future for Fangak through strategic infrastructure, 
                healthcare access, and youth empowerment.
            </p>
        </div>
    </header>

    <section class="max-w-7xl mx-auto px-6 -mt-10 mb-16">
        <div class="bg-white rounded-3xl shadow-xl p-4 md:p-6 flex flex-col md:flex-row items-center justify-between gap-6 border border-gray-100">
            <div class="filter-pills flex gap-2 overflow-x-auto w-full md:w-auto pb-2 md:pb-0">
                <button class="filter-pill active px-6 py-2.5 rounded-full text-sm font-bold whitespace-nowrap" data-filter="all">All Initiatives</button>
                <button class="filter-pill px-6 py-2.5 rounded-full text-sm font-bold text-gray-500 whitespace-nowrap" data-filter="current">Ongoing</button>
                <button class="filter-pill px-6 py-2.5 rounded-full text-sm font-bold text-gray-500 whitespace-nowrap" data-filter="completed">Completed</button>
                <button class="filter-pill px-6 py-2.5 rounded-full text-sm font-bold text-gray-500 whitespace-nowrap" data-filter="new">Planned</button>
            </div>

            <div class="relative w-full md:w-80">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="projectSearch" placeholder="Search projects..." 
                    class="w-full pl-12 pr-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-fyu-green transition">
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 pb-24">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10" id="projectContainer">
            <?php if (!empty($projects)): foreach ($projects as $p): 
                $progress = get_progress($p['start_date'] ?? '', $p['end_date'] ?? '');
                $statusClass = strtolower($p['status'] ?? 'planned');
            ?>
            <div class="project-card bg-white rounded-[2rem] overflow-hidden flex flex-col h-full border border-gray-100 cursor-pointer"
                 data-status="<?= $statusClass ?>"
                 data-title="<?= strtolower(htmlspecialchars($p['title'])) ?>"
                 onclick="openProjectModal(<?= htmlspecialchars(json_encode($p)) ?>, <?= $progress ?>)">
                
                <div class="h-64 relative overflow-hidden">
                    <img src="<?= get_project_image($p['image']) ?>" class="w-full h-full object-cover transition-transform duration-700 hover:scale-110" alt="<?= htmlspecialchars($p['title']) ?>">
                    <div class="absolute top-5 left-5">
                        <span class="bg-white/90 backdrop-blur px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest text-fyu-green shadow-sm">
                            <?= htmlspecialchars($p['status'] ?? 'New') ?>
                        </span>
                    </div>
                </div>

                <div class="p-8 flex flex-col flex-1">
                    <span class="text-fyu-gold font-bold text-xs uppercase tracking-tighter mb-2">
                        <i class="fa-solid fa-calendar-day mr-1 opacity-70"></i> 
                        <?= date('M Y', strtotime($p['start_date'] ?? 'now')) ?>
                    </span>
                    <h3 class="text-xl font-bold text-fyu-green mb-3 leading-snug">
                        <?= htmlspecialchars($p['title']) ?>
                    </h3>
                    <p class="text-gray-500 text-sm line-clamp-3 mb-8 leading-relaxed">
                        <?= strip_tags($p['description']) ?>
                    </p>

                    <div class="mt-auto">
                        <div class="flex justify-between text-[11px] font-bold text-gray-400 uppercase mb-2">
                            <span>Progress</span>
                            <span><?= $progress ?>%</span>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill" style="width: <?= $progress ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; else: ?>
                <div class="col-span-full py-20 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                        <i class="fa-solid fa-folder-open text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">No initiatives found</h3>
                    <p class="text-gray-500">Check back later for updates on our ongoing work.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

</main>

<div id="projectModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-6 opacity-0 pointer-events-none transition-all duration-300">
    <div class="absolute inset-0 bg-fyu-green/60 backdrop-blur-md" onclick="closeProjectModal()"></div>
    
    <div class="modal-content-box bg-white w-full max-w-5xl max-h-[90vh] rounded-[3rem] overflow-hidden shadow-2xl relative z-10 flex flex-col md:flex-row">
        <div class="md:w-5/12 h-64 md:h-auto relative">
            <img id="m-image" src="" class="w-full h-full object-cover" alt="">
            <button onclick="closeProjectModal()" class="absolute top-6 left-6 w-10 h-10 bg-white/20 backdrop-blur-xl rounded-full text-white hover:bg-white/40 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="md:w-7/12 p-8 md:p-14 overflow-y-auto">
            <div class="flex items-center gap-3 mb-6">
                <span id="m-status" class="px-4 py-1 rounded-full bg-fyu-accent text-fyu-green font-black text-[10px] uppercase"></span>
                <span class="text-gray-300">|</span>
                <span id="m-date" class="text-sm font-medium text-gray-400"></span>
            </div>
            
            <h2 id="m-title" class="text-3xl md:text-4xl font-serif font-bold text-fyu-green mb-8"></h2>
            
            <div class="grid grid-cols-2 gap-8 mb-10 border-y border-gray-100 py-8">
                <div>
                    <h4 class="text-[10px] font-black text-gray-300 uppercase tracking-widest mb-1">Target End Date</h4>
                    <p id="m-end" class="font-bold text-gray-700"></p>
                </div>
                <div>
                    <h4 class="text-[10px] font-black text-gray-300 uppercase tracking-widest mb-1">Progress Level</h4>
                    <p id="m-progress-txt" class="font-bold text-fyu-gold"></p>
                </div>
            </div>

            <div id="m-desc" class="prose prose-sm prose-green max-w-none text-gray-600 leading-relaxed mb-10"></div>

            <div class="flex flex-wrap gap-4">
                <a href="contact.php" class="bg-fyu-green text-white px-8 py-4 rounded-2xl font-bold hover:bg-fyu-gold transition-colors duration-300">
                    Sponsor This Initiative
                </a>
                <button onclick="closeProjectModal()" class="px-8 py-4 text-gray-400 font-bold hover:text-fyu-green transition">
                    Back to Grid
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Filtering and Search Logic
    const searchInput = document.getElementById('projectSearch');
    const filterBtns = document.querySelectorAll('.filter-pill');
    const cards = document.querySelectorAll('.project-card');

    function updateGrid() {
        const query = searchInput.value.toLowerCase();
        const activeFilter = document.querySelector('.filter-pill.active').dataset.filter;

        cards.forEach(card => {
            const title = card.dataset.title;
            const status = card.dataset.status;
            
            const matchesSearch = title.includes(query);
            const matchesFilter = activeFilter === 'all' || status === activeFilter;

            if (matchesSearch && matchesFilter) {
                card.style.display = 'flex';
                card.classList.add('animate-fade-in');
            } else {
                card.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', updateGrid);
    filterBtns.forEach(btn => btn.addEventListener('click', () => {
        filterBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        updateGrid();
    }));

    // Modal Operations
    function openProjectModal(p, progress) {
        const modal = document.getElementById('projectModal');
        document.getElementById('m-image').src = p.image ? 'uploads/projects/'+p.image : 'assets/images/placeholder.jpg';
        document.getElementById('m-title').innerText = p.title;
        document.getElementById('m-status').innerText = p.status || 'Planned';
        document.getElementById('m-date').innerText = p.start_date;
        document.getElementById('m-end').innerText = p.end_date || 'Ongoing';
        document.getElementById('m-progress-txt').innerText = progress + '% Complete';
        document.getElementById('m-desc').innerHTML = p.description;

        modal.classList.add('active');
        modal.classList.remove('opacity-0', 'pointer-events-none');
        document.body.style.overflow = 'hidden';
    }

    function closeProjectModal() {
        const modal = document.getElementById('projectModal');
        modal.classList.remove('active');
        modal.classList.add('opacity-0', 'pointer-events-none');
        document.body.style.overflow = 'auto';
    }
</script>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>