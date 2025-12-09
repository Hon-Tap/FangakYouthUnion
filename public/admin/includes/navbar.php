<header class="h-16 bg-white border-b flex items-center px-6 justify-between">
    <h2 class="text-xl font-semibold"><?= $tabs[$active] ?></h2>

    <?php if (in_array($active, ["news", "projects", "events", "announcements"])): ?>
        <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            New <?= rtrim($tabs[$active], "s") ?>
        </button>
    <?php endif; ?>
</header>
