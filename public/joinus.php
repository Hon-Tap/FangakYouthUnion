<?php
$pageTitle = "Join Us - Fangak Youth Union";
include_once __DIR__ . "/../app/views/layouts/header.php";
?>

<style>
    /* ===== GENERAL STYLES ===== */
    body {
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #0b6026, #064418);
        overflow-x: hidden;
        margin: 0;
        padding: 0;
    }

    /* ===== JOIN US SECTION ===== */
    .joinus-container {
        position: relative;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 60px 20px;
    }

    .joinus-card {
        position: relative;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        padding: 40px;
        max-width: 500px;
        width: 100%;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        text-align: center;
        animation: fadeIn 1.2s ease-in-out;
        overflow: hidden;
        z-index: 2;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(40px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ===== INNER BUBBLES CONTAINER ===== */
    .form-bubbles {
        position: absolute;
        inset: 0;
        z-index: 1;
        overflow: hidden;
        pointer-events: none;
        border-radius: 20px;
    }

    .form-bubble {
        position: absolute;
        border-radius: 50%;
        opacity: 0.5;
        animation: float 15s infinite ease-in-out;
    }

    @keyframes float {
        0% {
            transform: translateY(0) scale(1);
            opacity: 0.5;
        }

        50% {
            transform: translateY(-150px) scale(1.3);
            opacity: 1;
        }

        100% {
            transform: translateY(0) scale(1);
            opacity: 0.5;
        }
    }

    /* ===== CARD CONTENT ===== */
    .joinus-card h2 {
        font-size: 2rem;
        font-weight: 800;
        color: #0b6026;
        margin-bottom: 10px;
        position: relative;
        z-index: 2;
    }

    .joinus-card p {
        font-size: 1rem;
        color: #555;
        margin-bottom: 25px;
        position: relative;
        z-index: 2;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 15px;
        position: relative;
        z-index: 2;
        transition: opacity 0.4s ease;
    }

    form.dimmed input,
    form.dimmed textarea,
    form.dimmed button {
        opacity: 0.6;
        pointer-events: none;
    }

    form.dimmed select {
        opacity: 1;
        pointer-events: auto;
    }

    input,
    textarea,
    select {
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 1rem;
        width: 100%;
        outline: none;
        transition: border 0.3s ease;
    }

    input:focus,
    textarea:focus,
    select:focus {
        border-color: #0b6026;
    }

    button {
        padding: 14px;
        border: none;
        border-radius: 8px;
        background: #0b6026;
        color: white;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s ease, transform 0.2s ease;
    }

    button:hover {
        background: #064418;
        transform: translateY(-2px);
    }

    /* ===== SUCCESS POPUP ===== */
    .success-popup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.8);
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        padding: 40px;
        text-align: center;
        z-index: 1000;
        opacity: 0;
        transition: all 0.4s ease;
    }

    .success-popup.show {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }

    .success-popup h3 {
        color: #0b6026;
        font-size: 1.8rem;
        margin-bottom: 10px;
    }

    .success-popup p {
        color: #333;
        font-size: 1rem;
        margin-bottom: 15px;
    }

    .celebration-bubble {
        position: absolute;
        border-radius: 50%;
        animation: burst 2s ease-out forwards;
    }

    @keyframes burst {
        0% {
            transform: scale(0);
            opacity: 1;
        }

        70% {
            transform: scale(1.2);
            opacity: 1;
        }

        100% {
            transform: scale(0);
            opacity: 0;
        }
    }

    @media (max-width: 600px) {
        .joinus-card {
            padding: 30px 20px;
        }
    }
</style>

<section class="joinus-container">
    <div class="joinus-card">
        <div class="form-bubbles"></div>

        <h2>Join Fangak Youth Union</h2>
        <p>Fill out the form below to become part of our growing community. Together, we innovate and lead change.</p>

        <form id="joinForm" action="joinus_submit.php" method="POST">
            <select name="payam" id="payamSelect" required>
                <option value="">Select Payam</option>
                <option value="Pulita">Pulita</option>
                <option value="Barbuoi">Barbuoi</option>
                <option value="Manajang">Manajang</option>
                <option value="Paguir">Paguir</option>
                <option value="Mareng">Mareng</option>
                <option value="Toch">Toch</option>
                <option value="New Fangak">New Fangak</option>
                <option value="Old Fangak">Old Fangak</option>
            </select>

            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <textarea name="message" rows="4" placeholder="Why do you want to join?" required></textarea>

            <button type="submit">Join Us</button>
        </form>
    </div>
</section>

<div class="success-popup" id="successPopup">
    <h3>🎉 Welcome to FYU!</h3>
    <p>Your registration was successful. Together, we lead change!</p>
</div>

<script>
    // ===== FORM BUBBLES (inside the card only) =====
    const bubbleContainer = document.querySelector('.form-bubbles');
    const colors = ['#ff4d4d', '#ff944d', '#ffeb3b', '#4dff88', '#4da6ff', '#b84dff'];

    for (let i = 0; i < 25; i++) {
        const bubble = document.createElement('div');
        bubble.classList.add('form-bubble');
        const size = Math.random() * 20 + 10;
        bubble.style.width = `${size}px`;
        bubble.style.height = `${size}px`;
        bubble.style.background = colors[Math.floor(Math.random() * colors.length)];
        bubble.style.left = `${Math.random() * 100}%`;
        bubble.style.top = `${Math.random() * 100}%`;
        bubble.style.animationDuration = `${Math.random() * 20 + 10}s`;
        bubble.style.animationDelay = `${Math.random() * 10}s`;
        bubbleContainer.appendChild(bubble);
    }

    // ===== ENABLE FORM FIELDS AFTER PAYAM SELECTION =====
    const payamSelect = document.getElementById("payamSelect");
    const joinForm = document.getElementById("joinForm");
    joinForm.classList.add("dimmed");

    payamSelect.addEventListener("change", () => {
        if (payamSelect.value !== "") {
            joinForm.classList.remove("dimmed");
        } else {
            joinForm.classList.add("dimmed");
        }
    });

    // ===== FORM SUBMISSION =====
    joinForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        // quick client-side guard (shouldn't run because fields are required)
        const payam = payamSelect.value.trim();
        if (!payam) {
            alert('Please select a Payam.');
            return;
        }

        const formData = new FormData(joinForm);

        try {
            const response = await fetch("joinus_submit.php", {
                method: "POST",
                body: formData
            });

            // parse JSON (our PHP returns JSON)
            const data = await response.json();

            if (data.status === "success") {
                showSuccess();
                joinForm.reset();
                joinForm.classList.add("dimmed");
            } else {
                // show a friendly message — include server message if present
                const msg = data.message || "Something went wrong. Please try again.";
                alert(msg);
                console.warn('Server error:', data);
            }
        } catch (err) {
            console.error(err);
            alert("Connection error. Please try again later.");
        }
    });

    // ===== CELEBRATION POPUP =====
    function showSuccess() {
        const popup = document.getElementById("successPopup");
        popup.classList.add("show");

        for (let i = 0; i < 30; i++) {
            const bubble = document.createElement("div");
            bubble.classList.add("celebration-bubble");
            bubble.style.background = colors[Math.floor(Math.random() * colors.length)];
            const size = Math.random() * 20 + 10;
            bubble.style.width = `${size}px`;
            bubble.style.height = `${size}px`;
            bubble.style.left = `${Math.random() * 100}%`;
            bubble.style.top = `${Math.random() * 100}%`;
            bubble.style.animationDuration = `${1 + Math.random() * 2}s`;
            popup.appendChild(bubble);
            setTimeout(() => bubble.remove(), 2000);
        }

        setTimeout(() => popup.classList.remove("show"), 4000);
    }
</script>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>