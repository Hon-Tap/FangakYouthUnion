<?php
// 1. DATA LOGIC -------------------------------------------------------
$pageTitle = "Home - Fangak Youth Union";

// Include Header
include_once __DIR__ . "/../app/views/layouts/header.php";

// Ensure DB Connection
if (!isset($pdo)) {
    // Fallback if header doesn't provide it
    include_once __DIR__ . "/../app/config/db.php";
}

// Initialize arrays
$announcements = [];
$events = [];

try {
    if (isset($pdo)) {
        // A. FETCH ANNOUNCEMENTS (Newest first, max 3)
        $annStmt = $pdo->prepare("
            SELECT * FROM announcements 
            WHERE is_published = 1 
            ORDER BY created_at DESC 
            LIMIT 3
        ");
        $annStmt->execute();
        $announcements = $annStmt->fetchAll(PDO::FETCH_ASSOC);

        // B. FETCH EVENTS (Soonest date first, max 3, upcoming only)
        $evtStmt = $pdo->prepare("
            SELECT * FROM events 
            WHERE event_date >= CURDATE() 
            ORDER BY event_date ASC 
            LIMIT 3
        ");
        $evtStmt->execute();
        $events = $evtStmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    error_log("Homepage Query Error: " . $e->getMessage());
}

// Helper Functions
function formatDate($dateString) { return date('M d, Y', strtotime($dateString)); }
function getDay($dateString) { return date('d', strtotime($dateString)); }
function getMonth($dateString) { return date('M', strtotime($dateString)); }
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
                        dark: '#145233',   // Deep Green
                        primary: '#1f7a4b', // Standard Logo Green
                        light: '#3aa76a',   // Lighter Accent
                        accent: '#e6f4ea',  // Very light green bg
                    }
                },
                fontFamily: {
                    sans: ['Poppins', 'sans-serif'],
                    serif: ['Old Standard TT', 'serif'],
                }
            }
        }
    }
</script>

<style>
    /* Polished Scrollbar */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: #1f7a4b; border-radius: 4px; }
    
    /* Hero Gradient */
    .hero-gradient {
        background: linear-gradient(to bottom, rgba(0,0,0,0.5) 0%, rgba(20,82,51,0.9) 100%);
    }

    /* Text Truncation */
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Glassmorphism for Admin Button */
    .glass-btn {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
</style>

<section class="relative h-[85vh] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="<?= $baseUrl ?>images/FYU-LOGO.jpg" alt="Background" class="w-full h-full object-cover">
        <div class="hero-gradient absolute inset-0"></div>
    </div>

    <div class="absolute top-16 right-6 z-30" data-aos="fade-left" data-aos-delay="800">
        <a href="<?= $baseUrl ?>admin/login.php" class="glass-btn text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-white hover:text-fyu-dark transition flex items-center gap-2">
            <i class="fa-solid fa-user-shield"></i> Admin Login
        </a>
    </div>

    <div class="relative z-10 container mx-auto px-6 text-center text-white">
        <div data-aos="fade-down" data-aos-duration="1000">
            <span class="inline-block py-1 px-3 rounded-full bg-fyu-light/30 border border-fyu-light/50 text-sm font-semibold tracking-wide mb-4 backdrop-blur-sm">
                Unity • Innovation • Progress
            </span>
        </div>
        
        <h1 class="text-4xl md:text-6xl font-serif font-bold mb-6 leading-tight drop-shadow-lg" data-aos="fade-up" data-aos-delay="200">
            Welcome to <br> <span class="text-fyu-light">Fangak Youth Union</span>
        </h1>
        
        <p class="text-lg md:text-xl text-gray-200 max-w-2xl mx-auto mb-10 font-light" data-aos="fade-up" data-aos-delay="400">
            Empowering youth, building community, and creating a better future through sustainable projects and collective leadership.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center" data-aos="fade-up" data-aos-delay="600">
            <a href="<?= $baseUrl ?>joinus.php" class="px-8 py-3 bg-fyu-primary hover:bg-fyu-light text-white font-semibold rounded-full transition-all duration-300 shadow-lg hover:shadow-green-500/50 transform hover:-translate-y-1">
                Start Your Impact
            </a>
            <a href="#announcements" class="px-8 py-3 bg-transparent border-2 border-white text-white font-semibold rounded-full hover:bg-white hover:text-fyu-dark transition-all duration-300">
                Explore More
            </a>
        </div>
    </div>
    
    <a href="#impact" class="absolute bottom-8 animate-bounce text-white/70 hover:text-white transition z-20">
        <i class="fa-solid fa-arrow-down text-2xl"></i>
    </a>
</section>

<section id="impact" class="py-12 bg-white relative -mt-10 z-20 mx-4 rounded-xl shadow-xl max-w-6xl md:mx-auto">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center divide-x divide-gray-100">
        <div data-aos="fade-up" data-aos-delay="0">
            <span class="text-4xl font-bold text-fyu-primary block counter" data-target="1200">0</span>
            <span class="text-sm text-gray-500 font-medium uppercase tracking-wide">Over 1,200 Members</span>
        </div>
        <div data-aos="fade-up" data-aos-delay="100">
            <span class="text-4xl font-bold text-fyu-primary block counter" data-target="12">0</span>
            <span class="text-sm text-gray-500 font-medium uppercase tracking-wide">Over 12 Projects</span>
        </div>
        <div data-aos="fade-up" data-aos-delay="200">
            <span class="text-4xl font-bold text-fyu-primary block counter" data-target="8">0</span>
            <span class="text-sm text-gray-500 font-medium uppercase tracking-wide">Payams</span>
        </div>
        <div data-aos="fade-up" data-aos-delay="300">
            <span class="text-4xl font-bold text-fyu-primary block counter" data-target="100">0</span>
            <span class="text-sm text-gray-500 font-medium uppercase tracking-wide">% Dedicated</span>
        </div>
    </div>
</section>

<section id="announcements" class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="flex justify-between items-end mb-12" data-aos="fade-right">
            <div>
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-fyu-dark">Latest Announcements</h2>
                <div class="h-1 w-20 bg-fyu-primary mt-2 rounded"></div>
            </div>
            <a href="<?= $baseUrl ?>announcements.php" class="hidden md:flex items-center text-fyu-primary font-semibold hover:text-fyu-dark transition">
                View Archive <i class="fa-solid fa-arrow-right ml-2"></i>
            </a>
        </div>

        <?php if (empty($announcements)): ?>
            <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-100">
                <i class="fa-regular fa-folder-open text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No published announcements at the moment.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($announcements as $index => $ann): ?>
                    <article class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 border-l-4 border-fyu-primary group flex flex-col h-full" 
                             data-aos="fade-up" data-aos-delay="<?= $index * 150 ?>">
                        
                        <div class="flex items-center text-sm text-gray-500 mb-3">
                            <span class="bg-fyu-accent text-fyu-dark px-2 py-1 rounded text-xs font-semibold mr-2">
                                <?= getMonth($ann['created_at']) ?> <?= getDay($ann['created_at']) ?>
                            </span>
                            <span class="text-xs text-gray-400"><?= date('Y', strtotime($ann['created_at'])) ?></span>
                        </div>
                        
                        <h3 class="text-xl font-bold text-gray-800 mb-3 group-hover:text-fyu-primary transition-colors">
                            <?= htmlspecialchars($ann['title']) ?>
                        </h3>
                        
                        <div class="text-gray-600 mb-6 line-clamp-3 text-sm flex-grow">
                            <?= strip_tags(html_entity_decode($ann['body'])) ?>
                        </div>
                        
                        <a href="<?= $baseUrl ?>announcements.php?id=<?= $ann['id'] ?>" class="inline-flex items-center text-fyu-primary font-semibold text-sm hover:underline mt-auto">
                            Read Announcement <i class="fa-solid fa-arrow-right-long ml-2"></i>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-8 text-center md:hidden">
                <a href="<?= $baseUrl ?>announcements.php" class="btn-secondary">View All Announcements &rarr;</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="flex justify-between items-end mb-12" data-aos="fade-left">
            <div>
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-fyu-dark">Upcoming Events</h2>
                <div class="h-1 w-20 bg-fyu-primary mt-2 rounded"></div>
            </div>
            <a href="<?= $baseUrl ?>events.php" class="hidden md:flex items-center text-fyu-primary font-semibold hover:text-fyu-dark transition">
                Full Calendar <i class="fa-solid fa-calendar-days ml-2"></i>
            </a>
        </div>

        <?php if (empty($events)): ?>
            <div class="text-center py-12 bg-fyu-accent/30 rounded-xl border border-dashed border-fyu-primary/30">
                <i class="fa-regular fa-calendar-xmark text-4xl text-fyu-primary/40 mb-3"></i>
                <p class="text-gray-600">No upcoming events scheduled. Check back soon!</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($events as $index => $evt): ?>
                    <article class="group relative bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100"
                             data-aos="zoom-in-up" data-aos-delay="<?= $index * 150 ?>">
                        
                        <div class="h-48 overflow-hidden relative bg-gray-200">
                            <?php if(!empty($evt['image'])): ?>
                                <img src="<?= $baseUrl ?>images/events/<?= htmlspecialchars($evt['image']) ?>" 
                                     alt="<?= htmlspecialchars($evt['title']) ?>" 
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-fyu-primary to-fyu-dark text-white/20">
                                    <i class="fa-solid fa-calendar-check text-5xl"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="absolute top-4 right-4 bg-white/95 backdrop-blur rounded-lg p-2 text-center shadow-lg min-w-[60px] border border-gray-100">
                                <span class="block text-xl font-bold text-fyu-dark leading-none"><?= getDay($evt['event_date']) ?></span>
                                <span class="block text-xs uppercase font-semibold text-gray-500"><?= getMonth($evt['event_date']) ?></span>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex items-center text-xs text-fyu-primary font-semibold mb-2 uppercase tracking-wide">
                                <i class="fa-solid fa-location-dot mr-1"></i>
                                <?= htmlspecialchars($evt['location'] ?? 'Fangak HQ') ?>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-800 mb-3 leading-tight group-hover:text-fyu-primary transition-colors">
                                <?= htmlspecialchars($evt['title']) ?>
                            </h3>
                            
                            <p class="text-gray-600 text-sm mb-5 line-clamp-2">
                                <?= strip_tags(html_entity_decode($evt['description'])) ?>
                            </p>
                            
                            <a href="<?= $baseUrl ?>events.php?id=<?= $evt['id'] ?>" class="w-full inline-flex justify-center items-center py-2.5 rounded-lg border border-fyu-primary text-fyu-primary font-medium hover:bg-fyu-primary hover:text-white transition-all duration-300">
                                View Event Details <i class="fa-solid fa-arrow-right ml-2 text-sm"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-8 text-center md:hidden">
                <a href="<?= $baseUrl ?>events.php" class="text-fyu-primary font-semibold">View All Events &rarr;</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="py-20 bg-fyu-accent">
    <div class="container mx-auto px-6 text-center">
        <div class="max-w-3xl mx-auto mb-16" data-aos="fade-up">
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-fyu-dark mb-4">Our Core Pillars</h2>
            <p class="text-gray-600 text-lg">We drive change by focusing on unity, education, and community impact.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition duration-300 transform hover:-translate-y-2" data-aos="fade-up" data-aos-delay="100">
                <div class="w-14 h-14 mx-auto bg-green-100 rounded-full flex items-center justify-center text-fyu-primary text-2xl mb-6">
                    <i class="fa-solid fa-people-group"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Unity</h3>
                <p class="text-gray-600 text-sm">Fostering strong youth leadership to build a united and empowered generation.</p>
            </div>
            
            <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition duration-300 transform hover:-translate-y-2" data-aos="fade-up" data-aos-delay="200">
                <div class="w-14 h-14 mx-auto bg-green-100 rounded-full flex items-center justify-center text-fyu-primary text-2xl mb-6">
                    <i class="fa-solid fa-lightbulb"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Innovation</h3>
                <p class="text-gray-600 text-sm">Implementing creative and sustainable solutions that drive progress.</p>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition duration-300 transform hover:-translate-y-2" data-aos="fade-up" data-aos-delay="300">
                <div class="w-14 h-14 mx-auto bg-green-100 rounded-full flex items-center justify-center text-fyu-primary text-2xl mb-6">
                    <i class="fa-solid fa-hand-holding-heart"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Service</h3>
                <p class="text-gray-600 text-sm">Driving tangible change through service and grassroots development.</p>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition duration-300 transform hover:-translate-y-2" data-aos="fade-up" data-aos-delay="400">
                <div class="w-14 h-14 mx-auto bg-green-100 rounded-full flex items-center justify-center text-fyu-primary text-2xl mb-6">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Education</h3>
                <p class="text-gray-600 text-sm">Breaking barriers through comprehensive education and mentorship.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-fyu-dark text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div> 
    
    <div class="container mx-auto px-6 relative z-10">
        <div class="flex flex-col md:flex-row justify-between items-center mb-12" data-aos="fade-right">
            <div>
                <h2 class="text-3xl md:text-4xl font-serif font-bold">Current Initiatives</h2>
                <p class="text-gray-300 mt-2">Explore our ongoing projects driving real impact.</p>
            </div>
            <a href="<?= $baseUrl ?>projects.php" class="mt-4 md:mt-0 px-6 py-2 border border-white rounded-full hover:bg-white hover:text-fyu-dark transition">
                View All Projects
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php
            // Static Projects Array (unchanged)
            $projects = [
                ['img' => 'project1', 'title' => 'Youth Skills Development', 'desc' => 'Equipping young people with essential digital and vocational skills.'],
                ['img' => 'FloTra', 'title' => 'Flood Response Initiative', 'desc' => 'Supporting families displaced by floods with emergency relief.'],
                ['img' => 'SACOM', 'title' => 'Access to Education', 'desc' => 'Providing resources and mentorship for children in rural communities.'],
            ];
            foreach ($projects as $idx => $proj) :
            ?>
                <div class="group cursor-pointer" onclick="openProjectModal('<?= $proj['title'] ?>', '<?= $proj['desc'] ?>', '<?= $baseUrl ?>images/<?= $proj['img'] ?>.jpg')" 
                     data-aos="fade-up" data-aos-delay="<?= $idx * 150 ?>">
                    <div class="relative overflow-hidden rounded-xl aspect-video mb-4 border border-white/10">
                        <img src="<?= $baseUrl ?>images/<?= $proj['img'] ?>.jpg" alt="<?= $proj['title'] ?>" 
                             class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <span class="text-white border border-white px-4 py-2 rounded-full text-sm hover:bg-white hover:text-black transition">View Details</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mb-1 group-hover:text-fyu-light transition"><?= $proj['title'] ?></h3>
                    <p class="text-gray-400 text-sm"><?= $proj['desc'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-24 bg-gradient-to-r from-fyu-primary to-fyu-light text-white text-center">
    <div class="container mx-auto px-6" data-aos="zoom-in">
        <h2 class="text-4xl md:text-5xl font-serif font-bold mb-6">Be Part of the Change</h2>
        <p class="text-xl text-white/90 max-w-2xl mx-auto mb-10">Join us in empowering youth and transforming our communities. Your skills, energy, and passion matter.</p>
        <a href="<?= $baseUrl ?>register.php" class="inline-block px-10 py-4 bg-white text-fyu-dark font-bold rounded-full text-lg shadow-xl hover:shadow-2xl hover:bg-gray-100 transition transform hover:-translate-y-1">
            Join the Movement Now
        </a>
    </div>
</section>

<div id="projectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-300" aria-hidden="true">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeProjectModal()"></div>
    <div class="bg-white rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl transform scale-95 transition-transform duration-300 relative z-10" id="modalContent">
        <button onclick="closeProjectModal()" class="absolute top-4 right-4 bg-white/20 hover:bg-black/10 text-gray-800 p-2 rounded-full transition z-20">
            <i class="fa-solid fa-times text-xl"></i>
        </button>
        <img id="modalImg" src="" alt="Project" class="w-full h-64 object-cover">
        <div class="p-8">
            <h3 id="modalTitle" class="text-2xl font-bold text-fyu-primary mb-3"></h3>
            <p id="modalDesc" class="text-gray-600 mb-6 leading-relaxed"></p>
            <a href="<?= $baseUrl ?>joinus.php" class="inline-block px-6 py-2 bg-fyu-primary text-white rounded-lg hover:bg-fyu-dark transition">
                Get Involved
            </a>
        </div>
    </div>
</div>

<a href="<?= $baseUrl ?>register.php" class="fixed bottom-6 right-6 z-40 bg-fyu-primary text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:bg-fyu-dark transition md:w-auto md:px-6 md:rounded-full">
    <i class="fa-solid fa-handshake md:mr-2"></i>
    <span class="hidden md:inline font-semibold">Join Us</span>
</a>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // ----------------------------
    // Initialize AOS
    // ----------------------------
    AOS.init({
        once: true,
        offset: 100,
        duration: 800,
        easing: 'ease-out-cubic'
    });

    // ----------------------------
    // Modal Logic
    // ----------------------------
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
        modalContent.classList.remove('scale-95');
        modalContent.classList.add('scale-100');
        document.body.style.overflow = 'hidden';
    };

    window.closeProjectModal = () => {
        modal.classList.add('opacity-0', 'pointer-events-none');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        document.body.style.overflow = 'auto';
    };

    // Close modal on Escape key
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeProjectModal();
    });

    // ----------------------------
    // Counter Animation
    // ----------------------------
    const counters = document.querySelectorAll('.counter');
    const speed = 200; // lower is faster

    const animateCounter = (counter) => {
        const target = +counter.getAttribute('data-target');
        let count = 0;
        const step = () => {
            const inc = Math.ceil(target / speed);
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

    // Trigger counters when section comes into view
    const counterSection = document.getElementById('impact');
    let hasAnimated = false;

    const onScroll = () => {
        if (!hasAnimated && window.scrollY + window.innerHeight > counterSection.offsetTop + 100) {
            counters.forEach(animateCounter);
            hasAnimated = true;
            window.removeEventListener('scroll', onScroll);
        }
    };

    window.addEventListener('scroll', onScroll);
});
</script>
