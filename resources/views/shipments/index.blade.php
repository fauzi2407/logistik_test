@extends('layouts.app')

@section('title', 'Resi Pengiriman (AWB)')

@section('content')
<div class="space-y-6">
    <!-- Header Bar -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-sm">
                    <i class="fa-solid fa-box"></i>
                </span>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Resi Pengiriman (Air Waybill / AWB)</h2>
            </div>
            <p class="text-xs text-slate-500 mt-1">Kelola seluruh resi pengiriman, filter layanan ekspedisi, status tracking transit, dan penugasan kurir.</p>
        </div>
        <div class="flex items-center space-x-3 w-full md:w-auto">
            @if(Auth::user()->hasPermission('shipments.index', 'create'))
                <a href="{{ route('shipments.create') }}" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition flex items-center space-x-2 flex-shrink-0">
                    <i class="fa-solid fa-plus"></i>
                    <span>Buat Resi Baru</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Quick Stats Summary -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Resi</p>
                <p class="text-lg font-black text-slate-900 leading-tight">{{ number_format($statTotal) }}</p>
            </div>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Dalam Transit</p>
                <p class="text-lg font-black text-sky-700 leading-tight">{{ number_format($statInTransit) }}</p>
            </div>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Terkirim (Delivered)</p>
                <p class="text-lg font-black text-emerald-700 leading-tight">{{ number_format($statDelivered) }}</p>
            </div>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Menunggu Pickup</p>
                <p class="text-lg font-black text-amber-700 leading-tight">{{ number_format($statPending) }}</p>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden p-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div class="flex items-center space-x-2 text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                <i class="fa-solid fa-sliders text-indigo-600"></i>
                <span>Filter & Pencarian Resi Pengiriman</span>
            </div>
            @if($search || $status || $serviceType || $courierId)
                <a href="{{ route('shipments.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-800 flex items-center space-x-1">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Reset Filter</span>
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('shipments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
            <!-- 1. Search Query -->
            <div class="lg:col-span-4 relative">
                <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Cari Kata Kunci</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="No Resi, Pengirim, Penerima, Kota..." 
                        class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            <!-- 2. Filter Layanan (Service Type) -->
            <div class="lg:col-span-2">
                <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Jenis Layanan</label>
                <select name="service_type" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">Semua Layanan</option>
                    @foreach($serviceTypes as $st)
                        <option value="{{ $st }}" {{ ($serviceType ?? '') == $st ? 'selected' : '' }}>
                            {{ $st }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Filter Status Transit -->
            <div class="lg:col-span-3">
                <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Status Transit</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">Semua Status Transit</option>
                    <option value="pending" {{ ($status ?? '') == 'pending' ? 'selected' : '' }}>Menunggu Pickup (Pending)</option>
                    <option value="picked_up" {{ ($status ?? '') == 'picked_up' ? 'selected' : '' }}>Telah Dipickup (Picked Up)</option>
                    <option value="in_sorting_hub" {{ ($status ?? '') == 'in_sorting_hub' ? 'selected' : '' }}>In-Hub (Gudang Sortir)</option>
                    <option value="in_transit" {{ ($status ?? '') == 'in_transit' ? 'selected' : '' }}>In-Transit (Linehaul Antar Hub)</option>
                    <option value="out_for_delivery" {{ ($status ?? '') == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery (Dibawa Kurir)</option>
                    <option value="delivered" {{ ($status ?? '') == 'delivered' ? 'selected' : '' }}>Delivered (Terkirim / Selesai)</option>
                    <option value="cancelled" {{ ($status ?? '') == 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
                </select>
            </div>

            <!-- 4. Filter Kurir -->
            <div class="lg:col-span-2">
                <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Kurir Pengantar</label>
                @if(Auth::user()->role === 'courier' && $loggedInCourier)
                    <input type="text" disabled value="{{ $loggedInCourier->name }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-600">
                @else
                    <select name="courier_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">Semua Kurir</option>
                        @foreach($couriers as $c)
                            <option value="{{ $c->id }}" {{ ($courierId ?? '') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->courier_code }})
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            <!-- 5. Tombol Filter & Reset -->
            <div class="lg:col-span-1 flex items-end">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition flex items-center justify-center space-x-1.5 h-[38px]">
                    <i class="fa-solid fa-filter text-xs"></i>
                    <span>Filter</span>
                </button>
            </div>
        </form>

        <!-- Active Filter Badges -->
        @if($search || $status || $serviceType || $courierId)
            <div class="flex flex-wrap items-center gap-2 mt-4 pt-3 border-t border-slate-100 text-xs">
                <span class="text-[11px] font-bold text-slate-400">Filter Aktif:</span>
                
                @if($search)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-semibold text-[11px]">
                        Kata Kunci: "{{ $search }}"
                    </span>
                @endif

                @if($serviceType)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700 font-semibold text-[11px]">
                        Layanan: {{ $serviceType }}
                    </span>
                @endif

                @if($status)
                    @php
                        $statusBadgeName = match($status) {
                            'pending' => 'Menunggu Pickup',
                            'picked_up' => 'Telah Dipickup',
                            'in_sorting_hub' => 'In-Hub (Gudang Sortir)',
                            'in_transit' => 'In-Transit (Linehaul)',
                            'out_for_delivery' => 'Out for Delivery',
                            'delivered' => 'Delivered (Terkirim)',
                            'cancelled' => 'Dibatalkan',
                            default => $status
                        };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 font-semibold text-[11px]">
                        Status: {{ $statusBadgeName }}
                    </span>
                @endif

                @if($courierId)
                    @php
                        $selectedCourier = $couriers->firstWhere('id', $courierId);
                    @endphp
                    @if($selectedCourier)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-semibold text-[11px]">
                            Kurir: {{ $selectedCourier->name }}
                        </span>
                    @endif
                @endif
            </div>
        @endif
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center space-x-2">
                <i class="fa-solid fa-list text-indigo-600"></i>
                <span>Daftar Resi Pengiriman</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-100 text-slate-600 ml-1">
                    {{ $shipments->total() }} Data
                </span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-4">Nomor Resi AWB</th>
                        <th class="p-4">Pengirim & Asal</th>
                        <th class="p-4">Penerima & Tujuan</th>
                        <th class="p-4">Layanan / Berat</th>
                        <th class="p-4">Total Biaya</th>
                        <th class="p-4">Kurir Ditugaskan</th>
                        <th class="p-4">Status Transit</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($shipments as $s)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- AWB & Delivery Order -->
                            <td class="p-4">
                                <a href="{{ route('shipments.show', $s->id) }}" class="font-mono font-bold text-indigo-600 hover:text-indigo-800 flex items-center space-x-1.5 group">
                                    <i class="fa-solid fa-barcode text-slate-400 group-hover:text-indigo-600"></i>
                                    <span>{{ $s->tracking_number }}</span>
                                </a>
                                @if($s->deliveryOrder)
                                    <div class="text-[10px] text-slate-400 font-normal mt-0.5">
                                        DO: <span class="font-semibold text-slate-600">{{ $s->deliveryOrder->do_number }}</span>
                                    </div>
                                @endif
                                <div class="text-[10px] text-slate-400 font-normal mt-0.5">
                                    {{ $s->created_at->format('d/m/Y H:i') }}
                                </div>
                            </td>

                            <!-- Sender -->
                            <td class="p-4 text-slate-900">
                                <div class="font-bold text-slate-800">{{ $s->sender_name }}</div>
                                <div class="text-[11px] text-slate-400 flex items-center space-x-1 mt-0.5">
                                    <i class="fa-solid fa-location-dot text-[10px]"></i>
                                    <span>{{ $s->sender_city }}</span>
                                </div>
                            </td>

                            <!-- Recipient -->
                            <td class="p-4 text-slate-900">
                                <div class="font-bold text-slate-800">{{ $s->recipient_name }}</div>
                                <div class="text-[11px] text-slate-400 flex items-center space-x-1 mt-0.5">
                                    <i class="fa-solid fa-map-pin text-[10px]"></i>
                                    <span>{{ $s->recipient_city }}</span>
                                </div>
                            </td>

                            <!-- Service & Weight -->
                            <td class="p-4">
                                @php
                                    $stBadgeColor = match($s->service_type) {
                                        'Express' => 'bg-rose-50 text-rose-700 border border-rose-200',
                                        'SameDay' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                        default => 'bg-indigo-50 text-indigo-700 border border-indigo-200'
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-lg font-bold text-[11px] {{ $stBadgeColor }}">
                                    {{ $s->service_type }}
                                </span>
                                <div class="text-[11px] text-slate-500 font-medium mt-1 flex items-center space-x-1">
                                    <i class="fa-solid fa-weight-hanging text-[10px] text-slate-400"></i>
                                    <span>{{ number_format($s->weight_kg, 1) }} kg</span>
                                </div>
                            </td>

                            <!-- Total Amount -->
                            <td class="p-4">
                                <span class="font-extrabold text-slate-900">
                                    Rp {{ number_format($s->total_amount, 0, ',', '.') }}
                                </span>
                                <div class="text-[10px] font-semibold text-slate-400 mt-0.5">
                                    {{ $s->payment_method }} • {{ $s->payment_status ?? 'Pending' }}
                                </div>
                            </td>

                            <!-- Courier -->
                            <td class="p-4">
                                @if($s->courier)
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold">
                                            <i class="fa-solid fa-motorcycle"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 text-xs">{{ $s->courier->name }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $s->courier->courier_code }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[11px] flex items-center space-x-1">
                                        <i class="fa-solid fa-user-xmark text-slate-300"></i>
                                        <span>Belum Ditugaskan</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Status Transit -->
                            <td class="p-4">
                                @php
                                    $stColor = match($s->status) {
                                        'delivered' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'out_for_delivery' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'in_transit' => 'bg-sky-100 text-sky-800 border-sky-200',
                                        'in_sorting_hub' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                        'picked_up' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200'
                                    };
                                    $stIcon = match($s->status) {
                                        'delivered' => 'fa-circle-check',
                                        'out_for_delivery' => 'fa-truck',
                                        'in_transit' => 'fa-truck-fast',
                                        'in_sorting_hub' => 'fa-warehouse',
                                        'picked_up' => 'fa-box',
                                        'cancelled' => 'fa-ban',
                                        default => 'fa-clock'
                                    };
                                    $stLabel = match($s->status) {
                                        'pending' => 'Pending Pickup',
                                        'picked_up' => 'Telah Dipickup',
                                        'in_sorting_hub' => 'In-Hub (Sortir)',
                                        'in_transit' => 'In-Transit (Linehaul)',
                                        'out_for_delivery' => 'Out for Delivery',
                                        'delivered' => 'Delivered (Terkirim)',
                                        'cancelled' => 'Dibatalkan',
                                        default => str_replace('_', ' ', $s->status)
                                    };
                                @endphp
                                <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border {{ $stColor }}">
                                    <i class="fa-solid {{ $stIcon }} text-[9px]"></i>
                                    <span>{{ $stLabel }}</span>
                                </span>
                            </td>

                            <!-- Action Buttons -->
                            <td class="p-4 text-right space-x-1">
                                <a href="{{ route('shipments.show', $s->id) }}" class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold inline-block" title="Detail Resi">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @if(Auth::user()->hasPermission('shipments.index', 'edit'))
                                    <a href="{{ route('shipments.edit', $s->id) }}" class="p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 font-bold inline-block" title="Edit Resi">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                @endif
                                <a href="{{ route('shipments.print-label', $s->id) }}" target="_blank" class="p-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold inline-block" title="Cetak Label QR Resi AWB">
                                    <i class="fa-solid fa-qrcode"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-12 text-center text-slate-400">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                                        <i class="fa-solid fa-box-open"></i>
                                    </div>
                                    <p class="text-xs font-bold text-slate-600">Tidak ada resi pengiriman yang ditemukan.</p>
                                    @if($search || $status || $serviceType || $courierId)
                                        <p class="text-[11px] text-slate-400">Coba ubah atau reset filter pencarian yang Anda gunakan.</p>
                                        <a href="{{ route('shipments.index') }}" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-600 font-bold text-xs hover:bg-indigo-100 transition">
                                            <i class="fa-solid fa-rotate-left text-[10px]"></i>
                                            <span>Reset Semua Filter</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($shipments->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $shipments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
