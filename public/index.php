<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "\n";

// 1. DATA LOGIC -------------------------------------------------------

$pageTitle = "Home - Fangak Youth Union";

// Include Header
include_once __DIR__ . "/../app/views/layouts/header.php";

// Ensure DB Connection
if (!isset($pdo) || !($pdo instanceof PDO)) {
    include_once __DIR__ . "/../app/config/db.php";
}

// Fallback only if header does not provide it
if (!isset($baseUrl) || $baseUrl === '') {
    $baseUrl = '/';
}

// Initialize arrays
$announcements = [];
$events = [];

try {
    if (isset($pdo) && $pdo instanceof PDO) {
        // A. FETCH ANNOUNCEMENTS (Newest first, max 3)
        $annStmt = $pdo->prepare("
            SELECT *
            FROM announcements
            WHERE is_published = 1
            ORDER BY created_at DESC
            LIMIT 3
        ");
        $annStmt->execute();
        $announcements = $annStmt->fetchAll(PDO::FETCH_ASSOC);

        // B. FETCH EVENTS (Soonest date first, max 3, upcoming only)
        $evtStmt = $pdo->prepare("
            SELECT *
            FROM events
            WHERE event_date >= CURDATE()
            ORDER BY event_date ASC
            LIMIT 3
        ");
        $evtStmt->execute();
        $events = $evtStmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Throwable $e) {
    error_log("Homepage Query Error: " . $e->getMessage());
}

// Youth Engagement Images from assets/images folder
$engagements = [
    [
        'title' => 'Fangak chair person with the team distributing fishing nets to the elderly in the community',
        'category' => 'Support & Relief',
        'img' => 'Fishing.jpg',
        'span' => 'col-span-1 md:col-span-2 row-span-2' 
    ],
    [
        'title' => 'Emergency Response for the Floods',
        'category' => 'Support & Relief',
        'img' => 'donations.jpg',
        'span' => 'col-span-1 row-span-1'
    ],
    [
        'title' => 'Fangak youth holding a meeting with the community to discuss emergency response to the floods',
        'category' => 'Awareness & Advocacy',
        'img' => 'youthunion.jpg',
        'span' => 'col-span-1 row-span-1'
    ]
];
// Helper Functions
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

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    fyu: {
                        dark: '#145233',
                        primary: '#1f7a4b',
                        light: '#3aa76a',
                        accent: '#e6f4ea',
                    }
                },
                fontFamily: {
                    sans: ['Poppins', 'sans-serif'],
                    serif: ['Old Standard TT', 'serif'],
                },
                boxShadow: {
                    'soft': '0 10px 40px -10px rgba(0,0,0,0.08)',
                    'float': '0 20px 40px -10px rgba(31, 122, 75, 0.15)',
                    'glow': '0 0 20px rgba(58, 167, 106, 0.4)',
                },
                animation: {
                    'blob': 'blob 7s infinite',
                },
                keyframes: {
                    blob: {
                        '0%': { transform: 'translate(0px, 0px) scale(1)' },
                        '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                        '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                        '100%': { transform: 'translate(0px, 0px) scale(1)' },
                    }
                }
            }
        }
    }
</script>

<style>
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f9fafb; }
    ::-webkit-scrollbar-thumb { background: #1f7a4b; border-radius: 4px; }

    .hero-overlay {
        background: radial-gradient(circle at center, rgba(31,122,75,0.3) 0%, rgba(20,82,51,0.95) 80%, #0a291a 100%);
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Custom elegant timeline styling */
    .timeline-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 20px;
        height: 100%;
        width: 2px;
        background: linear-gradient(to bottom, transparent, #3aa76a, #1f7a4b, transparent);
    }
    @media (min-width: 768px) {
        .timeline-container::before {
            left: 50%;
            transform: translateX(-50%);
        }
    }

    .timeline-dot {
        box-shadow: 0 0 0 4px rgba(58, 167, 106, 0.2);
        transition: all 0.3s ease;
    }
    .timeline-item:hover .timeline-dot {
        box-shadow: 0 0 0 8px rgba(58, 167, 106, 0.4);
        transform: scale(1.2);
    }

    .text-gradient {
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-image: linear-gradient(90deg, #3aa76a, #e6f4ea);
    }
</style>

<section class="relative h-screen flex items-center justify-center overflow-hidden bg-[#0a261a]">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-[#0a261a]/70 z-10"></div> <img src="<?= $baseUrl ?>images/FYU-LOGO.jpg" 
             alt="Fangak Community Background" 
             class="w-full h-full object-cover scale-105 transition-transform duration-[2s] hover:scale-100"
             style="filter: brightness(0.6) saturate(1.2);"> </div>

    <div class="absolute top-8 right-8 z-30">
        <a href="<?= $baseUrl ?>admin/login.php" 
           class="bg-white/10 backdrop-blur-md border border-white/20 text-white px-5 py-2 rounded-full text-xs font-bold tracking-widest uppercase hover:bg-white hover:text-[#0a261a] transition-all duration-300 flex items-center gap-2">
            <i class="fa-solid fa-lock text-[10px]"></i> Admin
        </a>
    </div>

    <div class="relative z-20 container mx-auto px-6 text-center flex flex-col items-center">
        <div class="inline-flex items-center gap-3 py-1.5 px-4 rounded-full bg-white/5 border border-white/10 text-[10px] md:text-xs font-bold tracking-[0.25em] uppercase mb-8 text-white/80">
            <span class="w-1.5 h-1.5 rounded-full bg-[#c59d5f] animate-pulse"></span>
            Unity • Innovation • Progress
        </div>

        <h1 class="text-5xl md:text-[90px] font-serif font-bold mb-6 leading-[0.9] text-white">
            Fangak <br>
            <span class="text-[#c59d5f] italic">Youth Union</span>
        </h1>

        <p class="text-base md:text-xl text-gray-200 max-w-xl mx-auto mb-12 font-light leading-relaxed">
            Engineered for impact. Empowering the next generation through sustainable 
            community development and collective brilliance.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center w-full">
            <a href="<?= $baseUrl ?>register.php" 
               class="px-10 py-4 bg-[#c59d5f] text-white font-bold rounded-2xl hover:bg-[#b08a53] transition-all duration-300 shadow-xl w-full sm:w-auto text-sm uppercase tracking-widest hover:translate-y-[-2px]">
                Join the Movement
            </a>
            <a href="#engagement" 
               class="px-10 py-4 bg-white/10 backdrop-blur-sm border border-white/20 text-white font-bold rounded-2xl hover:bg-white/20 transition-all duration-300 w-full sm:w-auto text-sm uppercase tracking-widest">
                Explore Initiatives
            </a>
        </div>
    </div>

    <div class="absolute bottom-10 z-20">
        <div class="text-[9px] font-black uppercase tracking-[0.3em] text-white/50 mb-3 rotate-180" style="writing-mode: vertical-rl;">Scroll</div>
    </div>
</section>

<section id="engagement" class="py-32 bg-[#fafafa]">
    <div class="container mx-auto px-6">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16" data-aos="fade-up">
            <div class="max-w-2xl">
                <div class="flex items-center gap-4 mb-4">
                    <div class="h-[1px] w-12 bg-fyu-primary"></div>
                    <h2 class="text-sm font-bold text-fyu-primary tracking-widest uppercase">Active Involvement</h2>
                </div>
                <h3 class="text-4xl md:text-5xl font-serif font-bold text-gray-900 leading-tight">
                    Actions Speaking <br><span class="italic text-gray-400">Louder Than Words</span>
                </h3>
            </div>
            <p class="text-gray-500 mt-6 md:mt-0 md:max-w-sm text-right hidden md:block">
                Witness our community leaders stepping up to serve and connect across the region.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 md:grid-rows-2 gap-4 h-auto md:h-[600px]">
            <?php foreach ($engagements as $index => $eng): ?>
                <div class="group relative rounded-3xl overflow-hidden cursor-pointer shadow-soft <?= $eng['span'] ?> h-80 md:h-auto" 
                     data-aos="fade-up" 
                     data-aos-delay="<?= $index * 150 ?>">
                    
                    <img src="<?= $baseUrl ?>assets/images/<?= safeText($eng['img']) ?>" 
                         alt="<?= safeText($eng['title']) ?>" 
                         class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105"
                         onerror="this.src='<?= $baseUrl ?>assets/images/suffer.jpg'">

                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-70 group-hover:opacity-90 transition-opacity duration-500"></div>
                    
                    <div class="absolute bottom-0 left-0 w-full p-8 md:p-10 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                        <div class="overflow-hidden mb-3">
                            <span class="inline-block px-4 py-1.5 bg-white/20 backdrop-blur-md text-white border border-white/30 text-xs font-bold rounded-full transform translate-y-full group-hover:translate-y-0 transition-transform duration-500 delay-100">
                                <?= safeText($eng['category']) ?>
                            </span>
                        </div>
                        <h4 class="text-2xl md:text-3xl font-serif text-white mb-2 leading-tight">
                            <?= safeText($eng['title']) ?>
                        </h4>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<section class="py-24 bg-white relative">
    <div class="absolute top-0 right-0 w-1/3 h-full bg-fyu-accent/30 rounded-bl-[150px] z-0"></div>
    
    <div class="container mx-auto px-6 relative z-10">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16" data-aos="fade-right">
            <div>
                <h2 class="text-sm font-bold text-fyu-primary tracking-widest uppercase mb-2">Mark Your Calendar</h2>
                <h3 class="text-4xl md:text-5xl font-serif font-bold text-gray-900">Upcoming Events</h3>
            </div>
            <a href="<?= $baseUrl ?>events.php" class="group mt-6 md:mt-0 inline-flex items-center gap-3 text-fyu-dark font-bold hover:text-fyu-primary transition-colors bg-white px-6 py-3 rounded-full shadow-[0_5px_15px_rgba(0,0,0,0.05)] border border-gray-100">
                Full Calendar 
                <span class="w-8 h-8 rounded-full bg-fyu-accent flex items-center justify-center group-hover:bg-fyu-primary group-hover:text-white transition-colors">
                    <i class="fa-solid fa-arrow-right text-sm transform -rotate-45 group-hover:rotate-0 transition-transform"></i>
                </span>
            </a>
        </div>

        <?php if (empty($events)): ?>
            <div class="relative overflow-hidden rounded-[2.5rem] border border-dashed border-gray-300 bg-gradient-to-br from-gray-50 to-white p-16 text-center shadow-sm" data-aos="fade-up">
                <div class="absolute -top-20 -right-20 h-64 w-64 rounded-full bg-fyu-accent/40 blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-fyu-light/10 blur-3xl"></div>
                
                <div class="relative z-10 flex flex-col items-center">
                    <div class="mb-8 relative flex h-28 w-28 items-center justify-center rounded-3xl bg-white shadow-soft rotate-3 hover:rotate-0 transition-transform duration-500">
                        <div class="absolute inset-0 bg-fyu-primary/5 rounded-3xl transform -rotate-6 z-0"></div>
                        <i class="fa-regular fa-calendar-xmark text-5xl text-fyu-primary/70 relative z-10"></i>
                    </div>
                    <h4 class="mb-3 text-3xl font-serif font-bold text-gray-800">No events on the horizon</h4>
                    <p class="max-w-md text-gray-500 mb-8 leading-relaxed">We're busy planning our next big gathering. Check back soon for new workshops, community clean-ups, and summits.</p>
                    <button class="px-8 py-3 bg-fyu-dark text-white rounded-full font-medium hover:bg-fyu-primary transition-colors shadow-lg shadow-fyu-primary/20">
                        Notify Me
                    </button>
                </div>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($events as $index => $evt): ?>
                    <article class="group relative bg-white rounded-3xl p-3 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-float transition-all duration-500 border border-gray-100" data-aos="fade-up" data-aos-delay="<?= $index * 150 ?>">
                        <div class="relative h-64 rounded-2xl overflow-hidden mb-6">
                            <?php if (!empty($evt['image'])): ?>
                                <img src="<?= $baseUrl ?>images/events/<?= safeText($evt['image']) ?>" alt="<?= safeText($evt['title'] ?? '') ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-fyu-dark to-fyu-primary flex items-center justify-center">
                                    <i class="fa-solid fa-calendar-day text-5xl text-white/20"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-md rounded-2xl p-3 text-center shadow-lg min-w-[75px] border border-white/50 transform group-hover:-translate-y-1 transition-transform">
                                <span class="block text-3xl font-black text-fyu-dark leading-none"><?= getDay($evt['event_date'] ?? '') ?></span>
                                <span class="block text-xs uppercase font-bold text-fyu-primary mt-1"><?= getMonth($evt['event_date'] ?? '') ?></span>
                            </div>
                        </div>

                        <div class="px-5 pb-5">
                            <div class="flex items-center text-xs text-gray-400 font-bold mb-3 uppercase tracking-wider">
                                <i class="fa-solid fa-location-dot text-fyu-light mr-2"></i>
                                <?= safeText($evt['location'] ?? 'Fangak HQ') ?>
                            </div>
                            <h3 class="text-2xl font-serif font-bold text-gray-900 mb-4 group-hover:text-fyu-primary transition-colors line-clamp-2">
                                <?= safeText($evt['title'] ?? '') ?>
                            </h3>
                            <a href="<?= $baseUrl ?>events.php?id=<?= (int)($evt['id'] ?? 0) ?>" class="inline-flex items-center text-sm font-bold text-gray-900 group-hover:text-fyu-primary transition-colors">
                                View Details 
                                <span class="ml-2 w-6 h-6 rounded-full bg-gray-100 group-hover:bg-fyu-accent flex items-center justify-center transition-colors">
                                    <i class="fa-solid fa-arrow-right-long text-xs transform group-hover:translate-x-0.5 transition-transform"></i>
                                </span>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section id="announcements" class="py-32 bg-[#0a110d] text-white relative overflow-hidden border-t border-white/5">
    <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-fyu-primary/10 rounded-full filter blur-[100px] transform translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-fyu-light/5 rounded-full filter blur-[80px] transform -translate-x-1/2 translate-y-1/2"></div>
    
    <div class="container mx-auto px-6 relative z-10">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16" data-aos="fade-up">
            <div>
                <h2 class="text-sm font-bold text-fyu-light tracking-widest uppercase mb-2 flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-fyu-light animate-pulse"></span> Stay Informed
                </h2>
                <h3 class="text-4xl md:text-5xl font-serif font-bold text-white">Latest Transmissions</h3>
            </div>
            <a href="<?= $baseUrl ?>announcements.php" class="mt-6 md:mt-0 px-6 py-3 bg-white/5 border border-white/10 rounded-full hover:bg-white hover:text-black transition-all duration-300 text-sm font-bold backdrop-blur-md">
                View Archive
            </a>
        </div>

        <?php if (empty($announcements)): ?>
            <div class="relative overflow-hidden rounded-[2.5rem] border border-white/10 bg-white/5 backdrop-blur-sm p-16 text-center" data-aos="zoom-in">
                <i class="fa-solid fa-satellite-dish text-6xl text-white/10 mb-6 block"></i>
                <h4 class="mb-2 text-2xl font-serif font-bold text-white">The line is quiet</h4>
                <p class="text-white/40">No official announcements have been broadcasted yet. Stay tuned.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php foreach ($announcements as $index => $ann): ?>
                    <article class="relative bg-white/5 border border-white/10 rounded-3xl p-8 hover:bg-white/10 backdrop-blur-md transition-all duration-500 group flex flex-col h-full overflow-hidden" data-aos="fade-up" data-aos-delay="<?= $index * 150 ?>">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-fyu-light/20 filter blur-3xl rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                        <div class="flex items-center text-xs text-fyu-light mb-6 font-mono tracking-wider">
                            <i class="fa-regular fa-clock mr-2"></i>
                            <?= formatDate($ann['created_at'] ?? '') ?>
                        </div>

                        <h3 class="text-2xl font-serif font-bold text-white mb-4 group-hover:text-fyu-light transition-colors line-clamp-2">
                            <?= safeText($ann['title'] ?? '') ?>
                        </h3>

                        <div class="text-white/60 mb-8 line-clamp-3 text-sm leading-relaxed flex-grow font-light">
                            <?= safeHtmlPreview($ann['body'] ?? '') ?>
                        </div>

                        <a href="<?= $baseUrl ?>announcements.php?id=<?= (int)($ann['id'] ?? 0) ?>" class="mt-auto group/btn inline-flex items-center text-white font-bold text-sm w-max">
                            Read Article 
                            <span class="ml-3 h-[1px] w-8 bg-fyu-light group-hover/btn:w-12 transition-all duration-300"></span>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="py-32 bg-gray-50 relative overflow-hidden">
    <div class="container mx-auto px-6 relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-20" data-aos="fade-up">
            <h2 class="text-sm font-bold text-fyu-primary tracking-widest uppercase mb-4">The Blueprint</h2>
            <h3 class="text-4xl md:text-5xl font-serif font-bold text-gray-900">Our Core Objectives</h3>
        </div>

        <div class="relative timeline-container max-w-5xl mx-auto">
            
            <div class="mb-12 flex justify-between items-center w-full right-timeline timeline-item pl-10 md:pl-0" data-aos="fade-right">
                <div class="order-1 md:w-5/12 hidden md:block text-right pr-8">
                    <h4 class="text-3xl font-serif font-bold text-fyu-dark">Unity</h4>
                    <p class="text-gray-500 mt-2">Fostering strong youth leadership to build an empowered, indivisible generation.</p>
                </div>
                <div class="z-20 absolute left-0 md:left-1/2 transform -translate-x-1/2 w-10 h-10 flex items-center justify-center bg-white border-4 border-gray-50 rounded-full timeline-dot">
                    <i class="fa-solid fa-people-group text-fyu-primary text-sm"></i>
                </div>
                <div class="order-1 w-full md:w-5/12 bg-white rounded-3xl shadow-soft p-8 border border-gray-100 relative">
                    <h4 class="text-2xl font-serif font-bold text-fyu-dark md:hidden mb-2">Unity</h4>
                    <p class="text-gray-500 md:hidden mb-4">Fostering strong youth leadership to build an empowered, indivisible generation.</p>
                    <div class="w-full h-40 bg-fyu-accent rounded-xl flex items-center justify-center">
                         <img src="assets/images/Emergency.jpg" alt="Unity" class="w-full h-full object-cover rounded-xl opacity-80 mix-blend-multiply">
                    </div>
                </div>
            </div>

            <div class="mb-12 flex justify-between items-center w-full left-timeline timeline-item pl-10 md:pl-0" data-aos="fade-left">
                <div class="order-1 w-full md:w-5/12 bg-white rounded-3xl shadow-soft p-8 border border-gray-100">
                    <h4 class="text-2xl font-serif font-bold text-fyu-dark md:hidden mb-2">Innovation</h4>
                    <p class="text-gray-500 md:hidden mb-4">Implementing creative, sustainable solutions driving real technological progress.</p>
                    <div class="w-full h-40 bg-blue-50 rounded-xl flex items-center justify-center">
                         <img src="assets/images/fangak.jpg" alt="Innovation" class="w-full h-full object-cover rounded-xl opacity-80 mix-blend-multiply">
                    </div>
                </div>
                <div class="z-20 absolute left-0 md:left-1/2 transform -translate-x-1/2 w-10 h-10 flex items-center justify-center bg-white border-4 border-gray-50 rounded-full timeline-dot">
                    <i class="fa-solid fa-lightbulb text-fyu-primary text-sm"></i>
                </div>
                <div class="order-1 md:w-5/12 hidden md:block pl-8">
                    <h4 class="text-3xl font-serif font-bold text-fyu-dark">Innovation</h4>
                    <p class="text-gray-500 mt-2">Implementing creative, sustainable solutions driving real technological and social progress.</p>
                </div>
            </div>

            <div class="mb-12 flex justify-between items-center w-full right-timeline timeline-item pl-10 md:pl-0" data-aos="fade-right">
                <div class="order-1 md:w-5/12 hidden md:block text-right pr-8">
                    <h4 class="text-3xl font-serif font-bold text-fyu-dark">Service</h4>
                    <p class="text-gray-500 mt-2">Driving tangible change through dedicated, hands-on grassroots development.</p>
                </div>
                <div class="z-20 absolute left-0 md:left-1/2 transform -translate-x-1/2 w-10 h-10 flex items-center justify-center bg-white border-4 border-gray-50 rounded-full timeline-dot">
                    <i class="fa-solid fa-hand-holding-heart text-fyu-primary text-sm"></i>
                </div>
                <div class="order-1 w-full md:w-5/12 bg-white rounded-3xl shadow-soft p-8 border border-gray-100">
                    <h4 class="text-2xl font-serif font-bold text-fyu-dark md:hidden mb-2">Service</h4>
                    <p class="text-gray-500 md:hidden mb-4">Driving tangible change through dedicated, hands-on grassroots development.</p>
                    <div class="w-full h-40 bg-rose-50 rounded-xl flex items-center justify-center">
                        <img src="assets/images/mawichinaction.jpg" alt="Service" class="w-full h-full object-cover rounded-xl opacity-80 mix-blend-multiply">
                    </div>
                </div>
            </div>

            <div class="mb-12 flex justify-between items-center w-full left-timeline timeline-item pl-10 md:pl-0" data-aos="fade-left">
                <div class="order-1 w-full md:w-5/12 bg-white rounded-3xl shadow-soft p-8 border border-gray-100">
                    <h4 class="text-2xl font-serif font-bold text-fyu-dark md:hidden mb-2">Education</h4>
                    <p class="text-gray-500 md:hidden mb-4">Breaking barriers through comprehensive education & mentorship programs.</p>
                    <div class="w-full h-40 bg-amber-50 rounded-xl flex items-center justify-center">
                        <img src="assets/images/action.jpeg" alt="Education" class="w-full h-full object-cover rounded-xl opacity-80 mix-blend-multiply">
                    </div>
                </div>
                <div class="z-20 absolute left-0 md:left-1/2 transform -translate-x-1/2 w-10 h-10 flex items-center justify-center bg-white border-4 border-gray-50 rounded-full timeline-dot">
                    <i class="fa-solid fa-graduation-cap text-fyu-primary text-sm"></i>
                </div>
                <div class="order-1 md:w-5/12 hidden md:block pl-8">
                    <h4 class="text-3xl font-serif font-bold text-fyu-dark">Education</h4>
                    <p class="text-gray-500 mt-2">Breaking barriers through comprehensive education & mentorship programs.</p>
                </div>
            </div>
            
        </div>
    </div>
</section>

<section class="py-32 bg-white">

    <div class="container mx-auto px-6">

        <div class="text-center mb-20" data-aos="fade-up">

            <h2 class="text-sm font-bold text-fyu-primary tracking-widest uppercase mb-2">Driving Impact</h2>

            <h3 class="text-4xl md:text-5xl font-serif font-bold text-gray-900">Current Initiatives</h3>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

            <?php

            $projects = [

                [
                    'img' => 'FYU@Pulita.jpg',
                    'title' => 'Chairman\'s Initiative with community leaders in Pulita',
                    'desc' => 'Engaging local leaders to identify and address critical community needs through collaborative projects.'
                ],

                [
                    'img' => 'FYU-donates.jpg',
                    'title' => 'Flood Response',
                    'desc' => 'Supporting families displaced by floods with emergency relief and rehabilitation strategies.'
                ],

                [
                    'img' => 'YouthInAction.jpg',
                    'title' => 'Livelihood Programs',
                    'desc' => 'Youth empowerment through sustainable livelihood initiatives.'
                ],

            ];

            foreach ($projects as $idx => $proj) :
            ?>

                <div class="group cursor-pointer block"
                     onclick="openProjectModal(
                        '<?= safeText($proj['title']) ?>',
                        '<?= safeText($proj['desc']) ?>',
                        '<?= $baseUrl ?>images/<?= safeText($proj['img']) ?>'
                     )"
                     data-aos="fade-up"
                     data-aos-delay="<?= $idx * 150 ?>">

                    <div class="relative overflow-hidden rounded-[2rem] aspect-[4/5] mb-6 shadow-soft group-hover:shadow-float transition-all duration-500">

                        <img
                            src="<?= $baseUrl ?>images/<?= safeText($proj['img']) ?>"
                            alt="<?= safeText($proj['title']) ?>"
                            class="w-full h-full object-cover transition duration-1000 group-hover:scale-105"
                            onerror="this.src='https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'"
                        >

                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                        <div class="absolute bottom-0 left-0 w-full p-8 text-white">

                            <h4 class="text-2xl font-serif font-bold mb-2">
                                <?= safeText($proj['title']) ?>
                            </h4>

                            <div class="h-0 overflow-hidden group-hover:h-20 transition-all duration-500 ease-in-out">

                                <p class="text-sm text-white/80 leading-relaxed pt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">
                                    <?= safeText($proj['desc']) ?>
                                </p>

                            </div>

                        </div>

                        <div class="absolute top-6 right-6 w-12 h-12 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transform translate-x-4 group-hover:translate-x-0 transition-all duration-500">

                            <i class="fa-solid fa-arrow-up-right-from-square"></i>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

        <div class="mt-16 text-center" data-aos="fade-up">

            <a href="<?= $baseUrl ?>projects.php"
               class="inline-flex items-center font-bold text-fyu-primary text-lg hover:text-fyu-dark transition-colors border-b-2 border-fyu-primary pb-1">

                View Complete Portfolio
                <i class="fa-solid fa-arrow-right ml-2"></i>

            </a>

        </div>

    </div>

</section>
<section class="py-32 bg-fyu-dark text-white text-center relative overflow-hidden">
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[1000px] h-[1000px] border border-white/5 rounded-full animate-[spin_80s_linear_infinite] pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] border border-fyu-light/10 rounded-full animate-[spin_60s_linear_infinite_reverse] pointer-events-none"></div>

    <div class="container mx-auto px-6 relative z-10" data-aos="zoom-in">
        <h2 class="text-5xl md:text-7xl font-serif font-bold mb-8 drop-shadow-2xl">Be the Catalyst</h2>
        <p class="text-xl text-white/70 max-w-2xl mx-auto mb-12 font-light">Join an elite network of change-makers. Empower youth, transform communities, and engineer the future.</p>
        <a href="<?= $baseUrl ?>register.php" class="inline-block px-12 py-5 bg-white text-fyu-dark font-bold rounded-full text-lg shadow-[0_0_30px_rgba(255,255,255,0.2)] hover:shadow-[0_0_50px_rgba(255,255,255,0.4)] hover:scale-105 transition-all duration-500">
            Apply to Join
        </a>
    </div>
</section>

<div id="projectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-500" aria-hidden="true">
    <div class="absolute inset-0 bg-fyu-dark/80 backdrop-blur-md" onclick="closeProjectModal()"></div>

    <div class="bg-white rounded-[2rem] w-full max-w-4xl overflow-hidden shadow-2xl transform scale-95 opacity-0 transition-all duration-500 relative z-10 flex flex-col md:flex-row" id="modalContent">
        <button onclick="closeProjectModal()" class="absolute top-4 right-4 bg-white/50 hover:bg-white text-fyu-dark p-2 w-10 h-10 flex items-center justify-center rounded-full transition-colors z-20 shadow-sm backdrop-blur-md border border-gray-200">
            <i class="fa-solid fa-times"></i>
        </button>

        <div class="md:w-1/2 h-64 md:h-auto relative">
            <img id="modalImg" src="" alt="Project" class="w-full h-full object-cover absolute inset-0">
        </div>

        <div class="p-10 md:w-1/2 flex flex-col justify-center bg-gray-50">
            <span class="text-xs font-bold text-fyu-primary uppercase tracking-widest mb-3 flex items-center gap-2">
                <span class="w-8 h-[1px] bg-fyu-primary"></span> Initiative Insight
            </span>
            <h3 id="modalTitle" class="text-4xl font-serif font-bold text-gray-900 mb-6 leading-tight"></h3>
            <p id="modalDesc" class="text-gray-500 mb-10 leading-relaxed text-lg font-light"></p>
            <a href="<?= $baseUrl ?>register.php" class="inline-block text-center px-8 py-4 bg-fyu-dark text-white font-bold rounded-full hover:bg-fyu-primary transition-colors shadow-lg shadow-fyu-dark/20 w-max">
                Support This Cause
            </a>
        </div>
    </div>
</div>

<a href="<?= $baseUrl ?>register.php" class="fixed bottom-8 right-8 z-40 bg-fyu-primary text-white w-14 h-14 rounded-full flex items-center justify-center shadow-[0_0_20px_rgba(31,122,75,0.4)] hover:bg-fyu-dark hover:scale-110 transition-all duration-500 group overflow-hidden md:w-auto md:px-6">
    <i class="fa-solid fa-handshake group-hover:rotate-12 transition-transform duration-300 md:mr-2"></i>
    <span class="hidden md:inline font-bold">Join Network</span>
</a>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    AOS.init({
        once: true,
        offset: 50,
        duration: 1000,
        easing: 'ease-out-cubic'
    });

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
            document.body.style.overflow = 'auto';
        }, 400); // slightly longer to match the new duration-500
    };

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeProjectModal();
    });
});
</script>