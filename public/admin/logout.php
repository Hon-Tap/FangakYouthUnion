<?php
// admin/logout.php
include __DIR__ . "/includes/head.php";
?>

<div class="flex justify-center items-center min-h-screen bg-gray-100">

    <div class="bg-white p-8 rounded-xl shadow-lg max-w-sm text-center border">

        <h2 class="text-xl font-bold text-gray-800 mb-3">
            Are you sure you want to logout?
        </h2>

        <p class="text-gray-500 mb-6">
            You will need to log in again to access the dashboard.
        </p>

        <div class="flex justify-center gap-4">
            <a href="logout_confirm.php"
               class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                Yes, Logout
            </a>

            <a href="dashboard.php"
               class="bg-gray-300 px-4 py-2 rounded-lg hover:bg-gray-400">
                Cancel
            </a>
        </div>

    </div>

</div>
