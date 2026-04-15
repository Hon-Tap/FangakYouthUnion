<?php
declare(strict_types=1);
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Announcements</h1>
        <p class="text-slate-500 text-sm mt-1">Broadcast high-priority alerts to the platform.</p>
    </div>

    <button onclick="openAnnouncementModal('add')"
            class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-amber-500/20 flex items-center gap-2 transition-all">
        <i data-lucide="megaphone" class="w-5 h-5 text-white"></i>
        New Broadcast
    </button>
</div>

<div class="grid grid-cols-1 gap-4" id="announcementGrid">
    </div>

<?php include __DIR__ . "/announcements_modal.php"; ?>

<script>
async function loadAnnouncements() {
    const grid = document.getElementById("announcementGrid");
    try {
        const res = await fetch("sections/announcements_list_ajax.php");
        const rows = await res.json();

        if (!rows.length) {
            grid.innerHTML = `<div class="bg-white rounded-2xl p-12 text-center border-2 border-dashed text-slate-400 font-medium">No active announcements.</div>`;
            return;
        }

        grid.innerHTML = rows.map(a => `
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all flex items-start gap-4">
                <div class="w-12 h-12 rounded-full ${a.is_published == 1 ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400'} flex items-center justify-center shrink-0 border border-slate-100">
                    <i data-lucide="bell" class="w-6 h-6"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="font-bold text-slate-800 truncate">${a.title}</h3>
                        ${a.is_published == 1 
                            ? `<span class="px-2 py-0.5 text-[10px] font-bold bg-emerald-500 text-white rounded-md uppercase tracking-widest">Live</span>` 
                            : `<span class="px-2 py-0.5 text-[10px] font-bold bg-slate-200 text-slate-500 rounded-md uppercase tracking-widest">Draft</span>`
                        }
                    </div>
                    <p class="text-xs text-slate-500 flex items-center gap-2">
                        <i data-lucide="calendar" class="w-3 h-3"></i>
                        Active: <span class="text-slate-700 font-semibold">${a.starts_at}</span> to <span class="text-slate-700 font-semibold">${a.ends_at}</span>
                    </p>
                </div>
                <div class="flex gap-2 shrink-0">
                     <button onclick="openAnnouncementModal('edit', ${a.id})" class="p-2 hover:bg-slate-50 rounded-lg text-slate-400 hover:text-emerald-600 transition-colors">
                        <i data-lucide="edit" class="w-5 h-5"></i>
                     </button>
                     <button onclick="deleteAnnouncement(${a.id})" class="p-2 hover:bg-slate-50 rounded-lg text-slate-400 hover:text-rose-600 transition-colors">
                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                     </button>
                </div>
            </div>
        `).join('');
        lucide.createIcons();
    } catch(err) { console.error(err); }
}

async function deleteAnnouncement(id) {
    if (!confirm("Delete this?")) return;
    await fetch("sections/announcements_delete.php?id=" + id);
    loadAnnouncements();
}
loadAnnouncements();
</script>