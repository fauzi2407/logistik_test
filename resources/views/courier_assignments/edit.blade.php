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
                    <option value="">-- Pilih Armada Kendaraan --</option>
                    @foreach($vehicles as $vh)
                        <option value="{{ $vh->id }}" {{ $assignment->vehicle_id == $vh->id ? 'selected' : '' }}>{{ $vh->plate_number }} ({{ $vh->vehicle_type }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe Tugas *</label>
                <select name="assignment_type" id="editAssignmentTypeSelect" onchange="filterShipmentsByCourierHubEdit()" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="pickup" {{ $assignment->assignment_type == 'pickup' ? 'selected' : '' }}>Pickup (Penjemputan Paket dari Pengirim)</option>
                    <option value="delivery" {{ $assignment->assignment_type == 'delivery' ? 'selected' : '' }}>Delivery (Pengantaran Ke Penerima)</option>
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

        <!-- Dynamic Hub Section based on Tipe Tugas (Pickup & Transfer) -->
        <div id="editAssignmentHubSection" class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
            <!-- Tipe Pickup: Hub Tujuan Setor Hasil Pickup -->
            <div id="editPickupHubDiv" class="space-y-1">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    <i class="fa-solid fa-warehouse text-indigo-600 mr-1"></i> Hub Tujuan (Gudang Setor / Sorting Hasil Pickup) *
                </label>
                <select name="destination_hub_id" id="editPickupDestHubSelect" onchange="filterShipmentsByCourierHubEdit()" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="">-- Pilih Hub Tujuan Setor --</option>
                    @foreach($hubs as $hb)
                        <option value="{{ $hb->id }}" {{ $assignment->destination_hub_id == $hb->id ? 'selected' : '' }}>{{ $hb->name }} ({{ $hb->city }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Tipe Transfer: Hub Dari (Asal) dan Hub Ke (Tujuan) -->
            <div id="editTransferHubDiv" class="grid grid-cols-1 sm:grid-cols-2 gap-3 hidden">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        <i class="fa-solid fa-plane-departure text-cyan-600 mr-1"></i> Hub Dari (Asal Transfer) *
                    </label>
                    <select name="origin_hub_id" id="editTransferOriginHubSelect" onchange="this.dataset.userModified='true'; filterShipmentsByCourierHubEdit()" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        <option value="">-- Pilih Hub Asal --</option>
                        @foreach($hubs as $hb)
                            <option value="{{ $hb->id }}" {{ $assignment->origin_hub_id == $hb->id ? 'selected' : '' }}>{{ $hb->name }} ({{ $hb->city }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        <i class="fa-solid fa-plane-arrival text-emerald-600 mr-1"></i> Hub Ke (Tujuan Transfer) *
                    </label>
                    <select name="destination_hub_id" id="editTransferDestHubSelect" onchange="filterShipmentsByCourierHubEdit()" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        <option value="">-- Pilih Hub Tujuan --</option>
                        @foreach($hubs as $hb)
                            <option value="{{ $hb->id }}" {{ $assignment->destination_hub_id == $hb->id ? 'selected' : '' }}>{{ $hb->name }} ({{ $hb->city }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tipe Delivery: Filter Hub Pengantaran / Asal Paket Delivery -->
            <div id="editDeliveryHubDiv" class="space-y-1 hidden">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        <i class="fa-solid fa-warehouse text-emerald-600 mr-1"></i> Filter Hub Pengantaran (Gudang Asal Paket Delivery)
                    </label>
                    <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                        Saring Paket Delivery
                    </span>
                </div>
                <select name="origin_hub_id" id="editDeliveryHubSelect" onchange="this.dataset.userModified='true'; filterShipmentsByCourierHubEdit()" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Hub / Tampilkan Seluruh Paket Siap Antar --</option>
                    @foreach($hubs as $hb)
                        <option value="{{ $hb->id }}" {{ $assignment->origin_hub_id == $hb->id ? 'selected' : '' }}>{{ $hb->name }} ({{ $hb->city }})</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-slate-500">Pilih hub gudang untuk menyaring paket delivery yang berada di hub ini.</p>
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

            <div class="max-h-64 overflow-y-auto border border-slate-200 rounded-xl p-3 space-y-2 bg-slate-50" id="shipmentListContainerEdit">
                @foreach($unassignedShipments as $s)
                    @php
                        $isChecked = in_array($s->id, $assignedShipmentIds);
                        $hasArrivedAtDest = ($s->destination_hub_id && $s->current_hub_id && $s->current_hub_id == $s->destination_hub_id);
                    @endphp
                    <label class="shipment-item flex items-center justify-between text-xs text-slate-800 font-semibold cursor-pointer p-2.5 rounded-xl bg-white border border-slate-200/90 hover:border-indigo-400 hover:shadow-sm transition"
                           data-status="{{ $s->status }}"
                           data-origin-hub="{{ $s->origin_hub_id }}"
                           data-dest-hub="{{ $s->destination_hub_id }}"
                           data-current-hub="{{ $s->current_hub_id }}"
                           data-has-arrived-dest="{{ $hasArrivedAtDest ? 'true' : 'false' }}"
                           data-checked="{{ $isChecked ? 'true' : 'false' }}">
                        <div class="flex items-center space-x-3 overflow-hidden">
                            <input type="checkbox" name="shipment_ids[]" value="{{ $s->id }}" {{ $isChecked ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                            <div class="truncate">
                                <div class="flex items-center flex-wrap gap-1.5">
                                    @if($s->deliveryOrder)
                                        <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 font-mono text-[10px] font-bold">
                                            <i class="fa-solid fa-file-invoice mr-0.5"></i>{{ $s->deliveryOrder->do_number }}
                                        </span>
                                    @endif
                                    <span class="font-mono text-indigo-700 font-extrabold text-xs">{{ $s->tracking_number }}</span>
                                    
                                    <!-- BADGE KOTA TUJUAN UTAMA -->
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-300 font-extrabold text-[11px] inline-flex items-center gap-1">
                                        <i class="fa-solid fa-location-dot text-emerald-600"></i>
                                        <span>Tujuan: {{ $s->recipient_city ?? '-' }}</span>
                                        @if($s->recipient_district)
                                            <span class="text-emerald-600 font-normal">({{ $s->recipient_district }})</span>
                                        @endif
                                    </span>

                                    <span class="px-1.5 py-0.2 rounded text-[10px] font-bold uppercase {{ $s->status === 'pending' || $s->status === 'draft' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ str_replace('_', ' ', $s->status) }}
                                    </span>

                                    @if($hasArrivedAtDest)
                                        <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <i class="fa-solid fa-check-circle mr-0.5"></i>Tiba di Hub Tujuan ({{ $s->destinationHub ? $s->destinationHub->name : 'Tujuan' }})
                                        </span>
                                    @elseif($s->currentHub && $s->current_hub_id != $s->origin_hub_id)
                                        <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-200">
                                            <i class="fa-solid fa-location-arrow mr-0.5"></i>Di {{ $s->currentHub->name }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-500 mt-1 flex items-center flex-wrap gap-2">
                                    <span><strong>Pengirim:</strong> {{ $s->sender_name }} ({{ $s->sender_city }})</span>
                                    <span class="text-slate-300">&bull;</span>
                                    <span><strong>Penerima:</strong> {{ $s->recipient_name }} - {{ $s->recipient_address }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right pl-3 shrink-0 flex flex-col items-end gap-1">
                            <span class="text-[11px] px-2 py-0.5 rounded-lg font-bold bg-indigo-50 text-indigo-800 border border-indigo-100">
                                {{ $s->weight_kg }} kg • {{ $s->service_type }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono">
                                {{ $s->originHub ? $s->originHub->name : 'Hub Asal' }} &rarr; {{ $s->destinationHub ? $s->destinationHub->name : 'Hub Tujuan' }}
                            </span>
                        </div>
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

        const typeSelect = document.getElementById('editAssignmentTypeSelect');
        const assignmentType = typeSelect ? typeSelect.value : 'pickup';

        const selectedOpt = courierSelect.options[courierSelect.selectedIndex];
        const hubId = selectedOpt ? selectedOpt.getAttribute('data-hub-id') : null;
        const hubName = selectedOpt ? selectedOpt.getAttribute('data-hub-name') : null;
        const vehicleId = selectedOpt ? selectedOpt.getAttribute('data-vehicle-id') : null;
        const showAll = document.getElementById('showAllHubsCheckboxEdit') ? document.getElementById('showAllHubsCheckboxEdit').checked : false;

        // Toggle Hub Section for Pickup vs Transfer vs Delivery
        const hubSection = document.getElementById('editAssignmentHubSection');
        const pickupHubDiv = document.getElementById('editPickupHubDiv');
        const transferHubDiv = document.getElementById('editTransferHubDiv');
        const deliveryHubDiv = document.getElementById('editDeliveryHubDiv');
        const pickupDestHub = document.getElementById('editPickupDestHubSelect');
        const transferOriginHub = document.getElementById('editTransferOriginHubSelect');
        const transferDestHub = document.getElementById('editTransferDestHubSelect');
        const deliveryHubSelect = document.getElementById('editDeliveryHubSelect');

        if (hubSection && pickupHubDiv && transferHubDiv) {
            if (assignmentType === 'pickup') {
                hubSection.classList.remove('hidden');
                pickupHubDiv.classList.remove('hidden');
                transferHubDiv.classList.add('hidden');
                if (deliveryHubDiv) deliveryHubDiv.classList.add('hidden');
                if (pickupDestHub) {
                    pickupDestHub.disabled = false;
                    if (hubId && !pickupDestHub.value) pickupDestHub.value = hubId;
                }
                if (transferOriginHub) transferOriginHub.disabled = true;
                if (transferDestHub) transferDestHub.disabled = true;
                if (deliveryHubSelect) deliveryHubSelect.disabled = true;
            } else if (assignmentType === 'transfer') {
                hubSection.classList.remove('hidden');
                pickupHubDiv.classList.add('hidden');
                transferHubDiv.classList.remove('hidden');
                if (deliveryHubDiv) deliveryHubDiv.classList.add('hidden');
                if (pickupDestHub) pickupDestHub.disabled = true;
                if (transferOriginHub) {
                    transferOriginHub.disabled = false;
                    if (hubId && !transferOriginHub.value) transferOriginHub.value = hubId;
                }
                if (transferDestHub) transferDestHub.disabled = false;
                if (deliveryHubSelect) deliveryHubSelect.disabled = true;
            } else if (assignmentType === 'delivery') {
                hubSection.classList.remove('hidden');
                pickupHubDiv.classList.add('hidden');
                transferHubDiv.classList.add('hidden');
                if (deliveryHubDiv) deliveryHubDiv.classList.remove('hidden');

                if (pickupDestHub) pickupDestHub.disabled = true;
                if (transferOriginHub) transferOriginHub.disabled = true;
                if (transferDestHub) transferDestHub.disabled = true;
                if (deliveryHubSelect) {
                    deliveryHubSelect.disabled = false;
                    if (hubId && !deliveryHubSelect.value && !deliveryHubSelect.dataset.userModified) {
                        deliveryHubSelect.value = hubId;
                    }
                }
            } else {
                hubSection.classList.add('hidden');
                if (pickupDestHub) pickupDestHub.disabled = true;
                if (transferOriginHub) transferOriginHub.disabled = true;
                if (transferDestHub) transferDestHub.disabled = true;
                if (deliveryHubSelect) deliveryHubSelect.disabled = true;
            }
        }

        // Determine active filter hub based on assignment type
        let activeHubId = hubId;
        let activeHubName = hubName;

        if (assignmentType === 'delivery') {
            if (deliveryHubSelect && !deliveryHubSelect.disabled && deliveryHubSelect.value) {
                activeHubId = deliveryHubSelect.value;
                activeHubName = deliveryHubSelect.options[deliveryHubSelect.selectedIndex]?.text;
            }
        } else if (assignmentType === 'transfer') {
            if (transferOriginHub && !transferOriginHub.disabled && transferOriginHub.value) {
                activeHubId = transferOriginHub.value;
                activeHubName = transferOriginHub.options[transferOriginHub.selectedIndex]?.text;
            }
        } else if (assignmentType === 'pickup') {
            activeHubId = hubId;
            activeHubName = hubName;
        }

        const items = document.querySelectorAll('#shipmentListContainerEdit .shipment-item');
        let visibleCount = 0;

        items.forEach(item => {
            const originHub = item.getAttribute('data-origin-hub');
            const destHub = item.getAttribute('data-dest-hub');
            const currentHub = item.getAttribute('data-current-hub');
            const status = item.getAttribute('data-status');
            const isChecked = item.getAttribute('data-checked') === 'true';

            // Posisi fisik hub paket saat ini: current_hub jika ada, atau origin_hub jika belum bergerak
            const effectiveHub = currentHub ? currentHub : originHub;
            // Paket sudah sampai di hub tujuan jika current_hub sama dengan dest_hub
            const hasArrivedAtDest = Boolean(destHub && currentHub && currentHub == destHub);

            // Filter status based on assignment_type
            let statusMatch = false;
            if (assignmentType === 'pickup') {
                statusMatch = (status === 'pending' || status === 'draft');
            } else if (assignmentType === 'transfer') {
                statusMatch = (status === 'picked_up' || status === 'in_sorting_hub');
            } else if (assignmentType === 'delivery') {
                statusMatch = (status === 'in_transit' || status === 'in_sorting_hub' || status === 'picked_up' || status === 'out_for_delivery');
            } else {
                statusMatch = true;
            }

            let hubMatch = false;
            if (isChecked) {
                // Item yang sudah tersimpan di tugas ini selalu ditampilkan agar tetap bisa diedit/dilihat
                hubMatch = true;
                statusMatch = true;
            } else if (showAll) {
                if (assignmentType === 'transfer') {
                    hubMatch = !hasArrivedAtDest;
                } else {
                    hubMatch = true;
                }
            } else if (hasArrivedAtDest && activeHubId && activeHubId == originHub && originHub != destHub) {
                // ATURAN UTAMA: Resi yang sudah sampai hub tujuan TIDAK BOLEH muncul lagi di hub asal!
                hubMatch = false;
            } else if (assignmentType === 'transfer') {
                const transferDestId = (transferDestHub && !transferDestHub.disabled && transferDestHub.value) ? transferDestHub.value : null;

                // Paket yang sudah sampai di hub tujuan tidak boleh ditransfer ulang
                if (hasArrivedAtDest) {
                    hubMatch = false;
                } else {
                    // Paket harus secara fisik berada di Hub Asal Transfer
                    const isAtOriginHub = (!activeHubId || effectiveHub == activeHubId);
                    const matchesDest = (!transferDestId || !destHub || destHub == transferDestId);
                    hubMatch = isAtOriginHub && matchesDest;
                }
            } else if (assignmentType === 'delivery') {
                if (activeHubId && activeHubId !== "") {
                    // Delivery ke penerima:
                    // 1. Paket harus secara fisik berada di hub pengantaran (effectiveHub == activeHubId)
                    // 2. Dan tujuan pengantaran paket adalah area hub ini (destHub == activeHubId, atau jika tanpa destHub maka originHub == activeHubId)
                    const isAtActiveHub = (effectiveHub == activeHubId);
                    const isDestActiveHub = destHub ? (destHub == activeHubId) : (originHub == activeHubId);
                    hubMatch = isAtActiveHub && isDestActiveHub;
                } else {
                    hubMatch = true;
                }
            } else if (assignmentType === 'pickup') {
                hubMatch = (!activeHubId || originHub == activeHubId) && !hasArrivedAtDest;
            } else {
                hubMatch = (!activeHubId || effectiveHub == activeHubId);
            }

            if (statusMatch && hubMatch) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        const badge = document.getElementById('hubFilterBadgeEdit');
        if (badge) {
            const typeLabel = assignmentType === 'pickup' ? 'PICKUP' : (assignmentType === 'transfer' ? 'TRANSFER' : 'DELIVERY');
            if (showAll) {
                badge.innerHTML = `<i class="fa-solid fa-globe text-slate-600 mr-1.5"></i> Tipe [<strong>${typeLabel}</strong>]: Menampilkan <strong>${visibleCount} resi</strong> dari semua Gudang Hub.`;
            } else if (assignmentType === 'delivery') {
                if (activeHubId && activeHubName && activeHubId !== "") {
                    badge.innerHTML = `<i class="fa-solid fa-filter text-emerald-600 mr-1.5"></i> Terfilter [<strong>${typeLabel}</strong>]: <strong>${visibleCount} resi</strong> di Gudang Hub: <strong>${activeHubName}</strong>`;
                } else {
                    badge.innerHTML = `<i class="fa-solid fa-warehouse text-emerald-600 mr-1.5"></i> Tipe [<strong>${typeLabel}</strong>]: Menampilkan <strong>${visibleCount} resi</strong> (Semua Hub).`;
                }
            } else if (assignmentType === 'transfer') {
                if (activeHubId && activeHubName && activeHubId !== "") {
                    badge.innerHTML = `<i class="fa-solid fa-plane-departure text-cyan-600 mr-1.5"></i> Terfilter [<strong>${typeLabel}</strong>]: <strong>${visibleCount} resi</strong> siap transfer dari Hub: <strong>${activeHubName}</strong>`;
                } else {
                    badge.innerHTML = `<i class="fa-solid fa-plane-departure text-cyan-600 mr-1.5"></i> Tipe [<strong>${typeLabel}</strong>]: Menampilkan <strong>${visibleCount} resi</strong> siap transfer.`;
                }
            } else if (hubId && hubName) {
                badge.innerHTML = `<i class="fa-solid fa-filter text-indigo-600 mr-1.5"></i> Terfilter [<strong>${typeLabel}</strong>]: <strong>${visibleCount} resi</strong> cocok dengan Gudang Hub Kurir: <strong>${hubName}</strong>`;
            } else {
                badge.innerHTML = `<i class="fa-solid fa-circle-info text-slate-500 mr-1.5"></i> Menampilkan <strong>${visibleCount} resi</strong>. Pilih Kurir untuk menyaring otomatis sesuai Hub.`;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        filterShipmentsByCourierHubEdit();
    });
</script>
@endpush
@endsection
