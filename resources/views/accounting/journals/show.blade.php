@extends('layouts.app')

@section('title', 'Voucher Jurnal ' . $journal->journal_number)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header Bar -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">No. Jurnal Voucher</div>
            <h2 class="text-2xl font-black text-slate-900 font-mono tracking-tight">{{ $journal->journal_number }}</h2>
            <div class="text-xs text-slate-500 mt-0.5">Tanggal Transaksi: <strong>{{ $journal->entry_date->format('d F Y') }}</strong></div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('accounting.journals.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold text-xs transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
            </a>
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition">
                <i class="fa-solid fa-print mr-1"></i> Cetak Voucher
            </button>
        </div>
    </div>

    <!-- Voucher Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xl p-8 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-200 pb-4">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-wider text-indigo-600">VOUCHER JURNAL UMUM</span>
                <h3 class="text-base font-extrabold text-slate-900 mt-0.5">{{ $journal->description }}</h3>
            </div>
            <div class="text-right text-xs">
                <div class="text-slate-400 font-bold uppercase">No. Referensi:</div>
                <div class="font-mono font-bold text-slate-800 mt-0.5">{{ $journal->reference_number ?: '-' }}</div>
            </div>
        </div>

        <table class="w-full text-left text-xs">
            <thead class="bg-slate-100 text-slate-600 uppercase font-bold border-b border-slate-200">
                <tr>
                    <th class="p-3">Kode Akun</th>
                    <th class="p-3">Nama Akun COA</th>
                    <th class="p-3 text-right">Debit (Rp)</th>
                    <th class="p-3 text-right">Kredit (Rp)</th>
                    <th class="p-3">Memo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
                @foreach($journal->items as $item)
                    <tr class="hover:bg-slate-50/80">
                        <td class="p-3 font-mono font-bold text-indigo-600">{{ $item->account->account_code }}</td>
                        <td class="p-3 font-bold text-slate-900">
                            <span class="{{ $item->credit_amount > 0 ? 'ml-4 text-slate-600 font-normal' : '' }}">
                                {{ $item->account->account_name }}
                            </span>
                        </td>
                        <td class="p-3 text-right font-bold text-slate-900">
                            {{ $item->debit_amount > 0 ? number_format($item->debit_amount, 0, ',', '.') : '-' }}
                        </td>
                        <td class="p-3 text-right font-bold text-slate-900">
                            {{ $item->credit_amount > 0 ? number_format($item->credit_amount, 0, ',', '.') : '-' }}
                        </td>
                        <td class="p-3 text-slate-500 text-[11px]">{{ $item->memo ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-50 font-black border-t-2 border-slate-300 text-sm">
                <tr>
                    <td colspan="2" class="p-3 text-right uppercase">TOTAL:</td>
                    <td class="p-3 text-right text-indigo-600">Rp {{ number_format($journal->total_debit, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-emerald-600">Rp {{ number_format($journal->total_credit, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div class="pt-8 border-t border-slate-200 grid grid-cols-3 gap-4 text-center text-xs">
            <div>
                <p class="text-slate-400 font-bold mb-10">Dibuat Oleh,</p>
                <p class="font-extrabold text-slate-900 underline">{{ $journal->createdUser ? $journal->createdUser->name : 'Administrator' }}</p>
                <p class="text-[10px] text-slate-400">Staff Akuntansi</p>
            </div>
            <div>
                <p class="text-slate-400 font-bold mb-10">Diperiksa Oleh,</p>
                <p class="font-extrabold text-slate-900 underline">( ........................ )</p>
                <p class="text-[10px] text-slate-400">Supervisor / Finance</p>
            </div>
            <div>
                <p class="text-slate-400 font-bold mb-10">Disetujui Oleh,</p>
                <p class="font-extrabold text-slate-900 underline">( ........................ )</p>
                <p class="text-[10px] text-slate-400">Manager Keuangan</p>
            </div>
        </div>
    </div>
</div>
@endsection
