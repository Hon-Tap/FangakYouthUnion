<?php
/**
 * Main Homepage View - Enterprise Edition
 * Fangak Youth Union (FYU)
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$pageTitle = "Home - Fangak Youth Union";

// 1. INCLUDE GLOBAL HEADER Layout
include_once __DIR__ . "/../app/views/layouts/header.php";

// 2. DATABASE CONNECTION & DATA FETCHING
if (!isset($pdo) || !($pdo instanceof PDO)) {
    include_once __DIR__ . "/../app/config/db.php";
}

// Fallback path guard
if (!isset($baseUrl) || $baseUrl === '') {
    $baseUrl = '/';
}

$announcements = [];
$events        = [];

try {
    if (isset($pdo) && $pdo instanceof PDO) {
        // Fetch published announcements (max 3)
        $annStmt = $pdo->prepare("
            SELECT id, title, body, created_at 
            FROM announcements 
            WHERE is_published = 1 
            ORDER BY created_at DESC 
            LIMIT 3
        ");
        $annStmt->execute();
        $announcements = $annStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch upcoming events (max 3)
        $evtStmt = $pdo->prepare("
            SELECT id, title, image, location, event_date 
            FROM events 
            WHERE event_date >= CURDATE() 
            ORDER BY event_date ASC 
            LIMIT 3
        ");
        $evtStmt->execute();
        $events = $evtStmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Throwable $e) {
    error_log("Homepage Database Error: " . $e->getMessage());
}

// Community Engagement Data Grid
$engagements = [
    [
        'title'    => 'Fangak Chairperson & team distributing fishing nets to community elders',
        'category' => 'Support & Relief',
        'img'      => 'Fishing.jpg',
        'span'     => 'col-span-1 md:col-span-2 md:row-span-2'
    ],
    [
        'title'    => 'Emergency Response & Aid for Flood-Affected Communities',
        'category' => 'Disaster Relief',
        'img'      => 'donations.jpg',
        'span'     => 'col-span-1 row-span-1'
    ],
    [
        'title'    => 'Youth Assembly: Strategic Planning for Flood Interventions',
        'category' => 'Awareness & Advocacy',
        'img'      => 'youthunion.jpg',
        'span'     => 'col-span-1 row-span-1'
    ]
];

// Helper Formatter Functions
function formatDate($dateString) {
    return !empty($dateString) ? date('M d, Y', strtotime($dateString)) : '';
}

function getDay($dateString) {
    return !empty($dateString) ? date('d', strtotime($dateString)) : '';
}

function getMonth($dateString) {
    return !empty($dateString) ? date('M', strtotime($dateString)) : '';
}

function safeText($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function safeHtmlPreview($value) {
    return strip_tags(html_entity_decode((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
}
?>

<!-- ==========================================
     1. HERO SECTION
=========================================== -->
<section class="relative min-h-[92vh] flex items-center justify-center overflow-hidden bg-fyu-darker">
    <!-- Hero Background Image & Gradient Layering -->
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-t from-fyu-darker via-fyu-darker/70 to-fyu-darker/40 z-10"></div>
        <img src="<?= $baseUrl ?>images/FYU-LOGO.jpg" 
             alt="Fangak Community Background" 
             class="w-full h-full object-cover scale-105 transition-transform duration-[3000ms] hover:scale-100 filter brightness-[0.45] contrast-110">
    </div>

    <!-- Admin Quick Lock Indicator -->
    <div class="absolute top-8 right-8 z-30">
        <a href="<?= $baseUrl ?>admin/login.php" 
           title="Admin Portal Access"
           class="bg-white/10 backdrop-blur-md border border-white/20 text-white p-3 rounded-full hover:bg-fyu-gold hover:text-fyu-darker transition-all duration-300 shadow-md group">
            <i class="fa-solid fa-lock text-xs transition-transform group-hover:scale-110"></i>
        </a>
    </div>

    <!-- Main Hero Content -->
    <div class="relative z-20 container mx-auto px-6 text-center flex flex-col items-center py-20">
        <!-- Pill Badge -->
        <div class="inline-flex items-center gap-2.5 py-2 px-5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-xs font-semibold tracking-[0.25em] uppercase mb-8 text-fyu-gold shadow-lg"
             data-aos="fade-down">
            <span class="w-2 h-2 rounded-full bg-fyu-gold animate-ping"></span>
            <span>Unity • Innovation • Progress</span>
        </div>

        <!-- Headline -->
        <h1 class="text-5xl sm:text-6xl md:text-8xl font-serif font-bold mb-6 leading-[0.95] text-white tracking-tight" data-aos="zoom-in" data-aos-delay="100">
            Fangak <br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-fyu-gold via-amber-300 to-amber-500 italic">Youth Union</span>
        </h1>

        <!-- Subtitle -->
        <p class="text-base sm:text-lg md:text-xl text-gray-200 max-w-2xl mx-auto mb-12 font-light leading-relaxed drop-shadow-sm" data-aos="fade-up" data-aos-delay="200">
            Engineered for impact. Empowering the next generation through sustainable community development, grassroots innovation, and collective resilience.
        </p>

        <!-- CTA Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center w-full sm:w-auto" data-aos="fade-up" data-aos-delay="300">
            <a href="<?= $baseUrl ?>register.php" 
               class="px-8 py-4 bg-gradient-to-r from-fyu-gold to-amber-500 text-fyu-darker font-bold rounded-2xl hover:from-amber-400 hover:to-fyu-gold transition-all duration-300 shadow-xl shadow-fyu-gold/20 w-full sm:w-auto text-sm uppercase tracking-widest hover:-translate-y-1">
                Join the Movement
            </a>
            <a href="#engagement" 
               class="px-8 py-4 bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold rounded-2xl hover:bg-white/20 transition-all duration-300 w-full sm:w-auto text-sm uppercase tracking-widest hover:-translate-y-1">
                Explore Initiatives
            </a>
        </div>
    </div>

    <!-- Animated Down Scroll Indicator -->
    <div class="absolute bottom-6 z-20 hidden md:block animate-bounce">
        <a href="#engagement" class="text-white/60 hover:text-fyu-gold transition-colors">
            <i class="fa-solid fa-chevron-down text-lg"></i>
        </a>
    </div>
</section>

<!-- ==========================================
     2. ACTIVE INVOLVEMENT (BENTO GRID)
=========================================== -->
<section id="engagement" class="py-24 md:py-32 bg-gray-50">
    <div class="container mx-auto px-6 max-w-7xl">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-16" data-aos="fade-up">
            <div class="max-w-2xl">
                <div class="flex items-center gap-3 mb-3">
                    <span class="h-[2px] w-10 bg-fyu-primary"></span>
                    <h2 class="text-xs font-bold text-fyu-primary tracking-widest uppercase">Active Engagement</h2>
                </div>
                <h3 class="text-4xl md:text-5xl font-serif font-bold text-gray-900 leading-tight">
                    Actions Speaking <br><span class="italic text-gray-400 font-normal">Louder Than Words</span>
                </h3>
            </div>
            <p class="text-gray-600 mt-4 md:mt-0 md:max-w-xs text-left md:text-right text-sm leading-relaxed">
                Witness our community leaders stepping up to serve, support, and transform the region.
            </p>
        </div>

        <!-- Dynamic Responsive Grid Layout -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 auto-rows-[260px] md:auto-rows-[280px]">
            <?php foreach ($engagements as $index => $eng): ?>
                <div class="group relative rounded-3xl overflow-hidden cursor-pointer shadow-md hover:shadow-2xl transition-all duration-500 <?= $eng['span'] ?>" 
                     data-aos="fade-up" 
                     data-aos-delay="<?= $index * 150 ?>">
                    
                    <img src="<?= $baseUrl ?>images/<?= safeText($eng['img']) ?>" 
                         alt="<?= safeText($eng['title']) ?>" 
                         loading="lazy"
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                         onerror="this.src='https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&w=800&q=80'">

                    <!-- Subtle Dark Overlay Gradient -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent opacity-80 group-hover:opacity-95 transition-opacity duration-500"></div>
                    
                    <div class="absolute bottom-0 left-0 w-full p-6 md:p-8 transform translate-y-2 group-hover:translate-y-0 transition-transform duration-500">
                        <div class="mb-3">
                            <span class="inline-block px-3.5 py-1 bg-white/20 backdrop-blur-md text-white border border-white/30 text-[11px] font-semibold rounded-full uppercase tracking-wider">
                                <?= safeText($eng['category']) ?>
                            </span>
                        </div>
                        <h4 class="text-xl md:text-2xl font-serif text-white leading-tight font-bold">
                            <?= safeText($eng['title']) ?>
                        </h4>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- ==========================================
     3. UPCOMING EVENTS SECTION
=========================================== -->
<section class="py-24 bg-white relative overflow-hidden">
    <div class="container mx-auto px-6 max-w-7xl relative z-10">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-16" data-aos="fade-right">
            <div>
                <span class="text-xs font-bold text-fyu-primary tracking-widest uppercase mb-2 block">Mark Your Calendar</span>
                <h3 class="text-4xl md:text-5xl font-serif font-bold text-gray-900">Upcoming Events</h3>
            </div>
            <a href="<?= $baseUrl ?>events.php" class="mt-6 md:mt-0 px-6 py-3 rounded-full border border-gray-300 hover:border-fyu-primary hover:bg-fyu-primary hover:text-white transition-all font-semibold text-sm inline-flex items-center gap-2">
                <span>View All Events</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        <?php if (!empty($events)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($events as $index => $evt): ?>
                    <article class="group bg-white rounded-3xl border border-gray-100 p-4 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                        <div>
                            <div class="relative h-56 w-full rounded-2xl overflow-hidden mb-5">
                                <img src="<?= safeText($evt['image'] ?? $baseUrl . 'images/FYU-LOGO.jpg') ?>" 
                                     alt="<?= safeText($evt['title']) ?>" 
                                     loading="lazy"
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                     onerror="this.src='https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=800&q=80'">
                                <div class="absolute top-4 left-4 bg-white/95 backdrop-blur-sm px-3.5 py-1.5 rounded-xl text-center shadow-md">
                                    <span class="block text-xl font-black text-fyu-darker leading-none"><?= getDay($evt['event_date']) ?></span>
                                    <span class="block text-[10px] uppercase font-bold text-fyu-gold tracking-widest"><?= getMonth($evt['event_date']) ?></span>
                                </div>
                            </div>
                            <div class="px-2 pb-2">
                                <h4 class="text-lg font-bold text-gray-900 mb-3 group-hover:text-fyu-primary transition-colors line-clamp-2">
                                    <?= safeText($evt['title']) ?>
                                </h4>
                            </div>
                        </div>
                        <div class="px-2 pt-2 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500 font-medium">
                            <span class="flex items-center gap-1.5 text-fyu-primary">
                                <i class="fa-solid fa-location-dot"></i>
                                <?= safeText($evt['location'] ?? 'Fangak HQ') ?>
                            </span>
                            <span class="text-fyu-gold font-semibold">Join Event →</span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12 bg-gray-50 rounded-3xl border border-dashed border-gray-300">
                <i class="fa-solid fa-calendar-xmark text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-medium">No upcoming events scheduled at this moment.</p>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- ==========================================
     4. ANNOUNCEMENTS SECTION
=========================================== -->
<section class="py-24 md:py-32 bg-fyu-darker text-white relative">
    <div class="container mx-auto px-6 max-w-6xl relative z-10">
        
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-fyu-gold font-bold uppercase tracking-[0.2em] text-xs mb-3 block">Latest Transmissions</span>
            <h3 class="text-4xl md:text-5xl font-serif font-bold">Community Announcements</h3>
        </div>

        <?php if(!empty($announcements)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($announcements as $index => $ann): ?>
                    <article class="relative bg-white/5 border border-white/10 rounded-2xl p-8 hover:bg-white/10 transition-all duration-300 group flex flex-col justify-between" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                        <div>
                            <span class="text-[11px] font-mono text-fyu-gold uppercase tracking-widest block mb-3">
                                <i class="fa-regular fa-clock mr-1"></i> <?= formatDate($ann['created_at']) ?>
                            </span>
                            <h4 class="text-xl font-bold mb-3 group-hover:text-fyu-gold transition-colors leading-snug">
                                <?= safeText($ann['title']) ?>
                            </h4>
                            <p class="text-gray-300 text-sm leading-relaxed mb-6 line-clamp-3 font-light">
                                <?= safeHtmlPreview($ann['body']) ?>
                            </p>
                        </div>
                        <div>
                            <a href="<?= $baseUrl ?>announcements.php?id=<?= (int)$ann['id'] ?>" class="text-xs font-bold uppercase tracking-wider inline-flex items-center gap-2 text-fyu-gold group-hover:gap-3 transition-all">
                                <span>Read Full Update</span> 
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12 bg-white/5 rounded-2xl border border-white/10">
                <p class="text-gray-400">No official announcements posted recently.</p>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- ==========================================
     5. CORE OBJECTIVES / BLUEPRINT TIMELINE
=========================================== -->
<section class="py-24 md:py-32 bg-gray-50 relative overflow-hidden">
    <div class="container mx-auto px-6 relative z-10">
        
        <div class="text-center max-w-3xl mx-auto mb-20" data-aos="fade-up">
            <span class="text-xs font-bold text-fyu-primary tracking-widest uppercase mb-3 block">The Blueprint</span>
            <h3 class="text-4xl md:text-5xl font-serif font-bold text-gray-900">Our Core Pillars</h3>
        </div>

        <div class="max-w-5xl mx-auto space-y-12">
            
            <!-- Pillar 1 -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center" data-aos="fade-up">
                <div class="order-2 md:order-1 bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
                    <div class="w-12 h-12 rounded-2xl bg-fyu-primary/10 text-fyu-primary flex items-center justify-center text-xl font-bold mb-4">
                        <i class="fa-solid fa-people-group"></i>
                    </div>
                    <h4 class="text-2xl font-serif font-bold text-gray-900 mb-2">Unity & Social Cohesion</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">Fostering inclusive youth leadership, strengthening community bonds, and building a peaceful, collaborative generation across Fangak.</p>
                </div>
                <div class="order-1 md:order-2 h-64 rounded-3xl overflow-hidden shadow-sm">
                    <img src="<?= $baseUrl ?>images/Emergency.jpg" alt="Unity Pillar" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=800&q=80'">
                </div>
            </div>

            <!-- Pillar 2 -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center" data-aos="fade-up">
                <div class="h-64 rounded-3xl overflow-hidden shadow-sm">
                    <img src="<?= $baseUrl ?>images/fangak.jpg" alt="Innovation Pillar" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=800&q=80'">
                </div>
                <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
                    <div class="w-12 h-12 rounded-2xl bg-fyu-gold/15 text-fyu-gold flex items-center justify-center text-xl font-bold mb-4">
                        <i class="fa-solid fa-lightbulb"></i>
                    </div>
                    <h4 class="text-2xl font-serif font-bold text-gray-900 mb-2">Innovation & Development</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">Implementing sustainable solutions, agricultural technology initiatives, and climate resilience projects for long-term growth.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ==========================================
     6. CURRENT INITIATIVES PORTFOLIO
=========================================== -->
<section class="py-24 md:py-32 bg-white">
    <div class="container mx-auto px-6 max-w-7xl">
        
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-xs font-bold text-fyu-primary tracking-widest uppercase mb-2 block">Driving Impact</span>
            <h3 class="text-4xl md:text-5xl font-serif font-bold text-gray-900">Current Projects</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php
            $projects = [
                [
                    'img'   => 'FYU@Pulita.jpg',
                    'title' => 'Pulita Leadership Forum',
                    'desc'  => 'Engaging local community leaders in Pulita to address critical development priorities through cooperative youth action.'
                ],
                [
                    'img'   => 'FYU-donates.jpg',
                    'title' => 'Emergency Flood Relief',
                    'desc'  => 'Distributing food items, shelter kits, and clean water supplies to vulnerable families affected by seasonal flooding.'
                ],
                [
                    'img'   => 'YouthInAction.jpg',
                    'title' => 'Sustainable Livelihoods',
                    'desc'  => 'Empowering youth through vocational training, agricultural equipment distribution, and micro-entrepreneurship.'
                ]
            ];

            foreach ($projects as $idx => $proj) :
            ?>
                <div class="group cursor-pointer bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-md hover:shadow-2xl transition-all duration-500"
                     onclick="openProjectModal('<?= safeText($proj['title']) ?>', '<?= safeText($proj['desc']) ?>', '<?= $baseUrl ?>images/<?= safeText($proj['img']) ?>')"
                     data-aos="fade-up"
                     data-aos-delay="<?= $idx * 150 ?>">

                    <div class="relative h-64 overflow-hidden">
                        <img src="<?= $baseUrl ?>images/<?= safeText($proj['img']) ?>"
                             alt="<?= safeText($proj['title']) ?>"
                             loading="lazy"
                             class="w-full h-full object-cover transition duration-700 group-hover:scale-105"
                             onerror="this.src='https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=800&q=80'">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                        <div class="absolute top-4 right-4 w-10 h-10 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                        </div>
                    </div>

                    <div class="p-6">
                        <h4 class="text-xl font-serif font-bold text-gray-900 mb-2 group-hover:text-fyu-primary transition-colors">
                            <?= safeText($proj['title']) ?>
                        </h4>
                        <p class="text-gray-500 text-sm leading-relaxed line-clamp-2">
                            <?= safeText($proj['desc']) ?>
                        </p>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-16 text-center" data-aos="fade-up">
            <a href="<?= $baseUrl ?>project.php"
               class="inline-flex items-center gap-2 font-bold text-fyu-primary text-base hover:text-fyu-darker transition-colors border-b-2 border-fyu-primary pb-1">
                <span>View Complete Portfolio</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

    </div>
</section>

<!-- ==========================================
     7. CALL TO ACTION BANNER
=========================================== -->
<section class="py-24 bg-fyu-dark text-white text-center relative overflow-hidden">
    <div class="container mx-auto px-6 max-w-4xl relative z-10" data-aos="zoom-in">
        <h3 class="text-4xl md:text-6xl font-serif font-bold mb-6">Be the Catalyst for Change</h3>
        <p class="text-lg text-gray-200 max-w-2xl mx-auto mb-10 font-light leading-relaxed">
            Join an active network of youth leaders and change-makers committed to empowering communities across South Sudan.
        </p>
        <a href="<?= $baseUrl ?>register.php" 
           class="inline-flex items-center gap-3 px-10 py-4 bg-fyu-gold text-fyu-darker font-bold rounded-full text-base hover:bg-amber-400 hover:shadow-xl hover:shadow-fyu-gold/20 hover:-translate-y-0.5 transition-all duration-300">
            <span>Register Now</span>
            <i class="fa-solid fa-user-plus text-xs"></i>
        </a>
    </div>
</section>

<!-- ==========================================
     8. DYNAMIC PROJECT MODAL
=========================================== -->
<div id="projectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-300" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-fyu-darker/80 backdrop-blur-sm" onclick="closeProjectModal()"></div>

    <div id="modalContent" class="bg-white rounded-3xl w-full max-w-3xl overflow-hidden shadow-2xl transform scale-95 opacity-0 transition-all duration-300 relative z-10 flex flex-col md:flex-row">
        <button onclick="closeProjectModal()" class="absolute top-4 right-4 bg-white/80 hover:bg-white text-gray-800 p-2 w-9 h-9 flex items-center justify-center rounded-full transition-colors z-20 shadow-md">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="md:w-1/2 h-64 md:h-auto relative bg-gray-100">
            <img id="modalImg" src="" alt="Project Preview" class="w-full h-full object-cover">
        </div>

        <div class="p-8 md:w-1/2 flex flex-col justify-center bg-white">
            <span class="text-xs font-bold text-fyu-primary uppercase tracking-widest mb-2">Initiative Insight</span>
            <h4 id="modalTitle" class="text-2xl font-serif font-bold text-gray-900 mb-4 leading-tight"></h4>
            <p id="modalDesc" class="text-gray-600 mb-8 text-sm leading-relaxed font-light"></p>
            <a href="<?= $baseUrl ?>register.php" class="inline-block text-center px-6 py-3 bg-fyu-primary text-white font-bold text-sm rounded-xl hover:bg-fyu-dark transition-colors shadow-md">
                Get Involved Today
            </a>
        </div>
    </div>
</div>

<!-- Floating Action Button -->
<a href="<?= $baseUrl ?>register.php" class="fixed bottom-6 right-6 z-40 bg-fyu-gold text-fyu-darker w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:bg-amber-400 hover:scale-105 transition-all duration-300 group md:w-auto md:px-5">
    <i class="fa-solid fa-handshake text-lg md:mr-2"></i>
    <span class="hidden md:inline font-bold text-sm">Join Network</span>
</a>

<!-- ==========================================
     9. FOOTER & SCRIPT INITS
=========================================== -->
<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Initialize AOS Animations safely if library exists
    if (typeof AOS !== 'undefined') {
        AOS.init({
            once: true,
            offset: 40,
            duration: 800,
            easing: 'ease-out-cubic'
        });
    }

    // Modal Control Bindings
    const modal = document.getElementById('projectModal');
    const modalContent = document.getElementById('modalContent');
    const modalTitle = document.getElementById('modalTitle');
    const modalDesc = document.getElementById('modalDesc');
    const modalImg = document.getElementById('modalImg');

    window.openProjectModal = (title, desc, imgSrc) => {
        modalTitle.textContent = title;
        modalDesc.textContent = desc;
        modalImg.src = imgSrc;

        modal.classList.remove('opacity-0', 'pointer-events-none');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
        document.body.style.overflow = 'hidden';
    };

    window.closeProjectModal = () => {
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('opacity-0', 'pointer-events-none');
            document.body.style.overflow = '';
        }, 300);
    };

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeProjectModal();
    });
});
</script>