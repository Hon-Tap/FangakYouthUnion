<?php
// admin/sections/projects.php
declare(strict_types=1);
?>

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-green-800">Projects Management</h1>
        <p class="text-slate-500 text-sm mt-1">Create, update and organize all projects.</p>
    </div>

    <button onclick="openProjectModal('add')"
        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold shadow flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Add Project
    </button>
</div>

<div class="bg-white shadow-md rounded-xl overflow-hidden border border-slate-200">
    <div class="overflow-x-auto">
        <table id="projectsTable" class="min-w-full text-sm text-green-700">
            <thead>
                <tr class="bg-green-50 border-b text-green-800 uppercase text-xs font-semibold">
                    <th class="py-3 px-4">ID</th>
                    <th class="py-3 px-4">Title</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 w-[22rem]">Description</th>
                    <th class="py-3 px-4">Image</th>
                    <th class="py-3 px-4">Start</th>
                    <th class="py-3 px-4">End</th>
                    <th class="py-3 px-4">Featured</th>
                    <th class="py-3 px-4 text-center w-32">Actions</th>
                </tr>
            </thead>
            <tbody id="projectsTableBody">
                <tr>
                    <td colspan="9" class="py-8 text-center text-slate-500">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . "/projects_modal.php"; ?>

<script>
// ------------------------------------------------------------------
// Load projects
// ------------------------------------------------------------------
async function loadProjects() {
    const tbody = document.getElementById("projectsTableBody");

    try {
        const res = await fetch("sections/projects_list_ajax.php");
        const data = await res.json();

        if (!data.length) {
            tbody.innerHTML = `<tr><td colspan="9" class="py-8 text-center text-slate-500">No projects available yet.</td></tr>`;
            return;
        }

        tbody.innerHTML = data.map(p => `
            <tr class="border-b hover:bg-slate-50 transition-all">
                <td class="py-3 px-4 font-bold text-green-800">${p.id}</td>
                <td class="py-3 px-4 font-medium">${p.title}</td>
                <td class="py-3 px-4">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                        ${p.status}
                    </span>
                </td>
                <td class="py-3 px-4">
                    <p class="line-clamp-3 text-green-600 leading-relaxed">${p.description || ""}</p>
                </td>
                <td class="py-3 px-4">
                    ${p.image 
                        ? `<img src="/uploads/projects/${p.image}" class="w-14 h-14 rounded-lg object-cover shadow border">`
                        : `<span class="text-green-400 text-xs italic">No image</span>`
                    }
                </td>
                <td class="py-3 px-4 text-xs text-green-600">${p.start_date || "—"}</td>
                <td class="py-3 px-4 text-xs text-green-600">${p.end_date || "—"}</td>
                <td class="py-3 px-4 text-center">
                    ${p.featured == 1 
                        ? `<span class="text-yellow-600 text-lg">★</span>`
                        : `<span class="text-slate-300">—</span>`
                    }
                </td>
                <td class="py-3 px-4">
                    <div class="flex items-center justify-center gap-3">
                        <button onclick="openProjectModal('edit', ${p.id})"
                            class="px-2 py-1 rounded-lg bg-green-100 text-green-700 hover:bg-green-200 flex items-center gap-1">
                            <i class="fa-solid fa-pen text-sm"></i>
                            <span class="text-xs font-semibold">Edit</span>
                        </button>

                        <button onclick="deleteProject(${p.id})"
                            class="px-2 py-1 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 flex items-center gap-1">
                            <i class="fa-solid fa-trash text-sm"></i>
                            <span class="text-xs font-semibold">Delete</span>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

    } catch (err) {
        console.error(err);
        tbody.innerHTML = `<tr><td colspan="9" class="py-8 text-center text-red-500">Error loading projects</td></tr>`;
    }
}

// ------------------------------------------------------------------
// Delete project
// ------------------------------------------------------------------
async function deleteProject(id) {
    if (!confirm("Delete this project?")) return;

    try {
        const res = await fetch(`sections/projects_delete.php?id=${id}`);
        if (res.ok) loadProjects();
        else alert("Failed to delete.");
    } catch (err) {
        alert("Error deleting project.");
    }
}

// ------------------------------------------------------------------
// Submit Add/Edit form
// ------------------------------------------------------------------
document.getElementById("projectsForm").addEventListener("submit", async (e) => {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    try {
        const res = await fetch(form.action, { method: "POST", body: formData });
        if (!res.ok) throw new Error("Failed to save project");

        closeProjectModal();
        loadProjects();
    } catch (err) {
        alert(err.message);
    }
});

// Initial load
loadProjects();
</script>
