@extends('layouts.app')

@section('title', 'Laporan Neraca (Balance Sheet)')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Laporan Neraca Keuangan (Balance Sheet)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Ringkasan posisi Aset (Aktiva), Kewajiban (Pasiva), & Modal Perusahaan.</p>
        </div>
        <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs shadow-md transition">
            <i class="fa-solid fa-print mr-1.5"></i> Cetak Neraca
        </button>
    </div>

    <!-- Filter Form -->
    <form action="{{ route('accounting.balance-sheet.index') }}" method="GET" class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between text-xs font-bold">
        <div class="flex items-center space-x-2">
            <span class="text-slate-500">Posisi Per Tanggal:</span>
            <input type="date" name="as_of_date" value="{{ $asOfDate }}" class="px-3 py-1.5 rounded-xl border border-slate-300">
        </div>
        <button type="submit" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition">
            <i class="fa-solid fa-filter mr-1"></i> Tampilkan Neraca
        </button>
    </form>

    <!-- Balance Status Banner -->
    @php
        $isBalanced = abs($totalAssets - $totalLiabilitiesAndEquity) < 0.01;
    @endphp
    <div class="p-4 rounded-2xl {{ $isBalanced ? 'bg-emerald-50 border border-emerald-200 text-emerald-900' : 'bg-rose-50 border border-rose-200 text-rose-900' }} flex items-center justify-between text-xs font-bold">
        <div class="flex items-center space-x-2">
            <i class="fa-solid {{ $isBalanced ? 'fa-scale-balanced text-emerald-600 text-lg' : 'fa-triangle-exclamation text-rose-600 text-lg' }}"></i>
            <span>Persamaan Akuntansi: <strong>{{ $isBalanced ? 'SEIMBANG / BALANCE (Aset = Pasiva)' : 'BELUM SEIMBANG' }}</strong></span>
        </div>
        <div class="font-mono text-xs">
            Aset: <strong>Rp {{ number_format($totalAssets, 0, ',', '.') }}</strong> == Pasiva: <strong>Rp {{ number_format($totalLiabilitiesAndEquity, 0, ',', '.') }}</strong>
        </div>
    </div>

    <!-- Two-Column Balance Sheet Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Left Column: Assets (Aktiva) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
            <h3 class="text-xs font-extrabold text-indigo-700 uppercase tracking-wider border-b border-indigo-100 pb-2">
                AKTIVA / ASET (ASSETS)
            </h3>

            <table class="w-full text-xs">
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($assetData as $row)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2 text-slate-500 font-mono w-20">{{ $row['account']->account_code }}</td>
                            <td class="py-2 text-slate-900 font-bold">{{ $row['account']->account_name }}</td>
                            <td class="py-2 text-right font-mono font-bold text-slate-900">Rp {{ number_format($row['balance'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-3 text-slate-400 italic">Belum ada data saldo aset.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t-2 border-slate-900 font-black text-sm">
                    <tr>
                        <td colspan="2" class="py-3 text-slate-900 uppercase">JUMLAH TOTAL AKTIVA / ASET:</td>
                        <td class="py-3 text-right font-mono text-indigo-700">Rp {{ number_format($totalAssets, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Right Column: Liabilities & Equity (Pasiva) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6">
            <!-- 1. Liabilities -->
            <div class="space-y-3">
                <h3 class="text-xs font-extrabold text-amber-700 uppercase tracking-wider border-b border-amber-100 pb-2">
                    KEWAJIBAN / UTANG (LIABILITIES)
                </h3>

                <table class="w-full text-xs">
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($liabilityData as $row)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2 text-slate-500 font-mono w-20">{{ $row['account']->account_code }}</td>
                                <td class="py-2 text-slate-900 font-bold">{{ $row['account']->account_name }}</td>
                                <td class="py-2 text-right font-mono font-bold text-slate-900">Rp {{ number_format($row['balance'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-2 text-slate-400 italic">Tidak ada utang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-t border-slate-200 font-extrabold text-xs">
                        <tr>
                            <td colspan="2" class="py-2 text-slate-800 uppercase">Subtotal Kewajiban:</td>
                            <td class="py-2 text-right font-mono text-amber-700">Rp {{ number_format($totalLiabilities, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- 2. Equity -->
            <div class="space-y-3 pt-2">
                <h3 class="text-xs font-extrabold text-emerald-700 uppercase tracking-wider border-b border-emerald-100 pb-2">
                    MODAL / EKUITAS (EQUITY)
                </h3>

                <table class="w-full text-xs">
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($equityData as $row)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2 text-slate-500 font-mono w-20">{{ $row['account']->account_code }}</td>
                                <td class="py-2 text-slate-900 font-bold">{{ $row['account']->account_name }}</td>
                                <td class="py-2 text-right font-mono font-bold text-slate-900">Rp {{ number_format($row['balance'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <!-- Current Year Net Income -->
                        <tr class="bg-indigo-50/50">
                            <td class="py-2 text-indigo-500 font-mono w-20">3301</td>
                            <td class="py-2 text-indigo-900 font-bold">Laba Tahun Berjalan (Net Income)</td>
                            <td class="py-2 text-right font-mono font-black text-indigo-700">Rp {{ number_format($currentYearNetIncome, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="border-t border-slate-200 font-extrabold text-xs">
                        <tr>
                            <td colspan="2" class="py-2 text-slate-800 uppercase">Subtotal Modal & Laba Berjalan:</td>
                            <td class="py-2 text-right font-mono text-emerald-700">Rp {{ number_format($totalEquity + $currentYearNetIncome, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Grand Total Liabilities + Equity -->
            <div class="pt-4 border-t-2 border-slate-900 flex items-center justify-between text-sm font-black font-mono">
                <span class="uppercase text-slate-900">JUMLAH TOTAL PASIVA:</span>
                <span class="text-indigo-700">Rp {{ number_format($totalLiabilitiesAndEquity, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
