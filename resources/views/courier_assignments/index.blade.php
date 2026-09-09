@extends('layouts.app')

@section('title', 'Manifes Penugasan Kurir')

@section('content')
<div class="space-y-6">
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Manifes Penugasan Kurir</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola penugasan kurir untuk Pickup, Delivery, dan Transfer Antar Hub Logistik.</p>
        </div>
        <button onclick="document.getElementById('newAssignmentModal').classList.remove('hidden'); filterShipmentsByCourierHub();" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition flex items-center">
            <i class="fa-solid fa-plus mr-1.5"></i> Buat Penugasan Baru
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-list-check text-indigo-600 mr-1.5"></i> Riwayat Penugasan & Manifes
            </h3>
            <form method="GET" action="{{ route('courier-assignments.index') }}" class="flex flex-wrap items-center gap-2">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="🔍 No Manifes / Kurir / Plat..." class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 w-52 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                
                <select name="assignment_type" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="">Semua Tipe Tugas</option>
                    <option value="pickup" {{ $assignmentType == 'pickup' ? 'selected' : '' }}>Pickup</option>
                    <option value="delivery" {{ $assignmentType == 'delivery' ? 'selected' : '' }}>Delivery</option>
                    <option value="transfer" {{ $assignmentType == 'transfer' ? 'selected' : '' }}>Transfer Antar Hub</option>
                </select>

                <select name="status" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="">Semua Status</option>
                    <option value="assigned" {{ $status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <input type="date" name="date" value="{{ $date ?? '' }}" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">

                <div class="flex items-center space-x-1">
                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition">
                        Filter
                    </button>
                    @if($search || $status || $assignmentType || $courierId || $date)
                        <a href="{{ route('courier-assignments.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs hover:bg-slate-200">
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
                        <th class="p-4">Tipe Tugas & Rute Hub</th>
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
                                @if($asn->assignment_type === 'pickup' && $asn->destinationHub)
                                    <div class="text-[11px] text-indigo-600 font-semibold mt-1">
                                        <i class="fa-solid fa-arrow-right-to-bracket text-indigo-400 mr-1"></i> Ke: {{ $asn->destinationHub->name }}
                                    </div>
                                @elseif($asn->assignment_type === 'transfer')
                                    <div class="text-[11px] text-cyan-800 font-bold mt-1">
                                        <i class="fa-solid fa-route text-cyan-600 mr-1"></i> {{ $asn->originHub ? $asn->originHub->name : 'Hub Asal' }} &rarr; {{ $asn->destinationHub ? $asn->destinationHub->name : 'Hub Tujuan' }}
                                    </div>
                                @elseif($asn->assignment_type === 'delivery' && $asn->originHub)
                                    <div class="text-[11px] text-emerald-700 font-semibold mt-1">
                                        <i class="fa-solid fa-warehouse text-emerald-500 mr-1"></i> Hub: {{ $asn->originHub->name }}
                                    </div>
                                @endif
                            </td>
                            <td class="p-4 text-slate-700">{{ $asn->assignment_date->format('d M Y') }}</td>
                            <td class="p-4 font-bold text-slate-900">{{ count($asn->items) }} Resi</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $asn->status == 'completed' ? 'bg-emerald-100 text-emerald-800' : ($asn->status == 'in_progress' ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ str_replace('_', ' ', $asn->status) }}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-1 whitespace-nowrap">
                                @if($asn->status === 'in_progress')
                                    <form action="{{ route('courier-assignments.complete', $asn->id) }}" method="POST" class="inline" onsubmit="return confirm('Selesaikan penugasan {{ $asn->assignment_number }} dan update status semua resi ke In-Hub?')">
                                        @csrf
                                        <button type="submit" class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 font-bold" title="Selesaikan Manifes (Paket Tiba di Hub / In-Hub)">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </button>
                                    </form>
                                @endif
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
                            <td colspan="8" class="p-8 text-center text-slate-400 font-semibold">
                                Tidak ada manifes penugasan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assignments->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $assignments->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Create Baru -->
<div id="newAssignmentModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-2xl max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Buat Manifes Penugasan Kurir Baru</h3>
                    <p class="text-[11px] text-slate-500">Pilih kurir, armada, tipe tugas, serta rute hub operasional</p>
                </div>
            </div>
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
                        <option value="">-- Pilih Armada Kendaraan --</option>
                        @foreach($vehicles as $vh)
                            <option value="{{ $vh->id }}">{{ $vh->plate_number }} ({{ $vh->vehicle_type }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe Tugas *</label>
                    <select name="assignment_type" id="assignmentTypeSelect" onchange="filterShipmentsByCourierHub()" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
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

            <!-- Dynamic Hub Section based on Tipe Tugas (Pickup & Transfer) -->
            <div id="assignmentHubSection" class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                <!-- Tipe Pickup: Hub Tujuan Setor Hasil Pickup -->
                <div id="pickupHubDiv" class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        <i class="fa-solid fa-warehouse text-indigo-600 mr-1"></i> Hub Tujuan (Gudang Setor / Sorting Hasil Pickup) *
                    </label>
                    <select name="destination_hub_id" id="pickupDestHubSelect" onchange="filterShipmentsByCourierHub()" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        <option value="">-- Pilih Hub Tujuan Setor --</option>
                        @foreach($hubs as $hb)
                            <option value="{{ $hb->id }}">{{ $hb->name }} ({{ $hb->city }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tipe Transfer: Hub Dari (Asal) dan Hub Ke (Tujuan) -->
                <div id="transferHubDiv" class="grid grid-cols-1 sm:grid-cols-2 gap-3 hidden">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            <i class="fa-solid fa-plane-departure text-cyan-600 mr-1"></i> Hub Dari (Asal Transfer) *
                        </label>
                        <select name="origin_hub_id" id="transferOriginHubSelect" onchange="this.dataset.userModified='true'; filterShipmentsByCourierHub()" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                            <option value="">-- Pilih Hub Asal --</option>
                            @foreach($hubs as $hb)
                                <option value="{{ $hb->id }}">{{ $hb->name }} ({{ $hb->city }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            <i class="fa-solid fa-plane-arrival text-emerald-600 mr-1"></i> Hub Ke (Tujuan Transfer) *
                        </label>
                        <select name="destination_hub_id" id="transferDestHubSelect" onchange="filterShipmentsByCourierHub()" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                            <option value="">-- Pilih Hub Tujuan --</option>
                            @foreach($hubs as $hb)
                                <option value="{{ $hb->id }}">{{ $hb->name }} ({{ $hb->city }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tipe Delivery: Filter Hub Pengantaran / Asal Paket Delivery -->
                <div id="deliveryHubDiv" class="space-y-1 hidden">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            <i class="fa-solid fa-warehouse text-emerald-600 mr-1"></i> Filter Hub Pengantaran (Gudang Asal Paket)
                        </label>
                        <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                            Saring Paket Delivery
                        </span>
                    </div>
                    <select name="origin_hub_id" id="deliveryHubSelect" onchange="this.dataset.userModified='true'; filterShipmentsByCourierHub()" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Semua Hub / Tampilkan Seluruh Paket Siap Antar --</option>
                        @foreach($hubs as $hb)
                            <option value="{{ $hb->id }}">{{ $hb->name }} ({{ $hb->city }})</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-500">Pilih hub gudang untuk menyaring paket delivery yang berada di hub ini.</p>
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

                <div class="max-h-64 overflow-y-auto border border-slate-200 rounded-xl p-3 space-y-2 bg-slate-50" id="shipmentListContainer">
                    @forelse($unassignedShipments as $s)
                        @php
                            $hasArrivedAtDest = ($s->destination_hub_id && $s->current_hub_id && $s->current_hub_id == $s->destination_hub_id);
                        @endphp
                        <label class="shipment-item flex items-center justify-between text-xs text-slate-800 font-semibold cursor-pointer p-2.5 rounded-xl bg-white border border-slate-200/90 hover:border-indigo-400 hover:shadow-sm transition"
                               data-status="{{ $s->status }}"
                               data-origin-hub="{{ $s->origin_hub_id }}"
                               data-dest-hub="{{ $s->destination_hub_id }}"
                               data-current-hub="{{ $s->current_hub_id }}"
                               data-has-arrived-dest="{{ $hasArrivedAtDest ? 'true' : 'false' }}">
                            <div class="flex items-center space-x-3 overflow-hidden">
                                <input type="checkbox" name="shipment_ids[]" value="{{ $s->id }}" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
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
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs font-semibold">
                            Tidak ada resi/paket yang siap ditugaskan saat ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Catatan Instruksi Tugas</label>
                <input type="text" name="notes" placeholder="Contoh: Jemput paket jam 14:00 atau Langsung kirim prioritas..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
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

        const typeSelect = document.getElementById('assignmentTypeSelect');
        const assignmentType = typeSelect ? typeSelect.value : 'pickup';

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

        // Toggle Hub Section for Pickup vs Transfer vs Delivery
        const hubSection = document.getElementById('assignmentHubSection');
        const pickupHubDiv = document.getElementById('pickupHubDiv');
        const transferHubDiv = document.getElementById('transferHubDiv');
        const deliveryHubDiv = document.getElementById('deliveryHubDiv');
        const pickupDestHub = document.getElementById('pickupDestHubSelect');
        const transferOriginHub = document.getElementById('transferOriginHubSelect');
        const transferDestHub = document.getElementById('transferDestHubSelect');
        const deliveryHubSelect = document.getElementById('deliveryHubSelect');

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
                    // Auto-select courier's default hub if not modified by user
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
            if (deliveryHubSelect && !deliveryHubSelect.disabled) {
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

        const items = document.querySelectorAll('#shipmentListContainer .shipment-item');
        let visibleCount = 0;

        items.forEach(item => {
            const originHub = item.getAttribute('data-origin-hub');
            const destHub = item.getAttribute('data-dest-hub');
            const currentHub = item.getAttribute('data-current-hub');
            const status = item.getAttribute('data-status');

            // Posisi fisik hub paket saat ini: current_hub jika ada, atau origin_hub jika belum bergerak
            const effectiveHub = currentHub ? currentHub : originHub;
            // Paket sudah sampai di hub tujuan jika current_hub sama dengan dest_hub
            const hasArrivedAtDest = Boolean(destHub && currentHub && currentHub == destHub);

            // Filter status based on assignment_type
            let statusMatch = false;
            if (assignmentType === 'pickup') {
                // Pickup: hanya AWB yang belum di-pickup (status pending atau draft)
                statusMatch = (status === 'pending' || status === 'draft');
            } else if (assignmentType === 'transfer') {
                // Transfer Antar Hub: hanya AWB dengan status in-hub (picked_up atau in_sorting_hub)
                statusMatch = (status === 'picked_up' || status === 'in_sorting_hub');
            } else if (assignmentType === 'delivery') {
                // Delivery: paket siap diantar ke penerima (in_transit, in_sorting_hub, picked_up)
                statusMatch = (status === 'in_transit' || status === 'in_sorting_hub' || status === 'picked_up');
            } else {
                statusMatch = true;
            }

            let hubMatch = false;
            if (showAll) {
                // Jika "Tampilkan Semua Hub" dicentang:
                // Transfer: paket yang sudah sampai di hub tujuan tidak boleh ditransfer lagi
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
                    // Jika Hub Tujuan Transfer dipilih di form, pastikan paket tujuannya sesuai
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
                    // Jika Semua Hub dipilih pada dropdown delivery hub
                    hubMatch = true;
                }
            } else if (assignmentType === 'pickup') {
                // Pickup: paket dijemput dari pengirim di area hub asal kurir
                hubMatch = (!activeHubId || originHub == activeHubId) && !hasArrivedAtDest;
            } else {
                hubMatch = (!activeHubId || effectiveHub == activeHubId);
            }

            if (statusMatch && hubMatch) {
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
            const typeLabel = assignmentType === 'pickup' ? 'PICKUP (Belum Picked Up)' : (assignmentType === 'transfer' ? 'TRANSFER (Status In-Hub)' : 'DELIVERY (Pengantaran Ke Penerima)');
            if (showAll) {
                badge.innerHTML = `<i class="fa-solid fa-globe text-slate-600 mr-1.5"></i> Tipe [<strong>${typeLabel}</strong>]: Menampilkan <strong>${visibleCount} paket AWB</strong> dari semua Gudang Hub.`;
            } else if (assignmentType === 'delivery') {
                if (activeHubId && activeHubName && activeHubId !== "") {
                    badge.innerHTML = `<i class="fa-solid fa-filter text-emerald-600 mr-1.5"></i> Terfilter [<strong>${typeLabel}</strong>]: <strong>${visibleCount} paket AWB</strong> di Gudang Hub: <strong>${activeHubName}</strong>`;
                } else {
                    badge.innerHTML = `<i class="fa-solid fa-warehouse text-emerald-600 mr-1.5"></i> Tipe [<strong>${typeLabel}</strong>]: Menampilkan <strong>${visibleCount} paket AWB</strong> (Semua Hub).`;
                }
            } else if (assignmentType === 'transfer') {
                if (activeHubId && activeHubName && activeHubId !== "") {
                    badge.innerHTML = `<i class="fa-solid fa-plane-departure text-cyan-600 mr-1.5"></i> Terfilter [<strong>${typeLabel}</strong>]: <strong>${visibleCount} paket AWB</strong> siap transfer dari Hub: <strong>${activeHubName}</strong>`;
                } else {
                    badge.innerHTML = `<i class="fa-solid fa-plane-departure text-cyan-600 mr-1.5"></i> Tipe [<strong>${typeLabel}</strong>]: Menampilkan <strong>${visibleCount} paket AWB</strong> siap transfer.`;
                }
            } else if (hubId && hubName) {
                badge.innerHTML = `<i class="fa-solid fa-filter text-indigo-600 mr-1.5"></i> Terfilter [<strong>${typeLabel}</strong>]: <strong>${visibleCount} paket AWB</strong> cocok dengan Gudang Hub Kurir: <strong>${hubName}</strong>`;
            } else {
                badge.innerHTML = `<i class="fa-solid fa-circle-info text-slate-500 mr-1.5"></i> Tipe [<strong>${typeLabel}</strong>]: Menampilkan <strong>${visibleCount} paket AWB</strong>. Pilih Kurir untuk menyaring otomatis sesuai Hub.`;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        filterShipmentsByCourierHub();
    });
</script>
@endpush
@endsection
