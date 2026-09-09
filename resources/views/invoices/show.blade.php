@extends('layouts.app')

@section('title', 'Detail Invoice - ' . $invoice->invoice_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header & Action Bar -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight font-mono">{{ $invoice->invoice_number }}</h2>
                {!! $invoice->status_badge !!}
            </div>
            <p class="text-xs text-slate-500 mt-1">Diterbitkan pada {{ $invoice->invoice_date->format('d M Y') }} • Jatuh Tempo: <span class="font-bold text-rose-600">{{ $invoice->due_date->format('d M Y') }}</span></p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('invoices.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
            </a>
            <a href="{{ route('invoices.print', $invoice->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
                <i class="fa-solid fa-print mr-1"></i> Cetak Invoice
            </a>
        </div>
    </div>

    <!-- Update Payment Status Box (Admin / Staff Only) -->
    @if(!Auth::user()->isCustomer())
        <div class="p-5 rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white shadow-xl space-y-3">
            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-indigo-300 flex items-center">
                    <i class="fa-solid fa-credit-card mr-2"></i> Update Status Pembayaran Invoice
                </h3>
                @if($invoice->isPaid())
                    <span class="text-[11px] text-emerald-400 font-bold"><i class="fa-solid fa-check-double mr-1"></i> Telah Dibayar pada {{ $invoice->payment_date ? $invoice->payment_date->format('d M Y, H:i') : '-' }}</span>
                @endif
            </div>

            <form action="{{ route('invoices.payment', $invoice->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-400 mb-1">Status Pembayaran</label>
                    <select name="status" class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white font-bold">
                        <option value="unpaid" {{ $invoice->status == 'unpaid' ? 'selected' : '' }}>Belum Dibayar (Unpaid)</option>
                        <option value="paid" {{ $invoice->status == 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                        <option value="cancelled" {{ $invoice->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-slate-400 mb-1">Metode Pembayaran</label>
                    <input type="text" name="payment_method" value="{{ $invoice->payment_method ?? 'Transfer Bank BCA' }}" placeholder="Misal: Transfer BCA, VA" class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white font-bold">
                </div>

                <div>
                    <label class="block text-slate-400 mb-1">Tgl Pembayaran</label>
                    <input type="date" name="payment_date" value="{{ $invoice->payment_date ? $invoice->payment_date->format('Y-m-d') : date('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white font-bold">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full py-2 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 font-bold text-white shadow-lg shadow-emerald-600/30 transition">
                        Simpan Status Pembayaran
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Invoice Details Main Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6">
        <!-- Customer & DO Header Info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-b border-slate-100 pb-6">
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Ditagihkan Kepada (Customer):</div>
                <h3 class="text-base font-black text-slate-900">{{ $invoice->customer ? ($invoice->customer->company_name ?: $invoice->customer->name) : 'Customer Umum' }}</h3>
                <div class="text-xs text-slate-600 mt-1"><i class="fa-solid fa-user mr-1 text-slate-400"></i> Kontak: {{ $invoice->customer ? $invoice->customer->name : '-' }}</div>
                <div class="text-xs text-slate-600"><i class="fa-solid fa-phone mr-1 text-slate-400"></i> {{ $invoice->customer ? $invoice->customer->phone : '-' }}</div>
                <div class="text-xs text-slate-600 mt-0.5"><i class="fa-solid fa-location-dot mr-1 text-slate-400"></i> {{ $invoice->customer ? $invoice->customer->address : '-' }}, {{ $invoice->customer ? $invoice->customer->city : '' }}</div>
            </div>

            <div class="sm:text-right space-y-1 text-xs">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Detail Referensi Invoice:</div>
                <div><span class="text-slate-400">No. Invoice:</span> <span class="font-mono font-bold text-indigo-600 text-sm">{{ $invoice->invoice_number }}</span></div>
                @if($invoice->deliveryOrder)
                    <div class="flex items-center sm:justify-end gap-1.5 flex-wrap">
                        <span class="text-slate-400">Ref Surat Jalan DO:</span>
                        <a href="{{ route('delivery-orders.show', $invoice->deliveryOrder->id) }}" class="font-mono font-bold text-slate-800 hover:underline">{{ $invoice->deliveryOrder->do_number }}</a>
                        {!! $invoice->deliveryOrder->status_badge !!}
                    </div>
                @endif
                <div><span class="text-slate-400">Tgl Penerbitan:</span> <span class="font-semibold text-slate-800">{{ $invoice->invoice_date->format('d M Y') }}</span></div>
                <div><span class="text-slate-400">Tgl Jatuh Tempo:</span> <span class="font-bold text-rose-600">{{ $invoice->due_date->format('d M Y') }}</span></div>
            </div>
        </div>

        <!-- Shipping Items Breakdown Table -->
        <div>
            <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 mb-3">
                <i class="fa-solid fa-boxes-packing text-indigo-600 mr-1.5"></i> Rincian Pengiriman Barang
            </h4>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border border-slate-100 rounded-xl overflow-hidden">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                        <tr>
                            <th class="p-3">No.</th>
                            <th class="p-3">Penerima & Alamat Tujuan</th>
                            <th class="p-3">Ref No. / Jenis Barang</th>
                            <th class="p-3">Nomor AWB Resi</th>
                            <th class="p-3 text-right">Biaya Ongkir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @if($invoice->deliveryOrder && $invoice->deliveryOrder->items->count() > 0)
                            @foreach($invoice->deliveryOrder->items as $idx => $item)
                                @php
                                    $itemFee = $item->shipment ? $item->shipment->total_amount : (20000 * max(1, (int) ceil((float)$item->weight_kg)));
                                @endphp
                                <tr class="hover:bg-slate-50/80">
                                    <td class="p-3 font-bold text-slate-400">{{ $idx + 1 }}</td>
                                    <td class="p-3">
                                        <div class="font-bold text-slate-900">{{ $item->recipient_name }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $item->recipient_city }} ({{ $item->recipient_address }})</div>
                                    </td>
                                    <td class="p-3">
                                        <div class="font-semibold text-slate-800">{{ $item->item_name }} ({{ $item->weight_kg }} kg)</div>
                                        <div class="text-[10px] text-slate-400 font-mono">Ref: {{ $item->account_ref ?? '-' }}</div>
                                    </td>
                                    <td class="p-3 font-mono font-bold text-indigo-600">
                                        {{ $item->shipment ? $item->shipment->tracking_number : '-' }}
                                    </td>
                                    <td class="p-3 text-right font-black text-slate-900">
                                        Rp {{ number_format($itemFee, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="p-4 font-bold text-slate-700">Biaya Pengiriman Logistik</td>
                                <td class="p-4 text-right font-black text-slate-900">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Calculation & Total Summary -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 pt-3 border-t border-slate-100">
            <div class="text-xs space-y-2 max-w-sm">
                @php
                    $bankName = \App\Models\AppSetting::get('bank_name', 'Bank BCA');
                    $bankNo = \App\Models\AppSetting::get('bank_account_number', '8877-6655-44');
                    $bankHolder = \App\Models\AppSetting::get('bank_account_name', 'PT Radja Express Logistics');
                @endphp
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1">
                    <div class="font-bold text-slate-800 uppercase tracking-wider text-[10px]"><i class="fa-solid fa-building-columns mr-1"></i> Informasi Rekening Pembayaran Bank:</div>
                    <div class="text-[11px] text-slate-700 font-semibold">{{ $bankName }}: <span class="font-mono font-bold">{{ $bankNo }}</span></div>
                    <div class="text-[11px] text-slate-700 font-semibold">Atas Nama: <span class="font-bold">{{ $bankHolder }}</span></div>
                </div>
                @if($invoice->notes)
                    <div class="text-[11px] text-slate-500 italic"><span class="font-bold">Catatan / Instruksi:</span> {{ $invoice->notes }}</div>
                @endif
            </div>

            <div class="w-full sm:w-64 space-y-2 text-xs font-semibold text-slate-700">
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span>Subtotal Biaya Ongkir:</span>
                    <span class="font-bold text-slate-900">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($invoice->tax_amount > 0)
                    <div class="flex justify-between py-1 border-b border-slate-100 text-indigo-600">
                        <span>Pajak PPN (11%):</span>
                        <span class="font-bold">+ Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if($invoice->discount_amount > 0)
                    <div class="flex justify-between py-1 border-b border-slate-100 text-rose-600">
                        <span>Potongan Diskon:</span>
                        <span class="font-bold">- Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between py-2 border-t border-slate-900 text-sm font-black text-slate-900">
                    <span>Total Tagihan:</span>
                    <span class="text-emerald-600 text-base">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
