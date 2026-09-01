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
            <div class="text-2xl font-black text-slate-900">{{ count($myDeliveryOrders) }} DO</div>
            <div class="text-[11px] text-slate-500 font-medium">Surat jalan diterbitkan</div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm space-y-1">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Resi Pengiriman</div>
            <div class="text-2xl font-black text-indigo-600">{{ count($myShipments) }} Resi</div>
            <div class="text-[11px] text-slate-500 font-medium">Paket AWB terdaftar</div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm space-y-1">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sedang Diantar (In-Transit)</div>
            <div class="text-2xl font-black text-amber-500">{{ $myShipments->where('status', '!=', 'delivered')->count() }} Paket</div>
            <div class="text-[11px] text-slate-500 font-medium">Dalam proses kirim kurir</div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm space-y-1">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Terkirim (Delivered)</div>
            <div class="text-2xl font-black text-emerald-600">{{ $myShipments->where('status', 'delivered')->count() }} Paket</div>
            <div class="text-[11px] text-slate-500 font-medium">Selesai & diserahkan</div>
        </div>
    </div>

    <!-- Recent Customer Delivery Orders -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden space-y-4 p-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-file-contract text-indigo-600 mr-2"></i> Daftar DO Pengiriman Milik Anda
            </h3>
            <a href="{{ route('delivery-orders.create') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                + Tambah DO Baru
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-3">Nomor DO</th>
                        <th class="p-3">Tanggal Order</th>
                        <th class="p-3">Jumlah Penerima</th>
                        <th class="p-3">Status Pengiriman</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($myDeliveryOrders as $do)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3 font-mono font-bold text-indigo-600 text-sm">{{ $do->do_number }}</td>
                            <td class="p-3 font-bold text-slate-700">{{ $do->order_date->format('d M Y') }}</td>
                            <td class="p-3 font-bold text-slate-900">{{ count($do->items) }} Lokasi Penerima</td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-slate-100 text-slate-800">
                                    {{ $do->status }}
                                </span>
                            </td>
                            <td class="p-3 text-right space-x-1">
                                <a href="{{ route('delivery-orders.show', $do->id) }}" class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold" title="Detail DO">
                                    <i class="fa-solid fa-eye"></i> Detail
                                </a>
                                <a href="{{ route('delivery-orders.print', $do->id) }}" target="_blank" class="p-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold" title="Cetak Surat Jalan">
                                    <i class="fa-solid fa-print"></i> Cetak
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400">Belum ada data DO pengiriman. Klik "+ Buat DO Pengiriman Baru" untuk membuat pengiriman massal.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Customer Resi AWB -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden space-y-4 p-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-box text-emerald-600 mr-2"></i> Daftar Resi AWB Pengiriman Milik Anda
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-3">Nomor Resi AWB</th>
                        <th class="p-3">Nama Penerima & Kota</th>
                        <th class="p-3">Layanan & Berat</th>
                        <th class="p-3">Total Ongkir</th>
                        <th class="p-3">Status Pengiriman</th>
                        <th class="p-3 text-right">Lacak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($myShipments as $s)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3 font-mono font-bold text-indigo-600">{{ $s->tracking_number }}</td>
                            <td class="p-3">
                                <div class="font-bold text-slate-900">{{ $s->recipient_name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $s->recipient_city }}</div>
                            </td>
                            <td class="p-3 text-slate-700 font-semibold">{{ $s->service_type }} ({{ $s->weight_kg }} kg)</td>
                            <td class="p-3 font-bold text-emerald-600">Rp {{ number_format($s->total_amount, 0, ',', '.') }}</td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $s->status == 'delivered' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ str_replace('_', ' ', $s->status) }}
                                </span>
                            </td>
                            <td class="p-3 text-right">
                                <a href="{{ route('shipments.show', $s->id) }}" class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold">
                                    <i class="fa-solid fa-route"></i> Lacak
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Belum ada resi AWB terdaftar untuk akun customer Anda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
