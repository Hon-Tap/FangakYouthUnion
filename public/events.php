<?php
/**
 * Fangak Youth Union - Events Page
 * Standardized Version 2.0
 */

require_once __DIR__ . '/../app/config/db.php';

$pageTitle = "Community Events - Fangak Youth Union";

// Configuration - Ensure this matches your production table name
$tableName = "fyu_events"; 

$upcomingEvents = [];
$pastEvents = [];
$today = date('Y-m-d');

if ($pdo) {
    try {
        // We fetch sorted by date; let the DB do the heavy lifting
        $stmt = $pdo->query("SELECT * FROM {$tableName} ORDER BY event_date ASC");
        $allEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($allEvents as $event) {
            $eDate = $event['event_date'] ?? $today;
            if ($eDate >= $today) {
                $upcomingEvents[] = $event;
            } else {
                $pastEvents[] = $event;
            }
        }

        // Reverse past events so most recent is first
        $pastEvents = array_reverse($pastEvents);

    } catch (Throwable $e) {
        error_log("FYU Events Error: " . $e->getMessage());
    }
}

// Date Formatter Helper
function formatDate($dateStr, $format = 'full') {
    $time = strtotime($dateStr);
    return match($format) {
        'day' => date('d', $time),
        'month' => date('M', $time),
        'year' => date('Y', $time),
        'weekday' => date('l', $time),
        default => date('F j, Y', $time),
    };
}

include_once __DIR__ . '/../app/views/layouts/header.php';
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Old+Standard+TT:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    fyu: {
                        dark: '#0e3a24',
                        primary: '#1f7a4b',
                        light: '#3aa76a',
                        accent: '#f0fdf4',
                        gold: '#c59d08'
                    }
                },
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                    serif: ['Old Standard TT', 'serif'],
                }
            }
        }
    }
</script>

<style>
    .event-card-glass {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
    }
    .text-balance { text-wrap: balance; }
</style>

<div class="relative bg-fyu-dark py-24 overflow-hidden">
    <div class="absolute inset-0 opacity-20">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.2\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>
    <div class="container mx-auto px-6 relative z-10 text-center">
        <h1 class="text-5xl md:text-6xl font-serif font-bold text-white mb-6" data-aos="fade-down">Union Events</h1>
        <p class="text-fyu-accent/80 text-xl max-w-2xl mx-auto font-light leading-relaxed" data-aos="fade-up">
            Empowering the youth of Fangak through culture, education, and collective action.
        </p>
    </div>
</div>

<main class="max-w-7xl mx-auto px-6 py-16">
    
    <nav class="flex mb-12 items-center space-x-2 text-sm text-gray-400">
        <a href="/" class="hover:text-fyu-primary transition">Home</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-gray-900 font-semibold">Events</span>
    </nav>

    <div class="mb-24">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
            <div>
                <h2 class="text-4xl font-bold text-gray-900 tracking-tight">Upcoming Gatherings</h2>
                <p class="text-gray-500 mt-2">Mark your calendar for our next community milestones.</p>
            </div>
            <div class="bg-fyu-accent px-4 py-2 rounded-full border border-fyu-light/20">
                <span class="text-fyu-dark font-bold"><?= count($upcomingEvents) ?></span> 
                <span class="text-fyu-primary/80 text-sm ml-1 uppercase tracking-wider font-semibold">Scheduled</span>
            </div>
        </div>

        <?php if (empty($upcomingEvents)): ?>
            <div class="bg-white border-2 border-dashed border-gray-200 rounded-3xl p-20 text-center" data-aos="fade-up">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-50 text-gray-300 rounded-full mb-6">
                    <i class="fa-solid fa-calendar-plus text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">New events coming soon</h3>
                <p class="text-gray-500 max-w-sm mx-auto mt-2 text-balance">We're currently planning our next activities. Follow our social media for instant updates.</p>
            </div>
        <?php else: ?>
            <?php 
            $featured = array_shift($upcomingEvents); 
            ?>
            <div class="group relative bg-white rounded-[2rem] shadow-2xl shadow-fyu-dark/5 border border-gray-100 overflow-hidden mb-16 transition-all hover:shadow-fyu-dark/10" data-aos="zoom-in">
                <div class="grid lg:grid-cols-2">
                    <div class="relative min-h-[400px] overflow-hidden">
                        <?php if (!empty($featured['image'])): ?>
                            <img src="/uploads/events/<?= htmlspecialchars($featured['image']) ?>" 
                                 class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" alt="Event">
                        <?php else: ?>
                            <div class="absolute inset-0 bg-gradient-to-br from-fyu-primary to-fyu-dark flex items-center justify-center">
                                <i class="fa-solid fa-bullhorn text-white/10 text-9xl rotate-12"></i>
                            </div>
                        <?php endif; ?>
                        <div class="absolute top-8 left-8">
                            <span class="bg-fyu-gold text-white px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest shadow-lg">Featured Event</span>
                        </div>
                    </div>

                    <div class="p-10 lg:p-16 flex flex-col justify-center">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="text-center bg-fyu-accent rounded-2xl p-4 min-w-[80px] border border-fyu-light/20">
                                <div class="text-xs font-bold text-fyu-primary uppercase"><?= formatDate($featured['event_date'], 'month') ?></div>
                                <div class="text-3xl font-black text-fyu-dark"><?= formatDate($featured['event_date'], 'day') ?></div>
                            </div>
                            <div class="h-10 w-[1px] bg-gray-200"></div>
                            <div class="text-gray-500">
                                <div class="font-bold text-gray-900"><?= formatDate($featured['event_date'], 'weekday') ?></div>
                                <div class="text-sm"><?= formatDate($featured['event_date'], 'year') ?></div>
                            </div>
                        </div>

                        <h3 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-6 group-hover:text-fyu-primary transition-colors"><?= htmlspecialchars($featured['title']) ?></h3>
                        <p class="text-gray-600 text-lg leading-relaxed mb-8 line-clamp-3"><?= htmlspecialchars($featured['description']) ?></p>

                        <div class="flex flex-wrap gap-6 mb-10">
                            <div class="flex items-center text-gray-600">
                                <i class="fa-solid fa-location-dot text-fyu-primary mr-3 text-xl"></i>
                                <span class="font-medium"><?= htmlspecialchars($featured['location'] ?: 'To be announced') ?></span>
                            </div>
                        </div>

                        <a href="event-details.php?id=<?= $featured['id'] ?>" class="inline-flex items-center justify-center px-8 py-4 bg-fyu-dark hover:bg-fyu-primary text-white font-bold rounded-2xl transition-all duration-300 shadow-xl hover:-translate-y-1">
                            Participate & Details <i class="fa-solid fa-arrow-right ml-3"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
                <?php foreach ($upcomingEvents as $index => $ev): ?>
                    <div class="group bg-white rounded-3xl border border-gray-100 p-2 transition-all hover:shadow-2xl hover:shadow-fyu-dark/5 hover:-translate-y-2" data-aos="fade-up" data-aos-delay="<?= $index * 50 ?>">
                        <div class="relative h-56 rounded-2xl overflow-hidden mb-6">
                            <?php if (!empty($ev['image'])): ?>
                                <img src="/uploads/events/<?= htmlspecialchars($ev['image']) ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            <?php else: ?>
                                <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                    <i class="fa-solid fa-calendar-day text-gray-300 text-4xl"></i>
                                </div>
                            <?php endif; ?>
                            <div class="absolute bottom-4 left-4 right-4">
                                <div class="event-card-glass px-4 py-2 rounded-xl border border-white/20 flex items-center justify-between">
                                    <span class="text-sm font-bold text-fyu-dark"><?= formatDate($ev['event_date']) ?></span>
                                    <i class="fa-solid fa-clock text-fyu-primary text-xs"></i>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6">
                            <h4 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-fyu-primary transition-colors"><?= htmlspecialchars($ev['title']) ?></h4>
                            <p class="text-gray-500 text-sm line-clamp-2 mb-6"><?= htmlspecialchars($ev['description']) ?></p>
                            <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    <i class="fa-solid fa-map-marker-alt mr-1"></i> <?= htmlspecialchars($ev['location'] ?: 'Fangak') ?>
                                </span>
                                <a href="event-details.php?id=<?= $ev['id'] ?>" class="text-fyu-primary font-bold text-sm hover:underline">View More</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($pastEvents)): ?>
        <div class="pt-20 border-t border-gray-100">
            <div class="flex items-center mb-12">
                <h2 class="text-3xl font-serif font-bold text-gray-400">Past Union Activities</h2>
                <div class="flex-grow h-[1px] bg-gray-100 ml-8"></div>
            </div>
            
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($pastEvents as $ev): ?>
                    <div class="bg-gray-50/50 rounded-2xl p-6 border border-transparent hover:border-gray-200 hover:bg-white transition-all group" data-aos="fade-up">
                        <div class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mb-3 group-hover:text-fyu-primary transition-colors">
                            <?= formatDate($ev['event_date'], 'month') ?> <?= formatDate($ev['event_date'], 'year') ?>
                        </div>
                        <h5 class="font-bold text-gray-700 leading-snug"><?= htmlspecialchars($ev['title']) ?></h5>
                        <p class="text-xs text-gray-400 mt-4 flex items-center">
                            <i class="fa-solid fa-check-circle mr-2 text-green-500/50"></i> Completed
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

</main>

<?php include_once __DIR__ . '/../app/views/layouts/footer.php'; ?>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        once: true,
        duration: 1000,
        offset: 100,
        easing: 'ease-out-back'
    });
</script>