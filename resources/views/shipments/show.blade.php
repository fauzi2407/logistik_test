@extends('layouts.app')

@section('title', 'Detail Resi ' . $shipment->tracking_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Air Waybill (AWB)</div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight font-mono">{{ $shipment->tracking_number }}</h2>
            <div class="text-xs text-slate-500 mt-1">
                Layanan: <span class="font-bold text-indigo-600">{{ $shipment->service_type }}</span> • 
                Berat: <span class="font-bold text-slate-800">{{ $shipment->weight_kg }} kg</span>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            @if(Auth::user()->hasPermission('shipments.index', 'edit'))
                <a href="{{ route('shipments.edit', $shipment->id) }}" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition">
                    <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Data Resi
                </a>
            @endif
            <a href="{{ route('shipments.print-label', $shipment->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition">
                <i class="fa-solid fa-qrcode mr-1"></i> Cetak Label AWB (QR Code)
            </a>
            <button onclick="document.getElementById('updateStatusModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
                <i class="fa-solid fa-pen-to-square mr-1"></i> Update Status Transit
            </button>
        </div>
    </div>

    <!-- Status Badge Banner -->
    <div class="p-4 rounded-2xl bg-indigo-950 text-white shadow-md flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-truck-fast text-lg"></i>
            </div>
            <div>
                <div class="text-xs text-indigo-300 font-bold uppercase tracking-wider">Status Posisi Terakhir Paket</div>
                <div class="text-lg font-black uppercase text-white">{{ str_replace('_', ' ', $shipment->status) }}</div>
            </div>
        </div>
        <div class="text-right text-xs">
            <div class="text-slate-400">Total Ongkir:</div>
            <div class="text-lg font-black text-emerald-400">Rp {{ number_format($shipment->total_amount, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- ePOD Proof Section (If Delivered) -->
    @if($shipment->status === 'delivered')
        <div class="p-6 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-3">
            <div class="flex items-center space-x-2 text-sm font-extrabold uppercase text-emerald-700">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                <span>Bukti Penerimaan Paket (ePOD Verified)</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-semibold">
                <div>
                    <p><span class="text-slate-500">Nama Penerima:</span> <strong>{{ $shipment->pod_receiver_name }}</strong> ({{ $shipment->pod_receiver_relation }})</p>
                    <p><span class="text-slate-500">Waktu Diterima:</span> {{ $shipment->pod_delivered_at ? $shipment->pod_delivered_at->format('d M Y H:i WIB') : '-' }}</p>
                    @if($shipment->pod_latitude && $shipment->pod_longitude)
                        <p><span class="text-slate-500">Lokasi Real-Time GPS:</span> <strong class="text-emerald-700">{{ $shipment->pod_latitude }}, {{ $shipment->pod_longitude }}</strong>
                            <a href="https://maps.google.com/?q={{ $shipment->pod_latitude }},{{ $shipment->pod_longitude }}" target="_blank" class="ml-1 text-indigo-600 font-bold hover:underline">
                                <i class="fa-solid fa-map-location-dot"></i> Maps
                            </a>
                        </p>
                    @endif
                    <p><span class="text-slate-500">Catatan Penerima:</span> {{ $shipment->pod_notes ?? '-' }}</p>
                </div>
                <div>
                    @if($shipment->pod_signature)
                        <div class="text-slate-500 mb-1">Tanda Tangan Digital Penerima:</div>
                        <div class="bg-white p-2 rounded-xl border border-emerald-300 w-44 h-20 flex items-center justify-center">
                            <img src="{{ $shipment->pod_signature }}" alt="Tanda Tangan ePOD" class="max-h-full max-w-full">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Sender & Recipient Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
            <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider"><i class="fa-solid fa-upload text-indigo-600 mr-1.5"></i> Pengirim</h3>
            <div class="text-base font-bold text-slate-900">{{ $shipment->sender_name }}</div>
            <div class="text-xs text-slate-600"><i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $shipment->sender_phone }}</div>
            <div class="text-xs text-slate-500">{{ $shipment->sender_address }}, {{ $shipment->sender_city }}</div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
            <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider"><i class="fa-solid fa-location-dot text-emerald-600 mr-1.5"></i> Penerima</h3>
            <div class="text-base font-bold text-slate-900">{{ $shipment->recipient_name }}</div>
            <div class="text-xs text-slate-600"><i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $shipment->recipient_phone }}</div>
            <div class="text-xs text-slate-500">{{ $shipment->recipient_address }}, {{ $shipment->recipient_city }}</div>
        </div>
    </div>

    <!-- Tracking History Logs Timeline -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
        <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider mb-6">
            <i class="fa-solid fa-clock-rotate-left text-indigo-600 mr-2"></i> Riwayat Progress Transit paket
        </h3>
        <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
            @foreach($shipment->trackingLogs as $log)
                <div class="relative">
                    <div class="absolute -left-[23px] top-1 w-3.5 h-3.5 rounded-full bg-indigo-600 border-2 border-white shadow"></div>
                    <div class="text-xs text-indigo-600 font-mono font-bold">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }} WIB</div>
                    <div class="text-sm font-bold text-slate-900 mt-0.5">{{ $log->description }}</div>
                    <div class="text-xs text-slate-500 mt-0.5"><i class="fa-solid fa-map-pin mr-1 text-slate-400"></i> {{ $log->location }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal Update Status Transit -->
<div id="updateStatusModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm">Update Status Transit Resi</h3>
            <button onclick="document.getElementById('updateStatusModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('shipments.update-status', $shipment->id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Baru *</label>
                <select name="status" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="picked_up">Picked Up (Diterima / Dijemput)</option>
                    <option value="in_sorting_hub">In Sorting Hub (Tiba di Hub Transit)</option>
                    <option value="in_transit">In Transit (Perjalanan Linehaul)</option>
                    <option value="out_for_delivery">Out for Delivery (Dibawa Kurir Pengantar)</option>
                    <option value="delivered">Delivered (Terkirim)</option>
                    <option value="failed">Failed / Hold (Gagal Pengantaran)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Posisi Lokasi *</label>
                <input type="text" name="location" value="{{ $shipment->currentHub ? $shipment->currentHub->name : $shipment->recipient_city }}" required placeholder="Contoh: Hub Jakarta / Tol Cipularang / Bandung" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Catatan Keterangan *</label>
                <textarea name="description" rows="2" required placeholder="Instruksi / Keterangan update posisi..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">Paket telah diperbarui ke posisi lokasi transit terbaru.</textarea>
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('updateStatusModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md">Simpan Log Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
