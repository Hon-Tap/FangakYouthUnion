<?php
// sections/members.php
include_once __DIR__ . "/../../../app/config/db.php";

// 1. PAGINATION & URL SETUP
$current_params = $_GET;
$base_query = http_build_query(array_diff_key($current_params, ['action' => '', 'member_id' => '', 'success' => '', 'p' => '']));
$base_url = '?' . $base_query;

$limit = 10; 
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$offset = ($page - 1) * $limit;

$success_type = $_GET['success'] ?? null;

// 2. FILTERS & QUERY
$where = [];
$params = [];
if (!empty($_GET['gender'])) { $where[] = "gender = ?"; $params[] = $_GET['gender']; }
if (!empty($_GET['payam'])) { $where[] = "payam LIKE ?"; $params[] = "%" . $_GET['payam'] . "%"; }
if (!empty($_GET['status'])) { $where[] = "status = ?"; $params[] = $_GET['status']; }

$count_sql = "SELECT COUNT(id) FROM members" . ($where ? " WHERE " . implode(" AND ", $where) : "");
$stmt_count = $pdo->prepare($count_sql);
$stmt_count->execute($params);
$total_filtered_members = $stmt_count->fetchColumn();
$total_pages = ceil($total_filtered_members / $limit);

$sql = "SELECT * FROM members" . ($where ? " WHERE " . implode(" AND ", $where) : "") . " ORDER BY id DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. STATS
$stats = $pdo->query("SELECT COUNT(id) as total, SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active FROM members")->fetch(PDO::FETCH_ASSOC);

function build_page_link($page_num) {
    $p = $_GET; $p['p'] = $page_num;
    unset($p['action'], $p['member_id'], $p['success']);
    return '?' . http_build_query($p);
}
?>

<style>
    .modal-open { overflow: hidden; }
    .glass-modal { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(8px); }
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>

<div class="p-4 md:p-8">
    <div class="mb-8 flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Members file</h1>
            <p class="text-slate-500">Total Registered: <?= number_format((float)$stats['total']) ?></p>
        </div>
        <div class="flex gap-2">
             <div class="bg-emerald-100 text-emerald-700 px-4 py-2 rounded-lg text-sm font-bold">Active: <?= $stats['active'] ?></div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold">
                    <tr>
                        <th class="p-5">Member</th>
                        <th class="p-5">Contact</th>
                        <th class="p-5">Payam</th>
                        <th class="p-5">Status</th>
                        <th class="p-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($members as $m): 
                        $photoPath = (!empty($m['photo']) && $m['photo'] !== 'null') ? '/' . ltrim($m['photo'], '/') : null;
                        $initial = strtoupper(substr($m['full_name'], 0, 1));
                    ?>
                    <tr class="hover:bg-slate-50/50 transition-all cursor-pointer" onclick="viewMember(<?= $m['id'] ?>)">
                        <td class="p-5">
                            <div class="flex items-center gap-3">
                                <?php if ($photoPath): ?>
                                    <img src="<?= $photoPath ?>" class="h-11 w-11 rounded-2xl object-cover shadow-sm">
                                <?php else: ?>
                                    <div class="h-11 w-11 rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200 text-slate-500 flex items-center justify-center font-bold border border-slate-200">
                                        <?= $initial ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <p class="font-bold text-slate-800"><?= htmlspecialchars($m['full_name']) ?></p>
                                    <p class="text-xs text-slate-400"><?= $m['gender'] ?> • <?= $m['age'] ?> yrs</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-5 text-sm text-slate-600"><?= htmlspecialchars($m['phone'] ?: 'No Phone') ?></td>
                        <td class="p-5 text-sm text-slate-600"><?= htmlspecialchars($m['payam']) ?></td>
                        <td class="p-5">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest <?= $m['status'] === 'active' ? 'bg-green-100 text-green-600' : 'bg-amber-100 text-amber-600' ?>">
                                <?= $m['status'] ?>
                            </span>
                        </td>
                        <td class="p-5 text-right" onclick="event.stopPropagation()">
                            <div class="flex justify-end gap-4">
                                <button onclick="triggerEdit(<?= $m['id'] ?>)" class="text-blue-500 hover:text-blue-700 font-bold text-sm">Edit</button>
                                <a href="<?= $base_url ?>&action=delete&member_id=<?= $m['id'] ?>" onclick="return confirm('Delete permanently?')" class="text-rose-400 hover:text-rose-600 font-bold text-sm">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="viewModal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 glass-modal" onclick="closeViewModal()"></div>
    <div class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl transition-transform transform translate-x-full duration-300 flex flex-col" id="modalContent">
        <div class="p-6 border-b flex justify-between items-center">
            <h2 class="text-xl font-bold text-slate-800">Member Details</h2>
            <button onclick="closeViewModal()" class="p-2 hover:bg-slate-100 rounded-full"><i data-lucide="x"></i></button>
        </div>
        
        <div class="flex-1 overflow-y-auto custom-scrollbar p-8">
            <div id="modalBody" class="space-y-6 text-center">
                </div>
        </div>
    </div>
</div>

<script>
lucide.createIcons();

function viewMember(id) {
    const modal = document.getElementById('viewModal');
    const content = document.getElementById('modalContent');
    const body = document.getElementById('modalBody');

    modal.classList.remove('hidden');
    setTimeout(() => content.classList.remove('translate-x-full'), 10);

    fetch(`sections/members_fetch.php?id=${id}`)
        .then(r => r.json())
        .then(data => {
            const photo = data.photo ? `/${data.photo}` : null;
            body.innerHTML = `
                <div class="flex flex-col items-center">
                    ${photo ? 
                        `<img src="${photo}" class="w-32 h-32 rounded-3xl object-cover shadow-xl border-4 border-white">` :
                        `<div class="w-32 h-32 rounded-3xl bg-slate-100 flex items-center justify-center text-4xl font-bold text-slate-400 border-2 border-dashed border-slate-200">${data.full_name[0]}</div>`
                    }
                    <h3 class="mt-4 text-2xl font-black text-slate-800">${data.full_name}</h3>
                    <p class="text-blue-600 font-medium">Member ID: #${data.id}</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4 text-left pt-6">
                    <div class="bg-slate-50 p-4 rounded-2xl"><p class="text-xs text-slate-400 uppercase font-bold">Gender</p><p class="font-bold">${data.gender}</p></div>
                    <div class="bg-slate-50 p-4 rounded-2xl"><p class="text-xs text-slate-400 uppercase font-bold">Age</p><p class="font-bold">${data.age} Years</p></div>
                    <div class="bg-slate-50 p-4 rounded-2xl col-span-2"><p class="text-xs text-slate-400 uppercase font-bold">Email</p><p class="font-bold">${data.email}</p></div>
                    <div class="bg-slate-50 p-4 rounded-2xl col-span-2"><p class="text-xs text-slate-400 uppercase font-bold">Phone</p><p class="font-bold">${data.phone || 'N/A'}</p></div>
                    <div class="bg-slate-50 p-4 rounded-2xl col-span-2"><p class="text-xs text-slate-400 uppercase font-bold">Location (Payam)</p><p class="font-bold">${data.payam}</p></div>
                </div>
                
                <div class="pt-6">
                    <button onclick="triggerEdit(${data.id})" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold hover:bg-black transition">Edit Profile</button>
                </div>
            `;
            lucide.createIcons();
        });
}

function closeViewModal() {
    const content = document.getElementById('modalContent');
    content.classList.add('translate-x-full');
    setTimeout(() => document.getElementById('viewModal').classList.add('hidden'), 300);
}

function triggerEdit(id) {
    closeViewModal();
    if (typeof openMemberModal === 'function') {
        openMemberModal('edit', id);
    }
}
</script>