<?php

$pageTitle = "Announcements - Fangak Youth Union";

require_once __DIR__ . '/../app/config/db.php';

if (!$pdo) {
    error_log("Announcements page: DB connection unavailable");
    $announcements = [];
} else {
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM announcements
            WHERE is_published = 1
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        $announcements = $stmt->fetchAll();
    } catch (Throwable $e) {
        error_log("Announcement Query Error: " . $e->getMessage());
        $announcements = [];
    }
}

function formatAnnouncementDate($dateStr) {
    return date('F j, Y', strtotime($dateStr));
}

function formatTime($dateStr) {
    return date('g:i A', strtotime($dateStr));
}

include_once __DIR__ . '/../app/views/layouts/header.php';
?>

<!-- TAILWIND & ANIMATIONS CONFIG (If not already in header) -->
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
    .prose p { margin-bottom: 1rem; }
</style>

<!-- HERO HEADER -->
<div class="bg-fyu-dark text-white py-12 relative overflow-hidden">
    <div class="absolute inset-0 bg-black/20"></div>
    <div class="container mx-auto px-4 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl font-serif font-bold mb-2" data-aos="fade-down">Official Announcements</h1>
        <p class="text-gray-200 text-lg font-light" data-aos="fade-up" data-aos-delay="100">Stay updated with the latest news, assemblies, and initiatives.</p>
    </div>
</div>

<!-- MAIN CONTENT -->
<section class="max-w-4xl mx-auto px-4 py-12">

    <!-- BREADCRUMB -->
    <nav class="flex mb-8 text-sm text-gray-500" aria-label="Breadcrumb" data-aos="fade-right">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="index.php" class="inline-flex items-center hover:text-fyu-primary transition">
                    <i class="fa-solid fa-home mr-2"></i> Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-gray-400 mx-1"></i>
                    <span class="text-gray-800 font-medium">Announcements</span>
                </div>
            </li>
        </ol>
    </nav>

    <?php if (empty($announcements)): ?>
        <!-- EMPTY STATE -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center" data-aos="zoom-in">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-400">
                <i class="fa-regular fa-folder-open text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">No Announcements Yet</h3>
            <p class="text-gray-500">Check back later for updates from the Fangak Youth Union.</p>
        </div>
    <?php else: ?>
        
        <!-- ANNOUNCEMENTS FEED -->
        <div class="space-y-8">
            <?php foreach ($announcements as $index => $a): 
                // Determine styling based on logic (e.g., highlight the first one)
                $isFeatured = ($index === 0); 
                $delay = $index * 100;
            ?>
                <article class="relative bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 group" 
                         data-aos="fade-up" data-aos-delay="<?= $delay ?>">
                    
                    <!-- Left Accent Border -->
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 <?= $isFeatured ? 'bg-fyu-primary' : 'bg-gray-300 group-hover:bg-fyu-light' ?> transition-colors"></div>

                    <div class="p-6 md:p-8 ml-2">
                        <!-- Meta Header -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 gap-2">
                            <div class="flex items-center text-sm text-gray-500">
                                <span class="bg-green-50 text-fyu-dark px-3 py-1 rounded-full font-medium text-xs border border-green-100">
                                    <i class="fa-solid fa-bullhorn mr-1"></i> Update
                                </span>
                                <span class="mx-3 text-gray-300">|</span>
                                <span>Posted on <?= formatAnnouncementDate($a['created_at']) ?></span>
                            </div>
                        </div>

                        <!-- Title -->
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 group-hover:text-fyu-primary transition-colors">
                            <?= htmlspecialchars($a['title']) ?>
                        </h2>

                        <!-- Body Content -->
                        <div class="prose max-w-none text-gray-600 leading-relaxed mb-6">
                            <?= nl2br(html_entity_decode($a['body'])) ?>
                        </div>

                        <!-- Event Details Box (Only shows if dates are set) -->
                        <?php if (!empty($a['starts_at']) && $a['starts_at'] !== '0000-00-00 00:00:00'): ?>
                            <div class="bg-fyu-accent/50 rounded-xl p-4 border border-fyu-primary/10 flex flex-col sm:flex-row gap-6 mt-6">
                                
                                <!-- Start Date -->
                                <div class="flex items-start">
                                    <div class="bg-white p-2 rounded-lg shadow-sm text-fyu-primary mr-3">
                                        <i class="fa-regular fa-calendar-check text-xl"></i>
                                    </div>
                                    <div>
                                        <span class="block text-xs uppercase text-gray-500 font-bold tracking-wider">Starts</span>
                                        <span class="font-semibold text-fyu-dark">
                                            <?= formatAnnouncementDate($a['starts_at']) ?>
                                            <span class="text-sm font-normal text-gray-500 ml-1">at <?= formatTime($a['starts_at']) ?></span>
                                        </span>
                                    </div>
                                </div>

                                <!-- End Date (Optional) -->
                                <?php if (!empty($a['ends_at']) && $a['ends_at'] !== '0000-00-00 00:00:00' && $a['ends_at'] != $a['starts_at']): ?>
                                    <div class="hidden sm:block w-px bg-fyu-primary/20"></div>
                                    <div class="flex items-start">
                                        <div class="bg-white p-2 rounded-lg shadow-sm text-gray-500 mr-3">
                                            <i class="fa-regular fa-clock text-xl"></i>
                                        </div>
                                        <div>
                                            <span class="block text-xs uppercase text-gray-500 font-bold tracking-wider">Ends</span>
                                            <span class="font-semibold text-gray-700">
                                                <?= formatAnnouncementDate($a['ends_at']) ?>
                                                <span class="text-sm font-normal text-gray-500 ml-1">at <?= formatTime($a['ends_at']) ?></span>
                                            </span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination / End Note -->
        <div class="text-center mt-12 text-gray-400 text-sm">
            <p>You have reached the end of the announcements.</p>
        </div>

    <?php endif; ?>

</section>

<?php include_once __DIR__ . '/../app/views/layouts/footer.php'; ?>

<!-- Animation Script -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        once: true,
        duration: 800,
        offset: 50
    });
</script>