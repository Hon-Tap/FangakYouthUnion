<?php
include_once __DIR__ . '/../config/db.php';

// New post data
$title = "FYU Launches New Community Project";
$subheading = "Empowering youth across Fangak";
$content = "We are excited to announce the launch of our latest community initiative aimed at fostering innovation and youth empowerment in the region.";
$author = "HT Mathiang";
$created_at = date('Y-m-d H:i:s');

$stmt = $conn->prepare("INSERT INTO blog_posts (title, subheading, content, author, created_at) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $title, $subheading, $content, $author, $created_at);

if($stmt->execute()){
    echo "New post added successfully!";
} else {
    echo "Error adding post: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
