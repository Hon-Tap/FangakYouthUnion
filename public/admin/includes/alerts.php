<?php if (!empty($_GET['success'])): ?>
    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
        ✔ <?= htmlspecialchars($_GET['success']) ?>
    </div>
<?php endif; ?>

<?php if (!empty($_GET['error'])): ?>
    <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
        ❗ <?= htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif; ?>
