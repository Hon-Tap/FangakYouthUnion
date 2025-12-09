<?php
// admin/sections/news.php
declare(strict_types=1);
?>

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-green-800">News Management</h1>
        <p class="text-slate-500 text-sm mt-1">Create, edit, and organize all posted news articles.</p>
    </div>

    <button onclick="openModal('add')" 
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold shadow flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Add News
    </button>
</div>

<div class="bg-white shadow-md rounded-xl overflow-hidden border border-slate-200">
    <div class="overflow-x-auto">
        <table id="newsTable" class="min-w-full text-sm text-green-700">
            <thead>
                <tr class="bg-green-50 border-b text-green-800 uppercase text-xs font-semibold">
                    <th class="py-3 px-4">ID</th>
                    <th class="py-3 px-4">Title</th>
                    <th class="py-3 px-4">Subheading</th>
                    <th class="py-3 px-4">Author</th>
                    <th class="py-3 px-4 w-[24rem]">Description</th>
                    <th class="py-3 px-4">Image</th>
                    <th class="py-3 px-4">Category</th>
                    <th class="py-3 px-4">Created</th>
                    <th class="py-3 px-4 text-center w-32">Actions</th>
                </tr>
            </thead>
            <tbody id="newsTableBody">
                <tr>
                    <td colspan="9" class="py-8 text-center text-slate-500">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . "/news_modal.php"; ?>

<script>
// ----------------------------
// Load news list via AJAX
// ----------------------------
async function loadNews() {
    const tbody = document.getElementById("newsTableBody");
    try {
        const res = await fetch("sections/news_list_ajax.php");
        const news = await res.json();

        if (!news.length) {
            tbody.innerHTML = `<tr><td colspan="9" class="py-8 text-center text-slate-500">No news available yet.</td></tr>`;
            return;
        }

        tbody.innerHTML = news.map(post => `
            <tr class="border-b hover:bg-slate-50 transition-all">
                <td class="py-3 px-4 font-bold text-green-800">${post.id}</td>
                <td class="py-3 px-4 font-medium">${post.title || ''}</td>
                <td class="py-3 px-4">${post.subheading || '—'}</td>
                <td class="py-3 px-4 text-green-800">${post.author || 'Unknown'}</td>
                <td class="py-3 px-4">
                    <p class="line-clamp-3 text-green-600 leading-relaxed">${post.description || ''}</p>
                </td>
                <td class="py-3 px-4">
                    ${post.image ? `<img src="/uploads/news/${post.image}" class="w-14 h-14 rounded-lg object-cover shadow border" alt="News image">`
                                : `<span class="text-green-400 text-xs italic">No image</span>`}
                </td>
                <td class="py-3 px-4">
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 font-semibold">${post.category || 'Uncategorized'}</span>
                </td>
                <td class="py-3 px-4 text-green-500 text-xs">${post.created_at}</td>
                <td class="py-3 px-4">
                    <div class="flex items-center justify-center gap-3">
                        <button onclick="openModal('edit', ${post.id})"
                                class="px-2 py-1 rounded-lg bg-green-100 text-green-700 hover:bg-green-200 transition flex items-center gap-1" title="Edit">
                            <i class="fa-solid fa-pen text-sm"></i>
                            <span class="text-xs font-semibold">Edit</span>
                        </button>
                        <button onclick="deleteNews(${post.id})"
                                class="px-2 py-1 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition flex items-center gap-1" title="Delete">
                            <i class="fa-solid fa-trash text-sm"></i>
                            <span class="text-xs font-semibold">Delete</span>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    } catch(err) {
        tbody.innerHTML = `<tr><td colspan="9" class="py-8 text-center text-red-500">Error loading news</td></tr>`;
        console.error(err);
    }
}

// ----------------------------
// Delete news via AJAX
// ----------------------------
async function deleteNews(id) {
    if(!confirm('Are you sure you want to delete this article?')) return;
    try {
        const res = await fetch(`sections/news_delete.php?id=${id}`);
        if(res.ok) loadNews();
        else alert('Failed to delete the news.');
    } catch(err) {
        alert('Error deleting news.');
        console.error(err);
    }
}

// ----------------------------
// Submit Add/Edit modal via AJAX
// ----------------------------
document.getElementById('newsForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    try {
        const res = await fetch(form.action, {
            method: 'POST',
            body: formData
        });

        if(!res.ok) throw new Error('Failed to save news.');

        closeModal();
        loadNews();
    } catch(err) {
        alert(err.message);
        console.error(err);
    }
});

// Initial load
loadNews();
</script>
