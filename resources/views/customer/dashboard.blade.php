@extends('layouts.app')

@section('title', 'Dashboard Customer - Antaran Warga Ekspress (AWX)')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Top Breadcrumb & Action Header (Matching PDF Page 13) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-slate-800 tracking-tight flex items-center">
                <span>Dashboard Customer</span>
            </h2>
            <p class="text-xs text-slate-500 font-medium mt-0.5">
                Portal Manajemen Pengiriman & Rekap Transaksi Pelanggan AWX
            </p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition flex items-center">
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Back Menu
            </a>
            <a href="{{ route('customer.report-pod') }}" class="px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold shadow-md shadow-orange-500/20 transition flex items-center">
                <i class="fa-solid fa-file-excel mr-1.5"></i> Report POD
            </a>
            <a href="{{ route('customer.print-multiple') }}" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-md transition flex items-center">
                <i class="fa-solid fa-print mr-1.5"></i> Print Multiple
            </a>
        </div>
    </div>

    <!-- Dismissible Welcome Success Toast (Matching PDF Page 13 Screenshot) -->
    <div id="customerWelcomeToast" class="p-4 rounded-2xl bg-indigo-50 border border-indigo-200/80 text-indigo-950 flex items-center justify-between shadow-sm">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-md shadow-indigo-600/30">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span class="text-xs font-extrabold uppercase text-indigo-700 block">Success !</span>
                <span class="text-xs font-semibold text-slate-700">Welcome Back to Apps Ongkir Express System (AWX TMS), thanks !</span>
            </div>
        </div>
        <button type="button" onclick="document.getElementById('customerWelcomeToast').remove()" class="text-slate-400 hover:text-slate-700 p-1">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>
    </div>

    <!-- 5 Top KPI Cards (Matching PDF Page 13) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-4">
        <!-- 1. Total Awb 1 Month -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm relative overflow-hidden group hover:border-slate-300 transition">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                <span class="text-2xl md:text-3xl font-black text-slate-800 tracking-tight">{{ $totalAwbMonth }}</span>
            </div>
            <div class="text-[11px] font-bold text-slate-500 mt-1 uppercase tracking-wider">Total Awb 1 Month</div>
        </div>

        <!-- 2. AWB Entry -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm relative overflow-hidden group hover:border-slate-300 transition">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                <span class="text-2xl md:text-3xl font-black text-slate-800 tracking-tight">{{ $awbEntry }}</span>
            </div>
            <div class="text-[11px] font-bold text-slate-500 mt-1 uppercase tracking-wider">AWB Entry</div>
        </div>

        <!-- 3. Awb Success -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm relative overflow-hidden group hover:border-emerald-300 transition">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="text-2xl md:text-3xl font-black text-emerald-600 tracking-tight">{{ $awbSuccess }}</span>
            </div>
            <div class="text-[11px] font-bold text-slate-500 mt-1 uppercase tracking-wider">Awb Success</div>
        </div>

        <!-- 4. Awb Undelivered -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm relative overflow-hidden group hover:border-rose-300 transition">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <span class="text-2xl md:text-3xl font-black text-rose-600 tracking-tight">{{ $awbUndelivered }}</span>
            </div>
            <div class="text-[11px] font-bold text-slate-500 mt-1 uppercase tracking-wider">Awb Undelivered</div>
        </div>

        <!-- 5. Awb On Process -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm relative overflow-hidden group hover:border-amber-300 transition">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span class="text-2xl md:text-3xl font-black text-amber-600 tracking-tight">{{ $awbOnProcess }}</span>
            </div>
            <div class="text-[11px] font-bold text-slate-500 mt-1 uppercase tracking-wider">Awb On Process</div>
        </div>
    </div>

    <!-- Interval Date & Filter Toolbar (Matching PDF Page 13) -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Interval Date Indicator -->
        <form action="{{ route('dashboard') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="flex items-center space-x-2 text-xs font-bold text-slate-700 bg-slate-50 border border-slate-200 px-3 py-2 rounded-xl">
                <i class="fa-solid fa-calendar-days text-orange-500"></i>
                <span>Interval Date:</span>
                <input type="date" name="interval_start" value="{{ $intervalStart }}" class="bg-transparent border-0 font-bold text-xs p-0 text-slate-900 focus:ring-0">
                <span>&ndash;</span>
                <input type="date" name="interval_end" value="{{ $intervalEnd }}" class="bg-transparent border-0 font-bold text-xs p-0 text-slate-900 focus:ring-0">
            </div>

            <button type="submit" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition" title="Refresh Data">
                <i class="fa-solid fa-rotate text-xs"></i>
            </button>

            <!-- Entries Dropdown -->
            <select class="text-xs font-bold text-slate-700 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-0 focus:border-slate-300">
                <option value="50">50</option>
                <option value="25">25</option>
                <option value="10">10</option>
            </select>
        </form>

        <!-- Export Buttons & Live Search -->
        <div class="flex items-center space-x-2">
            <div class="inline-flex rounded-xl shadow-sm border border-slate-200 bg-slate-50 p-0.5">
                <button type="button" onclick="copyTableData()" class="px-3 py-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-white rounded-lg transition">
                    Copy
                </button>
                <a href="{{ route('customer.export-pod') }}" class="px-3 py-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-white rounded-lg transition">
                    Excel
                </a>
                <button type="button" onclick="window.print()" class="px-3 py-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-white rounded-lg transition">
                    PDF
                </button>
            </div>

            <!-- Search Field -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" id="liveSearchInput" onkeyup="filterTables()" placeholder="Search..."
                    class="pl-8 pr-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 placeholder-slate-400 focus:border-orange-500 focus:ring-1 focus:ring-orange-200 w-36 sm:w-48 bg-slate-50/60">
            </div>
        </div>
    </div>

    <!-- Summary Table Grouped By Date (Matching PDF Page 13) -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center">
                <i class="fa-solid fa-list-check text-orange-500 mr-2"></i> Rekapitulasi Pengiriman Per Tanggal
            </h3>
            <span class="text-xs font-bold text-slate-400">{{ count($groupedByDate) }} Periode Tanggal</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="summaryDateTable">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-black uppercase text-slate-500 border-b border-slate-200">
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4 text-center">Total Resi</th>
                        <th class="py-3 px-4 text-center">Koli</th>
                        <th class="py-3 px-4 text-center">Berat</th>
                        <th class="py-3 px-4 text-right">Biaya Kirim</th>
                        <th class="py-3 px-4 text-center text-emerald-600">Delivered</th>
                        <th class="py-3 px-4 text-center text-rose-600">Undelivered</th>
                        <th class="py-3 px-4 text-center text-amber-600">OnProcess</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-700">
                    @forelse($groupedByDate as $row)
                        <tr class="hover:bg-orange-50/40 transition {{ $activeDetailDate === $row->raw_date ? 'bg-orange-50/60 font-bold' : '' }}">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ \Carbon\Carbon::parse($row->raw_date)->format('d M Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold">{{ $row->total_resi }}</td>
                            <td class="py-3.5 px-4 text-center">{{ $row->total_koli }}</td>
                            <td class="py-3.5 px-4 text-center">{{ number_format($row->total_berat, 0) }} Kg</td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($row->total_biaya, 2, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-emerald-600">{{ $row->delivered }}</td>
                            <td class="py-3.5 px-4 text-center font-bold text-rose-600">{{ $row->undelivered }}</td>
                            <td class="py-3.5 px-4 text-center font-bold text-amber-600">{{ $row->on_process }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('dashboard', ['detail_date' => $row->raw_date]) }}#section-detail"
                                    class="inline-flex items-center px-3 py-1.5 rounded-lg bg-sky-500 hover:bg-sky-600 text-white font-bold text-[11px] shadow-sm transition">
                                    <i class="fa-solid fa-magnifying-glass mr-1 text-[10px]"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400 font-medium">
                                Tidak ada data pengiriman pada rentang interval tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Detail Dashboard Customer (Matching PDF Pages 14-15) -->
    <div id="section-detail" class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden scroll-mt-6">
        <!-- Detail Header -->
        <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3 bg-slate-50/50">
            <div>
                <div class="flex items-center space-x-2">
                    <h3 class="text-sm font-black uppercase text-slate-900 tracking-wide">
                        Detail Dashboard Customer
                    </h3>
                    <span class="text-xs font-bold text-orange-600 bg-orange-100 px-2.5 py-0.5 rounded-full">
                        {{ count($detailShipments) }} Data AWB
                    </span>
                </div>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">
                    List Pengiriman Tanggal : <strong class="text-slate-800">{{ $activeDetailDate ? \Carbon\Carbon::parse($activeDetailDate)->format('Y-m-d') : date('Y-m-d') }}</strong>
                </p>
            </div>

            <div class="flex items-center space-x-2">
                <a href="#summaryDateTable" class="px-3.5 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition flex items-center">
                    <i class="fa-solid fa-arrow-up mr-1.5"></i> Back Menu
                </a>
                <a href="{{ route('customer.export-pod') }}" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm flex items-center">
                    <i class="fa-solid fa-file-excel mr-1.5"></i> Download Excel
                </a>
            </div>
        </div>

        <!-- 15-Column Table Matching PDF Pages 14-15 -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-[11px]" id="detailShipmentTable">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 font-black uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-3 w-10">No</th>
                        <th class="py-3 px-3">Awb No</th>
                        <th class="py-3 px-3">Referensi No</th>
                        <th class="py-3 px-3">Penerima</th>
                        <th class="py-3 px-3">Tujuan</th>
                        <th class="py-3 px-3">Jenis Pembayaran</th>
                        <th class="py-3 px-2 text-center">Koli</th>
                        <th class="py-3 px-2 text-center">Berat</th>
                        <th class="py-3 px-3">Tanggal</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3 text-right">Shipment Cost</th>
                        <th class="py-3 px-2 text-right">Handling Cost</th>
                        <th class="py-3 px-2 text-right">Surcharge Cost</th>
                        <th class="py-3 px-2 text-right">Insurance Cost</th>
                        <th class="py-3 px-2 text-right">Packing Cost</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($detailShipments as $index => $shipment)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-3 font-bold text-slate-400">{{ $index + 1 }}</td>
                            <td class="py-3 px-3">
                                <button type="button" onclick="showAwbModal('{{ $shipment->tracking_number }}', 'POJBA3276', '{{ addslashes($shipment->sender_name) }}', '{{ addslashes($shipment->sender_address) }}', '{{ addslashes($shipment->recipient_name) }}', '{{ addslashes($shipment->recipient_phone) }}', '{{ addslashes($shipment->recipient_address) }}', '{{ $shipment->service_type }}', '{{ $shipment->weight_kg }}', '{{ strtoupper($shipment->status) }}', '{{ $shipment->created_at->format('d M Y') }}', '{{ $shipment->updated_at->format('d M Y') }}')"
                                    class="font-mono font-bold text-sky-600 hover:text-sky-800 hover:underline flex items-center space-x-1">
                                    <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                                    <span>{{ $shipment->tracking_number }}</span>
                                </button>
                            </td>
                            <td class="py-3 px-3 font-mono font-semibold text-slate-600">POJBA3276</td>
                            <td class="py-3 px-3 font-bold text-slate-900 uppercase">{{ $shipment->recipient_name }}</td>
                            <td class="py-3 px-3 text-slate-600 uppercase">{{ $shipment->recipient_city }}</td>
                            <td class="py-3 px-3 font-semibold text-slate-500 uppercase">{{ $shipment->payment_method ?: 'CREDIT' }}</td>
                            <td class="py-3 px-2 text-center font-bold">1</td>
                            <td class="py-3 px-2 text-center">{{ $shipment->weight_kg }}</td>
                            <td class="py-3 px-3 font-mono text-slate-500 text-[10px]">{{ $shipment->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-3 px-3">
                                @if($shipment->status === 'delivered')
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800">
                                        POD DELIVERED
                                    </span>
                                @elseif($shipment->status === 'failed')
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-black bg-rose-100 text-rose-800">
                                        POD UNDELIVERED
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-black bg-sky-100 text-sky-800">
                                        DELIVERY RUNSHEET ONPROCESS
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($shipment->shipping_fee ?: 5000, 2, ',', '.') }}
                            </td>
                            <td class="py-3 px-2 text-right font-mono text-slate-400">Rp 0,00</td>
                            <td class="py-3 px-2 text-right font-mono text-slate-400">Rp 0,00</td>
                            <td class="py-3 px-2 text-right font-mono text-slate-400">Rp 0,00</td>
                            <td class="py-3 px-2 text-right font-mono text-slate-400">Rp 0,00</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="py-8 text-center text-slate-400 font-medium">
                                Tidak ada data resi pengiriman untuk tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Detail AWB (Matching PDF Page 16 Screenshot) -->
<div id="awbDetailModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto border border-slate-200">
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <span class="text-[10px] font-extrabold uppercase text-orange-600 tracking-wider">Antaran Warga Ekspress (AWX)</span>
                <h3 class="text-lg font-black text-slate-900 flex items-center">
                    <span>Detail AWB</span>
                </h3>
            </div>
            <button type="button" onclick="closeAwbModal()" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl hover:bg-slate-100">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- AWB Summary Info Row -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs">
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase">AWB No:</div>
                <div id="modalAwbNo" class="font-mono font-black text-slate-900 text-sm"></div>
                <div class="text-[10px] text-slate-500">Ref: <span id="modalRefNo">POJBA3276</span></div>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase">From:</div>
                <div class="font-bold text-slate-800">JAKARTA BARAT - KEBON JERUK</div>
                <div class="text-[10px] font-mono text-slate-400">JK0203</div>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase">To:</div>
                <div id="modalTo" class="font-bold text-slate-800">JAKARTA BARAT - KALIDERES</div>
                <div class="text-[10px] font-mono text-slate-400">JK0203</div>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase">Service:</div>
                <div id="modalService" class="font-extrabold text-orange-600">REGULER</div>
                <div class="text-[10px] text-slate-500">Weight: <span id="modalWeight">1</span> Kg, Koli: 1</div>
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="space-y-2">
            <h4 class="text-xs font-black text-slate-700 uppercase tracking-wider">Transaction</h4>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs bg-slate-50/60 p-3 rounded-xl border border-slate-100">
                <div>
                    <span class="text-slate-400 text-[10px]">Type Transaction:</span>
                    <strong class="block text-slate-800">CREDIT</strong>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px]">User Inp:</span>
                    <strong class="block text-slate-800">CGKABU</strong>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px]">Created At:</span>
                    <strong id="modalCreatedAt" class="block text-slate-800">06 Jan 2024</strong>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px]">User Update:</span>
                    <strong class="block text-slate-800">JBACIM0725</strong>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px]">Updated At:</span>
                    <strong id="modalUpdatedAt" class="block text-slate-800">08 Jan 2024</strong>
                </div>
                <div class="flex items-center">
                    <a id="modalTrackBtn" href="{{ route('tracking.index') }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-sky-500 hover:bg-sky-600 text-white font-bold text-[10px] shadow-sm transition flex items-center">
                        <i class="fa-solid fa-magnifying-glass mr-1"></i> Tracking History
                    </a>
                </div>
            </div>
        </div>

        <!-- Shipper Box -->
        <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-1 text-xs">
            <div class="flex items-center space-x-1.5 text-xs font-extrabold text-slate-800 uppercase mb-2">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                <span>Shipper</span>
            </div>
            <p><span class="text-slate-400">Customer:</span> <strong id="modalShipperName">CSTJBA0208FSM - PT ASTRA SEDAYA FINANCE (KEBON JERUK)</strong></p>
            <p><span class="text-slate-400">Pic Pickup:</span> <strong>PT ASTRA SEDAYA FINANCE (KEBON JERUK)</strong></p>
            <p><span class="text-slate-400">Pickup Address:</span> <span id="modalShipperAddress">RUKAN KENCANA NIAGA 1 BLOK D 1 NO 3 M & 3N, TAMAN ARIES, MERUYA UTARA, KEMBANGAN, JAKARTA BARAT</span></p>
            <p><span class="text-slate-400">Postal Code:</span> <span class="font-mono">11810</span></p>
        </div>

        <!-- Receiver Box -->
        <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-1 text-xs">
            <div class="flex items-center space-x-1.5 text-xs font-extrabold text-slate-800 uppercase mb-2">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                <span>Receiver</span>
            </div>
            <p><span class="text-slate-400">Receiver Name:</span> <strong id="modalReceiverName" class="text-slate-900 uppercase">YSROMIYAH</strong></p>
            <p><span class="text-slate-400">Receiver Phone:</span> <span id="modalReceiverPhone" class="font-mono">082125993833</span></p>
            <p><span class="text-slate-400">Receiver Address:</span> <span id="modalReceiverAddress">KAMP BELAKANG RT/RW 007/003 KALIDERES JAKARTA BARAT 11810</span></p>
        </div>

        <div class="flex justify-end pt-2">
            <button type="button" onclick="closeAwbModal()" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition">
                Tutup Detail
            </button>
        </div>
    </div>
</div>

<script>
    function showAwbModal(awbNo, refNo, shipper, shipperAddr, receiver, receiverPhone, receiverAddr, service, weight, status, createdAt, updatedAt) {
        document.getElementById('modalAwbNo').innerText = awbNo;
        document.getElementById('modalRefNo').innerText = refNo;
        document.getElementById('modalTo').innerText = receiverAddr;
        document.getElementById('modalService').innerText = service;
        document.getElementById('modalWeight').innerText = weight;
        document.getElementById('modalCreatedAt').innerText = createdAt;
        document.getElementById('modalUpdatedAt').innerText = updatedAt;
        document.getElementById('modalShipperName').innerText = shipper;
        document.getElementById('modalShipperAddress').innerText = shipperAddr;
        document.getElementById('modalReceiverName').innerText = receiver;
        document.getElementById('modalReceiverPhone').innerText = receiverPhone;
        document.getElementById('modalReceiverAddress').innerText = receiverAddr;
        document.getElementById('modalTrackBtn').href = "{{ route('tracking.index') }}?tracking_number=" + awbNo;
        document.getElementById('awbDetailModal').classList.remove('hidden');
    }

    function closeAwbModal() {
        document.getElementById('awbDetailModal').classList.add('hidden');
    }

    function filterTables() {
        const query = document.getElementById('liveSearchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#detailShipmentTable tbody tr');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    }

    function copyTableData() {
        const table = document.getElementById('detailShipmentTable');
        let text = '';
        for (let row of table.rows) {
            let rowText = [];
            for (let cell of row.cells) {
                rowText.push(cell.innerText.trim());
            }
            text += rowText.join('\t') + '\n';
        }
        navigator.clipboard.writeText(text).then(() => {
            alert('Data tabel berhasil disalin ke clipboard!');
        });
    }
</script>
@endsection
