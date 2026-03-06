<?php
$pageTitle = "Events - Fangak Youth Union";
include_once __DIR__ . '/../app/views/layouts/header.php';

// Ensure DB connection
if (!isset($pdo)) {
    include_once __DIR__ . "/../app/config/db.php";
}

// 1. FETCH & SORT DATA
try {
    // Fetch all events
    $stmt = $pdo->query("SELECT * FROM events ORDER BY event_date ASC");
    $allEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Separate into Upcoming and Past
    $upcomingEvents = [];
    $pastEvents = [];
    $today = date('Y-m-d');

    foreach ($allEvents as $event) {
        // Check if event date is today or in future
        if ($event['event_date'] >= $today) {
            $upcomingEvents[] = $event;
        } else {
            $pastEvents[] = $event;
        }
    }

    // Sort past events to show most recent history first
    usort($pastEvents, function($a, $b) {
        return strtotime($b['event_date']) - strtotime($a['event_date']);
    });

} catch (PDOException $e) {
    error_log("Events Query Error: " . $e->getMessage());
    $upcomingEvents = [];
    $pastEvents = [];
}

// 2. HELPER FUNCTIONS
function getEventDay($dateStr) {
    return date('d', strtotime($dateStr));
}
function getEventMonth($dateStr) {
    return date('M', strtotime($dateStr));
}
function getEventFullDate($dateStr) {
    return date('l, F j, Y', strtotime($dateStr));
}
?>

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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
                        gold: '#d4a017', 
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
    body { background-color: #f9fafb; }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<div class="bg-fyu-dark text-white py-16 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;"></div>
    
    <div class="container mx-auto px-4 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl font-serif font-bold mb-4" data-aos="fade-down">Community Events</h1>
        <p class="text-fyu-accent text-lg font-light max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
            Join the Fangak Youth Union in our mission to connect, celebrate, and build our future together.
        </p>
    </div>
</div>

<section class="max-w-6xl mx-auto px-4 py-12">

    <nav class="flex mb-10 text-sm text-gray-500" data-aos="fade-right">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="index.php" class="hover:text-fyu-primary transition"><i class="fa-solid fa-home mr-2"></i> Home</a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-gray-400 mx-1"></i>
                    <span class="text-gray-800 font-medium">Events</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="mb-16">
        <div class="flex items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800 border-l-4 border-fyu-primary pl-4">Upcoming Events</h2>
            <span class="ml-4 px-3 py-1 bg-fyu-accent text-fyu-dark text-xs font-bold rounded-full uppercase tracking-wider">
                <?= count($upcomingEvents) ?> scheduled
            </span>
        </div>

        <?php if (empty($upcomingEvents)): ?>
            <div class="bg-white rounded-xl shadow-sm border border-dashed border-gray-300 p-10 text-center" data-aos="fade-up">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                    <i class="fa-regular fa-calendar-xmark text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-700">No upcoming events</h3>
                <p class="text-gray-500 text-sm mt-1">Check back soon for new announcements.</p>
            </div>
        <?php else: ?>
            
            <?php 
                $featured = array_shift($upcomingEvents); // Remove first event to display as featured
            ?>
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-10 group" data-aos="zoom-in">
                <div class="grid md:grid-cols-2">
                    <div class="relative h-64 md:h-auto overflow-hidden">
                        <?php if (!empty($featured['image'])): ?>
                            <img src="/uploads/events/<?= htmlspecialchars($featured['image']) ?>" 
                                 class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                                 alt="Event Image">
                        <?php else: ?>
                            <div class="absolute inset-0 w-full h-full bg-gradient-to-br from-fyu-primary to-fyu-dark flex items-center justify-center">
                                <i class="fa-solid fa-calendar-day text-white/30 text-6xl"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="absolute top-4 left-4 bg-fyu-gold text-white text-xs font-bold px-3 py-1 rounded shadow-md uppercase tracking-wide">
                            Next Event
                        </div>
                    </div>

                    <div class="p-8 md:p-10 flex flex-col justify-center relative">
                        <div class="absolute top-6 right-8 text-center hidden md:block">
                            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest"><?= getEventMonth($featured['event_date']) ?></div>
                            <div class="text-3xl font-bold text-fyu-primary"><?= getEventDay($featured['event_date']) ?></div>
                        </div>

                        <div class="text-fyu-primary font-medium mb-2 flex items-center">
                            <i class="fa-regular fa-clock mr-2"></i> Upcoming
                        </div>
                        
                        <h3 class="text-3xl font-serif font-bold text-gray-800 mb-4 group-hover:text-fyu-primary transition-colors">
                            <?= htmlspecialchars($featured['title']) ?>
                        </h3>
                        
                        <p class="text-gray-600 mb-6 leading-relaxed line-clamp-2">
                            <?= htmlspecialchars($featured['description']) ?>
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8 text-sm">
                            <div class="flex items-center text-gray-600">
                                <div class="w-8 h-8 rounded-full bg-fyu-accent flex items-center justify-center text-fyu-primary mr-3">
                                    <i class="fa-solid fa-calendar"></i>
                                </div>
                                <span><?= getEventFullDate($featured['event_date']) ?></span>
                            </div>
                            <?php if ($featured['location']): ?>
                            <div class="flex items-center text-gray-600">
                                <div class="w-8 h-8 rounded-full bg-fyu-accent flex items-center justify-center text-fyu-primary mr-3">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <span><?= htmlspecialchars($featured['location']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div>
                            <a href="#" class="inline-flex items-center justify-center px-6 py-3 bg-fyu-primary hover:bg-fyu-dark text-white rounded-lg transition-colors duration-300 shadow-lg shadow-fyu-primary/30">
                                View Full Details <i class="fa-solid fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($upcomingEvents)): ?>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($upcomingEvents as $index => $ev): ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col h-full"
                             data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                            
                            <div class="h-48 relative overflow-hidden bg-gray-200">
                                <?php if (!empty($ev['image'])): ?>
                                    <img src="<?= $baseUrl ?>uploads/events/<?= htmlspecialchars($ev['image']) ?>" 
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" alt="Event">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100">
                                        <i class="fa-regular fa-image text-3xl"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="absolute top-3 right-3 bg-white/95 backdrop-blur rounded-lg shadow-md p-2 text-center min-w-[60px]">
                                    <span class="block text-xs font-bold text-red-500 uppercase"><?= getEventMonth($ev['event_date']) ?></span>
                                    <span class="block text-xl font-bold text-gray-800"><?= getEventDay($ev['event_date']) ?></span>
                                </div>
                            </div>

                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-fyu-primary transition-colors">
                                    <?= htmlspecialchars($ev['title']) ?>
                                </h3>
                                
                                <p class="text-gray-500 text-sm mb-4 line-clamp-2 flex-grow">
                                    <?= htmlspecialchars($ev['description']) ?>
                                </p>
                                
                                <div class="border-t border-gray-100 pt-4 mt-auto space-y-2">
                                    <?php if ($ev['location']): ?>
                                        <div class="flex items-center text-sm text-gray-500">
                                            <i class="fa-solid fa-location-dot w-5 text-center mr-2 text-fyu-light"></i>
                                            <?= htmlspecialchars($ev['location']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fa-regular fa-clock w-5 text-center mr-2 text-fyu-light"></i>
                                        <?= getEventFullDate($ev['event_date']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>

    <?php if (!empty($pastEvents)): ?>
        <div class="pt-10 border-t border-gray-200">
            <h2 class="text-2xl font-bold text-gray-400 mb-6">Past Events</h2>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 opacity-80 hover:opacity-100 transition-opacity duration-300">
                <?php foreach ($pastEvents as $ev): ?>
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 flex flex-col" data-aos="fade-up">
                        <div class="text-xs font-bold text-gray-400 uppercase mb-1">
                            <?= getEventFullDate($ev['event_date']) ?>
                        </div>
                        <h4 class="font-bold text-gray-700 mb-2"><?= htmlspecialchars($ev['title']) ?></h4>
                        <?php if ($ev['location']): ?>
                            <div class="text-xs text-gray-500 mt-auto">
                                <i class="fa-solid fa-map-pin mr-1"></i> <?= htmlspecialchars($ev['location']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

</section>

<?php include_once __DIR__ . '/../app/views/layouts/footer.php'; ?>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        once: true,
        duration: 800,
        offset: 50
    });
</script>