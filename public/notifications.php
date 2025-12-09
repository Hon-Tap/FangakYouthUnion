<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$pageTitle = "Notifications - Fangak Youth Union";
include_once __DIR__ . "/../app/views/layouts/header.php";
include_once __DIR__ . "/../app/config/db.php";
?>

<style>
    .notifications-container {
        padding: 20px;
        max-width: 900px;
        margin: auto;
    }

    .notification-card {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .notification-card h3 {
        color: #0b6026;
        margin-bottom: 8px;
    }

    .notification-card p {
        font-size: 1rem;
        color: #333;
    }

    .notification-date {
        font-size: 0.85rem;
        color: gray;
    }
</style>

<div class="notifications-container">
    <h2>Notifications</h2>
    <?php
    $sql = "SELECT * FROM notifications ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='notification-card'>";
            echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
            echo "<p>" . htmlspecialchars($row['message']) . "</p>";
            echo "<p class='notification-date'>" . date("F j, Y, g:i a", strtotime($row['created_at'])) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No notifications available.</p>";
    }

    mysqli_close($conn);
    ?>
</div>

<?php
include_once __DIR__ . "/../app/views/layouts/footer.php";
?>