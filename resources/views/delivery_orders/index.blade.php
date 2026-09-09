@extends('layouts.app')

@section('title', 'Delivery Order (DO Customer)')

@section('content')
<div class="space-y-6">
    <!-- Action Bar -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Delivery Order / Surat Jalan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola dokumen Surat Jalan dan daftar pesanan pengiriman dari Customer.</p>
        </div>
        <div class="flex items-center space-x-3 w-full md:w-auto">
            <form action="{{ route('delivery-orders.index') }}" method="GET" class="flex items-center space-x-2 flex-1 md:flex-initial">
                <div class="relative flex-1 md:w-56">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. DO, penerima, kota..." 
                        class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
                <select name="status" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ $status == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="processing" {{ $status == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ $status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ $status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="completed" {{ in_array($status, ['completed', 'komplit']) ? 'selected' : '' }}>Komplit (Lunas)</option>
                    <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </form>
            <a href="{{ route('delivery-orders.create') }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition flex-shrink-0">
                <i class="fa-solid fa-plus mr-1.5"></i> Buat DO Baru
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-4">Nomor DO</th>
                        <th class="p-4">Customer Pengirim</th>
                        <th class="p-4">Hub Origin Transit</th>
                        <th class="p-4">Tanggal Order</th>
                        <th class="p-4">Penerima & Tujuan</th>
                        <th class="p-4">Item Barang</th>
                        <th class="p-4">Status DO</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($deliveryOrders as $do)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-bold text-indigo-600">
                                <a href="{{ route('delivery-orders.show', $do->id) }}">{{ $do->do_number }}</a>
                            </td>
                            <td class="p-4 text-slate-900">
                                <div class="font-bold">{{ $do->customer ? $do->customer->name : $do->sender_name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $do->sender_city }}</div>
                            </td>
                            <td class="p-4 text-slate-900 font-bold">
                                @php
                                    $hub = $do->origin_hub;
                                @endphp
                                <div class="flex items-center space-x-1 text-indigo-700">
                                    <i class="fa-solid fa-warehouse text-slate-400 text-[11px] mr-1"></i>
                                    <span>{{ $hub ? 'Hub ' . $hub->name : 'Hub ' . $do->sender_city }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 font-normal">({{ $hub ? $hub->city : $do->sender_city }})</div>
                            </td>
                            <td class="p-4 text-slate-500">
                                <div class="font-bold text-slate-800">{{ $do->order_date->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $do->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="p-4 text-slate-900">
                                <div class="font-bold">{{ $do->recipient_name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $do->recipient_city }}</div>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-bold">
                                    {{ count($do->items) }} Jenis Item
                                </span>
                            </td>
                            <td class="p-4">
                                {!! $do->status_badge !!}
                            </td>
                            <td class="p-4 text-right space-x-1">
                                <a href="{{ route('delivery-orders.show', $do->id) }}" class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold text-xs" title="Lihat Detail & Tagihan">
                                    <i class="fa-solid fa-eye mr-1"></i> Detail & Tagihan
                                </a>
                                @if(Auth::user()->role !== 'customer')
                                    @if($do->isPickedUp())
                                        <button disabled class="p-1.5 rounded-lg bg-slate-100 text-slate-400 cursor-not-allowed font-bold" title="Paket sudah diambil kurir / sedang berjalan (Tidak dapat di-edit)">
                                            <i class="fa-solid fa-lock text-slate-400"></i>
                                        </button>
                                    @else
                                        <a href="{{ route('delivery-orders.edit', $do->id) }}" class="p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 font-bold" title="Edit DO">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    @endif
                                @endif
                                <a href="{{ route('delivery-orders.print', $do->id) }}" target="_blank" class="p-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold" title="Cetak Surat Jalan">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                @php
                                    $hasResi = $do->items->contains(fn($item) => !empty($item->tracking_number) || $item->shipment);
                                    $needsResi = $do->items->contains(fn($item) => empty($item->tracking_number) || !$item->shipment);
                                @endphp
                                @if(!$hasResi)
                                    <form action="{{ route('delivery-orders.destroy', $do->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Delivery Order {{ $do->do_number }} ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold text-xs" title="Hapus DO (Belum Terbit Resi)">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($needsResi && Auth::user()->hasPermission('shipments.index', 'create'))
                                    <form action="{{ route('delivery-orders.generate-shipments', $do->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] transition inline-flex items-center" title="Generate Resi AWB Otomatis">
                                            <i class="fa-solid fa-bolt mr-1"></i> + Generate Resi
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">Belum ada dokumen Delivery Order.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $deliveryOrders->links() }}
        </div>
    </div>
</div>
@endsection
