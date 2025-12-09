<?php
session_start();

// Ensure admin logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Expose admin info to JS (escaped)
$admin_name  = htmlspecialchars($_SESSION['admin_name'] ?? 'Admin');
$admin_email = htmlspecialchars($_SESSION['admin_email'] ?? '');
$admin_photo = htmlspecialchars($_SESSION['admin_photo'] ?? '/FangakYouthUnion/public/images/admin-default.jpg'); // prefer session photo if available
$basePath    = '/FangakYouthUnion/public';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>FYU Admin Dashboard</title>

<!-- Fonts & Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
:root{
  --bg:#f7f9f8;
  --card:#ffffff;
  --accent:#0c6a2a;
  --accent-2:#275a3f;
  --muted:#6b7280;
  --radius:12px;
  --sidebar-width:260px;
  --sidebar-collapsed:72px;
  --shadow: 0 8px 24px rgba(0,0,0,0.06);
  --header-height:60px;
}

/* reset */
*{box-sizing:border-box;margin:0;padding:0;font-family:'Inter',system-ui,Arial, sans-serif}
html,body,#admin-wrapper{height:100%}
body{background:var(--bg);color:#07121a;overflow-y:auto;font-size:15px}

/* layout */
#admin-wrapper{display:flex;min-height:100vh;transition:all .25s ease}
.sidebar{
  width:var(--sidebar-width);
  background:linear-gradient(180deg,var(--accent),var(--accent-2));
  color:#fff;
  flex-shrink:0;
  display:flex;
  flex-direction:column;
  position:fixed;top:0;left:0;bottom:0;overflow-y:auto;
  padding-bottom:24px;
  box-shadow: 2px 0 12px rgba(2,12,8,0.08);
  z-index:1200;
}

/* sidebar header */
.sidebar .brand{
  display:flex;align-items:center;gap:12px;padding:18px;border-bottom:1px solid rgba(255,255,255,0.06);
}
.sidebar .brand img.logo{
  width:44px;height:44px;border-radius:8px;object-fit:cover;border:2px solid rgba(255,255,255,0.06);
}
.sidebar .brand h1{font-size:1rem;font-weight:700;letter-spacing:0.2px}

/* admin mini profile inside sidebar */
.sidebar .admin-mini{
  display:flex;align-items:center;gap:12px;padding:14px 16px;border-radius:10px;margin:14px;
  background:rgba(0,0,0,0.06);
}
.sidebar .admin-mini img{
  width:56px;height:56px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.08)
}
.sidebar .admin-mini .info{display:flex;flex-direction:column;overflow:hidden}
.sidebar .admin-mini .info .name{font-weight:700;font-size:0.95rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sidebar .admin-mini .info .email{font-size:0.85rem;color:rgba(255,255,255,0.85);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

/* nav */
.sidebar nav{margin-top:6px;flex:1}
.sidebar ul{list-style:none;padding:8px}
.sidebar li{
  display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:10px;margin:6px 8px;cursor:pointer;transition:all .15s;
  color:rgba(255,255,255,0.95);
}
.sidebar li:hover{transform:translateY(-2px);background:rgba(0,0,0,0.06)}
.sidebar li.active{background:rgba(0,0,0,0.12);box-shadow:var(--shadow)}
.sidebar li i.icon{width:20px;text-align:center;font-size:16px}
.sidebar li a{color:inherit;text-decoration:none;display:flex;align-items:center;width:100%}

/* logout */
.sidebar .logout{margin:12px;background:rgba(0,0,0,0.06);padding:10px;border-radius:10px;display:flex;align-items:center;gap:10px;margin:10px 12px}
.sidebar .logout a{color:#fff;text-decoration:none;font-weight:700}

/* header */
.header{
  height:var(--header-height);
  background:#fff;
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:0 20px;
  margin-left:var(--sidebar-width);
  border-bottom:1px solid #eee;
  position:fixed;top:0;right:0;left:var(--sidebar-width);
  z-index:1100;
  gap:12px;
}
.header .left{display:flex;align-items:center;gap:12px}
#sidebar-toggle{
  display:none;border:0;background:var(--accent);color:#fff;padding:8px;border-radius:8px;cursor:pointer;font-size:16px;
}
.header .title{font-weight:700;font-size:1.05rem;color:#072014}
.header .profile{display:flex;align-items:center;gap:12px;font-weight:600}
.header .profile img{width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #e8f8ec}
.header .profile button{background:transparent;border:0;cursor:pointer;font-weight:700}

/* content wrapper */
#content-wrapper{margin-left:var(--sidebar-width);margin-top:var(--header-height);padding:22px;flex:1;transition:all .25s ease;min-height:calc(100vh - var(--header-height))}
.card{background:var(--card);padding:18px;border-radius:var(--radius);box-shadow:var(--shadow)}

/* utilities & grid */
.admin-section-root{display:flex;flex-direction:column;gap:18px}
.header-row{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}
.h1{font-size:1.25rem;font-weight:700;color:#0b2d1a}
.small{font-size:.9rem;color:var(--muted)}
.btn{background:var(--accent);color:#fff;border:none;padding:8px 14px;border-radius:10px;cursor:pointer;font-weight:700}
.btn.ghost{background:transparent;border:1px solid var(--accent);color:var(--accent)}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;margin-top:10px}

/* card entity */
.card-entity{background:#fff;border-radius:10px;padding:12px;display:flex;flex-direction:column;gap:8px;box-shadow:0 6px 18px rgba(0,0,0,.06)}

/* modal */
.modal-backdrop{position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,.45);z-index:1400;padding:12px}
.modal-backdrop.open{display:flex}
.modal{width:100%;max-width:920px;background:#fff;border-radius:12px;padding:18px;box-shadow:0 18px 60px rgba(0,0,0,.18);max-height:90vh;overflow:auto;position:relative}
.modal .close-x{position:absolute;right:12px;top:12px;border:0;background:transparent;font-size:18px;cursor:pointer}

/* responsive */
@media (max-width: 1000px){
  .sidebar{transform:translateX(0);left:-320px;transition:all .25s ease}
  .sidebar.open{left:0;box-shadow: 6px 0 24px rgba(0,0,0,0.28)}
  #sidebar-toggle{display:inline-block}
  .header{left:0;margin-left:0}
  #content-wrapper{margin-left:0;margin-top:var(--header-height)}
  /* mobile overlay when sidebar open */
  .mobile-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:1300}
  .mobile-overlay.show{display:block}
}

/* small tweaks */
a.focusable:focus{outline:2px solid rgba(12,106,42,0.25);outline-offset:2px}
</style>
</head>
<body>

<!-- Mobile overlay -->
<div class="mobile-overlay" id="mobileOverlay" tabindex="-1" aria-hidden="true"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar" aria-label="Main navigation">
  <div class="brand" role="banner">
    <img class="logo" src="<?= $basePath ?>/images/FYU-LOGO.jpg" alt="FYU logo">
    <h1>FYU Admin</h1>
  </div>

  <div class="admin-mini" role="region" aria-label="Admin profile">
    <img id="sidebarAdminPhoto" src="<?= $admin_photo ?>" alt="Admin photo" loading="lazy">
    <div class="info">
      <div class="name" title="<?= $admin_name ?>"><?= $admin_name ?></div>
      <div class="email" title="<?= $admin_email ?>"><?= $admin_email ?: 'admin@fyunion.org' ?></div>
    </div>
  </div>

  <nav>
    <ul id="sidebarNav">
      <li class="active" data-load="admin/dashboard_load.php" tabindex="0" role="link"><a href="#"><i class="fas fa-house icon"></i> <span>Dashboard</span></a></li>
      <li data-load="admin/news_load.php" tabindex="0" role="link"><a href="#"><i class="fas fa-newspaper icon"></i> <span>News</span></a></li>
      <li data-load="admin/projects_load.php" tabindex="0" role="link"><a href="#"><i class="fas fa-briefcase icon"></i> <span>Projects</span></a></li>
      <li data-load="admin/events_load.php" tabindex="0" role="link"><a href="#"><i class="fas fa-calendar-alt icon"></i> <span>Events</span></a></li>
      <li data-load="admin/members_load.php" tabindex="0" role="link"><a href="#"><i class="fas fa-users icon"></i> <span>Members</span></a></li>
      <li data-load="admin/notifications_load.php" tabindex="0" role="link"><a href="#"><i class="fas fa-bell icon"></i> <span>Announcements</span></a></li>
      <li data-load="admin/admin_actions.php" tabindex="0" role="link"><a href="#"><i class="fas fa-cogs icon"></i> <span>Settings</span></a></li>
    </ul>
  </nav>

  <div class="logout" role="group" aria-label="Logout">
    <!-- logout should call a server-side logout script; keep it a link for graceful fallback -->
    <a href="<?= $basePath ?>/auth/logout.php" class="btn-logout focusable" title="Sign out"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>
</aside>

<!-- SIDEBAR TOGGLE (mobile) -->
<button id="sidebar-toggle" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation"><i class="fas fa-bars"></i></button>

<!-- HEADER -->
<header class="header" role="banner">
  <div class="left">
    <button id="globalSidebarToggle" aria-controls="sidebar" aria-expanded="false" aria-label="Open navigation" style="display:none"><i class="fas fa-bars"></i></button>
    <div class="title">Admin Dashboard</div>
  </div>

  <div class="profile" role="region" aria-label="Profile actions">
    <span class="small" style="color:#3b513f"><?= $admin_name ?></span>
    <button id="profileBtn" class="focusable" title="Open profile settings" aria-haspopup="true">
      <img id="headerAdminPhoto" src="<?= $admin_photo ?>" alt="Admin photo">
    </button>
  </div>
</header>

<!-- MAIN -->
<div id="admin-wrapper" role="main">
  <main id="content-wrapper" tabindex="-1">
    <div id="dynamic-content">
      <div class="card">
        <h2>Welcome, <?= $admin_name ?>!</h2>
        <p>Select a section from the sidebar to manage content.</p>
      </div>
    </div>
  </main>
</div>

<!-- GLOBAL MODAL (used for create/edit) -->
<div class="modal-backdrop" id="globalModal" aria-hidden="true">
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <button class="close-x" aria-label="Close" onclick="closeModal()">&times;</button>
    <h3 id="modalTitle">Modal</h3>

    <form id="globalForm" enctype="multipart/form-data">
      <input type="hidden" name="entity" value="">
      <input type="hidden" name="action" value="">
      <input type="hidden" name="id" value="">

      <div id="modalBody">
        <!-- fields injected dynamically -->
        <div class="form-row">
          <div class="field">
            <label>Title</label>
            <input type="text" name="title" required>
          </div>
          <div class="field">
            <label>Subheading / Short</label>
            <input type="text" name="subheading">
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label>Description</label>
            <textarea name="description" required></textarea>
          </div>
        </div>

        <div class="form-row" id="datesRow">
          <div class="field">
            <label>Status</label>
            <select name="status">
              <option value="New">New</option>
              <option value="Current">Current</option>
              <option value="Finished">Finished</option>
            </select>
          </div>
          <div class="field">
            <label>Start Date</label>
            <input type="date" name="start_date">
          </div>
          <div class="field">
            <label>End Date</label>
            <input type="date" name="end_date">
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label>Feature Image (optional)</label>
            <input type="file" name="image" accept="image/*">
          </div>
        </div>
      </div>

      <div class="form-actions" style="margin-top:12px;display:flex;justify-content:flex-end;gap:10px">
        <button type="button" class="btn ghost" onclick="closeModal()">Cancel</button>
        <button type="submit" class="btn" id="modalSaveBtn">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
/* ====== Config ====== */
const ADMIN_API = '<?= $basePath ?>/admin/admin_actions.php';
const BASE = '<?= $basePath ?>';

// Exposed admin info (read-only)
const ADMIN = {
  name: <?= json_encode($admin_name) ?>,
  email: <?= json_encode($admin_email) ?>,
  photo: <?= json_encode($admin_photo) ?>
};

/* ====== UI elements ====== */
const sidebar = document.getElementById('sidebar');
const sidebarNav = document.getElementById('sidebarNav');
const dynamicContent = document.getElementById('dynamic-content');
const sidebarToggle = document.getElementById('sidebar-toggle');
const mobileOverlay = document.getElementById('mobileOverlay');
const globalModal = document.getElementById('globalModal');
const globalForm = document.getElementById('globalForm');
const modalTitle = document.getElementById('modalTitle');
const datesRow = document.getElementById('datesRow');

/* ====== Accessibility helpers ====== */
function trapFocus(el) {
  const focusable = el.querySelectorAll('a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])');
  if(!focusable.length) return;
  const first = focusable[0], last = focusable[focusable.length-1];
  function handler(e){ if(e.key === 'Tab'){ if(e.shiftKey && document.activeElement === first){ e.preventDefault(); last.focus(); } else if(!e.shiftKey && document.activeElement === last){ e.preventDefault(); first.focus(); } } if(e.key === 'Escape'){ closeModal(); } }
  el.addEventListener('keydown', handler);
  // return a cleanup function
  return ()=> el.removeEventListener('keydown', handler);
}

/* ====== Sidebar behavior ====== */
// Open/close on small screens
function openSidebar(){
  sidebar.classList.add('open');
  mobileOverlay.classList.add('show');
  document.getElementById('sidebar-toggle').setAttribute('aria-expanded','true');
}
function closeSidebar(){
  sidebar.classList.remove('open');
  mobileOverlay.classList.remove('show');
  document.getElementById('sidebar-toggle').setAttribute('aria-expanded','false');
}
sidebarToggle.addEventListener('click', ()=>{
  if(sidebar.classList.contains('open')) closeSidebar(); else openSidebar();
});
mobileOverlay.addEventListener('click', closeSidebar);

// Keyboard navigation on sidebar items (Enter/Space)
sidebarNav.addEventListener('keydown', (e)=>{
  const li = e.target.closest('li');
  if(!li) return;
  if(e.key === 'Enter' || e.key === ' ') { e.preventDefault(); li.click(); }
});

// Load content when clicking nav items
sidebarNav.querySelectorAll('li').forEach(li=>{
  li.addEventListener('click', async ()=>{
    // highlight
    sidebarNav.querySelectorAll('li').forEach(l=>l.classList.remove('active'));
    li.classList.add('active');

    const page = li.getAttribute('data-load');
    if(!page) return;

    dynamicContent.innerHTML = '<div class="card"><p>Loading...</p></div>';
    try{
      const res = await fetch(BASE + '/' + page, {cache: 'no-store'});
      if(!res.ok) throw new Error('Failed to fetch section');
      const html = await res.text();
      dynamicContent.innerHTML = html;
      normalizeLoadedDOM();
      attachBindings(); // setup delegated handlers inside loaded fragment
    } catch (err) {
      console.error(err);
      dynamicContent.innerHTML = '<div class="card"><p>Error loading section.</p></div>';
    }
    // auto-close on mobile
    if(window.innerWidth < 1000) closeSidebar();
  });
});

// Auto-load default active
window.addEventListener('DOMContentLoaded', ()=>{
  const defaultLink = sidebar.querySelector('li.active');
  if(defaultLink) defaultLink.click();
  // populate header/profile images
  const headerPhoto = document.getElementById('headerAdminPhoto');
  const sidebarPhoto = document.getElementById('sidebarAdminPhoto');
  if(headerPhoto) headerPhoto.src = ADMIN.photo;
  if(sidebarPhoto) sidebarPhoto.src = ADMIN.photo;
});

/* ====== Modal utilities ====== */
let modalCleanup = null;
function openModal(entity, mode='create', data = {}) {
  globalForm.reset();
  // set hidden fields
  globalForm.querySelector('[name="entity"]').value = entity;
  globalForm.querySelector('[name="action"]').value = (mode==='create' ? 'create' : (mode==='update' ? 'update' : mode));
  globalForm.querySelector('[name="id"]').value = data.id || '';

  // populate fields if provided (safe)
  try { if(globalForm.title) globalForm.title.value = data.title || ''; }catch(e){}
  try { if(globalForm.subheading) globalForm.subheading.value = data.subheading || ''; }catch(e){}
  try { if(globalForm.description) globalForm.description.value = data.description || ''; }catch(e){}
  if(globalForm.status) globalForm.status.value = data.status || 'New';
  if(globalForm.start_date) globalForm.start_date.value = data.start_date || '';
  if(globalForm.end_date) globalForm.end_date.value = data.end_date || '';

  // entity-based UI
  if(entity === 'news') {
    datesRow.style.display = 'none';
  } else {
    datesRow.style.display = 'flex';
  }

  modalTitle.textContent = (mode === 'create' ? 'New ' : 'Edit ') + (entity.charAt(0).toUpperCase() + entity.slice(1));
  globalModal.classList.add('open');
  globalModal.setAttribute('aria-hidden','false');
  modalCleanup = trapFocus(globalModal);
}

function closeModal(){
  globalModal.classList.remove('open');
  globalModal.setAttribute('aria-hidden','true');
  if(typeof modalCleanup === 'function') modalCleanup();
}

/* ====== CRUD helpers ====== */
async function postFormData(fd, reloadOnSuccess = false){
  try{
    const res = await fetch(ADMIN_API, { method: 'POST', body: fd });
    const data = await res.json();
    if(data.success){
      alert(data.message || 'Success');
      if(reloadOnSuccess) reloadCurrentSection();
      return data;
    } else {
      alert('Error: ' + (data.message || 'Unknown'));
      return data;
    }
  }catch(err){
    console.error(err);
    alert('Network error');
    return { success:false };
  }
}

async function fetchItem(entity, id){
  const fd = new FormData();
  fd.append('action', 'get');
  fd.append('entity', entity);
  fd.append('id', id);
  try{
    const res = await fetch(ADMIN_API, { method: 'POST', body: fd });
    return await res.json();
  } catch(err){
    console.error(err);
    return { success:false, message: 'Network error' };
  }
}

function reloadCurrentSection(){
  const active = sidebar.querySelector('li.active');
  if(active) active.click();
}

/* Global form submit */
globalForm.addEventListener('submit', async function(e){
  e.preventDefault();
  const fd = new FormData(globalForm);
  const result = await postFormData(fd, true);
  if(result.success) closeModal();
});

/* ====== Delegation & normalization for loaded fragments ====== */
function normalizeLoadedDOM(){
  const c = dynamicContent;
  if(!c) return;
  // normalize edit/archive/delete/new buttons to data-action/data-id/data-entity
  c.querySelectorAll('.editBtn, .edit-btn, button.edit').forEach(b=>{
    if(!b.dataset.action) b.dataset.action = 'edit';
    if(!b.dataset.id) b.dataset.id = b.getAttribute('data-id') || b.closest('[data-id]')?.dataset.id || '';
  });
  c.querySelectorAll('.archiveBtn, .archive-btn, button.archive').forEach(b=>{
    if(!b.dataset.action) b.dataset.action = 'archive';
    if(!b.dataset.id) b.dataset.id = b.getAttribute('data-id') || b.closest('[data-id]')?.dataset.id || '';
  });
  c.querySelectorAll('.deleteBtn, .delete-btn, button.delete').forEach(b=>{
    if(!b.dataset.action) b.dataset.action = 'delete';
    if(!b.dataset.id) b.dataset.id = b.getAttribute('data-id') || b.closest('[data-id]')?.dataset.id || '';
  });
  c.querySelectorAll('[id^="btnAdd"], .btnAdd, .new-btn').forEach(b=>{
    if(!b.dataset.action) b.dataset.action = 'create';
    if(!b.dataset.entity) b.dataset.entity = inferEntityFromLoaded();
  });
}

// Single delegated listener (ensures only one listener)
if(!dynamicContent._hasDelegation){
  dynamicContent.addEventListener('click', async function(e){
    const btn = e.target.closest('button, a, [data-action]');
    if(!btn) return;
    const action = btn.dataset.action || null;
    const id = btn.dataset.id || btn.getAttribute('data-id') || null;
    const entity = btn.dataset.entity || inferEntityFromLoaded();

    if(!action) return;

    if(action === 'edit' && id){
      const fetchResp = await fetchItem(entity, id);
      if(!fetchResp.success){ alert(fetchResp.message || 'Failed to fetch'); return; }
      let payloadKey = 'post';
      if(entity === 'projects') payloadKey = 'project';
      if(entity === 'events') payloadKey = 'event';
      const data = fetchResp[payloadKey] || {};
      openModal(entity, 'update', data);
      return;
    }

    if(action === 'create'){
      openModal(entity || inferEntityFromLoaded(), 'create', {});
      return;
    }

    if(action === 'archive' && id){
      if(!confirm('Archive this item?')) return;
      const fd = new FormData();
      fd.append('entity', entity);
      fd.append('action', 'update');
      fd.append('id', id);
      fd.append('status', 'Finished');
      await postFormData(fd, true);
      return;
    }

    if(action === 'delete' && id){
      if(!confirm('Delete this item?')) return;
      const fd = new FormData();
      fd.append('entity', entity);
      fd.append('action', 'delete');
      fd.append('id', id);
      await postFormData(fd, true);
      return;
    }

  });
  dynamicContent._hasDelegation = true;
}

/* infer entity helper */
function inferEntityFromLoaded(){
  const active = document.querySelector('.sidebar li.active');
  if(!active) return null;
  const page = active.getAttribute('data-load') || '';
  if(page.includes('news')) return 'news';
  if(page.includes('projects')) return 'projects';
  if(page.includes('events')) return 'events';
  if(page.includes('members')) return 'members';
  if(page.includes('notifications') || page.includes('notifications_load')) return 'announcements';
  if(page.includes('admin_actions')) return 'settings';
  return null;
}

/* attachBindings placeholder for fragment-specific JS (called after fragment load) */
function attachBindings(){
  // fragments can place inline scripts that rely on normalizeLoadedDOM + delegation
  // also attach settings forms present inside loaded content
  const content = dynamicContent;
  const updateProfileForm = content.querySelector('#updateProfileForm');
  const changePasswordForm = content.querySelector('#changePasswordForm');
  const updateSiteForm = content.querySelector('#updateSiteForm');

  if(updateProfileForm){
    updateProfileForm.addEventListener('submit', async e=>{
      e.preventDefault();
      const fd = new FormData(updateProfileForm);
      fd.append('action','update_profile');
      const r = await postFormData(fd, false);
      // If photo updated and server returns new photo URL, update header/sidebar
      if(r.success && r.photo) {
        document.getElementById('headerAdminPhoto').src = r.photo;
        document.getElementById('sidebarAdminPhoto').src = r.photo;
      }
    });
  }
  if(changePasswordForm){
    changePasswordForm.addEventListener('submit', async e=>{
      e.preventDefault();
      const fd = new FormData(changePasswordForm);
      fd.append('action','update_password');
      await postFormData(fd, false);
    });
  }
  if(updateSiteForm){
    updateSiteForm.addEventListener('submit', async e=>{
      e.preventDefault();
      const fd = new FormData(updateSiteForm);
      fd.append('action','update_site');
      await postFormData(fd, false);
    });
  }
}

/* small improvements: close modal on backdrop click */
globalModal.addEventListener('click', (e)=>{
  if(e.target === globalModal) closeModal();
});
document.addEventListener('keydown', (e)=>{
  if(e.key === 'Escape' && globalModal.classList.contains('open')) closeModal();
});
</script>

</body>
</html>
