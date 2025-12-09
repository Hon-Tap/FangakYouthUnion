<?php
// ajax_handler.php
// Handles all dynamic requests: fetching/submitting comments and subscriptions.

declare(strict_types=1);

// Ensure only POST requests are processed to prevent direct browsing
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// 1. Configuration & Database Connection
// Assuming the correct path to your DB configuration
include_once __DIR__ . "/../config/db.php"; 

// Set header for JSON response
header('Content-Type: application/json');

// Get the action from the request body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$action = $data['action'] ?? null;
$response = ['success' => false, 'message' => 'Invalid action specified.'];

// Basic validation for post ID
$postId = (int)($data['post_id'] ?? 0);

try {
    // =====================================
    // A. FETCH COMMENTS
    // =====================================
    if ($action === 'fetch_comments' && $postId > 0) {
        $stmt = $conn->prepare("SELECT user_name, comment_body, created_at FROM blog_comments WHERE post_id = ? AND is_approved = 1 ORDER BY created_at DESC");
        
        if (!$stmt) {
            throw new Exception("Database error on preparing fetch: " . $conn->error);
        }

        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $comments = [];
        while ($row = $result->fetch_assoc()) {
            // Format time difference for user-friendliness
            $now = new DateTime();
            $posted = new DateTime($row['created_at']);
            $interval = $now->diff($posted);

            if ($interval->y > 0) {
                $time_ago = $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
            } elseif ($interval->m > 0) {
                $time_ago = $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
            } elseif ($interval->d > 0) {
                $time_ago = $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
            } elseif ($interval->h > 0) {
                $time_ago = $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
            } elseif ($interval->i > 0) {
                $time_ago = $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
            } else {
                $time_ago = 'just now';
            }

            $comments[] = [
                'name' => htmlspecialchars($row['user_name']),
                'body' => htmlspecialchars($row['comment_body']),
                'time_ago' => $time_ago
            ];
        }

        $response = ['success' => true, 'comments' => $comments, 'count' => count($comments)];

    // =====================================
    // B. SUBMIT COMMENT
    // =====================================
    } elseif ($action === 'submit_comment' && $postId > 0) {
        $name = trim($data['user_name'] ?? '');
        $email = filter_var(trim($data['user_email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $body = trim($data['comment_body'] ?? '');
        
        if (empty($name) || empty($email) || empty($body) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please ensure all fields are filled and the email is valid.");
        }
        
        // is_approved = 0 means it requires manual moderation before display
        $isApproved = 0; 
        
        $stmt = $conn->prepare("INSERT INTO blog_comments (post_id, user_name, user_email, comment_body, is_approved) VALUES (?, ?, ?, ?, ?)");
        
        if (!$stmt) {
            throw new Exception("Database error on preparing submission: " . $conn->error);
        }

        $stmt->bind_param("isssi", $postId, $name, $email, $body, $isApproved);
        
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Comment submitted! It will appear after moderation. Thank you for your input.'];
        } else {
            throw new Exception("Failed to save comment: " . $stmt->error);
        }

    // =====================================
    // C. SUBSCRIBE TO NEWSLETTER
    // =====================================
    } elseif ($action === 'subscribe') {
        $email = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email address.");
        }

        // Check if already subscribed (assuming a 'subscribers' table)
        $stmt = $conn->prepare("SELECT COUNT(*) FROM subscribers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $response = ['success' => true, 'message' => 'This email is already subscribed.'];
        } else {
            // Insert new subscriber
            $stmt = $conn->prepare("INSERT INTO subscribers (email) VALUES (?)");
            if (!$stmt) {
                throw new Exception("Database error on preparing subscription: " . $conn->error);
            }
            $stmt->bind_param("s", $email);
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Subscription successful! Thank you.'];
            } else {
                throw new Exception("Failed to save subscription: " . $stmt->error);
            }
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    $response = ['success' => false, 'message' => $e->getMessage()];
} finally {
    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
        $stmt->close();
    }
    @mysqli_close($conn);
    echo json_encode($response);
}