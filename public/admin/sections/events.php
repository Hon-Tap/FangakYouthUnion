<?php
declare(strict_types=1);
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight text-emerald-900">Events Calendar</h1>
        <p class="text-slate-500 text-sm mt-1">Manage meetups, workshops, and community sessions.</p>
    </div>

    <button onclick="openEventModal('add')" 
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-green-800 hover:bg-green-900 text-white text-sm font-semibold rounded-xl shadow-lg transition-all">
        <i data-lucide="calendar-plus" class="w-5 h-5"></i>
        <span>Schedule New Event</span>
    </button>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[11px] tracking-widest">
                    <th class="py-4 px-6">Event Details</th>
                    <th class="py-4 px-6">Date & Time</th>
                    <th class="py-4 px-6">Location</th>
                    <th class="py-4 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="eventTableBody" class="divide-y divide-slate-100">
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
            tbody.innerHTML = `<tr><td colspan="4" class="py-12 text-center text-slate-400">No events scheduled.</td></tr>`;
            return;
        }

        tbody.innerHTML = rows.map(ev => `
            <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="py-4 px-6">
                    <div class="font-bold text-slate-800">${ev.title}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5 uppercase tracking-tighter font-semibold">REF ID: #${ev.id}</div>
                </td>
                <td class="py-4 px-6">
                    <div class="flex items-center gap-2 text-slate-600">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-emerald-500"></i>
                        <span class="font-medium">${ev.event_date}</span>
                    </div>
                </td>
                <td class="py-4 px-6">
                    <div class="flex items-center gap-2 text-slate-500">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                        <span>${ev.location}</span>
                    </div>
                </td>
                <td class="py-4 px-6">
                    <div class="flex justify-center gap-3">
                        <button onclick="openEventModal('edit', ${ev.id})" class="text-emerald-600 font-bold hover:underline">Edit</button>
                        <button onclick="deleteEvent(${ev.id})" class="text-rose-400 hover:text-rose-600 font-bold hover:underline">Cancel</button>
                    </div>
                </td>
            </tr>
        `).join('');
        lucide.createIcons();
    } catch (err) { console.error(err); }
}

async function deleteEvent(id) {
    if (!confirm("Delete this event?")) return;
    await fetch("sections/events_delete.php?id=" + id);
    loadEvents();
}
loadEvents();
</script>