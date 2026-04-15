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

// Temporary bypass for testing (Remove/uncomment in production!)
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: login.php");
//     exit();
// }

// Fake admin for preview (Remove in production)
$admin_id = $_SESSION['admin_id'] ?? 1; 

try {
    $stmt = $pdo->prepare("SELECT id, name, email FROM admins WHERE id = ? LIMIT 1");
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Fallback if no admin found during dev
    if (!$admin && $env === 'development') {
        $admin = ['name' => 'Super Admin', 'email' => 'admin@fyu.org'];
    }
} catch (Throwable $e) {
    error_log("Dashboard admin fetch error: " . $e->getMessage());
    http_response_code(500);
    die("<h2>Failed to load administrator profile.</h2>");
}

if (!$admin && $env !== 'development') {
    session_destroy();
    header("Location: login.php");
    exit();
}

$pageTitle = "FYU Admin Workspace";

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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased h-screen flex overflow-hidden selection:bg-emerald-500 selection:text-white">

    <div id="mobileOverlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 hidden transition-opacity duration-300 lg:hidden" onclick="toggleSidebar()"></div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-[280px] bg-green-950 text-slate-300 transform -translate-x-full transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col shadow-2xl lg:shadow-none border-r border-green-900/50">
        
        <div class="h-20 flex items-center px-6 shrink-0">
            <div class="flex items-center gap-3 w-full">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <i data-lucide="shield-check" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-white leading-tight">FYU Portal</h1>
                    <p class="text-[10px] uppercase tracking-widest text-emerald-400 font-semibold">Admin Workspace</p>
                </div>
            </div>
            <button class="ml-auto lg:hidden text-slate-400 hover:text-white transition-colors" onclick="toggleSidebar()">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1.5 custom-scrollbar">
            <p class="px-3 text-xs font-semibold text-green-700/80 uppercase tracking-widest mb-3">Main Navigation</p>
            
            <?php foreach ($tabs as $key => $label): ?>
                <?php 
                $isActive = ($active === $key); 
                // Settings tab gets a visual separator
                if ($key === 'settings'): 
                ?>
                    <div class="my-4 border-t border-green-900/50"></div>
                    <p class="px-3 pt-4 text-xs font-semibold text-green-700/80 uppercase tracking-widest mb-3">System</p>
                <?php endif; ?>

                <a href="?tab=<?= $key ?>" 
                   class="flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium transition-all duration-300 group relative overflow-hidden
                   <?= $isActive 
                        ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/20' 
                        : 'text-slate-400 hover:bg-green-900/40 hover:text-white' ?>">
                    
                    <?php if ($isActive): ?>
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-white rounded-r-full"></div>
                    <?php endif; ?>

                    <i data-lucide="<?= $tabIcons[$key] ?>" 
                       class="w-5 h-5 transition-transform duration-300 <?= $isActive ? 'text-white' : 'text-slate-500 group-hover:text-emerald-400 group-hover:scale-110' ?>"></i>
                    <span class="z-10"><?= $label ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="p-4 shrink-0 m-4 rounded-2xl bg-green-900/30 border border-green-800/50 backdrop-blur-sm">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center font-bold text-sm text-emerald-400 border border-slate-700">
                        <?= strtoupper(substr($admin['name'] ?? 'AD', 0, 2)) ?>
                    </div>
                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-green-950 rounded-full"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate"><?= htmlspecialchars($admin['name'] ?? 'Admin') ?></p>
                    <p class="text-xs text-slate-400 truncate"><?= htmlspecialchars($admin['email'] ?? '') ?></p>
                </div>
                <a href="logout.php" title="Logout" class="p-2 text-slate-400 hover:bg-rose-500/10 hover:text-rose-400 rounded-lg transition-all">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                </a>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50/50">
        
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-4 lg:px-8 shadow-sm z-10 shrink-0 sticky top-0">
            <div class="flex items-center gap-3">
                <button class="lg:hidden p-2.5 -ml-2 text-slate-600 hover:bg-slate-100 rounded-xl transition-colors" onclick="toggleSidebar()">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 tracking-tight hidden sm:block">
                        <?= htmlspecialchars($tabs[$active]) ?>
                    </h2>
                    <div class="flex items-center gap-2 text-xs font-medium text-slate-400 mt-0.5">
                        <i data-lucide="home" class="w-3 h-3"></i>
                        <span>/</span>
                        <span class="text-emerald-600"><?= htmlspecialchars($tabs[$active]) ?></span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button class="relative p-2 text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="bell" class="w-6 h-6"></i>
                    <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-rose-500 border-2 border-white rounded-full"></span>
                </button>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8">
            <div class="max-w-[1600px] mx-auto w-full h-full flex flex-col">
                <?php
                try {
                    if (file_exists($sectionPath)) {
                        include $sectionPath;
                    } else {
                        // Polished Empty State
                        echo "
                        <div class='flex-1 flex flex-col items-center justify-center bg-white rounded-3xl border border-slate-200 border-dashed p-12 text-center shadow-sm min-h-[400px]'>
                            <div class='w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mb-6'>
                                <i data-lucide='hammer' class='w-10 h-10 text-emerald-500'></i>
                            </div>
                            <h3 class='text-2xl font-bold text-slate-800 mb-2'>Under Construction</h3>
                            <p class='text-slate-500 max-w-md mx-auto leading-relaxed'>
                                The <strong class='text-slate-700'>{$tabs[$active]}</strong> module is currently being developed. Please check back later.
                            </p>
                        </div>";
                    }
                } catch (Throwable $e) {
                    error_log("Dashboard section crash: " . $e->getMessage());
                    echo "
                    <div class='bg-rose-50 border-l-4 border-rose-500 p-6 rounded-2xl shadow-sm flex gap-4'>
                        <i data-lucide='alert-triangle' class='w-8 h-8 text-rose-500 shrink-0'></i>
                        <div>
                            <h3 class='text-lg font-bold text-rose-800'>System Error</h3>
                            <p class='text-rose-600 mt-1'>A critical error occurred while loading this section. Please contact technical support.</p>
                        </div>
                    </div>";
                }
                ?>
            </div>
        </main>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.2);
        }
    </style>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Mobile Sidebar Toggle Logic
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobileOverlay');

        function toggleSidebar() {
            const isClosed = sidebar.classList.contains('-translate-x-full');
            if (isClosed) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Prevent body scrolling when open
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }
    </script>
</body>
</html>