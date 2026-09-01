@extends('layouts.app')

@section('title', 'Penjurnalan Jurnal Umum')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Penjurnalan (Jurnal Umum)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Catatan pencatatan ganda (double-entry) transaksi keuangan logistik.</p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('accounting.journals.create') }}" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition">
                <i class="fa-solid fa-plus mr-1.5"></i> Buat Jurnal Baru
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <form action="{{ route('accounting.journals.index') }}" method="GET" class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex flex-wrap items-center justify-between gap-3 text-xs font-bold">
        <div class="flex items-center space-x-2">
            <span class="text-slate-500">Rentang Tanggal:</span>
            <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-1.5 rounded-xl border border-slate-300">
            <span class="text-slate-400">s/d</span>
            <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-1.5 rounded-xl border border-slate-300">
        </div>

        <div class="flex items-center space-x-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Jurnal / Ref / Memo..." class="px-3 py-1.5 rounded-xl border border-slate-300 w-64">
            <button type="submit" class="px-4 py-1.5 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition">
                <i class="fa-solid fa-magnifying-glass mr-1"></i> Filter
            </button>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-book text-indigo-600 mr-2"></i> Transaksi Jurnal Umum
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/70 text-slate-600 uppercase tracking-wider font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-4">Tanggal & No. Jurnal</th>
                        <th class="p-4">No. Referensi</th>
                        <th class="p-4">Deskripsi / Memo</th>
                        <th class="p-4 text-right">Total Nominal (Rp)</th>
                        <th class="p-4">Tipe</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($journals as $j)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4">
                                <div class="font-mono font-bold text-indigo-600 text-sm">
                                    <a href="{{ route('accounting.journals.show', $j->id) }}">{{ $j->journal_number }}</a>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $j->entry_date->format('d M Y') }}</div>
                            </td>
                            <td class="p-4 font-mono font-semibold text-slate-700">
                                {{ $j->reference_number ?: '-' }}
                            </td>
                            <td class="p-4 text-slate-800 max-w-md">
                                <div class="font-bold">{{ $j->description }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ count($j->items) }} baris akun terpilih</div>
                            </td>
                            <td class="p-4 text-right font-black text-sm text-slate-900">
                                Rp {{ number_format($j->total_debit, 0, ',', '.') }}
                            </td>
                            <td class="p-4">
                                @if($j->is_closing_entry)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-rose-100 text-rose-800 border border-rose-300">
                                        <i class="fa-solid fa-lock mr-1"></i> Jurnal Penutup
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        Jurnal Umum
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('accounting.journals.show', $j->id) }}" class="p-2 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold text-xs transition" title="Lihat Voucher Jurnal">
                                    <i class="fa-solid fa-eye mr-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Belum ada transaksi jurnal umum pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($journals->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $journals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
