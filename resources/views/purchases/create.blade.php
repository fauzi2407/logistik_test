@extends('layouts.app')

@section('title', 'Buat Purchase Order Pembelian Barang')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Form Purchase Order (PO) Pembelian</h2>
            <p class="text-xs text-slate-500 mt-0.5">Input transaksi pengadaan barang, perlengkapan packing, & sparepart armada.</p>
        </div>
        <a href="{{ route('purchases.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold text-xs transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('purchases.store') }}" method="POST" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs font-semibold">
            <div>
                <label class="block text-slate-700 font-bold uppercase tracking-wider mb-1">Tanggal Transaksi PO *</label>
                <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-bold text-slate-800">
            </div>

            <div>
                <label class="block text-slate-700 font-bold uppercase tracking-wider mb-1">Pilih Vendor Supplier *</label>
                <select name="vendor_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-bold text-slate-800">
                    <option value="">-- Pilih Supplier --</option>
                    @foreach($vendors as $v)
                        <option value="{{ $v->id }}">{{ $v->name }} ({{ $v->vendor_code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-slate-700 font-bold uppercase tracking-wider mb-1">Status Pembayaran awal *</label>
                <select name="payment_status" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-bold text-slate-800">
                    <option value="unpaid">Unpaid (Belum Dibayar)</option>
                    <option value="partial">Partial (DP / Dibaragian)</option>
                    <option value="paid">Paid (Lunas)</option>
                </select>
            </div>
        </div>

        <!-- Items Table -->
        <div class="space-y-3 pt-2">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                    <i class="fa-solid fa-cart-shopping text-indigo-600 mr-1.5"></i> Daftar Barang / Perlengkapan Dibereli
                </h3>
                <button type="button" onclick="addPurchaseRow()" class="px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold text-xs transition">
                    <i class="fa-solid fa-plus mr-1"></i> Tambah Baris Barang
                </button>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-left text-xs" id="purchaseTable">
                    <thead class="bg-slate-100/80 text-slate-600 uppercase font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-3">Nama Barang / Deskripsi *</th>
                            <th class="p-3 w-40">Kategori</th>
                            <th class="p-3 w-24 text-center">Qty *</th>
                            <th class="p-3 w-28">Satuan *</th>
                            <th class="p-3 w-36 text-right">Harga Satuan (Rp) *</th>
                            <th class="p-3 w-36 text-right">Total (Rp)</th>
                            <th class="p-3 w-10 text-center">Hapus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium" id="purchaseRows">
                        <!-- Default Row 1 -->
                        <tr>
                            <td class="p-2">
                                <input type="text" name="items[0][item_name]" required placeholder="Misal: Plastik Packing Resi Logistik" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-bold text-slate-800 text-xs">
                            </td>
                            <td class="p-2">
                                <select name="items[0][category]" class="w-full px-2 py-2 rounded-xl border border-slate-300 text-xs">
                                    <option value="Perlengkapan Packing">Perlengkapan Packing</option>
                                    <option value="Sparepart Fleet">Sparepart Fleet/Armada</option>
                                    <option value="ATK & Inventaris Gudang">ATK & Inventaris Gudang</option>
                                    <option value="Jasa & Operasional">Jasa & Operasional</option>
                                </select>
                            </td>
                            <td class="p-2 text-center">
                                <input type="number" min="1" name="items[0][quantity]" value="1" oninput="calculatePoTotals()" class="qty-input w-20 px-2 py-2 rounded-xl border border-slate-300 text-center font-bold text-slate-900 text-xs">
                            </td>
                            <td class="p-2">
                                <input type="text" name="items[0][unit]" value="Roll" placeholder="Roll/Pcs/Box" class="w-full px-2 py-2 rounded-xl border border-slate-300 text-slate-800 text-xs">
                            </td>
                            <td class="p-2">
                                <input type="number" step="0.01" min="0" name="items[0][unit_price]" value="150000" oninput="calculatePoTotals()" placeholder="0" class="price-input w-full px-2.5 py-2 rounded-xl border border-slate-300 text-right font-mono font-bold text-slate-900 text-xs">
                            </td>
                            <td class="p-2 text-right font-mono font-bold text-slate-900 line-total text-xs">
                                Rp 150.000
                            </td>
                            <td class="p-2 text-center">
                                <button type="button" onclick="removePoRow(this)" class="text-rose-500 hover:text-rose-700 text-sm font-bold">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Financial Calculation Summary -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Metode Pembayaran</label>
                <select name="payment_method" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                    <option value="Transfer Bank">Transfer Bank</option>
                    <option value="Cash / Tunai">Cash / Tunai</option>
                    <option value="Giro / Cek">Giro / Cek</option>
                </select>

                <label class="block font-bold text-slate-700 uppercase mt-3 mb-1">Catatan PO / Instruksi Pengiriman</label>
                <input type="text" name="notes" placeholder="Catatan syarat garansi atau kirim ke Hub Gudang..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
            </div>

            <div class="space-y-2 text-right">
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-bold">Subtotal Pembelian:</span>
                    <span class="font-mono font-bold text-slate-900 text-sm" id="poSubtotalText">Rp 150.000</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-bold">Diskon / Potongan (Rp):</span>
                    <input type="number" min="0" name="discount_amount" value="0" oninput="calculatePoTotals()" id="poDiscountInput" class="w-36 px-2 py-1 rounded-xl border border-slate-300 text-right font-mono font-bold text-rose-600">
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-bold">Pajak PPN (Rp):</span>
                    <input type="number" min="0" name="tax_amount" value="0" oninput="calculatePoTotals()" id="poTaxInput" class="w-36 px-2 py-1 rounded-xl border border-slate-300 text-right font-mono font-bold text-indigo-600">
                </div>
                <div class="pt-2 border-t border-slate-300 flex justify-between items-center text-sm">
                    <span class="font-black text-slate-900">GRAND TOTAL PO:</span>
                    <span class="font-mono font-black text-emerald-600 text-lg" id="poGrandTotalText">Rp 150.000</span>
                </div>
            </div>
        </div>

        <div class="pt-4 flex justify-end space-x-2 border-t border-slate-100">
            <a href="{{ route('purchases.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg">
                <i class="fa-solid fa-floppy-disk mr-1.5"></i> Terbitkan Purchase Order (PO)
            </button>
        </div>
    </form>
</div>

<script>
    let poRowIndex = 1;

    function addPurchaseRow() {
        const tbody = document.getElementById('purchaseRows');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="p-2">
                <input type="text" name="items[${poRowIndex}][item_name]" required placeholder="Nama barang / sparepart..." class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-bold text-slate-800 text-xs">
            </td>
            <td class="p-2">
                <select name="items[${poRowIndex}][category]" class="w-full px-2 py-2 rounded-xl border border-slate-300 text-xs">
                    <option value="Perlengkapan Packing">Perlengkapan Packing</option>
                    <option value="Sparepart Fleet">Sparepart Fleet/Armada</option>
                    <option value="ATK & Inventaris Gudang">ATK & Inventaris Gudang</option>
                    <option value="Jasa & Operasional">Jasa & Operasional</option>
                </select>
            </td>
            <td class="p-2 text-center">
                <input type="number" min="1" name="items[${poRowIndex}][quantity]" value="1" oninput="calculatePoTotals()" class="qty-input w-20 px-2 py-2 rounded-xl border border-slate-300 text-center font-bold text-slate-900 text-xs">
            </td>
            <td class="p-2">
                <input type="text" name="items[${poRowIndex}][unit]" value="Pcs" placeholder="Pcs/Box" class="w-full px-2 py-2 rounded-xl border border-slate-300 text-slate-800 text-xs">
            </td>
            <td class="p-2">
                <input type="number" step="0.01" min="0" name="items[${poRowIndex}][unit_price]" value="0" oninput="calculatePoTotals()" placeholder="0" class="price-input w-full px-2.5 py-2 rounded-xl border border-slate-300 text-right font-mono font-bold text-slate-900 text-xs">
            </td>
            <td class="p-2 text-right font-mono font-bold text-slate-900 line-total text-xs">
                Rp 0
            </td>
            <td class="p-2 text-center">
                <button type="button" onclick="removePoRow(this)" class="text-rose-500 hover:text-rose-700 text-sm font-bold">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        poRowIndex++;
        calculatePoTotals();
    }

    function removePoRow(btn) {
        const rows = document.querySelectorAll('#purchaseRows tr');
        if (rows.length <= 1) {
            alert('Minimal 1 item barang dalam transaksi Purchase Order.');
            return;
        }
        btn.closest('tr').remove();
        calculatePoTotals();
    }

    function calculatePoTotals() {
        let subtotal = 0;
        const rows = document.querySelectorAll('#purchaseRows tr');

        rows.forEach(tr => {
            const qty = parseFloat(tr.querySelector('.qty-input').value) || 0;
            const price = parseFloat(tr.querySelector('.price-input').value) || 0;
            const lineTotal = qty * price;
            tr.querySelector('.line-total').innerText = 'Rp ' + lineTotal.toLocaleString('id-ID');
            subtotal += lineTotal;
        });

        const discount = parseFloat(document.getElementById('poDiscountInput').value) || 0;
        const tax = parseFloat(document.getElementById('poTaxInput').value) || 0;
        const grandTotal = (subtotal - discount) + tax;

        document.getElementById('poSubtotalText').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        document.getElementById('poGrandTotalText').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
    }

    document.addEventListener('DOMContentLoaded', calculatePoTotals);
</script>
@endsection
