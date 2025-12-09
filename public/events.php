<?php
$pageTitle = "Events - Fangak Youth Union";
include_once __DIR__ . '/../app/views/layouts/header.php';
include_once __DIR__ . "/../app/config/db.php";

// Fetch events ordered by nearest date
$stmt = $pdo->query("
    SELECT *
    FROM events
    ORDER BY event_date ASC, created_at DESC
");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="max-w-6xl mx-auto px-4 py-10">

    <h1 class="text-3xl font-bold text-green-800 mb-8">Upcoming & Recent Events</h1>

    <?php if (empty($events)): ?>
        <div class="bg-white dark:bg-slate-800 p-8 rounded-xl shadow text-center text-slate-500">
            No events available.
        </div>
    <?php else: ?>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($events as $ev): ?>
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden">

                    <?php if (!empty($ev['image'])): ?>
                        <img src="/uploads/events/<?= htmlspecialchars($ev['image']) ?>"
                             class="w-full h-40 object-cover" alt="Event Image">
                    <?php else: ?>
                        <div class="w-full h-40 bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500">
                            No Image
                        </div>
                    <?php endif; ?>

                    <div class="p-5">

                        <h2 class="text-lg font-semibold text-green-800 mb-2">
                            <?= htmlspecialchars($ev['title']) ?>
                        </h2>

                        <p class="text-slate-600 dark:text-slate-400 mb-3 line-clamp-3">
                            <?= htmlspecialchars($ev['description']) ?>
                        </p>

                        <div class="text-sm text-green-600 space-y-1 mb-4">
                            <p><i class="fa-solid fa-calendar"></i> <?= $ev['event_date'] ?></p>
                            <?php if ($ev['location']): ?>
                                <p><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($ev['location']) ?></p>
                            <?php endif; ?>
                        </div>

                        <a href="#"
                           class="inline-block bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg shadow">
                            View Details
                        </a>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</section>

<?php include_once __DIR__ . '/../app/views/layouts/footer.php'; ?>
