@extends('layouts.app')

@section('title', 'Laporan Manajemen Pengambilan Keputusan')

@section('content')
<div class="space-y-6">
    <!-- Header & Filter Form -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Laporan Manajemen & Analytic Decision</h2>
            <p class="text-xs text-slate-500 mt-0.5">Analisis performa SLA, efisiensi kurir, dan kontribusi pendapatan customer.</p>
        </div>
        <form action="{{ route('reports.index') }}" method="GET" class="flex items-center space-x-3 text-xs">
            <div>
                <label class="block font-bold text-slate-600 mb-1">Mulai Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
            </div>
            <div>
                <label class="block font-bold text-slate-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
            </div>
            <div class="pt-5 flex space-x-2">
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('reports.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition flex items-center">
                    <i class="fa-solid fa-file-excel mr-1"></i> Ekspor CSV
                </a>
            </div>
        </form>
    </div>

    <!-- Executive Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Resi Periode Ini</p>
            <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalPeriod) }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Periode {{ $startDate }} s/d {{ $endDate }}</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">SLA Success Rate</p>
            <h3 class="text-2xl font-black text-indigo-600 mt-1">{{ $slaRate }}%</h3>
            <p class="text-[11px] text-emerald-600 mt-1"><i class="fa-solid fa-circle-check mr-1"></i> {{ $deliveredPeriod }} Terkirim (DELIVERED)</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pendapatan Ongkir</p>
            <h3 class="text-2xl font-black text-emerald-600 mt-1">Rp {{ number_format($totalRevenuePeriod, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Total pembayaran masuk</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Paket Aktif Dalam Transit</p>
            <h3 class="text-2xl font-black text-amber-600 mt-1">{{ number_format($inTransitPeriod) }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Proses pengiriman berjalan</p>
        </div>
    </div>

    <!-- Section 1: Courier Efficiency Ranking -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-user-ninja text-indigo-600 mr-2"></i> Performa & Efisiensi Tugas Kurir
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-4">Kode & Nama Kurir</th>
                        <th class="p-4">No. Telepon / SIM</th>
                        <th class="p-4">Armada Kendaraan</th>
                        <th class="p-4">Total Pengiriman Ditangani</th>
                        <th class="p-4">Status Kurir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($courierStats as $c)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4">
                                <div class="font-bold text-slate-900">{{ $c->name }}</div>
                                <div class="text-[11px] text-indigo-600 font-mono font-semibold">{{ $c->courier_code }}</div>
                            </td>
                            <td class="p-4 text-slate-600">
                                <div>{{ $c->phone }}</div>
                                <div class="text-[10px] text-slate-400">{{ $c->license_number ?? '-' }}</div>
                            </td>
                            <td class="p-4 font-bold text-slate-700">
                                {{ $c->vehicle ? $c->vehicle->plate_number . ' (' . $c->vehicle->vehicle_type . ')' : 'Tidak Ada' }}
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 font-extrabold text-xs">
                                    {{ $c->shipments_count }} Paket
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $c->status == 'on_duty' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ $c->status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Top Customer Revenue & Volume -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-crown text-amber-500 mr-2"></i> Rangking Customer Berdasarkan Kontribusi Omset & Volume
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-4">Kode & Nama Customer</th>
                        <th class="p-4">Tipe & Perusahaan</th>
                        <th class="p-4">Kota</th>
                        <th class="p-4">Total Resi</th>
                        <th class="p-4">Total Kontribusi Omset</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($topCustomers as $cust)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4">
                                <div class="font-bold text-slate-900">{{ $cust->name }}</div>
                                <div class="text-[11px] text-indigo-600 font-mono font-semibold">{{ $cust->customer_code }}</div>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $cust->customer_type == 'corporate' ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $cust->customer_type }}
                                </span>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $cust->company_name ?? '-' }}</div>
                            </td>
                            <td class="p-4 text-slate-700 font-semibold">{{ $cust->city }}</td>
                            <td class="p-4 font-extrabold text-slate-900">{{ $cust->shipment_count }} Transaksi</td>
                            <td class="p-4 font-black text-emerald-600 text-sm">
                                Rp {{ number_format($cust->total_spent, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
