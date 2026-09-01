@extends('layouts.app')

@section('title', 'Slip Gaji Saya (Paid)')

@section('content')
<div class="space-y-6">
    <!-- Header Bar -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">
                    <i class="fa-solid fa-circle-check mr-1"></i> Portal Kurir / Driver
                </span>
                <span class="text-xs text-slate-400 font-mono">({{ $loggedInCourier->courier_code }})</span>
            </div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Riwayat Slip Gaji Komisi Saya</h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftar slip gaji komisi pengantaran yang telah disetujui dan dibayarkan (Status Paid/Lunas) oleh Finance.</p>
        </div>

        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold text-xs transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Dashboard Kurir
        </a>
    </div>

    <!-- Info Banner -->
    <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-900 text-xs font-semibold flex items-center space-x-3">
        <i class="fa-solid fa-shield-halved text-indigo-600 text-xl shrink-0"></i>
        <div>
            <strong>Transparansi Pembayaran:</strong> Halaman ini menampilkan slip gaji resmi yang statusnya telah <strong>Paid (Lunas Dibayarkan)</strong>. Slip gaji mencakup kalkulasi otomatis jumlah paket terantar sukses (ePOD), komisi per paket, bonus, dan potongan.
        </div>
    </div>

    <!-- Paid Payroll Slips Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-receipt text-emerald-600 mr-2"></i> Daftar Slip Gaji Komisi Lunas (Paid)
            </h3>
            <span class="text-xs font-bold text-slate-500">Total: {{ count($paidPayrolls) }} Slip Gaji</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/70 text-slate-600 uppercase tracking-wider font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-4">No. Kode Slip</th>
                        <th class="p-4">Periode Bulan & Tahun</th>
                        <th class="p-4">Gaji Pokok</th>
                        <th class="p-4 text-center">Paket Sukses (ePOD)</th>
                        <th class="p-4">Komisi / Paket</th>
                        <th class="p-4">Subtotal Komisi</th>
                        <th class="p-4 text-emerald-700">Total Gaji Bersih</th>
                        <th class="p-4">Tanggal & Metode Bayar</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($paidPayrolls as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-bold text-indigo-600">
                                <a href="{{ route('courier-payrolls.show', $p->id) }}">{{ $p->payroll_code }}</a>
                            </td>
                            <td class="p-4 font-bold text-slate-900 text-sm">
                                {{ $p->month_name }} {{ $p->period_year }}
                            </td>
                            <td class="p-4 font-bold text-slate-900 font-mono">
                                Rp {{ number_format($p->basic_salary ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                                    {{ $p->total_deliveries }} Paket
                                </span>
                            </td>
                            <td class="p-4 font-semibold text-slate-700">
                                Rp {{ number_format($p->commission_per_delivery, 0, ',', '.') }}
                            </td>
                            <td class="p-4 font-bold text-slate-900">
                                Rp {{ number_format($p->total_commission, 0, ',', '.') }}
                            </td>
                            <td class="p-4 font-black text-sm text-emerald-600">
                                Rp {{ number_format($p->net_salary, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-slate-600">
                                <div>{{ $p->payment_date ? $p->payment_date->format('d M Y H:i') : '-' }}</div>
                                <div class="text-[11px] text-slate-400 font-bold uppercase">{{ $p->payment_method ?: 'Transfer Bank' }}</div>
                            </td>
                            <td class="p-4 text-right space-x-1.5">
                                <a href="{{ route('courier-payrolls.show', $p->id) }}" class="p-2 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold text-xs transition" title="Lihat Rincian Paket">
                                    <i class="fa-solid fa-eye mr-1"></i> Rincian
                                </a>
                                <a href="{{ route('courier-payrolls.print-slip', $p->id) }}" target="_blank" class="p-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800 font-bold text-xs transition shadow-sm" title="Cetak Slip Gaji Official">
                                    <i class="fa-solid fa-print mr-1"></i> Slip PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                Belum ada slip gaji komisi berstatus <strong>Paid (Lunas)</strong> untuk kurir Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
