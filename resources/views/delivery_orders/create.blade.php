@extends('layouts.app')

@section('title', 'Buat Delivery Order (DO) & Request Pickup')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-indigo-100 text-indigo-800 mb-1">
                Corporate Logistics & Distribution
            </span>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Formulir Delivery Order (DO Multi-Tujuan & Request Pickup)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Input pengiriman massal ke banyak lokasi penerima sekaligus lengkap dengan hitung otomatis tarif ongkir per item.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('delivery-orders.template-csv') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                <i class="fa-solid fa-file-excel text-emerald-600 mr-1.5"></i> Download Template Excel
            </a>
            <a href="{{ route('delivery-orders.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-700 ml-2">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('delivery-orders.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Section 1: Customer & Pickup Request Form -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                    <i class="fa-solid fa-truck-pickup text-indigo-600 mr-1.5"></i> 1. Informasi Customer & Form Penjemputan (Pickup Request)
                </h3>
                <span class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full border border-indigo-100">
                    <i class="fa-solid fa-calculator mr-1"></i> Asal Penjemputan Tarif Ongkir
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Bank / Customer *</label>
                    <select name="customer_id" id="customerSelect" onchange="onCustomerSelectChange(this)" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        <option value="">-- Pilih Customer / Bank --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" data-name="{{ $c->name }}" data-phone="{{ $c->phone }}" data-province="{{ $c->province }}" data-city="{{ $c->city }}" data-district="{{ $c->district }}" data-subdistrict="{{ $c->subdistrict }}" data-address="{{ $c->address }}" data-postal="{{ $c->postal_code }}">
                                {{ $c->name }} ({{ $c->company_name ?? $c->city }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Request Pickup *</label>
                    <input type="date" name="order_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target Pengiriman (Delivery)</label>
                    <input type="date" name="delivery_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
            </div>

            <!-- Regional Hierarchical Selection Dropdowns for Pickup -->
            <div class="space-y-2 pt-2">
                <label class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center">
                    <i class="fa-solid fa-map-location-dot text-indigo-600 mr-1.5"></i> Wilayah Asal Penjemputan Paket *
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 text-xs bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Provinsi Pickup *</label>
                        <input type="text" onkeyup="filterSelectOptions(this, 'pickup-prov-select')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px] font-medium">
                        <select name="pickup_province" id="pickup-prov-select" onchange="onPickupProvinceChange(this.value)" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                            <option value="">-- Pilih Provinsi --</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kota / Kab Pickup *</label>
                        <input type="text" onkeyup="filterSelectOptions(this, 'pickup-city-select')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px] font-medium">
                        <select name="pickup_city" id="pickup-city-select" onchange="onPickupCityChange(this.value)" required class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-bold text-indigo-700">
                            <option value="">-- Pilih Kota/Kab --</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kecamatan Pickup</label>
                        <input type="text" onkeyup="filterSelectOptions(this, 'pickup-dist-select')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px] font-medium">
                        <select name="pickup_district" id="pickup-dist-select" onchange="onPickupDistrictChange(this.value)" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                            <option value="">-- Pilih Kecamatan --</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kelurahan Pickup</label>
                        <input type="text" onkeyup="filterSelectOptions(this, 'pickup-subdist-select')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px] font-medium">
                        <select name="pickup_subdistrict" id="pickup-subdist-select" onchange="onPickupSubdistrictChange(this.value)" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                            <option value="">-- Pilih Kelurahan --</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kode Pos Pickup</label>
                        <input type="text" name="pickup_postal_code" id="pickup-postal-input" placeholder="12430" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-bold text-indigo-600">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Hub Origin / Gudang Penjemputan *</label>
                    <select name="origin_hub_id" id="originHubSelect" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-indigo-700">
                        <option value="">-- Pilih Hub Gudang Origin --</option>
                        @foreach($hubs as $hub)
                            <option value="{{ $hub->id }}" data-city="{{ $hub->city }}">
                                Hub {{ $hub->name }} ({{ $hub->city }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Layanan Pengiriman *</label>
                    <select name="service_type" id="serviceTypeSelect" onchange="calculateAllOngkir()" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                        <option value="Express" selected>Express (1 Hari Tiba)</option>
                        <option value="Regular">Regular (2-3 Hari)</option>
                        <option value="SameDay">SameDay (Hari Ini Tiba)</option>
                        <option value="Cargo">Cargo (3-5 Hari)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Penjemputan (Pickup Address) *</label>
                    <input type="text" name="pickup_address" id="pickupAddressInput" placeholder="Jl. Sudirman Plaza, Indofood Tower Lt. 23..." required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Catatan Batch DO & Instruksi Pickup (Opsional)</label>
                <input type="text" name="notes" placeholder="Contoh: Titik pickup di Dokumen Center Lantai 2. Pengiriman Massal Kartu Kredit Batch 08-2026" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>
        </div>

        <!-- Dynamic Multi-Destination Recipients Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-2">
                <div>
                    <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider"><i class="fa-solid fa-people-carry-box text-emerald-600 mr-1.5"></i> 2. Daftar Lokasi Penerima & Pilihan Wilayah</h3>
                    <p class="text-[11px] text-slate-400">Pilihan Provinsi, Kota/Kab, Kecamatan, Kelurahan & Kode Pos otomatis terhubung.</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-indigo-600 text-white font-bold text-xs hover:bg-indigo-700 transition shadow-md shadow-indigo-600/20">
                        <i class="fa-solid fa-file-import mr-1"></i> Import Data Excel / CSV
                    </button>
                    <button type="button" onclick="addRecipientRow()" class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 transition shadow-md shadow-emerald-600/20">
                        + Tambah Manual
                    </button>
                </div>
            </div>

            <div class="space-y-4" id="recipientContainer">
                <!-- Recipient Row 1 template will be injected via JS -->
            </div>
        </div>

        <!-- Bottom Summary Card & Submit -->
        <div class="bg-slate-900 text-white p-6 rounded-2xl shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="text-xs font-extrabold text-indigo-400 uppercase tracking-wider flex items-center">
                    <i class="fa-solid fa-receipt text-indigo-400 mr-2 text-sm"></i> Ringkasan Total Tarif Ongkir DO Massal
                </div>
                <div class="text-xs text-slate-300">
                    Total Penerima: <span id="summaryTotalItems" class="font-bold text-white">0 Lokasi</span> • 
                    Total Berat: <span id="summaryTotalWeight" class="font-bold text-white">0 kg</span>
                </div>
            </div>

            <div class="flex items-center space-x-5">
                <div class="text-right">
                    <div class="text-[10px] text-slate-400 uppercase tracking-wider font-bold">Total Estimasi Tarif Ongkir</div>
                    <div class="text-2xl font-black text-emerald-400 font-mono" id="summaryGrandTotalOngkir">Rp 0</div>
                </div>

                <div class="flex items-center space-x-2">
                    <a href="{{ route('delivery-orders.index') }}" class="px-4 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition">Batal</a>
                    <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-xs shadow-lg shadow-indigo-600/40 transition flex items-center">
                        <i class="fa-solid fa-paper-plane mr-2"></i> Simpan & Terbitkan DO
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal Import CSV/Excel -->
<div id="importModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl text-left">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm">Import Data Penerima Bank dari Excel / CSV</h3>
            <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-slate-400">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        
        <form id="csvImportForm" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Berkas CSV / Excel Template *</label>
                <input type="file" name="csv_file" accept=".csv,.txt,.xls,.xlsx" required class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs text-slate-700">
                <p class="text-[10px] text-slate-400 mt-1">Pastikan format file sesuai dengan template CSV yang dapat diunduh di atas.</p>
            </div>

            <div class="pt-2 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30">
                    <i class="fa-solid fa-upload mr-1"></i> Process Import
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let recipientIndex = 0;

    function initPickupProvinces(targetProv = null, targetCity = null, targetDist = null, targetSubdist = null) {
        const provSelect = document.getElementById('pickup-prov-select');
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
                    provSelect.value = provinces[0].name;
                }
                onPickupProvinceChange(provSelect.value, targetCity, targetDist, targetSubdist);
            });
    }

    function onPickupProvinceChange(provName, targetCity = null, targetDist = null, targetSubdist = null) {
        const provSelect = document.getElementById('pickup-prov-select');
        const citySelect = document.getElementById('pickup-city-select');
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
                onPickupCityChange(citySelect.value, targetDist, targetSubdist);
            });
    }

    function autoSelectMatchingHub(city) {
        const hubSelect = document.getElementById('originHubSelect');
        if (!hubSelect || !city) return;

        const lowerCity = city.toLowerCase().replace(/^(kota|kabupaten|kab\.)\s+/i, '').trim();
        let matchedOpt = null;

        Array.from(hubSelect.options).forEach(opt => {
            if (!opt.value) return;
            const hubCity = (opt.dataset.city || opt.text || '').toLowerCase().replace(/^(kota|kabupaten|kab\.)\s+/i, '').trim();
            if (hubCity && (lowerCity.includes(hubCity) || hubCity.includes(lowerCity))) {
                matchedOpt = opt;
            }
        });

        if (matchedOpt) {
            hubSelect.value = matchedOpt.value;
        } else if (hubSelect.options.length > 1 && !hubSelect.value) {
            hubSelect.selectedIndex = 1;
        }
    }

    function onPickupCityChange(cityName, targetDist = null, targetSubdist = null) {
        autoSelectMatchingHub(cityName);
        const citySelect = document.getElementById('pickup-city-select');
        const distSelect = document.getElementById('pickup-dist-select');
        const selectedOpt = Array.from(citySelect.options).find(o => o.value === cityName);
        const cityId = selectedOpt ? selectedOpt.dataset.id : null;

        distSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        calculateAllOngkir();

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
                } else if (districts.length > 0) {
                    distSelect.value = districts[0].name;
                }
                onPickupDistrictChange(distSelect.value, targetSubdist);
            });
    }

    function onPickupDistrictChange(distName, targetSubdist = null) {
        const distSelect = document.getElementById('pickup-dist-select');
        const subdistSelect = document.getElementById('pickup-subdist-select');
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
                } else if (subdistricts.length > 0) {
                    subdistSelect.value = subdistricts[0].name;
                }
                onPickupSubdistrictChange(subdistSelect.value);
            });
    }

    function onPickupSubdistrictChange(subdistName) {
        const subdistSelect = document.getElementById('pickup-subdist-select');
        const postalInput = document.getElementById('pickup-postal-input');
        const selectedOpt = Array.from(subdistSelect.options).find(o => o.value === subdistName);

        if (selectedOpt && selectedOpt.dataset.postal) {
            postalInput.value = selectedOpt.dataset.postal;
        }
    }

    function onCustomerSelectChange(select) {
        const opt = select.options[select.selectedIndex];
        if (opt && opt.value !== '') {
            const addrInput = document.getElementById('pickupAddressInput');
            if (addrInput) addrInput.value = opt.dataset.address || '';

            const postalInput = document.getElementById('pickup-postal-input');
            if (postalInput) postalInput.value = opt.dataset.postal || '';

            const city = opt.dataset.city || '';
            autoSelectMatchingHub(city);

            initPickupProvinces(
                opt.dataset.province || null,
                city || null,
                opt.dataset.district || null,
                opt.dataset.subdistrict || null
            );
        } else {
            initPickupProvinces();
        }
    }

    function addRecipientRow(data = null) {
        const idx = recipientIndex;
        const container = document.getElementById('recipientContainer');
        const num = container.children.length + 1;

        const row = document.createElement('div');
        row.className = 'recipient-row bg-slate-50/90 p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-4';
        row.innerHTML = `
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-indigo-100 text-indigo-800 flex items-center">
                        <i class="fa-solid fa-location-dot mr-1.5 text-indigo-600"></i> Penerima #<span class="row-num">${num}</span>
                    </span>
                    <span class="text-xs text-slate-400 font-semibold">Detail Alamat & Barang</span>
                </div>
                <button type="button" onclick="removeRecipientRow(this)" class="px-3 py-1 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition flex items-center">
                    <i class="fa-solid fa-trash mr-1.5"></i> Hapus Baris
                </button>
            </div>

            <!-- Row Block 1: Recipient & Item Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Penerima *</label>
                    <input type="text" name="items[${idx}][recipient_name]" value="${data ? (data.recipient_name || '') : ''}" required placeholder="Bpk. Anton Wijaya / PT ABC" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">No. HP Penerima *</label>
                    <input type="text" name="items[${idx}][recipient_phone]" value="${data ? (data.recipient_phone || '') : ''}" required placeholder="081299887766" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">No. Ref / PO / Kontrak (Opsional)</label>
                    <input type="text" name="items[${idx}][account_ref]" value="${data ? (data.account_ref || '') : ''}" placeholder="Contoh: PO-2026-001 / REF-99" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama / Jenis Barang *</label>
                    <input type="text" name="items[${idx}][item_name]" value="${data ? (data.item_name || 'Paket / Dokumen') : 'Paket / Dokumen'}" required placeholder="Contoh: Dokumen Kontrak / Sparepart" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                </div>
            </div>

            <!-- Row Block 2: Regional Hierarchical Dropdowns (4-Level) -->
            <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 space-y-2">
                <div class="text-[11px] font-extrabold text-indigo-700 uppercase tracking-wider flex items-center">
                    <i class="fa-solid fa-map-location-dot mr-1"></i> Pilih Hirarki Wilayah Tujuan
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Provinsi *</label>
                        <input type="text" onkeyup="filterSelectOptions(this, 'prov-select-${idx}')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px]">
                        <select name="items[${idx}][recipient_province]" id="prov-select-${idx}" onchange="onProvinceChange(${idx}, this.value)" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800 prov-select-${idx}">
                            <option value="">-- Pilih Provinsi --</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kota / Kabupaten *</label>
                        <input type="text" onkeyup="filterSelectOptions(this, 'city-select-${idx}')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px]">
                        <select name="items[${idx}][recipient_city]" id="city-select-${idx}" onchange="onCityChange(${idx}, this.value)" required class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800 city-select-${idx}">
                            <option value="">-- Pilih Kota/Kab --</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kecamatan *</label>
                        <input type="text" onkeyup="filterSelectOptions(this, 'dist-select-${idx}')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px]">
                        <select name="items[${idx}][recipient_district]" id="dist-select-${idx}" onchange="onDistrictChange(${idx}, this.value)" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800 dist-select-${idx}">
                            <option value="">-- Pilih Kecamatan --</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kelurahan *</label>
                        <input type="text" onkeyup="filterSelectOptions(this, 'subdist-select-${idx}')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px]">
                        <select name="items[${idx}][recipient_subdistrict]" id="subdist-select-${idx}" onchange="onSubdistrictChange(${idx}, this.value)" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800 subdist-select-${idx}">
                            <option value="">-- Pilih Kelurahan --</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kode Pos *</label>
                        <input type="text" name="items[${idx}][recipient_postal_code]" value="${data ? (data.recipient_postal_code || '') : ''}" placeholder="40135" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-bold text-indigo-600 postal-input-${idx}">
                    </div>
                </div>
            </div>

            <!-- Row Block 3: Street Address, Qty, Unit & Weight -->
            <div class="grid grid-cols-1 md:grid-cols-6 gap-3 text-xs">
                <div class="md:col-span-3">
                    <label class="block font-bold text-slate-700 mb-1">Alamat Jalan / Gedung / No. Rumah *</label>
                    <input type="text" name="items[${idx}][recipient_address]" value="${data ? (data.recipient_address || '') : ''}" required placeholder="Jl. Sudirman No. 45, RT 02/RW 05..." class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Qty *</label>
                    <input type="number" name="items[${idx}][qty]" value="${data ? (data.qty || 1) : 1}" min="1" required class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Satuan *</label>
                    <select name="items[${idx}][unit]" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                        <option value="Paket" ${data && data.unit === 'Paket' ? 'selected' : ''}>Paket</option>
                        <option value="Pcs" ${data && data.unit === 'Pcs' ? 'selected' : ''}>Pcs</option>
                        <option value="Dokumen" ${data && data.unit === 'Dokumen' ? 'selected' : ''}>Dokumen</option>
                        <option value="Amplop" ${data && data.unit === 'Amplop' ? 'selected' : ''}>Amplop</option>
                        <option value="Box" ${data && data.unit === 'Box' ? 'selected' : ''}>Box</option>
                        <option value="Kg" ${data && data.unit === 'Kg' ? 'selected' : ''}>Kg</option>
                        <option value="Koli" ${data && data.unit === 'Koli' ? 'selected' : ''}>Koli</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Berat (kg) *</label>
                    <input type="number" step="0.1" name="items[${idx}][weight_kg]" value="${data ? (data.weight_kg || 1.0) : 1.0}" min="0.1" oninput="calculateAllOngkir()" required class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-bold text-indigo-700 weight-input-${idx}">
                </div>
            </div>

            <!-- Per-Item Ongkir Estimation Badge -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-indigo-50/80 p-3 rounded-xl border border-indigo-100 text-xs gap-2">
                <div class="flex items-center space-x-2 text-indigo-900">
                    <i class="fa-solid fa-calculator text-indigo-600"></i>
                    <span class="font-bold">Estimasi Tarif Ongkir Item:</span>
                    <span id="ongkir-badge-${idx}" class="font-black text-indigo-700 font-mono text-sm">Hitung...</span>
                </div>
                <div class="text-[11px] text-indigo-600 font-bold bg-white px-2.5 py-1 rounded-lg border border-indigo-100" id="charged-weight-info-${idx}">
                    Berat Dihitung: 1 kg (Minimal 1 kg)
                </div>
            </div>
        `;

        container.appendChild(row);
        populateProvinces(idx, data);
        recipientIndex++;
        setTimeout(calculateAllOngkir, 300);
    }

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

    function populateProvinces(idx, data = null) {
        const provSelect = document.querySelector(`.prov-select-${idx}`);
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

                if (data && data.recipient_province) {
                    provSelect.value = data.recipient_province;
                } else if (provinces.length > 0) {
                    provSelect.value = provinces[0].name;
                }
                onProvinceChange(idx, provSelect.value, data);
            })
            .catch(err => {
                console.error("Gagal mengambil data provinsi:", err);
            });
    }

    function onProvinceChange(idx, provName, data = null) {
        const provSelect = document.querySelector(`.prov-select-${idx}`);
        const citySelect = document.querySelector(`.city-select-${idx}`);
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

                if (data && data.recipient_city) {
                    citySelect.value = data.recipient_city;
                } else if (cities.length > 0) {
                    citySelect.value = cities[0].name;
                }
                onCityChange(idx, citySelect.value, data);
            });
    }

    function onCityChange(idx, cityName, data = null) {
        const citySelect = document.querySelector(`.city-select-${idx}`);
        const distSelect = document.querySelector(`.dist-select-${idx}`);
        const selectedOpt = Array.from(citySelect.options).find(o => o.value === cityName);
        const cityId = selectedOpt ? selectedOpt.dataset.id : null;

        distSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        calculateAllOngkir();

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

                if (data && data.recipient_district) {
                    distSelect.value = data.recipient_district;
                } else if (districts.length > 0) {
                    distSelect.value = districts[0].name;
                }
                onDistrictChange(idx, distSelect.value, data);
            });
    }

    function onDistrictChange(idx, distName, data = null) {
        const distSelect = document.querySelector(`.dist-select-${idx}`);
        const subdistSelect = document.querySelector(`.subdist-select-${idx}`);
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

                if (data && data.recipient_subdistrict) {
                    subdistSelect.value = data.recipient_subdistrict;
                } else if (subdistricts.length > 0) {
                    subdistSelect.value = subdistricts[0].name;
                }
                onSubdistrictChange(idx, subdistSelect.value, data);
            });
    }

    function onSubdistrictChange(idx, subdistName, data = null) {
        const subdistSelect = document.querySelector(`.subdist-select-${idx}`);
        const postalInput = document.querySelector(`.postal-input-${idx}`);
        const selectedOpt = Array.from(subdistSelect.options).find(o => o.value === subdistName);

        if (selectedOpt && selectedOpt.dataset.postal) {
            postalInput.value = selectedOpt.dataset.postal;
        } else if (data && data.recipient_postal_code) {
            postalInput.value = data.recipient_postal_code;
        }
    }

    function removeRecipientRow(btn) {
        const row = btn.closest('.recipient-row');
        if (document.querySelectorAll('.recipient-row').length > 1) {
            row.remove();
            updateRowNumbers();
            calculateAllOngkir();
        } else {
            alert('Minimal 1 baris penerima harus diisi.');
        }
    }

    function updateRowNumbers() {
        document.querySelectorAll('.recipient-row').forEach((row, i) => {
            row.querySelector('.row-num').textContent = i + 1;
        });
    }

    function calculateAllOngkir() {
        const pickupCity = document.getElementById('pickup-city-select')?.value || '';
        const pickupDist = document.getElementById('pickup-dist-select')?.value || '';
        const pickupSubdist = document.getElementById('pickup-subdist-select')?.value || '';

        const serviceType = document.getElementById('serviceTypeSelect')?.value || 'Express';
        const rows = document.querySelectorAll('.recipient-row');

        let grandTotal = 0;
        let totalWeight = 0;
        let totalItems = rows.length;
        let processedCount = 0;

        if (rows.length === 0) {
            updateSummaryTotals(0, 0, 0);
            return;
        }

        rows.forEach((row, i) => {
            const citySelect = row.querySelector(`.city-select-${i}`) || row.querySelector('select[name*="[recipient_city]"]');
            const distSelect = row.querySelector(`.dist-select-${i}`) || row.querySelector('select[name*="[recipient_district]"]');
            const subdistSelect = row.querySelector(`.subdist-select-${i}`) || row.querySelector('select[name*="[recipient_subdistrict]"]');

            const destCity = citySelect ? citySelect.value : '';
            const destDist = distSelect ? distSelect.value : '';
            const destSubdist = subdistSelect ? subdistSelect.value : '';

            const weightInput = row.querySelector(`.weight-input-${i}`) || row.querySelector('input[name*="[weight_kg]"]');
            const rawWeight = weightInput ? parseFloat(weightInput.value) || 0.2 : 0.2;
            const chargedWeight = Math.max(1, Math.ceil(rawWeight));
            totalWeight += rawWeight;

            const badge = document.getElementById(`ongkir-badge-${i}`);
            const weightInfo = document.getElementById(`charged-weight-info-${i}`);

            if (!pickupCity || !destCity) {
                if (badge) badge.textContent = 'Rp 0 (Lengkapi Wilayah)';
                if (weightInfo) weightInfo.textContent = `Berat Dihitung: ${chargedWeight} kg (Minimal 1 kg)`;
                processedCount++;
                if (processedCount === totalItems) updateSummaryTotals(totalItems, totalWeight, grandTotal);
                return;
            }

            const url = `/tariffs/calculate?origin_city=${encodeURIComponent(pickupCity)}&origin_district=${encodeURIComponent(pickupDist)}&origin_subdistrict=${encodeURIComponent(pickupSubdist)}&destination_city=${encodeURIComponent(destCity)}&destination_district=${encodeURIComponent(destDist)}&destination_subdistrict=${encodeURIComponent(destSubdist)}&service_type=${encodeURIComponent(serviceType)}&weight_kg=${rawWeight}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    const fee = data.shipping_fee || 0;
                    grandTotal += fee;
                    if (badge) badge.textContent = 'Rp ' + fee.toLocaleString('id-ID');
                    if (weightInfo) weightInfo.textContent = `Berat Dihitung: ${chargedWeight} kg (Rp ${ (data.price_per_kg || 20000).toLocaleString('id-ID') }/kg)`;
                    processedCount++;
                    if (processedCount === totalItems) updateSummaryTotals(totalItems, totalWeight, grandTotal);
                })
                .catch(err => {
                    const defaultRate = serviceType === 'Express' ? 20000 : 12000;
                    const fee = defaultRate * chargedWeight;
                    grandTotal += fee;
                    if (badge) badge.textContent = 'Rp ' + fee.toLocaleString('id-ID');
                    if (weightInfo) weightInfo.textContent = `Berat Dihitung: ${chargedWeight} kg`;
                    processedCount++;
                    if (processedCount === totalItems) updateSummaryTotals(totalItems, totalWeight, grandTotal);
                });
        });
    }

    function updateSummaryTotals(items, weight, grandTotal) {
        const summaryItems = document.getElementById('summaryTotalItems');
        const summaryWeight = document.getElementById('summaryTotalWeight');
        const summaryGrand = document.getElementById('summaryGrandTotalOngkir');

        if (summaryItems) summaryItems.textContent = items + ' Lokasi';
        if (summaryWeight) summaryWeight.textContent = weight.toFixed(1) + ' kg';
        if (summaryGrand) summaryGrand.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
    }

    // CSV Import Form AJAX Handler
    document.getElementById('csvImportForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Processing...';

        fetch("{{ route('delivery-orders.import-csv') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(res => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-upload mr-1"></i> Process Import';
            document.getElementById('importModal').classList.add('hidden');

            if (res.success && res.data.length > 0) {
                document.getElementById('recipientContainer').innerHTML = '';
                recipientIndex = 0;

                res.data.forEach(item => {
                    addRecipientRow(item);
                });
                alert(res.message);
                setTimeout(calculateAllOngkir, 500);
            } else {
                alert('Gagal meng-import file CSV. Pastikan format kolom sesuai.');
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-upload mr-1"></i> Process Import';
            alert('Terjadi kesalahan koneksi server saat meng-import CSV.');
        });
    });

    // Initialize 1 default recipient row, pickup provinces & customer auto select if single
    document.addEventListener('DOMContentLoaded', function() {
        addRecipientRow();
        initPickupProvinces();

        const custSel = document.getElementById('customerSelect');
        if (custSel && custSel.options.length > 0) {
            if (custSel.value) {
                onCustomerSelectChange(custSel);
            } else if (custSel.options.length === 2 && custSel.options[0].value === '') {
                custSel.selectedIndex = 1;
                onCustomerSelectChange(custSel);
            }
        }
    });
</script>
@endpush
@endsection
