<?php
session_start();
$message = $_SESSION['info'] ?? "Your account is pending approval.";
unset($_SESSION['info']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Account Pending Approval</title>
<style>
    body {
        margin: 0;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background: radial-gradient(circle at center, #9bf194ff, #c0fab1ff, #a1ee8eff);
        overflow: hidden;
        font-family: "Poppins", sans-serif;
    }

    .popup {
        position: relative;
        text-align: center;
        background: white;
        padding: 40px 50px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        animation: fadeInUp 0.8s ease;
        z-index: 10;
    }

    .popup h2 {
        margin-bottom: 10px;
        color: #33d649ff;
        font-size: 1.5rem;
        animation: textFade 1.5s ease;
    }

    .popup p {
        color: #555;
        font-size: 1rem;
    }

    @keyframes fadeInUp {
        from { transform: translateY(40px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    @keyframes textFade {
        0% { opacity: 0; transform: scale(0.9); }
        100% { opacity: 1; transform: scale(1); }
    }

    .particle {
        position: absolute;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        opacity: 0;
        animation: burst 2s forwards;
    }

    @keyframes burst {
        0% {
            transform: translate(0, 0) scale(1);
            opacity: 1;
        }
        100% {
            transform: translate(var(--x), var(--y)) scale(0.2);
            opacity: 0;
        }
    }
</style>
</head>
<body>
    <div class="popup">
        <h2><?= htmlspecialchars($message) ?></h2>
        <p>Please wait for admin approval or contact support if needed.</p>
    </div>

    <noscript>
        <p style="text-align:center;">Redirecting back to login...</p>
        <meta http-equiv="refresh" content="3;url=login.php">
    </noscript>

    <script>
        // Create flower-like color burst
        const colors = ['#076129ff', '#65ee70ff', '#c9ffe6ff', '#6cf394ff', '#c4dbb4ff', '#45ffc7ff', '#cfffa2ff'];
        const popup = document.querySelector('.popup');

        for (let i = 0; i < 20; i++) {
            const p = document.createElement('div');
            p.classList.add('particle');
            p.style.background = colors[Math.floor(Math.random() * colors.length)];
            const angle = Math.random() * 2 * Math.PI;
            const radius = 100 + Math.random() * 80;
            p.style.setProperty('--x', `${Math.cos(angle) * radius}px`);
            p.style.setProperty('--y', `${Math.sin(angle) * radius}px`);
            p.style.left = "50%";
            p.style.top = "50%";
            popup.appendChild(p);
        }

        // Redirect automatically after 3 seconds
        setTimeout(() => {
            window.location.href = "login.php";
        }, 5000);
    </script>
</body>
</html>
