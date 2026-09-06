@extends('layouts.app')

@section('title', 'Buat Resi Pengiriman (AWB)')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Form Terbitkan Resi Baru (Air Waybill)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Input data pengiriman lengkap dengan wilayah hierarki Provinsi, Kota/Kab, Kecamatan, Kelurahan & Kode Pos.</p>
        </div>
        <a href="{{ route('shipments.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-700">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <form action="{{ route('shipments.store') }}" method="POST" class="space-y-6">
        @csrf

        @if($deliveryOrder)
            <input type="hidden" name="delivery_order_id" value="{{ $deliveryOrder->id }}">
            <input type="hidden" name="customer_id" value="{{ $deliveryOrder->customer_id }}">
            <div class="p-4 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-800 text-xs font-semibold flex items-center space-x-2">
                <i class="fa-solid fa-circle-info text-indigo-600 text-base"></i>
                <span>Menerbitkan Resi dari Delivery Order: <strong>{{ $deliveryOrder->do_number }}</strong></span>
            </div>
        @else
            <!-- Customer Selector if not coming from DO -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2">Pilih Customer Pengirim *</h3>
                <select name="customer_id" id="customerSelect" onchange="onCustomerSelectChange(this)" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="">-- Pilih Customer --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" data-name="{{ $c->company_name ?: $c->name }}" data-phone="{{ $c->phone }}" data-province="{{ $c->province }}" data-city="{{ $c->city }}" data-district="{{ $c->district }}" data-subdistrict="{{ $c->subdistrict }}" data-postal="{{ $c->postal_code }}" data-address="{{ $c->address }}">
                            {{ $c->name }} ({{ $c->company_name ?? $c->city }})
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <!-- Hubs & Courier Selection -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2">1. Transit Hub & Kurir Assigned</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Hub Asal *</label>
                    <select name="origin_hub_id" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        @foreach($hubs as $h)
                            <option value="{{ $h->id }}">{{ $h->name }} ({{ $h->city }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Hub Tujuan *</label>
                    <select name="destination_hub_id" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        @foreach($hubs as $h)
                            <option value="{{ $h->id }}" {{ $loop->index == 1 ? 'selected' : '' }}>{{ $h->name }} ({{ $h->city }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Kurir (Opsional)</label>
                    <select name="courier_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        <option value="">-- Ditugaskan Nanti --</option>
                        @foreach($couriers as $cr)
                            <option value="{{ $cr->id }}">{{ $cr->name }} ({{ $cr->courier_code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Armada (Opsional)</label>
                    <select name="vehicle_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        <option value="">-- Pilih Armada Kendaraan --</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}">{{ $v->plate_number }} ({{ $v->vehicle_type }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Sender & Recipient Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Sender -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2">2. Data Pengirim & Hirarki Wilayah Asal</h3>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Pengirim *</label>
                    <input type="text" name="sender_name" id="senderNameInput" value="{{ $deliveryOrder ? $deliveryOrder->sender_name : '' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. HP Pengirim *</label>
                    <input type="text" name="sender_phone" id="senderPhoneInput" value="{{ $deliveryOrder ? $deliveryOrder->sender_phone : '' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>

                <!-- Sender Regional Dropdowns -->
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Provinsi Asal</label>
                        <input type="text" onkeyup="filterSelectOptions(this, 'senderProvSelect')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px] font-medium">
                        <select name="sender_province" id="senderProvSelect" onchange="onSenderProvChange(this.value)" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                            <option value="">-- Pilih Provinsi --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kota / Kab Asal *</label>
                        <input type="text" onkeyup="filterSelectOptions(this, 'senderCitySelect')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px] font-medium">
                        <select name="sender_city" id="senderCitySelect" onchange="onSenderCityChange(this.value)" required class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-bold text-indigo-700">
                            <option value="">-- Pilih Kota/Kab --</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kecamatan</label>
                        <input type="text" onkeyup="filterSelectOptions(this, 'senderDistSelect')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px] font-medium">
                        <select name="sender_district" id="senderDistSelect" onchange="onSenderDistChange(this.value)" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                            <option value="">-- Pilih Kecamatan --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kelurahan</label>
                        <input type="text" onkeyup="filterSelectOptions(this, 'senderSubdistSelect')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px] font-medium">
                        <select name="sender_subdistrict" id="senderSubdistSelect" onchange="onSenderSubdistChange(this.value)" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                            <option value="">-- Pilih Kelurahan --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kode Pos</label>
                        <input type="text" name="sender_postal_code" id="senderPostalInput" placeholder="12430" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-bold text-indigo-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Lengkap Pengirim *</label>
                    <textarea name="sender_address" id="senderAddressInput" rows="2" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800" placeholder="Jl. Raya Utama No. 88...">{{ $deliveryOrder ? $deliveryOrder->sender_address : '' }}</textarea>
                </div>
            </div>

            <!-- Recipient -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2">3. Data Penerima & Hirarki Wilayah Tujuan</h3>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Penerima *</label>
                    <input type="text" name="recipient_name" value="{{ $deliveryOrder ? $deliveryOrder->recipient_name : '' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. HP Penerima *</label>
                    <input type="text" name="recipient_phone" value="{{ $deliveryOrder ? $deliveryOrder->recipient_phone : '' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>

                <!-- Recipient Regional Dropdowns -->
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Provinsi Tujuan</label>
                        <input type="text" onkeyup="filterSelectOptions(this, 'shipmentProvSelect')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px] font-medium">
                        <select name="recipient_province" id="shipmentProvSelect" onchange="onShipmentProvChange(this.value)" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                            <option value="">-- Pilih Provinsi --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kota / Kab Tujuan *</label>
                        <input type="text" onkeyup="filterSelectOptions(this, 'shipmentCitySelect')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px] font-medium">
                        <select name="recipient_city" id="shipmentCitySelect" onchange="onShipmentCityChange(this.value)" required class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-bold text-indigo-700">
                            <option value="">-- Pilih Kota/Kab --</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kecamatan</label>
                        <input type="text" onkeyup="filterSelectOptions(this, 'shipmentDistSelect')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px] font-medium">
                        <select name="recipient_district" id="shipmentDistSelect" onchange="onShipmentDistChange(this.value)" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                            <option value="">-- Pilih Kecamatan --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kelurahan</label>
                        <input type="text" onkeyup="filterSelectOptions(this, 'shipmentSubdistSelect')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px] font-medium">
                        <select name="recipient_subdistrict" id="shipmentSubdistSelect" onchange="onShipmentSubdistChange(this.value)" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                            <option value="">-- Pilih Kelurahan --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kode Pos</label>
                        <input type="text" name="recipient_postal_code" id="shipmentPostalInput" placeholder="40135" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-bold text-indigo-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Jalan / No. Rumah *</label>
                    <textarea name="recipient_address" id="recipientAddressInput" rows="2" required placeholder="Jl. Sudirman No. 45..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">{{ $deliveryOrder ? $deliveryOrder->recipient_address : '' }}</textarea>
                </div>
            </div>
        </div>

        <!-- Package & Fee Info -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2">4. Informasi Paket & Tarif Pembayaran</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Layanan Pengiriman *</label>
                    <select name="service_type" id="serviceTypeSelect" onchange="updateLivePrice()" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        <option value="Express">Express (1-2 Hari)</option>
                        <option value="Regular">Regular (2-4 Hari)</option>
                        <option value="SameDay">SameDay (Hari Ini)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Berat Paket (kg) *</label>
                    <input type="number" step="0.1" name="weight_kg" id="weightKgInput" oninput="updateLivePrice()" value="1.0" min="0.1" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nilai Pertanggungan (Rp)</label>
                    <input type="number" name="declared_value" id="declaredValueInput" oninput="updateLivePrice()" value="0" min="0" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Metode Pembayaran *</label>
                    <select name="payment_method" id="paymentMethodSelect" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        <option value="Transfer">Transfer Bank</option>
                        <option value="Cash">Cash / Tunai</option>
                        <option value="COD">COD (Bayar Di Tempat)</option>
                    </select>
                </div>
            </div>

            <!-- Live Calculated Price Banner -->
            <div class="p-5 rounded-2xl bg-gradient-to-br from-indigo-950 via-slate-900 to-slate-950 text-white shadow-xl space-y-3 border border-indigo-500/30">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-indigo-300 flex items-center">
                        <i class="fa-solid fa-calculator mr-2 text-indigo-400"></i> Rincian Perhitungan Biaya Resi Pengiriman
                    </h4>
                    <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 text-[10px] font-bold border border-indigo-500/30">
                        ⚡ Real-Time Rate
                    </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Rute Pengiriman</div>
                        <div class="font-bold text-slate-200 truncate mt-0.5" id="displayRouteText">- &rarr; -</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Tarif Dasar per KG</div>
                        <div class="font-bold text-indigo-300 mt-0.5" id="displayPricePerKg">Rp 0 / kg</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Subtotal Biaya Ongkir</div>
                        <div class="font-bold text-slate-200 mt-0.5" id="displayShippingFee">Rp 0</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Biaya Asuransi (0.2%)</div>
                        <div class="font-bold text-slate-200 mt-0.5" id="displayInsuranceFee">Rp 0</div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-800">
                    <div>
                        <div class="text-xs font-extrabold uppercase text-slate-300 tracking-wider">HARGA YANG HARUS DIBAYAR:</div>
                        <div class="text-[11px] text-indigo-300" id="displayEstDays">Estimasi Pengiriman: 2-3 Hari</div>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-black text-emerald-400 tracking-tight" id="displayTotalAmount">Rp 0</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('shipments.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg shadow-indigo-600/30">
                <i class="fa-solid fa-paper-plane mr-1.5"></i> Terbitkan Resi AWB (Generate QR Code)
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

    // Sender Regional Cascading
    function initSenderRegions(targetProv = null, targetCity = null, targetDist = null, targetSubdist = null) {
        const provSelect = document.getElementById('senderProvSelect');
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

        updateLivePrice();

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
    function initShipmentRegions(targetProv = null, targetCity = null, targetDist = null, targetSubdist = null) {
        const provSelect = document.getElementById('shipmentProvSelect');
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

                if (targetProv) {
                    const found = Array.from(provSelect.options).find(o => o.value.toLowerCase().includes(targetProv.toLowerCase()));
                    if (found) provSelect.value = found.value;
                } else if (provinces.length > 0) {
                    const jabar = Array.from(provSelect.options).find(o => o.value.includes('Jawa Barat'));
                    if (jabar) provSelect.value = jabar.value;
                    else provSelect.value = provinces[0].name;
                }
                onShipmentProvChange(provSelect.value, targetCity, targetDist, targetSubdist);
            });
    }

    function onShipmentProvChange(provName, targetCity = null, targetDist = null, targetSubdist = null) {
        const provSelect = document.getElementById('shipmentProvSelect');
        const citySelect = document.getElementById('shipmentCitySelect');
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

                if (targetCity) {
                    const found = Array.from(citySelect.options).find(o => o.value.toLowerCase().includes(targetCity.toLowerCase()));
                    if (found) citySelect.value = found.value;
                } else if (cities.length > 0) {
                    citySelect.value = cities[0].name;
                }
                onShipmentCityChange(citySelect.value, targetDist, targetSubdist);
            });
    }

    function onShipmentCityChange(cityName, targetDist = null, targetSubdist = null) {
        const citySelect = document.getElementById('shipmentCitySelect');
        const distSelect = document.getElementById('shipmentDistSelect');
        const selectedOpt = Array.from(citySelect.options).find(o => o.value === cityName);
        const cityId = selectedOpt ? selectedOpt.dataset.id : null;

        updateLivePrice();

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

                if (targetDist) {
                    const found = Array.from(distSelect.options).find(o => o.value.toLowerCase().includes(targetDist.toLowerCase()));
                    if (found) distSelect.value = found.value;
                }
                onShipmentDistChange(distSelect.value, targetSubdist);
            });
    }

    function onShipmentDistChange(distName, targetSubdist = null) {
        const distSelect = document.getElementById('shipmentDistSelect');
        const subdistSelect = document.getElementById('shipmentSubdistSelect');
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

                if (targetSubdist) {
                    const found = Array.from(subdistSelect.options).find(o => o.value.toLowerCase().includes(targetSubdist.toLowerCase()));
                    if (found) subdistSelect.value = found.value;
                }
                onShipmentSubdistChange(subdistSelect.value);
            });
    }

    function onShipmentSubdistChange(subdistName) {
        const subdistSelect = document.getElementById('shipmentSubdistSelect');
        const postalInput = document.getElementById('shipmentPostalInput');
        const selectedOpt = Array.from(subdistSelect.options).find(o => o.value === subdistName);

        if (selectedOpt && selectedOpt.dataset.postal) {
            postalInput.value = selectedOpt.dataset.postal;
        }
    }

    function onCustomerSelectChange(selectElem) {
        const opt = selectElem.options[selectElem.selectedIndex];
        if (opt && opt.value !== "") {
            if (document.getElementById('senderNameInput')) document.getElementById('senderNameInput').value = opt.getAttribute('data-name') || '';
            if (document.getElementById('senderPhoneInput')) document.getElementById('senderPhoneInput').value = opt.getAttribute('data-phone') || '';
            if (document.getElementById('senderAddressInput')) document.getElementById('senderAddressInput').value = opt.getAttribute('data-address') || '';

            const prov = opt.getAttribute('data-province');
            const city = opt.getAttribute('data-city');
            const dist = opt.getAttribute('data-district');
            const subdist = opt.getAttribute('data-subdistrict');
            const postal = opt.getAttribute('data-postal');

            if (postal && document.getElementById('senderPostalInput')) {
                document.getElementById('senderPostalInput').value = postal;
            }

            initSenderRegions(prov, city, dist, subdist);
        }
    }

    function updateLivePrice() {
        const originSelect = document.getElementById('senderCitySelect');
        const destinationSelect = document.getElementById('shipmentCitySelect');
        const serviceSelect = document.getElementById('serviceTypeSelect');
        const weightInput = document.getElementById('weightKgInput');
        const declaredInput = document.getElementById('declaredValueInput');

        const senderDistSelect = document.getElementById('senderDistSelect');
        const recipientDistSelect = document.getElementById('shipmentDistSelect');

        const origin = originSelect ? (originSelect.value || 'Jakarta Selatan') : 'Jakarta Selatan';
        const destination = destinationSelect ? (destinationSelect.value || 'Kota Bandung') : 'Kota Bandung';
        const service = serviceSelect ? (serviceSelect.value || 'Express') : 'Express';
        const weight = weightInput ? (parseFloat(weightInput.value) || 1.0) : 1.0;
        const declaredVal = declaredInput ? (parseFloat(declaredInput.value) || 0) : 0;

        const senderDistrict = senderDistSelect ? senderDistSelect.value : null;
        const recipientDistrict = recipientDistSelect ? recipientDistSelect.value : null;

        fetch("{{ route('tariffs.calculate') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                origin_city: origin,
                destination_city: destination,
                origin_district: senderDistrict,
                destination_district: recipientDistrict,
                weight_kg: weight,
                service_type: service
            })
        })
        .then(res => res.json())
        .then(data => {
            const insuranceFee = declaredVal > 0 ? (declaredVal * 0.002) : 0;
            const shippingFee = data.total_fee || 0;
            const grandTotal = shippingFee + insuranceFee;

            if (document.getElementById('displayRouteText')) document.getElementById('displayRouteText').innerText = origin + ' → ' + destination;
            if (document.getElementById('displayPricePerKg')) document.getElementById('displayPricePerKg').innerText = 'Rp ' + (data.price_per_kg || 0).toLocaleString('id-ID') + ' / kg';
            if (document.getElementById('displayShippingFee')) document.getElementById('displayShippingFee').innerText = 'Rp ' + shippingFee.toLocaleString('id-ID');
            if (document.getElementById('displayInsuranceFee')) document.getElementById('displayInsuranceFee').innerText = 'Rp ' + insuranceFee.toLocaleString('id-ID');
            if (document.getElementById('displayEstDays')) document.getElementById('displayEstDays').innerText = 'Estimasi Pengiriman: ' + (data.estimated_days || '1-2 Hari');
            if (document.getElementById('displayTotalAmount')) document.getElementById('displayTotalAmount').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
        })
        .catch(err => console.log('Error calculating live price:', err));
    }

    document.addEventListener('DOMContentLoaded', function() {
        const custSel = document.getElementById('customerSelect');
        if (custSel && custSel.options.length > 0) {
            if (custSel.value) {
                onCustomerSelectChange(custSel);
            } else if (custSel.options.length === 1 || (custSel.options.length === 2 && custSel.options[0].value === '')) {
                const optIndex = custSel.options[0].value === '' ? 1 : 0;
                custSel.selectedIndex = optIndex;
                onCustomerSelectChange(custSel);
            } else {
                initSenderRegions();
            }
        } else {
            initSenderRegions();
        }

        initShipmentRegions();
        setTimeout(updateLivePrice, 500);
    });
</script>
@endpush
@endsection
