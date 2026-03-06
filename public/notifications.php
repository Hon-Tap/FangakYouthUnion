<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$pageTitle = "Notifications - Fangak Youth Union";
include_once __DIR__ . "/../app/views/layouts/header.php";

// Ensure DB connection exists (switching to PDO to match announcements.php)
if (!isset($pdo)) {
    include_once __DIR__ . "/../app/config/db.php";
}

// 1. FETCH DATA
try {
    // Assuming notifications are global based on your original code. 
    // If they are user-specific, change to: WHERE user_id = :uid
    $stmt = $pdo->prepare("
        SELECT * FROM notifications 
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Notification Query Error: " . $e->getMessage());
    $notifications = [];
}

// 2. HELPER FUNCTIONS
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
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
</style>

<div class="bg-fyu-dark text-white py-12 relative overflow-hidden">
    <div class="absolute inset-0 bg-black/20"></div>
    <div class="container mx-auto px-4 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl font-serif font-bold mb-2" data-aos="fade-down">Notifications</h1>
        <p class="text-gray-200 text-lg font-light" data-aos="fade-up" data-aos-delay="100">Updates, alerts, and activity related to you.</p>
    </div>
</div>

<section class="max-w-3xl mx-auto px-4 py-12">

    <nav class="flex mb-8 text-sm text-gray-500" aria-label="Breadcrumb" data-aos="fade-right">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="index.php" class="inline-flex items-center hover:text-fyu-primary transition">
                    <i class="fa-solid fa-home mr-2"></i> Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-gray-400 mx-1"></i>
                    <span class="text-gray-800 font-medium">Notifications</span>
                </div>
            </li>
        </ol>
    </nav>

    <?php if (empty($notifications)): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center" data-aos="zoom-in">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-400">
                <i class="fa-regular fa-bell-slash text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">No Notifications</h3>
            <p class="text-gray-500">You are all caught up! Check back later.</p>
        </div>
    <?php else: ?>

        <div class="space-y-4">
            <?php foreach ($notifications as $index => $row): 
                $delay = $index * 50; // Faster stagger than announcements
            ?>
                <div class="group relative bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all duration-300 flex items-start gap-4"
                     data-aos="fade-up" data-aos-delay="<?= $delay ?>">
                    
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-fyu-accent flex items-center justify-center text-fyu-primary group-hover:bg-fyu-primary group-hover:text-white transition-colors duration-300">
                            <i class="fa-regular fa-bell"></i>
                        </div>
                    </div>

                    <div class="flex-grow">
                        <div class="flex justify-between items-start">
                            <h3 class="font-bold text-gray-800 mb-1 group-hover:text-fyu-primary transition-colors">
                                <?= htmlspecialchars($row['title']) ?>
                            </h3>
                            <span class="text-xs text-gray-400 whitespace-nowrap ml-2">
                                <?= time_elapsed_string($row['created_at']) ?>
                            </span>
                        </div>
                        
                        <p class="text-gray-600 text-sm leading-relaxed">
                            <?= nl2br(htmlspecialchars($row['message'])) ?>
                        </p>
                        
                        <div class="mt-2 text-xs text-gray-400">
                            <i class="fa-regular fa-clock mr-1"></i> 
                            <?= date("F j, Y, g:i a", strtotime($row['created_at'])) ?>
                        </div>
                    </div>

                    <div class="absolute left-0 top-4 bottom-4 w-1 rounded-r bg-fyu-primary opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-8 text-gray-400 text-xs uppercase tracking-widest">
            End of notifications
        </div>

    <?php endif; ?>

</section>

<?php
include_once __DIR__ . "/../app/views/layouts/footer.php";
?>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        once: true,
        duration: 800,
        offset: 30
    });
</script>