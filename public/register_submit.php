<?php
// register_submit.php — PDO version for FYU members table
declare(strict_types=1);
header('Content-Type: application/json');

require_once __DIR__ . "/../app/config/db.php"; // Must define $pdo

if (!isset($pdo)) {
    echo json_encode([
        "success" => false,
        "message" => "Server error: Database unavailable."
    ]);
    exit;
}

// Helper function for JSON response
function respond(bool $success, string $message, array $extra = []): void {
    echo json_encode(array_merge([
        "success" => $success,
        "message" => $message
    ], $extra));
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, "Invalid request method.");
}

// Fetch & sanitize data
$full_name       = trim($_POST['full_name'] ?? '');
$email           = trim($_POST['email'] ?? '');
$phone           = trim($_POST['phone'] ?? '');
$payam           = trim($_POST['payam'] ?? '');
$gender          = trim($_POST['gender'] ?? ''); // New Field
$age             = intval($_POST['age'] ?? 0);
$education_level = trim($_POST['education_level'] ?? '');
$course          = trim($_POST['course'] ?? '');
$year_or_done    = trim($_POST['year_or_done'] ?? '');

// Validate required fields
if (!$full_name || !$email || !$phone || !$payam || !$gender || !$age || !$education_level) {
    respond(false, "Please fill in all required fields.");
}

// Validate Gender specifically (Security check)
if (!in_array($gender, ['Male', 'Female'])) {
    respond(false, "Invalid gender selection.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, "Please enter a valid email.");
}

// Handle file upload
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    respond(false, "Photo upload failed or missing.");
}

$allowed_exts = ['jpg','jpeg','png','webp'];
$photo_ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
if (!in_array($photo_ext, $allowed_exts)) {
    respond(false, "Invalid photo format. Only JPG, PNG, or WEBP allowed.");
}

// Ensure upload directory exists
$upload_dir = __DIR__ . "/../public/uploads/members/";
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

$new_filename = "member_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $photo_ext;
$photo_path = $upload_dir . $new_filename;

if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
    respond(false, "Failed to save uploaded photo.");
}

$photo_rel_path = "members/" . $new_filename;

try {
    // Start transaction
    $pdo->beginTransaction();

    // Check duplicate email
    $stmt = $pdo->prepare("SELECT id FROM members WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        // Remove uploaded photo
        unlink($photo_path);
        respond(false, "Email is already registered.");
    }

    // Insert member (Added gender to query)
    $insert = $pdo->prepare("
        INSERT INTO members 
        (full_name, email, phone, gender, age, payam, education_level, course, year_or_done, photo, status, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())
    ");
    $success = $insert->execute([
        $full_name,
        $email,
        $phone,
        $gender, // New Field
        $age,
        $payam,
        $education_level,
        $course ?: null,
        $year_or_done ?: null,
        $photo_rel_path
    ]);

    if ($success) {
        $pdo->commit();
        respond(true, "Registration successful! Your application is pending review.");
    } else {
        $pdo->rollBack();
        unlink($photo_path);
        respond(false, "Unable to register member.");
    }

} catch (Exception $e) {
    $pdo->rollBack();
    if (isset($photo_path) && file_exists($photo_path)) unlink($photo_path);
    respond(false, "Server error: " . $e->getMessage());
}