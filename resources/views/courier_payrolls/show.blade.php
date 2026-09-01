@extends('layouts.app')

@section('title', 'Detail Penggajian Kurir ' . $payroll->payroll_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Payroll Slip Ref</div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight font-mono">{{ $payroll->payroll_code }}</h2>
            <div class="text-xs text-slate-500 mt-1">
                Kurir: <span class="font-bold text-slate-900">{{ $payroll->courier->name }}</span> ({{ $payroll->courier->courier_code }}) • 
                Periode: <span class="font-bold text-indigo-600">{{ $payroll->month_name }} {{ $payroll->period_year }}</span>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('courier-payrolls.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold text-xs transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
            </a>
            <a href="{{ route('courier-payrolls.print-slip', $payroll->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-md">
                <i class="fa-solid fa-print mr-1"></i> Cetak Slip Gaji Official
            </a>
        </div>
    </div>

    <!-- Status Banner -->
    <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-900 via-slate-950 to-indigo-950 text-white shadow-xl flex items-center justify-between">
        <div>
            <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Status Pembayaran Gaji</div>
            <div class="text-xl font-extrabold uppercase tracking-tight mt-0.5 flex items-center space-x-2">
                @if($payroll->status === 'paid')
                    <span class="text-emerald-400"><i class="fa-solid fa-circle-check mr-1.5"></i> PAID (LUNAS DIBAYARKAN)</span>
                @elseif($payroll->status === 'approved')
                    <span class="text-indigo-400"><i class="fa-solid fa-circle-check mr-1.5"></i> APPROVED (SIAP DIBAYAR)</span>
                @else
                    <span class="text-amber-400"><i class="fa-solid fa-clock mr-1.5"></i> DRAFT (BELUM DISETUJUI)</span>
                @endif
            </div>
            @if($payroll->payment_date)
                <div class="text-xs text-slate-400 mt-1">Tanggal Bayar: {{ $payroll->payment_date->format('d M Y H:i WIB') }}</div>
            @endif
        </div>
        <div class="text-right">
            <div class="text-xs text-slate-400 font-bold uppercase">Total Gaji Bersih (Take Home Pay)</div>
            <div class="text-2xl font-black text-emerald-400">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Form Update Bonus, Potongan, & Status -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">
            <i class="fa-solid fa-calculator text-indigo-600 mr-2"></i> Perhitungan & Penyesuaian Komisi Gaji
        </h3>

        <form action="{{ route('courier-payrolls.generate') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="courier_id" value="{{ $payroll->courier_id }}">
            <input type="hidden" name="period_month" value="{{ $payroll->period_month }}">
            <input type="hidden" name="period_year" value="{{ $payroll->period_year }}">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs font-semibold">
                <div class="p-3 bg-indigo-50/70 rounded-xl border border-indigo-200">
                    <span class="text-indigo-900 font-bold uppercase tracking-wider text-[10px]">Gaji Pokok (Basic Salary):</span>
                    <input type="number" name="basic_salary" value="{{ old('basic_salary', $payroll->basic_salary ?? 0) }}" min="0" step="1000" class="w-full px-2.5 py-1.5 mt-1 rounded-lg border border-indigo-300 font-black text-indigo-900 text-sm">
                </div>

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <span class="text-slate-500 font-bold uppercase tracking-wider text-[10px]">Total Pengantaran Sukses:</span>
                    <div class="text-base font-extrabold text-slate-900 mt-1">{{ $payroll->total_deliveries }} Paket</div>
                </div>

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <span class="text-slate-500 font-bold uppercase tracking-wider text-[10px]">Komisi Per Paket:</span>
                    <div class="text-base font-extrabold text-indigo-600 mt-1">Rp {{ number_format($payroll->commission_per_delivery, 0, ',', '.') }}</div>
                </div>

                <div class="p-3 bg-indigo-50 rounded-xl border border-indigo-200">
                    <span class="text-indigo-800 font-bold uppercase tracking-wider text-[10px]">Subtotal Komisi Murni:</span>
                    <div class="text-base font-extrabold text-indigo-900 mt-1">Rp {{ number_format($payroll->total_commission, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bonus / Tunjangan Tambahan (Rp)</label>
                    <input type="number" name="bonus_amount" value="{{ old('bonus_amount', $payroll->bonus_amount) }}" min="0" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-emerald-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Potongan Gaji (Rp)</label>
                    <input type="number" name="deduction_amount" value="{{ old('deduction_amount', $payroll->deduction_amount) }}" min="0" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-rose-600">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Pembayaran Gaji *</label>
                    <select name="status" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                        <option value="draft" {{ $payroll->status == 'draft' ? 'selected' : '' }}>Draft (Belum Disetujui)</option>
                        <option value="approved" {{ $payroll->status == 'approved' ? 'selected' : '' }}>Approved (Disetujui Disbursment)</option>
                        <option value="paid" {{ $payroll->status == 'paid' ? 'selected' : '' }}>Paid (Lunas Dibayarkan)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Metode Pembayaran</label>
                    <select name="payment_method" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                        <option value="Transfer Bank" {{ $payroll->payment_method == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="Cash / Tunai" {{ $payroll->payment_method == 'Cash / Tunai' ? 'selected' : '' }}>Cash / Tunai</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Catatan Tambahan / Keterangan</label>
                <input type="text" name="notes" value="{{ old('notes', $payroll->notes) }}" placeholder="Catatan bonus atau potongan komisi..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md">
                    <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan & Update Penggajian
                </button>
            </div>
        </form>
    </div>

    <!-- Itemized List of Delivered Shipments -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-list-check text-indigo-600 mr-2"></i> Rincian {{ count($deliveredShipments) }} Paket Resi Terantar (ePOD)
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/70 text-slate-600 uppercase tracking-wider font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-3 w-10 text-center">#</th>
                        <th class="p-3">No. Resi AWB</th>
                        <th class="p-3">Nama Penerima & Kota</th>
                        <th class="p-3">Tanggal Diterima</th>
                        <th class="p-3 text-right">Biaya Komisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($deliveredShipments as $s)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3 text-center text-slate-400 font-mono">{{ $loop->iteration }}</td>
                            <td class="p-3 font-mono font-bold text-indigo-600">
                                <a href="{{ route('shipments.show', $s->id) }}" target="_blank">{{ $s->tracking_number }}</a>
                            </td>
                            <td class="p-3 font-semibold text-slate-800">
                                {{ $s->recipient_name }} <span class="text-slate-400">({{ $s->recipient_city }})</span>
                            </td>
                            <td class="p-3 text-slate-600">
                                {{ $s->pod_delivered_at ? $s->pod_delivered_at->format('d M Y H:i') : $s->updated_at->format('d M Y H:i') }}
                            </td>
                            <td class="p-3 text-right font-bold text-emerald-600">
                                Rp {{ number_format($payroll->commission_per_delivery, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-400">Belum ada paket terantar pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
