<?php 
$pageTitle = "Join the Movement - FYU";
include_once __DIR__ . "/../app/views/layouts/header.php"; 
?>

<style>
    .reg-bg { background: #f4f7f6; padding: 60px 0; }
    .reg-card { 
        max-width: 900px; margin: auto; background: white; 
        display: flex; border-radius: 20px; overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1); 
    }
    .reg-sidebar { 
        width: 35%; background: #0b6026; color: white; padding: 40px;
        display: flex; flex-direction: column; justify-content: center;
    }
    .reg-form-area { width: 65%; padding: 40px; }
    
    .input-group { margin-bottom: 20px; }
    .input-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #444; }
    .input-group input, .input-group select { 
        width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 10px; transition: 0.3s;
    }
    .input-group input:focus { border-color: #0b6026; outline: none; }
    
    .submit-btn { 
        width: 100%; padding: 15px; background: #0b6026; color: white; 
        border: none; border-radius: 10px; font-size: 1.1rem; cursor: pointer;
    }
</style>

<div class="reg-bg">
    <div class="reg-card">
        <div class="reg-sidebar">
            <h2>Join the Union</h2>
            <p>Empowering the youth of Fangak to lead through innovation and integrity.</p>
            <hr style="opacity: 0.2; margin: 20px 0;">
            <small>✓ Leadership Training<br>✓ Community Projects<br>✓ Global Networking</small>
        </div>
        <div class="reg-form-area">
            <form id="mainRegForm" enctype="multipart/form-data">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" placeholder="As it appears on ID" required>
                </div>
                
                <div style="display: flex; gap: 20px;">
                    <div class="input-group" style="flex:1;">
                        <label>Payam</label>
                        <select name="payam" required>
                            <option value="">Choose...</option>
                            <option>New Fangak</option>
                            <option>Old Fangak</option>
                            <option>Paguir</option>
                        </select>
                    </div>
                    <div class="input-group" style="flex:1;">
                        <label>Age</label>
                        <input type="number" name="age" min="18" max="35" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Profile Photo</label>
                    <input type="file" name="photo" accept="image/*" required>
                </div>

                <button type="submit" class="submit-btn" id="regBtn">Complete Registration</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('mainRegForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('regBtn');
    btn.disabled = true;
    btn.innerText = "Processing...";

    try {
        const response = await fetch('register_submit.php', {
            method: 'POST',
            body: new FormData(e.target)
        });
        const result = await response.json();

        if (result.success) {
            Swal.fire('Welcome!', 'Your application is being reviewed.', 'success');
            e.target.reset();
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    } catch (err) {
        Swal.fire('Connection Error', 'Please check your internet.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerText = "Complete Registration";
    }
});
</script>