@extends('layouts.app')

@section('title', 'Edit Customer - ' . $customer->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Edit Data Customer</h2>
            <p class="text-xs text-indigo-600 font-mono font-semibold">{{ $customer->customer_code }}</p>
        </div>
        <a href="{{ route('customers.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-700">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <form action="{{ route('customers.update', $customer->id) }}" method="POST" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe Customer *</label>
                <select name="customer_type" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="individual" {{ $customer->customer_type == 'individual' ? 'selected' : '' }}>Perorangan (Individual)</option>
                    <option value="corporate" {{ $customer->customer_type == 'corporate' ? 'selected' : '' }}>Perusahaan (Corporate)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap Customer *</label>
                <input type="text" name="name" value="{{ old('name', $customer->name) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Perusahaan (Opsional)</label>
                <input type="text" name="company_name" value="{{ old('company_name', $customer->company_name) }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor Telepon / WhatsApp *</label>
                <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Email</label>
            <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
        </div>

        <!-- Regional Hierarchical Selection Dropdowns -->
        <div class="space-y-2 pt-2 border-t border-slate-100">
            <label class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center">
                <i class="fa-solid fa-map-location-dot text-indigo-600 mr-1.5"></i> Alamat Wilayah Customer *
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 text-xs bg-slate-50 p-4 rounded-xl border border-slate-200">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Provinsi *</label>
                    <input type="text" onkeyup="filterSelectOptions(this, 'cust-prov-select')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px] font-medium">
                    <select name="province" id="cust-prov-select" onchange="onCustProvinceChange(this.value)" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                        <option value="">-- Pilih Provinsi --</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kota / Kab *</label>
                    <input type="text" onkeyup="filterSelectOptions(this, 'cust-city-select')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px] font-medium">
                    <select name="city" id="cust-city-select" onchange="onCustCityChange(this.value)" required class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-bold text-indigo-700">
                        <option value="">-- Pilih Kota/Kab --</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kecamatan</label>
                    <input type="text" onkeyup="filterSelectOptions(this, 'cust-dist-select')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px] font-medium">
                    <select name="district" id="cust-dist-select" onchange="onCustDistrictChange(this.value)" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                        <option value="">-- Pilih Kecamatan --</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kelurahan</label>
                    <input type="text" onkeyup="filterSelectOptions(this, 'cust-subdist-select')" placeholder="🔍 Cari..." class="w-full px-2 py-1 mb-1 rounded-lg border border-slate-200 text-[11px] font-medium">
                    <select name="subdistrict" id="cust-subdist-select" onchange="onCustSubdistrictChange(this.value)" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800">
                        <option value="">-- Pilih Kelurahan --</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kode Pos</label>
                    <input type="text" name="postal_code" id="cust-postal-input" value="{{ old('postal_code', $customer->postal_code) }}" placeholder="12910" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-bold text-indigo-600">
                </div>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Jalan / Gedung / No. Bangunan *</label>
            <textarea name="address" rows="3" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">{{ old('address', $customer->address) }}</textarea>
        </div>

        <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
            <a href="{{ route('customers.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</a>
            <button type="submit" class="px-6 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30">
                <i class="fa-solid fa-save mr-1"></i> Simpan Perubahan Customer
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const INITIAL_PROV = "{{ old('province', $customer->province) }}";
    const INITIAL_CITY = "{{ old('city', $customer->city) }}";
    const INITIAL_DIST = "{{ old('district', $customer->district) }}";
    const INITIAL_SUBDIST = "{{ old('subdistrict', $customer->subdistrict) }}";

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

    function initCustProvinces() {
        const provSelect = document.getElementById('cust-prov-select');
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

                if (INITIAL_PROV) {
                    const found = Array.from(provSelect.options).find(o => o.value.toLowerCase().includes(INITIAL_PROV.toLowerCase()));
                    if (found) provSelect.value = found.value;
                } else if (provinces.length > 0) {
                    provSelect.value = provinces[0].name;
                }
                onCustProvinceChange(provSelect.value);
            });
    }

    function onCustProvinceChange(provName) {
        const provSelect = document.getElementById('cust-prov-select');
        const citySelect = document.getElementById('cust-city-select');
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

                if (INITIAL_CITY) {
                    const found = Array.from(citySelect.options).find(o => o.value.toLowerCase().includes(INITIAL_CITY.toLowerCase()));
                    if (found) citySelect.value = found.value;
                } else if (cities.length > 0) {
                    citySelect.value = cities[0].name;
                }
                onCustCityChange(citySelect.value);
            });
    }

    function onCustCityChange(cityName) {
        const citySelect = document.getElementById('cust-city-select');
        const distSelect = document.getElementById('cust-dist-select');
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

                if (INITIAL_DIST) {
                    const found = Array.from(distSelect.options).find(o => o.value.toLowerCase().includes(INITIAL_DIST.toLowerCase()));
                    if (found) distSelect.value = found.value;
                } else if (districts.length > 0) {
                    distSelect.value = districts[0].name;
                }
                onCustDistrictChange(distSelect.value);
            });
    }

    function onCustDistrictChange(distName) {
        const distSelect = document.getElementById('cust-dist-select');
        const subdistSelect = document.getElementById('cust-subdist-select');
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

                if (INITIAL_SUBDIST) {
                    const found = Array.from(subdistSelect.options).find(o => o.value.toLowerCase().includes(INITIAL_SUBDIST.toLowerCase()));
                    if (found) subdistSelect.value = found.value;
                } else if (subdistricts.length > 0) {
                    subdistSelect.value = subdistricts[0].name;
                }
                onCustSubdistrictChange(subdistSelect.value);
            });
    }

    function onCustSubdistrictChange(subdistName) {
        const subdistSelect = document.getElementById('cust-subdist-select');
        const postalInput = document.getElementById('cust-postal-input');
        const selectedOpt = Array.from(subdistSelect.options).find(o => o.value === subdistName);

        if (selectedOpt && selectedOpt.dataset.postal) {
            postalInput.value = selectedOpt.dataset.postal;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        initCustProvinces();
    });
</script>
@endpush
@endsection
