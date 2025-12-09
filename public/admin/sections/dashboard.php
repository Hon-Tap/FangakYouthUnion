<?php

try {
    $stats = [
        'news'      => $pdo->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn(),
        'projects'  => $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
        'events'    => $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn(),
        'members'   => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    ];
} catch (PDOException $e) {
    die("Error loading dashboard stats: " . $e->getMessage());
}

?>


<div class="mb-6">
    <h1 class="text-3xl font-bold">Welcome, <?= explode(" ", $admin['name'])[0] ?> 👋</h1>
    <p class="text-slate-600">Quick overview of system activity.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
    <?php foreach ($stats as $label => $value): ?>
        <div class="bg-white p-6 rounded-xl shadow border">
            <p class="text-4xl font-bold"><?= $value ?></p>
            <p class="text-slate-500 uppercase text-xs"><?= ucfirst($label) ?></p>
        </div>
    <?php endforeach; ?>
</div>
