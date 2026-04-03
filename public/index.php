<?php

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
        'title' => 'Community Clean-up',
        'category' => 'Environment',
        'img' => 'cleanup.jpg'
    ],
    [
        'title' => 'Tech Workshop',
        'category' => 'Education',
        'img' => 'coding.jpg'
    ],
    [
        'title' => 'Sports Tournament',
        'category' => 'Health & Unity',
        'img' => 'project1.jpg'
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
                }
            }
        }
    }
</script>

<style>
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f9fafb; }
    ::-webkit-scrollbar-thumb { background: #1f7a4b; border-radius: 4px; }

    .hero-gradient {
        background: linear-gradient(135deg, rgba(20,82,51,0.95) 0%, rgba(0,0,0,0.6) 100%);
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Modern Card Hover Smoothing */
    .smooth-hover {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>

<section class="relative h-[90vh] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="<?= $baseUrl ?>images/FYU-LOGO.jpg" alt="Background" class="w-full h-full object-cover transform scale-105 animate-[pulse_20s_ease-in-out_infinite_alternate]">
        <div class="hero-gradient absolute inset-0 backdrop-blur-[2px]"></div>
    </div>

    <div class="absolute top-8 right-8 z-30" data-aos="fade-left" data-aos-delay="800">
        <a href="<?= $baseUrl ?>admin/login.php" class="bg-white/10 backdrop-blur-md text-white px-5 py-2.5 rounded-full text-sm font-medium hover:bg-white hover:text-fyu-dark transition-all duration-300 border border-white/20 flex items-center gap-2 hover:shadow-[0_0_20px_rgba(255,255,255,0.3)]">
            <i class="fa-solid fa-user-shield"></i> Admin
        </a>
    </div>

    <div class="relative z-10 container mx-auto px-6 text-center text-white">
        <div data-aos="fade-down" data-aos-duration="1200">
            <span class="inline-block py-1.5 px-4 rounded-full bg-fyu-light/20 border border-fyu-light/40 text-xs md:text-sm font-semibold tracking-[0.2em] uppercase mb-6 backdrop-blur-md">
                Unity • Innovation • Progress
            </span>
        </div>

        <h1 class="text-5xl md:text-7xl font-serif font-bold mb-6 leading-tight drop-shadow-2xl" data-aos="zoom-in-up" data-aos-delay="200">
            Welcome to <br> <span class="text-transparent bg-clip-text bg-gradient-to-r from-fyu-light to-white">Fangak Youth Union</span>
        </h1>

        <p class="text-lg md:text-2xl text-gray-200 max-w-3xl mx-auto mb-10 font-light leading-relaxed" data-aos="fade-up" data-aos-delay="400">
            Empowering youth, building community, and creating a better future through sustainable projects and collective leadership.
        </p>

        <div class="flex flex-col sm:flex-row gap-5 justify-center items-center" data-aos="fade-up" data-aos-delay="600">
            <a href="<?= $baseUrl ?>register.php" class="px-8 py-4 bg-fyu-primary hover:bg-fyu-light text-white font-bold rounded-full transition-all duration-300 shadow-[0_0_20px_rgba(31,122,75,0.4)] hover:shadow-[0_0_30px_rgba(58,167,106,0.6)] transform hover:-translate-y-1 w-full sm:w-auto text-lg">
                Start Your Impact
            </a>
            <a href="#engagement" class="px-8 py-4 bg-transparent border-2 border-white text-white font-bold rounded-full hover:bg-white hover:text-fyu-dark transition-all duration-300 w-full sm:w-auto text-lg">
                Explore More
            </a>
        </div>
    </div>

    <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 z-20 flex flex-col items-center animate-bounce text-white/70 hover:text-white cursor-pointer transition-colors duration-300">
        <span class="text-xs tracking-widest uppercase mb-2">Scroll</span>
        <i class="fa-solid fa-arrow-down"></i>
    </div>
</section>

<section id="impact" class="relative -mt-16 z-20 container mx-auto px-4 md:px-6">
    <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-float p-8 md:p-10 border border-white">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x divide-gray-100/50">
            <div data-aos="fade-up" data-aos-delay="0">
                <span class="text-4xl md:text-5xl font-bold text-fyu-dark block mb-1 counter" data-target="1200">0</span>
                <span class="text-xs md:text-sm text-fyu-primary font-bold uppercase tracking-wider">Members</span>
            </div>
            <div data-aos="fade-up" data-aos-delay="100">
                <span class="text-4xl md:text-5xl font-bold text-fyu-dark block mb-1 counter" data-target="12">0</span>
                <span class="text-xs md:text-sm text-fyu-primary font-bold uppercase tracking-wider">Projects</span>
            </div>
            <div data-aos="fade-up" data-aos-delay="200">
                <span class="text-4xl md:text-5xl font-bold text-fyu-dark block mb-1 counter" data-target="8">0</span>
                <span class="text-xs md:text-sm text-fyu-primary font-bold uppercase tracking-wider">Payams</span>
            </div>
            <div data-aos="fade-up" data-aos-delay="300">
                <span class="text-4xl md:text-5xl font-bold text-fyu-dark block mb-1 counter" data-target="100">0</span>
                <span class="text-xs md:text-sm text-fyu-primary font-bold uppercase tracking-wider">% Dedicated</span>
            </div>
        </div>
    </div>
</section>

<section id="engagement" class="py-24 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
            <h2 class="text-sm font-bold text-fyu-primary tracking-widest uppercase mb-2">Active Involvement</h2>
            <h3 class="text-3xl md:text-4xl font-serif font-bold text-gray-900 mb-4">Youth Engagement</h3>
            <p class="text-gray-600">See how our members are stepping up to lead, serve, and connect across the region on a daily basis.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ($engagements as $index => $eng): ?>
                <div class="group relative h-80 rounded-2xl overflow-hidden cursor-pointer shadow-soft" data-aos="fade-up" data-aos-delay="<?= $index * 150 ?>">
                    <img src="<?= $baseUrl ?>assets/images/<?= safeText($eng['img']) ?>" 
                    alt="<?= safeText($eng['title']) ?>" 
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                    onerror="this.src='https://images.unsplash.com/photo-1529156069898-49953eb1b5ce?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'">
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 transition-opacity duration-300 group-hover:opacity-100"></div>
                    
                    <div class="absolute bottom-0 left-0 w-full p-6 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                        <span class="inline-block px-3 py-1 bg-fyu-primary text-white text-xs font-bold rounded-full mb-3 shadow-lg">
                            <?= safeText($eng['category']) ?>
                        </span>
                        <h4 class="text-xl font-bold text-white mb-2"><?= safeText($eng['title']) ?></h4>
                        <div class="h-1 w-0 bg-fyu-light group-hover:w-12 transition-all duration-500 ease-out"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-24 bg-white relative">
    <div class="absolute top-0 right-0 w-64 h-64 bg-fyu-accent rounded-bl-full opacity-50 z-0"></div>
    
    <div class="container mx-auto px-6 relative z-10">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16" data-aos="fade-right">
            <div>
                <h2 class="text-sm font-bold text-fyu-primary tracking-widest uppercase mb-2">Mark Your Calendar</h2>
                <h3 class="text-3xl md:text-4xl font-serif font-bold text-gray-900">Upcoming Events</h3>
            </div>
            <a href="<?= $baseUrl ?>events.php" class="group mt-4 md:mt-0 flex items-center text-fyu-primary font-bold hover:text-fyu-dark transition-colors">
                Full Calendar 
                <span class="ml-2 w-8 h-8 rounded-full bg-fyu-accent flex items-center justify-center group-hover:bg-fyu-primary group-hover:text-white transition-colors">
                    <i class="fa-solid fa-arrow-right text-sm"></i>
                </span>
            </a>
        </div>

        <?php if (empty($events)): ?>
            <div class="text-center py-16 bg-gray-50 rounded-2xl border border-dashed border-gray-200" data-aos="fade-in">
                <div class="w-20 h-20 mx-auto bg-white rounded-full flex items-center justify-center shadow-sm mb-4">
                    <i class="fa-regular fa-calendar-xmark text-3xl text-gray-400"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-700">No events right now</h4>
                <p class="text-gray-500 mt-2">Check back soon for upcoming gatherings and workshops.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-10">
                <?php foreach ($events as $index => $evt): ?>
                    <article class="group bg-white rounded-2xl p-4 shadow-soft hover:shadow-float smooth-hover transform hover:-translate-y-2 border border-gray-50" data-aos="fade-up" data-aos-delay="<?= $index * 150 ?>">
                        <div class="relative h-56 rounded-xl overflow-hidden mb-6">
                            <?php if (!empty($evt['image'])): ?>
                                <img src="<?= $baseUrl ?>images/events/<?= safeText($evt['image']) ?>" alt="<?= safeText($evt['title'] ?? '') ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            <?php else: ?>
                                <div class="w-full h-full bg-fyu-dark flex items-center justify-center">
                                    <i class="fa-solid fa-calendar-day text-4xl text-white/20"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="absolute top-4 left-4 bg-white/95 backdrop-blur-sm rounded-xl p-3 text-center shadow-lg min-w-[70px]">
                                <span class="block text-2xl font-black text-fyu-primary leading-none"><?= getDay($evt['event_date'] ?? '') ?></span>
                                <span class="block text-xs uppercase font-bold text-gray-500 mt-1"><?= getMonth($evt['event_date'] ?? '') ?></span>
                            </div>
                        </div>

                        <div class="px-2 pb-2">
                            <div class="flex items-center text-xs text-gray-500 font-semibold mb-3 uppercase tracking-wide">
                                <i class="fa-solid fa-location-dot text-fyu-light mr-2"></i>
                                <?= safeText($evt['location'] ?? 'Fangak HQ') ?>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-fyu-primary transition-colors line-clamp-2">
                                <?= safeText($evt['title'] ?? '') ?>
                            </h3>
                            <a href="<?= $baseUrl ?>events.php?id=<?= (int)($evt['id'] ?? 0) ?>" class="inline-flex items-center text-sm font-bold text-fyu-dark group-hover:text-fyu-primary transition-colors mt-2">
                                View Details <i class="fa-solid fa-arrow-right-long ml-2 transform group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section id="announcements" class="py-24 bg-fyu-dark text-white relative">
    <div class="absolute inset-0 opacity-5 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')]"></div>
    
    <div class="container mx-auto px-6 relative z-10">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16" data-aos="fade-left">
            <div>
                <h2 class="text-sm font-bold text-fyu-light tracking-widest uppercase mb-2">Stay Informed</h2>
                <h3 class="text-3xl md:text-4xl font-serif font-bold text-white">Latest Announcements</h3>
            </div>
            <a href="<?= $baseUrl ?>announcements.php" class="mt-4 md:mt-0 px-6 py-2 border border-white/30 rounded-full hover:bg-white hover:text-fyu-dark transition-colors text-sm font-bold">
                View Archive
            </a>
        </div>

        <?php if (empty($announcements)): ?>
            <div class="text-center py-12 bg-white/5 rounded-2xl border border-white/10 backdrop-blur-sm">
                <p class="text-white/60">No published announcements at the moment.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php foreach ($announcements as $index => $ann): ?>
                    <article class="bg-white/5 border border-white/10 rounded-2xl p-8 hover:bg-white/10 backdrop-blur-sm transition-all duration-300 group flex flex-col h-full" data-aos="zoom-in" data-aos-delay="<?= $index * 150 ?>">
                        <div class="flex items-center text-sm text-fyu-light mb-4 font-mono">
                            <i class="fa-regular fa-clock mr-2"></i>
                            <?= formatDate($ann['created_at'] ?? '') ?>
                        </div>

                        <h3 class="text-xl font-bold text-white mb-4 group-hover:text-fyu-light transition-colors line-clamp-2">
                            <?= safeText($ann['title'] ?? '') ?>
                        </h3>

                        <div class="text-white/70 mb-8 line-clamp-3 text-sm leading-relaxed flex-grow">
                            <?= safeHtmlPreview($ann['body'] ?? '') ?>
                        </div>

                        <a href="<?= $baseUrl ?>announcements.php?id=<?= (int)($ann['id'] ?? 0) ?>" class="mt-auto inline-flex items-center text-white font-bold text-sm border-b border-fyu-light pb-1 hover:text-fyu-light transition-colors w-max">
                            Read Article
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="py-24 bg-white">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-sm font-bold text-fyu-primary tracking-widest uppercase mb-2" data-aos="fade-up">Driving Impact</h2>
        <h3 class="text-3xl md:text-4xl font-serif font-bold text-gray-900 mb-16" data-aos="fade-up" data-aos-delay="100">Current Initiatives</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php
            $projects = [
                ['img' => 'project1', 'title' => 'Youth Skills Development', 'desc' => 'Equipping young people with essential digital and vocational skills for the modern economy.'],
                ['img' => 'FloTra', 'title' => 'Flood Response Initiative', 'desc' => 'Supporting families displaced by floods with emergency relief and rehabilitation strategies.'],
                ['img' => 'SACOM', 'title' => 'Access to Education', 'desc' => 'Providing necessary resources, facilities, and mentorship for children in rural communities.'],
            ];

            foreach ($projects as $idx => $proj) :
            ?>
                <div class="group cursor-pointer text-left" onclick="openProjectModal('<?= safeText($proj['title']) ?>', '<?= safeText($proj['desc']) ?>', '<?= $baseUrl ?>images/<?= safeText($proj['img']) ?>.jpg')"
                     data-aos="fade-up" data-aos-delay="<?= $idx * 150 ?>">

                    <div class="relative overflow-hidden rounded-2xl aspect-[4/3] mb-6 shadow-soft">
                        <img src="<?= $baseUrl ?>images/<?= safeText($proj['img']) ?>.jpg" alt="<?= safeText($proj['title']) ?>"
                             class="w-full h-full object-cover transition duration-700 group-hover:scale-110"
                             onerror="this.src='https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'">
                        <div class="absolute inset-0 bg-fyu-dark/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center backdrop-blur-sm">
                            <span class="bg-white text-fyu-dark font-bold px-6 py-3 rounded-full text-sm transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 shadow-xl">
                                Discover More
                            </span>
                        </div>
                    </div>

                    <h4 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-fyu-primary transition-colors"><?= safeText($proj['title']) ?></h4>
                    <p class="text-gray-500 text-sm leading-relaxed"><?= safeText($proj['desc']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="mt-12" data-aos="fade-up">
            <a href="<?= $baseUrl ?>projects.php" class="inline-flex items-center font-bold text-fyu-primary hover:text-fyu-dark transition-colors">
                View All Projects <i class="fa-solid fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<section class="py-20 bg-gray-50 border-t border-gray-100">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="bg-white p-8 rounded-2xl shadow-soft smooth-hover hover:-translate-y-2 border border-gray-50 text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="w-16 h-16 mx-auto bg-fyu-accent rounded-2xl flex items-center justify-center text-fyu-primary text-2xl mb-6 transform rotate-3 hover:rotate-6 transition-transform">
                    <i class="fa-solid fa-people-group"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Unity</h3>
                <p class="text-gray-500 text-sm">Fostering strong youth leadership to build an empowered generation.</p>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-soft smooth-hover hover:-translate-y-2 border border-gray-50 text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="w-16 h-16 mx-auto bg-fyu-accent rounded-2xl flex items-center justify-center text-fyu-primary text-2xl mb-6 transform -rotate-3 hover:-rotate-6 transition-transform">
                    <i class="fa-solid fa-lightbulb"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Innovation</h3>
                <p class="text-gray-500 text-sm">Implementing creative, sustainable solutions driving real progress.</p>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-soft smooth-hover hover:-translate-y-2 border border-gray-50 text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="w-16 h-16 mx-auto bg-fyu-accent rounded-2xl flex items-center justify-center text-fyu-primary text-2xl mb-6 transform rotate-3 hover:rotate-6 transition-transform">
                    <i class="fa-solid fa-hand-holding-heart"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Service</h3>
                <p class="text-gray-500 text-sm">Driving tangible change through dedicated grassroots development.</p>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-soft smooth-hover hover:-translate-y-2 border border-gray-50 text-center" data-aos="fade-up" data-aos-delay="400">
                <div class="w-16 h-16 mx-auto bg-fyu-accent rounded-2xl flex items-center justify-center text-fyu-primary text-2xl mb-6 transform -rotate-3 hover:-rotate-6 transition-transform">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Education</h3>
                <p class="text-gray-500 text-sm">Breaking barriers through comprehensive education & mentorship.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-24 bg-gradient-to-br from-fyu-primary to-fyu-dark text-white text-center relative overflow-hidden">
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] border border-white/10 rounded-full animate-[spin_60s_linear_infinite] pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] border border-white/5 rounded-full animate-[spin_40s_linear_infinite_reverse] pointer-events-none"></div>

    <div class="container mx-auto px-6 relative z-10" data-aos="zoom-in">
        <h2 class="text-4xl md:text-5xl font-serif font-bold mb-6 drop-shadow-lg">Be Part of the Change</h2>
        <p class="text-xl text-white/90 max-w-2xl mx-auto mb-10 font-light">Join us in empowering youth and transforming our communities. Your skills, energy, and passion matter.</p>
        <a href="<?= $baseUrl ?>register.php" class="inline-block px-10 py-4 bg-white text-fyu-dark font-bold rounded-full text-lg shadow-xl hover:shadow-2xl hover:bg-gray-100 transition-all duration-300 transform hover:-translate-y-1">
            Join the Movement Now
        </a>
    </div>
</section>

<div id="projectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300" aria-hidden="true">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-md" onclick="closeProjectModal()"></div>

    <div class="bg-white rounded-3xl w-full max-w-3xl overflow-hidden shadow-2xl transform scale-95 opacity-0 transition-all duration-500 relative z-10 flex flex-col md:flex-row" id="modalContent">
        <button onclick="closeProjectModal()" class="absolute top-4 right-4 bg-black/20 hover:bg-black/40 text-white p-2 w-8 h-8 flex items-center justify-center rounded-full transition z-20 backdrop-blur-sm">
            <i class="fa-solid fa-times"></i>
        </button>

        <div class="md:w-1/2 h-64 md:h-auto relative">
            <img id="modalImg" src="" alt="Project" class="w-full h-full object-cover absolute inset-0">
        </div>

        <div class="p-8 md:w-1/2 flex flex-col justify-center">
            <span class="text-xs font-bold text-fyu-light uppercase tracking-widest mb-2">Initiative</span>
            <h3 id="modalTitle" class="text-3xl font-serif font-bold text-gray-900 mb-4"></h3>
            <p id="modalDesc" class="text-gray-600 mb-8 leading-relaxed"></p>
            <a href="<?= $baseUrl ?>register.php" class="inline-block text-center px-6 py-3 bg-fyu-primary text-white font-bold rounded-xl hover:bg-fyu-dark transition-colors shadow-lg">
                Get Involved
            </a>
        </div>
    </div>
</div>

<a href="<?= $baseUrl ?>register.php" class="fixed bottom-6 right-6 z-40 bg-fyu-dark text-white w-14 h-14 rounded-full flex items-center justify-center shadow-float hover:bg-fyu-primary transition-colors duration-300 group overflow-hidden md:w-auto md:px-6">
    <i class="fa-solid fa-handshake group-hover:scale-110 transition-transform md:mr-2"></i>
    <span class="hidden md:inline font-bold">Join Us</span>
</a>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    AOS.init({
        once: true,
        offset: 50,
        duration: 800,
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
        }, 300);
    };

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeProjectModal();
    });

    // Counter Animation Logic
    const counters = document.querySelectorAll('.counter');
    const speed = 150; 
    const counterSection = document.getElementById('impact');
    let hasAnimated = false;

    const animateCounter = (counter) => {
        const target = +counter.getAttribute('data-target');
        let count = 0;

        const step = () => {
            const inc = Math.max(1, Math.ceil(target / speed));
            count += inc;

            if (count < target) {
                counter.textContent = count.toLocaleString();
                requestAnimationFrame(step);
            } else {
                counter.textContent = target.toLocaleString();
            }
        };

        step();
    };

    const onScroll = () => {
        if (!hasAnimated && counterSection && window.scrollY + window.innerHeight > counterSection.offsetTop + 50) {
            counters.forEach(animateCounter);
            hasAnimated = true;
            window.removeEventListener('scroll', onScroll);
        }
    };

    window.addEventListener('scroll', onScroll);
    onScroll(); // Check on load
});
</script>