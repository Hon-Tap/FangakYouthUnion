<div class="fixed inset-y-0 left-0 w-64 bg-green-900 text-white p-5 hidden lg:flex flex-col">

    <h1 class="text-2xl font-bold mb-8">FYU Admin</h1>

    <nav class="space-y-1 flex-1">
        <?php foreach ($tabs as $key => $label): ?>
            <a href="?tab=<?= $key ?>"
               class="block px-3 py-2 rounded-lg
               <?= $active === $key ? 'bg-green-600' : 'hover:bg-green-700' ?>">
               <?= $label ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="border-t border-slate-700 pt-4">
        <p class="font-semibold"><?= $admin['name'] ?></p>
        <p class="text-sm text-slate-400"><?= $admin['email'] ?></p>
        <a href="logout.php" class="block mt-3 text-red-400 hover:text-red-300">Logout</a>
    </div>
</div>
