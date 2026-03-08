<?php

/**
 * Fangak Youth Union
 * Homepage
 */

$pageTitle = "Home - Fangak Youth Union";

require_once __DIR__ . "/../app/views/layouts/header.php";
require_once __DIR__ . "/../app/config/db.php";

$announcements = [];
$events = [];


/* =============================
   FETCH DATA
============================= */

try {

    if ($pdo instanceof PDO) {

        $stmt = $pdo->prepare("
            SELECT id,title,body,created_at
            FROM announcements
            WHERE is_published = 1
            ORDER BY created_at DESC
            LIMIT 3
        ");
        $stmt->execute();
        $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);


        $stmt = $pdo->prepare("
            SELECT id,title,description,image,location,event_date
            FROM events
            WHERE event_date >= CURDATE()
            ORDER BY event_date ASC
            LIMIT 3
        ");
        $stmt->execute();
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

} catch(Throwable $e){
    error_log("Homepage DB error: ".$e->getMessage());
}


/* =============================
   HELPERS
============================= */

function e($value){
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function eventDay($date){
    return date('d', strtotime($date));
}

function eventMonth($date){
    return date('M', strtotime($date));
}

?>

<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css"/>

<style>

.hero-overlay{
background:linear-gradient(to bottom,rgba(0,0,0,.45),rgba(20,82,51,.9));
}

.line-clamp-2{
display:-webkit-box;
-webkit-line-clamp:2;
-webkit-box-orient:vertical;
overflow:hidden;
}

</style>


<!-- HERO -->

<section class="relative h-[85vh] flex items-center justify-center overflow-hidden">

<img src="/images/FYU-LOGO.jpg"
class="absolute inset-0 w-full h-full object-cover">

<div class="hero-overlay absolute inset-0"></div>

<div class="relative z-10 text-center text-white px-6 max-w-3xl">

<span class="inline-block bg-white/20 px-4 py-1 rounded-full text-sm mb-4">
Unity • Innovation • Progress
</span>

<h1 class="text-5xl font-serif font-bold mb-6">
Welcome to <span class="text-fyu-light">Fangak Youth Union</span>
</h1>

<p class="text-lg text-gray-200 mb-10">
Empowering youth, building community, and creating a better future through sustainable projects and collective leadership.
</p>

<div class="flex gap-4 justify-center flex-wrap">

<a href="/joinus.php"
class="px-8 py-3 bg-fyu-primary rounded-full font-semibold hover:bg-fyu-light">

Start Your Impact

</a>

<a href="#announcements"
class="px-8 py-3 border border-white rounded-full hover:bg-white hover:text-fyu-dark">

Explore More

</a>

</div>

</div>

</section>


<!-- ANNOUNCEMENTS -->

<section id="announcements" class="py-20 bg-gray-50">

<div class="max-w-7xl mx-auto px-6">

<div class="flex justify-between items-center mb-10">

<h2 class="text-3xl font-serif font-bold">
Latest Announcements
</h2>

<a href="/announcements.php"
class="text-fyu-primary font-semibold">
View Archive →
</a>

</div>


<?php if(empty($announcements)): ?>

<div class="text-center py-16 text-gray-500">
No announcements available.
</div>

<?php else: ?>

<div class="grid md:grid-cols-3 gap-8">

<?php foreach($announcements as $ann): ?>

<article class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">

<h3 class="font-bold text-xl mb-2">

<?= e($ann['title']) ?>

</h3>

<p class="text-gray-600 text-sm mb-4 line-clamp-2">

<?= e(strip_tags($ann['body'])) ?>

</p>

<a href="/announcements.php?id=<?= (int)$ann['id'] ?>"
class="text-fyu-primary font-semibold">

Read More →

</a>

</article>

<?php endforeach; ?>

</div>

<?php endif; ?>

</div>

</section>



<!-- EVENTS -->

<section class="py-20 bg-white">

<div class="max-w-7xl mx-auto px-6">

<div class="flex justify-between items-center mb-10">

<h2 class="text-3xl font-serif font-bold">
Upcoming Events
</h2>

<a href="/events.php"
class="text-fyu-primary font-semibold">

Full Calendar →

</a>

</div>


<?php if(empty($events)): ?>

<div class="text-center text-gray-500 py-16">
No upcoming events.
</div>

<?php else: ?>

<div class="grid md:grid-cols-3 gap-8">

<?php foreach($events as $evt): ?>

<article class="rounded-xl overflow-hidden shadow hover:shadow-xl transition">

<div class="h-48 bg-gray-200 relative">

<?php if(!empty($evt['image'])): ?>

<img src="/images/events/<?= e($evt['image']) ?>"
class="w-full h-full object-cover">

<?php endif; ?>

<div class="absolute top-4 right-4 bg-white rounded p-2 text-center shadow">

<div class="font-bold text-lg">
<?= eventDay($evt['event_date']) ?>
</div>

<div class="text-xs text-gray-500">
<?= eventMonth($evt['event_date']) ?>
</div>

</div>

</div>

<div class="p-6">

<h3 class="text-xl font-bold mb-2">

<?= e($evt['title']) ?>

</h3>

<p class="text-sm text-gray-600 mb-4 line-clamp-2">

<?= e(strip_tags($evt['description'])) ?>

</p>

<a href="/events.php?id=<?= (int)$evt['id'] ?>"
class="text-fyu-primary font-semibold">

View Event →

</a>

</div>

</article>

<?php endforeach; ?>

</div>

<?php endif; ?>

</div>

</section>



<!-- PROJECTS -->

<?php

$projects = [

[
"title"=>"Youth Skills Development",
"desc"=>"Equipping youth with essential digital and vocational skills.",
"img"=>"project1.jpg"
],

[
"title"=>"Flood Response Initiative",
"desc"=>"Supporting families displaced by floods.",
"img"=>"FloTra.jpg"
],

[
"title"=>"Access to Education",
"desc"=>"Providing educational resources to rural communities.",
"img"=>"SACOM.jpg"
]

];

?>

<section class="py-20 bg-fyu-dark text-white">

<div class="max-w-7xl mx-auto px-6">

<h2 class="text-3xl font-serif font-bold mb-10">
Current Initiatives
</h2>

<div class="grid md:grid-cols-3 gap-8">

<?php foreach($projects as $proj): ?>

<div class="group">

<div class="aspect-video overflow-hidden rounded-lg mb-4">

<img src="/images/<?= e($proj['img']) ?>"
class="w-full h-full object-cover group-hover:scale-110 transition">

</div>

<h3 class="text-xl font-bold mb-2">
<?= e($proj['title']) ?>
</h3>

<p class="text-gray-300 text-sm">
<?= e($proj['desc']) ?>
</p>

</div>

<?php endforeach; ?>

</div>

</div>

</section>



<!-- CTA -->

<section class="py-24 bg-gradient-to-r from-fyu-primary to-fyu-light text-white text-center">

<div class="max-w-3xl mx-auto px-6">

<h2 class="text-4xl font-serif font-bold mb-6">
Be Part of the Change
</h2>

<p class="mb-10 text-lg">
Join us in empowering youth and transforming communities.
</p>

<a href="/register.php"
class="px-10 py-4 bg-white text-fyu-dark font-bold rounded-full">

Join the Movement

</a>

</div>

</section>


<?php require_once __DIR__ . "/../app/views/layouts/footer.php"; ?>


<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>

AOS.init({
once:true,
duration:700
});

</script>