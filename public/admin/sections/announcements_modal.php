<?php // admin/sections/announcements_modal.php ?>
<!-- ============================
     OVERLAY (click to close)
============================ -->
<div id="announcementModalOverlay"
     onclick="closeAnnouncementModal()"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 hidden">
</div>

<!-- ============================
     MODAL
============================ -->
<div id="announcementModal"
     class="fixed inset-0 z-50 hidden flex items-center justify-center">

    <div class="bg-white w-full max-w-2xl rounded-xl shadow-lg border border-slate-300
                p-6 relative animate-fadeIn">

        <!-- Close Button -->
        <button onclick="closeAnnouncementModal()"
                class="absolute top-3 right-3 text-slate-500 hover:text-slate-700 text-xl">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <!-- Title -->
        <h2 id="announcementModalTitle"
            class="text-xl font-bold text-green-800 mb-5">
            Add Announcement
        </h2>

        <!-- ============================
             FORM
        ============================= -->
        <form id="announcementsForm"
              action="sections/announcements_save.php"
              method="POST"
              class="space-y-5">

            <input type="hidden" name="id" id="announcement_id">

            <!-- Title -->
            <div>
                <label class="text-sm font-semibold text-green-700">Title</label>
                <input type="text" id="announcement_title" name="title"
                       class="w-full mt-1 px-3 py-2 border border-slate-300 rounded-lg"
                       required>
            </div>

            <!-- Body -->
            <div>
                <label class="text-sm font-semibold text-green-700">Body</label>
                <textarea id="announcement_body" name="body" rows="5"
                          class="w-full mt-1 px-3 py-2 border border-slate-300 rounded-lg"
                          required></textarea>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-semibold text-green-700">Starts At</label>
                    <input type="date" id="announcement_starts_at" name="starts_at"
                           class="w-full mt-1 px-3 py-2 border border-slate-300 rounded-lg">
                </div>

                <div>
                    <label class="text-sm font-semibold text-green-700">Ends At</label>
                    <input type="date" id="announcement_ends_at" name="ends_at"
                           class="w-full mt-1 px-3 py-2 border border-slate-300 rounded-lg">
                </div>
            </div>

            <!-- Publish -->
            <div>
                <label class="text-sm font-semibold text-green-700">Publish Status</label>
                <select id="announcement_is_published" name="is_published"
                        class="w-full mt-1 px-3 py-2 border border-slate-300 rounded-lg">
                    <option value="1">Published</option>
                    <option value="0">Hidden</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3">
                <button type="button"
                        onclick="closeAnnouncementModal()"
                        class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-100">
                    Exit
                </button>

                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                    Save Announcement
                </button>
            </div>

        </form>
    </div>
</div>

<!-- ============================
     JS LOGIC
============================ -->
<script>
// OPEN
function openAnnouncementModal(type, id = null) {
    document.body.classList.add("overflow-hidden");

    document.getElementById('announcementModalOverlay').classList.remove('hidden');
    document.getElementById('announcementModal').classList.remove('hidden');

    const title = document.getElementById('announcementModalTitle');
    const form = document.getElementById('announcementsForm');

    form.reset();
    document.getElementById('announcement_id').value = "";

    if (type === 'add') {
        title.textContent = "Add Announcement";
        return;
    }

    // EDIT MODE
    title.textContent = "Edit Announcement";

    fetch("sections/announcements_list_ajax.php?id=" + id)
        .then(r => r.json())
        .then(a => {
            if (!a) return;

            announcement_id.value            = a.id;
            announcement_title.value         = a.title;
            announcement_body.value          = a.body;
            announcement_starts_at.value     = a.starts_at || "";
            announcement_ends_at.value       = a.ends_at || "";
            announcement_is_published.value  = a.is_published ? 1 : 0;
        });
}

// CLOSE
function closeAnnouncementModal() {
    document.getElementById('announcementModalOverlay').classList.add('hidden');
    document.getElementById('announcementModal').classList.add('hidden');
    document.body.classList.remove("overflow-hidden");
}

// SAVE VIA AJAX
document.getElementById("announcementsForm").addEventListener("submit", async (e) => {
    e.preventDefault();
    const fd = new FormData(e.target);

    try {
        const res = await fetch(e.target.action, { method: "POST", body: fd });
        if (!res.ok) throw new Error("Saving failed");

        closeAnnouncementModal();
        if (typeof loadAnnouncements === "function") loadAnnouncements();

    } catch (err) {
        alert(err.message);
    }
});
</script>

<style>
@keyframes fadeIn {
    from { transform: scale(0.95); opacity: 0; }
    to   { transform: scale(1); opacity: 1; }
}
.animate-fadeIn {
    animation: fadeIn 0.2s ease-out;
}
</style>
