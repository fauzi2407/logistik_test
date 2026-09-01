@extends('layouts.app')

@section('title', 'Tutup Buku Akhir Tahun')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Proses Tutup Buku Akhir Tahun</h2>
            <p class="text-xs text-slate-500 mt-0.5">Penutupan akun nominal (Pendapatan & Beban), penerbitan Jurnal Penutup, dan transfer saldo awal ke tahun buku baru.</p>
        </div>

        <!-- Filter Year Form -->
        <form action="{{ route('accounting.closing.index') }}" method="GET" class="flex items-center space-x-2">
            <span class="text-xs font-bold text-slate-500">Tahun Buku:</span>
            <select name="year" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                @for($y = date('Y'); $y >= 2024; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>

    <!-- Status Banner for Selected Year -->
    <div class="p-6 rounded-2xl {{ $isClosed ? 'bg-indigo-950 text-white' : 'bg-slate-900 text-white' }} shadow-xl space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                @if($isClosed)
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase bg-emerald-400 text-slate-950 border border-emerald-300">
                        <i class="fa-solid fa-lock mr-1"></i> STATUS: SUDAH TUTUP BUKU
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase bg-amber-400 text-slate-950 border border-amber-300">
                        <i class="fa-solid fa-unlock mr-1"></i> STATUS: BELUM TUTUP BUKU
                    </span>
                @endif
            </div>
            <div class="text-xs font-bold text-slate-400">Tahun Buku {{ $year }}</div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-slate-800 pt-4 text-xs font-semibold">
            <div>
                <div class="text-slate-400">Total Pendapatan {{ $year }}:</div>
                <div class="text-lg font-black text-emerald-400 font-mono mt-0.5">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>
            <div>
                <div class="text-slate-400">Total Beban Operasional {{ $year }}:</div>
                <div class="text-lg font-black text-rose-400 font-mono mt-0.5">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
            </div>
            <div>
                <div class="text-slate-400">Laba / (Rugi) Bersih Terakumulasi:</div>
                <div class="text-lg font-black text-indigo-300 font-mono mt-0.5">Rp {{ number_format($netProfitLoss, 0, ',', '.') }}</div>
            </div>
        </div>

        @if(!$isClosed)
            <form action="{{ route('accounting.closing.store') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MELAKUKAN TUTUP BUKU AKHIR TAHUN {{ $year }}?\n\nJurnal penutup akan diterbitkan secara otomatis dan saldo aset/kewajiban akan ditransfer sebagai Saldo Awal tahun {{ $year + 1 }}.')" class="pt-4 border-t border-slate-800 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                @csrf
                <input type="hidden" name="fiscal_year" value="{{ $year }}">
                <input type="text" name="notes" placeholder="Catatan tutup buku tahun {{ $year }}..." class="px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-xs text-white font-semibold flex-1">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs shadow-lg transition">
                    <i class="fa-solid fa-key mr-1.5"></i> Eksekusi Tutup Buku Tahun {{ $year }}
                </button>
            </form>
        @else
            <div class="p-3 bg-emerald-950/60 rounded-xl border border-emerald-800/60 text-xs text-emerald-300">
                <i class="fa-solid fa-circle-check mr-1.5"></i> Tahun buku {{ $year }} telah ditutup secara resmi. Seluruh saldo riil telah dioper sebagai Saldo Awal Tahun Buku {{ $year + 1 }}.
            </div>
        @endif
    </div>

    <!-- Closed History Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-history text-indigo-600 mr-2"></i> Riwayat Tutup Buku Perusahaan
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/70 text-slate-600 uppercase tracking-wider font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-4">Tahun Buku</th>
                        <th class="p-4">Waktu Eksekusi</th>
                        <th class="p-4">Hasil Laba / Rugi Bersih</th>
                        <th class="p-4">No. Jurnal Penutup</th>
                        <th class="p-4">Eksekutif / User</th>
                        <th class="p-4">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($closedHistory as $c)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-black text-indigo-600 text-sm font-mono">Tahun {{ $c->fiscal_year }}</td>
                            <td class="p-4 text-slate-700 font-bold">{{ $c->closed_at->format('d M Y H:i WIB') }}</td>
                            <td class="p-4 font-black font-mono {{ $c->net_profit_loss >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                Rp {{ number_format($c->net_profit_loss, 0, ',', '.') }}
                            </td>
                            <td class="p-4 font-mono font-bold text-indigo-600">
                                @if($c->closingJournalEntry)
                                    <a href="{{ route('accounting.journals.show', $c->closing_journal_entry_id) }}">{{ $c->closingJournalEntry->journal_number }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="p-4 font-semibold text-slate-800">{{ $c->closedUser ? $c->closedUser->name : 'System Admin' }}</td>
                            <td class="p-4 text-slate-500 max-w-xs truncate">{{ $c->notes ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Belum ada riwayat tutup buku akhir tahun.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
