<?php
/**
 * Rewritten Events Page - FYU
 * Features automatic Past/Upcoming logic and Cloudinary Support
 */
require_once __DIR__ . '/../app/config/db.php';

function get_event_media(?string $path): string {
    if (!$path) return 'assets/images/placeholder-event.jpg';
    if (str_starts_with($path, 'http')) return $path;
    return "/uploads/events/" . ltrim($path, '/');
}

$upcoming = [];
$past = [];
$today = date('Y-m-d');

try {
    $stmt = $pdo->query("SELECT * FROM events ORDER BY event_date ASC");
    while ($row = $stmt->fetch()) {
        if (($row['event_date'] ?? $today) >= $today) {
            $upcoming[] = $row;
        } else {
            $past[] = $row;
        }
    }
    $past = array_reverse($past);
} catch (PDOException $e) {
    error_log($e->getMessage());
}

$pageTitle = "Community Events - Fangak Youth Union";
include_once __DIR__ . '/../app/views/layouts/header.php';
?>

<main class="bg-white min-h-screen">
    <header class="bg-[#0e3a24] py-24 text-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
        <div class="container mx-auto px-6 relative z-10">
            <h1 class="text-5xl font-serif font-bold text-white mb-4" data-aos="zoom-out">Union Events</h1>
            <p class="text-green-200/70 text-lg font-light">Join us in shaping the future of Fangak through collective action.</p>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-6 py-16">
        <div class="mb-24">
            <div class="flex items-center gap-4 mb-12">
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Upcoming</h2>
                <div class="h-px flex-1 bg-gray-100"></div>
            </div>

            <?php if(empty($upcoming)): ?>
                <div class="bg-gray-50 rounded-3xl p-20 text-center border-2 border-dashed border-gray-200">
                    <p class="text-gray-400">No scheduled events at this time. Check back soon!</p>
                </div>
            <?php else: ?>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
                    <?php foreach($upcoming as $ev): ?>
                    <article class="group bg-white rounded-3xl border border-gray-100 overflow-hidden hover:shadow-2xl transition-all" data-aos="fade-up">
                        <div class="relative h-52">
                            <img src="<?= get_event_media($ev['image']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute bottom-4 left-4">
                                <span class="bg-white/90 backdrop-blur px-3 py-1 rounded-lg text-xs font-bold text-green-800 shadow-sm">
                                    <?= date('d M, Y', strtotime($ev['event_date'])) ?>
                                </span>
                            </div>
                        </div>
                        <div class="p-8">
                            <h3 class="text-xl font-bold text-gray-900 mb-3"><?= htmlspecialchars($ev['title']) ?></h3>
                            <p class="text-gray-500 text-sm line-clamp-2 mb-6"><?= htmlspecialchars($ev['description']) ?></p>
                            <div class="flex items-center text-xs font-bold text-amber-600 uppercase">
                                <i class="fa-solid fa-location-dot mr-2"></i> <?= htmlspecialchars($ev['location'] ?: 'Fangak') ?>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if(!empty($past)): ?>
        <section>
            <div class="flex items-center gap-4 mb-8">
                <h2 class="text-2xl font-bold text-gray-400 uppercase tracking-widest text-sm">Past Memories</h2>
                <div class="h-px flex-1 bg-gray-100"></div>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach($past as $ev): ?>
                <div class="bg-gray-50 p-6 rounded-2xl border border-transparent hover:border-gray-200 hover:bg-white transition-all">
                    <span class="text-[10px] font-black text-gray-300 block mb-2 uppercase tracking-tighter">
                        <?= date('F Y', strtotime($ev['event_date'])) ?>
                    </span>
                    <h4 class="font-bold text-gray-700 leading-snug"><?= htmlspecialchars($ev['title']) ?></h4>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </div>
</main>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({ once: true });</script>

<?php include_once __DIR__ . '/../app/views/layouts/footer.php'; ?>