<?php
// subscribe.php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

include_once __DIR__ . "/../app/config/db.php"; // adjust path if needed

// Read body (accept JSON or form)
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    // fallback to POST form
    $data = $_POST;
}

$email = trim($data['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
    exit;
}

try {
    // Prepared statement (INSERT IGNORE style)
    $stmt = mysqli_prepare($conn, "INSERT INTO subscribers (email) VALUES (?)");
    if (!$stmt) {
        throw new Exception('Database prepare failed.');
    }
    mysqli_stmt_bind_param($stmt, 's', $email);
    $ok = mysqli_stmt_execute($stmt);
    if (!$ok) {
        $errno = mysqli_errno($conn);
        // duplicate entry
        if ($errno === 1062) {
            echo json_encode(['success' => true, 'message' => 'You are already subscribed.']);
            exit;
        } else {
            throw new Exception('Database execute error.');
        }
    }
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => true, 'message' => 'Thanks — you are subscribed.']);
} catch (Exception $e) {
    // In prod, log error message to a file instead of exposing it
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Subscription failed. Try again later.']);
}
