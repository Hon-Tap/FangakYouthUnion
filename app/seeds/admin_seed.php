<?php
include_once __DIR__ . '/../config/db.php';

// Admin credentials
$name = "HT Mathiang";
$email = "hontap.cs@gmail.com";
$password = password_hash("Nyatieh2470", PASSWORD_BCRYPT);
$role = "admin";

// Check if the admin already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result && $result->num_rows > 0){
    echo "Admin user already exists!";
} else {
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);
    if($stmt->execute()){
        echo "Admin user created successfully!";
    } else {
        echo "Error creating admin user: " . $conn->error;
    }
}
?>
