<?php
$pageTitle = "Join Us - Fangak Youth Union";
include_once __DIR__ . "/../app/views/layouts/header.php";
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* --- ADVANCED UI/UX VARIABLES --- */
    :root {
        --fyu-green: #0b6026;
        --fyu-green-dark: #064418;
        --fyu-green-light: #e8f5e9;
        --surface: #ffffff;
        --text-main: #1f2937;
        --text-muted: #6b7280;
        --border-color: #d1d5db;
        --ring-color: rgba(11, 96, 38, 0.15);
        --radius: 12px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
        background-color: #f3f4f6;
        font-family: 'Inter', sans-serif;
    }

    /* PAGE WRAPPER */
    .register-section {
        padding: 60px 20px;
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* CENTERED SPLIT CARD */
    .register-wrapper {
        max-width: 1100px;
        width: 100%;
        background: var(--surface);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.08), 0 1px 3px rgba(0,0,0,0.05);
        display: grid;
        grid-template-columns: 1.1fr 1.4fr;
        overflow: hidden;
    }

    /* LEFT INFO PANEL (BRANDING) */
    .register-info {
        background: linear-gradient(145deg, var(--fyu-green), var(--fyu-green-dark));
        color: white;
        padding: 50px 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    /* Decorative Background SVG */
    .register-info::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 10%, transparent 10%), radial-gradient(circle, rgba(255,255,255,0.05) 10%, transparent 10%);
        background-size: 40px 40px;
        background-position: 0 0, 20px 20px;
        opacity: 0.3;
        animation: subtleMove 30s linear infinite;
        z-index: 0;
    }

    @keyframes subtleMove {
        0% { transform: translateY(0); }
        100% { transform: translateY(-40px); }
    }

    .register-info-content {
        position: relative;
        z-index: 1;
    }

    .register-info h2 {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 20px;
        line-height: 1.2;
    }

    .register-info p {
        font-size: 1.05rem;
        color: rgba(255,255,255,0.9);
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .feature-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .feature-list li {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 15px;
        font-size: 0.95rem;
        font-weight: 500;
        background: rgba(255,255,255,0.1);
        padding: 12px 16px;
        border-radius: 8px;
        backdrop-filter: blur(5px);
    }

    .feature-list i {
        color: #a7f3d0;
        font-size: 1.1rem;
    }

    /* RIGHT FORM PANEL */
    .register-card {
        padding: 50px;
        background: var(--surface);
    }

    .register-card-header h2 {
        font-size: 1.75rem;
        color: var(--text-main);
        margin-bottom: 8px;
        font-weight: 700;
    }

    .register-card-header p {
        color: var(--text-muted);
        margin-bottom: 30px;
        font-size: 0.95rem;
    }

    /* FORM LAYOUT */
    .register-form {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .full-width {
        grid-column: span 2;
    }

    /* MODERN INPUTS */
    .input-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .input-group label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-main);
    }

    .register-form input[type="text"],
    .register-form input[type="email"],
    .register-form select {
        padding: 14px 16px;
        border-radius: var(--radius);
        border: 1px solid var(--border-color);
        background: #f9fafb;
        font-size: 0.95rem;
        font-family: inherit;
        color: var(--text-main);
        transition: var(--transition);
        width: 100%;
        box-sizing: border-box;
    }

    .register-form input:focus,
    .register-form select:focus {
        border-color: var(--fyu-green);
        background: var(--surface);
        box-shadow: 0 0 0 4px var(--ring-color);
        outline: none;
    }

    /* ANIMATED CONDITIONAL FIELDS */
    .expandable-field {
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition: max-height 0.4s ease, opacity 0.3s ease, margin 0.3s ease;
        grid-column: span 2;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .expandable-field.show {
        max-height: 100px; /* Enough space to show inputs */
        opacity: 1;
    }

    /* CUSTOM FILE UPLOAD UI */
    .file-upload-wrapper {
        border: 2px dashed var(--border-color);
        border-radius: var(--radius);
        padding: 25px;
        text-align: center;
        background: #f9fafb;
        cursor: pointer;
        transition: var(--transition);
    }

    .file-upload-wrapper:hover, .file-upload-wrapper.dragover {
        border-color: var(--fyu-green);
        background: var(--fyu-green-light);
    }

    .file-upload-wrapper i {
        font-size: 2rem;
        color: var(--fyu-green);
        margin-bottom: 10px;
    }

    .file-upload-text {
        font-size: 0.95rem;
        font-weight: 500;
        color: var(--text-muted);
        display: block;
    }

    .file-upload-text span {
        color: var(--fyu-green);
        text-decoration: underline;
    }

    /* BUTTON & LOADING STATE */
    .submit-btn {
        padding: 16px;
        background: var(--fyu-green);
        color: white;
        border: none;
        border-radius: var(--radius);
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }

    .submit-btn:hover {
        background: var(--fyu-green-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(11, 96, 38, 0.2);
    }

    .submit-btn:disabled {
        background: #9ca3af;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .btn-loader {
        display: none;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin { 100% { transform: rotate(360deg); } }

    /* RESPONSIVE */
    @media(max-width: 900px) {
        .register-wrapper {
            grid-template-columns: 1fr;
        }
        .register-info {
            padding: 40px 20px;
        }
        .register-card {
            padding: 30px 20px;
        }
        .register-form {
            grid-template-columns: 1fr;
        }
        .full-width, .expandable-field {
            grid-column: span 1;
            grid-template-columns: 1fr;
        }
        .expandable-field.show {
            max-height: 200px; /* Taller for stacked inputs on mobile */
        }
    }
</style>

<section class="register-section">
    <div class="register-wrapper">

        <div class="register-info">
            <div class="register-info-content">
                <h2>Join Fangak Youth Union</h2>
                <p>Become part of a growing network of young leaders dedicated to building a stronger Fangak community through innovation, collaboration, and service.</p>
                <ul class="feature-list">
                    <li><i class="fa-solid fa-graduation-cap"></i> Leadership training & youth development</li>
                    <li><i class="fa-solid fa-seedling"></i> Community development projects</li>
                    <li><i class="fa-solid fa-users-gear"></i> Networking with global youth leaders</li>
                    <li><i class="fa-solid fa-calendar-check"></i> Exclusive events and initiatives</li>
                </ul>
            </div>
        </div>

        <div class="register-card">
            <div class="register-card-header">
                <h2>Membership Application</h2>
                <p>Complete the form below to begin your journey with us.</p>
            </div>

            <form id="registerForm" class="register-form" method="POST" enctype="multipart/form-data">

                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" placeholder="Fangak son/daughter" required>
                </div>

                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="email@example.com" required>
                </div>

                <div class="input-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" placeholder="+211 ..." required>
                </div>

                <div class="input-group">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="">Select Gender</option>
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>Payam</label>
                    <select name="payam" required>
                        <option value="">Select Payam</option>
                        <option>Pulita</option>
                        <option>Paguir</option>
                        <option>Manajang</option>
                        <option>Barbuoi</option>
                        <option>Mareang</option>
                        <option>Toch</option>
                        <option>New Fangak</option>
                        <option>Old Fangak</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>Age</label>
                    <select name="age" required>
                        <option value="">Select Age</option>
                        <?php for($i=18; $i<=35; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="input-group full-width">
                    <label>Highest Education Level</label>
                    <select name="education_level" id="education_level" required>
                        <option value="">Select Education Level</option>
                        <option>Primary</option>
                        <option>Secondary</option>
                        <option>Undergraduate</option>
                        <option>Graduate</option>
                        <option>Others</option>
                    </select>
                </div>

                <div id="educationExtras" class="expandable-field">
                    <div class="input-group">
                        <label>Course of Study</label>
                        <input type="text" name="course" placeholder="e.g. Computer Science">
                    </div>
                    <div class="input-group">
                        <label>Year / Completed</label>
                        <input type="text" name="year_or_done" placeholder="e.g. Year 3 or 2024">
                    </div>
                </div>

                <div id="secondaryExtras" class="expandable-field">
                    <div class="input-group">
                        <label>Secondary Stream</label>
                        <select name="course">
                            <option value="">Select Stream</option>
                            <option>Science</option>
                            <option>Arts</option>
                        </select>
                    </div>
                    <div></div>
                </div>

                <div class="input-group full-width">
                    <label>Profile Photo</label>
                    <label for="photo-upload" class="file-upload-wrapper" id="drop-zone">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span class="file-upload-text" id="file-name-display">Drag & drop your photo here or <span>browse</span></span>
                    </label>
                    <input type="file" id="photo-upload" name="photo" accept="image/*" required hidden>
                </div>

                <div class="full-width" style="margin-top: 10px;">
                    <button type="submit" class="submit-btn" id="submitBtn">
                        <span id="btnText">Submit Application</span>
                        <div class="btn-loader" id="btnLoader"></div>
                    </button>
                </div>

            </form>
        </div>
    </div>
</section>

<script>
    // --- UI Logic: Conditional Fields ---
    const eduSelect = document.getElementById('education_level');
    const eduExtras = document.getElementById('educationExtras');
    const secExtras = document.getElementById('secondaryExtras');

    eduSelect.addEventListener('change', () => {
        let val = eduSelect.value;
        
        // Reset classes
        eduExtras.classList.remove('show');
        secExtras.classList.remove('show');

        // Small delay ensures smooth transition if switching between them
        setTimeout(() => {
            if (val === 'Undergraduate' || val === 'Graduate') {
                eduExtras.classList.add('show');
            } else if (val === 'Secondary') {
                secExtras.classList.add('show');
            }
        }, 50);
    });

    // --- UI Logic: Custom File Input ---
    const fileInput = document.getElementById('photo-upload');
    const fileNameDisplay = document.getElementById('file-name-display');
    const dropZone = document.getElementById('drop-zone');

    fileInput.addEventListener('change', function(e) {
        if(this.files && this.files.length > 0) {
            fileNameDisplay.innerHTML = `<span style="color: var(--fyu-green);"><i class="fa-solid fa-image"></i> ${this.files[0].name}</span>`;
            dropZone.style.borderColor = 'var(--fyu-green)';
            dropZone.style.backgroundColor = 'var(--fyu-green-light)';
        }
    });

    // --- Form Submission Logic ---
    const form = document.getElementById('registerForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');

    form.addEventListener('submit', async e => {
        e.preventDefault();

        // Loading State
        submitBtn.disabled = true;
        btnText.textContent = 'Processing...';
        btnLoader.style.display = 'block';

        let data = new FormData(form);

        try {
            const res = await fetch('register_submit.php', { method: 'POST', body: data });
            const result = await res.json();

            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Welcome!',
                    text: 'Your membership request has been submitted successfully.',
                    confirmButtonColor: '#0b6026',
                    backdrop: `rgba(11, 96, 38, 0.4)`
                });
                form.reset();
                // Reset UI custom states
                eduExtras.classList.remove('show');
                secExtras.classList.remove('show');
                fileNameDisplay.innerHTML = `Drag & drop your photo here or <span>browse</span>`;
                dropZone.style.borderColor = 'var(--border-color)';
                dropZone.style.backgroundColor = '#f9fafb';
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Registration Failed',
                    text: result.message || 'Please check your inputs and try again.',
                    confirmButtonColor: '#0b6026'
                });
            }

        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Connection Error',
                text: 'Unable to reach the server. Please try again later.',
                confirmButtonColor: '#0b6026'
            });
        } finally {
            // Restore Button State
            submitBtn.disabled = false;
            btnText.textContent = 'Submit Application';
            btnLoader.style.display = 'none';
        }
    });
</script>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>