<?php

session_start();

// -------------------------------
// Load DB (PDO)
// -------------------------------
require_once __DIR__ . '/../../app/config/db.php';

// -------------------------------
// Initialize
// -------------------------------
$error = "";
$pageTitle = "Admin Login";

// -------------------------------
// Handle login form
// -------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === "" || $password === "") {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (!$pdo) {
        $error = "Database connection unavailable. Please try again later.";
        error_log("Admin login: PDO connection unavailable.");
    } else {
        try {
            $stmt = $pdo->prepare("
                SELECT id, name, email, password
                FROM admins
                WHERE email = :email
                LIMIT 1
            ");
            $stmt->execute(['email' => $email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'] ?? '';
                $_SESSION['admin_email'] = $admin['email'] ?? '';

                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } catch (Throwable $e) {
            error_log("Admin login error: " . $e->getMessage());
            $error = "Server error. Please try again later.";
        }
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: { fontFamily: { sans: ["Inter", "sans-serif"] } }
            }
        }
    </script>
</head>
<body class="bg-slate-100 h-screen flex items-center justify-center">

<div class="bg-white p-10 rounded-xl shadow-xl w-full max-w-md border">
    <h1 class="text-2xl font-bold mb-1">FYU Admin Login</h1>
    <p class="text-slate-500 mb-6 text-sm">Enter your admin credentials to continue.</p>

    <?php if ($error): ?>
        <p class="mb-4 p-3 text-sm text-red-600 bg-red-100 rounded-md">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-sm mb-1 font-medium text-slate-600">Email</label>
            <input name="email" type="email" required
                   class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200"/>
        </div>

        <div>
            <label class="block text-sm mb-1 font-medium text-slate-600">Password</label>
            <input name="password" type="password" required
                   class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200"/>
        </div>

        <button class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
            Login
        </button>
    </form>
</div>

</body>
</html>
