<?php
declare(strict_types=1);
require_once __DIR__ . "/../app/config/db.php";

/**
 * UTILITY: Resolves image paths for Projects
 * Handles Cloudinary URLs, local paths, and placeholders
 */
function get_project_media(?string $path): string {
    if (!$path) return 'assets/images/placeholder-project.jpg';
    if (str_starts_with($path, 'http')) return $path;
    return "uploads/projects/" . ltrim($path, '/');
}

function calculate_progress(?string $start, ?string $end): int {
    if (!$start || !$end) return 0;
    $s = strtotime($start);
    $e = strtotime($end);
    $n = time();
    if ($n < $s) return 0;
    if ($n > $e) return 100;
    return (int) round((($n - $s) / ($e - $s)) * 100);
}

/* DATA FETCHING */
$projects = [];
try {
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
}

$pageTitle = "Initiatives – Fangak Youth Union";
include_once __DIR__ . "/../app/views/layouts/header.php";
?>

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<style>
    .glass-nav { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); }
    .project-card:hover .project-img { transform: scale(1.05); }
    .filter-pill.active { background: #1f7a4b; color: white; }
</style>

<main class="bg-[#fcfcfd] min-h-screen pb-20">
    <section class="relative bg-[#0e3a24] pt-32 pb-48 overflow-hidden">
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white"></path>
            </svg>
        </div>
        <div class="max-w-7xl mx-auto px-6 text-center relative z-10">
            <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 tracking-tight" data-aos="fade-down">Our Initiatives</h1>
            <p class="text-green-100/80 text-lg md:text-xl max-w-2xl mx-auto font-light" data-aos="fade-up" data-aos-delay="100">
                Transforming Fangak through sustainable development, leadership, and community action.
            </p>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-6 -mt-16 mb-16 relative z-20">
        <div class="bg-white rounded-3xl shadow-2xl p-4 flex flex-col md:flex-row items-center gap-4 border border-gray-100">
            <div class="flex gap-2 overflow-x-auto pb-2 md:pb-0 w-full md:w-auto">
                <button class="filter-pill active px-6 py-2.5 rounded-full text-sm font-bold transition-all" data-filter="all">All</button>
                <button class="filter-pill px-6 py-2.5 rounded-full text-sm font-bold text-gray-500 hover:bg-gray-100" data-filter="current">Current</button>
                <button class="filter-pill px-6 py-2.5 rounded-full text-sm font-bold text-gray-500 hover:bg-gray-100" data-filter="finished">Completed</button>
                <button class="filter-pill px-6 py-2.5 rounded-full text-sm font-bold text-gray-500 hover:bg-gray-100" data-filter="new">Planned</button>
            </div>
            <div class="relative w-full md:flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="projectSearch" placeholder="Search initiatives..." 
                       class="w-full pl-12 pr-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-green-600 transition">
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="projectContainer">
            <?php foreach ($projects as $p): 
                $progress = calculate_progress($p['start_date'], $p['end_date']);
                $status = strtolower($p['status'] ?? 'New');
            ?>
            <div class="project-card bg-white rounded-[2.5rem] border border-gray-100 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 flex flex-col"
                 data-status="<?= $status ?>" data-aos="fade-up">
                
                <div class="relative h-64 overflow-hidden">
                    <img src="<?= get_project_media($p['image']) ?>" 
                         class="project-img w-full h-full object-cover transition-transform duration-700" alt="Project">
                    <div class="absolute top-6 left-6">
                        <span class="bg-white/90 backdrop-blur-md px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest text-green-900 shadow-sm">
                            <?= htmlspecialchars($p['status']) ?>
                        </span>
                    </div>
                </div>

                <div class="p-8 flex flex-col flex-1">
                    <div class="text-amber-600 font-bold text-[11px] uppercase tracking-widest mb-3">
                        <i class="fa-regular fa-calendar-check mr-2"></i>
                        <?= $p['start_date'] ? date('M Y', strtotime($p['start_date'])) : 'TBD' ?>
                    </div>
                    <h3 class="text-2xl font-bold text-green-950 mb-4 leading-tight"><?= htmlspecialchars($p['title']) ?></h3>
                    <p class="text-gray-500 text-sm leading-relaxed line-clamp-3 mb-8">
                        <?= strip_tags($p['description']) ?>
                    </p>

                    <div class="mt-auto">
                        <div class="flex justify-between text-[11px] font-bold text-gray-400 mb-2 uppercase">
                            <span>Status</span>
                            <span><?= $progress ?>%</span>
                        </div>
                        <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-gradient-to-r from-amber-400 to-amber-600 h-full transition-all duration-1000" style="width: <?= $progress ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true });

    // Live Filter Logic
    const search = document.getElementById('projectSearch');
    const pills = document.querySelectorAll('.filter-pill');
    const cards = document.querySelectorAll('.project-card');

    function filterGrid() {
        const query = search.value.toLowerCase();
        const activeFilter = document.querySelector('.filter-pill.active').dataset.filter;

        cards.forEach(card => {
            const title = card.querySelector('h3').innerText.toLowerCase();
            const status = card.dataset.status;
            const matchesSearch = title.includes(query);
            const matchesFilter = activeFilter === 'all' || status === activeFilter;

            card.style.display = (matchesSearch && matchesFilter) ? 'flex' : 'none';
        });
    }

    search.addEventListener('input', filterGrid);
    pills.forEach(p => p.addEventListener('click', () => {
        pills.forEach(btn => btn.classList.remove('active'));
        p.classList.add('active');
        filterGrid();
    }));
</script>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>