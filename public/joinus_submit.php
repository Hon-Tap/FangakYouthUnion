<?php
// joinus_submit.php
header('Content-Type: application/json; charset=utf-8');

// adjust path if your db.php is elsewhere
include_once __DIR__ . "/../app/config/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// pull + trim values
$payam = isset($_POST['payam']) ? trim($_POST['payam']) : '';
$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// basic validation
if ($full_name === '' || $email === '' || $phone === '' || $message === '') {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please provide a valid email address.']);
    exit;
}

// check whether payam column exists (so script works whether you added it or not)
$hasPayam = false;
$check = $conn->query("SHOW COLUMNS FROM `join_requests` LIKE 'payam'");
if ($check && $check->num_rows > 0) {
    $hasPayam = true;
}

// prepare and execute insert
if ($hasPayam) {
    $stmt = $conn->prepare("INSERT INTO join_requests (payam, full_name, email, phone, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("sssss", $payam, $full_name, $email, $phone, $message);
} else {
    $stmt = $conn->prepare("INSERT INTO join_requests (full_name, email, phone, message, created_at) VALUES (?, ?, ?, ?, NOW())");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("ssss", $full_name, $email, $phone, $message);
}

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Thank you — registration saved.']);
} else {
    // don't expose raw SQL error to users in production; we return it here to help debug locally
    $err = $stmt->error ?: $conn->error;
    error_log("joinus_submit error: " . $err);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $err]);
}

$stmt->close();
$conn->close();
exit;
