<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$pageTitle = "Edit Profile - Fangak Youth Union";
include_once __DIR__ . "/../app/views/layouts/header.php";
include_once __DIR__ . "/../app/config/db.php";

$userId = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id='$userId' LIMIT 1";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $phone     = mysqli_real_escape_string($conn, $_POST['phone']);
    $age       = mysqli_real_escape_string($conn, $_POST['age']);

    $update_sql = "UPDATE users SET full_name='$full_name', email='$email', phone='$phone', age='$age' WHERE id='$userId'";

    if (mysqli_query($conn, $update_sql)) {
        $_SESSION['full_name'] = $full_name;
        $_SESSION['email']     = $email;
        $_SESSION['age']       = $age;
        header("Location: dashboard.php?update=success");
        exit();
    } else {
        $error = "Update failed: " . mysqli_error($conn);
    }
}
?>

<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #0b6026, #064418);
        margin: 0;
        padding: 0;
    }

    .profile-container {
        padding: 20px;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .profile-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        padding: 30px;
        max-width: 500px;
        width: 100%;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .profile-card h2 {
        color: #0b6026;
        font-size: 2rem;
        margin-bottom: 20px;
        text-align: center;
    }

    .profile-card form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .profile-card input,
    .profile-card select {
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 1rem;
    }

    .profile-card button {
        padding: 14px;
        border: none;
        border-radius: 8px;
        background: #0b6026;
        color: white;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .profile-card button:hover {
        background: #064418;
    }

    .error-message {
        color: red;
        text-align: center;
    }
</style>

<div class="profile-container">
    <div class="profile-card">
        <h2>Edit Profile</h2>

        <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>

        <form action="" method="POST">
            <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
            <select name="age" required>
                <?php for ($age = 18; $age <= 35; $age++) : ?>
                    <option value="<?= $age ?>" <?= ($user['age'] == $age) ? "selected" : "" ?>><?= $age ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit">Update Profile</button>
        </form>
    </div>
</div>

<?php
mysqli_close($conn);
include_once __DIR__ . "/../app/views/layouts/footer.php";
?>