<?php
$pageTitle = "Join Us - Fangak Youth Union";
include_once __DIR__ . "/../app/views/layouts/header.php";
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* ===== GENERAL STYLES ===== */
body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #0b6026, #064418);
    margin: 0;
    padding: 40px;
    scroll-behavior: smooth;
    position: relative;
}

/* ===== PAGE LAYOUT ===== */
.register-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: stretch;
    min-height: 100vh;
    padding: 60px 20px;
    gap: 40px;
    position: relative;
    z-index: 1;
}

/* ===== INFO SECTION ===== */
.register-info {
    flex: 1 1 350px;
    max-width: 500px;
    color: #fff;
    text-align: left;
}

.register-info h2 {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 20px;
}

/* ===== FORM CARD ===== */
.register-card {
    flex: 1 1 350px;
    max-width: 500px;
    background: rgba(255,255,255,0.95);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.25);
}

.register-card h2 {
    font-size: 1.8rem;
    font-weight: 800;
    color: #0b6026;
    margin-bottom: 10px;
}

/* ===== FORM FIELDS ===== */
form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

input, select {
    padding: 14px;
    border-radius: 10px;
    border: 1px solid #ccc;
    outline: none;
    font-size: 1rem;
    width: 100%;
    background: #f9f9f9;
    transition: border 0.3s ease, background 0.3s ease;
}

input:focus, select:focus {
    border-color: #0b6026;
    background: #fff;
}

/* ===== BUTTON ===== */
button {
    padding: 14px;
    border: none;
    border-radius: 10px;
    background: #0b6026;
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
}
button:hover { background: #064418; transform: translateY(-2px); }

/* ===== PROGRESS BAR ===== */
.progress-bar {
    width: 100%;
    height: 6px;
    border-radius: 5px;
    background: #eee;
    overflow: hidden;
    margin-top: 10px;
    display: none;
}
.progress-bar-inner {
    width: 0%;
    height: 100%;
    background: linear-gradient(90deg,#107e36,#0b6026);
    transition: width 0.3s ease;
}

@media(max-width:900px){
    .register-container { flex-direction: column; align-items:center; padding-bottom:80px; }
    .register-info, .register-card{max-width:100%; width:100%; padding:20px;}
}
</style>

<section class="register-container">

    <div class="register-info">
        <h2>Why Join Fangak Youth Union?</h2>
        <ul>
            <li>Be part of a dynamic youth community that innovates and leads change in Fangak.</li>
            <li>We provide training, networking, and projects to grow your skills and serve your community.</li>
            <li>Stay updated with events, workshops, and initiatives that empower young leaders.</li>
        </ul>
    </div>

    <div class="register-card">
        <h2>Membership Registration</h2>
        <p>Fill out the form below to join us and start your journey of impact.</p>

        <form id="registerForm" method="POST" enctype="multipart/form-data">
            <select name="payam" required>
                <option value="">Select Payam</option>
                <option value="Pulita">Pulita</option>
                <option value="Paguir">Paguir</option>
                <option value="Manajang">Manajang</option>
                <option value="Barbouy">Barbouy</option>
                <option value="Mareng">Mareng</option>
                <option value="Toch">Toch</option>
                <option value="New Fangak">New Fangak</option>
                <option value="Old Fangak">Old Fangak</option>
            </select>

            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="phone" placeholder="Phone Number" required>

            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <select name="age" required>
                <option value="">Select Age</option>
                <?php for($i=18;$i<=35;$i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>

            <select name="education_level" id="education_level" required>
                <option value="">Select Education Level</option>
                <option value="Primary">Primary</option>
                <option value="Secondary">Secondary</option>
                <option value="Undergraduate">Undergraduate</option>
                <option value="Graduate">Graduate</option>
                <option value="Others">Others</option>
            </select>

            <div id="educationExtras" style="display:none;">
                <input type="text" name="course" id="course" placeholder="Course (if applicable)">
                <input type="text" name="year_or_done" id="year_or_done" placeholder="Year / Completed (if applicable)">
            </div>

            <div id="secondaryExtras" style="display:none;">
                <select name="course" id="secondaryStream">
                    <option value="">Select Stream</option>
                    <option value="Science">Science</option>
                    <option value="Arts">Arts</option>
                </select>
            </div>

            <input type="file" name="photo" accept="image/*" required>

            <button type="submit">Register</button>
            <div class="progress-bar"><div class="progress-bar-inner"></div></div>
        </form>
    </div>

</section>

<script>
// ===== EDUCATION FIELD DYNAMICS =====
const eduSelect = document.getElementById('education_level');
const eduExtras = document.getElementById('educationExtras');
const secExtras = document.getElementById('secondaryExtras');

eduSelect.addEventListener('change', () => {
  const val = eduSelect.value;
  eduExtras.style.display = 'none';
  secExtras.style.display = 'none';

  if (val === 'Undergraduate' || val === 'Graduate') {
    eduExtras.style.display = 'block';
  } else if (val === 'Secondary') {
    secExtras.style.display = 'block';
  }
});

// ===== FORM SUBMISSION =====
const registerForm = document.getElementById('registerForm');
const progressBar = registerForm.querySelector('.progress-bar');
const progressInner = registerForm.querySelector('.progress-bar-inner');

registerForm.addEventListener('submit', async e => {
  e.preventDefault();

  progressBar.style.display = 'block';
  progressInner.style.width = '0%';
  let progress = 0;
  const interval = setInterval(() => {
    if (progress < 90) {
      progress += Math.random() * 10;
      progressInner.style.width = progress + '%';
    }
  }, 200);

  const formData = new FormData(registerForm);

  try {
    const res = await fetch('register_submit.php', { method: 'POST', body: formData });
    const data = await res.json();

    clearInterval(interval);
    progressInner.style.width = '100%';
    setTimeout(() => (progressBar.style.display = 'none'), 500);

    if (data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Welcome to FYU!',
        text: 'Your registration was successful. Please wait for admin approval.',
        showConfirmButton: true,
        confirmButtonColor: '#0b6026'
      });
      registerForm.reset();
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: data.message || 'Something went wrong. Please try again.',
        confirmButtonColor: '#0b6026'
      });
    }
  } catch (err) {
    clearInterval(interval);
    progressBar.style.display = 'none';
    Swal.fire({
      icon: 'error',
      title: 'Connection Error',
      text: 'Unable to connect. Please try again later.',
      confirmButtonColor: '#0b6026'
    });
    console.error(err);
  }
});
</script>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>