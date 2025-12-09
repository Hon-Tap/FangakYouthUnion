<?php
require __DIR__ . "/includes/auth.php";
require __DIR__ . "/includes/head.php";
require_once __DIR__ . "/../../app/config/db.php";
?>

<div class="p-6 max-w-xl mx-auto">

    <?php include __DIR__ . "/includes/alerts.php"; ?>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-green-700">Change Password</h1>
        <a href="dashboard.php" 
           class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-3 py-1 rounded-lg">
            Exit
        </a>
    </div>

    <form action="settings_password_save.php" method="POST"
          class="bg-white p-6 rounded-lg shadow space-y-4">

        <div>
            <label class="block font-semibold">Current Password</label>
            <input type="password" name="current_password" required
                   class="p-2 border rounded w-full">
        </div>

        <div>
            <label class="block font-semibold">New Password</label>
            <input type="password" name="new_password" required
                   class="p-2 border rounded w-full">
        </div>

        <div>
            <label class="block font-semibold">Confirm New Password</label>
            <input type="password" name="confirm_password" required
                   class="p-2 border rounded w-full">
        </div>

        <div class="flex justify-between items-center">
            <button class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800">
                Update Password
            </button>
            <a href="dashboard.php" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
                Cancel
            </a>
        </div>

    </form>
</div>

<?php include __DIR__ . "/includes/footer.php"; ?>
