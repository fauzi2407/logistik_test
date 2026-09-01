@extends('layouts.app')

@section('title', 'Detail Purchase Order ' . $purchase->purchase_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Bar -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Purchase Order Ref</div>
            <h2 class="text-2xl font-black text-slate-900 font-mono tracking-tight">{{ $purchase->purchase_number }}</h2>
            <div class="text-xs text-slate-500 mt-1">
                Vendor: <span class="font-bold text-slate-900">{{ $purchase->vendor->name }}</span> ({{ $purchase->vendor->vendor_code }}) • 
                Tanggal PO: <span class="font-bold text-indigo-600">{{ $purchase->purchase_date->format('d M Y') }}</span>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('purchases.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold text-xs transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
            </a>
            <button onclick="document.getElementById('updateStatusModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold text-xs transition">
                <i class="fa-solid fa-pen-to-square mr-1"></i> Update Status
            </button>
            <a href="{{ route('purchases.print', $purchase->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition">
                <i class="fa-solid fa-print mr-1"></i> Cetak Surat PO
            </a>
        </div>
    </div>

    <!-- Status Banners -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs text-slate-400 font-extrabold uppercase">Status Penerimaan Barang</div>
                <div class="mt-1">
                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase border {{ $purchase->status_badge }}">
                        {{ $purchase->status }}
                    </span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs text-slate-400 font-extrabold uppercase">Status Pembayaran PO</div>
                <div class="mt-1">
                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase border {{ $purchase->payment_badge }}">
                        {{ $purchase->payment_status }}
                    </span>
                </div>
            </div>
            <div class="text-right">
                <div class="text-[10px] text-slate-400 font-bold uppercase">Metode Pembayaran</div>
                <div class="text-xs font-black text-slate-800">{{ $purchase->payment_method ?: 'Transfer Bank' }}</div>
            </div>
        </div>
    </div>

    <!-- Vendor Profile Card -->
    <div class="bg-slate-900 text-white p-5 rounded-2xl shadow-xl grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-semibold">
        <div>
            <div class="text-slate-400 font-bold uppercase">Identitas Vendor Supplier:</div>
            <div class="text-base font-black text-white mt-0.5">{{ $purchase->vendor->name }}</div>
            <div class="text-slate-300 mt-0.5">PIC: {{ $purchase->vendor->contact_person ?: '-' }}</div>
            <div class="text-slate-300"><i class="fa-solid fa-phone text-indigo-400 mr-1"></i> {{ $purchase->vendor->phone ?: '-' }}</div>
        </div>

        <div>
            <div class="text-slate-400 font-bold uppercase">Info Rekening Pembayaran Vendor:</div>
            <div class="text-sm font-bold text-white mt-0.5">{{ $purchase->vendor->bank_name ?: 'Bank Vendor' }}</div>
            <div class="font-mono text-indigo-300 text-base font-black mt-0.5">{{ $purchase->vendor->bank_account_number ?: '-' }}</div>
            <div class="text-slate-400 text-[11px] mt-0.5">Alamat: {{ $purchase->vendor->address ?: '-' }}</div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden space-y-4">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-list-check text-indigo-600 mr-2"></i> Rincian {{ count($purchase->items) }} Item Barang Dibereli
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/70 text-slate-600 uppercase tracking-wider font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-3 w-10 text-center">#</th>
                        <th class="p-3">Nama Barang / Deskripsi</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3 text-center">Qty / Satuan</th>
                        <th class="p-3 text-right">Harga Satuan (Rp)</th>
                        <th class="p-3 text-right">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($purchase->items as $it)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3 text-center text-slate-400 font-mono">{{ $loop->iteration }}</td>
                            <td class="p-3 font-bold text-slate-900">
                                {{ $it->item_name }}
                                @if($it->notes)
                                    <div class="text-[11px] text-slate-400 font-normal">{{ $it->notes }}</div>
                                @endif
                            </td>
                            <td class="p-3 text-slate-600">
                                <span class="px-2 py-0.5 rounded bg-slate-100 font-semibold text-[11px] text-slate-700">{{ $it->category }}</span>
                            </td>
                            <td class="p-3 text-center font-bold text-slate-800">
                                {{ $it->quantity }} {{ $it->unit }}
                            </td>
                            <td class="p-3 text-right font-semibold text-slate-800">
                                Rp {{ number_format($it->unit_price, 0, ',', '.') }}
                            </td>
                            <td class="p-3 text-right font-black text-slate-900">
                                Rp {{ number_format($it->total_price, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 font-bold border-t-2 border-slate-300 text-xs">
                    <tr>
                        <td colspan="5" class="p-2.5 text-right text-slate-600">Subtotal:</td>
                        <td class="p-2.5 text-right font-mono text-slate-900 font-black">Rp {{ number_format($purchase->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($purchase->discount_amount > 0)
                        <tr>
                            <td colspan="5" class="p-2.5 text-right text-slate-600">Diskon / Potongan:</td>
                            <td class="p-2.5 text-right font-mono text-rose-600 font-black">- Rp {{ number_format($purchase->discount_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if($purchase->tax_amount > 0)
                        <tr>
                            <td colspan="5" class="p-2.5 text-right text-slate-600">Pajak PPN:</td>
                            <td class="p-2.5 text-right font-mono text-indigo-600 font-black">+ Rp {{ number_format($purchase->tax_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr class="bg-emerald-50 text-sm font-black text-emerald-900">
                        <td colspan="5" class="p-3 text-right uppercase">TOTAL GRAND PRICE PO:</td>
                        <td class="p-3 text-right font-mono text-emerald-700">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div id="updateStatusModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Update Status Purchase Order</h3>
            <button onclick="document.getElementById('updateStatusModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('purchases.update-status', $purchase->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
            @csrf
            <div>
                <label class="block text-slate-700 mb-1">Status Penerimaan Barang *</label>
                <select name="status" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
                    <option value="ordered" {{ $purchase->status == 'ordered' ? 'selected' : '' }}>Ordered (Dalam Pemesanan)</option>
                    <option value="received" {{ $purchase->status == 'received' ? 'selected' : '' }}>Received (Sudah Diterima Di Gudang)</option>
                    <option value="draft" {{ $purchase->status == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="cancelled" {{ $purchase->status == 'cancelled' ? 'selected' : '' }}>Cancelled (Dibatalkan)</option>
                </select>
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Status Pembayaran PO *</label>
                <select name="payment_status" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
                    <option value="unpaid" {{ $purchase->payment_status == 'unpaid' ? 'selected' : '' }}>Unpaid (Belum Dibayar)</option>
                    <option value="partial" {{ $purchase->payment_status == 'partial' ? 'selected' : '' }}>Partial (Sebagian / DP)</option>
                    <option value="paid" {{ $purchase->payment_status == 'paid' ? 'selected' : '' }}>Paid (Lunas)</option>
                </select>
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Metode Pembayaran</label>
                <select name="payment_method" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
                    <option value="Transfer Bank" {{ $purchase->payment_method == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                    <option value="Cash / Tunai" {{ $purchase->payment_method == 'Cash / Tunai' ? 'selected' : '' }}>Cash / Tunai</option>
                    <option value="Giro / Cek" {{ $purchase->payment_method == 'Giro / Cek' ? 'selected' : '' }}>Giro / Cek</option>
                </select>
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Catatan Tambahan</label>
                <input type="text" name="notes" value="{{ old('notes', $purchase->notes) }}" placeholder="Catatan penerimaan barang..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('updateStatusModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
