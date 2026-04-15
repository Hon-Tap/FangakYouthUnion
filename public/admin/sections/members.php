<?php
// NEW REFACTORED members.php (Tab Include)
// ======================================================================

include_once __DIR__ . "/../../../app/config/db.php";

// 1. DYNAMIC URL PRESERVATION (Fixes the dashboard redirect bug)
// This ensures we keep ?page=members (or whatever routing you use) in the URL
$current_params = $_GET;
$base_query = http_build_query(array_diff_key($current_params, ['action' => '', 'member_id' => '', 'success' => '']));
$base_url = '?' . $base_query;

$error_message = null;

// ======================================================================
// 2. ACTION LOGIC (APPROVE / REJECT)
// ======================================================================
if (isset($_GET['action'], $_GET['member_id']) && in_array($_GET['action'], ['approve', 'reject'])) {
    $member_id = filter_var($_GET['member_id'], FILTER_VALIDATE_INT);
    $new_status = ($_GET['action'] === 'approve') ? 'active' : 'rejected';

    if ($member_id) {
        try {
            $update_stmt = $pdo->prepare("UPDATE members SET status = ? WHERE id = ?");
            $update_stmt->execute([$new_status, $member_id]);
            
            // Redirect back to the exact same page/tab without the action parameters
            header("Location: " . $base_url . "&success=1");
            exit;
        } catch (PDOException $e) {
            $error_message = "Database error: Could not update member status.";
        }
    }
}

// ======================================================================
// 3. FILTERS & SORTING
// ======================================================================
$where = [];
$params = [];

// Filters
if (!empty($_GET['gender'])) {
    $where[] = "gender = ?";
    $params[] = $_GET['gender'];
}
if (!empty($_GET['payam'])) {
    $where[] = "payam LIKE ?";
    $params[] = "%" . $_GET['payam'] . "%";
}
if (!empty($_GET['age_group'])) {
    if ($_GET['age_group'] === 'under20') $where[] = "age < 20";
    if ($_GET['age_group'] === '20to30') $where[] = "age BETWEEN 20 AND 30";
    if ($_GET['age_group'] === 'over30') $where[] = "age > 30";
}
if (!empty($_GET['status'])) {
    $where[] = "status = ?";
    $params[] = $_GET['status'];
}

// Sorting logic
$allowed_sorts = ['id', 'full_name', 'age', 'status'];
$sort = in_array($_GET['sort'] ?? '', $allowed_sorts) ? $_GET['sort'] : 'id';
$dir = strtoupper($_GET['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

// QUERY
$sql = "SELECT * FROM members";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY $sort $dir";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// STATS
$total_members = $pdo->query("SELECT COUNT(id) FROM members")->fetchColumn();
$active_members = $pdo->query("SELECT COUNT(id) FROM members WHERE status = 'active'")->fetchColumn();
$pending_members = $pdo->query("SELECT COUNT(id) FROM members WHERE status = 'pending'")->fetchColumn();
$male_members = $pdo->query("SELECT COUNT(id) FROM members WHERE gender = 'Male'")->fetchColumn();
$female_members = $pdo->query("SELECT COUNT(id) FROM members WHERE gender = 'Female'")->fetchColumn();

// Helper to build sorting links dynamically
function build_sort_link($column, $current_sort, $current_dir, $base_url) {
    $params = $_GET;
    $params['sort'] = $column;
    $params['dir'] = ($current_sort === $column && $current_dir === 'ASC') ? 'DESC' : 'ASC';
    unset($params['action'], $params['member_id'], $params['success']);
    return '?' . http_build_query($params);
}
?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-800 mb-2">Members</h1>
        <p class="text-gray-500">Manage and filter all registered members.</p>
    </div>

    <?php if ($error_message): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            Action completed successfully.
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 border-l-4 border-l-green-500">
            <p class="text-sm text-gray-500">Total Members</p>
            <p class="text-3xl font-bold mt-1"><?= number_format((float)$total_members) ?></p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 border-l-4 border-l-blue-500">
            <p class="text-sm text-gray-500">Active</p>
            <p class="text-3xl font-bold mt-1"><?= number_format((float)$active_members) ?></p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 border-l-4 border-l-yellow-500">
            <p class="text-sm text-gray-500">Pending</p>
            <p class="text-3xl font-bold mt-1"><?= number_format((float)$pending_members) ?></p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 border-l-4 border-l-indigo-500">
            <p class="text-sm text-gray-500">Gender Breakdown</p>
            <p class="text-sm mt-1">👦 Male: <span class="font-bold"><?= $male_members ?></span></p>
            <p class="text-sm">👧 Female: <span class="font-bold"><?= $female_members ?></span></p>
        </div>
    </div>

    <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 mb-8 bg-white p-5 rounded-xl shadow-sm border border-slate-100">
        <?php foreach ($_GET as $key => $val): ?>
            <?php if (!in_array($key, ['gender', 'age_group', 'status', 'payam', 'action', 'member_id', 'success'])): ?>
                <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars((string)$val) ?>">
            <?php endif; ?>
        <?php endforeach; ?>

        <select name="gender" class="border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
            <option value="">All Genders</option>
            <option value="Male" <?= ($_GET['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= ($_GET['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
        </select>
        
        <select name="age_group" class="border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
            <option value="">All Ages</option>
            <option value="under20" <?= ($_GET['age_group'] ?? '') === 'under20' ? 'selected' : '' ?>>Under 20</option>
            <option value="20to30" <?= ($_GET['age_group'] ?? '') === '20to30' ? 'selected' : '' ?>>20–30</option>
            <option value="over30" <?= ($_GET['age_group'] ?? '') === 'over30' ? 'selected' : '' ?>>Over 30</option>
        </select>
        
        <select name="status" class="border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
            <option value="">All Status</option>
            <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="rejected" <?= ($_GET['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
        </select>
        
        <input type="text" name="payam" placeholder="Search Payam..." value="<?= htmlspecialchars($_GET['payam'] ?? '') ?>" class="border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
        
        <button type="submit" class="bg-green-600 text-white font-semibold rounded-lg p-3 hover:bg-green-700 transition">Filter</button>
        <a href="<?= htmlspecialchars($base_url) ?>" class="flex items-center justify-center bg-gray-100 text-gray-700 font-medium rounded-lg p-3 hover:bg-gray-200 transition">Reset</a>
    </form>

    <div class="bg-white shadow-sm border border-slate-100 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-700">
                    <tr>
                        <th class="p-4 font-semibold">
                            <a href="<?= build_sort_link('id', $sort, $dir, $base_url) ?>" class="hover:text-green-600 flex items-center gap-1">
                                ID <?= $sort === 'id' ? ($dir === 'ASC' ? '↑' : '↓') : '' ?>
                            </a>
                        </th>
                        <th class="p-4 font-semibold">Photo</th>
                        <th class="p-4 font-semibold">
                            <a href="<?= build_sort_link('full_name', $sort, $dir, $base_url) ?>" class="hover:text-green-600 flex items-center gap-1">
                                Full Name <?= $sort === 'full_name' ? ($dir === 'ASC' ? '↑' : '↓') : '' ?>
                            </a>
                        </th>
                        <th class="p-4 font-semibold">Gender/Age</th>
                        <th class="p-4 font-semibold">Contact</th>
                        <th class="p-4 font-semibold">Payam</th>
                        <th class="p-4 font-semibold text-center">Status</th>
                        <th class="p-4 font-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!$members): ?>
                        <tr><td colspan="8" class="p-8 text-center text-gray-500">No members found matching your criteria.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($members as $m): ?>
                        <?php
                        // FIX FOR BROKEN IMAGES
                        // Strip 'public/' from the database path if it's there. 
                        // Assuming Railway serves your app from the root, this makes it '/uploads/...'
                        $photo_url = null;
                        if (!empty($m['photo'])) {
                            $clean_path = str_replace('public/', '', $m['photo']);
                            // Ensures it always starts with a forward slash for absolute web path
                            $photo_url = '/' . ltrim($clean_path, '/'); 
                        }
                        ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 font-medium text-slate-600">#<?= $m['id'] ?></td>
                            <td class="p-4">
                                <?php if ($photo_url): ?>
                                    <img src="<?= htmlspecialchars($photo_url) ?>" alt="Photo" class="h-10 w-10 rounded-full object-cover border border-gray-200 shadow-sm">
                                <?php else: ?>
                                    <div class="h-10 w-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 font-bold text-xs border border-slate-200">N/A</div>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 font-semibold text-slate-800"><?= htmlspecialchars($m['full_name']) ?></td>
                            <td class="p-4 text-slate-600"><?= htmlspecialchars($m['gender']) ?> <span class="text-slate-400">(<?= $m['age'] ?>)</span></td>
                            <td class="p-4">
                                <div class="text-slate-800"><?= htmlspecialchars($m['email']) ?></div>
                                <div class="text-xs text-slate-500 mt-0.5"><?= htmlspecialchars($m['phone']) ?></div>
                            </td>
                            <td class="p-4 text-slate-600"><?= htmlspecialchars($m['payam']) ?></td>
                            <td class="p-4 text-center">
                                <?php
                                $status_classes = [
                                    'active' => 'bg-green-100 text-green-700 border-green-200',
                                    'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                    'rejected' => 'bg-red-100 text-red-700 border-red-200'
                                ];
                                $class = $status_classes[$m['status']] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                ?>
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full border <?= $class ?>">
                                    <?= ucfirst(htmlspecialchars($m['status'])) ?>
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <?php if ($m['status'] === 'pending'): ?>
                                    <div class="flex items-center justify-center gap-3">
                                        <a href="<?= $base_url ?>&action=approve&member_id=<?= $m['id'] ?>" class="text-green-600 font-bold hover:text-green-800 hover:underline">Approve</a>
                                        <a href="<?= $base_url ?>&action=reject&member_id=<?= $m['id'] ?>" class="text-red-600 font-bold hover:text-red-800 hover:underline">Reject</a>
                                    </div>
                                <?php else: ?>
                                    <div class="flex items-center justify-center gap-3">
                                        <button class="text-blue-600 font-medium hover:text-blue-800 hover:underline" onclick="openMemberModal('edit', <?= $m['id'] ?>)">Edit</button>
                                        <a href="admin/members_delete.php?id=<?= $m['id'] ?>" class="text-red-500 font-medium hover:text-red-700 hover:underline" onclick="return confirm('Delete this member completely?')">Delete</a>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>