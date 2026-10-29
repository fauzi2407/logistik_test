@extends('layouts.app')

@section('title', 'Print Multiple Resi - Antaran Warga Ekspress (AWX)')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header (Matching PDF Page 18) -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-slate-800 tracking-tight">
                Print Multiple
            </h2>
            <p class="text-xs text-slate-500 font-medium mt-0.5">
                Pencetakan Resi & Label AWB Massal (Hingga 100 Airwaybill)
            </p>
        </div>

        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Back Menu
        </a>
    </div>

    <!-- Batch Search Form Card (Matching PDF Page 18) -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-sm space-y-4">
        <form action="{{ route('customer.print-multiple') }}" method="GET" class="space-y-4">
            
            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                <span class="text-xs font-bold text-slate-700">Search By:</span>
                <select name="search_by" class="text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-xl px-3 py-2 w-48">
                    <option value="awb" {{ $searchBy === 'awb' ? 'selected' : '' }}>Awb No</option>
                    <option value="ref" {{ $searchBy === 'ref' ? 'selected' : '' }}>Referensi No</option>
                </select>

                <button type="submit" class="px-5 py-2 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs shadow-md shadow-sky-500/20 transition inline-flex items-center">
                    <i class="fa-solid fa-magnifying-glass mr-1.5"></i> Search
                </button>
            </div>

            <!-- Textarea for Multiple AWBs -->
            <div>
                <textarea name="awb_list" rows="5" placeholder="CGKXXXXXX (ENTER)&#10;CGKXXXXXX (ENTER)&#10;CGKXXXXXX (ENTER)"
                    class="w-full p-4 rounded-2xl border border-slate-200 text-xs font-mono text-slate-800 placeholder-slate-400 focus:border-orange-500 focus:ring-1 focus:ring-orange-200">{{ $rawAwbs }}</textarea>
                <p class="text-[11px] text-slate-400 mt-1">
                    Enter airwaybill number / package id (separated by enter), available up to 100 airwaybill.
                </p>
            </div>

            <!-- Print Actions Row -->
            <div class="flex items-center space-x-3 pt-2">
                <select name="print_type" class="text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-xl px-3.5 py-2.5">
                    <option value="regular">Print AWB Reguler</option>
                    <option value="thermal">Print AWB Thermal (100x150)</option>
                </select>

                <button type="submit" name="do_print" value="1" class="px-6 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs shadow-md shadow-sky-500/20 transition inline-flex items-center">
                    <i class="fa-solid fa-print mr-2"></i> Print
                </button>
            </div>
        </form>
    </div>

    <!-- Preview Table of Found Shipments (Matching PDF Page 18 Table) -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-black uppercase text-slate-800">
                Daftar Resi yang Ditemukan ({{ count($shipments) }} Resi)
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-black uppercase text-slate-500 border-b border-slate-200">
                        <th class="py-3 px-3">No</th>
                        <th class="py-3 px-3">Awb No</th>
                        <th class="py-3 px-3">Customer</th>
                        <th class="py-3 px-3">Origin</th>
                        <th class="py-3 px-3">Destination</th>
                        <th class="py-3 px-3">Service</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3 text-center">Weigth</th>
                        <th class="py-3 px-3 text-center">Colly</th>
                        <th class="py-3 px-3 text-center">History</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($shipments as $i => $s)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-3 font-bold text-slate-400">{{ $i + 1 }}</td>
                            <td class="py-3 px-3 font-mono font-bold text-sky-600">{{ $s->tracking_number }}</td>
                            <td class="py-3 px-3 font-semibold text-slate-800 uppercase">{{ $customer->name ?? 'PT ASTRA SEDAYA FINANCE' }}</td>
                            <td class="py-3 px-3 uppercase text-slate-600">{{ $s->sender_city ?? 'JAKARTA BARAT' }}</td>
                            <td class="py-3 px-3 uppercase text-slate-600">{{ $s->recipient_city }}</td>
                            <td class="py-3 px-3 font-bold text-orange-600 uppercase">{{ $s->service_type }}</td>
                            <td class="py-3 px-3">
                                @if($s->status === 'delivered')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800">DELIVERED</span>
                                @elseif($s->status === 'failed')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-rose-100 text-rose-800">UNDELIVERED</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-sky-100 text-sky-800">ONPROCESS</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center font-bold">{{ $s->weight_kg }} Kg</td>
                            <td class="py-3 px-3 text-center">1</td>
                            <td class="py-3 px-3 text-center">
                                <a href="{{ route('tracking.index', ['tracking_number' => $s->tracking_number]) }}" target="_blank"
                                    class="p-1.5 rounded-lg text-sky-600 hover:bg-sky-50 transition inline-block" title="Lihat History">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-slate-400 font-medium">
                                Tidak ada data resi. Masukkan nomor AWB pada kolom di atas untuk mencari.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
