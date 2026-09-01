@extends('layouts.app')

@section('title', 'Manajemen Pembelian Barang (Purchase Orders)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Manajemen Pembelian Barang (Purchase Orders)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pengadaan perlengkapan packing resi, sparepart armada kendaraan, & inventaris gudang.</p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('vendors.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-xs transition">
                <i class="fa-solid fa-truck-field mr-1.5"></i> Master Vendor
            </a>
            <a href="{{ route('purchases.create') }}" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition">
                <i class="fa-solid fa-cart-plus mr-1.5"></i> Buat PO Pembelian
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <form action="{{ route('purchases.index') }}" method="GET" class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex flex-wrap items-center justify-between gap-3 text-xs font-bold">
        <div class="flex items-center space-x-2">
            <span class="text-slate-500">Rentang Tanggal:</span>
            <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-1.5 rounded-xl border border-slate-300">
            <span class="text-slate-400">s/d</span>
            <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-1.5 rounded-xl border border-slate-300">
        </div>

        <div class="flex items-center space-x-2">
            <select name="vendor_id" class="px-3 py-1.5 rounded-xl border border-slate-300">
                <option value="">-- Semua Vendor --</option>
                @foreach($vendors as $v)
                    <option value="{{ $v->id }}" {{ request('vendor_id') == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                @endforeach
            </select>

            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. PO / Catatan..." class="px-3 py-1.5 rounded-xl border border-slate-300 w-52">
            <button type="submit" class="px-4 py-1.5 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition">
                <i class="fa-solid fa-magnifying-glass mr-1"></i> Filter
            </button>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-boxes-packing text-indigo-600 mr-2"></i> Rekapitulasi Purchase Orders (PO) Pembelian
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/70 text-slate-600 uppercase tracking-wider font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-4">Tanggal & No. PO</th>
                        <th class="p-4">Vendor Supplier</th>
                        <th class="p-4 text-right">Total Pembelian (Rp)</th>
                        <th class="p-4">Status Barang</th>
                        <th class="p-4">Status Pembayaran</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($purchases as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4">
                                <div class="font-mono font-bold text-indigo-600 text-sm">
                                    <a href="{{ route('purchases.show', $p->id) }}">{{ $p->purchase_number }}</a>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $p->purchase_date->format('d M Y') }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-extrabold text-slate-900 text-sm">{{ $p->vendor->name }}</div>
                                <div class="text-[11px] text-slate-400">Kode: {{ $p->vendor->vendor_code }}</div>
                            </td>
                            <td class="p-4 text-right font-black text-sm text-slate-900">
                                Rp {{ number_format($p->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase border {{ $p->status_badge }}">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase border {{ $p->payment_badge }}">
                                    {{ $p->payment_status }}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-1">
                                <a href="{{ route('purchases.show', $p->id) }}" class="p-2 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold text-xs transition" title="Lihat Rincian PO">
                                    <i class="fa-solid fa-eye mr-1"></i> Rincian
                                </a>
                                <a href="{{ route('purchases.print', $p->id) }}" target="_blank" class="p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-xs transition" title="Cetak Surat PO">
                                    <i class="fa-solid fa-print mr-1"></i> PO PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Belum ada transaksi Purchase Order (PO) pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
