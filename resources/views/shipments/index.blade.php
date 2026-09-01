@extends('layouts.app')

@section('title', 'Resi Pengiriman (AWB)')

@section('content')
<div class="space-y-6">
    <!-- Action Bar -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Resi Pengiriman (Air Waybill / AWB)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola seluruh resi pengiriman, tarif, status transit, dan label AWB.</p>
        </div>
        <div class="flex items-center space-x-3 w-full md:w-auto">
            <form action="{{ route('shipments.index') }}" method="GET" class="flex-1 md:w-64">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. Resi, pengirim, penerima..." 
                        class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </form>
            @if(Auth::user()->hasPermission('shipments.index', 'create'))
                <a href="{{ route('shipments.create') }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition flex-shrink-0">
                    <i class="fa-solid fa-plus mr-1.5"></i> Buat Resi Baru
                </a>
            @endif
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-4">Nomor Resi AWB</th>
                        <th class="p-4">Pengirim & Asal</th>
                        <th class="p-4">Penerima & Tujuan</th>
                        <th class="p-4">Layanan / Berat</th>
                        <th class="p-4">Total Biaya</th>
                        <th class="p-4">Kurir</th>
                        <th class="p-4">Status Transit</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($shipments as $s)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-bold text-indigo-600">
                                <a href="{{ route('shipments.show', $s->id) }}">{{ $s->tracking_number }}</a>
                                @if($s->deliveryOrder)
                                    <div class="text-[10px] text-slate-400 font-normal">DO: {{ $s->deliveryOrder->do_number }}</div>
                                @endif
                            </td>
                            <td class="p-4 text-slate-900">
                                <div class="font-bold">{{ $s->sender_name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $s->sender_city }}</div>
                            </td>
                            <td class="p-4 text-slate-900">
                                <div class="font-bold">{{ $s->recipient_name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $s->recipient_city }}</div>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded bg-slate-100 font-bold text-slate-700">{{ $s->service_type }}</span>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $s->weight_kg }} kg</div>
                            </td>
                            <td class="p-4 font-bold text-slate-900">
                                Rp {{ number_format($s->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="p-4 font-bold text-slate-700">
                                {{ $s->courier ? $s->courier->name : '-' }}
                            </td>
                            <td class="p-4">
                                @php
                                    $stColor = match($s->status) {
                                        'delivered' => 'bg-emerald-100 text-emerald-800',
                                        'out_for_delivery' => 'bg-amber-100 text-amber-800',
                                        'in_transit' => 'bg-sky-100 text-sky-800',
                                        default => 'bg-slate-100 text-slate-700'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $stColor }}">
                                    {{ str_replace('_', ' ', $s->status) }}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-1">
                                <a href="{{ route('shipments.show', $s->id) }}" class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold" title="Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @if(Auth::user()->hasPermission('shipments.index', 'edit'))
                                    <a href="{{ route('shipments.edit', $s->id) }}" class="p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 font-bold" title="Edit Resi">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                @endif
                                <a href="{{ route('shipments.print-label', $s->id) }}" target="_blank" class="p-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold" title="Print AWB Label QR Code">
                                    <i class="fa-solid fa-qrcode"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">Belum ada data resi pengiriman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $shipments->links() }}
        </div>
    </div>
</div>
@endsection
