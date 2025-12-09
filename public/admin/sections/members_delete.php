<?php
// admin/members_delete.php
declare(strict_types=1);

include __DIR__ . "/../includes/admin_auth.php";
include_once __DIR__ . "/../../../app/config/db.php";

if (empty($_GET['id'])) {
    die("Missing ID.");
}

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM members WHERE id = ?");
$stmt->execute([$id]);

header("Location: members.php?deleted=1");
exit;
