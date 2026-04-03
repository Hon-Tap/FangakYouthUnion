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
                        gold: '#d4a017'
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
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<div class="bg-fyu-dark text-white py-16 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;"></div>
    
    <div class="container mx-auto px-4 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl font-serif font-bold mb-4" data-aos="fade-down">Official Announcements</h1>
        <p class="text-fyu-accent text-lg font-light max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
            Stay updated with the latest news, assemblies, and initiatives from the Fangak Youth Union.
        </p>
    </div>
</div>

<section class="max-w-6xl mx-auto px-4 py-12">

    <nav class="flex mb-10 text-sm text-gray-500" data-aos="fade-right">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="index.php" class="hover:text-fyu-primary transition flex items-center">
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
        <div class="bg-white rounded-2xl shadow-sm border border-dashed border-gray-300 p-12 text-center" data-aos="fade-up">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                <i class="fa-regular fa-folder-open text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-700">No Announcements Yet</h3>
            <p class="text-gray-500 text-sm mt-1">Check back later for updates from the Fangak Youth Union.</p>
        </div>
    <?php else: ?>
        
        <div class="grid md:grid-cols-2 gap-8">
            <?php foreach ($announcements as $index => $a): 
                $isFeatured = ($index === 0); 
                $delay = $index * 100;
            ?>
                <article class="relative bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-full group" 
                         data-aos="fade-up" data-aos-delay="<?= $delay ?>">
                    
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 <?= $isFeatured ? 'bg-fyu-primary' : 'bg-gray-200 group-hover:bg-fyu-light' ?> transition-colors"></div>

                    <div class="p-8 flex flex-col flex-grow">
                        <div class="flex items-center text-xs text-gray-500 mb-4">
                            <span class="bg-fyu-accent text-fyu-dark px-3 py-1 rounded-full font-bold uppercase tracking-wide border border-fyu-primary/10">
                                <i class="fa-solid fa-bullhorn mr-1"></i> Update
                            </span>
                            <span class="mx-3 text-gray-300">|</span>
                            <span class="flex items-center"><i class="fa-regular fa-calendar-alt mr-1.5"></i> <?= formatAnnouncementDate($a['created_at']) ?></span>
                        </div>

                        <h2 class="text-xl font-bold text-gray-800 mb-3 group-hover:text-fyu-primary transition-colors">
                            <?= htmlspecialchars($a['title']) ?>
                        </h2>

                        <div class="prose max-w-none text-gray-600 text-sm leading-relaxed mb-6 flex-grow line-clamp-3">
                            <?= nl2br(html_entity_decode($a['body'])) ?>
                        </div>

                        <?php if (!empty($a['starts_at']) && $a['starts_at'] !== '0000-00-00 00:00:00'): ?>
                            <div class="bg-fyu-accent/50 rounded-xl p-4 border border-fyu-primary/10 flex flex-col sm:flex-row gap-4 mt-auto">
                                <div class="flex items-start text-sm flex-1">
                                    <div class="bg-white p-2 rounded-lg shadow-sm text-fyu-primary mr-3 flex-shrink-0">
                                        <i class="fa-regular fa-calendar-check"></i>
                                    </div>
                                    <div>
                                        <span class="block text-xs uppercase text-gray-500 font-bold tracking-wider">Starts</span>
                                        <span class="font-semibold text-fyu-dark">
                                            <?= formatAnnouncementDate($a['starts_at']) ?>
                                            <span class="text-xs font-normal text-gray-500 block">at <?= formatTime($a['starts_at']) ?></span>
                                        </span>
                                    </div>
                                </div>

                                <?php if (!empty($a['ends_at']) && $a['ends_at'] !== '0000-00-00 00:00:00' && $a['ends_at'] != $a['starts_at']): ?>
                                    <div class="hidden sm:block w-px bg-fyu-primary/10"></div>
                                    <div class="flex items-start text-sm flex-1">
                                        <div class="bg-white p-2 rounded-lg shadow-sm text-gray-500 mr-3 flex-shrink-0">
                                            <i class="fa-regular fa-clock"></i>
                                        </div>
                                        <div>
                                            <span class="block text-xs uppercase text-gray-500 font-bold tracking-wider">Ends</span>
                                            <span class="font-semibold text-gray-700">
                                                <?= formatAnnouncementDate($a['ends_at']) ?>
                                                <span class="text-xs font-normal text-gray-500 block">at <?= formatTime($a['ends_at']) ?></span>
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

        <div class="text-center mt-16 text-gray-400 text-sm">
            <p><i class="fa-solid fa-check-circle mr-1"></i> You have reached the end of the announcements.</p>
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