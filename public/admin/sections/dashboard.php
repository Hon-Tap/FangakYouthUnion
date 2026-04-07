<?php

$stats = [
    'news'     => 0,
    'projects' => 0,
    'events'   => 0,
    'members'  => 0,
];

$queries = [
    'news'     => "SELECT COUNT(*) FROM blog_posts",
    'projects' => "SELECT COUNT(*) FROM projects",
    'events'   => "SELECT COUNT(*) FROM events",
    'members'  => "SELECT COUNT(*) FROM users",
];

foreach ($queries as $key => $sql) {

    try {

        $stmt = $pdo->query($sql);

        if ($stmt) {
            $stats[$key] = (int) $stmt->fetchColumn();
        }

    } catch (Throwable $e) {

        // Log error but DO NOT crash page
        error_log("Dashboard stat error: " . $e->getMessage());

        $stats[$key] = 0;

    }

}

?>