@extends('layouts.app')

@section('title', 'Penugasan Kurir & Dispatch')

@section('content')
<div class="space-y-6">
    <!-- Action Bar -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Penugasan Kurir & Manifes Dispatch</h2>
            <p class="text-xs text-slate-500 mt-0.5">Assign armada dan kurir untuk tugas Penjemputan / Pengantaran paket.</p>
        </div>
        <button onclick="document.getElementById('newAssignmentModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
            <i class="fa-solid fa-plus mr-1.5"></i> Buat Penugasan Baru
        </button>
    </div>

    <!-- Active Assignments Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-clipboard-check text-indigo-600 mr-2"></i> Daftar Manifes Penugasan Kurir
            </h3>

            <form method="GET" action="{{ route('courier-assignments.index') }}" class="flex flex-wrap items-center gap-2 text-xs">
                <select name="courier_id" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-xl border border-slate-300 font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">-- Semua Kurir --</option>
                    @foreach($allCouriers as $c)
                        <option value="{{ $c->id }}" {{ ($courierId ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>

                <select name="assignment_type" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-xl border border-slate-300 font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">-- Tipe Tugas --</option>
                    <option value="pickup" {{ ($assignmentType ?? '') == 'pickup' ? 'selected' : '' }}>Pickup</option>
                    <option value="delivery" {{ ($assignmentType ?? '') == 'delivery' ? 'selected' : '' }}>Delivery</option>
                    <option value="transfer" {{ ($assignmentType ?? '') == 'transfer' ? 'selected' : '' }}>Transfer Antar Hub</option>
                </select>

                <select name="status" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-xl border border-slate-300 font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">-- Semua Status --</option>
                    <option value="assigned" {{ ($status ?? '') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="in_progress" {{ ($status ?? '') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ ($status ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ ($status ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <input type="date" name="date" value="{{ $date ?? '' }}" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-xl border border-slate-300 font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">

                <div class="flex items-center space-x-1.5">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="🔍 Cari manifes/kurir/plat..." class="px-3 py-1.5 rounded-xl border border-slate-300 font-semibold text-slate-800 w-44 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition">
                        Filter
                    </button>
                    @if(!empty($search) || !empty($status) || !empty($assignmentType) || !empty($courierId) || !empty($date))
                        <a href="{{ route('courier-assignments.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-4">No. Manifes Penugasan</th>
                        <th class="p-4">Kurir & Hub Assigned</th>
                        <th class="p-4">Armada Kendaraan</th>
                        <th class="p-4">Tipe Tugas</th>
                        <th class="p-4">Tanggal</th>
                        <th class="p-4">Total Paket</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($assignments as $asn)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-bold text-indigo-600">
                                <a href="{{ route('courier-assignments.show', $asn->id) }}">{{ $asn->assignment_number }}</a>
                            </td>
                            <td class="p-4 text-slate-900">
                                <div class="font-bold">{{ $asn->courier ? $asn->courier->name : 'Kurir' }}</div>
                                <div class="text-[11px] text-slate-500"><i class="fa-solid fa-warehouse text-slate-400 mr-1"></i> {{ $asn->courier && $asn->courier->branchHub ? $asn->courier->branchHub->name : 'Semua Hub' }}</div>
                            </td>
                            <td class="p-4 font-bold text-slate-700">
                                {{ $asn->vehicle ? $asn->vehicle->plate_number . ' (' . $asn->vehicle->vehicle_type . ')' : '-' }}
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase {{ $asn->assignment_type == 'delivery' ? 'bg-indigo-100 text-indigo-800' : ($asn->assignment_type == 'transfer' ? 'bg-cyan-100 text-cyan-800' : 'bg-purple-100 text-purple-800') }}">
                                    {{ $asn->assignment_type == 'transfer' ? 'Transfer Hub' : $asn->assignment_type }}
                                </span>
                            </td>
                            <td class="p-4 text-slate-700">{{ $asn->assignment_date->format('d M Y') }}</td>
                            <td class="p-4 font-bold text-slate-900">{{ count($asn->items) }} Resi</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-100 text-amber-800">
                                    {{ $asn->status }}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-1">
                                <a href="{{ route('courier-assignments.show', $asn->id) }}" class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold" title="Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('courier-assignments.edit', $asn->id) }}" class="p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 font-bold" title="Edit Manifes">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="{{ route('courier-assignments.manifest', $asn->id) }}" target="_blank" class="p-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold" title="Cetak Manifes">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">Belum ada penugasan kurir.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $assignments->links() }}
        </div>
    </div>
</div>

<!-- Modal Buat Penugasan Baru -->
<div id="newAssignmentModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm">Buat Manifes Penugasan Kurir Baru</h3>
            <button onclick="document.getElementById('newAssignmentModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('courier-assignments.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Kurir *</label>
                    <select name="courier_id" id="assignmentCourierSelect" onchange="filterShipmentsByCourierHub()" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        <option value="">-- Pilih Kurir --</option>
                        @foreach($couriers as $cr)
                            <option value="{{ $cr->id }}" 
                                    data-hub-id="{{ $cr->branch_hub_id }}" 
                                    data-hub-name="{{ $cr->branchHub ? $cr->branchHub->name : '' }}"
                                    data-vehicle-id="{{ $cr->vehicle_id }}"
                                    data-vehicle-plate="{{ $cr->vehicle ? $cr->vehicle->plate_number . ' (' . $cr->vehicle->vehicle_type . ')' : '' }}">
                                {{ $cr->name }} ({{ $cr->courier_code }}) - {{ $cr->branchHub ? $cr->branchHub->name : 'Semua Hub' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Armada Kendaraan *</label>
                    <select name="vehicle_id" id="assignmentVehicleSelect" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        <option value="">-- Sesuai Bawaan Master Kurir --</option>
                        @foreach($vehicles as $vh)
                            <option value="{{ $vh->id }}">{{ $vh->plate_number }} ({{ $vh->vehicle_type }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe Tugas *</label>
                    <select name="assignment_type" onchange="filterShipmentsByCourierHub()" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        <option value="pickup">Pickup (Penjemputan Paket / DO dari Pengirim)</option>
                        <option value="delivery">Delivery (Pengantaran Ke Penerima)</option>
                        <option value="transfer">Transfer Antar Hub (Linehaul / Port to Port)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Tugas *</label>
                    <input type="date" name="assignment_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Pilih DO / Resi Pengiriman Yang Ditugaskan *</label>
                    <div class="flex items-center space-x-3">
                        <label class="text-[11px] font-bold text-indigo-700 cursor-pointer flex items-center space-x-1 hover:text-indigo-900 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-200">
                            <input type="checkbox" id="checkAllShipments" onchange="toggleCheckAllShipments(this)" class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span><i class="fa-solid fa-check-double mr-1"></i>Pilih Semua</span>
                        </label>
                        <label class="text-[11px] font-semibold text-slate-500 cursor-pointer flex items-center space-x-1">
                            <input type="checkbox" id="showAllHubsCheckbox" onchange="filterShipmentsByCourierHub()" class="rounded text-indigo-600">
                            <span>Tampilkan Semua Hub</span>
                        </label>
                    </div>
                </div>

                <!-- Hub Filter Status Badge -->
                <div id="hubFilterBadge" class="p-2.5 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-900 text-[11px] font-bold mb-2">
                    <i class="fa-solid fa-filter text-indigo-600 mr-1.5"></i> Silakan pilih kurir untuk menyaring DO dan resi AWB.
                </div>

                <div class="max-h-56 overflow-y-auto border border-slate-200 rounded-xl p-3 space-y-2 bg-slate-50" id="shipmentListContainer">
                    @forelse($unassignedShipments as $s)
                        <label class="shipment-item flex items-center justify-between text-xs text-slate-800 font-semibold cursor-pointer p-2 rounded-lg bg-white border border-slate-100 hover:border-indigo-300 transition"
                               data-status="{{ $s->status }}"
                               data-origin-hub="{{ $s->origin_hub_id }}"
                               data-dest-hub="{{ $s->destination_hub_id }}"
                               data-current-hub="{{ $s->current_hub_id }}">
                            <div class="flex items-center space-x-2.5 overflow-hidden">
                                <input type="checkbox" name="shipment_ids[]" value="{{ $s->id }}" class="rounded text-indigo-600 focus:ring-indigo-500">
                                <div class="truncate">
                                    <div class="flex items-center space-x-2">
                                        @if($s->deliveryOrder)
                                            <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 font-mono text-[10px] font-bold">
                                                <i class="fa-solid fa-file-invoice mr-1"></i>{{ $s->deliveryOrder->do_number }}
                                            </span>
                                        @endif
                                        <span class="font-mono text-indigo-600 font-bold">{{ $s->tracking_number }}</span>
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase {{ $s->status == 'pending' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $s->status == 'pending' ? 'Pickup DO' : $s->status }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-600 mt-0.5 truncate">
                                        <span class="font-bold text-slate-800">Pengirim:</span> {{ $s->sender_name }} ({{ $s->sender_city }})
                                        &rarr; <span class="font-bold text-slate-800">Penerima:</span> {{ $s->recipient_name }} ({{ $s->recipient_city }})
                                    </div>
                                </div>
                            </div>
                            <span class="text-[10px] text-slate-400 font-normal flex-shrink-0 ml-2">
                                [{{ $s->originHub ? $s->originHub->name : 'Hub Asal' }} &rarr; {{ $s->destinationHub ? $s->destinationHub->name : 'Hub Tujuan' }}]
                            </span>
                        </label>
                    @empty
                        <div class="text-slate-400 text-xs text-center py-4">Belum ada paket/DO yang membutuhkan penugasan.</div>
                    @endforelse
                </div>
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('newAssignmentModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md">Terbitkan Manifes Penugasan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleCheckAllShipments(masterCheckbox) {
        const container = document.getElementById('shipmentListContainer');
        if (!container) return;

        const visibleItems = Array.from(container.querySelectorAll('.shipment-item')).filter(item => item.style.display !== 'none');
        visibleItems.forEach(item => {
            const cb = item.querySelector('input[name="shipment_ids[]"]');
            if (cb) cb.checked = masterCheckbox.checked;
        });
    }

    function filterShipmentsByCourierHub() {
        const courierSelect = document.getElementById('assignmentCourierSelect');
        if (!courierSelect) return;

        const selectedOpt = courierSelect.options[courierSelect.selectedIndex];
        const hubId = selectedOpt ? selectedOpt.getAttribute('data-hub-id') : null;
        const hubName = selectedOpt ? selectedOpt.getAttribute('data-hub-name') : null;
        const vehicleId = selectedOpt ? selectedOpt.getAttribute('data-vehicle-id') : null;
        const showAll = document.getElementById('showAllHubsCheckbox') ? document.getElementById('showAllHubsCheckbox').checked : false;

        // Automatically match Vehicle armada with selected Courier master data
        const vehicleSelect = document.getElementById('assignmentVehicleSelect');
        if (vehicleSelect && vehicleId && vehicleId !== "" && vehicleId !== "null") {
            vehicleSelect.value = vehicleId;
        }

        const items = document.querySelectorAll('#shipmentListContainer .shipment-item');
        let visibleCount = 0;

        items.forEach(item => {
            const originHub = item.getAttribute('data-origin-hub');
            const destHub = item.getAttribute('data-dest-hub');
            const currentHub = item.getAttribute('data-current-hub');

            if (showAll || !hubId || hubId === "" || originHub == hubId || destHub == hubId || currentHub == hubId) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
                const checkbox = item.querySelector('input[type="checkbox"]');
                if (checkbox && !checkbox.checked) {
                    checkbox.checked = false;
                }
            }
        });

        const badge = document.getElementById('hubFilterBadge');
        if (badge) {
            if (showAll) {
                badge.innerHTML = `<i class="fa-solid fa-globe text-slate-600 mr-1.5"></i> Menampilkan <strong>seluruh ${visibleCount} paket AWB</strong> dari semua Gudang Hub.`;
            } else if (hubId && hubName) {
                badge.innerHTML = `<i class="fa-solid fa-filter text-indigo-600 mr-1.5"></i> Terfilter otomatis: <strong>${visibleCount} paket AWB</strong> cocok dengan Gudang Hub Kurir: <strong>${hubName}</strong>`;
            } else {
                badge.innerHTML = `<i class="fa-solid fa-circle-info text-slate-500 mr-1.5"></i> Menampilkan ${visibleCount} paket AWB. Pilih Kurir untuk menyaring otomatis sesuai Hub.`;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        filterShipmentsByCourierHub();
    });
</script>
@endpush
@endsection
