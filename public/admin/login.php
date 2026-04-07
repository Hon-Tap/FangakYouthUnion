<?php
/**
 * FYU Admin Login - Modern Editorial Style
 * Enhanced with Fail-Safe Diagnostics
 */
declare(strict_types=1);

// Initialize session before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Load Database
require_once __DIR__ . '/../../app/config/db.php';

$error = "";
$debug_info = ""; // Secret debug info for you
$pageTitle = "Admin Portal | Fangak Youth Union";

// 2. Handle Login Logic

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = "Please provide both email and password.";
        return;
    }

    if (!$pdo) {
        $error = "Critical: Database connection is offline.";
        return;
    }

    try {

        $stmt = $pdo->prepare("
            SELECT id, name, email, password
            FROM admins
            WHERE email = :email
            LIMIT 1
        ");

        $stmt->execute([
            'email' => $email
        ]);

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {

            error_log("LOGIN FAIL: No user found with email: $email");
            $error = "Access denied. Please check your credentials.";
            return;

        }

        $storedHash = $admin['password'];

        $loginSuccess = false;

        /*
        =========================================
        PRIMARY: password_hash() verification
        =========================================
        */

        if (password_verify($password, $storedHash)) {

            $loginSuccess = true;

            /*
            Upgrade hash if algorithm changed
            */

            if (password_needs_rehash(
                $storedHash,
                PASSWORD_DEFAULT
            )) {

                $newHash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $update = $pdo->prepare("
                    UPDATE admins
                    SET password = :password
                    WHERE id = :id
                ");

                $update->execute([
                    'password' => $newHash,
                    'id'       => $admin['id']
                ]);

                error_log("Password rehashed for user: $email");

            }

        }

        /*
        =========================================
        LEGACY SUPPORT (crypt-style hash)
        =========================================
        */

        elseif (hash_equals(
            crypt($password, $storedHash),
            $storedHash
        )) {

            $loginSuccess = true;

            /*
            MIGRATE to modern hash
            */

            $newHash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $update = $pdo->prepare("
                UPDATE admins
                SET password = :password
                WHERE id = :id
            ");

            $update->execute([
                'password' => $newHash,
                'id'       => $admin['id']
            ]);

            error_log("Legacy hash migrated for user: $email");

        }

        /*
        =========================================
        FINAL RESULT
        =========================================
        */

        if ($loginSuccess) {

            session_regenerate_id(true);

            $_SESSION['admin_id']    = $admin['id'];
            $_SESSION['admin_name']  = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['last_login']  = time();

            header("Location: dashboard.php");
            exit();

        }

        error_log("LOGIN FAIL: Password mismatch for user: $email");

        $error = "Access denied. Please check your credentials.";

    }

    catch (Throwable $e) {

        error_log("SYSTEM ERROR: " . $e->getMessage());

        $error = "A temporary system error occurred.";

    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1 { font-family: 'Playfair Display', serif; }
        .news-border { border-top: 4px solid #0b6026; } /* Matching FYU Green */
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-block bg-white p-3 rounded-full shadow-sm mb-4">
                 <svg class="w-8 h-8 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 0014.243 3.125m-3.21 1.258l-.16.033M15.474 19.33L16.5 21m-1.5-6.5l.5-.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1 class="text-3xl text-slate-900">Admin Portal</h1>
            <p class="text-slate-500 mt-2">Fangak Youth Union Management</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 overflow-hidden news-border border-x border-b">
            <div class="p-8">
                
                <?php if ($error): ?>
                    <div class="mb-6 flex items-center gap-3 p-4 text-sm text-red-700 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="font-semibold">Authentication Error</p>
                            <p class="opacity-90"><?= htmlspecialchars($error) ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($debug_info): ?>
                    <div class="mb-6 p-3 bg-amber-50 text-amber-800 text-xs border border-amber-200 rounded">
                        <strong>Developer Trace:</strong> <?= $debug_info ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                            Administrative Email
                        </label>
                        <input
                            name="email"
                            type="email"
                            autocomplete="email"
                            required
                            placeholder="e.g. admin@fyu.org"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-600 transition-all outline-none text-slate-700"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                            Secure Password
                        </label>
                        <input
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                            placeholder="••••••••"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-600 transition-all outline-none text-slate-700"
                        />
                    </div>

                    <button type="submit" class="w-full py-4 bg-green-700 hover:bg-green-800 text-white rounded-xl font-bold transition-all transform active:scale-[0.98] shadow-lg shadow-green-900/20 mt-2">
                        Sign In to Dashboard
                    </button>
                </form>
            </div>

            <div class="bg-slate-50 p-4 border-t border-slate-100 text-center">
                <a href="../" class="text-sm font-medium text-slate-500 hover:text-green-700 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Return to Public Website
                </a>
            </div>
        </div>

        <p class="text-center text-slate-400 text-xs mt-8">
            &copy; <?= date('Y') ?> Fangak Youth Union. Protected Environment.
        </p>
    </div>

</body>
</html>