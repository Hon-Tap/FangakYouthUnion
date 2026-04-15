<?php
declare(strict_types=1);
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">News Management</h1>
        <p class="text-slate-500 text-sm mt-1">Create, edit, and organize all published articles.</p>
    </div>

    <button onclick="openModal('add')" 
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-emerald-600/20 transition-all active:scale-95">
        <i data-lucide="plus-circle" class="w-5 h-5"></i>
        <span>Add News Article</span>
    </button>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-200">
                    <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider text-slate-500">Article</th>
                    <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider text-slate-500">Author</th>
                    <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider text-slate-500">Category</th>
                    <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider text-slate-500">Date Published</th>
                    <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider text-slate-500 text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="newsTableBody" class="divide-y divide-slate-100 text-sm">
                <tr>
                    <td colspan="5" class="py-12 text-center text-slate-400">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-8 h-8 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin"></div>
                            <p>Loading news stream...</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . "/news_modal.php"; ?>

<script>
async function loadNews() {
    const tbody = document.getElementById("newsTableBody");
    try {
        const res = await fetch("sections/news_list_ajax.php");
        const news = await res.json();

        if (!news.length) {
            tbody.innerHTML = `<tr><td colspan="5" class="py-12 text-center text-slate-400 font-medium">No news found. Start by creating one!</td></tr>`;
            return;
        }

        tbody.innerHTML = news.map(post => `
            <tr class="hover:bg-slate-50/80 transition-colors group">
                <td class="py-4 px-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg bg-slate-100 overflow-hidden shrink-0 border border-slate-200">
                            ${post.image ? `<img src="/uploads/news/${post.image}" class="w-full h-full object-cover">` : `<div class="flex items-center justify-center h-full text-slate-400"><i data-lucide="image" class="w-5 h-5"></i></div>`}
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-slate-800 truncate max-w-[250px]">${post.title || 'Untitled Article'}</p>
                            <p class="text-xs text-slate-500 line-clamp-1">${post.subheading || 'No subtitle'}</p>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-6 font-medium text-slate-600">${post.author || 'Staff'}</td>
                <td class="py-4 px-6">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100 capitalize">
                        ${post.category || 'General'}
                    </span>
                </td>
                <td class="py-4 px-6 text-slate-500 font-mono text-xs">${post.created_at}</td>
                <td class="py-4 px-6">
                    <div class="flex items-center justify-center gap-2">
                        <button onclick="openModal('edit', ${post.id})" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="Edit">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </button>
                        <button onclick="deleteNews(${post.id})" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Delete">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
        lucide.createIcons();
    } catch(err) {
        tbody.innerHTML = `<tr><td colspan="5" class="py-8 text-center text-rose-500">System Error: Failed to fetch news.</td></tr>`;
    }
}

async function deleteNews(id) {
    if(!confirm('Permanently delete this article?')) return;
    try {
        const res = await fetch(`sections/news_delete.php?id=${id}`);
        if(res.ok) loadNews();
    } catch(err) { console.error(err); }
}

loadNews();
</script>