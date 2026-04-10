<?php
// sections/dashboard.php

$stats = [
    'news'     => 0,
    'projects' => 0,
    'events'   => 0,
    'members'  => 0,
];

$queries = [
    'news'     => "SELECT COUNT(*) FROM blog_posts",
    'projects' => "SELECT COUNT(*) FROM projects",
    'events'   => "SELECT COUNT(*) FROM events",
    'members'  => "SELECT COUNT(*) FROM users",
];

// Fetch the stats safely
foreach ($queries as $key => $sql) {
    try {
        $stmt = $pdo->query($sql);
        if ($stmt) {
            $stats[$key] = (int) $stmt->fetchColumn();
        }
    } catch (Throwable $e) {
        error_log("Dashboard stat error ($key): " . $e->getMessage());
        $stats[$key] = 0;
    }
}
?>

<div class="bg-green-700 rounded-xl shadow-sm p-6 mb-8 text-white">
    <h2 class="text-2xl font-bold mb-1">Welcome back, <?= htmlspecialchars($admin['name'] ?? 'Admin') ?>! 👋</h2>
    <p class="text-green-100 text-sm">Here is a quick overview of what's happening in the FYU system today.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 flex items-center gap-4 transition-transform hover:-translate-y-1">
        <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
            <i data-lucide="newspaper" class="w-7 h-7"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500">Total News</p>
            <h3 class="text-2xl font-bold text-slate-800"><?= number_format($stats['news']) ?></h3>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 flex items-center gap-4 transition-transform hover:-translate-y-1">
        <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
            <i data-lucide="briefcase" class="w-7 h-7"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500">Active Projects</p>
            <h3 class="text-2xl font-bold text-slate-800"><?= number_format($stats['projects']) ?></h3>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 flex items-center gap-4 transition-transform hover:-translate-y-1">
        <div class="w-14 h-14 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center shrink-0">
            <i data-lucide="calendar-days" class="w-7 h-7"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500">Upcoming Events</p>
            <h3 class="text-2xl font-bold text-slate-800"><?= number_format($stats['events']) ?></h3>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 flex items-center gap-4 transition-transform hover:-translate-y-1">
        <div class="w-14 h-14 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
            <i data-lucide="users" class="w-7 h-7"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500">Registered Members</p>
            <h3 class="text-2xl font-bold text-slate-800"><?= number_format($stats['members']) ?></h3>
        </div>
    </div>

</div>