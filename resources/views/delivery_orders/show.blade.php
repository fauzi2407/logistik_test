@extends('layouts.app')

@section('title', 'Detail DO & Tagihan ' . $deliveryOrder->do_number)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-indigo-100 text-indigo-800">
                    Delivery Order (DO) Multi-Tujuan
                </span>
                {!! $deliveryOrder->status_badge !!}
            </div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight font-mono mt-1">{{ $deliveryOrder->do_number }}</h2>
            <div class="text-xs text-slate-500 mt-0.5">Tanggal Order: <span class="font-bold text-slate-800">{{ $deliveryOrder->order_date->format('d M Y') }}</span></div>
        </div>
        <div class="flex items-center space-x-2">
            @php
                $hasResi = $deliveryOrder->items->contains(fn($item) => !empty($item->tracking_number) || $item->shipment);
                $needsResi = $deliveryOrder->items->contains(fn($item) => empty($item->tracking_number) || !$item->shipment);
            @endphp
            @if(!$hasResi)
                <form action="{{ route('delivery-orders.destroy', $deliveryOrder->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Delivery Order {{ $deliveryOrder->do_number }} ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition shadow-md shadow-rose-600/30 flex items-center">
                        <i class="fa-solid fa-trash mr-1.5"></i> Hapus DO
                    </button>
                </form>
            @endif
            @if($needsResi && Auth::user()->hasPermission('shipments.index', 'create'))
                <form action="{{ route('delivery-orders.generate-shipments', $deliveryOrder->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition flex items-center">
                        <i class="fa-solid fa-bolt mr-1.5"></i> Generate Resi Otomatis
                    </button>
                </form>
            @endif
            @if(Auth::user()->role !== 'customer')
                @if($deliveryOrder->isPickedUp())
                    <button disabled class="px-4 py-2 rounded-xl bg-slate-100 text-slate-400 font-bold text-xs cursor-not-allowed border border-slate-200" title="Paket sudah diambil oleh kurir / sedang berjalan">
                        <i class="fa-solid fa-lock mr-1.5 text-slate-400"></i> Terkunci (Sudah Diambil Kurir)
                    </button>
                @else
                    <a href="{{ route('delivery-orders.edit', $deliveryOrder->id) }}" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition">
                        <i class="fa-solid fa-pen-to-square mr-1"></i> Edit DO
                    </a>
                @endif
            @endif
            <a href="{{ route('delivery-orders.print', $deliveryOrder->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition">
                <i class="fa-solid fa-print mr-1"></i> Cetak Surat Jalan
            </a>
        </div>
    </div>

    <!-- Rincian Tagihan Pengiriman Customer Box (Invoice Billing Summary) -->
    @php
        $totalShippingFee = 0;
        foreach($deliveryOrder->items as $item) {
            if ($item->shipment) {
                $totalShippingFee += $item->shipment->total_amount;
            } else {
                $t = \App\Models\Tariff::findTariff(
                    $deliveryOrder->sender_city,
                    $item->recipient_city,
                    'Express',
                    $deliveryOrder->pickup_district ?? null,
                    $deliveryOrder->pickup_subdistrict ?? null,
                    $item->recipient_district ?? null,
                    $item->recipient_subdistrict ?? null
                );
                $rate = $t ? $t->price_per_kg : 20000;
                $totalShippingFee += $rate * max(1, (int) ceil((float) $item->weight_kg));
            }
        }
    @endphp

    <div class="bg-gradient-to-br from-indigo-950 via-slate-900 to-slate-950 text-white p-6 rounded-2xl shadow-xl space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-slate-800 pb-4 gap-3">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-400 flex items-center">
                    <i class="fa-solid fa-file-invoice-dollar mr-1.5 text-sm"></i> Ringkasan Tagihan Pengiriman DO
                </span>
                <h3 class="text-lg font-bold text-white mt-0.5">Tagihan Ongkos Kirim Batch {{ $deliveryOrder->do_number }}</h3>
            </div>
            <div class="text-left md:text-right">
                <span class="text-xs text-slate-400 block">Total Nominal Tagihan:</span>
                <span class="text-2xl font-black text-emerald-400">Rp {{ number_format($totalShippingFee, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div class="bg-slate-800/60 p-3.5 rounded-xl border border-slate-700/60">
                <span class="text-slate-400 block">Total Lokasi Tujuan:</span>
                <span class="font-bold text-white text-sm">{{ count($deliveryOrder->items) }} Penerima Paket</span>
            </div>

            <div class="bg-slate-800/60 p-3.5 rounded-xl border border-slate-700/60">
                <span class="text-slate-400 block">Metode Pembayaran:</span>
                <span class="font-bold text-white text-sm">Transfer Bank / Billing Monthly</span>
            </div>

            <div class="bg-slate-800/60 p-3.5 rounded-xl border border-slate-700/60">
                <span class="text-slate-400 block">Status Pembayaran Tagihan:</span>
                @if($deliveryOrder->invoice && $deliveryOrder->invoice->status === 'paid')
                    <span class="inline-flex items-center mt-0.5 px-2.5 py-0.5 rounded text-[11px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        <i class="fa-solid fa-circle-check mr-1.5"></i> LUNAS (PAID) — DO KOMPLIT
                    </span>
                    <div class="mt-1">
                        <a href="{{ route('invoices.show', $deliveryOrder->invoice->id) }}" class="text-[11px] font-bold text-indigo-400 hover:text-indigo-300 underline">
                            <i class="fa-solid fa-file-invoice mr-1"></i> {{ $deliveryOrder->invoice->invoice_number }}
                        </a>
                    </div>
                @elseif($deliveryOrder->invoice)
                    <span class="inline-flex items-center mt-0.5 px-2.5 py-0.5 rounded text-[11px] font-extrabold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        <i class="fa-solid fa-clock mr-1.5"></i> BELUM LUNAS (PENDING)
                    </span>
                    <div class="mt-1">
                        <a href="{{ route('invoices.show', $deliveryOrder->invoice->id) }}" class="text-[11px] font-bold text-amber-300 hover:underline">
                            <i class="fa-solid fa-file-invoice mr-1"></i> Lihat Invoice ({{ $deliveryOrder->invoice->invoice_number }})
                        </a>
                    </div>
                @else
                    <span class="inline-flex items-center mt-0.5 px-2.5 py-0.5 rounded text-[11px] font-extrabold bg-slate-500/20 text-slate-300 border border-slate-500/30">
                        <i class="fa-solid fa-circle-minus mr-1.5"></i> BELUM DITAGIHKAN
                    </span>
                    @if(Auth::user()->role !== 'customer' && !$deliveryOrder->isCompleted())
                        <div class="mt-1.5">
                            <a href="{{ route('invoices.create', ['do_id' => $deliveryOrder->id, 'customer_id' => $deliveryOrder->customer_id]) }}" class="inline-flex items-center px-2 py-1 rounded bg-indigo-600 hover:bg-indigo-500 text-[10px] font-bold text-white shadow transition">
                                <i class="fa-solid fa-file-invoice-dollar mr-1"></i> Terbitkan Invoice
                            </a>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Sender Info Box -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
            <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider"><i class="fa-solid fa-building text-indigo-600 mr-1.5"></i> Customer Pengirim</h3>
            <div class="text-lg font-bold text-slate-900 mt-1">{{ $deliveryOrder->sender_name }}</div>
            <div class="text-xs text-slate-500"><i class="fa-solid fa-phone mr-1"></i> {{ $deliveryOrder->sender_phone }} • {{ $deliveryOrder->sender_address }}, {{ $deliveryOrder->sender_city }}</div>
        </div>
        <div class="text-right">
            <span class="px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 font-extrabold text-xs">
                Total {{ count($deliveryOrder->items) }} Penerima / Lokasi
            </span>
        </div>
    </div>

    <!-- Multi-Destination Recipients & AWB Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-people-carry-box text-emerald-600 mr-2"></i> Daftar Penerima & Rincian Ongkir Per Paket
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-4">No.</th>
                        <th class="p-4">Penerima & Kontak</th>
                        <th class="p-4">Kota & Alamat Tujuan</th>
                        <th class="p-4">No. Ref / PO / Kontrak</th>
                        <th class="p-4">Jenis Barang</th>
                        <th class="p-4">Tarif Ongkir</th>
                        <th class="p-4">Nomor Resi AWB</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($deliveryOrder->items as $idx => $item)
                        @php
                            if ($item->shipment) {
                                $itemFee = $item->shipment->total_amount;
                            } else {
                                $t = \App\Models\Tariff::findTariff(
                                    $deliveryOrder->sender_city,
                                    $item->recipient_city,
                                    'Express',
                                    $deliveryOrder->pickup_district ?? null,
                                    $deliveryOrder->pickup_subdistrict ?? null,
                                    $item->recipient_district ?? null,
                                    $item->recipient_subdistrict ?? null
                                );
                                $rate = $t ? $t->price_per_kg : 20000;
                                $itemFee = $rate * max(1, (int) ceil((float) $item->weight_kg));
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-bold text-slate-400">{{ $idx + 1 }}</td>
                            <td class="p-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $item->recipient_name }}</div>
                                <div class="text-[11px] text-slate-500"><i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $item->recipient_phone }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-900">{{ $item->recipient_city }}</div>
                                <div class="text-[11px] text-slate-500 truncate max-w-xs">{{ $item->recipient_address }}</div>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded bg-slate-100 font-mono font-bold text-slate-700">
                                    {{ $item->account_ref ?? '-' }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-800">{{ $item->item_name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $item->weight_kg }} kg</div>
                            </td>
                            <td class="p-4 font-black text-emerald-600">
                                Rp {{ number_format($itemFee, 0, ',', '.') }}
                            </td>
                            <td class="p-4">
                                @if($item->shipment)
                                    <a href="{{ route('shipments.show', $item->shipment->id) }}" class="font-mono font-bold text-indigo-600 hover:text-indigo-800">
                                        {{ $item->shipment->tracking_number }}
                                    </a>
                                    <div class="text-[10px]">
                                        <span class="font-bold uppercase text-emerald-600">{{ $item->shipment->status }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 font-mono">-</span>
                                @endif
                            </td>
                            <td class="p-4 text-right space-x-1">
                                @if($item->shipment)
                                    <a href="{{ route('shipments.print-label', $item->shipment->id) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px]" title="Cetak Label AWB QR Code">
                                        <i class="fa-solid fa-qrcode mr-1"></i> Print Label
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
