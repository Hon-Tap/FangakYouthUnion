<?php
declare(strict_types=1);
ob_start(); // Start output buffering to prevent header errors
session_start();

// 1. Authentication & Configuration
// Ensure auth.php is included BEFORE any HTML is sent
require_once __DIR__ . '/includes/auth.php'; 
require_once __DIR__ . '/../../app/config/db.php';

// 2. Data Fetching Logic
try {
    // Fetch Pending Members count for notifications
    $pendingMembers = (int)$pdo->query("SELECT COUNT(*) FROM members WHERE status = 'pending'")->fetchColumn();
    $totalNotifications = $pendingMembers; 
} catch (Exception $e) {
    $totalNotifications = 0;
}

// 3. Navigation Configuration
$active = $_GET['tab'] ?? 'dashboard';
$tabs = [
    "dashboard"     => ["label" => "Dashboard", "icon" => "layout-dashboard"],
    "news"          => ["label" => "News", "icon" => "newspaper"],
    "projects"      => ["label" => "Projects", "icon" => "briefcase"],
    "events"        => ["label" => "Events", "icon" => "calendar-days"],
    "members"       => ["label" => "Members", "icon" => "users"],
    "announcements" => ["label" => "Announcements", "icon" => "megaphone"],
    "settings"      => ["label" => "Settings", "icon" => "settings"]
];

// 4. Determine content path
$sectionPath = ($active === 'settings') ? __DIR__ . '/settings.php' : __DIR__ . "/sections/{$active}.php";

// 5. Begin UI Output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FYU Admin Portal | <?= $tabs[$active]['label'] ?? 'Admin' ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        
        /* Mini Sidebar Styles */
        .sidebar-mini { width: 80px !important; }
        .sidebar-mini .nav-text, 
        .sidebar-mini .logo-text, 
        .sidebar-mini .user-details { display: none; }
        .sidebar-mini .nav-item { justify-content: center; padding: 0.75rem; }
        .sidebar-mini .nav-header { text-align: center; font-size: 0.6rem; }
        
        /* Tooltip-like effect for mini-sidebar could be added here */
    </style>
</head>
<body class="bg-slate-50 text-slate-800 h-screen flex overflow-hidden">

    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <aside id="mainSidebar" class="sidebar-transition fixed inset-y-0 left-0 z-50 w-[280px] bg-green-950 text-slate-300 transform -translate-x-full lg:static lg:translate-x-0 flex flex-col border-r border-green-900/50 shadow-xl">
        
        <div class="h-20 flex items-center px-6 shrink-0 border-b border-green-900/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/20">
                    <i data-lucide="shield-check" class="w-6 h-6 text-white"></i>
                </div>
                <div class="logo-text whitespace-nowrap">
                    <h1 class="text-xl font-bold text-white tracking-tight">FYU Portal</h1>
                    <p class="text-[10px] uppercase text-emerald-400 font-bold">Workspace</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1.5 custom-scrollbar">
            <?php foreach ($tabs as $key => $data): $isActive = ($active === $key); ?>
                <a href="?tab=<?= $key ?>" class="nav-item flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium transition-all group <?= $isActive ? 'bg-emerald-600 text-white shadow-md' : 'hover:bg-green-900/40 hover:text-white text-slate-400' ?>">
                    <i data-lucide="<?= $data['icon'] ?>" class="w-5 h-5 shrink-0"></i>
                    <span class="nav-text truncate"><?= $data['label'] ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="p-4 bg-green-900/20 border-t border-green-900/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center font-bold text-emerald-400 shrink-0 border border-slate-700">SA</div>
                <div class="user-details min-w-0 flex-1">
                    <p class="text-xs font-bold text-white truncate">Super Admin</p>
                    <a href="logout.php" class="text-[10px] text-rose-400 font-bold hover:underline">LOGOUT</a>
                </div>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-4 lg:px-8 shrink-0 z-30">
            <div class="flex items-center gap-4">
                <button class="hidden lg:flex p-2 text-slate-500 hover:bg-slate-100 rounded-xl" onclick="toggleMiniSidebar()">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <button class="lg:hidden p-2 text-slate-500 hover:bg-slate-100 rounded-xl" onclick="toggleSidebar()">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <h2 class="text-xl font-bold text-slate-800 capitalize"><?= $active ?></h2>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative">
                    <button id="notifBtn" class="p-2.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all relative">
                        <i data-lucide="bell" class="w-6 h-6"></i>
                        <?php if ($totalNotifications > 0): ?>
                            <span class="absolute top-2 right-2 w-5 h-5 bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center rounded-full border-2 border-white">
                                <?= $totalNotifications ?>
                            </span>
                        <?php endif; ?>
                    </button>

                    <div id="notifModal" class="hidden absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in duration-200">
                        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                            <h3 class="font-bold text-slate-800">Notifications</h3>
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-full">LATEST</span>
                        </div>
                        <div class="max-h-[350px] overflow-y-auto">
                            <?php if ($pendingMembers > 0): ?>
                                <a href="?tab=members&status=pending" class="block p-4 hover:bg-slate-50 transition-colors border-b border-slate-50">
                                    <div class="flex gap-3">
                                        <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                            <i data-lucide="user-plus" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800">New Member Registration</p>
                                            <p class="text-xs text-slate-500 mt-0.5"><?= $pendingMembers ?> members are awaiting approval.</p>
                                        </div>
                                    </div>
                                </a>
                            <?php else: ?>
                                <div class="p-10 text-center text-slate-400 italic text-sm">No new alerts.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-8">
            <div class="max-w-7xl mx-auto">
                <?php include $sectionPath; ?>
            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();

        function toggleMiniSidebar() {
            document.getElementById('mainSidebar').classList.toggle('sidebar-mini');
        }

        function toggleSidebar() {
            const sb = document.getElementById('mainSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sb.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Notification Modal Logic
        const btn = document.getElementById('notifBtn');
        const modal = document.getElementById('notifModal');
        
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            modal.classList.toggle('hidden');
        });

        window.addEventListener('click', (e) => {
            if (!modal.contains(e.target) && !btn.contains(e.target)) {
                modal.classList.add('hidden');
            }
        });
    </script>
</body>
</html>