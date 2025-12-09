<?php
// --- Admin Dashboard (Modern & Responsive) ---
session_start();
require_once __DIR__ . '/../../app/config/db.php';

// 1. Authentication Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// 2. Fetch Admin Data
try {
    $stmt = $pdo->prepare("SELECT id, name, email FROM admins WHERE id = ?");
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error loading admin info: " . $e->getMessage());
}

if (!$admin) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$pageTitle = "Admin Dashboard";

// 3. Navigation Configuration
$tabs = [
    "dashboard"      => "Dashboard",
    "news"           => "News",
    "projects"       => "Projects",
    "events"         => "Events",
    "members"        => "Members",
    "announcements"  => "Announcements",
    "settings"       => "Settings"
];

$tabIcons = [
    "dashboard"     => "layout-dashboard",
    "news"          => "newspaper",
    "projects"      => "briefcase",
    "events"        => "calendar-days",
    "members"       => "users",
    "announcements" => "megaphone",
    "settings"      => "settings"
];

// 4. Secure Routing Logic
$active = $_GET['tab'] ?? "dashboard";
if (!array_key_exists($active, $tabs)) {
    $active = "dashboard";
}

// ROUTING FIX: Check if it's settings (root folder) or others (sections folder)
if ($active === 'settings') {
    $sectionPath = __DIR__ . '/settings.php';
} else {
    $sectionPath = __DIR__ . "/sections/{$active}.php";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
    <style>
        /* Custom Scrollbar for a cleaner look */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>

<body class="bg-green-50 font-sans text-slate-800 antialiased h-screen flex overflow-hidden">

    <div id="mobileOverlay" 
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden transition-opacity duration-300 lg:hidden" 
         onclick="closeSidebar()">
    </div>

    <aside id="sidebar" 
           class="fixed inset-y-0 left-0 z-50 w-64 bg-green-900 text-white transform -translate-x-full transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col shadow-2xl lg:shadow-none">
        
        <div class="h-16 flex items-center px-6 border-b border-green-800 bg-green-900">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-green-500 flex items-center justify-center">
                    <i data-lucide="shield-check" class="w-5 h-5 text-white"></i>
                </div>
                <h1 class="text-xl font-bold tracking-tight">FYU Admin</h1>
            </div>
            <button class="ml-auto lg:hidden text-green-400 hover:text-white" onclick="closeSidebar()">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
            <p class="px-3 text-xs font-semibold text-green-700 uppercase tracking-wider mb-2">Main Menu</p>
            
            <?php foreach ($tabs as $key => $label): ?>
                <?php $isActive = ($active === $key); ?>
                <a href="?tab=<?= $key ?>" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group
                   <?= $isActive 
                       ? 'bg-green-600 text-white shadow-md shadow-green-900/20' 
                       : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
                    <i data-lucide="<?= $tabIcons[$key] ?>" 
                       class="w-5 h-5 transition-colors <?= $isActive ? 'text-green-200' : 'text-slate-500 group-hover:text-white' ?>"></i>
                    <span><?= $label ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="p-4 border-t border-slate-800 bg-slate-900/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-indigo-500/20 text-indigo-300 flex items-center justify-center font-bold text-sm border border-indigo-500/30">
                    <?= strtoupper(substr($admin['name'], 0, 2)) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate"><?= htmlspecialchars($admin['name']) ?></p>
                    <p class="text-xs text-slate-500 truncate"><?= htmlspecialchars($admin['email']) ?></p>
                </div>
                <a href="logout.php" title="Logout" class="p-2 text-slate-400 hover:text-red-400 transition-colors">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                </a>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 lg:px-8 shadow-sm z-10">
            
            <div class="flex items-center gap-4">
                <button class="lg:hidden p-2 -ml-2 text-slate-500 hover:bg-slate-100 rounded-md" onclick="openSidebar()">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight"><?= $tabs[$active] ?></h2>
            </div>

            <div class="flex items-center gap-3">
                <?php if (in_array($active, ["news", "projects", "events", "announcements"])): ?>
                    <button class="hidden sm:flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition shadow-sm shadow-indigo-200">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>New <?= rtrim($tabs[$active], 's') ?></span>
                    </button>
                    <button class="sm:hidden p-2 bg-indigo-600 text-white rounded-lg">
                        <i data-lucide="plus" class="w-5 h-5"></i>
                    </button>
                <?php endif; ?>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto bg-slate-50 p-4 lg:p-8">
            <div class="max-w-7xl mx-auto">
                <?php 
                    if (file_exists($sectionPath)) {
                        // Wrap included content in a fade-in div
                        echo '<div class="animate-fade-in">';
                        include $sectionPath;
                        echo '</div>';
                    } else {
                        ?>
                        <div class="bg-white rounded-xl border border-slate-200 p-12 text-center shadow-sm">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 text-red-600 mb-4">
                                <i data-lucide="file-warning" class="w-8 h-8"></i>
                            </div>
                            <h3 class="text-lg font-medium text-slate-900">Section Not Found</h3>
                            <p class="text-slate-500 mt-2">
                                The file <code><?= htmlspecialchars(basename($sectionPath)) ?></code> does not exist.
                            </p>
                        </div>
                        <?php
                    }
                ?>
            </div>
        </main>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobileOverlay');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            // Small delay to allow display:block to apply before opacity transition
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
            }, 10);
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('opacity-0');
            
            // Wait for transition to finish before hiding
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300);
        }

        // Add simple fade-in animation to style
        document.head.insertAdjacentHTML("beforeend", `<style>.animate-fade-in { animation: fadeIn 0.3s ease-in-out; } @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }</style>`)
    </script>
</body>
</html>