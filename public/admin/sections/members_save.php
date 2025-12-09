<?php
// admin/members_save.php
declare(strict_types=1);

include __DIR__ . "/../includes/admin_auth.php";
include_once __DIR__ . "/../../../app/config/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

$id         = $_POST['id'] ?? null;
$full_name  = trim($_POST['full_name']);
$gender     = $_POST['gender'];
$age        = $_POST['age'];
$email      = $_POST['email'];
$phone      = $_POST['phone'];
$payam      = $_POST['payam'];
$status     = $_POST['status'];

// Handle photo upload
$photo = null;

if (!empty($_FILES['photo']['name'])) {
    $filename = time() . "_" . basename($_FILES['photo']['name']);
    $filepath = __DIR__ . "/../../../uploads/" . $filename;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $filepath)) {
        $photo = $filename;
    }
}

try {
    if ($id) {
        // UPDATE
        $sql = "UPDATE members SET full_name=?, gender=?, age=?, email=?, phone=?, payam=?, status=?";
        $params = [$full_name, $gender, $age, $email, $phone, $payam, $status];

        if ($photo) {
            $sql .= ", photo=?";
            $params[] = $photo;
        }

        $sql .= " WHERE id=?";
        $params[] = $id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

    } else {
        // INSERT
        $stmt = $pdo->prepare("
            INSERT INTO members (full_name, gender, age, email, phone, payam, status, photo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $full_name, $gender, $age, $email, $phone, $payam, $status, $photo
        ]);
    }

    header("Location: members.php?success=1");
    exit;

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
