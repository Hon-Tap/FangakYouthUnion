<?php
session_start();
require_once __DIR__ . "/../../app/config/db.php";

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!$name || !$email || !$password) {
        $message = "All fields are required.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");

        if ($stmt->execute([$name, $email, $hash])) {
            $message = "Admin created successfully!";
        } else {
            $message = "Email already exists or an error occurred.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 h-screen flex items-center justify-center">

<div class="bg-white p-10 rounded-xl shadow-xl w-full max-w-md">

    <h1 class="text-xl font-bold mb-2">Register Admin</h1>

    <?php if ($message): ?>
        <p class="p-3 bg-blue-100 text-blue-700 rounded mb-4"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-3">
        <input name="name" type="text" placeholder="Full Name" class="w-full p-2 border rounded" required>
        <input name="email" type="email" placeholder="Email" class="w-full p-2 border rounded" required>
        <input name="password" type="password" placeholder="Password" class="w-full p-2 border rounded" required>

        <button class="w-full bg-green-700 text-white py-2 rounded">Register</button>
    </form>

</div>

</body>
</html>
