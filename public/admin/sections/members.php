<?php
// sections/members.php (Refactored with Pagination, Smart Images, and Responsive UI)
// ======================================================================

include_once __DIR__ . "/../../../app/config/db.php";

// ======================================================================
// 1. DYNAMIC URL & PAGINATION SETUP
// ======================================================================
$current_params = $_GET;
$base_query = http_build_query(array_diff_key($current_params, ['action' => '', 'member_id' => '', 'success' => '', 'p' => '']));
$base_url = '?' . $base_query;

$limit = 10; // Number of members per page
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$offset = ($page - 1) * $limit;

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
            
            // Redirect back keeping the current page number
            $redirect_url = $base_url . "&p=" . $page . "&success=1";
            header("Location: " . $redirect_url);
            exit;
        } catch (PDOException $e) {
            $error_message = "Database error: Could not update member status.";
        }
    }
}

// ======================================================================
// 3. FILTERS, SORTING & QUERYING
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

// Count total filtered members for Pagination
$count_sql = "SELECT COUNT(id) FROM members";
if ($where) {
    $count_sql .= " WHERE " . implode(" AND ", $where);
}
$stmt_count = $pdo->prepare($count_sql);
$stmt_count->execute($params);
$total_filtered_members = $stmt_count->fetchColumn();
$total_pages = ceil($total_filtered_members / $limit);

// Fetch actual members for this page
$sql = "SELECT * FROM members";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY $sort $dir LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ======================================================================
// 4. STATS (Overall Database)
// ======================================================================
$total_members = $pdo->query("SELECT COUNT(id) FROM members")->fetchColumn();
$active_members = $pdo->query("SELECT COUNT(id) FROM members WHERE status = 'active'")->fetchColumn();
$pending_members = $pdo->query("SELECT COUNT(id) FROM members WHERE status = 'pending'")->fetchColumn();
$male_members = $pdo->query("SELECT COUNT(id) FROM members WHERE gender = 'Male'")->fetchColumn();
$female_members = $pdo->query("SELECT COUNT(id) FROM members WHERE gender = 'Female'")->fetchColumn();

// Helpers for Links
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
    <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm mb-6 flex items-center">
            <i data-lucide="check-circle" class="w-5 h-5 mr-3"></i> Action completed successfully.
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between transition-transform hover:-translate-y-1">
            <div>
                <p class="text-sm font-medium text-slate-500">Total Members</p>
                <p class="text-3xl font-bold text-slate-800 mt-1"><?= number_format((float)$total_members) ?></p>
            </div>
            <div class="w-12 h-12 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center"><i data-lucide="users" class="w-6 h-6"></i></div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between transition-transform hover:-translate-y-1">
            <div>
                <p class="text-sm font-medium text-slate-500">Active</p>
                <p class="text-3xl font-bold text-green-600 mt-1"><?= number_format((float)$active_members) ?></p>
            </div>
            <div class="w-12 h-12 rounded-full bg-green-50 text-green-500 flex items-center justify-center"><i data-lucide="user-check" class="w-6 h-6"></i></div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between transition-transform hover:-translate-y-1">
            <div>
                <p class="text-sm font-medium text-slate-500">Pending</p>
                <p class="text-3xl font-bold text-yellow-600 mt-1"><?= number_format((float)$pending_members) ?></p>
            </div>
            <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-500 flex items-center justify-center"><i data-lucide="clock" class="w-6 h-6"></i></div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between transition-transform hover:-translate-y-1">
            <div>
                <p class="text-sm font-medium text-slate-500">Demographics</p>
                <p class="text-sm mt-1 text-slate-600">Male: <span class="font-bold text-blue-600"><?= $male_members ?></span></p>
                <p class="text-sm text-slate-600">Female: <span class="font-bold text-pink-600"><?= $female_members ?></span></p>
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
                <select name="gender" class="bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none w-full">
                    <option value="">All Genders</option>
                    <option value="Male" <?= ($_GET['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= ($_GET['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                </select>
                
                <select name="age_group" class="bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-green-500 outline-none w-full">
                    <option value="">All Ages</option>
                    <option value="under20" <?= ($_GET['age_group'] ?? '') === 'under20' ? 'selected' : '' ?>>Under 20</option>
                    <option value="20to30" <?= ($_GET['age_group'] ?? '') === '20to30' ? 'selected' : '' ?>>20–30</option>
                    <option value="over30" <?= ($_GET['age_group'] ?? '') === 'over30' ? 'selected' : '' ?>>Over 30</option>
                </select>
                
                <select name="status" class="bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-green-500 outline-none w-full">
                    <option value="">All Status</option>
                    <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="rejected" <?= ($_GET['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
                
                <input type="text" name="payam" placeholder="Search Payam..." value="<?= htmlspecialchars($_GET['payam'] ?? '') ?>" class="bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-green-500 outline-none w-full">
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="bg-green-600 text-white font-medium rounded-xl px-6 py-2.5 hover:bg-green-700 transition shadow-sm w-full md:w-auto">Filter</button>
                <a href="<?= htmlspecialchars($base_url) ?>" class="bg-white border border-slate-200 text-slate-600 font-medium rounded-xl px-6 py-2.5 hover:bg-slate-50 transition w-full md:w-auto text-center">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white shadow-sm border border-slate-100 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 text-sm uppercase tracking-wider">
                    <tr>
                        <th class="p-4 font-semibold whitespace-nowrap">
                            <a href="<?= build_sort_link('id', $sort, $dir) ?>" class="hover:text-green-600 flex items-center gap-1">
                                ID <?= $sort === 'id' ? ($dir === 'ASC' ? '↑' : '↓') : '' ?>
                            </a>
                        </th>
                        <th class="p-4 font-semibold whitespace-nowrap">User</th>
                        <th class="p-4 font-semibold whitespace-nowrap hidden sm:table-cell">Contact Info</th>
                        <th class="p-4 font-semibold whitespace-nowrap hidden md:table-cell">Location</th>
                        <th class="p-4 font-semibold whitespace-nowrap text-center">Status</th>
                        <th class="p-4 font-semibold whitespace-nowrap text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php if (!$members): ?>
                        <tr>
                            <td colspan="6" class="p-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-lucide="search-x" class="w-12 h-12 text-slate-300 mb-3"></i>
                                    <p class="text-base font-medium text-slate-600">No members found</p>
                                    <p class="text-slate-400 mt-1">Try adjusting your filters or search terms.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($members as $m): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="p-4 text-slate-500 font-medium whitespace-nowrap">#<?= $m['id'] ?></td>
                            
                            <td class="p-4 whitespace-nowrap">
                                <div class="flex items-center gap-4">
                                    <?php 
                                    // SMART IMAGE FALLBACK:
                                    // 1. Primary SRC: Assumes the root of your domain is where the `public/` folder is (e.g. /public/uploads/...)
                                    // 2. Fallback (onerror): Assumes the `public/` folder IS your document root (e.g. /uploads/...)
                                    $primary_img = '/' . ltrim($m['photo'], '/');
                                    $fallback_img = '/' . ltrim(str_replace('public/', '', $m['photo']), '/');
                                    ?>
                                    <?php if (!empty($m['photo'])): ?>
                                        <img src="<?= htmlspecialchars($primary_img) ?>" 
                                             onerror="this.onerror=null; this.src='<?= htmlspecialchars($fallback_img) ?>';" 
                                             alt="Avatar" 
                                             class="h-10 w-10 sm:h-12 sm:w-12 rounded-full object-cover border-2 border-white shadow-sm shrink-0 bg-slate-100">
                                    <?php else: ?>
                                        <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold border-2 border-white shadow-sm shrink-0">
                                            <?= strtoupper(substr($m['full_name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div>
                                        <p class="font-bold text-slate-800"><?= htmlspecialchars($m['full_name']) ?></p>
                                        <p class="text-xs text-slate-500 mt-0.5"><?= htmlspecialchars($m['gender']) ?> • <?= $m['age'] ?> yrs</p>
                                        <p class="text-xs text-slate-500 sm:hidden mt-1"><?= htmlspecialchars($m['phone']) ?></p>
                                    </div>
                                </div>
                            </td>

                            <td class="p-4 hidden sm:table-cell whitespace-nowrap">
                                <p class="text-slate-800 font-medium"><?= htmlspecialchars($m['phone']) ?></p>
                                <p class="text-slate-500 text-xs mt-0.5"><?= htmlspecialchars($m['email']) ?></p>
                            </td>

                            <td class="p-4 hidden md:table-cell whitespace-nowrap text-slate-600">
                                <?= htmlspecialchars($m['payam']) ?>
                            </td>

                            <td class="p-4 text-center whitespace-nowrap">
                                <?php
                                $status_styles = [
                                    'active' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                    'pending' => 'bg-amber-50 text-amber-600 border-amber-200',
                                    'rejected' => 'bg-rose-50 text-rose-600 border-rose-200'
                                ];
                                $style = $status_styles[$m['status']] ?? 'bg-slate-50 text-slate-600 border-slate-200';
                                ?>
                                <span class="px-3 py-1.5 text-xs font-bold rounded-lg border <?= $style ?>">
                                    <?= ucfirst(htmlspecialchars($m['status'])) ?>
                                </span>
                            </td>

                            <td class="p-4 text-right whitespace-nowrap">
                                <?php if ($m['status'] === 'pending'): ?>
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="<?= $base_url ?>&p=<?= $page ?>&action=approve&member_id=<?= $m['id'] ?>" class="p-2 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white rounded-lg transition-colors" title="Approve">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </a>
                                        <a href="<?= $base_url ?>&p=<?= $page ?>&action=reject&member_id=<?= $m['id'] ?>" class="p-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition-colors" title="Reject">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="flex items-center justify-end gap-3 text-sm">
                                        <button class="text-blue-600 font-semibold hover:text-blue-800 transition-colors" onclick="openMemberModal('edit', <?= $m['id'] ?>)">Edit</button>
                                        <span class="text-slate-300">|</span>
                                        <a href="admin/members_delete.php?id=<?= $m['id'] ?>" class="text-rose-500 font-semibold hover:text-rose-700 transition-colors" onclick="return confirm('Delete this member completely?')">Delete</a>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1): ?>
        <div class="border-t border-slate-100 p-4 bg-slate-50 flex items-center justify-between">
            <p class="text-sm text-slate-500 hidden sm:block">
                Showing <span class="font-medium text-slate-800"><?= $offset + 1 ?></span> to 
                <span class="font-medium text-slate-800"><?= min($offset + $limit, $total_filtered_members) ?></span> of 
                <span class="font-medium text-slate-800"><?= $total_filtered_members ?></span> results
            </p>
            
            <div class="flex gap-1 w-full sm:w-auto justify-between sm:justify-end">
                <?php if ($page > 1): ?>
                    <a href="<?= build_page_link($page - 1) ?>" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-100 transition shadow-sm">Previous</a>
                <?php else: ?>
                    <span class="px-4 py-2 bg-slate-100 border border-slate-200 text-slate-400 rounded-lg text-sm font-medium cursor-not-allowed">Previous</span>
                <?php endif; ?>

                <div class="hidden md:flex gap-1">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="<?= build_page_link($i) ?>" class="px-3.5 py-2 <?= $i === $page ? 'bg-green-600 text-white border-green-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-100' ?> border rounded-lg text-sm font-medium transition shadow-sm">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>

                <?php if ($page < $total_pages): ?>
                    <a href="<?= build_page_link($page + 1) ?>" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-100 transition shadow-sm">Next</a>
                <?php else: ?>
                    <span class="px-4 py-2 bg-slate-100 border border-slate-200 text-slate-400 rounded-lg text-sm font-medium cursor-not-allowed">Next</span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>