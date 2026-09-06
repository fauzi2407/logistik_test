@extends('layouts.app')

@section('title', 'Detail Penugasan Kurir ' . $assignment->assignment_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Manifes Penugasan Kurir</div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight font-mono">{{ $assignment->assignment_number }}</h2>
            <div class="text-xs text-slate-500 mt-1">Tanggal Tugas: <span class="font-bold text-indigo-600">{{ $assignment->assignment_date->format('d M Y') }}</span></div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if($assignment->status === 'in_progress')
                <form action="{{ route('courier-assignments.complete', $assignment->id) }}" method="POST" onsubmit="return confirm('Selesaikan penugasan {{ $assignment->assignment_number }} dan perbarui status seluruh resi menjadi In-Hub (Sorting Hub)?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition flex items-center">
                        <i class="fa-solid fa-circle-check mr-1.5"></i> Selesaikan Manifes (Paket Masuk Hub)
                    </button>
                </form>
            @endif
            <a href="{{ route('courier-assignments.edit', $assignment->id) }}" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition">
                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Manifes
            </a>
            <a href="{{ route('courier-assignments.manifest', $assignment->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition">
                <i class="fa-solid fa-print mr-1"></i> Cetak Lembar Manifes
            </a>
            <a href="{{ route('courier-assignments.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Courier & Route Info Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kurir Petugas</div>
            <div class="text-base font-bold text-slate-900">{{ $assignment->courier->name }}</div>
            <div class="text-xs text-slate-500 font-mono">{{ $assignment->courier->courier_code }} • HP: {{ $assignment->courier->phone }}</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Armada Kendaraan</div>
            <div class="text-base font-bold text-slate-900">{{ $assignment->vehicle ? $assignment->vehicle->plate_number : 'Kendaraan Pribadi' }}</div>
            <div class="text-xs text-slate-500">{{ $assignment->vehicle ? $assignment->vehicle->vehicle_type : '-' }}</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tipe Tugas & Status</div>
            <div class="text-base font-bold text-indigo-600 uppercase">{{ $assignment->assignment_type }}</div>
            <div class="text-xs font-bold text-emerald-600 uppercase">{{ $assignment->status }}</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rute / Hub Operasional</div>
            @if($assignment->assignment_type === 'pickup')
                <div class="text-sm font-bold text-indigo-700">
                    <i class="fa-solid fa-warehouse mr-1 text-indigo-500"></i> {{ $assignment->destinationHub ? $assignment->destinationHub->name : 'Hub Kurir' }}
                </div>
                <div class="text-[11px] text-slate-400">Gudang Tujuan Setor</div>
            @elseif($assignment->assignment_type === 'transfer')
                <div class="text-xs font-bold text-cyan-800">
                    <i class="fa-solid fa-route mr-1 text-cyan-600"></i> {{ $assignment->originHub ? $assignment->originHub->name : 'Hub Asal' }} &rarr; {{ $assignment->destinationHub ? $assignment->destinationHub->name : 'Hub Tujuan' }}
                </div>
                <div class="text-[11px] text-slate-400">Rute Transfer Linehaul</div>
            @else
                <div class="text-sm font-bold text-slate-800">
                    <i class="fa-solid fa-house-chimney mr-1 text-slate-400"></i> Alamat Penerima
                </div>
                @if($assignment->originHub)
                    <div class="text-[11px] text-emerald-600 font-bold">
                        <i class="fa-solid fa-warehouse mr-1 text-emerald-500"></i> Hub: {{ $assignment->originHub->name }}
                    </div>
                @endif
                <div class="text-[11px] text-slate-400">Pengantaran Last-Mile</div>
            @endif
        </div>
    </div>

    <!-- Assignment Items List -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-boxes-stacked text-indigo-600 mr-2"></i> Daftar Resi Dalam Manifes Penugasan Ini ({{ count($assignment->items) }} Resi)
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-4">No. Resi</th>
                        <th class="p-4">Penerima & Alamat Tujuan</th>
                        <th class="p-4">No. HP</th>
                        <th class="p-4">Status Resi</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($assignment->items as $item)
                        <tr class="hover:bg-slate-50/80">
                            <td class="p-4 font-mono font-bold text-indigo-600">
                                <a href="{{ route('shipments.show', $item->shipment->id) }}">{{ $item->shipment->tracking_number }}</a>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-900">{{ $item->shipment->recipient_name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $item->shipment->recipient_address }}, {{ $item->shipment->recipient_city }}</div>
                            </td>
                            <td class="p-4 text-slate-700 font-bold">{{ $item->shipment->recipient_phone }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-indigo-100 text-indigo-800">
                                    {{ $item->shipment->status }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('epod.show', $item->shipment->tracking_number) }}" target="_blank" class="p-1.5 rounded-lg bg-emerald-50 text-emerald-700 font-bold text-[11px]">
                                    <i class="fa-solid fa-qrcode mr-1"></i> Buka ePOD
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
