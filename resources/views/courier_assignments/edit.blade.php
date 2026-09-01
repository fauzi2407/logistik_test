@extends('layouts.app')

@section('title', 'Edit Penugasan Kurir ' . $assignment->assignment_number)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Edit Manifes Penugasan Kurir</h2>
            <p class="text-xs text-indigo-600 font-mono font-semibold">{{ $assignment->assignment_number }}</p>
        </div>
        <a href="{{ route('courier-assignments.show', $assignment->id) }}" class="text-xs font-bold text-slate-500 hover:text-slate-700">
            <i class="fa-solid fa-arrow-left mr-1"></i> Batal
        </a>
    </div>

    <form action="{{ route('courier-assignments.update', $assignment->id) }}" method="POST" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Kurir *</label>
                <select name="courier_id" id="editAssignmentCourierSelect" onchange="filterShipmentsByCourierHubEdit()" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    @foreach($couriers as $cr)
                        <option value="{{ $cr->id }}" 
                                data-hub-id="{{ $cr->branch_hub_id }}" 
                                data-hub-name="{{ $cr->branchHub ? $cr->branchHub->name : '' }}"
                                data-vehicle-id="{{ $cr->vehicle_id }}"
                                {{ $assignment->courier_id == $cr->id ? 'selected' : '' }}>
                            {{ $cr->name }} ({{ $cr->courier_code }}) - {{ $cr->branchHub ? $cr->branchHub->name : 'Semua Hub' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Armada Kendaraan *</label>
                <select name="vehicle_id" id="editAssignmentVehicleSelect" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="">-- Sesuai Bawaan Master Kurir --</option>
                    @foreach($vehicles as $vh)
                        <option value="{{ $vh->id }}" {{ $assignment->vehicle_id == $vh->id ? 'selected' : '' }}>{{ $vh->plate_number }} ({{ $vh->vehicle_type }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe Tugas *</label>
                <select name="assignment_type" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="delivery" {{ $assignment->assignment_type == 'delivery' ? 'selected' : '' }}>Delivery (Pengantaran Ke Penerima)</option>
                    <option value="pickup" {{ $assignment->assignment_type == 'pickup' ? 'selected' : '' }}>Pickup (Penjemputan Paket dari Pengirim)</option>
                    <option value="transfer" {{ $assignment->assignment_type == 'transfer' ? 'selected' : '' }}>Transfer Antar Hub (Linehaul / Port to Port)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Tugas *</label>
                <input type="date" name="assignment_date" value="{{ old('assignment_date', $assignment->assignment_date->format('Y-m-d')) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Penugasan *</label>
                <select name="status" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="assigned" {{ $assignment->status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="in_progress" {{ $assignment->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ $assignment->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $assignment->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Pilih Resi Pengiriman Yang Ditugaskan *</label>
                <div class="flex items-center space-x-3">
                    <label class="text-[11px] font-bold text-indigo-700 cursor-pointer flex items-center space-x-1 hover:text-indigo-900 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-200">
                        <input type="checkbox" id="checkAllShipmentsEdit" onchange="toggleCheckAllShipmentsEdit(this)" class="rounded text-indigo-600 focus:ring-indigo-500">
                        <span><i class="fa-solid fa-check-double mr-1"></i>Pilih Semua</span>
                    </label>
                    <label class="text-[11px] font-semibold text-slate-500 cursor-pointer flex items-center space-x-1">
                        <input type="checkbox" id="showAllHubsCheckboxEdit" onchange="filterShipmentsByCourierHubEdit()" class="rounded text-indigo-600">
                        <span>Tampilkan Semua Hub</span>
                    </label>
                </div>
            </div>

            <!-- Hub Filter Status Badge -->
            <div id="hubFilterBadgeEdit" class="p-2.5 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-900 text-[11px] font-bold mb-2">
                <i class="fa-solid fa-filter text-indigo-600 mr-1.5"></i> Menyaring resi AWB sesuai Hub Gudang Kurir.
            </div>

            <div class="max-h-52 overflow-y-auto border border-slate-200 rounded-xl p-3 space-y-2 bg-slate-50" id="shipmentListContainerEdit">
                @foreach($unassignedShipments as $s)
                    @php
                        $isChecked = in_array($s->id, $assignedShipmentIds);
                    @endphp
                    <label class="shipment-item flex items-center space-x-2 text-xs text-slate-800 font-semibold cursor-pointer p-1 rounded hover:bg-white transition"
                           data-origin-hub="{{ $s->origin_hub_id }}"
                           data-dest-hub="{{ $s->destination_hub_id }}"
                           data-current-hub="{{ $s->current_hub_id }}"
                           data-checked="{{ $isChecked ? 'true' : 'false' }}">
                        <input type="checkbox" name="shipment_ids[]" value="{{ $s->id }}" {{ $isChecked ? 'checked' : '' }} class="rounded text-indigo-600">
                        <span class="font-mono text-indigo-600 font-bold">{{ $s->tracking_number }}</span>
                        <span class="text-slate-700">({{ $s->recipient_name }} - {{ $s->recipient_city }})</span>
                        <span class="text-[10px] text-slate-400 font-normal">
                            [{{ $s->originHub ? $s->originHub->name : 'Hub Asal' }} &rarr; {{ $s->destinationHub ? $s->destinationHub->name : 'Hub Tujuan' }}]
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Catatan</label>
            <input type="text" name="notes" value="{{ old('notes', $assignment->notes) }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
        </div>

        <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
            <a href="{{ route('courier-assignments.show', $assignment->id) }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</a>
            <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md">Simpan Perubahan Manifes</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function toggleCheckAllShipmentsEdit(masterCheckbox) {
        const container = document.getElementById('shipmentListContainerEdit');
        if (!container) return;

        const visibleItems = Array.from(container.querySelectorAll('.shipment-item')).filter(item => item.style.display !== 'none');
        visibleItems.forEach(item => {
            const cb = item.querySelector('input[name="shipment_ids[]"]');
            if (cb) cb.checked = masterCheckbox.checked;
        });
    }

    function filterShipmentsByCourierHubEdit() {
        const courierSelect = document.getElementById('editAssignmentCourierSelect');
        if (!courierSelect) return;

        const selectedOpt = courierSelect.options[courierSelect.selectedIndex];
        const hubId = selectedOpt ? selectedOpt.getAttribute('data-hub-id') : null;
        const hubName = selectedOpt ? selectedOpt.getAttribute('data-hub-name') : null;
        const vehicleId = selectedOpt ? selectedOpt.getAttribute('data-vehicle-id') : null;
        const showAll = document.getElementById('showAllHubsCheckboxEdit') ? document.getElementById('showAllHubsCheckboxEdit').checked : false;

        // Auto match vehicle with courier master data
        const vehicleSelect = document.getElementById('editAssignmentVehicleSelect');
        if (vehicleSelect && vehicleId && vehicleId !== "" && vehicleId !== "null") {
            vehicleSelect.value = vehicleId;
        }

        const items = document.querySelectorAll('#shipmentListContainerEdit .shipment-item');
        let visibleCount = 0;

        items.forEach(item => {
            const originHub = item.getAttribute('data-origin-hub');
            const destHub = item.getAttribute('data-dest-hub');
            const currentHub = item.getAttribute('data-current-hub');
            const isChecked = item.getAttribute('data-checked') === 'true';

            if (showAll || isChecked || !hubId || hubId === "" || originHub == hubId || destHub == hubId || currentHub == hubId) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        const badge = document.getElementById('hubFilterBadgeEdit');
        if (badge) {
            if (showAll) {
                badge.innerHTML = `<i class="fa-solid fa-globe text-slate-600 mr-1.5"></i> Menampilkan <strong>seluruh ${visibleCount} paket AWB</strong> dari semua Gudang Hub.`;
            } else if (hubId && hubName) {
                badge.innerHTML = `<i class="fa-solid fa-filter text-indigo-600 mr-1.5"></i> Terfilter otomatis: <strong>${visibleCount} paket AWB</strong> cocok dengan Gudang Hub Kurir: <strong>${hubName}</strong>`;
            } else {
                badge.innerHTML = `<i class="fa-solid fa-circle-info text-slate-500 mr-1.5"></i> Menampilkan ${visibleCount} paket AWB.`;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        filterShipmentsByCourierHubEdit();
    });
</script>
@endpush
@endsection
