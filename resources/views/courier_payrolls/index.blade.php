@extends('layouts.app')

@section('title', 'Manajemen Penggajian & Komisi Kurir')

@section('content')
<div class="space-y-6">
    <!-- Header Bar -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Manajemen Penggajian & Komisi Kurir</h2>
            <p class="text-xs text-slate-500 mt-0.5">Perhitungan otomatis gaji komisi kurir berdasarkan (Komisi x Jumlah Paket Terantar Sukses) per bulan.</p>
        </div>

        <!-- Filter Period Form -->
        <form action="{{ route('courier-payrolls.index') }}" method="GET" class="flex items-center space-x-2">
            <select name="month" class="px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                @foreach($monthsList as $num => $name)
                    <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <select name="year" class="px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                @for($y = date('Y'); $y >= 2024; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition">
                <i class="fa-solid fa-filter mr-1"></i> Tampilkan
            </button>
        </form>
    </div>

    <!-- Summary Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs text-slate-400 font-extrabold uppercase tracking-wider">Total Paket Terantar</div>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($grandTotalDeliveries) }} Paket</div>
                <div class="text-[11px] text-slate-500 mt-0.5">Periode {{ $monthsList[$month] }} {{ $year }}</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-boxes-packing"></i>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs text-slate-400 font-extrabold uppercase tracking-wider">Total Komisi Murni</div>
                <div class="text-2xl font-black text-indigo-600 mt-1">Rp {{ number_format($grandTotalCommission, 0, ',', '.') }}</div>
                <div class="text-[11px] text-indigo-600 font-bold mt-0.5">Komisi x Paket Sukses</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs text-slate-400 font-extrabold uppercase tracking-wider">Total Take Home Pay</div>
                <div class="text-2xl font-black text-emerald-600 mt-1">Rp {{ number_format($grandTotalNetSalary, 0, ',', '.') }}</div>
                <div class="text-[11px] text-emerald-600 font-bold mt-0.5">Total Bersih Ditagihkan</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs text-slate-400 font-extrabold uppercase tracking-wider">Status Pembayaran</div>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalPaidCount }} / {{ count($payrollsData) }} Paid</div>
                <div class="text-[11px] text-slate-500 mt-0.5">Kurir Sudah Dibayar</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-file-invoice-dollar text-indigo-600 mr-2"></i> Rekapitulasi Gaji Komisi Kurir - Periode {{ $monthsList[$month] }} {{ $year }}
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/70 text-slate-600 uppercase tracking-wider font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-4">Kurir & Hub</th>
                        <th class="p-4">Gaji Pokok</th>
                        <th class="p-4 text-center">Paket Terantar (ePOD)</th>
                        <th class="p-4">Komisi / Paket</th>
                        <th class="p-4">Subtotal Komisi</th>
                        <th class="p-4">Bonus / Tunjangan</th>
                        <th class="p-4">Potongan</th>
                        <th class="p-4 text-indigo-700">Total Gaji Bersih</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($payrollsData as $item)
                        @php
                            $c = $item['courier'];
                            $p = $item['payroll'];
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4">
                                <div class="font-extrabold text-slate-900 text-sm">{{ $c->name }}</div>
                                <div class="text-[11px] text-slate-500 flex items-center space-x-2 mt-0.5">
                                    <span class="font-mono text-indigo-600 font-bold">{{ $c->courier_code }}</span>
                                    <span>•</span>
                                    <span><i class="fa-solid fa-warehouse text-slate-400 mr-1"></i> {{ $c->branchHub ? $c->branchHub->name : 'Semua Hub' }}</span>
                                </div>
                            </td>
                            <td class="p-4 font-bold text-slate-900 font-mono">
                                @if(Auth::user()->hasPermission('courier-payrolls.index', 'view_salary'))
                                    Rp {{ number_format($item['basic_salary'], 0, ',', '.') }}
                                @else
                                    <span class="text-slate-400 font-normal">***</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                                    {{ $item['delivered_count'] }} Paket
                                </span>
                            </td>
                            <td class="p-4 font-semibold text-slate-700">
                                @if(Auth::user()->hasPermission('courier-payrolls.index', 'view_salary'))
                                    Rp {{ number_format($item['commission_rate'], 0, ',', '.') }}
                                @else
                                    <span class="text-slate-400 font-normal">***</span>
                                @endif
                            </td>
                            <td class="p-4 font-bold text-slate-900">
                                @if(Auth::user()->hasPermission('courier-payrolls.index', 'view_salary'))
                                    Rp {{ number_format($item['total_commission'], 0, ',', '.') }}
                                @else
                                    <span class="text-slate-400 font-normal">***</span>
                                @endif
                            </td>
                            <td class="p-4 font-semibold text-emerald-600">
                                @if(Auth::user()->hasPermission('courier-payrolls.index', 'view_salary'))
                                    + Rp {{ number_format($item['bonus'], 0, ',', '.') }}
                                @else
                                    <span class="text-slate-400 font-normal">***</span>
                                @endif
                            </td>
                            <td class="p-4 font-semibold text-rose-600">
                                @if(Auth::user()->hasPermission('courier-payrolls.index', 'view_salary'))
                                    - Rp {{ number_format($item['deduction'], 0, ',', '.') }}
                                @else
                                    <span class="text-slate-400 font-normal">***</span>
                                @endif
                            </td>
                            <td class="p-4 font-black text-sm text-emerald-600 font-mono">
                                @if(Auth::user()->hasPermission('courier-payrolls.index', 'view_salary'))
                                    Rp {{ number_format($item['net_salary'], 0, ',', '.') }}
                                @else
                                    <span class="text-slate-400 font-normal text-xs">*** Rahasia ***</span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($item['status'] === 'paid')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        <i class="fa-solid fa-circle-check mr-1"></i> Paid
                                    </span>
                                @elseif($item['status'] === 'approved')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-indigo-100 text-indigo-800 border border-indigo-300">
                                        Approved
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800 border border-amber-300">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-right space-x-1.5">
                                @if($p)
                                    <a href="{{ route('courier-payrolls.show', $p->id) }}" class="p-2 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold text-xs transition" title="Kelola & Rincian Gaji">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Process
                                    </a>
                                    <a href="{{ route('courier-payrolls.print-slip', $p->id) }}" target="_blank" class="p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-xs transition" title="Cetak Slip Gaji">
                                        <i class="fa-solid fa-print mr-1"></i> Slip
                                    </a>
                                @else
                                    <form action="{{ route('courier-payrolls.generate') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="courier_id" value="{{ $c->id }}">
                                        <input type="hidden" name="period_month" value="{{ $month }}">
                                        <input type="hidden" name="period_year" value="{{ $year }}">
                                        <input type="hidden" name="basic_salary" value="{{ $item['basic_salary'] }}">
                                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition">
                                            <i class="fa-solid fa-calculator mr-1"></i> Terbit Slip
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="p-8 text-center text-slate-400">Belum ada kurir aktif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
