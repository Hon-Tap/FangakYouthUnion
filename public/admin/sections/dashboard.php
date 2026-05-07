<?php
// sections/dashboard.php

$stats = ['news' => 0, 'projects' => 0, 'events' => 0, 'members' => 0];

$queries = [
    'news'     => "SELECT COUNT(*) FROM blog_posts",
    'projects' => "SELECT COUNT(*) FROM projects",
    'events'   => "SELECT COUNT(*) FROM events",
    'members'  => "SELECT COUNT(*) FROM members",
];

foreach ($queries as $key => $sql) {
    try {
        $stats[$key] = (int) $pdo->query($sql)->fetchColumn();
    } catch (Throwable $e) {
        $stats[$key] = 0;
    }
}
?>

<div class="bg-gradient-to-br from-green-700 via-emerald-600 to-green-600 rounded-3xl shadow-xl p-8 md:p-12 mb-10 text-white relative overflow-hidden group">
    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white/10 blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
    <div class="relative z-10">
        <h2 class="text-3xl md:text-4xl font-extrabold mb-3 tracking-tight">Welcome back, <?= htmlspecialchars($admin['name'] ?? 'Super Admin') ?>! 👋</h2>
        <p class="text-emerald-50 text-lg opacity-90 max-w-2xl leading-relaxed">
            Everything looks good today. You have <span class="font-bold underline decoration-wavy underline-offset-4"><?= $stats['members'] ?> total members</span> in the ecosystem.
        </p>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">

    <a href="?tab=news" class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col gap-4 transition-all duration-300 hover:border-emerald-500 hover:shadow-2xl hover:shadow-emerald-500/10 hover:-translate-y-2 group">
        <div class="flex justify-between items-start">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 shadow-sm">
                <i data-lucide="newspaper" class="w-7 h-7"></i>
            </div>
            <i data-lucide="arrow-up-right" class="w-5 h-5 text-slate-300 group-hover:text-emerald-500 transition-colors"></i>
        </div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total News</p>
            <h3 class="text-3xl font-black text-slate-800 mt-1"><?= number_format($stats['news']) ?></h3>
        </div>
    </a>

    <a href="?tab=projects" class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col gap-4 transition-all duration-300 hover:border-emerald-500 hover:shadow-2xl hover:shadow-emerald-500/10 hover:-translate-y-2 group">
        <div class="flex justify-between items-start">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500 shadow-sm">
                <i data-lucide="briefcase" class="w-7 h-7"></i>
            </div>
            <i data-lucide="arrow-up-right" class="w-5 h-5 text-slate-300 group-hover:text-emerald-500 transition-colors"></i>
        </div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Active Projects</p>
            <h3 class="text-3xl font-black text-slate-800 mt-1"><?= number_format($stats['projects']) ?></h3>
        </div>
    </a>

    <a href="?tab=events" class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col gap-4 transition-all duration-300 hover:border-emerald-500 hover:shadow-2xl hover:shadow-emerald-500/10 hover:-translate-y-2 group">
        <div class="flex justify-between items-start">
            <div class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-all duration-500 shadow-sm">
                <i data-lucide="calendar-days" class="w-7 h-7"></i>
            </div>
            <i data-lucide="arrow-up-right" class="w-5 h-5 text-slate-300 group-hover:text-emerald-500 transition-colors"></i>
        </div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Upcoming Events</p>
            <h3 class="text-3xl font-black text-slate-800 mt-1"><?= number_format($stats['events']) ?></h3>
        </div>
    </a>

    <a href="?tab=members" class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col gap-4 transition-all duration-300 hover:border-emerald-500 hover:shadow-2xl hover:shadow-emerald-500/10 hover:-translate-y-2 group">
        <div class="flex justify-between items-start">
            <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-all duration-500 shadow-sm">
                <i data-lucide="users" class="w-7 h-7"></i>
            </div>
            <i data-lucide="arrow-up-right" class="w-5 h-5 text-slate-300 group-hover:text-emerald-500 transition-colors"></i>
        </div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Registered Members</p>
            <h3 class="text-3xl font-black text-slate-800 mt-1"><?= number_format($stats['members']) ?></h3>
        </div>
    </a>

</div>