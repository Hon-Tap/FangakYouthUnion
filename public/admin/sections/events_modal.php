<?php
// admin/sections/events_modal.php
declare(strict_types=1);
?>

<!-- OVERLAY -->
<div id="eventModalOverlay"
     onclick="closeEventModal()"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 hidden"></div>

<!-- MODAL -->
<div id="eventModal"
     class="fixed inset-0 z-50 hidden flex items-center justify-center">

    <div class="bg-white w-full max-w-3xl rounded-xl shadow-lg border border-slate-200 p-6 relative animate-fadeIn">

        <!-- Close / Exit Button -->
        <button onclick="closeEventModal()"
                class="absolute top-3 right-3 text-slate-500 hover:text-slate-700 text-xl">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <h2 id="eventModalTitle" class="text-xl font-bold text-green-800 mb-5">
            Add Event
        </h2>

        <form id="eventsForm"
              action="sections/events_save.php"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-5">

            <input type="hidden" name="id" id="event_id">

            <!-- Title -->
            <div>
                <label class="text-sm font-semibold text-green-700">Title</label>
                <input type="text" id="event_title" name="title"
                       class="w-full mt-1 px-3 py-2 border rounded-lg" required>
            </div>

            <!-- Dates + Status -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-sm font-semibold text-green-700">Event Date</label>
                    <input type="date" id="event_event_date" name="event_date"
                           class="w-full mt-1 px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="text-sm font-semibold text-green-700">Start</label>
                    <input type="date" id="event_start_date" name="start_date"
                           class="w-full mt-1 px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="text-sm font-semibold text-green-700">End</label>
                    <input type="date" id="event_end_date" name="end_date"
                           class="w-full mt-1 px-3 py-2 border rounded-lg">
                </div>
            </div>

            <!-- Status -->
            <div>
                <label class="text-sm font-semibold text-green-700">Status</label>
                <select id="event_status" name="status"
                        class="w-full mt-1 px-3 py-2 border rounded-lg">
                    <option value="Upcoming">Upcoming</option>
                    <option value="Ongoing">Ongoing</option>
                    <option value="Past">Past</option>
                </select>
            </div>

            <!-- Description -->
            <div>
                <label class="text-sm font-semibold text-green-700">Description</label>
                <textarea id="event_description" name="description" rows="4"
                          class="w-full mt-1 px-3 py-2 border rounded-lg"></textarea>
            </div>

            <!-- Location & Optional Project -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-semibold text-green-700">Location</label>
                    <input type="text" id="event_location" name="location"
                           class="w-full mt-1 px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="text-sm font-semibold text-green-700">Project ID (optional)</label>
                    <input type="number" id="event_project_id" name="project_id"
                           class="w-full mt-1 px-3 py-2 border rounded-lg"
                           placeholder="Project ID">
                </div>
            </div>

            <!-- Image -->
            <div>
                <label class="text-sm font-semibold text-green-700">Image</label>
                <input type="file" id="event_image" name="image" accept="image/*"
                       class="mt-1 border rounded-lg px-3 py-2 w-full">

                <div id="eventImagePreview" class="mt-3 hidden">
                    <img id="eventImagePreviewImg"
                         class="w-40 h-28 object-cover rounded-lg border shadow"
                         src="">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3">
                <button type="button"
                        onclick="openEventModal('add')"
                        class="px-4 py-2 border rounded-lg text-slate-600 hover:bg-slate-100">
                    Reset
                </button>

                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                    Save Event
                </button>
            </div>

        </form>
    </div>
</div>

<script>
// Open Modal
function openEventModal(type, id=null) {
    document.body.classList.add("overflow-hidden");
    eventModalOverlay.classList.remove("hidden");
    eventModal.classList.remove("hidden");

    const title = document.getElementById("eventModalTitle");
    const form  = document.getElementById("eventsForm");

    form.reset();
    event_id.value = "";
    eventImagePreview.classList.add("hidden");
    eventImagePreviewImg.src = "";

    if (type === "add") {
        title.textContent = "Add Event";
        return;
    }

    if (type === "edit") {
        title.textContent = "Edit Event";

        fetch("sections/events_list_ajax.php?id=" + id)
            .then(r => r.json())
            .then(ev => {
                if (!ev) return;

                event_id.value = ev.id;
                event_title.value = ev.title;
                event_event_date.value = ev.event_date || "";
                event_start_date.value = ev.start_date || "";
                event_end_date.value = ev.end_date || "";
                event_description.value = ev.description || "";
                event_location.value = ev.location || "";
                event_project_id.value = ev.project_id || "";
                event_status.value = ev.status || "Upcoming";

                if (ev.image) {
                    eventImagePreview.classList.remove("hidden");
                    eventImagePreviewImg.src = "/uploads/events/" + ev.image;
                }
            });
    }
}

// Close Modal
function closeEventModal() {
    eventModalOverlay.classList.add("hidden");
    eventModal.classList.add("hidden");
    document.body.classList.remove("overflow-hidden");
}

// Image Preview
event_image.addEventListener("change", function () {
    const file = this.files[0];
    if (file) {
        eventImagePreviewImg.src = URL.createObjectURL(file);
        eventImagePreview.classList.remove("hidden");
    }
});

// Submit Form (AJAX)
eventsForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const fd = new FormData(e.target);

    try {
        const res = await fetch(e.target.action, { method: "POST", body: fd });
        if (!res.ok) throw new Error("Failed to save event");

        closeEventModal();
        if (typeof loadEvents === "function") loadEvents();

    } catch (err) {
        alert(err.message);
        console.error(err);
    }
});
</script>
