@extends('layouts.app')

@section('title', 'Edit Resi ' . $shipment->tracking_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Edit Resi Pengiriman (AWB)</h2>
            <p class="text-xs text-indigo-600 font-mono font-semibold">{{ $shipment->tracking_number }}</p>
        </div>
        <a href="{{ route('shipments.show', $shipment->id) }}" class="text-xs font-bold text-slate-500 hover:text-slate-700">
            <i class="fa-solid fa-arrow-left mr-1"></i> Batal
        </a>
    </div>

    <form action="{{ route('shipments.update', $shipment->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Hubs & Courier Selection -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2">1. Transit Hub, Status & Kurir Assigned</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Hub Asal *</label>
                    <select name="origin_hub_id" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        @foreach($hubs as $h)
                            <option value="{{ $h->id }}" {{ $shipment->origin_hub_id == $h->id ? 'selected' : '' }}>{{ $h->name }} ({{ $h->city }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Hub Tujuan *</label>
                    <select name="destination_hub_id" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        @foreach($hubs as $h)
                            <option value="{{ $h->id }}" {{ $shipment->destination_hub_id == $h->id ? 'selected' : '' }}>{{ $h->name }} ({{ $h->city }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Kurir</label>
                    <select name="courier_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        <option value="">-- Ditugaskan Nanti --</option>
                        @foreach($couriers as $cr)
                            <option value="{{ $cr->id }}" {{ $shipment->courier_id == $cr->id ? 'selected' : '' }}>{{ $cr->name }} ({{ $cr->courier_code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Resi *</label>
                    <select name="status" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        <option value="draft" {{ $shipment->status == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="picked_up" {{ $shipment->status == 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                        <option value="in_sorting_hub" {{ $shipment->status == 'in_sorting_hub' ? 'selected' : '' }}>In Sorting Hub</option>
                        <option value="in_transit" {{ $shipment->status == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                        <option value="out_for_delivery" {{ $shipment->status == 'out_for_delivery' ? 'selected' : '' }}>Out For Delivery</option>
                        <option value="delivered" {{ $shipment->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="failed" {{ $shipment->status == 'failed' ? 'selected' : '' }}>Failed / Hold</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Sender & Recipient Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Sender -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2">2. Data Pengirim</h3>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Pengirim *</label>
                    <input type="text" name="sender_name" value="{{ old('sender_name', $shipment->sender_name) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. HP Pengirim *</label>
                    <input type="text" name="sender_phone" value="{{ old('sender_phone', $shipment->sender_phone) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kota Asal *</label>
                    <input type="text" name="sender_city" value="{{ old('sender_city', $shipment->sender_city) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Lengkap Pengirim *</label>
                    <textarea name="sender_address" rows="2" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">{{ old('sender_address', $shipment->sender_address) }}</textarea>
                </div>
            </div>

            <!-- Recipient -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2">3. Data Penerima</h3>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Penerima *</label>
                    <input type="text" name="recipient_name" value="{{ old('recipient_name', $shipment->recipient_name) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. HP Penerima *</label>
                    <input type="text" name="recipient_phone" value="{{ old('recipient_phone', $shipment->recipient_phone) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kota Tujuan *</label>
                    <input type="text" name="recipient_city" value="{{ old('recipient_city', $shipment->recipient_city) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Lengkap Penerima *</label>
                    <textarea name="recipient_address" rows="2" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">{{ old('recipient_address', $shipment->recipient_address) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Package & Fee Info -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2">4. Informasi Paket & Tarif Pembayaran</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Layanan *</label>
                    <select name="service_type" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        <option value="Regular" {{ $shipment->service_type == 'Regular' ? 'selected' : '' }}>Regular (2-3 Hari)</option>
                        <option value="Express" {{ $shipment->service_type == 'Express' ? 'selected' : '' }}>Express (1 Hari Next Day)</option>
                        <option value="SameDay" {{ $shipment->service_type == 'SameDay' ? 'selected' : '' }}>SameDay (Tiba Hari Ini)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Berat Total (kg) *</label>
                    <input type="number" step="0.1" name="weight_kg" value="{{ old('weight_kg', $shipment->weight_kg) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Dimensi PxLxT (cm)</label>
                    <input type="text" name="dimensions" value="{{ old('dimensions', $shipment->dimensions) }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nilai Barang Pertanggungan (Rp)</label>
                    <input type="number" name="declared_value" value="{{ old('declared_value', $shipment->declared_value) }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Metode Pembayaran *</label>
                    <select name="payment_method" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        <option value="Cash" {{ $shipment->payment_method == 'Cash' ? 'selected' : '' }}>Cash (Tunai)</option>
                        <option value="Transfer" {{ $shipment->payment_method == 'Transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="COD" {{ $shipment->payment_method == 'COD' ? 'selected' : '' }}>COD (Bayar Di Tempat)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Pembayaran *</label>
                    <select name="payment_status" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        <option value="Paid" {{ $shipment->payment_status == 'Paid' ? 'selected' : '' }}>LUNAS (Paid)</option>
                        <option value="Pending" {{ $shipment->payment_status == 'Pending' ? 'selected' : '' }}>PENDING</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('shipments.show', $shipment->id) }}" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg shadow-indigo-600/30">
                Simpan Perubahan Resi
            </button>
        </div>
    </form>
</div>
@endsection
