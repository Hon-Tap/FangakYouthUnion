<?php
declare(strict_types=1);

$env = getenv('APP_ENV') ?: 'production';
if ($env === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    error_reporting(E_ALL);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../app/config/db.php';

if (!isset($pdo)) {
    http_response_code(500);
    die("<h2>System Error: Database connection not available.</h2>");
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = (int) $_SESSION['admin_id'];

try {
    $stmt = $pdo->prepare("SELECT id, name, email FROM admins WHERE id = ? LIMIT 1");
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log("Dashboard admin fetch error: " . $e->getMessage());
    http_response_code(500);
    die("<h2>Failed to load administrator profile.</h2>");
}

if (!$admin) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$pageTitle = "Admin Dashboard";

$tabs = [
    "dashboard"     => "Dashboard",
    "news"          => "News",
    "projects"      => "Projects",
    "events"        => "Events",
    "members"       => "Members",
    "announcements" => "Announcements",
    "settings"      => "Settings"
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

$active = $_GET['tab'] ?? 'dashboard';
if (!array_key_exists($active, $tabs)) {
    $active = 'dashboard';
}

$sectionPath = ($active === 'settings') ? __DIR__ . '/settings.php' : __DIR__ . "/sections/{$active}.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased h-screen flex overflow-hidden">

    <div id="mobileOverlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden transition-opacity duration-300 lg:hidden" onclick="toggleSidebar()"></div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-green-900 text-white transform -translate-x-full transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col shadow-2xl lg:shadow-none">
        
        <div class="h-16 flex items-center px-6 border-b border-green-800 bg-green-900 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-green-500 flex items-center justify-center">
                    <i data-lucide="shield-check" class="w-5 h-5 text-white"></i>
                </div>
                <h1 class="text-xl font-bold tracking-tight">FYU Admin</h1>
            </div>
            <button class="ml-auto lg:hidden text-green-400 hover:text-white" onclick="toggleSidebar()">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
            <p class="px-3 text-xs font-semibold text-green-700 uppercase tracking-wider mb-2">Main Menu</p>
            <?php foreach ($tabs as $key => $label): ?>
                <?php $isActive = ($active === $key); ?>
                <a href="?tab=<?= $key ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group <?= $isActive ? 'bg-green-600 text-white shadow-md' : 'text-slate-300 hover:bg-green-800 hover:text-white' ?>">
                    <i data-lucide="<?= $tabIcons[$key] ?>" class="w-5 h-5 transition-colors <?= $isActive ? 'text-white' : 'text-slate-400 group-hover:text-white' ?>"></i>
                    <span><?= $label ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="p-4 border-t border-green-800 bg-green-900/50 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-green-700 flex items-center justify-center font-bold text-sm text-white">
                    <?= strtoupper(substr($admin['name'] ?? 'AD', 0, 2)) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate"><?= htmlspecialchars($admin['name'] ?? 'Admin') ?></p>
                    <p class="text-xs text-green-300 truncate"><?= htmlspecialchars($admin['email'] ?? '') ?></p>
                </div>
                <a href="logout.php" title="Logout" class="p-2 text-green-400 hover:text-red-400 transition-colors">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                </a>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <header class="h-16 bg-white border-b border-slate-200 flex items-center px-4 lg:px-8 shadow-sm z-10 shrink-0">
            <button class="lg:hidden p-2 -ml-2 mr-3 text-slate-500 hover:bg-slate-100 rounded-md" onclick="toggleSidebar()">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <h2 class="text-xl font-bold text-slate-800 tracking-tight">
                <?= htmlspecialchars($tabs[$active]) ?>
            </h2>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto w-full">
                <?php
                try {
                    if (file_exists($sectionPath)) {
                        include $sectionPath;
                    } else {
                        echo "<div class='bg-white rounded-xl border border-slate-200 p-12 text-center'>
                                <h3 class='text-lg font-medium text-slate-900'>Under Construction</h3>
                                <p class='text-slate-500 mt-2'>The <strong>{$tabs[$active]}</strong> section module hasn't been created yet.</p>
                              </div>";
                    }
                } catch (Throwable $e) {
                    error_log("Dashboard section crash: " . $e->getMessage());
                    echo "<div class='bg-red-50 border border-red-200 text-red-800 p-6 rounded-lg'>
                            <strong>System Error:</strong><br>A critical error occurred while loading this section.
                          </div>";
                }
                ?>
            </div>
        </main>
    </div>

    <script>
        // Initialize Icons
        lucide.createIcons();

        // Mobile Sidebar Toggle Logic
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobileOverlay');

        function toggleSidebar() {
            const isClosed = sidebar.classList.contains('-translate-x-full');
            if (isClosed) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }
    </script>
</body>
</html>