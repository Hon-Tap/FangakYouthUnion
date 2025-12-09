<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/db.php';

$baseUrl = "/FangakYouthUnion/public/";

$client = new Google\Client();
$client->setClientId('YOUR_CLIENT_ID');
$client->setClientSecret('YOUR_CLIENT_SECRET');
$client->setRedirectUri('http://localhost/FangakYouthUnion/public/google_callback.php');

$client->addScope('email');
$client->addScope('profile');

if (!isset($_GET['code'])) {
    $_SESSION['error'] = "Google login failed.";
    header("Location: {$baseUrl}login.php");
    exit();
}

// Get access token
$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

if (isset($token['error'])) {
    $_SESSION['error'] = "Google authentication error.";
    header("Location: {$baseUrl}login.php");
    exit();
}

$client->setAccessToken($token['access_token']);

// Get user profile
$oauth = new Google\Service\Oauth2($client);
$googleUser = $oauth->userinfo->get();

$email = $googleUser->email;
$fullName = $googleUser->name ?? $googleUser->givenName;

// --- Check if user exists ---
$stmt = $conn->prepare("SELECT * FROM members WHERE email=? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    // Insert new member
    $stmt = $conn->prepare("INSERT INTO members (full_name, email, status) VALUES (?, ?, 'active')");
    $stmt->bind_param("ss", $fullName, $email);
    $stmt->execute();

    $userId = $stmt->insert_id;
    $user = ['id' => $userId, 'full_name' => $fullName, 'email' => $email, 'status' => 'active'];
}

// --- Login user ---
session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];
$_SESSION['full_name'] = $user['full_name'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = 'member';

header("Location: {$baseUrl}member_dashboard.php");
exit();
