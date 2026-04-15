<?php
declare(strict_types=1);
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Project Portfolio</h1>
        <p class="text-slate-500 text-sm mt-1">Manage infrastructure and community growth projects.</p>
    </div>

    <button onclick="openProjectModal('add')"
        class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-emerald-500/20 flex items-center gap-2 transition-all active:scale-95">
        <i data-lucide="plus-square" class="w-5 h-5"></i>
        <span>New Project</span>
    </button>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] uppercase tracking-widest font-black text-slate-400">
                    <th class="py-4 px-6">Project Preview</th>
                    <th class="py-4 px-6">Status</th>
                    <th class="py-4 px-6">Timeline</th>
                    <th class="py-4 px-6 text-center">Featured</th>
                    <th class="py-4 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="projectsTableBody" class="divide-y divide-slate-100">
                </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . "/projects_modal.php"; ?>

<script>
async function loadProjects() {
    const tbody = document.getElementById("projectsTableBody");
    try {
        const res = await fetch("sections/projects_list_ajax.php");
        const data = await res.json();

        if (!data.length) {
            tbody.innerHTML = `<tr><td colspan="5" class="py-12 text-center text-slate-400">No projects listed.</td></tr>`;
            return;
        }

        tbody.innerHTML = data.map(p => `
            <tr class="hover:bg-slate-50/50 group transition-colors">
                <td class="py-4 px-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-slate-100 overflow-hidden border border-slate-200 shrink-0">
                            ${p.image ? `<img src="/uploads/projects/${p.image}" class="w-full h-full object-cover">` : `<div class="flex items-center justify-center h-full text-slate-300"><i data-lucide="package" class="w-6 h-6"></i></div>`}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 truncate max-w-[200px]">${p.title}</p>
                            <p class="text-xs text-slate-500 line-clamp-1">${p.description || 'No description'}</p>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-6">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight bg-slate-100 text-slate-600 border border-slate-200">
                        ${p.status}
                    </span>
                </td>
                <td class="py-4 px-6">
                    <div class="text-[11px] text-slate-500 leading-tight">
                        <span class="block">Start: <b class="text-slate-700">${p.start_date || 'N/A'}</b></span>
                        <span class="block">End: <b class="text-slate-700">${p.end_date || 'N/A'}</b></span>
                    </div>
                </td>
                <td class="py-4 px-6 text-center">
                    ${p.featured == 1 ? `<div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-50 text-amber-500 border border-amber-100 shadow-sm"><i data-lucide="star" class="w-4 h-4 fill-amber-500"></i></div>` : `<span class="text-slate-200">—</span>`}
                </td>
                <td class="py-4 px-6">
                    <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onclick="openProjectModal('edit', ${p.id})" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg"><i data-lucide="pencil" class="w-4 h-4"></i></button>
                        <button onclick="deleteProject(${p.id})" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                    </div>
                </td>
            </tr>
        `).join('');
        lucide.createIcons();
    } catch (err) { console.error(err); }
}

async function deleteProject(id) {
    if (!confirm("Delete project?")) return;
    await fetch(`sections/projects_delete.php?id=${id}`);
    loadProjects();
}
loadProjects();
</script>