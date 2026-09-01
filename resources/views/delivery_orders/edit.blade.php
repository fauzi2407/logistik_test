@extends('layouts.app')

@section('title', 'Edit Delivery Order ' . $deliveryOrder->do_number)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Action Bar / Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Edit Delivery Order (Surat Jalan Massal)</h2>
            <p class="text-xs text-indigo-600 font-mono font-semibold">{{ $deliveryOrder->do_number }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('delivery-orders.template-csv') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center">
                <i class="fa-solid fa-file-excel text-emerald-600 mr-1.5"></i> Unduh Template CSV
            </a>
            <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition flex items-center">
                <i class="fa-solid fa-file-import mr-1.5"></i> Import Penerima CSV
            </button>
            <a href="{{ route('delivery-orders.show', $deliveryOrder->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs">
                <i class="fa-solid fa-arrow-left mr-1"></i> Batal
            </a>
        </div>
    </div>

    <!-- Main Edit Form -->
    <form action="{{ route('delivery-orders.update', $deliveryOrder->id) }}" method="POST" id="editDoForm" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Step 1: Customer & Header Info -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2 flex items-center">
                <i class="fa-solid fa-building-columns text-indigo-600 mr-1.5"></i> 1. Informasi Customer & Layanan Pengiriman
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Bank / Customer *</label>
                    <select name="customer_id" id="customerSelect" onchange="onCustomerSelectChange(this)" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        <option value="">-- Pilih Customer / Bank --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" 
                                    {{ $deliveryOrder->customer_id == $c->id ? 'selected' : '' }}
                                    data-name="{{ $c->name }}" 
                                    data-phone="{{ $c->phone }}" 
                                    data-province="{{ $c->province }}" 
                                    data-city="{{ $c->city }}" 
                                    data-district="{{ $c->district }}" 
                                    data-subdistrict="{{ $c->subdistrict }}" 
                                    data-address="{{ $c->address }}" 
                                    data-postal="{{ $c->postal_code }}">
                                {{ $c->name }} ({{ $c->company_name ?? $c->city }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Request Pickup *</label>
                    <input type="date" name="order_date" value="{{ old('order_date', $deliveryOrder->order_date->format('Y-m-d')) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target Pengiriman (Delivery)</label>
                    <input type="date" name="delivery_date" value="{{ old('delivery_date', $deliveryOrder->delivery_date ? $deliveryOrder->delivery_date->format('Y-m-d') : '') }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status DO *</label>
                    <select name="status" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                        <option value="draft" {{ $deliveryOrder->status == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending" {{ $deliveryOrder->status == 'pending' ? 'selected' : '' }}>Pending (Menunggu Persetujuan)</option>
                        <option value="approved" {{ $deliveryOrder->status == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="processing" {{ $deliveryOrder->status == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ $deliveryOrder->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $deliveryOrder->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $deliveryOrder->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
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
                        <input type="text" name="pickup_postal_code" id="pickup-postal-input" value="{{ old('pickup_postal_code', $deliveryOrder->pickup_postal_code ?? $deliveryOrder->customer->postal_code ?? '') }}" placeholder="12430" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-bold text-indigo-600">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Hub Origin / Gudang Penjemputan *</label>
                    <select name="origin_hub_id" id="originHubSelect" onchange="this.dataset.userSelected = 'true'" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-indigo-700">
                        <option value="">-- Pilih Hub Gudang Origin --</option>
                        @foreach($hubs as $hub)
                            <option value="{{ $hub->id }}" 
                                    data-city="{{ $hub->city }}"
                                    {{ (old('origin_hub_id', $deliveryOrder->origin_hub_id ?? $deliveryOrder->origin_hub->id ?? '') == $hub->id) ? 'selected' : '' }}>
                                Hub {{ $hub->name }} ({{ $hub->city }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Layanan Pengiriman *</label>
                    <select name="service_type" id="serviceTypeSelect" onchange="calculateAllOngkir()" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                        <option value="Express" {{ ($deliveryOrder->service_type ?? 'Express') == 'Express' ? 'selected' : '' }}>Express (1 Hari Tiba)</option>
                        <option value="Regular" {{ ($deliveryOrder->service_type ?? '') == 'Regular' ? 'selected' : '' }}>Regular (2-3 Hari)</option>
                        <option value="SameDay" {{ ($deliveryOrder->service_type ?? '') == 'SameDay' ? 'selected' : '' }}>SameDay (Hari Ini Tiba)</option>
                        <option value="Cargo" {{ ($deliveryOrder->service_type ?? '') == 'Cargo' ? 'selected' : '' }}>Cargo (3-5 Hari)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Penjemputan (Pickup Address) *</label>
                    <input type="text" name="pickup_address" id="pickupAddressInput" value="{{ old('pickup_address', $deliveryOrder->sender_address) }}" placeholder="Jl. Sudirman Plaza, Indofood Tower Lt. 23..." required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Catatan Batch DO & Instruksi Pickup (Opsional)</label>
                <input type="text" name="notes" value="{{ old('notes', $deliveryOrder->notes) }}" placeholder="Contoh: Titik pickup di Dokumen Center Lantai 2. Pengiriman Massal Kartu Kredit Batch 08-2026" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>
        </div>

        <!-- Step 2: Multi-Recipient Cards -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center">
                        <i class="fa-solid fa-users text-indigo-600 mr-1.5"></i> 2. Daftar Penerima Tujuan & Rincian Paket Barang
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Edit, tambahkan, atau hapus baris penerima paket pengiriman.</p>
                </div>
                <button type="button" onclick="addRecipientRow()" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition flex items-center shrink-0">
                    <i class="fa-solid fa-user-plus mr-1.5"></i> Tambah Penerima
                </button>
            </div>

            <div id="recipientContainer" class="space-y-4">
                <!-- Dynamically populated via JS -->
            </div>
        </div>

        <!-- Step 3: Calculation Summary Bar -->
        <div class="bg-slate-900 text-white p-6 rounded-2xl shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="grid grid-cols-3 gap-6 text-center md:text-left w-full md:w-auto">
                <div>
                    <div class="text-[10px] uppercase font-bold text-slate-400">Total Alamat Tujuan</div>
                    <div class="text-lg font-black text-white" id="summaryTotalItems">0 Lokasi</div>
                </div>
                <div>
                    <div class="text-[10px] uppercase font-bold text-slate-400">Total Weight (Kg)</div>
                    <div class="text-lg font-black text-indigo-400" id="summaryTotalWeight">0.0 kg</div>
                </div>
                <div>
                    <div class="text-[10px] uppercase font-bold text-slate-400">Estimasi Total Ongkir</div>
                    <div class="text-lg font-black text-amber-400 font-mono" id="summaryGrandTotalOngkir">Rp 0</div>
                </div>
            </div>

            <div class="flex items-center space-x-3 w-full md:w-auto justify-end">
                <a href="{{ route('delivery-orders.show', $deliveryOrder->id) }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs shadow-lg shadow-indigo-600/40 transition">
                    <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan Perubahan DO
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Modal Import CSV -->
<div id="importModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm">Import Data Penerima via Berkas CSV</h3>
            <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="csvImportForm" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Pilih Berkas CSV *</label>
                <input type="file" name="csv_file" accept=".csv" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs">
                <p class="text-[11px] text-slate-500">Gunakan template CSV standar agar seluruh kolom terisi otomatis.</p>
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
    const initialItemsData = @json($deliveryOrder->items);

    function autoSelectMatchingHub(city, force = false) {
        const hubSelect = document.getElementById('originHubSelect');
        if (!hubSelect || !city) return;
        if (hubSelect.dataset.userSelected === 'true' && !force) return;

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
            if (addrInput && !addrInput.value) addrInput.value = opt.dataset.address || '';

            const postalInput = document.getElementById('pickup-postal-input');
            if (postalInput && !postalInput.value) postalInput.value = opt.dataset.postal || '';

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
            <input type="hidden" name="items[${idx}][tracking_number]" value="${data ? (data.tracking_number || '') : ''}">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-indigo-100 text-indigo-800 flex items-center">
                        <i class="fa-solid fa-location-dot mr-1.5 text-indigo-600"></i> Penerima #<span class="row-num">${num}</span>
                    </span>
                    ${data && data.tracking_number ? `<span class="px-2.5 py-0.5 rounded bg-indigo-50 font-mono text-indigo-600 font-bold text-xs">${data.tracking_number}</span>` : ''}
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
                    <input type="text" name="items[${idx}][recipient_phone]" value="${data ? (data.recipient_phone || '') : ''}" required placeholder="08123456789" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jenis Barang / Isi Paket *</label>
                    <input type="text" name="items[${idx}][item_name]" value="${data ? (data.item_name || 'Paket / Dokumen') : 'Paket / Dokumen'}" required placeholder="Paket / Dokumen" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-bold text-slate-800">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">No. Ref / PO / Kontrak (Opsional)</label>
                    <input type="text" name="items[${idx}][account_ref]" value="${data ? (data.account_ref || '') : ''}" placeholder="PO-8812 / Ref Kontrak" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                </div>
            </div>

            <!-- Row Block 2: 4-Level Regional Hierarchy for Recipient -->
            <div class="space-y-1.5">
                <label class="block text-[11px] font-extrabold text-slate-600 uppercase tracking-wider">Hirarki Wilayah Tujuan Penerima Paket *</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-600 mb-0.5">Provinsi Tujuan</label>
                        <input type="text" onkeyup="filterSelectOptions(this, 'prov-select-${idx}')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px] font-medium">
                        <select name="items[${idx}][recipient_province]" class="prov-select-${idx} w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800" onchange="onRowProvinceChange(${idx}, this.value)">
                            <option value="">-- Pilih Provinsi --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-0.5">Kota / Kab Tujuan *</label>
                        <input type="text" onkeyup="filterSelectOptions(this, 'city-select-${idx}')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px] font-medium">
                        <select name="items[${idx}][recipient_city]" class="city-select-${idx} w-full px-2.5 py-2 rounded-xl border border-slate-300 font-bold text-indigo-700" onchange="onRowCityChange(${idx}, this.value)" required>
                            <option value="">-- Pilih Kota/Kab --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 mb-0.5">Kecamatan Tujuan</label>
                        <input type="text" onkeyup="filterSelectOptions(this, 'dist-select-${idx}')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px] font-medium">
                        <select name="items[${idx}][recipient_district]" class="dist-select-${idx} w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800" onchange="onRowDistrictChange(${idx}, this.value)">
                            <option value="">-- Pilih Kecamatan --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 mb-0.5">Kelurahan Tujuan</label>
                        <input type="text" onkeyup="filterSelectOptions(this, 'subdist-select-${idx}')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px] font-medium">
                        <select name="items[${idx}][recipient_subdistrict]" class="subdist-select-${idx} w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800" onchange="onSubdistrictChange(${idx}, this.value)">
                            <option value="">-- Pilih Kelurahan --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 mb-0.5">Kode Pos</label>
                        <input type="text" name="items[${idx}][recipient_postal_code]" value="${data ? (data.recipient_postal_code || '') : ''}" class="postal-input-${idx} w-full px-2.5 py-2 rounded-xl border border-slate-300 font-bold text-indigo-600" placeholder="Kode Pos">
                    </div>
                </div>
            </div>

            <!-- Row Block 3: Street Address, Qty, Unit, Weight & Tariff Badge -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 text-xs items-end">
                <div class="md:col-span-4">
                    <label class="block font-bold text-slate-700 mb-1">Alamat Lengkap Penerima (Jalan, RT/RW, Patokan) *</label>
                    <input type="text" name="items[${idx}][recipient_address]" value="${data ? (data.recipient_address || '') : ''}" required placeholder="Jl. Merdeka No. 45 RT 02/05..." class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                </div>
                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Satuan *</label>
                    <select name="items[${idx}][unit]" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-bold text-slate-800">
                        <option value="Paket" ${data && data.unit === 'Paket' ? 'selected' : ''}>Paket</option>
                        <option value="Pcs" ${data && data.unit === 'Pcs' ? 'selected' : ''}>Pcs</option>
                        <option value="Dokumen" ${data && data.unit === 'Dokumen' ? 'selected' : ''}>Dokumen</option>
                        <option value="Amplop" ${data && data.unit === 'Amplop' ? 'selected' : ''}>Amplop</option>
                        <option value="Box" ${data && data.unit === 'Box' ? 'selected' : ''}>Box</option>
                        <option value="Kg" ${data && data.unit === 'Kg' ? 'selected' : ''}>Kg</option>
                        <option value="Koli" ${data && data.unit === 'Koli' ? 'selected' : ''}>Koli</option>
                    </select>
                </div>
                <div class="md:col-span-1">
                    <label class="block font-bold text-slate-700 mb-1">Qty *</label>
                    <input type="number" name="items[${idx}][qty]" value="${data ? (data.qty || 1) : 1}" min="1" required class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                </div>
                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Berat Total (Kg) *</label>
                    <input type="number" step="0.1" name="items[${idx}][weight_kg]" value="${data ? (data.weight_kg || 0.2) : 0.2}" min="0.1" onchange="calculateAllOngkir()" onkeyup="calculateAllOngkir()" required class="weight-input-${idx} w-full px-3 py-2 rounded-xl border border-slate-300 font-bold text-indigo-600">
                </div>
                <div class="md:col-span-3">
                    <div class="bg-indigo-50/80 p-2.5 rounded-xl border border-indigo-100 text-right">
                        <div class="text-[10px] font-bold uppercase text-slate-400">Perkiraan Ongkir (Item ini)</div>
                        <div class="text-sm font-black text-indigo-700 font-mono" id="ongkir-badge-${idx}">Rp 0</div>
                        <div class="text-[9px] font-bold text-slate-500" id="charged-weight-info-${idx}">Memuat tarif...</div>
                    </div>
                </div>
            </div>
        `;

        container.appendChild(row);
        recipientIndex++;

        initRowRegionalProvinces(
            idx,
            data ? data.recipient_province : null,
            data ? data.recipient_city : null,
            data ? data.recipient_district : null,
            data ? data.recipient_subdistrict : null
        );

        updateRowNumbers();
    }

    function initRowRegionalProvinces(idx, targetProv = null, targetCity = null, targetDist = null, targetSubdist = null) {
        const provSelect = document.querySelector(`.prov-select-${idx}`);
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
                onRowProvinceChange(idx, provSelect.value, targetCity, targetDist, targetSubdist);
            });
    }

    function onRowProvinceChange(idx, provName, targetCity = null, targetDist = null, targetSubdist = null) {
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

                if (targetCity) {
                    const found = Array.from(citySelect.options).find(o => o.value.toLowerCase().includes(targetCity.toLowerCase()));
                    if (found) citySelect.value = found.value;
                } else if (cities.length > 0) {
                    citySelect.value = cities[0].name;
                }
                onRowCityChange(idx, citySelect.value, targetDist, targetSubdist);
            });
    }

    function onRowCityChange(idx, cityName, targetDist = null, targetSubdist = null) {
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

                if (targetDist) {
                    const found = Array.from(distSelect.options).find(o => o.value.toLowerCase().includes(targetDist.toLowerCase()));
                    if (found) distSelect.value = found.value;
                } else if (districts.length > 0) {
                    distSelect.value = districts[0].name;
                }
                onRowDistrictChange(idx, distSelect.value, targetSubdist);
            });
    }

    function onRowDistrictChange(idx, distName, targetSubdist = null) {
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

                if (targetSubdist) {
                    const found = Array.from(subdistSelect.options).find(o => o.value.toLowerCase().includes(targetSubdist.toLowerCase()));
                    if (found) subdistSelect.value = found.value;
                } else if (subdistricts.length > 0) {
                    subdistSelect.value = subdistricts[0].name;
                }
                onSubdistrictChange(idx, subdistSelect.value);
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
        calculateAllOngkir();
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

    function filterSelectOptions(input, selectId) {
        const filter = input.value.toLowerCase();
        const select = document.getElementById(selectId) || document.querySelector('.' + selectId);
        if (!select) return;
        const options = select.options;
        for (let i = 0; i < options.length; i++) {
            const txt = options[i].text.toLowerCase();
            if (txt.includes(filter) || options[i].value === "") {
                options[i].style.display = "";
            } else {
                options[i].style.display = "none";
            }
        }
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

    // Populate existing items when page loads
    document.addEventListener('DOMContentLoaded', function() {
        const hubSel = document.getElementById('originHubSelect');
        if (hubSel && hubSel.value) {
            hubSel.dataset.userSelected = 'true';
        }

        if (initialItemsData && initialItemsData.length > 0) {
            initialItemsData.forEach(item => {
                addRecipientRow(item);
            });
        } else {
            addRecipientRow();
        }

        const custSel = document.getElementById('customerSelect');
        if (custSel && custSel.value) {
            onCustomerSelectChange(custSel);
        } else {
            initPickupProvinces();
        }

        setTimeout(calculateAllOngkir, 800);
    });
</script>
@endpush
@endsection
