@extends('layouts.app')

@section('title', 'Report POD Customer - Antaran Warga Ekspress (AWX)')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Top Header (Matching PDF Page 17) -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-slate-800 tracking-tight">
                Report POD Customer
            </h2>
            <p class="text-xs text-slate-500 font-medium mt-0.5">
                Laporan Bukti Penerimaan Barang & Status Pengiriman Pelanggan
            </p>
        </div>

        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Back Menu
        </a>
    </div>

    <!-- Filter Form Card (Matching PDF Page 17 Layout) -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-sm space-y-6">
        <form action="{{ route('customer.report-pod') }}" method="GET" class="space-y-4">
            
            <!-- Row 1: Date Range -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">
                        Dari Tanggal <i class="fa-regular fa-circle-question text-slate-400 text-[10px]"></i>
                    </label>
                    <div class="relative">
                        <input type="date" name="start_date" value="{{ $startDate }}" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:border-orange-500 focus:ring-1 focus:ring-orange-200">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">
                        Sampai Tanggal <i class="fa-regular fa-circle-question text-slate-400 text-[10px]"></i>
                    </label>
                    <div class="relative">
                        <input type="date" name="end_date" value="{{ $endDate }}" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:border-orange-500 focus:ring-1 focus:ring-orange-200">
                    </div>
                </div>
            </div>

            <!-- Row 2: Region Origin & TLC Origin, Region Destination & TLC Destination -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">
                        Region Origin <i class="fa-regular fa-circle-question text-slate-400 text-[10px]"></i>
                    </label>
                    <select name="region_origin" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 bg-white">
                        <option value="">Region Origin</option>
                        <option value="Jawa Barat">Jawa Barat</option>
                        <option value="DKI Jakarta" selected>DKI Jakarta</option>
                        <option value="Banten">Banten</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">
                        Tlc Origin <i class="fa-regular fa-circle-question text-slate-400 text-[10px]"></i>
                    </label>
                    <input type="text" name="tlc_origin" placeholder="type city name" value="Jakarta Barat"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">
                        Region Destination <i class="fa-regular fa-circle-question text-slate-400 text-[10px]"></i>
                    </label>
                    <select name="region_destination" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 bg-white">
                        <option value="">Region Destination</option>
                        <option value="All">Semua Wilayah</option>
                        <option value="Jabodetabek">Jabodetabek</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">
                        Tlc Destination <i class="fa-regular fa-circle-question text-slate-400 text-[10px]"></i>
                    </label>
                    <input type="text" name="tlc_destination" placeholder="type city destination"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800">
                </div>
            </div>

            <!-- Row 3: Customer, Service, Status Transaksi -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">
                        Customer <i class="fa-regular fa-circle-question text-slate-400 text-[10px]"></i>
                    </label>
                    <input type="text" readonly value="{{ $customer->customer_code ?? 'CSTJBA0208FSM' }} - {{ $customer->name ?? 'PT ASTRA SEDAYA FINANCE (KEBON JERUK)' }}"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-slate-50 truncate">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">
                        Service <i class="fa-regular fa-circle-question text-slate-400 text-[10px]"></i>
                    </label>
                    <select name="service" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-white">
                        <option value="all" {{ $service === 'all' ? 'selected' : '' }}>all</option>
                        <option value="REGULER" {{ $service === 'REGULER' ? 'selected' : '' }}>REGULER</option>
                        <option value="ODS" {{ $service === 'ODS' ? 'selected' : '' }}>ODS (One Day Service)</option>
                        <option value="SDS" {{ $service === 'SDS' ? 'selected' : '' }}>SDS (Same Day Service)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">
                        Status Transaksi <i class="fa-regular fa-circle-question text-slate-400 text-[10px]"></i>
                    </label>
                    <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-white">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>all</option>
                        <option value="delivered" {{ $status === 'delivered' ? 'selected' : '' }}>POD DELIVERED</option>
                        <option value="undelivered" {{ $status === 'undelivered' ? 'selected' : '' }}>POD UNDELIVERED</option>
                        <option value="onprocess" {{ $status === 'onprocess' ? 'selected' : '' }}>ONPROCESS</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons Row -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('customer.report-pod') }}" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>

                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs shadow-md transition flex items-center">
                    <i class="fa-solid fa-eye mr-1.5"></i> Preview
                </button>

                <a href="{{ route('customer.export-pod', request()->all()) }}" class="px-5 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs shadow-md shadow-sky-500/20 transition flex items-center">
                    <i class="fa-solid fa-file-excel mr-1.5"></i> Export Excel
                </a>
            </div>
        </form>
    </div>

    <!-- Preview Table -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-black uppercase text-slate-800">
                Hasil Pratinjau Laporan POD ({{ count($previewShipments) }} Data)
            </h3>
            <span class="text-xs font-medium text-slate-400">Format Excel Tersedia di Export Excel</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-black uppercase text-slate-500 border-b border-slate-200">
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">No. Resi</th>
                        <th class="py-3 px-4">Referensi</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Penerima</th>
                        <th class="py-3 px-4">Tujuan</th>
                        <th class="py-3 px-4">Status POD</th>
                        <th class="py-3 px-4">Diterima Oleh</th>
                        <th class="py-3 px-4 text-right">Biaya</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($previewShipments as $idx => $s)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-bold text-slate-400">{{ $idx + 1 }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-sky-600">{{ $s->tracking_number }}</td>
                            <td class="py-3 px-4 font-mono text-slate-600">POJBA3276</td>
                            <td class="py-3 px-4 font-mono">{{ $s->created_at->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900 uppercase">{{ $s->recipient_name }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $s->recipient_city }}</td>
                            <td class="py-3 px-4">
                                @if($s->status === 'delivered')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800">
                                        POD DELIVERED
                                    </span>
                                @elseif($s->status === 'failed')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-rose-100 text-rose-800">
                                        POD UNDELIVERED
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-sky-100 text-sky-800">
                                        ONPROCESS
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">{{ $s->pod_receiver_name ?? '-' }}</td>
                            <td class="py-3 px-4 text-right font-mono font-bold">Rp {{ number_format($s->total_amount ?: 5000, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400 font-medium">
                                Tidak ada data resi ditemukan untuk kriteria filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
