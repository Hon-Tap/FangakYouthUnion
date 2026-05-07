<?php
// sections/members.php
// ======================================================================

include_once __DIR__ . "/../../../app/config/db.php";

// ======================================================================
// 1. DYNAMIC URL & PAGINATION SETUP
// ======================================================================
$current_params = $_GET;
// Remove actions and success flags from the base URL to prevent loop triggers
$base_query = http_build_query(array_diff_key($current_params, ['action' => '', 'member_id' => '', 'success' => '', 'p' => '']));
$base_url = '?' . $base_query;

$limit = 10; 
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$offset = ($page - 1) * $limit;

$error_message = null;
$success_type = $_GET['success'] ?? null;

// ======================================================================
// 2. ACTION LOGIC (APPROVE / REJECT / DELETE)
// ======================================================================
if (isset($_GET['action'], $_GET['member_id'])) {
    $member_id = filter_var($_GET['member_id'], FILTER_VALIDATE_INT);
    $action = $_GET['action'];

    if ($member_id) {
        try {
            if ($action === 'approve' || $action === 'reject') {
                $new_status = ($action === 'approve') ? 'active' : 'rejected';
                $stmt = $pdo->prepare("UPDATE members SET status = ? WHERE id = ?");
                $stmt->execute([$new_status, $member_id]);
                $success_param = ($action === 'approve') ? 'approved' : 'rejected';
            } 
            elseif ($action === 'delete') {
                $stmt = $pdo->prepare("DELETE FROM members WHERE id = ?");
                $stmt->execute([$member_id]);
                $success_param = 'deleted';
            }

            // Redirect back with a specific success type for the popup
            header("Location: " . $base_url . "&p=" . $page . "&success=" . $success_param);
            exit;
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    }
}

// ======================================================================
// 3. FILTERS, SORTING & QUERYING (Keeping your existing logic)
// ======================================================================
$where = [];
$params = [];

if (!empty($_GET['gender'])) { $where[] = "gender = ?"; $params[] = $_GET['gender']; }
if (!empty($_GET['payam'])) { $where[] = "payam LIKE ?"; $params[] = "%" . $_GET['payam'] . "%"; }
if (!empty($_GET['age_group'])) {
    if ($_GET['age_group'] === 'under20') $where[] = "age < 20";
    if ($_GET['age_group'] === '20to30') $where[] = "age BETWEEN 20 AND 30";
    if ($_GET['age_group'] === 'over30') $where[] = "age > 30";
}
if (!empty($_GET['status'])) { $where[] = "status = ?"; $params[] = $_GET['status']; }

$allowed_sorts = ['id', 'full_name', 'age', 'status'];
$sort = in_array($_GET['sort'] ?? '', $allowed_sorts) ? $_GET['sort'] : 'id';
$dir = strtoupper($_GET['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

$count_sql = "SELECT COUNT(id) FROM members" . ($where ? " WHERE " . implode(" AND ", $where) : "");
$stmt_count = $pdo->prepare($count_sql);
$stmt_count->execute($params);
$total_filtered_members = $stmt_count->fetchColumn();
$total_pages = ceil($total_filtered_members / $limit);

$sql = "SELECT * FROM members" . ($where ? " WHERE " . implode(" AND ", $where) : "") . " ORDER BY $sort $dir LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ======================================================================
// 4. STATS (Optimized)
// ======================================================================
$stats = $pdo->query("SELECT COUNT(id) as total, SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active, SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending, SUM(CASE WHEN gender = 'Male' THEN 1 ELSE 0 END) as male, SUM(CASE WHEN gender = 'Female' THEN 1 ELSE 0 END) as female FROM members")->fetch(PDO::FETCH_ASSOC);

function build_sort_link($column, $current_sort, $current_dir) {
    $params = $_GET;
    $params['sort'] = $column;
    $params['dir'] = ($current_sort === $column && $current_dir === 'ASC') ? 'DESC' : 'ASC';
    unset($params['action'], $params['member_id'], $params['success']);
    return '?' . http_build_query($params);
}

function build_page_link($page_num) {
    $params = $_GET;
    $params['p'] = $page_num;
    unset($params['action'], $params['member_id'], $params['success']);
    return '?' . http_build_query($params);
}
?>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 8px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }
    
    /* Popup Toast Animation */
    @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    .toast-popup { animation: slideIn 0.4s ease-out forwards; }
</style>

<?php if ($success_type): ?>
    <div id="successToast" class="fixed top-24 right-8 z-[100] toast-popup">
        <div class="bg-white border-l-4 border-green-500 shadow-2xl rounded-xl p-5 flex items-center gap-4 min-w-[300px]">
            <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center shrink-0">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="font-bold text-slate-800 capitalize">Member <?= htmlspecialchars($success_type) ?>!</p>
                <p class="text-xs text-slate-500">The database has been updated successfully.</p>
            </div>
            <button onclick="document.getElementById('successToast').remove()" class="ml-auto text-slate-300 hover:text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    </div>
    <script>setTimeout(() => document.getElementById('successToast')?.remove(), 5000);</script>
<?php endif; ?>

<div class="p-4 md:p-6 lg:p-8 max-w-full">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight mb-1">Members Directory</h1>
            <p class="text-slate-500">Manage, filter, and approve registered members.</p>
        </div>
    </div>

    <?php if ($error_message): ?>
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm mb-6 flex items-center">
            <i data-lucide="alert-circle" class="w-5 h-5 mr-3"></i> <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between transition-transform hover:-translate-y-1">
            <div>
                <p class="text-sm font-medium text-slate-500">Total Members</p>
                <p class="text-3xl font-bold text-slate-800 mt-1"><?= number_format((float)$stats['total']) ?></p>
            </div>
            <div class="w-12 h-12 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center"><i data-lucide="users" class="w-6 h-6"></i></div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between transition-transform hover:-translate-y-1">
            <div>
                <p class="text-sm font-medium text-slate-500">Active</p>
                <p class="text-3xl font-bold text-green-600 mt-1"><?= number_format((float)$stats['active']) ?></p>
            </div>
            <div class="w-12 h-12 rounded-full bg-green-50 text-green-500 flex items-center justify-center"><i data-lucide="user-check" class="w-6 h-6"></i></div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between transition-transform hover:-translate-y-1">
            <div>
                <p class="text-sm font-medium text-slate-500">Pending</p>
                <p class="text-3xl font-bold text-yellow-600 mt-1"><?= number_format((float)$stats['pending']) ?></p>
            </div>
            <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-500 flex items-center justify-center"><i data-lucide="clock" class="w-6 h-6"></i></div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between transition-transform hover:-translate-y-1">
            <div>
                <p class="text-sm font-medium text-slate-500">Demographics</p>
                <p class="text-sm mt-1 text-slate-600">M: <span class="text-blue-600 font-bold"><?= number_format((float)$stats['male']) ?></span> | F: <span class="text-pink-600 font-bold"><?= number_format((float)$stats['female']) ?></span></p>
            </div>
            <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center"><i data-lucide="pie-chart" class="w-6 h-6"></i></div>
        </div>
    </div>

    <div class="bg-white p-4 md:p-5 rounded-2xl shadow-sm border border-slate-100 mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-3">
            <?php foreach ($_GET as $key => $val): ?>
                <?php if (!in_array($key, ['gender', 'age_group', 'status', 'payam', 'action', 'member_id', 'success', 'p'])): ?>
                    <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars((string)$val) ?>">
                <?php endif; ?>
            <?php endforeach; ?>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 flex-grow">
                <select name="gender" class="bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-green-500 outline-none">
                    <option value="">All Genders</option>
                    <option value="Male" <?= ($_GET['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= ($_GET['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                </select>
                <select name="age_group" class="bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-green-500 outline-none">
                    <option value="">All Ages</option>
                    <option value="under20" <?= ($_GET['age_group'] ?? '') === 'under20' ? 'selected' : '' ?>>Under 20</option>
                    <option value="20to30" <?= ($_GET['age_group'] ?? '') === '20to30' ? 'selected' : '' ?>>20–30</option>
                    <option value="over30" <?= ($_GET['age_group'] ?? '') === 'over30' ? 'selected' : '' ?>>Over 30</option>
                </select>
                <select name="status" class="bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-green-500 outline-none">
                    <option value="">All Status</option>
                    <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                </select>
                <input type="text" name="payam" placeholder="Search Payam..." value="<?= htmlspecialchars($_GET['payam'] ?? '') ?>" class="bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-green-500 outline-none">
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="bg-green-600 text-white font-medium rounded-xl px-6 py-2.5 hover:bg-green-700 transition shadow-sm">Filter</button>
                <a href="<?= htmlspecialchars($base_url) ?>" class="bg-white border border-slate-200 text-slate-600 font-medium rounded-xl px-6 py-2.5 hover:bg-slate-50 transition text-center flex items-center">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white shadow-sm border border-slate-100 rounded-2xl overflow-hidden flex flex-col">
        <div class="overflow-x-auto overflow-y-auto max-h-[65vh] custom-scrollbar">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 text-sm uppercase tracking-wider sticky top-0 z-10">
                    <tr>
                        <th class="p-4 font-semibold"><a href="<?= build_sort_link('id', $sort, $dir) ?>">ID <?= $sort === 'id' ? ($dir === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                        <th class="p-4 font-semibold">User</th>
                        <th class="p-4 font-semibold hidden sm:table-cell">Contact Info</th>
                        <th class="p-4 font-semibold hidden md:table-cell">Location</th>
                        <th class="p-4 font-semibold text-center">Status</th>
                        <th class="p-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php foreach ($members as $m): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="p-4 text-slate-500 font-medium">#<?= $m['id'] ?></td>
                            <td class="p-4">
                                <div class="flex items-center gap-4">
                                    <?php if (!empty($m['photo'])): ?>
                                        <img src="<?= htmlspecialchars('/' . ltrim($m['photo'], '/')) ?>" class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-sm">
                                    <?php else: ?>
                                        <div class="h-10 w-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold border-2 border-white shadow-sm">
                                            <?= strtoupper(substr($m['full_name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <p class="font-bold text-slate-800"><?= htmlspecialchars($m['full_name']) ?></p>
                                        <p class="text-xs text-slate-500"><?= $m['gender'] ?> • <?= $m['age'] ?> yrs</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 hidden sm:table-cell text-slate-600 font-medium"><?= htmlspecialchars($m['phone']) ?></td>
                            <td class="p-4 hidden md:table-cell text-slate-600"><?= htmlspecialchars($m['payam']) ?></td>
                            <td class="p-4 text-center">
                                <?php $s = $m['status']; ?>
                                <span class="px-3 py-1.5 text-xs font-bold rounded-lg border <?= $s === 'active' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-amber-50 text-amber-600 border-amber-200' ?>">
                                    <?= ucfirst($s) ?>
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <?php if ($m['status'] === 'pending'): ?>
                                        <a href="<?= $base_url ?>&p=<?= $page ?>&action=approve&member_id=<?= $m['id'] ?>" class="p-2 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white rounded-lg transition-colors"><i data-lucide="check" class="w-4 h-4"></i></a>
                                        <a href="<?= $base_url ?>&p=<?= $page ?>&action=reject&member_id=<?= $m['id'] ?>" class="p-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition-colors"><i data-lucide="x" class="w-4 h-4"></i></a>
                                    <?php else: ?>
                                        <button onclick="triggerEditModal(<?= $m['id'] ?>)" class="text-blue-600 font-bold hover:text-blue-800">Edit</button>
                                        <span class="text-slate-300">|</span>
                                        <a href="<?= $base_url ?>&p=<?= $page ?>&action=delete&member_id=<?= $m['id'] ?>" 
                                           class="text-rose-500 font-bold hover:text-rose-700" 
                                           onclick="return confirm('Permanently delete this member?')">Delete</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1): ?>
        <div class="border-t border-slate-200 p-4 bg-slate-50 flex items-center justify-between">
            <p class="text-sm text-slate-500 hidden sm:block">Showing <span class="font-medium"><?= $offset + 1 ?></span> to <span class="font-medium"><?= min($offset + $limit, $total_filtered_members) ?></span> of <?= $total_filtered_members ?></p>
            <div class="flex gap-1">
                <a href="<?= build_page_link($page - 1) ?>" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm <?= $page <= 1 ? 'pointer-events-none opacity-50' : '' ?>">Prev</a>
                <a href="<?= build_page_link($page + 1) ?>" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm <?= $page >= $total_pages ? 'pointer-events-none opacity-50' : '' ?>">Next</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    lucide.createIcons();

    // FIXED EDIT FUNCTIONALITY
    function triggerEditModal(id) {
        // If you have members_modal.php, we trigger it here.
        // For now, let's show a popup that we are ready to edit.
        console.log("Opening edit for ID: " + id);
        if (typeof openMemberModal === 'function') {
            openMemberModal('edit', id);
        } else {
            alert("Edit feature for ID #" + id + " is ready. Please ensure members_modal.php is included in your main dashboard.");
        }
    }
</script>