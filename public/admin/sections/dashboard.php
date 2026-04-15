<?php
// sections/dashboard.php

$stats = [
    'news'     => 0,
    'projects' => 0,
    'events'   => 0,
    'members'  => 0,
];

// FIXED: Changed 'users' to 'members' in the last query
$queries = [
    'news'     => "SELECT COUNT(*) FROM blog_posts",
    'projects' => "SELECT COUNT(*) FROM projects",
    'events'   => "SELECT COUNT(*) FROM events",
    'members'  => "SELECT COUNT(*) FROM members", 
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

<div class="bg-gradient-to-r from-green-700 to-green-600 rounded-xl shadow-md p-8 mb-8 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 -mr-8 -mt-8 w-48 h-48 rounded-full bg-white opacity-10"></div>
    
    <div class="relative z-10">
        <h2 class="text-3xl font-extrabold mb-2">Welcome back, <?= htmlspecialchars($admin['name'] ?? 'Super Admin') ?>! 👋</h2>
        <p class="text-green-50 text-lg font-medium opacity-90">Here is a quick overview of what's happening in the FYU system today.</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 flex items-center gap-5 transition-all duration-300 hover:shadow-md hover:-translate-y-1 group">
        <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
            <i data-lucide="newspaper" class="w-7 h-7"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Total News</p>
            <h3 class="text-3xl font-extrabold text-slate-800 mt-1"><?= number_format($stats['news']) ?></h3>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 flex items-center gap-5 transition-all duration-300 hover:shadow-md hover:-translate-y-1 group">
        <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
            <i data-lucide="briefcase" class="w-7 h-7"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Active Projects</p>
            <h3 class="text-3xl font-extrabold text-slate-800 mt-1"><?= number_format($stats['projects']) ?></h3>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 flex items-center gap-5 transition-all duration-300 hover:shadow-md hover:-translate-y-1 group">
        <div class="w-14 h-14 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center shrink-0 group-hover:bg-orange-600 group-hover:text-white transition-colors duration-300">
            <i data-lucide="calendar-days" class="w-7 h-7"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Upcoming Events</p>
            <h3 class="text-3xl font-extrabold text-slate-800 mt-1"><?= number_format($stats['events']) ?></h3>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 flex items-center gap-5 transition-all duration-300 hover:shadow-md hover:-translate-y-1 group">
        <div class="w-14 h-14 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 group-hover:bg-purple-600 group-hover:text-white transition-colors duration-300">
            <i data-lucide="users" class="w-7 h-7"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Registered Members</p>
            <h3 class="text-3xl font-extrabold text-slate-800 mt-1"><?= number_format($stats['members']) ?></h3>
        </div>
    </div>

</div>