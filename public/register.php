<?php
$pageTitle = "Join Us - Fangak Youth Union";
include_once __DIR__ . "/../app/views/layouts/header.php";
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>

/* PAGE WRAPPER */
.register-section{
    padding:80px 20px;
    background:linear-gradient(135deg,#0b6026,#064418);
}

/* CENTERED CONTAINER (same width feel as other pages) */
.register-wrapper{
    max-width:1100px;
    margin:auto;
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:50px;
    align-items:center;
}

/* LEFT INFO PANEL */
.register-info{
    color:white;
}

.register-info h2{
    font-size:2rem;
    font-weight:700;
    margin-bottom:20px;
}

.register-info ul{
    padding-left:18px;
    line-height:1.8;
}

/* FORM CARD */
.register-card{
    background:white;
    padding:40px;
    border-radius:14px;
    box-shadow:0 10px 30px rgba(0,0,0,.2);
}

.register-card h2{
    font-size:1.6rem;
    color:#0b6026;
    margin-bottom:10px;
}

.register-card p{
    font-size:.95rem;
    margin-bottom:20px;
    color:#666;
}

/* FORM */
.register-form{
    display:grid;
    gap:14px;
}

.register-form input,
.register-form select{
    padding:12px;
    border-radius:8px;
    border:1px solid #ddd;
    font-size:.95rem;
    width:100%;
}

.register-form input:focus,
.register-form select:focus{
    border-color:#0b6026;
    outline:none;
}

/* BUTTON */
.register-form button{
    padding:14px;
    background:#0b6026;
    color:white;
    border:none;
    border-radius:8px;
    font-weight:600;
    cursor:pointer;
}

.register-form button:hover{
    background:#064418;
}

/* PROGRESS BAR */
.progress-bar{
    height:6px;
    background:#eee;
    border-radius:10px;
    overflow:hidden;
    display:none;
}

.progress-inner{
    height:100%;
    width:0%;
    background:#0b6026;
}

/* MOBILE */
@media(max-width:900px){

.register-wrapper{
    grid-template-columns:1fr;
}

.register-card{
    padding:25px;
}

.register-section{
    padding:60px 15px;
}

}

</style>


<section class="register-section">

<div class="register-wrapper">

<!-- LEFT INFO -->
<div class="register-info">

<h2>Join Fangak Youth Union</h2>

<p>
Become part of a growing network of young leaders dedicated to building
a stronger Fangak community through innovation, collaboration, and service.
</p>

<ul>
<li>Leadership training & youth development</li>
<li>Community development projects</li>
<li>Networking with youth leaders</li>
<li>Participation in events and initiatives</li>
</ul>

</div>


<!-- FORM -->
<div class="register-card">

<h2>Membership Registration</h2>
<p>Complete the form below to apply for membership.</p>

<form id="registerForm" class="register-form" method="POST" enctype="multipart/form-data">

<select name="payam" required>
<option value="">Select Payam</option>
<option>Pulita</option>
<option>Paguir</option>
<option>Manajang</option>
<option>Barbouy</option>
<option>Mareng</option>
<option>Toch</option>
<option>New Fangak</option>
<option>Old Fangak</option>
</select>

<input type="text" name="full_name" placeholder="Full Name" required>

<input type="email" name="email" placeholder="Email Address" required>

<input type="text" name="phone" placeholder="Phone Number" required>

<select name="gender" required>
<option value="">Select Gender</option>
<option>Male</option>
<option>Female</option>
</select>

<select name="age" required>
<option value="">Select Age</option>
<?php for($i=18;$i<=35;$i++): ?>
<option value="<?= $i ?>"><?= $i ?></option>
<?php endfor; ?>
</select>

<select name="education_level" id="education_level" required>
<option value="">Education Level</option>
<option>Primary</option>
<option>Secondary</option>
<option>Undergraduate</option>
<option>Graduate</option>
<option>Others</option>
</select>

<div id="educationExtras" style="display:none;">
<input type="text" name="course" placeholder="Course">
<input type="text" name="year_or_done" placeholder="Year / Completed">
</div>

<div id="secondaryExtras" style="display:none;">
<select name="course">
<option value="">Stream</option>
<option>Science</option>
<option>Arts</option>
</select>
</div>

<input type="file" name="photo" accept="image/*" required>

<button type="submit">Register</button>

<div class="progress-bar">
<div class="progress-inner"></div>
</div>

</form>

</div>

</div>
</section>


<script>

const eduSelect=document.getElementById('education_level');
const eduExtras=document.getElementById('educationExtras');
const secExtras=document.getElementById('secondaryExtras');

eduSelect.addEventListener('change',()=>{
let val=eduSelect.value;

eduExtras.style.display='none';
secExtras.style.display='none';

if(val==='Undergraduate'||val==='Graduate'){
eduExtras.style.display='block';
}

if(val==='Secondary'){
secExtras.style.display='block';
}

});

const form=document.getElementById('registerForm');
const progressBar=document.querySelector('.progress-bar');
const progressInner=document.querySelector('.progress-inner');

form.addEventListener('submit',async e=>{

e.preventDefault();

progressBar.style.display='block';
progressInner.style.width='10%';

let data=new FormData(form);

try{

const res=await fetch('register_submit.php',{method:'POST',body:data});
const result=await res.json();

progressInner.style.width='100%';

if(result.success){

Swal.fire({
icon:'success',
title:'Registration Successful',
text:'Your membership request has been submitted.',
confirmButtonColor:'#0b6026'
});

form.reset();

}else{

Swal.fire({
icon:'error',
title:'Error',
text:result.message || 'Something went wrong',
confirmButtonColor:'#0b6026'
});

}

setTimeout(()=>progressBar.style.display='none',600);

}catch(err){

progressBar.style.display='none';

Swal.fire({
icon:'error',
title:'Connection Error',
text:'Please try again later',
confirmButtonColor:'#0b6026'
});

}

});

</script>

<?php include_once __DIR__ . "/../app/views/layouts/footer.php"; ?>