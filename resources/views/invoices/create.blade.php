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

    @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
            <div class="font-bold flex items-center"><i class="fa-solid fa-circle-exclamation mr-1.5"></i> Periksa kembali input formulir:</div>
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('invoices.store') }}" method="POST" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
        @csrf

        <!-- Customer & DO Selection -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">1. Pilih Customer Pengirim *</label>
                <input type="text" onkeyup="filterSelectOptions(this, 'customerSelect')" placeholder="🔍 Cari nama customer..." class="w-full px-3 py-1.5 mb-1.5 rounded-xl bg-slate-50 border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500">
                <select name="customer_id" id="customerSelect" required onchange="onCustomerChange(this.value)" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Pilih Customer Terlebih Dahulu --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ (old('customer_id', $customerId) == $c->id || ($selectedDo && $selectedDo->customer_id == $c->id)) ? 'selected' : '' }}>
                            {{ $c->company_name ?: $c->name }} ({{ $c->city }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">2. Referensi DO Surat Jalan</label>
                    <span id="doCountBadge" class="text-[10px] font-extrabold text-indigo-600"></span>
                </div>
                <div class="space-y-1.5">
                    <select name="delivery_order_id" id="doSelect" onchange="onDoSelected(this)" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Customer Terlebih Dahulu --</option>
                    </select>
                    <div id="doHelperText" class="text-[11px] text-slate-500 flex items-center">
                        <i class="fa-solid fa-circle-info text-slate-400 mr-1.5"></i> Silakan pilih customer di sebelah kiri untuk menampilkan DO yang belum selesai.
                    </div>
                </div>
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
    // Master data DO yang belum selesai
    const allDeliveryOrders = [
        @foreach($deliveryOrders as $do)
            @php
                $doTotal = ($do->shipments && $do->shipments->count() > 0) ? $do->shipments->sum('total_amount') : 0;
                if ($doTotal == 0 && $do->items) {
                    $doTotal = $do->items->sum(function($item) {
                        return $item->shipment ? $item->shipment->total_amount : (12000 * max(1, (int) ceil((float)$item->weight_kg)));
                    });
                }
            @endphp
            {
                id: {{ $do->id }},
                customerId: {{ $do->customer_id }},
                doNumber: "{{ $do->do_number }}",
                status: "{{ $do->status }}",
                orderDate: "{{ $do->order_date ? $do->order_date->format('d/m/Y') : '' }}",
                amount: {{ (float) $doTotal }},
                itemsCount: {{ count($do->items) }},
            },
        @endforeach
    ];

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

    function filterDoByCustomer(customerId, selectedDoId = null) {
        const doSelect = document.getElementById('doSelect');
        const badge = document.getElementById('doCountBadge');
        const helper = document.getElementById('doHelperText');
        if (!doSelect) return;

        doSelect.innerHTML = '';

        if (!customerId) {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = '-- Pilih Customer Terlebih Dahulu --';
            doSelect.appendChild(opt);
            if (badge) badge.textContent = '';
            if (helper) {
                helper.innerHTML = '<i class="fa-solid fa-circle-info text-slate-400 mr-1.5"></i> Silakan pilih customer di sebelah kiri untuk menampilkan DO yang belum selesai.';
            }
            return;
        }

        const filtered = allDeliveryOrders.filter(item => String(item.customerId) === String(customerId));

        const defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = '-- Tanpa DO (Input Manual Biaya Pengiriman) --';
        doSelect.appendChild(defaultOpt);

        if (filtered.length === 0) {
            if (badge) badge.innerHTML = '<span class="text-amber-600">0 DO Belum Selesai</span>';
            if (helper) {
                helper.innerHTML = '<span class="text-amber-600 font-semibold"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Tidak ada DO yang belum selesai untuk customer ini. Anda tetap dapat menginput nominal tagihan manual.</span>';
            }
        } else {
            if (badge) badge.innerHTML = `<span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200"><i class="fa-solid fa-check mr-1"></i> ${filtered.length} DO Belum Selesai</span>`;
            if (helper) {
                helper.innerHTML = `<span class="text-indigo-600 font-semibold"><i class="fa-solid fa-circle-check mr-1"></i> Ditemukan ${filtered.length} DO belum selesai milik customer ini.</span>`;
            }

            filtered.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.id;
                opt.setAttribute('data-customer', item.customerId);
                opt.setAttribute('data-amount', item.amount);
                const formattedAmount = 'Rp ' + Number(item.amount).toLocaleString('id-ID');
                opt.textContent = `${item.doNumber} [${item.status.toUpperCase()}] • ${item.itemsCount} Item • ${formattedAmount}`;
                if (selectedDoId && String(selectedDoId) === String(item.id)) {
                    opt.selected = true;
                }
                doSelect.appendChild(opt);
            });
        }
    }

    function onCustomerChange(customerId) {
        const doSelect = document.getElementById('doSelect');
        const currentDoId = doSelect ? doSelect.value : null;

        // Check if currently selected DO belongs to new customer
        const isCurrentDoValid = allDeliveryOrders.some(d => String(d.id) === String(currentDoId) && String(d.customerId) === String(customerId));

        filterDoByCustomer(customerId, isCurrentDoValid ? currentDoId : null);

        if (!isCurrentDoValid) {
            if (doSelect) doSelect.value = '';
        }
    }

    function onDoSelected(selectElem) {
        const selectedOpt = selectElem.options[selectElem.selectedIndex];
        if (selectedOpt && selectedOpt.value !== "") {
            const amount = selectedOpt.getAttribute('data-amount');
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
        const customerSelect = document.getElementById('customerSelect');
        const initialCustomerId = customerSelect ? customerSelect.value : null;
        const initialDoId = "{{ old('delivery_order_id', $selectedDo ? $selectedDo->id : '') }}";

        if (initialCustomerId) {
            filterDoByCustomer(initialCustomerId, initialDoId);
            if (initialDoId) {
                const doSelect = document.getElementById('doSelect');
                if (doSelect && doSelect.value) {
                    onDoSelected(doSelect);
                }
            }
        } else {
            filterDoByCustomer(null, null);
        }

        calculateTotal();
    });
</script>
@endpush
@endsection
