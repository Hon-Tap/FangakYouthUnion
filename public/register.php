<?php
$pageTitle = "Join Us - Fangak Youth Union";
include_once __DIR__ . "/../app/views/layouts/header.php";
?>

<!-- UI Dependencies -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root {
        --fyu-green: #0b6026;
        --fyu-green-dark: #064418;
        --fyu-green-light: #e8f5e9;
        --error-red: #dc2626;
        --surface: #ffffff;
        --text-main: #1f2937;
        --text-muted: #6b7280;
        --border-color: #d1d5db;
        --ring-color: rgba(11, 96, 38, 0.15);
        --radius: 12px;
        --transition: all 0.25s ease;
    }

    body { background-color: #f3f4f6; font-family: 'Inter', sans-serif; }

    .register-section { padding: 40px 20px; min-height: 90vh; display: flex; align-items: center; justify-content: center; }

    .register-wrapper {
        max-width: 1050px; width: 100%; background: var(--surface);
        border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.1);
        display: grid; grid-template-columns: 1fr 1.5fr; overflow: hidden;
    }

    /* LEFT INFO PANEL */
    .register-info {
        background: linear-gradient(135deg, var(--fyu-green), var(--fyu-green-dark));
        color: white; padding: 60px 40px; position: relative;
    }

    .register-info h2 { font-size: 2.4rem; margin-bottom: 20px; font-weight: 800; }
    
    .feature-list { list-style: none; padding: 0; margin-top: 40px; }
    .feature-list li { 
        margin-bottom: 20px; display: flex; align-items: center; gap: 15px;
        background: rgba(255,255,255,0.1); padding: 15px; border-radius: 12px;
    }

    /* FORM PANEL */
    .register-card { padding: 45px; }
    .register-form { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
    .full-width { grid-column: span 2; }

    .input-group label { font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 6px; display: block; }
    
    .register-form input, .register-form select {
        padding: 12px 16px; border-radius: var(--radius); border: 1px solid var(--border-color);
        background: #fdfdfd; width: 100%; transition: var(--transition); font-size: 0.95rem;
    }

    .register-form input:focus { border-color: var(--fyu-green); box-shadow: 0 0 0 4px var(--ring-color); outline: none; }

    /* CONDITIONAL FIELDS ANIMATION */
    .expandable-field {
        max-height: 0; opacity: 0; overflow: hidden; grid-column: span 2;
        display: grid; grid-template-columns: 1fr 1fr; gap: 18px;
        transition: max-height 0.4s ease, opacity 0.3s ease;
    }
    .expandable-field.show { max-height: 150px; opacity: 1; margin-bottom: 10px; }

    /* PHOTO UPLOAD UX */
    .photo-preview-container {
        display: flex; align-items: center; gap: 15px; margin-top: 5px;
    }
    
    .preview-circle {
        width: 60px; height: 60px; border-radius: 50%; border: 2px solid var(--fyu-green);
        background: #eee; overflow: hidden; display: none; object-fit: cover;
    }

    .file-upload-label {
        flex: 1; border: 2px dashed var(--border-color); padding: 20px;
        border-radius: var(--radius); text-align: center; cursor: pointer; transition: var(--transition);
    }
    .file-upload-label:hover { border-color: var(--fyu-green); background: var(--fyu-green-light); }
    .file-size-hint { font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 4px; }

    /* SUBMIT BUTTON */
    .submit-btn {
        width: 100%; padding: 16px; background: var(--fyu-green); color: white;
        border: none; border-radius: var(--radius); font-weight: 700; font-size: 1.1rem;
        cursor: pointer; transition: var(--transition); display: flex; justify-content: center; align-items: center; gap: 10px;
    }
    .submit-btn:hover:not(:disabled) { background: var(--fyu-green-dark); transform: translateY(-2px); }
    .submit-btn:disabled { background: #9ca3af; cursor: wait; }

    .loader { width: 18px; height: 18px; border: 3px solid #ffffff44; border-top-color: #fff; border-radius: 50%; animation: spin 0.8s linear infinite; display: none; }
    @keyframes spin { to { transform: rotate(360deg); } }

    @media(max-width: 900px) {
        .register-wrapper { grid-template-columns: 1fr; }
        .register-info { padding: 40px 20px; text-align: center; }
        .register-form { grid-template-columns: 1fr; }
        .full-width, .expandable-field { grid-column: span 1; grid-template-columns: 1fr; }
        .expandable-field.show { max-height: 300px; }
    }
</style>

<section class="register-section">
    <div class="register-wrapper">
        <!-- Brand Side -->
        <div class="register-info">
            <h2>Join the Union</h2>
            <p>Empowering the youth of Fangak for a brighter, more connected future.</p>
            <ul class="feature-list">
                <li><i class="fa-solid fa-id-card"></i> Official Digital Membership</li>
                <li><i class="fa-solid fa-network-wired"></i> Professional Networking</li>
                <li><i class="fa-solid fa-handshake-angle"></i> Community Leadership</li>
            </ul>
        </div>

        <!-- Form Side -->
        <div class="register-card">
            <div style="margin-bottom: 25px;">
                <h2 style="font-size: 1.6rem; color: var(--text-main);">Create Account</h2>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Please provide accurate information for verification.</p>
            </div>

            <form id="registerForm" enctype="multipart/form-data">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" placeholder="John Doe" required>
                </div>

                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="name@email.com" required>
                </div>

                <div class="input-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" placeholder="+211 ..." required>
                </div>

                <div class="input-group">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="">Select</option>
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
                        <option value="">Select</option>
                        <?php for($i=18; $i<=35; $i++) echo "<option>$i</option>"; ?>
                    </select>
                </div>

                <div class="input-group full-width">
                    <label>Education Level</label>
                    <select name="education_level" id="education_level" required>
                        <option value="">Highest Level Attained</option>
                        <option>Primary</option>
                        <option>Secondary</option>
                        <option>Undergraduate</option>
                        <option>Graduate</option>
                    </select>
                </div>

                <!-- Conditional Fields -->
                <div id="eduExtras" class="expandable-field">
                    <div class="input-group">
                        <label>Course / Major</label>
                        <input type="text" name="course" placeholder="e.g. IT, Nursing">
                    </div>
                    <div class="input-group">
                        <label>Current Status</label>
                        <input type="text" name="year_or_done" placeholder="e.g. Final Year, Graduated">
                    </div>
                </div>

                <div id="secExtras" class="expandable-field">
                    <div class="input-group">
                        <label>Stream</label>
                        <select name="sec_stream">
                            <option value="">Select</option>
                            <option>Science</option>
                            <option>Arts</option>
                        </select>
                    </div>
                </div>

                <div class="input-group full-width">
                    <label>Passport Photo</label>
                    <div class="photo-preview-container">
                        <img id="imgPreview" class="preview-circle">
                        <label for="photo-upload" class="file-upload-label" id="drop-zone">
                            <i class="fa-solid fa-camera" id="upload-icon"></i>
                            <span id="file-label-text" style="display: block; font-weight: 600; font-size: 0.9rem;">Click to upload photo</span>
                            <span class="file-size-hint">Max size: 2MB (JPG/PNG)</span>
                        </label>
                    </div>
                    <input type="file" id="photo-upload" name="photo" accept="image/*" required hidden>
                </div>

                <div class="full-width" style="margin-top: 15px;">
                    <button type="submit" class="submit-btn" id="submitBtn">
                        <span id="btnText">Apply for Membership</span>
                        <div class="loader" id="btnLoader"></div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    const form = document.getElementById('registerForm');
    const eduSelect = document.getElementById('education_level');
    const fileInput = document.getElementById('photo-upload');
    const imgPreview = document.getElementById('imgPreview');
    const labelText = document.getElementById('file-label-text');

    // 1. Conditional Logic
    eduSelect.addEventListener('change', (e) => {
        const val = e.target.value;
        document.getElementById('eduExtras').classList.toggle('show', ['Undergraduate', 'Graduate'].includes(val));
        document.getElementById('secExtras').classList.toggle('show', val === 'Secondary');
    });

    // 2. Image Logic (Validation + Preview)
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (file) {
            if (file.size > maxSize) {
                Swal.fire('File Too Large', 'Please select an image smaller than 2MB.', 'warning');
                this.value = "";
                imgPreview.style.display = 'none';
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                imgPreview.src = e.target.result;
                imgPreview.style.display = 'block';
                labelText.innerText = file.name;
            };
            reader.readAsDataURL(file);
        }
    });

    // 3. Robust Submit Logic
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const btn = document.getElementById('submitBtn');
        const loader = document.getElementById('btnLoader');
        const txt = document.getElementById('btnText');

        // UI State: Loading
        btn.disabled = true;
        loader.style.display = 'block';
        txt.innerText = 'Uploading...';

        try {
            const formData = new FormData(form);
            const response = await fetch('register_submit.php', {
                method: 'POST',
                body: formData
            });

            // Handle non-JSON responses (server crashes)
            const textResponse = await response.text();
            let result;
            try {
                result = JSON.parse(textResponse);
            } catch(e) {
                console.error("Server raw output:", textResponse);
                throw new Error("Invalid server response format.");
            }

            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Your application has been received.',
                    confirmButtonColor: '#0b6026'
                }).then(() => {
                    window.location.reload(); 
                });
            } else {
                Swal.fire('Error', result.message || 'Submission failed.', 'error');
            }
        } catch (err) {
            Swal.fire('Connection Error', 'Check your internet and try again.', 'error');
        } finally {
            btn.disabled = false;
            loader.style.display = 'none';
            txt.innerText = 'Apply for Membership';
        }
    });
</script>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>