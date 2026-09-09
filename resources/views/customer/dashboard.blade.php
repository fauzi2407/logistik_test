@extends('layouts.app')

@section('title', 'Portal Customer & Corporate Klien')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Customer Profile Banner -->
    <div class="p-6 rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 rounded-2xl bg-indigo-600/40 border border-indigo-400/30 flex items-center justify-center text-indigo-300 text-2xl font-bold shadow-inner">
                <i class="fa-solid fa-building-user"></i>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        {{ strtoupper($customer->customer_type ?? 'CORPORATE') }}
                    </span>
                    <span class="text-xs font-mono font-bold text-indigo-300">{{ $customer->customer_code ?? 'CUST-001' }}</span>
                </div>
                <h2 class="text-xl md:text-2xl font-extrabold text-white tracking-tight mt-1">{{ $customer->name ?? Auth::user()->name }}</h2>
                <p class="text-xs text-slate-400">
                    <i class="fa-solid fa-location-dot mr-1"></i> {{ $customer->address ?? 'Jakarta' }}, {{ $customer->city ?? '' }}
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-2 w-full md:w-auto">
            <a href="{{ route('delivery-orders.create') }}" class="flex-1 md:flex-none px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/30 text-center transition">
                <i class="fa-solid fa-file-circle-plus mr-1.5"></i> Buat DO Pengiriman
            </a>
            @if(Auth::user()->hasPermission('shipments.index', 'create'))
                <a href="{{ route('shipments.create') }}" class="flex-1 md:flex-none px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/30 text-center transition">
                    <i class="fa-solid fa-plus mr-1.5"></i> Buat Resi Baru
                </a>
            @endif
        </div>
    </div>

    <!-- Customer KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm space-y-1">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total DO Pengiriman</div>
            <div class="text-2xl font-black text-slate-900">{{ $totalDOCount }} DO</div>
            <div class="text-[11px] text-slate-500 font-medium">Surat jalan diterbitkan</div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm space-y-1">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Resi Pengiriman</div>
            <div class="text-2xl font-black text-indigo-600">{{ $totalShipmentCount }} Resi</div>
            <div class="text-[11px] text-slate-500 font-medium">Paket AWB terdaftar</div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm space-y-1">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sedang Diantar (In-Transit)</div>
            <div class="text-2xl font-black text-amber-500">{{ $inTransitCount }} Paket</div>
            <div class="text-[11px] text-slate-500 font-medium">Dalam proses kirim kurir</div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm space-y-1">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Terkirim (Delivered)</div>
            <div class="text-2xl font-black text-emerald-600">{{ $deliveredCount }} Paket</div>
            <div class="text-[11px] text-slate-500 font-medium">Selesai & diserahkan</div>
        </div>
    </div>

    <!-- Quick Navigation Anchor Links -->
    <div class="flex items-center space-x-2">
        <a href="#section-do" class="px-3.5 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs transition border border-indigo-200/60 flex items-center">
            <i class="fa-solid fa-file-contract mr-2 text-indigo-600"></i> DO Pengiriman ({{ $totalDOCount }})
        </a>
        <a href="#section-resi" class="px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs transition border border-emerald-200/60 flex items-center">
            <i class="fa-solid fa-box mr-2 text-emerald-600"></i> Resi AWB Pengiriman ({{ $totalShipmentCount }})
        </a>
    </div>

    <!-- 1. Customer Delivery Orders Section -->
    <div id="section-do" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden space-y-4 p-6 scroll-mt-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <div class="flex items-center space-x-2">
                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">
                    <i class="fa-solid fa-file-contract text-indigo-600 mr-1.5"></i> Daftar DO Pengiriman Milik Anda
                </h3>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-indigo-100 text-indigo-800">
                    {{ $myDeliveryOrders->total() }} Data
                </span>
            </div>
            <a href="{{ route('delivery-orders.create') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center">
                <i class="fa-solid fa-plus mr-1"></i> Tambah DO Baru
            </a>
        </div>

        <!-- Search & Filter DO Toolbar -->
        <form action="{{ route('dashboard') }}" method="GET" class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-slate-50/80 p-3 rounded-2xl border border-slate-200/70">
            @if(request('search_shipment')) <input type="hidden" name="search_shipment" value="{{ request('search_shipment') }}"> @endif
            @if(request('status_shipment')) <input type="hidden" name="status_shipment" value="{{ request('status_shipment') }}"> @endif
            @if(request('shipment_page')) <input type="hidden" name="shipment_page" value="{{ request('shipment_page') }}"> @endif

            <div class="flex flex-1 flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <div class="relative flex-1">
                    <input type="text" name="search_do" value="{{ $searchDo }}" placeholder="Cari No. DO, nama penerima, kota tujuan, barang..." 
                        class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500 bg-white">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
                <select name="status_do" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500 bg-white">
                    <option value="">Semua Status DO</option>
                    <option value="draft" {{ $statusDo == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="pending" {{ $statusDo == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $statusDo == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="processing" {{ $statusDo == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ $statusDo == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ $statusDo == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="completed" {{ in_array($statusDo, ['completed', 'komplit']) ? 'selected' : '' }}>Komplit (Lunas)</option>
                    <option value="cancelled" {{ $statusDo == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition shadow-sm flex items-center justify-center">
                    <i class="fa-solid fa-magnifying-glass mr-1.5"></i> Cari
                </button>
                @if(request('search_do') || request('status_do'))
                    <a href="{{ route('dashboard', request()->except(['search_do', 'status_do', 'do_page'])) }}" class="px-3 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-600 border border-slate-300 font-bold text-xs text-center transition flex items-center justify-center" title="Reset Pencarian DO">
                        <i class="fa-solid fa-rotate-left mr-1"></i> Reset
                    </a>
                @endif
            </div>
            <div class="text-[11px] font-medium text-slate-500 self-end md:self-center">
                Ditemukan <span class="font-extrabold text-indigo-600">{{ $myDeliveryOrders->total() }}</span> DO
            </div>
        </form>

        <!-- DO Table -->
        <div class="overflow-x-auto rounded-xl border border-slate-100">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-3.5">Nomor DO</th>
                        <th class="p-3.5">Tanggal Order</th>
                        <th class="p-3.5">Tujuan & Penerima</th>
                        <th class="p-3.5">Jumlah Item</th>
                        <th class="p-3.5">Status DO</th>
                        <th class="p-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($myDeliveryOrders as $do)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3.5 font-mono font-bold text-indigo-600 text-sm">
                                <a href="{{ route('delivery-orders.show', $do->id) }}" class="hover:underline">
                                    {{ $do->do_number }}
                                </a>
                            </td>
                            <td class="p-3.5">
                                <div class="font-bold text-slate-800">{{ $do->order_date->format('d M Y') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $do->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="p-3.5">
                                <div class="font-bold text-slate-900">{{ $do->recipient_name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $do->recipient_city }}</div>
                            </td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-bold">
                                    {{ count($do->items) }} Penerima / Item
                                </span>
                            </td>
                            <td class="p-3.5">
                                {!! $do->status_badge !!}
                            </td>
                            <td class="p-3.5 text-right space-x-1">
                                <a href="{{ route('delivery-orders.show', $do->id) }}" class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold text-xs inline-flex items-center" title="Detail DO">
                                    <i class="fa-solid fa-eye mr-1"></i> Detail
                                </a>
                                <a href="{{ route('delivery-orders.print', $do->id) }}" target="_blank" class="p-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-xs inline-flex items-center" title="Cetak Surat Jalan">
                                    <i class="fa-solid fa-print mr-1"></i> Cetak
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                @if(request('search_do') || request('status_do'))
                                    <i class="fa-solid fa-magnifying-glass text-2xl text-slate-300 block mb-2"></i>
                                    Tidak ada Delivery Order yang sesuai dengan pencarian Anda.
                                    <div class="mt-2">
                                        <a href="{{ route('dashboard', request()->except(['search_do', 'status_do', 'do_page'])) }}" class="text-indigo-600 font-bold text-xs hover:underline">
                                            Reset Filter Pencarian
                                        </a>
                                    </div>
                                @else
                                    <i class="fa-solid fa-folder-open text-2xl text-slate-300 block mb-2"></i>
                                    Belum ada data DO pengiriman. Klik "+ Tambah DO Baru" untuk membuat pengiriman.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- DO Pagination Footer -->
        @if($myDeliveryOrders->hasPages())
            <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="text-xs text-slate-500 font-medium">
                    Menampilkan <span class="font-bold text-slate-800">{{ $myDeliveryOrders->firstItem() ?? 0 }}</span> - <span class="font-bold text-slate-800">{{ $myDeliveryOrders->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-800">{{ $myDeliveryOrders->total() }}</span> DO
                </div>
                <div class="overflow-x-auto">
                    {{ $myDeliveryOrders->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- 2. Customer Resi AWB Section -->
    <div id="section-resi" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden space-y-4 p-6 scroll-mt-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <div class="flex items-center space-x-2">
                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">
                    <i class="fa-solid fa-box text-emerald-600 mr-1.5"></i> Daftar Resi AWB Pengiriman Milik Anda
                </h3>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">
                    {{ $myShipments->total() }} Data
                </span>
            </div>
            @if(Auth::user()->hasPermission('shipments.index', 'create'))
                <a href="{{ route('shipments.create') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center">
                    <i class="fa-solid fa-plus mr-1"></i> Buat Resi Baru
                </a>
            @endif
        </div>

        <!-- Search & Filter Resi Toolbar -->
        <form action="{{ route('dashboard') }}" method="GET" class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-slate-50/80 p-3 rounded-2xl border border-slate-200/70">
            @if(request('search_do')) <input type="hidden" name="search_do" value="{{ request('search_do') }}"> @endif
            @if(request('status_do')) <input type="hidden" name="status_do" value="{{ request('status_do') }}"> @endif
            @if(request('do_page')) <input type="hidden" name="do_page" value="{{ request('do_page') }}"> @endif

            <div class="flex flex-1 flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <div class="relative flex-1">
                    <input type="text" name="search_shipment" value="{{ $searchShipment }}" placeholder="Cari No. Resi AWB, penerima, kota tujuan, layanan..." 
                        class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 bg-white">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
                <select name="status_shipment" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 bg-white">
                    <option value="">Semua Status Pengiriman</option>
                    <option value="pending" {{ $statusShipment == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="picked_up" {{ $statusShipment == 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                    <option value="in_sorting_hub" {{ $statusShipment == 'in_sorting_hub' ? 'selected' : '' }}>In Sorting Hub</option>
                    <option value="in_transit" {{ $statusShipment == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                    <option value="out_for_delivery" {{ $statusShipment == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                    <option value="delivered" {{ $statusShipment == 'delivered' ? 'selected' : '' }}>Delivered (Terkirim)</option>
                    <option value="failed" {{ $statusShipment == 'failed' ? 'selected' : '' }}>Failed / Hold</option>
                    <option value="cancelled" {{ $statusShipment == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-sm flex items-center justify-center">
                    <i class="fa-solid fa-magnifying-glass mr-1.5"></i> Cari
                </button>
                @if(request('search_shipment') || request('status_shipment'))
                    <a href="{{ route('dashboard', request()->except(['search_shipment', 'status_shipment', 'shipment_page'])) }}" class="px-3 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-600 border border-slate-300 font-bold text-xs text-center transition flex items-center justify-center" title="Reset Pencarian Resi">
                        <i class="fa-solid fa-rotate-left mr-1"></i> Reset
                    </a>
                @endif
            </div>
            <div class="text-[11px] font-medium text-slate-500 self-end md:self-center">
                Ditemukan <span class="font-extrabold text-emerald-600">{{ $myShipments->total() }}</span> Resi
            </div>
        </form>

        <!-- Resi Table -->
        <div class="overflow-x-auto rounded-xl border border-slate-100">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-3.5">Nomor Resi AWB</th>
                        <th class="p-3.5">Nama Penerima & Kota</th>
                        <th class="p-3.5">Layanan & Berat</th>
                        <th class="p-3.5">Total Ongkir</th>
                        <th class="p-3.5">Status Pengiriman</th>
                        <th class="p-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($myShipments as $s)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3.5 font-mono font-bold text-indigo-600 text-sm">
                                <a href="{{ route('shipments.show', $s->id) }}" class="hover:underline">
                                    {{ $s->tracking_number }}
                                </a>
                            </td>
                            <td class="p-3.5">
                                <div class="font-bold text-slate-900">{{ $s->recipient_name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $s->recipient_city }}</div>
                            </td>
                            <td class="p-3.5 text-slate-700 font-semibold">
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-800 font-bold text-[11px] mr-1">{{ $s->service_type }}</span>
                                <span>{{ $s->weight_kg }} kg</span>
                            </td>
                            <td class="p-3.5 font-bold text-emerald-600">Rp {{ number_format($s->total_amount, 0, ',', '.') }}</td>
                            <td class="p-3.5">
                                @php
                                    $badgeStyle = match($s->status) {
                                        'delivered' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'in_transit', 'out_for_delivery' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'in_sorting_hub', 'picked_up' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'failed', 'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
                                        default => 'bg-slate-100 text-slate-800 border-slate-200',
                                    };
                                    $iconStyle = match($s->status) {
                                        'delivered' => 'fa-circle-check',
                                        'in_transit', 'out_for_delivery' => 'fa-truck-fast',
                                        'in_sorting_hub', 'picked_up' => 'fa-boxes-packing',
                                        'failed', 'cancelled' => 'fa-circle-xmark',
                                        default => 'fa-clock',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase border {{ $badgeStyle }}">
                                    <i class="fa-solid {{ $iconStyle }} mr-1"></i> {{ str_replace('_', ' ', $s->status) }}
                                </span>
                            </td>
                            <td class="p-3.5 text-right">
                                <a href="{{ route('shipments.show', $s->id) }}" class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold text-xs inline-flex items-center">
                                    <i class="fa-solid fa-route mr-1"></i> Lacak
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                @if(request('search_shipment') || request('status_shipment'))
                                    <i class="fa-solid fa-magnifying-glass text-2xl text-slate-300 block mb-2"></i>
                                    Tidak ada resi AWB yang sesuai dengan kriteria pencarian Anda.
                                    <div class="mt-2">
                                        <a href="{{ route('dashboard', request()->except(['search_shipment', 'status_shipment', 'shipment_page'])) }}" class="text-emerald-600 font-bold text-xs hover:underline">
                                            Reset Filter Pencarian
                                        </a>
                                    </div>
                                @else
                                    <i class="fa-solid fa-box-open text-2xl text-slate-300 block mb-2"></i>
                                    Belum ada resi AWB terdaftar untuk akun customer Anda.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Resi Pagination Footer -->
        @if($myShipments->hasPages())
            <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="text-xs text-slate-500 font-medium">
                    Menampilkan <span class="font-bold text-slate-800">{{ $myShipments->firstItem() ?? 0 }}</span> - <span class="font-bold text-slate-800">{{ $myShipments->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-800">{{ $myShipments->total() }}</span> Resi
                </div>
                <div class="overflow-x-auto">
                    {{ $myShipments->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
