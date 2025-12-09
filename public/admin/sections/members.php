<?php
// NEW REFACTORED members.php (Improved Filtering, Gender Tabs, Correct Photo Fetching)
// ======================================================================
// 1. DATABASE + PRE-OUTPUT LOGIC
// ======================================================================

include_once __DIR__ . "/../../../app/config/db.php";

// Base path for member photos relative to domain root
$photo_base_path = '/FangakYouthUnion/uploads/';

// Helper
function get_filter(string $key): string {
    return trim($_GET[$key] ?? '');
}

$error_message = null;

// STATUS UPDATE ACTION
if (isset($_GET['action'], $_GET['member_id']) && in_array($_GET['action'], ['approve', 'reject'])) {

    $member_id = filter_var($_GET['member_id'], FILTER_VALIDATE_INT);
    $new_status = ($_GET['action'] === 'approve') ? 'active' : 'rejected';

    if ($member_id) {
        try {
            $update_stmt = $pdo->prepare("UPDATE members SET status = ? WHERE id = ?");
            $update_stmt->execute([$new_status, $member_id]);

        } catch (PDOException $e) {
            $error_message = "Database error: Could not update member status.";
        }
    }
}

// ======================================================================
// 2. LAYOUT INCLUDE
// ======================================================================
include __DIR__ . "/../includes/head.php";

// ======================================================================
// 3. FILTERS
// ======================================================================
$where = [];
$params = [];

// gender
$gender_filter = get_filter('gender');
if ($gender_filter !== '') {
    $where[] = "gender = ?";
    $params[] = $gender_filter;
}

// payam
$payam_filter = get_filter('payam');
if ($payam_filter !== '') {
    $where[] = "payam LIKE ?";
    $params[] = "%$payam_filter%";
}

// age group
$age_group_filter = get_filter('age_group');
if ($age_group_filter === 'under20') {
    $where[] = "age < 20";
}
if ($age_group_filter === '20to30') {
    $where[] = "age BETWEEN 20 AND 30";
}
if ($age_group_filter === 'over30') {
    $where[] = "age > 30";
}

// status
$status_filter = get_filter('status');
if ($status_filter !== '') {
    $where[] = "status = ?";
    $params[] = $status_filter;
}

// QUERY
$sql = "SELECT * FROM members";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// STATS
$total_members = $pdo->query("SELECT COUNT(id) FROM members")->fetchColumn();
$active_members = $pdo->query("SELECT COUNT(id) FROM members WHERE status = 'active'")->fetchColumn();
$pending_members = $pdo->query("SELECT COUNT(id) FROM members WHERE status = 'pending'")->fetchColumn();
$male_members = $pdo->query("SELECT COUNT(id) FROM members WHERE gender = 'Male'")->fetchColumn();
$female_members = $pdo->query("SELECT COUNT(id) FROM members WHERE gender = 'Female'")->fetchColumn();
$other_gender = $pdo->query("SELECT COUNT(id) FROM members WHERE gender NOT IN ('Male','Female') OR gender IS NULL")->fetchColumn();
?>

<!-- UI START -->
<div class="p-6">
    <h1 class="text-3xl font-extrabold text-green-800 mb-2">👥 FYU Member Dashboard</h1>
    <p class="text-gray-500 mb-8">Manage and filter all registered members.</p>

    <?php if ($error_message): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= $error_message ?>
        </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-5 rounded-xl shadow-lg border-l-4 border-green-500">
            <p class="text-sm text-gray-500">Total Members</p>
            <p class="text-3xl font-bold mt-1"><?= number_format($total_members) ?></p>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-lg border-l-4 border-blue-500">
            <p class="text-sm text-gray-500">Active</p>
            <p class="text-3xl font-bold mt-1"><?= number_format($active_members) ?></p>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-lg border-l-4 border-yellow-500">
            <p class="text-sm text-gray-500">Pending</p>
            <p class="text-3xl font-bold mt-1"><?= number_format($pending_members) ?></p>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-lg border-l-4 border-indigo-500">
            <p class="text-sm text-gray-500">Gender Breakdown</p>
            <p class="text-sm mt-1">👦 Male: <b><?= $male_members ?></b></p>
            <p class="text-sm">👧 Female: <b><?= $female_members ?></b></p>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 mb-8 bg-white p-5 rounded-xl shadow">

        <select name="gender" class="border p-3 rounded">
            <option value="">All Genders</option>
            <option value="Male" <?= $gender_filter === 'Male' ? 'selected' : '' ?>>Male (<?= $male_members ?>)</option>
            <option value="Female" <?= $gender_filter === 'Female' ? 'selected' : '' ?>>Female (<?= $female_members ?>)</option>
        </select>

        <select name="age_group" class="border p-3 rounded">
            <option value="">All Ages</option>
            <option value="under20" <?= $age_group_filter === 'under20' ? 'selected' : '' ?>>Under 20</option>
            <option value="20to30" <?= $age_group_filter === '20to30' ? 'selected' : '' ?>>20–30</option>
            <option value="over30" <?= $age_group_filter === 'over30' ? 'selected' : '' ?>>Over 30</option>
        </select>

        <select name="status" class="border p-3 rounded">
            <option value="">All Status</option>
            <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="rejected" <?= $status_filter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
        </select>

        <input type="text" name="payam" placeholder="Search Payam..." value="<?= htmlspecialchars($payam_filter) ?>" class="border p-3 rounded">

        <button class="bg-green-600 text-white font-semibold rounded p-3">Filter</button>
        <a href="members.php" class="text-center bg-gray-200 p-3 rounded hover:bg-gray-300">Reset</a>
    </form>

    <!-- Table -->
    <div class="bg-white shadow rounded-xl overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">ID</th>
                    <th class="p-3 text-left">Photo</th>
                    <th class="p-3 text-left">Full Name</th>
                    <th class="p-3 text-left">Gender/Age</th>
                    <th class="p-3 text-left">Contact</th>
                    <th class="p-3 text-left">Location</th>
                    <th class="p-3 text-center">Status</th>
                    <th class="p-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$members): ?>
                    <tr><td colspan="8" class="p-6 text-center text-gray-500">No members found.</td></tr>
                <?php endif; ?>

                <?php foreach ($members as $m): ?>
                    <?php
                    $photo_url = $m['photo'] ? $photo_base_path . $m['photo'] : null;
                    ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">#<?= $m['id'] ?></td>

                        <td class="p-3">
                            <?php if ($photo_url): ?>
                                <img src="<?= $photo_url ?>" class="h-12 w-12 rounded-full object-cover">
                            <?php else: ?>
                                <div class="h-12 w-12 bg-gray-300 rounded-full flex items-center justify-center text-gray-700">N/A</div>
                            <?php endif; ?>
                        </td>

                        <td class="p-3 font-medium"><?= htmlspecialchars($m['full_name']) ?></td>
                        <td class="p-3"><?= $m['gender'] ?> (<?= $m['age'] ?>)</td>

                        <td class="p-3">
                            <?= $m['email'] ?><br>
                            <span class="text-xs text-gray-500"><?= $m['phone'] ?></span>
                        </td>

                        <td class="p-3"><?= $m['payam'] ?></td>

                        <td class="p-3 text-center">
                            <span class="px-2 py-1 text-xs rounded-full <?=
                                $m['status']==='active'?'bg-green-100 text-green-700':
                                ($m['status']==='pending'?'bg-yellow-100 text-yellow-700':'bg-red-100 text-red-700')
                            ?>"><?= ucfirst($m['status']) ?></span>
                        </td>

                        <td class="p-3 text-center">
                            <?php if ($m['status']==='pending'): ?>
                                <a href="?action=approve&member_id=<?= $m['id'] ?>" class="text-green-600 font-bold mr-2">Approve</a>
                                <a href="?action=reject&member_id=<?= $m['id'] ?>" class="text-red-600 font-bold">Reject</a>
                            <?php else: ?>
                                <button class="text-blue-600 mr-3" onclick="openMemberModal('edit', <?= $m['id'] ?>)">Edit</button>
                                <button class="text-red-600" onclick="deleteMember(<?= $m['id'] ?>)">Delete</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . "/../includes/footer.php"; ?>
