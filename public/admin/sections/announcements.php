<?php
// admin/sections/announcements.php
declare(strict_types=1);
?>

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-green-800">Announcements</h1>
        <p class="text-slate-500 text-sm mt-1">Share important updates with the public.</p>
    </div>

    <button onclick="openAnnouncementModal('add')"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold shadow flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Add Announcement
    </button>
</div>

<div class="bg-white shadow-md rounded-xl overflow-hidden border">
    <div class="overflow-x-auto">
        <table id="announcementTable" class="min-w-full text-sm">
            <thead>
            <tr class="bg-green-50 text-green-800 uppercase text-xs font-semibold border-b">
                <th class="py-3 px-4">ID</th>
                <th class="py-3 px-4">Title</th>
                <th class="py-3 px-4">Active Dates</th>
                <th class="py-3 px-4">Published</th>
                <th class="py-3 px-4 w-32 text-center">Actions</th>
            </tr>
            </thead>
            <tbody id="announcementTableBody">
                <tr>
                    <td colspan="5" class="py-8 text-center text-slate-500">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . "/announcements_modal.php"; ?>

<script>
async function loadAnnouncements() {
    const tbody = document.getElementById("announcementTableBody");

    try {
        const res = await fetch("sections/announcements_list_ajax.php");
        const rows = await res.json();

        if (!rows.length) {
            tbody.innerHTML = `<tr><td colspan="5" class="py-8 text-center text-slate-500">No announcements yet.</td></tr>`;
            return;
        }

        tbody.innerHTML = rows.map(a => `
            <tr class="border-b hover:bg-slate-50">
                <td class="py-3 px-4 font-bold">${a.id}</td>
                <td class="py-3 px-4">${a.title}</td>
                <td class="py-3 px-4 text-sm">${a.starts_at} → ${a.ends_at}</td>
                <td class="py-3 px-4">
                    ${a.is_published == 1
                        ? `<span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">Published</span>`
                        : `<span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">Hidden</span>`
                    }
                </td>
                <td class="py-3 px-4">
                    <div class="flex justify-center gap-2">
                        <button onclick="openAnnouncementModal('edit', ${a.id})"
                                class="px-2 py-1 bg-green-100 text-green-700 rounded-lg text-xs">Edit</button>
                        <button onclick="deleteAnnouncement(${a.id})"
                                class="px-2 py-1 bg-red-100 text-red-700 rounded-lg text-xs">Delete</button>
                    </div>
                </td>
            </tr>
        `).join('');

    } catch(err) {
        tbody.innerHTML = `<tr><td colspan="5" class="py-8 text-center text-red-500">Error loading data.</td></tr>`;
    }
}

async function deleteAnnouncement(id) {
    if (!confirm("Delete this announcement?")) return;

    await fetch("sections/announcements_delete.php?id=" + id);
    loadAnnouncements();
}

loadAnnouncements();
</script>
