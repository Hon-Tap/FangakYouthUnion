<?php
// admin/members_save.php
declare(strict_types=1);

// 1. Security & Requirements
include __DIR__ . "/../includes/admin_auth.php";
include_once __DIR__ . "/../../../app/config/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Direct access is not allowed.");
}

// 2. Capture and Sanitize Inputs
$id         = !empty($_POST['id']) ? (int)$_POST['id'] : null;
$full_name  = trim($_POST['full_name'] ?? '');
$gender     = trim($_POST['gender'] ?? '');
$age        = (int)($_POST['age'] ?? 0);
$email      = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone      = trim($_POST['phone'] ?? '');
$payam      = trim($_POST['payam'] ?? '');
$status     = trim($_POST['status'] ?? 'pending');

// Basic Validation
if (!$full_name || !$gender || !$age || !$email) {
    // Redirect back with error if fields are missing
    header("Location: ../index.php?section=members&error=missing_fields");
    exit;
}

// 3. Photo Upload Logic
$photo = null;
$allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
$upload_dir = __DIR__ . "/../../../public/uploads/members/";

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_exts)) {
        die("Invalid file type. Only JPG, PNG, and WEBP are allowed.");
    }

    $filename = "member_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
    $filepath = $upload_dir . $filename;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $filepath)) {
        // Save relative path for database
        $photo = "uploads/members/" . $filename;
    }
}

try {
    if ($id) {
        // --- UPDATE EXISTING MEMBER ---
        
        // Handle old photo cleanup if a new one is uploaded
        if ($photo) {
            $stmt_old = $pdo->prepare("SELECT photo FROM members WHERE id = ?");
            $stmt_old->execute([$id]);
            $old_photo_path = $stmt_old->fetchColumn();

            if ($old_photo_path) {
                $absolute_old_path = __DIR__ . "/../../../public/" . ltrim($old_photo_path, '/');
                if (file_exists($absolute_old_path) && is_file($absolute_old_path)) {
                    unlink($absolute_old_path);
                }
            }
        }

        $sql = "UPDATE members SET full_name = ?, gender = ?, age = ?, email = ?, phone = ?, payam = ?, status = ?, updated_at = NOW()";
        $params = [$full_name, $gender, $age, $email, $phone, $payam, $status];

        if ($photo) {
            $sql .= ", photo = ?";
            $params[] = $photo;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $success_type = "edited";
    } else {
        // --- INSERT NEW MEMBER ---
        $stmt = $pdo->prepare("
            INSERT INTO members (full_name, gender, age, email, phone, payam, status, photo, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$full_name, $gender, $age, $email, $phone, $payam, $status, $photo]);
        
        $success_type = "added";
    }

    // 4. Redirect back to the dashboard with the specific success state
    // Note: Adjust the URL below to match your actual dashboard routing (e.g., index.php?page=members)
    header("Location: ../index.php?section=members&success=" . $success_type);
    exit;

} catch (PDOException $e) {
    // Log error and redirect with error message
    error_log($e->getMessage());
    header("Location: ../index.php?section=members&error=db_failure");
    exit;
}