<?php
// admin/sections/projects_modal.php
declare(strict_types=1);
?>

<!-- ============================
     MODAL OVERLAY
=============================== -->
<div id="projectModalOverlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 hidden"></div>

<!-- ============================
     PROJECT MODAL
=============================== -->
<div id="projectModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="bg-white w-full max-w-3xl rounded-xl shadow-lg border border-slate-200 p-6 relative animate-fadeIn overflow-y-auto max-h-[90vh]">

        <!-- Close Button -->
        <button onclick="closeProjectModal()"
                class="absolute top-3 right-3 text-slate-500 hover:text-slate-700 text-xl">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <!-- Modal Title -->
        <h2 id="projectModalTitle" class="text-xl font-bold text-green-800 mb-5">
            Add Project
        </h2>

        <!-- FORM -->
        <form id="projectsForm"
              action="sections/projects_save.php"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-5">

            <input type="hidden" name="id" id="project_id">

            <!-- Title -->
            <div>
                <label class="text-sm font-semibold text-green-700">Title</label>
                <input type="text" id="project_title" name="title"
                       class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500"
                       required placeholder="Project title">
            </div>

            <!-- Description -->
            <div>
                <label class="text-sm font-semibold text-green-700">Description</label>
                <textarea id="project_description" name="description" rows="5"
                          class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500"
                          required placeholder="Describe the project..."></textarea>
            </div>

            <!-- Status & Featured -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-semibold text-green-700">Status</label>
                    <select id="project_status" name="status"
                            class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="current">Ongoing</option>
                        <option value="planned">Planned</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold text-green-700">Featured</label>
                    <select id="project_featured" name="featured"
                            class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-semibold text-green-700">Start Date</label>
                    <input type="date" id="project_start" name="start_date"
                           class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="text-sm font-semibold text-green-700">End Date</label>
                    <input type="date" id="project_end" name="end_date"
                           class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
            </div>

            <!-- Image -->
            <div>
                <label class="text-sm font-semibold text-green-700">Project Image</label>
                <input type="file" id="project_image" name="image" accept="image/*"
                       class="w-full mt-1 border rounded-lg px-3 py-2 bg-white cursor-pointer">

                <div id="projectImagePreview" class="mt-3 hidden">
                    <img id="projectImagePreviewImg" src=""
                         class="w-32 h-32 object-cover rounded-lg border shadow">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3">
                <button type="button" onclick="openProjectModal('add')"
                        class="px-4 py-2 border rounded-lg text-slate-600 hover:bg-slate-100">
                    Reset
                </button>

                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold shadow">
                    Save Project
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================
     JS LOGIC
=============================== -->
<script>
(() => {
    const overlay = document.getElementById('projectModalOverlay');
    const modal = document.getElementById('projectModal');
    const form = document.getElementById('projectsForm');
    const imgPreview = document.getElementById('projectImagePreview');
    const imgPreviewImg = document.getElementById('projectImagePreviewImg');

    const fields = {
        id: document.getElementById('project_id'),
        title: document.getElementById('project_title'),
        description: document.getElementById('project_description'),
        status: document.getElementById('project_status'),
        featured: document.getElementById('project_featured'),
        start: document.getElementById('project_start'),
        end: document.getElementById('project_end'),
        image: document.getElementById('project_image')
    };

    window.openProjectModal = function(type, id = null) {
        document.body.classList.add("overflow-hidden");
        overlay.classList.remove("hidden");
        modal.classList.remove("hidden");
        form.reset();
        imgPreview.classList.add("hidden");
        imgPreviewImg.src = "";

        if(type === "add") {
            document.getElementById('projectModalTitle').textContent = "Add Project";
            fields.id.value = "";
            return;
        }

        if(type === "edit" && id) {
            document.getElementById('projectModalTitle').textContent = "Edit Project";
            fetch("sections/projects_list_ajax.php?id=" + id)
                .then(res => res.json())
                .then(p => {
                    if(!p) return;
                    fields.id.value = p.id || "";
                    fields.title.value = p.title || "";
                    fields.description.value = p.description || "";
                    fields.status.value = p.status || "current";
                    fields.featured.value = p.featured || 0;
                    fields.start.value = p.start_date || "";
                    fields.end.value = p.end_date || "";

                    if(p.image) {
                        imgPreview.classList.remove("hidden");
                        imgPreviewImg.src = "/uploads/projects/" + p.image;
                    }
                });
        }
    }

    window.closeProjectModal = function() {
        overlay.classList.add("hidden");
        modal.classList.add("hidden");
        document.body.classList.remove("overflow-hidden");
    }

    fields.image.addEventListener("change", () => {
        const file = fields.image.files[0];
        if(file) {
            imgPreviewImg.src = URL.createObjectURL(file);
            imgPreview.classList.remove("hidden");
        }
    });

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        const fd = new FormData(form);
        try {
            const res = await fetch(form.action, { method: "POST", body: fd });
            if(!res.ok) throw new Error("Failed to save project");
            closeProjectModal();
            if(typeof loadProjects === "function") loadProjects();
        } catch(err) {
            alert(err.message);
            console.error(err);
        }
    });

    overlay.addEventListener("click", closeProjectModal);
    document.addEventListener("keydown", (e) => {
        if(e.key === "Escape") closeProjectModal();
    });

})();
</script>

<style>
@keyframes fadeIn {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.animate-fadeIn { animation: fadeIn 0.18s ease-out; }
</style>
