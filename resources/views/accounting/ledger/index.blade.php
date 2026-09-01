@extends('layouts.app')

@section('title', 'Buku Besar (General Ledger)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Buku Besar (General Ledger)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Rincian mutasi transaksi per akun COA & perhitungan saldo berjalan (running balance).</p>
        </div>
    </div>

    <!-- Filter Form -->
    <form action="{{ route('accounting.ledger.index') }}" method="GET" class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-4 text-xs font-bold items-end">
        <div>
            <label class="block text-slate-700 mb-1">Pilih Akun COA *</label>
            <select name="chart_of_account_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-bold text-slate-800">
                @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}" {{ $selectedAccount && $selectedAccount->id == $acc->id ? 'selected' : '' }}>
                        {{ $acc->account_code }} - {{ $acc->account_name }} ({{ $acc->normal_balance }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-slate-700 mb-1">Tanggal Mulai</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
        </div>

        <div>
            <label class="block text-slate-700 mb-1">Tanggal Akhir</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
        </div>

        <div>
            <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-md transition">
                <i class="fa-solid fa-filter mr-1.5"></i> Tampilkan Buku Besar
            </button>
        </div>
    </form>

    @if($selectedAccount)
        <!-- Ledger Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden space-y-4">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row items-start md:items-center justify-between gap-2">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase">AKUN BUKU BESAR:</span>
                    <h3 class="text-lg font-black text-slate-900 font-mono">
                        {{ $selectedAccount->account_code }} - {{ $selectedAccount->account_name }}
                    </h3>
                    <div class="text-xs text-slate-500 mt-0.5">
                        Tipe: <span class="font-bold border px-2 py-0.5 rounded text-[10px] {{ $selectedAccount->type_badge_class }}">{{ $selectedAccount->account_type }}</span> •
                        Saldo Normal: <strong class="text-indigo-600">{{ $selectedAccount->normal_balance }}</strong>
                    </div>
                </div>

                <div class="text-right bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
                    <div class="text-[10px] font-extrabold uppercase text-slate-400">SALDO AWAL PERIODE INI</div>
                    <div class="text-base font-black text-indigo-600 font-mono">Rp {{ number_format($openingBalance, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100/70 text-slate-600 uppercase tracking-wider font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-3 w-28">Tanggal</th>
                            <th class="p-3 w-36">No. Jurnal</th>
                            <th class="p-3 w-32">Ref</th>
                            <th class="p-3">Keterangan Transaksi</th>
                            <th class="p-3 text-right w-36">Debit (Rp)</th>
                            <th class="p-3 text-right w-36">Kredit (Rp)</th>
                            <th class="p-3 text-right w-40 text-indigo-700">Saldo Akhir (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <!-- Row Saldo Awal -->
                        <tr class="bg-slate-50/50 italic font-semibold">
                            <td class="p-3 text-slate-500">{{ date('d M Y', strtotime($startDate)) }}</td>
                            <td class="p-3 font-mono text-slate-400">-</td>
                            <td class="p-3 text-slate-400">-</td>
                            <td class="p-3 text-slate-700 font-bold">SALDO AWAL PERIODE {{ date('d/m/Y', strtotime($startDate)) }}</td>
                            <td class="p-3 text-right font-mono text-slate-400">-</td>
                            <td class="p-3 text-right font-mono text-slate-400">-</td>
                            <td class="p-3 text-right font-mono font-black text-indigo-700">Rp {{ number_format($openingBalance, 0, ',', '.') }}</td>
                        </tr>

                        @php
                            $runningBalance = $openingBalance;
                            $totalDebitMutasi = 0;
                            $totalCreditMutasi = 0;
                        @endphp

                        @forelse($ledgerEntries as $item)
                            @php
                                $deb = $item->debit_amount;
                                $cred = $item->credit_amount;

                                $totalDebitMutasi += $deb;
                                $totalCreditMutasi += $cred;

                                if ($selectedAccount->normal_balance === 'Debit') {
                                    $runningBalance += ($deb - $cred);
                                } else {
                                    $runningBalance += ($cred - $deb);
                                }
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-3 text-slate-700 font-semibold">{{ $item->journalEntry->entry_date->format('d M Y') }}</td>
                                <td class="p-3 font-mono font-bold text-indigo-600">
                                    <a href="{{ route('accounting.journals.show', $item->journal_entry_id) }}">{{ $item->journalEntry->journal_number }}</a>
                                </td>
                                <td class="p-3 font-mono text-slate-600">{{ $item->journalEntry->reference_number ?: '-' }}</td>
                                <td class="p-3 font-medium text-slate-900">
                                    {{ $item->memo ?: $item->journalEntry->description }}
                                </td>
                                <td class="p-3 text-right font-mono font-bold text-slate-900">
                                    {{ $deb > 0 ? number_format($deb, 0, ',', '.') : '-' }}
                                </td>
                                <td class="p-3 text-right font-mono font-bold text-slate-900">
                                    {{ $cred > 0 ? number_format($cred, 0, ',', '.') : '-' }}
                                </td>
                                <td class="p-3 text-right font-mono font-extrabold text-indigo-700">
                                    Rp {{ number_format($runningBalance, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-6 text-center text-slate-400">Tidak ada mutasi transaksi pada rentang tanggal ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-100 font-black text-xs border-t-2 border-slate-300">
                        <tr>
                            <td colspan="4" class="p-3 text-right uppercase">TOTAL MUTASI PERIODE INI:</td>
                            <td class="p-3 text-right text-indigo-700 font-mono">Rp {{ number_format($totalDebitMutasi, 0, ',', '.') }}</td>
                            <td class="p-3 text-right text-emerald-700 font-mono">Rp {{ number_format($totalCreditMutasi, 0, ',', '.') }}</td>
                            <td class="p-3 text-right text-indigo-900 font-mono text-sm">Rp {{ number_format($runningBalance, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
