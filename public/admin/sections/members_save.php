<?php
// admin/members_save.php
declare(strict_types=1);

include __DIR__ . "/../includes/admin_auth.php";
include_once __DIR__ . "/../../../app/config/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

$id        = $_POST['id'] ?? null;
$full_name = trim($_POST['full_name'] ?? '');
$gender    = trim($_POST['gender'] ?? '');
$age       = intval($_POST['age'] ?? 0);
$email     = trim($_POST['email'] ?? '');
$phone     = trim($_POST['phone'] ?? '');
$payam     = trim($_POST['payam'] ?? '');
$status    = trim($_POST['status'] ?? 'pending');

if (!$full_name || !$gender || !$age || !$email) {
    die("Missing required fields.");
}

$photo = null;
$allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
$upload_dir = __DIR__ . "/../../../public/uploads/members/";

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Handle Photo Upload
if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_exts)) {
        die("Invalid file type. Only JPG, PNG, WEBP allowed.");
    }

    $filename = "member_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
    $filepath = $upload_dir . $filename;

    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $filepath)) {
        die("Failed to upload photo.");
    }

    // Save as clean relative path to the public folder
    $photo = "uploads/members/" . $filename;
}

try {
    if ($id) {
        // Fetch old photo to delete later
        if ($photo) {
            $old = $pdo->prepare("SELECT photo FROM members WHERE id = ?");
            $old->execute([$id]);
            $old_photo = $old->fetchColumn();
        }

        $sql = "UPDATE members SET full_name = ?, gender = ?, age = ?, email = ?, phone = ?, payam = ?, status = ?";
        $params = [$full_name, $gender, $age, $email, $phone, $payam, $status];

        if ($photo) {
            $sql .= ", photo = ?";
            $params[] = $photo;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Delete old photo if replaced
        if ($photo && !empty($old_photo)) {
            $old_path_clean = str_replace('public/', '', $old_photo);
            $old_file = __DIR__ . "/../../../public/" . ltrim($old_path_clean, '/');
            if (file_exists($old_file) && is_file($old_file)) {
                unlink($old_file);
            }
        }
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO members (full_name, gender, age, email, phone, payam, status, photo, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$full_name, $gender, $age, $email, $phone, $payam, $status, $photo]);
    }

    header("Location: members.php?success=1");
    exit;

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}