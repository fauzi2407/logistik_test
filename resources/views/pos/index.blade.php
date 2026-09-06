@extends('layouts.app')

@section('title', 'Menu Kasir / POS Resi AWB')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header Bar -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">
                    <i class="fa-solid fa-cash-register mr-1"></i> POS Counter Active
                </span>
                <span class="text-xs text-slate-400 font-mono">Operator: {{ Auth::user() ? Auth::user()->name : 'Kasir' }}</span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight mt-1">Menu Kasir (POS Resi & Struk Pembayaran)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Input cepat customer baru, pengirim, penerima, hitung tarif real-time, dan cetak langsung struk kasir & resi AWB.</p>
        </div>
        <button onclick="openNewCustomerModal()" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition flex items-center">
            <i class="fa-solid fa-user-plus mr-1.5"></i> + Input Customer Baru
        </button>
    </div>

    <!-- Main POS Form -->
    <form id="posForm" onsubmit="handlePosFormSubmit(event)" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf

        <!-- Left & Center Columns: Customer, Pengirim, & Penerima Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- 1. Customer Select & Data Pengirim -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                        <i class="fa-solid fa-user-tag text-indigo-600 mr-1.5"></i> 1. Customer Pengirim & Wilayah Asal
                    </h3>
                    <button type="button" onclick="openNewCustomerModal()" class="text-xs font-bold text-indigo-600 hover:underline">
                        <i class="fa-solid fa-plus mr-1"></i> Customer Baru
                    </button>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih / Cari Customer Pengirim *</label>
                        <div class="space-y-1.5">
                            <input type="text" id="customerSearchInput" onkeyup="filterCustomerOptions(this)" placeholder="🔍 Ketik nama, HP, perusahaan, atau kota customer untuk mencari..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-slate-50/50">
                            <select name="customer_id" id="customerSelect" onchange="onCustomerSelectChange(this)" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Pilih Customer Terdaftar --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" data-name="{{ $c->company_name ?: $c->name }}" data-phone="{{ $c->phone }}" data-province="{{ $c->province }}" data-city="{{ $c->city }}" data-district="{{ $c->district }}" data-subdistrict="{{ $c->subdistrict }}" data-postal="{{ $c->postal_code }}" data-address="{{ $c->address }}">
                                        {{ $c->name }} {{ $c->company_name ? '('.$c->company_name.')' : '' }} - HP: {{ $c->phone }} ({{ $c->city }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Nama Pengirim *</label>
                            <input type="text" name="sender_name" id="senderNameInput" required placeholder="Nama pengirim..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">No. HP Pengirim *</label>
                            <input type="text" name="sender_phone" id="senderPhoneInput" required placeholder="0812..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        </div>
                    </div>

                    <!-- Sender Regional Dropdowns -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs bg-slate-50 p-3 rounded-xl border border-slate-200">
                        <div>
                            <label class="block font-bold text-slate-700 mb-0.5">Provinsi Asal</label>
                            <select name="sender_province" id="senderProvSelect" onchange="onSenderProvChange(this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-[11px] font-semibold text-slate-800">
                                <option value="">-- Provinsi --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-0.5">Kota / Kab Asal *</label>
                            <select name="sender_city" id="senderCitySelect" onchange="onSenderCityChange(this.value)" required class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-[11px] font-bold text-indigo-700">
                                <option value="">-- Kota/Kab --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-0.5">Kecamatan</label>
                            <select name="sender_district" id="senderDistSelect" onchange="onSenderDistChange(this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-[11px] font-semibold text-slate-800">
                                <option value="">-- Kecamatan --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-0.5">Kelurahan</label>
                            <select name="sender_subdistrict" id="senderSubdistSelect" onchange="onSenderSubdistChange(this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-[11px] font-semibold text-slate-800">
                                <option value="">-- Kelurahan --</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Kode Pos Asal</label>
                            <input type="text" name="sender_postal_code" id="senderPostalInput" placeholder="12430" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-indigo-600">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Alamat Jalan Pengirim *</label>
                            <input type="text" name="sender_address" id="senderAddressInput" required placeholder="Jl. Raya Merdeka No. 12..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Data Penerima -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">
                    <i class="fa-solid fa-location-dot text-indigo-600 mr-1.5"></i> 2. Data Penerima & Hirarki Wilayah Tujuan
                </h3>

                <div class="space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Nama Penerima *</label>
                            <input type="text" name="recipient_name" required placeholder="Nama lengkap penerima..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">No. HP Penerima *</label>
                            <input type="text" name="recipient_phone" required placeholder="0857..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        </div>
                    </div>

                    <!-- Recipient Regional Dropdowns -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs bg-indigo-50/50 p-3 rounded-xl border border-indigo-100">
                        <div>
                            <label class="block font-bold text-slate-700 mb-0.5">Provinsi Tujuan</label>
                            <select name="recipient_province" id="recipientProvSelect" onchange="onRecipientProvChange(this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-[11px] font-semibold text-slate-800">
                                <option value="">-- Provinsi --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-0.5">Kota / Kab Tujuan *</label>
                            <select name="recipient_city" id="recipientCitySelect" onchange="onRecipientCityChange(this.value)" required class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-[11px] font-bold text-indigo-700">
                                <option value="">-- Kota/Kab --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-0.5">Kecamatan</label>
                            <select name="recipient_district" id="recipientDistSelect" onchange="onRecipientDistChange(this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-[11px] font-semibold text-slate-800">
                                <option value="">-- Kecamatan --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-0.5">Kelurahan</label>
                            <select name="recipient_subdistrict" id="recipientSubdistSelect" onchange="onRecipientSubdistChange(this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-[11px] font-semibold text-slate-800">
                                <option value="">-- Kelurahan --</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Kode Pos Tujuan</label>
                            <input type="text" name="recipient_postal_code" id="recipientPostalInput" placeholder="40135" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-indigo-600">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Alamat Jalan / Detail Penerima *</label>
                            <input type="text" name="recipient_address" required placeholder="Jl. Sudirman No. 45..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Package Specs, Live Price Calculation & Payment Terminal -->
        <div class="space-y-6">
            <!-- 3. Spesifikasi Paket & Hub -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">
                    <i class="fa-solid fa-box text-indigo-600 mr-1.5"></i> 3. Spesifikasi Paket & Layanan
                </h3>

                <div class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Layanan Pengiriman *</label>
                        <select name="service_type" id="serviceTypeSelect" onchange="calculateLivePrice()" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                            <option value="Express">Express (1-2 Hari)</option>
                            <option value="Regular">Regular (2-4 Hari)</option>
                            <option value="SameDay">SameDay (Hari Ini)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Berat (kg) *</label>
                            <input type="number" step="0.1" name="weight_kg" id="weightKgInput" oninput="calculateLivePrice()" value="1.0" min="0.1" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-indigo-700">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Nilai Barang (Rp)</label>
                            <input type="number" name="declared_value" id="declaredValueInput" oninput="calculateLivePrice()" value="0" min="0" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Hub Asal *</label>
                            <select name="origin_hub_id" required class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-[11px] font-semibold text-slate-800">
                                @foreach($hubs as $h)
                                    <option value="{{ $h->id }}">{{ $h->name }} ({{ $h->city }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Hub Tujuan *</label>
                            <select name="destination_hub_id" required class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-[11px] font-semibold text-slate-800">
                                @foreach($hubs as $h)
                                    <option value="{{ $h->id }}" {{ $loop->index == 1 ? 'selected' : '' }}>{{ $h->name }} ({{ $h->city }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Real-time Payment Calculator & Cashier Terminal -->
            <div class="bg-gradient-to-br from-slate-900 via-slate-950 to-indigo-950 text-white p-6 rounded-2xl shadow-xl space-y-4 border border-indigo-500/30">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-indigo-300 flex items-center">
                        <i class="fa-solid fa-calculator text-indigo-400 mr-2"></i> Cashier Terminal & Payment
                    </h3>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-bold border border-emerald-500/30">
                        Live Rate
                    </span>
                </div>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Rute Pengiriman:</span>
                        <span class="font-bold text-slate-200" id="displayRoute">- &rarr; -</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Tarif Per KG:</span>
                        <span class="font-bold text-indigo-300" id="displayPricePerKg">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Subtotal Ongkir:</span>
                        <span class="font-bold text-slate-200" id="displayShippingFee">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Asuransi (0.2%):</span>
                        <span class="font-bold text-slate-200" id="displayInsuranceFee">Rp 0</span>
                    </div>
                    <div class="border-t border-slate-800 pt-2 flex justify-between items-center">
                        <span class="font-extrabold uppercase text-slate-300 text-xs">TOTAL BAYAR:</span>
                        <span class="text-2xl font-black text-emerald-400" id="displayTotalAmount">Rp 0</span>
                    </div>
                </div>

                <!-- Payment Methods & Cash Tendered -->
                <div class="space-y-3 pt-2 border-t border-slate-800">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Metode Pembayaran *</label>
                        <select name="payment_method" id="paymentMethodSelect" onchange="onPaymentMethodChange(this.value)" required class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-xs font-bold text-white focus:ring-2 focus:ring-emerald-500">
                            <option value="Cash">Cash / Tunai (Bayar Di Kasir)</option>
                            <option value="Transfer">Transfer Bank (BCA/Mandiri)</option>
                            <option value="QRIS">QRIS / E-Wallet</option>
                            <option value="COD">COD (Bayar Di Tempat Tujuan)</option>
                        </select>
                    </div>

                    <div id="cashTenderedContainer" class="space-y-2">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Uang Dibayar (Rp)</label>
                                <input type="number" name="cash_tendered" id="cashTenderedInput" oninput="calculateChange()" placeholder="0" class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-xs font-black text-emerald-400">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Kembalian (Rp)</label>
                                <input type="text" id="cashChangeDisplay" readonly value="Rp 0" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs font-black text-amber-400 cursor-not-allowed">
                            </div>
                        </div>

                        <!-- Quick Cash Shortcut Buttons -->
                        <div class="flex items-center space-x-1.5 pt-1">
                            <button type="button" onclick="setQuickCash('exact')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] font-bold">Uang Pas</button>
                            <button type="button" onclick="setQuickCash(50000)" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] font-bold">50.000</button>
                            <button type="button" onclick="setQuickCash(100000)" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] font-bold">100.000</button>
                        </div>
                    </div>
                </div>

                <button type="submit" id="posSubmitBtn" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-extrabold text-sm shadow-lg shadow-emerald-500/30 transition flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-receipt text-lg"></i>
                    <span>Bayar & Terbitkan Resi AWB</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Modal Input Customer Baru Quick POS -->
<div id="newCustomerModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl text-left max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm flex items-center">
                <i class="fa-solid fa-user-plus text-emerald-600 mr-2"></i> Pendaftaran Customer Baru (POS)
            </h3>
            <button onclick="closeNewCustomerModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="newCustomerForm" onsubmit="handleNewCustomerSubmit(event)" class="space-y-3 text-xs">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Customer / Perusahaan *</label>
                    <input type="text" name="name" required placeholder="Contoh: PT Sumber Rejeki" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">No. HP / WhatsApp *</label>
                    <input type="text" name="phone" required placeholder="0812998877..." class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Email Customer (Opsional)</label>
                <input type="email" name="email" placeholder="customer@email.com" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
            </div>

            <!-- Regional Dropdowns inside Customer Modal -->
            <div class="space-y-1.5 pt-1">
                <label class="block font-bold text-slate-700">Hirarki Wilayah Customer</label>
                <div class="grid grid-cols-2 gap-2 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                    <div>
                        <label class="block font-semibold text-slate-600 text-[10px]">Provinsi</label>
                        <select name="province" id="custModalProvSelect" onchange="onCustModalProvChange(this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-[11px] font-semibold">
                            <option value="">-- Pilih Provinsi --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 text-[10px]">Kota / Kab *</label>
                        <select name="city" id="custModalCitySelect" onchange="onCustModalCityChange(this.value)" required class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-[11px] font-bold text-indigo-700">
                            <option value="">-- Pilih Kota/Kab --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 text-[10px]">Kecamatan</label>
                        <select name="district" id="custModalDistSelect" onchange="onCustModalDistChange(this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-[11px] font-semibold">
                            <option value="">-- Pilih Kecamatan --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 text-[10px]">Kelurahan</label>
                        <select name="subdistrict" id="custModalSubdistSelect" onchange="onCustModalSubdistChange(this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-[11px] font-semibold">
                            <option value="">-- Pilih Kelurahan --</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kode Pos</label>
                    <input type="text" name="postal_code" id="custModalPostalInput" placeholder="12430" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-bold text-indigo-600">
                </div>
                <div class="col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Alamat Jalan / Rumah *</label>
                    <input type="text" name="address" required placeholder="Jl. Utama No. 88..." class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                </div>
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="closeNewCustomerModal()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" id="custModalSubmitBtn" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md">Simpan Customer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Success Post Transaction POS -->
<div id="posSuccessModal" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 text-center space-y-4 shadow-2xl">
        <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-3xl mx-auto">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div>
            <h3 class="text-lg font-black text-slate-900">Transaksi Kasir POS Berhasil!</h3>
            <p class="text-xs text-slate-500 mt-1">Resi pengantaran dan pembayaran lunas telah diterbitkan ke sistem.</p>
        </div>
        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 font-mono font-black text-base text-indigo-700" id="successTrackingNumber">
            TRK-20260901-0001
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2">
            <a id="btnPrintReceipt" href="#" target="_blank" class="py-2.5 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow flex items-center justify-center space-x-1.5">
                <i class="fa-solid fa-receipt text-sm"></i>
                <span>Cetak Struk Kasir</span>
            </a>
            <a id="btnPrintLabel" href="#" target="_blank" class="py-2.5 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow flex items-center justify-center space-x-1.5">
                <i class="fa-solid fa-barcode text-sm"></i>
                <span>Cetak Label Resi</span>
            </a>
        </div>

        <div class="pt-2 border-t border-slate-100">
            <button onclick="resetPosForm()" class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                <i class="fa-solid fa-plus mr-1"></i> Transaksi Kasir Baru
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentTotalAmount = 0;

    // Sender Regional Cascading
    function initSenderRegions(targetProv = null, targetCity = null, targetDist = null, targetSubdist = null) {
        const provSelect = document.getElementById('senderProvSelect');
        if (!provSelect) return;
        provSelect.innerHTML = '<option value="">-- Provinsi --</option>';

        fetch("{{ route('api.provinces') }}")
            .then(res => res.json())
            .then(provinces => {
                provinces.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.name;
                    opt.dataset.id = p.id;
                    opt.textContent = p.name;
                    provSelect.appendChild(opt);
                });

                if (targetProv) {
                    const found = Array.from(provSelect.options).find(o => o.value.toLowerCase().includes(targetProv.toLowerCase()));
                    if (found) provSelect.value = found.value;
                } else if (provinces.length > 0) {
                    const dki = Array.from(provSelect.options).find(o => o.value.includes('DKI Jakarta'));
                    if (dki) provSelect.value = dki.value;
                }
                onSenderProvChange(provSelect.value, targetCity, targetDist, targetSubdist);
            });
    }

    function onSenderProvChange(provName, targetCity = null, targetDist = null, targetSubdist = null) {
        const provSelect = document.getElementById('senderProvSelect');
        const citySelect = document.getElementById('senderCitySelect');
        const selectedOpt = Array.from(provSelect.options).find(o => o.value === provName);
        const provId = selectedOpt ? selectedOpt.dataset.id : null;

        citySelect.innerHTML = '<option value="">-- Kota/Kab --</option>';
        if (!provId) return;

        fetch(`/api/cities/${provId}`)
            .then(res => res.json())
            .then(cities => {
                cities.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.name;
                    opt.dataset.id = c.id;
                    opt.textContent = c.name;
                    citySelect.appendChild(opt);
                });

                if (targetCity) {
                    const found = Array.from(citySelect.options).find(o => o.value.toLowerCase().includes(targetCity.toLowerCase()));
                    if (found) citySelect.value = found.value;
                } else if (cities.length > 0) {
                    citySelect.value = cities[0].name;
                }
                onSenderCityChange(citySelect.value, targetDist, targetSubdist);
            });
    }

    function onSenderCityChange(cityName, targetDist = null, targetSubdist = null) {
        const citySelect = document.getElementById('senderCitySelect');
        const distSelect = document.getElementById('senderDistSelect');
        const selectedOpt = Array.from(citySelect.options).find(o => o.value === cityName);
        const cityId = selectedOpt ? selectedOpt.dataset.id : null;

        calculateLivePrice();

        distSelect.innerHTML = '<option value="">-- Kecamatan --</option>';
        if (!cityId) return;

        fetch(`/api/districts/${cityId}`)
            .then(res => res.json())
            .then(districts => {
                districts.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.name;
                    opt.dataset.id = d.id;
                    opt.textContent = d.name;
                    distSelect.appendChild(opt);
                });

                if (targetDist) {
                    const found = Array.from(distSelect.options).find(o => o.value.toLowerCase().includes(targetDist.toLowerCase()));
                    if (found) distSelect.value = found.value;
                }
                onSenderDistChange(distSelect.value, targetSubdist);
            });
    }

    function onSenderDistChange(distName, targetSubdist = null) {
        const distSelect = document.getElementById('senderDistSelect');
        const subdistSelect = document.getElementById('senderSubdistSelect');
        const selectedOpt = Array.from(distSelect.options).find(o => o.value === distName);
        const distId = selectedOpt ? selectedOpt.dataset.id : null;

        subdistSelect.innerHTML = '<option value="">-- Kelurahan --</option>';
        if (!distId) return;

        fetch(`/api/subdistricts/${distId}`)
            .then(res => res.json())
            .then(subdistricts => {
                subdistricts.forEach(sd => {
                    const opt = document.createElement('option');
                    opt.value = sd.name;
                    opt.dataset.postal = sd.postal_code;
                    opt.textContent = sd.name;
                    subdistSelect.appendChild(opt);
                });

                if (targetSubdist) {
                    const found = Array.from(subdistSelect.options).find(o => o.value.toLowerCase().includes(targetSubdist.toLowerCase()));
                    if (found) subdistSelect.value = found.value;
                }
                onSenderSubdistChange(subdistSelect.value);
            });
    }

    function onSenderSubdistChange(subdistName) {
        const subdistSelect = document.getElementById('senderSubdistSelect');
        const postalInput = document.getElementById('senderPostalInput');
        const selectedOpt = Array.from(subdistSelect.options).find(o => o.value === subdistName);

        if (selectedOpt && selectedOpt.dataset.postal) {
            postalInput.value = selectedOpt.dataset.postal;
        }
    }

    // Recipient Regional Cascading
    function initRecipientRegions(targetProv = null, targetCity = null, targetDist = null, targetSubdist = null) {
        const provSelect = document.getElementById('recipientProvSelect');
        if (!provSelect) return;
        provSelect.innerHTML = '<option value="">-- Provinsi --</option>';

        fetch("{{ route('api.provinces') }}")
            .then(res => res.json())
            .then(provinces => {
                provinces.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.name;
                    opt.dataset.id = p.id;
                    opt.textContent = p.name;
                    provSelect.appendChild(opt);
                });

                if (targetProv) {
                    const found = Array.from(provSelect.options).find(o => o.value.toLowerCase().includes(targetProv.toLowerCase()));
                    if (found) provSelect.value = found.value;
                } else if (provinces.length > 0) {
                    const jabar = Array.from(provSelect.options).find(o => o.value.includes('Jawa Barat'));
                    if (jabar) provSelect.value = jabar.value;
                    else provSelect.value = provinces[0].name;
                }
                onRecipientProvChange(provSelect.value, targetCity, targetDist, targetSubdist);
            });
    }

    function onRecipientProvChange(provName, targetCity = null, targetDist = null, targetSubdist = null) {
        const provSelect = document.getElementById('recipientProvSelect');
        const citySelect = document.getElementById('recipientCitySelect');
        const selectedOpt = Array.from(provSelect.options).find(o => o.value === provName);
        const provId = selectedOpt ? selectedOpt.dataset.id : null;

        citySelect.innerHTML = '<option value="">-- Kota/Kab --</option>';
        if (!provId) return;

        fetch(`/api/cities/${provId}`)
            .then(res => res.json())
            .then(cities => {
                cities.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.name;
                    opt.dataset.id = c.id;
                    opt.textContent = c.name;
                    citySelect.appendChild(opt);
                });

                if (targetCity) {
                    const found = Array.from(citySelect.options).find(o => o.value.toLowerCase().includes(targetCity.toLowerCase()));
                    if (found) citySelect.value = found.value;
                } else if (cities.length > 0) {
                    citySelect.value = cities[0].name;
                }
                onRecipientCityChange(citySelect.value, targetDist, targetSubdist);
            });
    }

    function onRecipientCityChange(cityName, targetDist = null, targetSubdist = null) {
        const citySelect = document.getElementById('recipientCitySelect');
        const distSelect = document.getElementById('recipientDistSelect');
        const selectedOpt = Array.from(citySelect.options).find(o => o.value === cityName);
        const cityId = selectedOpt ? selectedOpt.dataset.id : null;

        calculateLivePrice();

        distSelect.innerHTML = '<option value="">-- Kecamatan --</option>';
        if (!cityId) return;

        fetch(`/api/districts/${cityId}`)
            .then(res => res.json())
            .then(districts => {
                districts.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.name;
                    opt.dataset.id = d.id;
                    opt.textContent = d.name;
                    distSelect.appendChild(opt);
                });

                if (targetDist) {
                    const found = Array.from(distSelect.options).find(o => o.value.toLowerCase().includes(targetDist.toLowerCase()));
                    if (found) distSelect.value = found.value;
                }
                onRecipientDistChange(distSelect.value, targetSubdist);
            });
    }

    function onRecipientDistChange(distName, targetSubdist = null) {
        const distSelect = document.getElementById('recipientDistSelect');
        const subdistSelect = document.getElementById('recipientSubdistSelect');
        const selectedOpt = Array.from(distSelect.options).find(o => o.value === distName);
        const distId = selectedOpt ? selectedOpt.dataset.id : null;

        subdistSelect.innerHTML = '<option value="">-- Kelurahan --</option>';
        if (!distId) return;

        fetch(`/api/subdistricts/${distId}`)
            .then(res => res.json())
            .then(subdistricts => {
                subdistricts.forEach(sd => {
                    const opt = document.createElement('option');
                    opt.value = sd.name;
                    opt.dataset.postal = sd.postal_code;
                    opt.textContent = sd.name;
                    subdistSelect.appendChild(opt);
                });

                if (targetSubdist) {
                    const found = Array.from(subdistSelect.options).find(o => o.value.toLowerCase().includes(targetSubdist.toLowerCase()));
                    if (found) subdistSelect.value = found.value;
                }
                onRecipientSubdistChange(subdistSelect.value);
            });
    }

    function onRecipientSubdistChange(subdistName) {
        const subdistSelect = document.getElementById('recipientSubdistSelect');
        const postalInput = document.getElementById('recipientPostalInput');
        const selectedOpt = Array.from(subdistSelect.options).find(o => o.value === subdistName);

        if (selectedOpt && selectedOpt.dataset.postal) {
            postalInput.value = selectedOpt.dataset.postal;
        }
    }

    function filterCustomerOptions(inputElem) {
        const filter = inputElem.value.toLowerCase();
        const select = document.getElementById('customerSelect');
        if (!select) return;
        const options = select.options;
        let firstMatch = null;

        for (let i = 0; i < options.length; i++) {
            const txt = options[i].text.toLowerCase();
            if (options[i].value === "") continue;
            if (txt.includes(filter)) {
                options[i].style.display = "";
                if (!firstMatch) firstMatch = options[i];
            } else {
                options[i].style.display = "none";
            }
        }

        if (filter.length > 0 && firstMatch) {
            select.value = firstMatch.value;
            onCustomerSelectChange(select);
        }
    }

    // Customer Selection Change
    function onCustomerSelectChange(selectElem) {
        const opt = selectElem.options[selectElem.selectedIndex];
        if (opt && opt.value !== "") {
            document.getElementById('senderNameInput').value = opt.getAttribute('data-name') || '';
            document.getElementById('senderPhoneInput').value = opt.getAttribute('data-phone') || '';
            document.getElementById('senderAddressInput').value = opt.getAttribute('data-address') || '';

            const prov = opt.getAttribute('data-province');
            const city = opt.getAttribute('data-city');
            const dist = opt.getAttribute('data-district');
            const subdist = opt.getAttribute('data-subdistrict');
            const postal = opt.getAttribute('data-postal');

            if (postal) document.getElementById('senderPostalInput').value = postal;
            initSenderRegions(prov, city, dist, subdist);
        }
    }

    // Modal Customer Quick Register
    function openNewCustomerModal() {
        document.getElementById('newCustomerModal').classList.remove('hidden');
        initCustModalRegions();
    }

    function closeNewCustomerModal() {
        document.getElementById('newCustomerModal').classList.add('hidden');
    }

    function initCustModalRegions() {
        const provSelect = document.getElementById('custModalProvSelect');
        if (!provSelect) return;
        provSelect.innerHTML = '<option value="">-- Pilih Provinsi --</option>';

        fetch("{{ route('api.provinces') }}")
            .then(res => res.json())
            .then(provinces => {
                provinces.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.name;
                    opt.dataset.id = p.id;
                    opt.textContent = p.name;
                    provSelect.appendChild(opt);
                });
            });
    }

    function onCustModalProvChange(provName) {
        const provSelect = document.getElementById('custModalProvSelect');
        const citySelect = document.getElementById('custModalCitySelect');
        const selectedOpt = Array.from(provSelect.options).find(o => o.value === provName);
        const provId = selectedOpt ? selectedOpt.dataset.id : null;

        citySelect.innerHTML = '<option value="">-- Pilih Kota/Kab --</option>';
        if (!provId) return;

        fetch(`/api/cities/${provId}`)
            .then(res => res.json())
            .then(cities => {
                cities.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.name;
                    opt.dataset.id = c.id;
                    opt.textContent = c.name;
                    citySelect.appendChild(opt);
                });
            });
    }

    function onCustModalCityChange(cityName) {
        const citySelect = document.getElementById('custModalCitySelect');
        const distSelect = document.getElementById('custModalDistSelect');
        const selectedOpt = Array.from(citySelect.options).find(o => o.value === cityName);
        const cityId = selectedOpt ? selectedOpt.dataset.id : null;

        distSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        if (!cityId) return;

        fetch(`/api/districts/${cityId}`)
            .then(res => res.json())
            .then(districts => {
                districts.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.name;
                    opt.dataset.id = d.id;
                    opt.textContent = d.name;
                    distSelect.appendChild(opt);
                });
            });
    }

    function onCustModalDistChange(distName) {
        const distSelect = document.getElementById('custModalDistSelect');
        const subdistSelect = document.getElementById('custModalSubdistSelect');
        const selectedOpt = Array.from(distSelect.options).find(o => o.value === distName);
        const distId = selectedOpt ? selectedOpt.dataset.id : null;

        subdistSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
        if (!distId) return;

        fetch(`/api/subdistricts/${distId}`)
            .then(res => res.json())
            .then(subdistricts => {
                subdistricts.forEach(sd => {
                    const opt = document.createElement('option');
                    opt.value = sd.name;
                    opt.dataset.postal = sd.postal_code;
                    opt.textContent = sd.name;
                    subdistSelect.appendChild(opt);
                });
            });
    }

    function onCustModalSubdistChange(subdistName) {
        const subdistSelect = document.getElementById('custModalSubdistSelect');
        const postalInput = document.getElementById('custModalPostalInput');
        const selectedOpt = Array.from(subdistSelect.options).find(o => o.value === subdistName);

        if (selectedOpt && selectedOpt.dataset.postal) {
            postalInput.value = selectedOpt.dataset.postal;
        }
    }

    function handleNewCustomerSubmit(e) {
        e.preventDefault();
        const form = document.getElementById('newCustomerForm');
        const submitBtn = document.getElementById('custModalSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.innerText = 'Menyimpan...';

        const formData = new FormData(form);

        fetch("{{ route('pos.store-customer') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Simpan Customer';
            if (data.success) {
                closeNewCustomerModal();
                form.reset();

                // Add to customer select dropdown and select it
                const custSelect = document.getElementById('customerSelect');
                const opt = document.createElement('option');
                opt.value = data.customer.id;
                opt.dataset.name = data.customer.company_name || data.customer.name;
                opt.dataset.phone = data.customer.phone;
                opt.dataset.province = data.customer.province || '';
                opt.dataset.city = data.customer.city;
                opt.dataset.district = data.customer.district || '';
                opt.dataset.subdistrict = data.customer.subdistrict || '';
                opt.dataset.postal = data.customer.postal_code || '';
                opt.dataset.address = data.customer.address;
                opt.textContent = `${data.customer.name} (${data.customer.city}) - ${data.customer.phone}`;
                
                custSelect.appendChild(opt);
                custSelect.value = data.customer.id;
                onCustomerSelectChange(custSelect);

                alert(data.message || 'Customer baru berhasil ditambahkan!');
            } else {
                alert('Gagal menyimpan customer. Periksa data kembali.');
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Simpan Customer';
            alert('Terjadi kesalahan koneksi.');
        });
    }

    // Live Tariff Calculation
    function calculateLivePrice() {
        const originSelect = document.getElementById('senderCitySelect');
        const destinationSelect = document.getElementById('recipientCitySelect');
        const serviceSelect = document.getElementById('serviceTypeSelect');
        const weightInput = document.getElementById('weightKgInput');
        const declaredInput = document.getElementById('declaredValueInput');

        const senderDistSelect = document.getElementById('senderDistSelect');
        const recipientDistSelect = document.getElementById('recipientDistSelect');

        const origin = originSelect ? (originSelect.value || 'Jakarta Selatan') : 'Jakarta Selatan';
        const destination = destinationSelect ? (destinationSelect.value || 'Kota Bandung') : 'Kota Bandung';
        const service = serviceSelect ? (serviceSelect.value || 'Express') : 'Express';
        const weight = weightInput ? (parseFloat(weightInput.value) || 1.0) : 1.0;
        const declaredVal = declaredInput ? (parseFloat(declaredInput.value) || 0) : 0;

        fetch("{{ route('tariffs.calculate') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                origin_city: origin,
                destination_city: destination,
                origin_district: senderDistSelect ? senderDistSelect.value : null,
                destination_district: recipientDistSelect ? recipientDistSelect.value : null,
                weight_kg: weight,
                service_type: service
            })
        })
        .then(res => res.json())
        .then(data => {
            const insuranceFee = declaredVal > 0 ? (declaredVal * 0.002) : 0;
            const shippingFee = data.total_fee || 0;
            currentTotalAmount = shippingFee + insuranceFee;

            if (document.getElementById('displayRoute')) document.getElementById('displayRoute').innerText = origin + ' → ' + destination;
            if (document.getElementById('displayPricePerKg')) document.getElementById('displayPricePerKg').innerText = 'Rp ' + (data.price_per_kg || 0).toLocaleString('id-ID');
            if (document.getElementById('displayShippingFee')) document.getElementById('displayShippingFee').innerText = 'Rp ' + shippingFee.toLocaleString('id-ID');
            if (document.getElementById('displayInsuranceFee')) document.getElementById('displayInsuranceFee').innerText = 'Rp ' + insuranceFee.toLocaleString('id-ID');
            if (document.getElementById('displayTotalAmount')) document.getElementById('displayTotalAmount').innerText = 'Rp ' + currentTotalAmount.toLocaleString('id-ID');

            calculateChange();
        });
    }

    // Cashier Change Calculation
    function onPaymentMethodChange(method) {
        const cashContainer = document.getElementById('cashTenderedContainer');
        if (method === 'Cash') {
            cashContainer.classList.remove('hidden');
        } else {
            cashContainer.classList.add('hidden');
        }
    }

    function calculateChange() {
        const cashInput = document.getElementById('cashTenderedInput');
        const changeDisplay = document.getElementById('cashChangeDisplay');
        const cashTendered = parseFloat(cashInput.value) || 0;

        if (cashTendered >= currentTotalAmount) {
            const change = cashTendered - currentTotalAmount;
            changeDisplay.value = 'Rp ' + change.toLocaleString('id-ID');
        } else {
            changeDisplay.value = 'Uang Kurang';
        }
    }

    function setQuickCash(amount) {
        const cashInput = document.getElementById('cashTenderedInput');
        if (amount === 'exact') {
            cashInput.value = currentTotalAmount;
        } else {
            cashInput.value = amount;
        }
        calculateChange();
    }

    // Handle POS Form Submit via AJAX
    function handlePosFormSubmit(e) {
        e.preventDefault();
        const submitBtn = document.getElementById('posSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Memproses Transaksi Kasir...';

        const formData = new FormData(document.getElementById('posForm'));

        fetch("{{ route('pos.store-shipment') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-receipt text-lg"></i><span>Bayar & Terbitkan Resi AWB</span>';

            if (data.success) {
                document.getElementById('successTrackingNumber').innerText = data.tracking_number;
                document.getElementById('btnPrintReceipt').href = data.receipt_url;
                document.getElementById('btnPrintLabel').href = data.label_url;

                document.getElementById('posSuccessModal').classList.remove('hidden');
            } else {
                alert('Gagal memproses transaksi. Mohon periksa kembali form.');
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-receipt text-lg"></i><span>Bayar & Terbitkan Resi AWB</span>';
            alert('Terjadi kesalahan jaringan/server.');
        });
    }

    function resetPosForm() {
        document.getElementById('posSuccessModal').classList.add('hidden');
        document.getElementById('posForm').reset();
        initSenderRegions();
        initRecipientRegions();
        setTimeout(calculateLivePrice, 400);
    }

    document.addEventListener('DOMContentLoaded', function() {
        initSenderRegions();
        initRecipientRegions();

        const custSel = document.getElementById('customerSelect');
        if (custSel && custSel.options.length > 1) {
            custSel.selectedIndex = 1;
            onCustomerSelectChange(custSel);
        }

        setTimeout(calculateLivePrice, 500);
    });
</script>
@endpush
@endsection
