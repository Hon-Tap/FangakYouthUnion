<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

// Google OAuth configuration
$client = new Google\Client();
$client->setClientId('YOUR_CLIENT_ID');
$client->setClientSecret('YOUR_CLIENT_SECRET');
$client->setRedirectUri('http://localhost/FangakYouthUnion/public/google_callback.php');
$client->addScope('email');
$client->addScope('profile');

// Generate Google login URL
$authUrl = $client->createAuthUrl();

// Redirect to Google
header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
exit();
