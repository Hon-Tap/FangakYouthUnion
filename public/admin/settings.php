<?php
require __DIR__ . "/includes/auth.php";
require __DIR__ . "/includes/head.php";
require_once __DIR__ . "/../../app/config/db.php";

// Load settings row (always expect one)
$stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC) ?: [
    "site_name" => "",
    "contact_email" => "",
    "logo" => ""
];
?>

<div class="p-6">

    <?php include __DIR__ . "/includes/alerts.php"; ?>

    <h1 class="text-2xl font-bold text-green-700 mb-4">System Settings</h1>

    <form action="settings_save.php" method="POST" enctype="multipart/form-data"
          class="bg-white p-6 rounded-lg shadow max-w-3xl space-y-4">

        <div>
            <label class="block font-semibold">Website Name</label>
            <input type="text" name="site_name" value="<?= htmlspecialchars($settings['site_name']) ?>"
                class="p-2 border rounded w-full">
        </div>

        <div>
            <label class="block font-semibold">Contact Email</label>
            <input type="email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email']) ?>"
                class="p-2 border rounded w-full">
        </div>

        <div>
            <label class="block font-semibold">Logo</label>
            <input type="file" name="logo" class="p-2 border rounded w-full">
            <?php if (!empty($settings['logo'])): ?>
                <img src="/FangakYouthUnion/uploads/<?= htmlspecialchars($settings['logo']) ?>" 
                     class="h-20 mt-2">
            <?php endif; ?>
        </div>

        <button class="bg-green-700 text-white px-4 py-2 rounded">Save Settings</button>
    </form>

    <hr class="my-8">

    <a href="settings_password.php" class="text-blue-600 underline">
        Change Admin Password
    </a>
</div>

<?php include __DIR__ . "/includes/footer.php"; ?>
