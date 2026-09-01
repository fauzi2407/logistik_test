@extends('layouts.app')

@section('title', 'Terbit Invoice Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Terbitkan Invoice Penagihan Baru</h2>
            <p class="text-xs text-slate-500 mt-0.5">Buat tagihan biaya pengiriman logistik resmi untuk customer.</p>
        </div>
        <a href="{{ route('invoices.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-700">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <form action="{{ route('invoices.store') }}" method="POST" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
        @csrf

        <!-- Customer & DO Selection -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Cari & Pilih Customer *</label>
                <input type="text" onkeyup="filterSelectOptions(this, 'customerSelect')" placeholder="🔍 Cari customer..." class="w-full px-3 py-1.5 mb-1.5 rounded-xl bg-slate-50 border border-slate-300 text-xs font-semibold text-slate-800">
                <select name="customer_id" id="customerSelect" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="">-- Pilih Customer --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ (old('customer_id') == $c->id || ($selectedDo && $selectedDo->customer_id == $c->id)) ? 'selected' : '' }}>
                            {{ $c->company_name ?: $c->name }} ({{ $c->city }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Referensi DO Surat Jalan (Opsional)</label>
                <select name="delivery_order_id" id="doSelect" onchange="onDoSelected(this)" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="">-- Tanpa DO (Input Manual) --</option>
                    @foreach($deliveryOrders as $do)
                        @php
                            $doTotal = ($do->shipments && $do->shipments->count() > 0) ? $do->shipments->sum('total_amount') : 0;
                            if ($doTotal == 0 && $do->items) {
                                $doTotal = $do->items->sum(function($item) {
                                    return $item->shipment ? $item->shipment->total_amount : (12000 * max(1, (int) ceil((float)$item->weight_kg)));
                                });
                            }
                        @endphp
                        <option value="{{ $do->id }}" data-customer="{{ $do->customer_id }}" data-amount="{{ $doTotal }}" {{ ($selectedDo && $selectedDo->id == $do->id) ? 'selected' : '' }}>
                            {{ $do->do_number }} - {{ $do->customer ? ($do->customer->company_name ?: $do->customer->name) : 'Customer' }} (Rp {{ number_format($doTotal, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Dates -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Invoice *</label>
                <input type="date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Jatuh Tempo *</label>
                <input type="date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+14 days'))) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>
        </div>

        <!-- Billing Amounts -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
            <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-800 flex items-center">
                <i class="fa-solid fa-calculator text-indigo-600 mr-2"></i> Perhitungan Rincian Tagihan
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Subtotal Biaya Ongkir (Rp) *</label>
                    @php
                        $initialSubtotal = 0;
                        if ($selectedDo) {
                            $initialSubtotal = ($selectedDo->shipments && $selectedDo->shipments->count() > 0) ? $selectedDo->shipments->sum('total_amount') : 0;
                            if ($initialSubtotal == 0 && $selectedDo->items) {
                                $initialSubtotal = $selectedDo->items->sum(function($item) {
                                    return $item->shipment ? $item->shipment->total_amount : (12000 * max(1, (int) ceil((float)$item->weight_kg)));
                                });
                            }
                        }
                    @endphp
                    <input type="number" name="subtotal" id="subtotalInput" value="{{ old('subtotal', $initialSubtotal) }}" required step="1000" min="0" oninput="calculateTotal()" placeholder="0" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-900">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-[11px] font-bold text-slate-600">Pajak PPN 11% (Rp)</label>
                        <button type="button" onclick="autoCalculatePpn()" class="text-[10px] font-bold text-indigo-600 hover:underline">+ PPN 11%</button>
                    </div>
                    <input type="number" name="tax_amount" id="taxInput" value="{{ old('tax_amount', 0) }}" step="100" min="0" oninput="calculateTotal()" placeholder="0" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-900">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Potongan Diskon (Rp)</label>
                    <input type="number" name="discount_amount" id="discountInput" value="{{ old('discount_amount', 0) }}" step="100" min="0" oninput="calculateTotal()" placeholder="0" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-900">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase text-slate-700">Grand Total Tagihan (Rp):</span>
                <span class="text-xl font-black text-emerald-600" id="grandTotalDisplay">Rp 0</span>
            </div>
        </div>

        <!-- Notes -->
        @php
            $bankName = \App\Models\AppSetting::get('bank_name', 'Bank BCA');
            $bankNo = \App\Models\AppSetting::get('bank_account_number', '8877-6655-44');
            $bankHolder = \App\Models\AppSetting::get('bank_account_name', 'PT Radja Express Logistics');
            $defaultBankInstruction = "Silakan melakukan pembayaran transfer ke " . $bankName . " No. Rek: " . $bankNo . " a/n " . $bankHolder . ". Mohon konfirmasi setelah melakukan pembayaran.";
        @endphp
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Catatan Tambahan / Instruksi Pembayaran</label>
                <button type="button" onclick="loadDefaultBankInstruction()" class="text-[11px] font-bold text-indigo-600 hover:underline">
                    <i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Ambil Dari Pengaturan Bank
                </button>
            </div>
            <textarea name="notes" id="notesInput" rows="3" placeholder="Contoh: Silakan melakukan pembayaran melalui transfer Bank BCA..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">{{ old('notes', $defaultBankInstruction) }}</textarea>
        </div>

        <!-- Actions -->
        <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
            <a href="{{ route('invoices.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</a>
            <button type="submit" class="px-6 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30">
                Terbitkan Invoice
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function filterSelectOptions(inputElem, selectId) {
        const filter = inputElem.value.toLowerCase();
        const select = document.getElementById(selectId);
        if (!select) return;
        const options = select.options;
        for (let i = 0; i < options.length; i++) {
            const txt = options[i].text.toLowerCase();
            if (options[i].value === "") continue;
            if (txt.includes(filter)) {
                options[i].style.display = "";
            } else {
                options[i].style.display = "none";
            }
        }
    }

    function onDoSelected(selectElem) {
        const selectedOpt = selectElem.options[selectElem.selectedIndex];
        if (selectedOpt && selectedOpt.value !== "") {
            const customerId = selectedOpt.getAttribute('data-customer');
            const amount = selectedOpt.getAttribute('data-amount');
            
            if (customerId) {
                document.getElementById('customerSelect').value = customerId;
            }
            if (amount && parseFloat(amount) > 0) {
                document.getElementById('subtotalInput').value = amount;
                calculateTotal();
            }
        }
    }

    function autoCalculatePpn() {
        const subtotal = parseFloat(document.getElementById('subtotalInput').value) || 0;
        const ppn = Math.round(subtotal * 0.11);
        document.getElementById('taxInput').value = ppn;
        calculateTotal();
    }

    function calculateTotal() {
        const subtotal = parseFloat(document.getElementById('subtotalInput').value) || 0;
        const tax = parseFloat(document.getElementById('taxInput').value) || 0;
        const discount = parseFloat(document.getElementById('discountInput').value) || 0;
        const grandTotal = Math.max(0, subtotal + tax - discount);

        document.getElementById('grandTotalDisplay').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
    }

    function loadDefaultBankInstruction() {
        const defaultText = "{{ addslashes($defaultBankInstruction) }}";
        document.getElementById('notesInput').value = defaultText;
    }

    document.addEventListener('DOMContentLoaded', function() {
        calculateTotal();
    });
</script>
@endpush
@endsection
