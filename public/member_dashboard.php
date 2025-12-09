<?php
session_start();
require_once __DIR__ . "/../app/config/db.php";

// --- Security Check ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

$member_id = $_SESSION['user_id'];
if(!isset($baseUrl)) $baseUrl = '/public/'; 

// --- Fetch Member Info ---
$stmt = $conn->prepare("SELECT * FROM members WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Member data not found. Contact support.");
}

$member = $result->fetch_assoc();

// --- Prepare "Safe" Variables for Display ---
$full_name       = htmlspecialchars($member['full_name'] ?? 'N/A');
$email           = htmlspecialchars($member['email'] ?? 'N/A');
$phone           = htmlspecialchars($member['phone'] ?? '');
$age             = htmlspecialchars($member['age'] ?? '');
$payam           = htmlspecialchars($member['payam'] ?? 'N/A');
$education_level = htmlspecialchars($member['education_level'] ?? 'N/A');
$course          = htmlspecialchars($member['course'] ?? '');
$year_or_done    = htmlspecialchars($member['year_or_done'] ?? '');
$status          = htmlspecialchars(ucfirst($member['status'] ?? 'N/A'));
$photo_path      = htmlspecialchars($member['photo'] ?? $baseUrl . 'images/default-avatar.png');
$joined_date     = $member['created_at'] ? date("M j, Y", strtotime($member['created_at'])) : 'N/A';
$joined_datetime = htmlspecialchars($member['created_at'] ?? '');

// --- NEW: Handle Active Tab from URL ---
// This allows update_profile.php to redirect back to the correct tab
$valid_tabs = ['dashboard', 'profile', 'notifications', 'events', 'resources', 'messages'];
$active_tab = 'dashboard'; // Default
if (isset($_GET['tab']) && in_array($_GET['tab'], $valid_tabs)) {
    $active_tab = $_GET['tab'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard | FYU</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Old+Standard+TT:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

    <style>
        /* --- Root Variables --- */
        :root {
            --primary: #1f7a4b;
            --accent: #3aa76a;
            --muted: #6b7280;
            --bg-soft: #f6fbf8;
            --bg-white: #ffffff;
            --border-color: #eef2f0;
            --card-radius: 16px;
            --font-serif: 'Old Standard TT', serif;
            --font-sans: 'Poppins', sans-serif;
            --shadow-soft: 0 10px 36px rgba(12, 20, 16, 0.04);
            --shadow-hover: 0 18px 40px rgba(12, 20, 16, 0.08);
            
            /* NEW: Alert colors */
            --alert-success-bg: #e6f7ec;
            --alert-success-text: #0f5132;
            --alert-success-border: #b7e0c7;
            --alert-error-bg: #f8d7da;
            --alert-error-text: #58151c;
            --alert-error-border: #f1aeb5;
        }

        /* --- Base Styles --- */
        * { box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            font-family: var(--font-sans);
            color: #132226;
            background: var(--bg-soft);
            margin: 0;
            line-height: 1.6;
            display: flex;
            overflow: hidden;
        }
        a { text-decoration: none; color: var(--primary); }

        /* --- Dashboard Layout --- */
        .dash-sidebar {
            width: 260px;
            background: var(--bg-white);
            border-right: 1px solid var(--border-color);
            padding: 24px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            height: 100vh;
            overflow-y: auto;
        }
        .dash-main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .dash-header {
            background: var(--bg-white);
            color: var(--primary);
            padding: 18px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            flex-shrink: 0;
        }
        .dash-header h1 {
            font-family: var(--font-serif);
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
        }
        .logout-btn {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 600;
            transition: all .25s;
            background: transparent;
            border: 1px solid rgba(0,0,0,0.1);
            color: var(--muted);
            font-size: 0.9rem;
        }
        .logout-btn:hover {
            background: var(--bg-soft);
            color: var(--primary);
            border-color: var(--primary);
        }
        .logout-btn i { margin-right: 6px; }
        .dash-main {
            flex: 1;
            padding: 32px;
            overflow-y: auto;
        }

        /* --- Sidebar --- */
        .sidebar-profile {
            text-align: center;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 24px;
        }
        .sidebar-profile img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 12px;
            border: 4px solid var(--bg-soft);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .sidebar-profile h4 {
            margin: 0;
            font-weight: 600;
            color: #111;
            font-size: 1.1rem;
            line-height: 1.3;
        }
        .sidebar-profile p {
            margin: 2px 0 0;
            font-size: 0.85rem;
            color: var(--muted);
            word-break: break-all;
        }
        .dash-sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 18px;
            border-radius: 10px;
            text-decoration: none;
            color: var(--muted);
            font-weight: 600;
            transition: all .25s ease;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
            font-size: 1rem;
            cursor: pointer;
            font-family: var(--font-sans);
        }
        .nav-item i {
            width: 20px;
            text-align: center;
            font-size: 0.95rem;
        }
        .nav-item:hover {
            background: var(--bg-soft);
            color: var(--primary);
        }
        .nav-item.active {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 8px 20px rgba(31, 122, 75, 0.2);
        }
        .sidebar-footer {
            margin-top: auto;
            padding-top: 24px;
        }
        .sidebar-footer .nav-item { color: #c94a4a; }
        .sidebar-footer .nav-item:hover { background: #fdf2f2; color: #b91c1c; }

        /* --- Page Content Styles --- */
        .section-heading {
            font-family: var(--font-serif);
            color: var(--primary);
            font-size: 2.4rem;
            font-weight: 700;
            margin-bottom: 12px;
        }
        .section-subheading {
            font-size: 1.1rem;
            color: var(--muted);
            max-width: 700px;
            margin: 0 0 32px;
            line-height: 1.7;
        }

        /* --- NEW: Alert Boxes --- */
        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-weight: 600;
            border: 1px solid transparent;
        }
        .alert.success {
            background: var(--alert-success-bg); 
            color: var(--alert-success-text); 
            border-color: var(--alert-success-border);
        }
        .alert.error {
            background: var(--alert-error-bg); 
            color: var(--alert-error-text); 
            border-color: var(--alert-error-border);
        }

        /* --- Widgets --- */
        .widget-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }
        .widget {
            background: var(--bg-white);
            border-radius: var(--card-radius);
            padding: 24px;
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: flex-start;
            gap: 18px;
            transition: transform .3s, box-shadow .3s;
        }
        .widget:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-hover);
        }
        .widget .icon {
            font-size: 1.8rem;
            color: #fff;
            background: var(--accent);
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }
        .widget .icon.purple { background: #8B5CF6; }
        .widget .icon.blue { background: #3B82F6; }
        .widget .icon.orange { background: #F59E0B; }
        .widget .content h3 {
            margin: 0;
            font-size: 0.95rem;
            color: var(--muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .widget .content p {
            margin: 4px 0 0;
            font-size: 1.8rem;
            font-weight: 700;
            color: #111;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* --- Content Card (for Profile/Events) --- */
        .content-card {
            background: var(--bg-white);
            border-radius: var(--card-radius);
            padding: 28px 32px;
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--border-color);
            margin-top: 24px;
        }

        /* --- Form Styles --- */
        .profile-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0 24px;
        }
        @media(min-width: 900px) {
            .profile-grid {
                grid-template-columns: 3fr 1fr;
            }
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="tel"],
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-family: var(--font-sans);
            font-size: 1rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-group input:focus, 
        .form-group select:focus, 
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(31, 122, 75, 0.15);
        }
        .form-group input[readonly] {
            background:#f4f4f4; 
            cursor: not-allowed;
            color: var(--muted);
        }
        .form-grid-cols-2 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0 20px;
        }
        @media(min-width: 640px) {
            .form-grid-cols-2 {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        .profile-photo-group { text-align: center; }
        .profile-photo-group label { text-align: left; }
        .profile-photo-group img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 10px auto 16px;
            border: 6px solid var(--bg-white);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .form-group input[type="file"] {
            font-size: 0.9rem;
            padding: 10px 14px;
        }

        /* --- Button style --- */
        .button { 
            display: inline-block; 
            padding: 12px 26px; 
            border-radius: 999px; 
            text-decoration: none; 
            font-weight: 700; 
            transition: all .25s; 
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        .button.primary { 
            background: var(--primary); 
            color: #fff; 
        }
        .button.primary:hover { 
            background: var(--accent); 
            transform: translateY(-3px); 
            box-shadow: 0 8px 20px rgba(31, 122, 75, 0.2);
        }

        /* --- Tab Logic & Animation --- */
        .tab-content { 
            display: none;
            opacity: 0;
            transition: opacity .3s ease-in-out;
        }
        .tab-content.active { 
            display: block;
            opacity: 1;
        }

        /* --- NEW: Empty State --- */
        .empty-state {
            color: var(--muted);
            text-align: center;
            padding: 40px;
            background: rgba(255,255,255,0.5);
            border-radius: var(--card-radius);
            border: 1px dashed var(--border-color);
        }
        .empty-state i {
            font-size: 2.5rem;
            display: block;
            margin-bottom: 16px;
            opacity: 0.7;
        }
        .empty-state h3 {
            font-family: var(--font-serif);
            font-size: 1.5rem;
            color: #333;
            margin: 0 0 8px;
        }

        /* --- NEW: Resource List --- */
        .resource-list {
            list-style: none; 
            padding-left: 0; 
            display: flex; 
            flex-direction: column; 
            gap: 12px;
        }
        .resource-item {
            display: block;
            border: 1px solid var(--border-color);
            padding: 16px 20px;
            border-radius: 12px;
            transition: all .25s ease;
        }
        .resource-item:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
            border-color: var(--accent);
        }
        .resource-item a {
            font-weight: 600; 
            font-size: 1.1rem; 
            display: block;
            color: #111;
        }
        .resource-item a i {
            margin-right: 10px; 
            color: var(--primary);
        }
        .resource-item span {
            font-size: 0.9rem; 
            color: var(--muted); 
            padding-left: 28px; 
            display: block; 
            margin-top: 4px;
        }

    </style>
</head>
<body>

    <aside class="dash-sidebar">
        <div class="sidebar-profile">
            <img src="<?= $photo_path ?>" alt="Profile Photo">
            <h4><?= $full_name ?></h4>
            <p><?= $email ?></p>
        </div>
        
        <nav class="dash-sidebar-nav">
            <button class="nav-item <?= ($active_tab == 'dashboard') ? 'active' : '' ?>" data-tab="dashboard">
                <i class="fas fa-home"></i> <span>Dashboard</span>
            </button>
            <button class="nav-item <?= ($active_tab == 'profile') ? 'active' : '' ?>" data-tab="profile">
                <i class="fas fa-user-circle"></i> <span>My Profile</span>
            </button>
            <button class="nav-item <?= ($active_tab == 'notifications') ? 'active' : '' ?>" data-tab="notifications">
                <i class="fas fa-bell"></i> <span>Notifications</span>
            </button>
            <button class="nav-item <?= ($active_tab == 'events') ? 'active' : '' ?>" data-tab="events">
                <i class="fas fa-calendar-alt"></i> <span>Events</span>
            </button>
            <button class="nav-item <?= ($active_tab == 'resources') ? 'active' : '' ?>" data-tab="resources">
                <i class="fas fa-book"></i> <span>Resources</span>
            </button>
            <button class="nav-item <?= ($active_tab == 'messages') ? 'active' : '' ?>" data-tab="messages">
                <i class="fas fa-comments"></i> <span>Messages</span>
            </button>
        </nav>

        <div class="sidebar-footer">
             <a href="../logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
             </a>
        </div>
    </aside>

    <div class="dash-main-wrapper">
    
        <header class="dash-header">
            <h1>Member Portal</h1>
            <a href="../logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </header>

        <main class="dash-main">

            <?php
            // --- NEW: Display Session Alerts ---
            if (isset($_SESSION['success'])) {
                echo '<div class="alert success">' . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']); // Clear message after showing
            }
            if (isset($_SESSION['error'])) {
                echo '<div class="alert error">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']); // Clear message after showing
            }
            ?>

            <div id="dashboard" class="tab-content <?= ($active_tab == 'dashboard') ? 'active' : '' ?>">
                <h2 class="section-heading">Welcome, <?= $full_name ?>!</h2>
                <p class="section-subheading">Here's a summary of your membership and union activities.</p>

                <div class="widget-grid">
                    <div class="widget">
                        <div class="icon"><i class="fas fa-check"></i></div>
                        <div class="content">
                            <h3>Membership Status</h3>
                            <p><?= $status ?></p>
                        </div>
                    </div>
                     <div class="widget">
                        <div class="icon purple"><i class="fas fa-calendar-day"></i></div>
                        <div class="content">
                            <h3>Member Since</h3>
                            <p><?= $joined_date ?></p>
                        </div>
                    </div>
                     <div class="widget">
                        <div class="icon blue"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="content">
                            <h3>Your Payam</h3>
                            <p><?= $payam ?></p>
                        </div>
                    </div>
                     <div class="widget">
                        <div class="icon orange"><i class="fas fa-graduation-cap"></i></div>
                        <div class="content">
                            <h3>Education</h3>
                            <p><?= $education_level ?></p>
                        </div>
                    </div>
                </div>

                <div class="content-card">
                    <h3>Recent Announcements</h3>
                    <p style="color: var(--muted); margin-top: 12px;">
                        Welcome to the new member portal! We are excited to launch this new platform to better connect our members. 
                        Please take a moment to review and update your profile information using the "My Profile" tab.
                    </p>
                </div>
            </div>

            <div id="profile" class="tab-content <?= ($active_tab == 'profile') ? 'active' : '' ?>">
                <h2 class="section-heading">My Profile</h2>
                <p class="section-subheading">Keep your information up to date to stay connected with union activities.
                Contact an admin if you need to change your email address.</p>

                <div class="content-card">
                    <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                        <div class="profile-grid">
                            
                            <div class="form-fields">
                                <div class="form-group">
                                    <label for="full_name">Full Name</label>
                                    <input type="text" id="full_name" name="full_name" value="<?= $full_name ?>" required>
                                </div>
                                
                                <div class="form-grid-cols-2">
                                    <div class="form-group">
                                        <label for="phone">Phone Number</label>
                                        <input type="tel" id="phone" name="phone" value="<?= $phone ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="age">Age</label>
                                        <input type="number" id="age" name="age" value="<?= $age ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="payam">Payam / Location</label>
                                    <input type="text" id="payam" name="payam" value="<?= $payam ?>">
                                </div>

                                <div class="form-grid-cols-2">
                                    <div class="form-group">
                                        <label for="education_level">Education Level</label>
                                        <input type="text" id="education_level" name="education_level" value="<?= $education_level ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="course">Course / Field of Study</label>
                                        <input type="text" id="course" name="course" value="<?= $course ?>">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="year_or_done">Year/Status (e.g., "Year 3" or "Graduated")</label>
                                    <input type="text" id="year_or_done" name="year_or_done" value="<?= $year_or_done ?>">
                                </div>
                            </div>

                            <div class="profile-photo-group">
                                <label>Current Profile Photo</label>
                                <img src="<?= $photo_path ?>" alt="Profile Photo Preview" id="photoPreview">
                                
                                <div class="form-group">
                                    <label for="photo">Update Photo</label>
                                    <input type="file" id="photo" name="photo" accept="image/*" onchange="document.getElementById('photoPreview').src = window.URL.createObjectURL(this.files[0])">
                                </div>

                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" id="email" name="email" value="<?= $email ?>" readonly title="Contact admin to change email.">
                                </div>

                                <div class="form-group">
                                    <label for="joined">Member Since</label>
                                    <input type="text" id="joined" value="<?= $joined_datetime ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <hr style="border:none; border-top: 1px solid var(--border-color); margin: 24px 0;">
                        
                        <button type="submit" class="button primary">
                            <i class="fas fa-save" style="margin-right: 8px;"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
            
            <div id="notifications" class="tab-content <?= ($active_tab == 'notifications') ? 'active' : '' ?>">
                <h2 class="section-heading">Notifications</h2>
                <p class="section-subheading">Updates and alerts from the union.</p>
                 <div class="content-card">
                    <div class="empty-state">
                        <i class="fas fa-bell-slash"></i>
                        <h3>No New Notifications</h3>
                        <p>You're all caught up! Check back later for updates.</p>
                    </div>
                </div>
            </div>
            
            <div id="events" class="tab-content <?= ($active_tab == 'events') ? 'active' : '' ?>">
                <h2 class="section-heading">Upcoming Events</h2>
                <p class="section-subheading">Get involved in union activities.</p>
                 <div class="content-card">
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No Upcoming Events</h3>
                        <p>There are no events scheduled at this time.</p>
                    </div>
                </div>
            </div>

            <div id="resources" class="tab-content <?= ($active_tab == 'resources') ? 'active' : '' ?>">
                <h2 class="section-heading">Member Resources</h2>
                <p class="section-subheading">Important documents and links for members.</p>
                 <div class="content-card">
                    <ul class="resource-list">
                        <li class="resource-item">
                            <a href="#"><i class="fas fa-file-pdf"></i> FYU Constitution</a>
                            <span>The official constitution and bylaws of the union.</span>
                        </li>
                        <li class="resource-item">
                            <a href="#"><i class="fas fa-file-word" style="color: #3B82F6;"></i> Project Proposal Template</a>
                            <span>Use this template to submit new project ideas.</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div id="messages" class="tab-content <?= ($active_tab == 'messages') ? 'active' : '' ?>">
                <h2 class="section-heading">Messages</h2>
                <p class="section-subheading">Direct messages from admins or members.</p>
                 <div class="content-card">
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>No New Messages</h3>
                        <p>This feature is coming soon.</p>
                    </div>
                </div>
            </div>

        </main>
    </div> <script>
document.addEventListener('DOMContentLoaded', () => {
    const navItems = document.querySelectorAll('.nav-item');
    const tabContents = document.querySelectorAll('.tab-content');

    // This JS only handles *clicks* after the page has loaded.
    // The initial active tab is now set by PHP.
    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            // Don't intercept clicks on actual links (like logout)
            if (item.tagName === 'A') {
                return;
            }
            
            e.preventDefault();
            const tabId = item.dataset.tab;
            if (!tabId) return;

            // Handle nav active state
            navItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');

            // Handle content active state
            tabContents.forEach(content => {
                if (content.id === tabId) {
                    content.classList.add('active');
                } else {
                    content.classList.remove('active');
                }
            });

            // Optional: Update URL hash without reloading
            // history.pushState(null, '', '#' + tabId);
        });
    });
});
</script>

</body>
</html>