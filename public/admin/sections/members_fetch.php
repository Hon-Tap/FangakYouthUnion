<?php
// admin/sections/members_fetch.php
include_once __DIR__ . "/../../../app/config/db.php";

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode($member);
}