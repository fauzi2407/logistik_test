@extends('layouts.app')

@section('title', 'Laporan Laba Rugi (Profit & Loss)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Laporan Laba Rugi (Profit & Loss Statement)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Rincian seluruh pendapatan dan beban operasional perusahaan logistik.</p>
        </div>
        <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs shadow-md transition">
            <i class="fa-solid fa-print mr-1.5"></i> Cetak Laporan
        </button>
    </div>

    <!-- Filter Form -->
    <form action="{{ route('accounting.profit-loss.index') }}" method="GET" class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between text-xs font-bold">
        <div class="flex items-center space-x-2">
            <span class="text-slate-500">Periode Tanggal:</span>
            <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-1.5 rounded-xl border border-slate-300">
            <span class="text-slate-400">s/d</span>
            <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-1.5 rounded-xl border border-slate-300">
        </div>
        <button type="submit" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition">
            <i class="fa-solid fa-filter mr-1"></i> Tampilkan P&L
        </button>
    </form>

    <!-- Net Profit Banner -->
    <div class="p-6 rounded-2xl {{ $netProfitLoss >= 0 ? 'bg-gradient-to-r from-emerald-600 to-teal-700 text-white' : 'bg-gradient-to-r from-rose-600 to-red-700 text-white' }} shadow-xl flex items-center justify-between">
        <div>
            <div class="text-xs font-bold uppercase tracking-wider opacity-80">HASIL LABA / RUGI BERSIH (NET PROFIT)</div>
            <h3 class="text-3xl font-black font-mono mt-1">
                {{ $netProfitLoss >= 0 ? 'LABA BERSIH: Rp ' . number_format($netProfitLoss, 0, ',', '.') : 'RUGI BERSIH: - Rp ' . number_format(abs($netProfitLoss), 0, ',', '.') }}
            </h3>
            <p class="text-xs opacity-90 mt-1">Periode {{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }}</p>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-2xl">
            <i class="fa-solid {{ $netProfitLoss >= 0 ? 'fa-chart-line' : 'fa-chart-line-down' }}"></i>
        </div>
    </div>

    <!-- P&L Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6">
        <!-- 1. Revenue Section -->
        <div class="space-y-3">
            <h3 class="text-xs font-extrabold text-indigo-700 uppercase tracking-wider border-b border-indigo-100 pb-2">
                1. PENDAPATAN OPERASIONAL (REVENUE)
            </h3>
            <table class="w-full text-xs">
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($revenueData as $row)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2 text-slate-500 font-mono w-24">{{ $row['account']->account_code }}</td>
                            <td class="py-2 text-slate-900 font-bold">{{ $row['account']->account_name }}</td>
                            <td class="py-2 text-right font-mono font-bold text-slate-900">Rp {{ number_format($row['amount'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-3 text-slate-400 italic">Belum ada transaksi pendapatan pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t-2 border-slate-300 font-black text-sm">
                    <tr>
                        <td colspan="2" class="py-2.5 text-slate-800 uppercase">TOTAL PENDAPATAN OPERASIONAL:</td>
                        <td class="py-2.5 text-right font-mono text-indigo-700">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- 2. Expense Section -->
        <div class="space-y-3 pt-4">
            <h3 class="text-xs font-extrabold text-rose-700 uppercase tracking-wider border-b border-rose-100 pb-2">
                2. BEBAN OPERASIONAL & KANTOR (EXPENSES)
            </h3>
            <table class="w-full text-xs">
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($expenseData as $row)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2 text-slate-500 font-mono w-24">{{ $row['account']->account_code }}</td>
                            <td class="py-2 text-slate-900 font-bold">{{ $row['account']->account_name }}</td>
                            <td class="py-2 text-right font-mono font-bold text-slate-900">Rp {{ number_format($row['amount'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-3 text-slate-400 italic">Belum ada transaksi beban pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t-2 border-slate-300 font-black text-sm">
                    <tr>
                        <td colspan="2" class="py-2.5 text-slate-800 uppercase">TOTAL BEBAN OPERASIONAL:</td>
                        <td class="py-2.5 text-right font-mono text-rose-700">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Summary Calculation -->
        <div class="p-4 rounded-xl bg-slate-900 text-white flex items-center justify-between text-sm font-black font-mono">
            <span>LABA / (RUGI) BERSIH OPERASIONAL:</span>
            <span class="{{ $netProfitLoss >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                Rp {{ number_format($netProfitLoss, 0, ',', '.') }}
            </span>
        </div>
    </div>
</div>
@endsection
