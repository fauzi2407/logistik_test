@extends('layouts.app')

@section('title', 'Saldo Awal Akun Keuangan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Saldo Awal Akun (Initial Balances)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Penetapan saldo awal akun pada pembukaan tahun buku akuntansi.</p>
        </div>

        <!-- Filter Year Form -->
        <form action="{{ route('accounting.initial-balances.index') }}" method="GET" class="flex items-center space-x-2">
            <span class="text-xs font-bold text-slate-500">Tahun Buku:</span>
            <select name="year" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                @for($y = date('Y') + 1; $y >= 2024; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>

    <!-- Balance Status Banner -->
    <div class="p-4 rounded-2xl {{ abs($totalDebit - $totalCredit) < 0.01 ? 'bg-emerald-50 border border-emerald-200 text-emerald-900' : 'bg-rose-50 border border-rose-200 text-rose-900' }} flex items-center justify-between text-xs font-bold">
        <div class="flex items-center space-x-2">
            @if(abs($totalDebit - $totalCredit) < 0.01)
                <i class="fa-solid fa-scale-balanced text-emerald-600 text-lg"></i>
                <span>Status Saldo Awal Tahun {{ $year }}: <strong class="text-emerald-700">SEIMBANG / BALANCE</strong></span>
            @else
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg"></i>
                <span>Status Saldo Awal Tahun {{ $year }}: <strong class="text-rose-700">BELUM SEIMBANG (Selisih: Rp {{ number_format(abs($totalDebit - $totalCredit), 0, ',', '.') }})</strong></span>
            @endif
        </div>
        <div class="flex items-center space-x-4 font-mono">
            <span>Total Debit: <strong>Rp {{ number_format($totalDebit, 0, ',', '.') }}</strong></span>
            <span>Total Kredit: <strong>Rp {{ number_format($totalCredit, 0, ',', '.') }}</strong></span>
        </div>
    </div>

    <!-- Form Saldo Awal Table -->
    <form action="{{ route('accounting.initial-balances.store') }}" method="POST" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden space-y-4">
        @csrf
        <input type="hidden" name="fiscal_year" value="{{ $year }}">

        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-calculator text-indigo-600 mr-2"></i> Input Nominal Saldo Awal Tahun {{ $year }}
            </h3>
            <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition">
                <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan Saldo Awal
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/70 text-slate-600 uppercase tracking-wider font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-4 w-32">Kode Akun</th>
                        <th class="p-4">Nama Akun COA</th>
                        <th class="p-4">Tipe Akun</th>
                        <th class="p-4 w-48 text-right">Debit (Rp)</th>
                        <th class="p-4 w-48 text-right">Kredit (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($accounts as $acc)
                        @php
                            $bal = $savedBalances->get($acc->id);
                            $deb = $bal ? $bal->debit_amount : 0;
                            $cred = $bal ? $bal->credit_amount : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-black text-indigo-600">{{ $acc->account_code }}</td>
                            <td class="p-4 font-bold text-slate-900">{{ $acc->account_name }}</td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $acc->type_badge_class }}">
                                    {{ $acc->account_type }} ({{ $acc->normal_balance }})
                                </span>
                            </td>
                            <td class="p-3 text-right">
                                <input type="number" step="0.01" min="0" name="balances[{{ $acc->id }}][debit]" value="{{ $deb ?: '' }}" placeholder="0" class="w-40 px-3 py-1.5 rounded-xl border border-slate-300 text-right font-mono font-bold text-slate-900 focus:ring-2 focus:ring-indigo-500">
                            </td>
                            <td class="p-3 text-right">
                                <input type="number" step="0.01" min="0" name="balances[{{ $acc->id }}][credit]" value="{{ $cred ?: '' }}" placeholder="0" class="w-40 px-3 py-1.5 rounded-xl border border-slate-300 text-right font-mono font-bold text-slate-900 focus:ring-2 focus:ring-indigo-500">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg transition">
                <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan Saldo Awal Tahun {{ $year }}
            </button>
        </div>
    </form>
</div>
@endsection
