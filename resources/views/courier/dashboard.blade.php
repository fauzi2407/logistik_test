@extends('layouts.app')

@section('title', 'Portal Tugas Kurir & Driver')

@section('content')
<div class="max-w-4xl mx-auto space-y-4 md:space-y-6">
    <!-- Courier Profile Banner (Mobile Optimized) -->
    <div class="p-5 md:p-6 rounded-2xl md:rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5 w-full sm:w-auto">
            <div class="w-14 h-14 rounded-2xl bg-indigo-600/40 border border-indigo-400/30 flex items-center justify-center text-indigo-300 text-2xl font-bold shrink-0 shadow-inner">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        {{ $courier ? strtoupper($courier->status) : 'ON DUTY' }}
                    </span>
                    <span class="text-xs font-mono font-bold text-indigo-300">{{ $courier->courier_code ?? 'KUR-001' }}</span>
                </div>
                <h2 class="text-lg md:text-xl font-extrabold text-white tracking-tight mt-0.5 truncate">{{ Auth::user()->name }}</h2>
                <p class="text-[11px] text-slate-400 truncate">
                    Armada: <span class="font-bold text-slate-200">{{ $courier && $courier->vehicle ? $courier->vehicle->plate_number . ' (' . $courier->vehicle->vehicle_type . ')' : 'Kendaraan Pribadi' }}</span>
                    @if($courier && $courier->branchHub)
                        • Hub: <span class="font-bold text-indigo-300">{{ $courier->branchHub->name }}</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="w-full sm:w-auto bg-white/10 sm:bg-transparent p-3 sm:p-0 rounded-xl flex sm:flex-col justify-between items-center sm:items-end gap-1">
            <div>
                <div class="text-[11px] text-slate-300 sm:text-slate-400 uppercase tracking-wider font-semibold">Total Resi:</div>
                <div class="text-xl sm:text-2xl font-black text-emerald-400">{{ count($assignedShipments) }} Paket</div>
            </div>
            @php
                $deliveredCount = $assignedShipments->where('status', 'delivered')->count();
                $ratePerPackage = $courier->commission_per_delivery ?? 5000;
                $totalCommission = $deliveredCount * $ratePerPackage;
            @endphp
            <div class="text-right">
                <div class="text-[10px] text-slate-400 uppercase font-bold">Estimasi Komisi ePOD:</div>
                <div class="text-xs font-black text-amber-300 font-mono">Rp {{ number_format($totalCommission, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Quick Shortcuts for Courier (Kasbon & Payroll) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <a href="{{ route('courier-cash-advances.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm hover:border-amber-300 hover:shadow-md transition flex items-center justify-between group">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg group-hover:scale-110 transition">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black text-slate-800 uppercase tracking-tight">Kasbon Kurir</h4>
                    <p class="text-[11px] text-slate-500">Ajukan permohonan kasbon baru</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-amber-100 text-amber-800">
                Ajukan <i class="fa-solid fa-arrow-right ml-1"></i>
            </span>
        </a>

        <a href="{{ route('courier-payrolls.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm hover:border-indigo-300 hover:shadow-md transition flex items-center justify-between group">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg group-hover:scale-110 transition">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black text-slate-800 uppercase tracking-tight">Slip Gaji & Komisi</h4>
                    <p class="text-[11px] text-slate-500">Cek rekapitulasi gaji lunas</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-indigo-100 text-indigo-800">
                Lihat <i class="fa-solid fa-arrow-right ml-1"></i>
            </span>
        </a>
    </div>

    <!-- MANIFES PENUGASAN KURIR SECTION (MANIFES AKTIF & SELESAI) -->
    @if(isset($myAssignments) && $myAssignments->isNotEmpty())
        @php
            $activeManifests = $myAssignments->where('status', '!=', 'completed')->where('status', '!=', 'cancelled');
            $completedManifests = $myAssignments->where('status', 'completed');
        @endphp

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                <div class="flex items-center space-x-2">
                    <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-xs uppercase tracking-wider">Manifes Penugasan Saya</h3>
                        <p class="text-[10px] text-slate-500">{{ count($activeManifests) }} Manifes Aktif • {{ count($completedManifests) }} Manifes Selesai</p>
                    </div>
                </div>
            </div>

            <!-- Active Manifests Cards -->
            @if($activeManifests->isNotEmpty())
                <div class="space-y-2.5">
                    @foreach($activeManifests as $asn)
                        <div class="p-3.5 rounded-xl bg-indigo-50/60 border border-indigo-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center flex-wrap gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider {{ $asn->assignment_type == 'delivery' ? 'bg-indigo-100 text-indigo-800' : ($asn->assignment_type == 'transfer' ? 'bg-cyan-100 text-cyan-800' : 'bg-purple-100 text-purple-800') }}">
                                        {{ $asn->assignment_type == 'transfer' ? 'Transfer Hub' : $asn->assignment_type }}
                                    </span>
                                    <span class="font-mono font-bold text-indigo-900 text-xs">{{ $asn->assignment_number }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-amber-100 text-amber-800">
                                        {{ str_replace('_', ' ', $asn->status) }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-600 font-medium">
                                    @if($asn->assignment_type === 'pickup' && $asn->destinationHub)
                                        <i class="fa-solid fa-warehouse text-indigo-600 mr-1"></i> Hub Tujuan Setor: <strong>{{ $asn->destinationHub->name }}</strong>
                                    @elseif($asn->assignment_type === 'transfer')
                                        <i class="fa-solid fa-route text-cyan-600 mr-1"></i> Rute: <strong>{{ $asn->originHub ? $asn->originHub->name : 'Hub Asal' }} &rarr; {{ $asn->destinationHub ? $asn->destinationHub->name : 'Hub Tujuan' }}</strong>
                                    @else
                                        <i class="fa-solid fa-truck text-indigo-600 mr-1"></i> Pengantaran Last-Mile ({{ count($asn->items) }} Paket)
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('courier-assignments.manifest', $asn->id) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white border border-slate-300 text-slate-700 font-bold text-[11px] hover:bg-slate-50 transition flex items-center gap-1">
                                    <i class="fa-solid fa-print"></i> Lembar Manifes
                                </a>
                                <form action="{{ route('courier-assignments.complete', $asn->id) }}" method="POST" onsubmit="return confirm('Selesaikan manifes {{ $asn->assignment_number }} dan masukkan seluruh paket ke status In-Hub / Selesai?')">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-sm transition flex items-center gap-1">
                                        <i class="fa-solid fa-circle-check"></i> Selesaikan Manifes
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Completed Manifests History Toggle -->
            @if($completedManifests->isNotEmpty())
                <details class="text-xs pt-1">
                    <summary class="font-bold text-slate-500 hover:text-indigo-600 cursor-pointer list-none flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-clock-rotate-left text-slate-400"></i>
                            <span>Riwayat Manifes Selesai ({{ count($completedManifests) }} Manifes)</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                    </summary>
                    <div class="mt-2 space-y-1.5 max-h-40 overflow-y-auto pr-1">
                        @foreach($completedManifests as $casn)
                            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/70 flex items-center justify-between text-[11px]">
                                <div class="space-x-1.5">
                                    <span class="font-mono font-bold text-slate-800">{{ $casn->assignment_number }}</span>
                                    <span class="px-1.5 py-0.2 rounded font-bold uppercase text-[9px] bg-slate-200 text-slate-700">{{ $casn->assignment_type }}</span>
                                    <span class="text-slate-500">({{ count($casn->items) }} resi • {{ $casn->assignment_date->format('d/m/Y') }})</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-emerald-100 text-emerald-800">
                                    ✓ Selesai
                                </span>
                            </div>
                        @endforeach
                    </div>
                </details>
            @endif
        </div>
    @endif

    <!-- Quick Filter Touch Tabs (Pickup vs Delivery vs Selesai) -->
    @php
        // Active Pickup: belum dijemput atau sedang dijemput menuju hub
        $pickupCount = $assignedShipments->filter(fn($s) => in_array($s->status, ['pending', 'picked_up']))->count();
        // Active Delivery: sedang diantar atau linehaul
        $deliveryCount = $assignedShipments->filter(fn($s) => in_array($s->status, ['in_transit', 'out_for_delivery']))->count();
        // Selesai: terkirim ke penerima atau sudah diserahkan ke hub sortir (In-Hub)
        $doneCount = $assignedShipments->filter(fn($s) => in_array($s->status, ['delivered', 'in_sorting_hub']))->count();
    @endphp

    <div class="grid grid-cols-4 gap-1.5 bg-white p-1.5 rounded-2xl border border-slate-200/80 shadow-sm text-center text-xs font-bold">
        <button onclick="filterStatus('all')" class="filter-btn py-2 rounded-xl bg-indigo-600 text-white transition touch-btn text-[11px]" id="btn-all">
            Semua ({{ count($assignedShipments) }})
        </button>
        <button onclick="filterStatus('pickup')" class="filter-btn py-2 rounded-xl text-slate-600 hover:bg-slate-100 transition touch-btn text-[11px]" id="btn-pickup">
            📦 Pickup ({{ $pickupCount }})
        </button>
        <button onclick="filterStatus('delivery')" class="filter-btn py-2 rounded-xl text-slate-600 hover:bg-slate-100 transition touch-btn text-[11px]" id="btn-delivery">
            🚚 Antar ({{ $deliveryCount }})
        </button>
        <button onclick="filterStatus('delivered')" class="filter-btn py-2 rounded-xl text-slate-600 hover:bg-slate-100 transition touch-btn text-[11px]" id="btn-delivered">
            ✅ Selesai ({{ $doneCount }})
        </button>
    </div>

    <!-- Assigned Shipments List (Mobile Touch Cards) -->
    <div class="space-y-4">
        @forelse($assignedShipments as $s)
            @php
                $isDone = in_array($s->status, ['delivered', 'in_sorting_hub']);
                $isActivePickup = in_array($s->status, ['pending', 'picked_up']);
                $isActiveDelivery = in_array($s->status, ['in_transit', 'out_for_delivery']);

                // Category for tab filtering: 'pickup' | 'delivery' | 'delivered'
                $cardCategory = $isDone ? 'delivered' : ($isActivePickup ? 'pickup' : 'delivery');
            @endphp
            
            <div class="shipment-card bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-4 hover:border-indigo-300 transition" 
                 data-type="{{ $cardCategory }}" 
                 data-status="{{ $s->status }}"
                 data-is-done="{{ $isDone ? 'true' : 'false' }}">
                
                <!-- Card Header with Task Type Badge -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 gap-2">
                    <div>
                        <div class="flex items-center flex-wrap gap-1.5">
                            @if($s->status === 'in_sorting_hub')
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    <i class="fa-solid fa-warehouse mr-0.5"></i> Masuk Hub Sortir (Selesai)
                                </span>
                            @elseif($s->status === 'delivered')
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    <i class="fa-solid fa-circle-check mr-0.5"></i> Terkirim (Delivered)
                                </span>
                            @elseif($isActivePickup)
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-purple-100 text-purple-800">
                                    📦 Tugas Penjemputan (Pickup)
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-800">
                                    🚚 Tugas Pengantaran (Delivery)
                                </span>
                            @endif

                            @if($s->deliveryOrder)
                                <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 font-mono text-[10px] font-bold">
                                    <i class="fa-solid fa-file-invoice mr-0.5"></i>{{ $s->deliveryOrder->do_number }}
                                </span>
                            @endif
                        </div>
                        <div class="font-mono font-black text-indigo-600 text-base leading-tight mt-1">{{ $s->tracking_number }}</div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase shrink-0 {{ $isDone ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : ($s->status == 'pending' ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-amber-100 text-amber-800 border border-amber-200') }}">
                        <i class="fa-solid {{ $s->status == 'delivered' ? 'fa-circle-check' : ($s->status == 'in_sorting_hub' ? 'fa-warehouse' : ($s->status == 'pending' ? 'fa-box-open' : 'fa-truck-fast')) }} mr-1"></i>
                        {{ $s->status == 'pending' ? 'Penjemputan' : ($s->status == 'in_sorting_hub' ? 'In-Hub (Selesai)' : str_replace('_', ' ', $s->status)) }}
                    </span>
                </div>

                @if($isActivePickup || ($s->status === 'in_sorting_hub' && $s->origin_hub_id == $s->current_hub_id))
                    <!-- PICKUP CARD DETAILS (Pengirim / Pickup Location) -->
                    <div class="space-y-3">
                        <div class="bg-purple-50/60 p-4 rounded-xl border border-purple-100 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-purple-700">Lokasi Penjemputan (Pengirim):</span>
                                <span class="text-[11px] font-bold text-slate-500">{{ $s->service_type }} ({{ $s->weight_kg }} kg)</span>
                            </div>
                            
                            <div class="font-extrabold text-slate-900 text-base leading-snug">{{ $s->sender_name }}</div>
                            <div class="text-xs text-slate-600 font-medium">{{ $s->sender_address }}, {{ $s->sender_city }}</div>
                            
                            @if(!$isDone)
                                <!-- Quick Touch Contact Buttons -->
                                <div class="grid grid-cols-2 gap-2 pt-1">
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $s->sender_phone) }}" target="_blank" class="py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center space-x-1.5 touch-btn shadow-sm">
                                        <i class="fa-brands fa-whatsapp text-sm"></i>
                                        <span>WA Pengirim</span>
                                    </a>
                                    <a href="tel:{{ preg_replace('/[^0-9]/', '', $s->sender_phone) }}" class="py-2 px-3 rounded-xl bg-purple-100 text-purple-800 hover:bg-purple-200 font-bold text-xs flex items-center justify-center space-x-1.5 touch-btn border border-purple-200">
                                        <i class="fa-solid fa-phone text-xs"></i>
                                        <span>Telepon</span>
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/70 text-xs text-slate-600 flex justify-between items-center">
                            <div>
                                <span class="font-bold text-slate-800">Tujuan Akhir:</span> 
                                <span class="font-bold text-emerald-700">{{ $s->recipient_name }} ({{ $s->recipient_city }})</span>
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono">
                                {{ $s->originHub ? $s->originHub->name : 'Hub Asal' }} &rarr; {{ $s->destinationHub ? $s->destinationHub->name : 'Hub Tujuan' }}
                            </div>
                        </div>
                    </div>
                @else
                    <!-- DELIVERY CARD DETAILS (Penerima / Delivery Destination) -->
                    <div class="space-y-3">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/70 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Penerima Paket:</span>
                                <span class="text-[11px] font-bold text-slate-500">{{ $s->service_type }} ({{ $s->weight_kg }} kg)</span>
                            </div>
                            
                            <div class="font-extrabold text-slate-900 text-base leading-snug">{{ $s->recipient_name }}</div>
                            
                            @if(!$isDone)
                                <!-- Quick Touch Contact Buttons -->
                                <div class="grid grid-cols-2 gap-2 pt-1">
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $s->recipient_phone) }}" target="_blank" class="py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center space-x-1.5 touch-btn shadow-sm">
                                        <i class="fa-brands fa-whatsapp text-sm"></i>
                                        <span>Chat WA</span>
                                    </a>
                                    <a href="tel:{{ preg_replace('/[^0-9]/', '', $s->recipient_phone) }}" class="py-2 px-3 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold text-xs flex items-center justify-center space-x-1.5 touch-btn border border-indigo-200">
                                        <i class="fa-solid fa-phone text-xs"></i>
                                        <span>Telepon</span>
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Destination Address -->
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/70 space-y-1">
                            <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 flex items-center">
                                <i class="fa-solid fa-location-dot text-rose-500 mr-1.5"></i> Alamat Pengantaran:
                            </div>
                            <div class="font-bold text-slate-900 text-xs">{{ $s->recipient_city }} ({{ $s->recipient_district ?? '' }})</div>
                            <div class="text-slate-600 text-xs leading-relaxed">{{ $s->recipient_address }}</div>
                        </div>
                    </div>
                @endif

                <!-- ACTION BUTTONS FOR COURIER -->
                <div class="pt-1 space-y-2">
                    @if($s->status === 'in_sorting_hub')
                        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold text-xs flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                                <span>Tugas Selesai: Paket sudah berada di Gudang Hub Sortir</span>
                            </span>
                            <span class="text-[10px] font-mono bg-white px-2 py-0.5 rounded border border-emerald-300 font-bold">
                                {{ $s->currentHub ? $s->currentHub->name : 'Hub Sortir' }}
                            </span>
                        </div>
                    @elseif($s->status === 'delivered')
                        <a href="{{ route('epod.show', $s->tracking_number) }}" target="_blank" class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs md:text-sm flex items-center justify-center space-x-2 shadow-md shadow-emerald-600/30 transition touch-btn">
                            <i class="fa-solid fa-circle-check text-base"></i>
                            <span>LIHAT BUKTI TERIMA ePOD (FOTO & TTD)</span>
                        </a>
                    @elseif($isActivePickup)
                        @if($s->status == 'pending')
                            <form action="{{ route('shipments.update-status', $s->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="picked_up">
                                <input type="hidden" name="location" value="{{ $s->sender_city }}">
                                <input type="hidden" name="description" value="Paket/DO telah berhasil dijemput oleh Kurir {{ Auth::user()->name }} dari lokasi pengirim.">
                                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs flex items-center justify-center space-x-2 shadow-sm transition touch-btn">
                                    <i class="fa-solid fa-box-open"></i>
                                    <span>Konfirmasi Jemput Paket (Picked Up)</span>
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('shipments.complete-task', $s->id) }}" method="POST" onsubmit="return confirm('Selesaikan tugas penjemputan resi {{ $s->tracking_number }} dan setorkan paket ke Gudang Hub Sortir?')">
                            @csrf
                            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-black text-xs md:text-sm flex items-center justify-center space-x-2 shadow-lg shadow-emerald-600/30 transition touch-btn">
                                <i class="fa-solid fa-circle-check text-base"></i>
                                <span>SELESAIKAN TUGAS (COMPLETE & SETOR KE HUB)</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('epod.show', $s->tracking_number) }}" class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white font-black text-xs md:text-sm flex items-center justify-center space-x-2 shadow-lg shadow-indigo-600/30 transition touch-btn">
                            <i class="fa-solid fa-camera text-base"></i>
                            <span>INPUT BUKTI TERIMA ePOD (FOTO & TTD)</span>
                        </a>

                        <form action="{{ route('shipments.complete-task', $s->id) }}" method="POST" onsubmit="return confirm('Selesaikan tugas pengantaran untuk resi {{ $s->tracking_number }}?')">
                            @csrf
                            <button type="submit" class="w-full py-2 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs flex items-center justify-center space-x-1.5 transition">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                <span>Selesaikan Tugas & Update Manifes (Complete)</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-10 text-center bg-white rounded-2xl border border-slate-200/80 text-slate-400 space-y-3 shadow-sm">
                <i class="fa-solid fa-box-open text-4xl text-slate-300"></i>
                <div class="font-bold text-sm text-slate-600">Belum ada tugas penjemputan atau pengantaran untuk Anda.</div>
                <p class="text-xs">Hubungi Staf Operasional Hub jika ada tugas baru.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Quick Update Status Pengiriman Kurir -->
<div id="updateCourierStatusModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-extrabold text-slate-900 text-sm">Update Status Pengiriman / Penjemputan</h3>
                <p class="text-xs text-indigo-600 font-mono font-bold mt-0.5" id="modalTrackingNum"></p>
            </div>
            <button onclick="closeUpdateStatusModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="modalUpdateStatusForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Pengiriman Terbaru *</label>
                <select name="status" id="modalStatusSelect" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                    <option value="picked_up">Picked Up (Paket Telah Dijemput Kurir dari Pengirim)</option>
                    <option value="in_sorting_hub">In Sorting Hub (Paket Masuk Hub Gudang Sortir)</option>
                    <option value="out_for_delivery">Out For Delivery (Paket Dibawa Kurir untuk Antar)</option>
                    <option value="delivered">Delivered (Paket Berhasil Terkirim ke Penerima)</option>
                    <option value="failed">Failed / Retur (Gagal Diantar / Alamat Tidak Ditemukan)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Lokasi Saat Ini *</label>
                <input type="text" name="location" id="modalLocationInput" required placeholder="Contoh: Hub Bandung / Alamat Pengirim" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan / Catatan Posisi *</label>
                <textarea name="description" id="modalDescriptionInput" rows="2" required placeholder="Tuliskan keterangan posisi paket saat ini..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800"></textarea>
            </div>

            <div class="pt-2 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="closeUpdateStatusModal()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md">Simpan Update Status</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function filterStatus(filterType) {
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('bg-indigo-600', 'text-white');
            b.classList.add('text-slate-600', 'hover:bg-slate-100');
        });

        const activeBtn = document.getElementById('btn-' + filterType);
        if (activeBtn) {
            activeBtn.classList.remove('text-slate-600', 'hover:bg-slate-100');
            activeBtn.classList.add('bg-indigo-600', 'text-white');
        }

        document.querySelectorAll('.shipment-card').forEach(card => {
            const cardType = card.dataset.type; // 'pickup', 'delivery', 'delivered'
            const isDone = card.dataset.isDone === 'true';

            if (filterType === 'all') {
                card.style.display = 'block';
            } else if (filterType === 'pickup') {
                card.style.display = (cardType === 'pickup' && !isDone) ? 'block' : 'none';
            } else if (filterType === 'delivery') {
                card.style.display = (cardType === 'delivery' && !isDone) ? 'block' : 'none';
            } else if (filterType === 'delivered') {
                card.style.display = isDone ? 'block' : 'none';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function openUpdateStatusModal(shipmentId, trackingNum, currentStatus, currentLoc) {
        const modal = document.getElementById('updateCourierStatusModal');
        const form = document.getElementById('modalUpdateStatusForm');
        const trackingElem = document.getElementById('modalTrackingNum');
        const statusSelect = document.getElementById('modalStatusSelect');
        const locationInput = document.getElementById('modalLocationInput');
        const descInput = document.getElementById('modalDescriptionInput');

        form.action = '/shipments/' + shipmentId + '/status';
        trackingElem.textContent = 'RESI: ' + trackingNum;
        statusSelect.value = currentStatus || 'picked_up';
        locationInput.value = currentLoc || 'Lokasi Kurir';
        descInput.value = 'Status posisi pengiriman diperbarui oleh Kurir {{ Auth::user()->name }}.';

        modal.classList.remove('hidden');
    }

    function closeUpdateStatusModal() {
        document.getElementById('updateCourierStatusModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
