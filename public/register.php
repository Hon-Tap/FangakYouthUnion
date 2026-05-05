<?php
$pageTitle = "Join Us - Fangak Youth Union";
include_once __DIR__ . "/../app/views/layouts/header.php";
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
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

    .register-section {
        padding: 60px 20px;
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .register-wrapper {
        max-width: 1100px;
        width: 100%;
        background: var(--surface);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        display: grid;
        grid-template-columns: 1.1fr 1.4fr;
        overflow: hidden;
    }

    /* BRAND PANEL */
    .register-info {
        background: linear-gradient(145deg, var(--fyu-green), var(--fyu-green-dark));
        color: white;
        padding: 50px 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
    }

    .register-info h2 { font-size: 2.2rem; font-weight: 700; margin-bottom: 20px; }
    .register-info p { font-size: 1.05rem; color: rgba(255,255,255,0.9); margin-bottom: 30px; }

    .feature-list { list-style: none; padding: 0; }
    .feature-list li {
        display: flex; align-items: center; gap: 12px;
        margin-bottom: 15px; font-size: 0.95rem; font-weight: 500;
        background: rgba(255,255,255,0.1); padding: 12px 16px; border-radius: 8px;
    }

    /* FORM PANEL */
    .register-card { padding: 50px; }
    .register-card-header h2 { font-size: 1.75rem; color: var(--text-main); margin-bottom: 8px; font-weight: 700; }
    .register-card-header p { color: var(--text-muted); margin-bottom: 30px; font-size: 0.95rem; }

    .register-form { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .full-width { grid-column: span 2; }

    .input-group { display: flex; flex-direction: column; gap: 6px; }
    .input-group label { font-size: 0.85rem; font-weight: 600; color: var(--text-main); }

    .register-form input, .register-form select {
        padding: 14px 16px; border-radius: var(--radius); border: 1px solid var(--border-color);
        background: #f9fafb; font-size: 0.95rem; font-family: inherit; transition: var(--transition); width: 100%; box-sizing: border-box;
    }

    .register-form input:focus, .register-form select:focus {
        border-color: var(--fyu-green); background: var(--surface); box-shadow: 0 0 0 4px var(--ring-color); outline: none;
    }

    /* EXPANDABLE FIELDS */
    .expandable-field {
        max-height: 0; opacity: 0; overflow: hidden;
        transition: max-height 0.4s ease, opacity 0.3s ease;
        grid-column: span 2; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
    }
    .expandable-field.show { max-height: 120px; opacity: 1; margin-top: 5px; }

    /* PHOTO UPLOAD UX */
    .photo-preview-container { display: flex; align-items: center; gap: 15px; margin-top: 5px; }
    .preview-circle {
        width: 65px; height: 65px; border-radius: 50%; border: 2px solid var(--fyu-green);
        background: #eee; overflow: hidden; display: none; object-fit: cover;
    }
    .file-upload-wrapper {
        flex: 1; border: 2px dashed var(--border-color); border-radius: var(--radius);
        padding: 20px; text-align: center; background: #f9fafb; cursor: pointer; transition: var(--transition);
    }
    .file-upload-wrapper:hover { border-color: var(--fyu-green); background: var(--fyu-green-light); }
    .file-upload-wrapper i { font-size: 1.5rem; color: var(--fyu-green); margin-bottom: 5px; }

    /* BUTTONS */
    .submit-btn {
        padding: 16px; background: var(--fyu-green); color: white; border: none;
        border-radius: var(--radius); font-size: 1rem; font-weight: 600; cursor: pointer;
        transition: var(--transition); display: flex; justify-content: center; align-items: center; gap: 10px; width: 100%;
    }
    .submit-btn:hover:not(:disabled) { background: var(--fyu-green-dark); transform: translateY(-2px); }
    .submit-btn:disabled { background: #9ca3af; cursor: not-allowed; }

    .btn-loader { display: none; width: 20px; height: 20px; border: 3px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 1s linear infinite; }
    @keyframes spin { 100% { transform: rotate(360deg); } }

    @media(max-width: 900px) {
        .register-wrapper { grid-template-columns: 1fr; }
        .register-form { grid-template-columns: 1fr; }
        .full-width, .expandable-field { grid-column: span 1; grid-template-columns: 1fr; }
        .expandable-field.show { max-height: 250px; }
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
                <h2>Membership Registration</h2>
                <p>Please provide accurate information for verification.</p>
            </div>

            <form id="registerForm" class="register-form" method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" placeholder="fangak son/daughter" required>
                </div>

                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="email@example.com" required>
                </div>

                <div class="input-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" placeholder="number ..." required>
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
                        <option>Pulita</option><option>Paguir</option>
                        <option>Manajang</option><option>Barbuoi</option>
                        <option>Mareang</option><option>Toch</option>
                        <option>New Fangak</option><option>Old Fangak</option>
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
                    </select>
                </div>

                <div id="eduExtras" class="expandable-field">
                    <div class="input-group">
                        <label>Course / Major</label>
                        <input type="text" name="course" placeholder="e.g. IT, Nursing">
                    </div>
                    <div class="input-group">
                        <label>Current Status</label>
                        <input type="text" name="year_or_done" placeholder="e.g. Final Year">
                    </div>
                </div>

                <div id="secExtras" class="expandable-field">
                    <div class="input-group">
                        <label>Secondary Stream</label>
                        <select name="sec_stream">
                            <option value="">Select Stream</option>
                            <option>Science</option>
                            <option>Arts</option>
                        </select>
                    </div>
                </div>

                <div class="input-group full-width">
                    <label>Passport Photo</label>
                    <div class="photo-preview-container">
                        <img id="imgPreview" class="preview-circle">
                        <label for="photo-upload" class="file-upload-wrapper" id="drop-zone">
                            <i class="fa-solid fa-camera"></i>
                            <span id="file-label-text" style="display:block; font-weight:600; font-size:0.9rem;">Click to upload photo</span>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">Max size: 2MB (JPG/PNG)</span>
                        </label>
                    </div>
                    <input type="file" id="photo-upload" name="photo" accept="image/*" required hidden>
                </div>

                <div class="full-width" style="margin-top: 10px;">
                    <button type="submit" class="submit-btn" id="submitBtn">
                        <span id="btnText">Apply for Membership</span>
                        <div class="btn-loader" id="btnLoader"></div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    const eduSelect = document.getElementById('education_level');
    const fileInput = document.getElementById('photo-upload');
    const imgPreview = document.getElementById('imgPreview');
    const labelText = document.getElementById('file-label-text');

    // Conditional Fields Logic
    eduSelect.addEventListener('change', () => {
        const val = eduSelect.value;
        document.getElementById('eduExtras').classList.toggle('show', ['Undergraduate', 'Graduate'].includes(val));
        document.getElementById('secExtras').classList.toggle('show', val === 'Secondary');
    });

    // Image Preview Logic
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                imgPreview.src = e.target.result;
                imgPreview.style.display = 'block';
                labelText.innerText = file.name;
            };
            reader.readAsDataURL(file);
        }
    });

    // Form Submission
    const form = document.getElementById('registerForm');
    form.addEventListener('submit', async e => {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        const loader = document.getElementById('btnLoader');
        const txt = document.getElementById('btnText');

        btn.disabled = true;
        loader.style.display = 'block';
        txt.innerText = 'Processing...';

        try {
            const formData = new FormData(form);
            const res = await fetch('register_submit.php', { method: 'POST', body: formData });
            const result = await res.json();

            if (result.success) {
                Swal.fire({ icon: 'success', title: 'Success!', text: 'Application submitted successfully.', confirmButtonColor: '#0b6026' })
                .then(() => window.location.reload());
            } else {
                Swal.fire('Error', result.message || 'Submission failed.', 'error');
            }
        } catch (err) {
            Swal.fire('Error', 'Connection failed. Please try again.', 'error');
        } finally {
            btn.disabled = false;
            loader.style.display = 'none';
            txt.innerText = 'Apply for Membership';
        }
    });
</script>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>