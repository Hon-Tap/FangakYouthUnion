<?php
// register_submit.php — Production-grade version

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . "/../app/config/db.php";

if (!isset($pdo)) {
    respond(false, "Server error: Database unavailable.");
}

/**
 * -------------------------------------------------------
 * Helper: JSON response
 * -------------------------------------------------------
 */
function respond(bool $success, string $message, array $extra = []): void {
    echo json_encode(array_merge([
        "success" => $success,
        "message" => $message
    ], $extra));
    exit;
}

/**
 * -------------------------------------------------------
 * Ensure POST request
 * -------------------------------------------------------
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, "Invalid request method.");
}

/**
 * -------------------------------------------------------
 * Sanitize input
 * -------------------------------------------------------
 */
$full_name       = trim($_POST['full_name'] ?? '');
$email           = trim($_POST['email'] ?? '');
$phone           = trim($_POST['phone'] ?? '');
$payam           = trim($_POST['payam'] ?? '');
$gender          = trim($_POST['gender'] ?? '');
$age             = intval($_POST['age'] ?? 0);
$education_level = trim($_POST['education_level'] ?? '');
$course          = trim($_POST['course'] ?? '');
$year_or_done    = trim($_POST['year_or_done'] ?? '');

/**
 * -------------------------------------------------------
 * Validate required fields
 * -------------------------------------------------------
 */
if (!$full_name || !$email || !$phone || !$payam || !$gender || !$age || !$education_level) {
    respond(false, "Please fill in all required fields.");
}

if (!in_array($gender, ['Male', 'Female'])) {
    respond(false, "Invalid gender selection.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, "Please enter a valid email.");
}

/**
 * -------------------------------------------------------
 * Validate photo upload
 * -------------------------------------------------------
 */
if (!isset($_FILES['photo'])) {
    respond(false, "Photo upload missing.");
}

if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {

    error_log("UPLOAD ERROR CODE: " . $_FILES['photo']['error']);

    respond(false, "Photo upload failed.");
}

$allowed_exts = ['jpg','jpeg','png','webp'];

$photo_ext = strtolower(
    pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION)
);

if (!in_array($photo_ext, $allowed_exts)) {
    respond(false, "Invalid photo format. Only JPG, PNG, WEBP allowed.");
}

/**
 * -------------------------------------------------------
 * Define upload directory
 * -------------------------------------------------------
 */
$upload_dir = __DIR__ . "/../public/uploads/members/";

if (!is_dir($upload_dir)) {

    if (!mkdir($upload_dir, 0755, true)) {

        error_log("FAILED TO CREATE DIRECTORY: " . $upload_dir);

        respond(false, "Upload directory creation failed.");
    }
}

/**
 * -------------------------------------------------------
 * Ensure directory writable
 * -------------------------------------------------------
 */
if (!is_writable($upload_dir)) {

    error_log("DIRECTORY NOT WRITABLE: " . $upload_dir);

    respond(false, "Upload directory not writable.");
}

/**
 * -------------------------------------------------------
 * Generate safe filename
 * -------------------------------------------------------
 */
$new_filename =
    "member_" .
    time() .
    "_" .
    bin2hex(random_bytes(4)) .
    "." .
    $photo_ext;

$photo_path = $upload_dir . $new_filename;

/**
 * -------------------------------------------------------
 * Move uploaded file
 * -------------------------------------------------------
 */
if (!move_uploaded_file(
    $_FILES['photo']['tmp_name'],
    $photo_path
)) {

    error_log("MOVE FAILED:");
    error_log("TMP: " . $_FILES['photo']['tmp_name']);
    error_log("DEST: " . $photo_path);

    respond(false, "Failed to save uploaded photo.");
}

/**
 * -------------------------------------------------------
 * Store relative path
 * -------------------------------------------------------
 */
$photo_rel_path = "members/" . $new_filename;

/**
 * -------------------------------------------------------
 * Database transaction
 * -------------------------------------------------------
 */
try {

    $pdo->beginTransaction();

    /**
     * Check duplicate email
     */
    $stmt = $pdo->prepare(
        "SELECT id FROM members WHERE email = ? LIMIT 1"
    );

    $stmt->execute([$email]);

    if ($stmt->fetch()) {

        if (file_exists($photo_path)) {
            unlink($photo_path);
        }

        respond(false, "Email is already registered.");
    }

    /**
     * Insert member
     */
    $insert = $pdo->prepare("
        INSERT INTO members (
            full_name,
            email,
            phone,
            gender,
            age,
            payam,
            education_level,
            course,
            year_or_done,
            photo,
            status,
            created_at,
            updated_at
        )
        VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
            'pending',
            NOW(),
            NOW()
        )
    ");

    $success = $insert->execute([
        $full_name,
        $email,
        $phone,
        $gender,
        $age,
        $payam,
        $education_level,
        $course ?: null,
        $year_or_done ?: null,
        $photo_rel_path
    ]);

    if (!$success) {

        $pdo->rollBack();

        if (file_exists($photo_path)) {
            unlink($photo_path);
        }

        respond(false, "Unable to register member.");
    }

    $pdo->commit();

    respond(true, "Registration successful! Your application is pending review.");

} catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    if (isset($photo_path) && file_exists($photo_path)) {
        unlink($photo_path);
    }

    error_log("REGISTER ERROR: " . $e->getMessage());

    respond(false, "Server error occurred.");
}