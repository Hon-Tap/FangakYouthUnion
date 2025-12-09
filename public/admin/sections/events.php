<?php
// admin/sections/events.php
declare(strict_types=1);
?>

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-green-800">Events</h1>
        <p class="text-slate-500 text-sm mt-1">Post upcoming meetups and community events.</p>
    </div>

    <button onclick="openEventModal('add')"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Add Event
    </button>
</div>

<div class="bg-white shadow-md rounded-xl overflow-hidden border">
    <div class="overflow-x-auto">
        <table id="eventTable" class="min-w-full text-sm">
            <thead>
            <tr class="bg-green-50 text-green-800 uppercase text-xs font-semibold border-b">
                <th class="py-3 px-4">ID</th>
                <th class="py-3 px-4">Title</th>
                <th class="py-3 px-4">Event Date</th>
                <th class="py-3 px-4">Location</th>
                <th class="py-3 px-4 w-32 text-center">Actions</th>
            </tr>
            </thead>
            <tbody id="eventTableBody">
                <tr>
                    <td colspan="5" class="py-8 text-center text-slate-500">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . "/events_modal.php"; ?>

<script>
async function loadEvents() {
    const tbody = document.getElementById("eventTableBody");

    try {
        const res = await fetch("sections/events_list_ajax.php");
        const rows = await res.json();

        if (!rows.length) {
            tbody.innerHTML = `<tr><td colspan="5" class="py-8 text-center text-slate-500">No events yet.</td></tr>`;
            return;
        }

        tbody.innerHTML = rows.map(ev => `
            <tr class="border-b hover:bg-slate-50">
                <td class="py-3 px-4 font-bold">${ev.id}</td>
                <td class="py-3 px-4">${ev.title}</td>
                <td class="py-3 px-4">${ev.event_date}</td>
                <td class="py-3 px-4">${ev.location}</td>
                <td class="py-3 px-4">
                    <div class="flex justify-center gap-2">
                        <button onclick="openEventModal('edit', ${ev.id})"
                                class="px-2 py-1 bg-green-100 text-green-700 rounded-lg text-xs">Edit</button>
                        <button onclick="deleteEvent(${ev.id})"
                                class="px-2 py-1 bg-red-100 text-red-700 rounded-lg text-xs">Delete</button>
                    </div>
                </td>
            </tr>
        `).join('');

    } catch (err) {
        tbody.innerHTML = `<tr><td colspan="5" class="py-8 text-center text-red-500">Error loading events.</td></tr>`;
    }
}

async function deleteEvent(id) {
    if (!confirm("Delete this event?")) return;
    await fetch("sections/events_delete.php?id=" + id);
    loadEvents();
}

loadEvents();
</script>
