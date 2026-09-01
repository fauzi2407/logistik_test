@extends('layouts.app')

@section('title', 'Master Tarif & Cek Ongkir')

@section('content')
<div class="space-y-6">
    <!-- Action Bar -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Master Tarif & Kalkulator Ongkir</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola penetapan tarif ongkir per kg antar kota, kecamatan & kelurahan lengkap dengan simulasi kalkulator.</p>
        </div>
        <button onclick="document.getElementById('newTariffModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition flex items-center">
            <i class="fa-solid fa-plus mr-1.5"></i> Tambah Master Tarif
        </button>
    </div>

    <!-- Live Tariff Calculator Tool Widget -->
    <div class="p-6 rounded-2xl bg-gradient-to-br from-indigo-950 via-slate-900 to-slate-950 text-white shadow-xl space-y-4">
        <h3 class="text-sm font-extrabold uppercase tracking-wider text-indigo-300 flex items-center">
            <i class="fa-solid fa-calculator mr-2"></i> Simulasi Kalkulator Hitung Ongkir
        </h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-xs font-semibold">
            <!-- Origin Group -->
            <div class="bg-slate-900/80 p-3.5 rounded-xl border border-slate-800 space-y-2">
                <div class="text-[11px] font-extrabold text-indigo-400 uppercase tracking-wider flex items-center">
                    <i class="fa-solid fa-location-dot mr-1"></i> Wilayah Asal (Origin)
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <div>
                        <label class="block text-slate-400 mb-1 text-[10px]">Provinsi</label>
                        <select id="calcOriginProv" onchange="onCalcOriginProvChange(this.value)" class="w-full px-2 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-white text-[11px]">
                            <option value="">-- Pilih --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1 text-[10px]">Kota / Kab *</label>
                        <select id="calcOrigin" onchange="onCalcOriginCityChange(this.value)" class="w-full px-2 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-indigo-300 font-bold text-[11px]">
                            <option value="">-- Pilih Kota/Kab --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1 text-[10px]">Kecamatan</label>
                        <select id="calcOriginDist" onchange="onCalcOriginDistChange(this.value)" class="w-full px-2 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-white text-[11px]">
                            <option value="">-- Pilih Kecamatan --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1 text-[10px]">Kelurahan</label>
                        <select id="calcOriginSubdist" class="w-full px-2 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-white text-[11px]">
                            <option value="">-- Pilih Kelurahan --</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Destination Group -->
            <div class="bg-slate-900/80 p-3.5 rounded-xl border border-slate-800 space-y-2">
                <div class="text-[11px] font-extrabold text-indigo-400 uppercase tracking-wider flex items-center">
                    <i class="fa-solid fa-paper-plane mr-1"></i> Wilayah Tujuan (Destination)
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <div>
                        <label class="block text-slate-400 mb-1 text-[10px]">Provinsi</label>
                        <select id="calcDestProv" onchange="onCalcDestProvChange(this.value)" class="w-full px-2 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-white text-[11px]">
                            <option value="">-- Pilih Provinsi --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1 text-[10px]">Kota / Kab *</label>
                        <select id="calcDest" onchange="onCalcDestCityChange(this.value)" class="w-full px-2 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-indigo-300 font-bold text-[11px]">
                            <option value="">-- Pilih Kota/Kab --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1 text-[10px]">Kecamatan</label>
                        <select id="calcDestDist" onchange="onCalcDestDistChange(this.value)" class="w-full px-2 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-white text-[11px]">
                            <option value="">-- Pilih Kecamatan --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1 text-[10px]">Kelurahan</label>
                        <select id="calcDestSubdist" class="w-full px-2 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-white text-[11px]">
                            <option value="">-- Pilih Kelurahan --</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-semibold pt-2">
            <div class="flex items-center space-x-3">
                <div class="w-1/2">
                    <label class="block text-slate-400 mb-1">Berat Paket (kg)</label>
                    <input type="number" id="calcWeight" value="1" min="0.1" step="0.1" class="w-full px-3 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-white font-bold">
                </div>
                <div class="w-1/2">
                    <label class="block text-slate-400 mb-1">Jenis Layanan</label>
                    <select id="calcService" class="w-full px-3 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-white font-bold">
                        <option value="Regular">Regular</option>
                        <option value="Express" selected>Express</option>
                        <option value="SameDay">SameDay</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-4">
                <button onclick="runCalculator()" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 font-bold text-xs shadow-lg shadow-indigo-600/40 transition flex items-center">
                    <i class="fa-solid fa-calculator mr-1.5"></i> Hitung Ongkir Sekarang
                </button>
                <div id="calcResult" class="text-right text-xs">
                    <span class="text-slate-400">Estimasi Ongkir:</span>
                    <span class="text-lg font-black text-emerald-400 ml-2" id="calcResultFee">Rp 20.000</span>
                    <span class="text-indigo-300 ml-2" id="calcResultEst">(1 Hari)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Master Tariffs Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-table-list text-indigo-600 mr-2"></i> Daftar Master Tarif Pengiriman
            </h3>
            <form method="GET" action="{{ route('tariffs.index') }}" class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3 w-full">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Filter Origin 4-Level -->
                    <div class="space-y-1.5">
                        <div class="font-extrabold text-indigo-700 uppercase tracking-wider text-[11px] flex items-center">
                            <i class="fa-solid fa-location-dot mr-1"></i> Filter Wilayah Asal (Origin)
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 mb-0.5">Provinsi</label>
                                <select name="origin_province" id="filterProvOrig" onchange="onFilterOrigProvChange(this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-semibold text-slate-800 text-[11px]">
                                    <option value="">-- Semua --</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 mb-0.5">Kota / Kab</label>
                                <select name="origin_city" id="filterCityOrig" onchange="onFilterOrigCityChange(this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-semibold text-slate-800 text-[11px]">
                                    <option value="">-- Semua --</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 mb-0.5">Kecamatan</label>
                                <select name="origin_district" id="filterDistOrig" onchange="onFilterOrigDistChange(this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-semibold text-slate-800 text-[11px]">
                                    <option value="">-- Semua --</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 mb-0.5">Kelurahan</label>
                                <select name="origin_subdistrict" id="filterSubdistOrig" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-semibold text-slate-800 text-[11px]">
                                    <option value="">-- Semua --</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Destination 4-Level -->
                    <div class="space-y-1.5">
                        <div class="font-extrabold text-indigo-700 uppercase tracking-wider text-[11px] flex items-center">
                            <i class="fa-solid fa-paper-plane mr-1"></i> Filter Wilayah Tujuan (Destination)
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 mb-0.5">Provinsi</label>
                                <select name="destination_province" id="filterProvDest" onchange="onFilterDestProvChange(this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-semibold text-slate-800 text-[11px]">
                                    <option value="">-- Semua --</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 mb-0.5">Kota / Kab</label>
                                <select name="destination_city" id="filterCityDest" onchange="onFilterDestCityChange(this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-semibold text-slate-800 text-[11px]">
                                    <option value="">-- Semua --</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 mb-0.5">Kecamatan</label>
                                <select name="destination_district" id="filterDistDest" onchange="onFilterDestDistChange(this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-semibold text-slate-800 text-[11px]">
                                    <option value="">-- Semua --</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 mb-0.5">Kelurahan</label>
                                <select name="destination_subdistrict" id="filterSubdistDest" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-semibold text-slate-800 text-[11px]">
                                    <option value="">-- Semua --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-slate-200 pt-3">
                    <div class="flex items-center space-x-2 w-full sm:w-auto">
                        <select name="service_type" class="px-2.5 py-1.5 rounded-lg border border-slate-300 font-semibold text-slate-800 text-[11px]">
                            <option value="">-- Semua Layanan --</option>
                            <option value="Regular" {{ ($serviceType ?? '') == 'Regular' ? 'selected' : '' }}>Regular</option>
                            <option value="Express" {{ ($serviceType ?? '') == 'Express' ? 'selected' : '' }}>Express</option>
                            <option value="SameDay" {{ ($serviceType ?? '') == 'SameDay' ? 'selected' : '' }}>SameDay</option>
                        </select>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="🔍 Kata Kunci..." class="px-3 py-1.5 rounded-lg border border-slate-300 text-[11px] font-semibold text-slate-800 w-48">
                    </div>
                    <div class="flex items-center space-x-2">
                        <button type="submit" class="px-4 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition">
                            Terapkan Filter Tarif
                        </button>
                        <a href="{{ route('tariffs.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition">
                            Reset Filter
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-4">Kota / Prov Asal</th>
                        <th class="p-4">Kec. & Kel. Asal</th>
                        <th class="p-4">Kota / Prov Tujuan</th>
                        <th class="p-4">Kec. & Kel. Tujuan</th>
                        <th class="p-4">Jenis Layanan</th>
                        <th class="p-4">Tarif per KG</th>
                        <th class="p-4">Estimasi Sampai</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($tariffs as $t)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4">
                                <div class="font-bold text-slate-900">{{ $t->origin_city }}</div>
                                <div class="text-[11px] text-slate-400 font-semibold">{{ $t->origin_province ?? '-' }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-semibold text-slate-800"><i class="fa-solid fa-map-pin text-indigo-500 mr-1 text-[10px]"></i> Kec: {{ $t->origin_district ?? 'Semua Kec.' }}</div>
                                <div class="text-[11px] text-slate-500"><i class="fa-solid fa-location-dot text-slate-400 mr-1 text-[10px]"></i> Kel: {{ $t->origin_subdistrict ?? 'Semua Kel.' }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-indigo-600">{{ $t->destination_city }}</div>
                                <div class="text-[11px] text-slate-400 font-semibold">{{ $t->destination_province ?? '-' }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-semibold text-indigo-900"><i class="fa-solid fa-map-pin text-indigo-500 mr-1 text-[10px]"></i> Kec: {{ $t->destination_district ?? 'Semua Kec.' }}</div>
                                <div class="text-[11px] text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded-full inline-block mt-0.5"><i class="fa-solid fa-location-dot text-indigo-500 mr-1 text-[10px]"></i> Kel: {{ $t->destination_subdistrict ?? 'Semua Kel.' }}</div>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-0.5 rounded bg-slate-100 font-bold text-slate-700">{{ $t->service_type }}</span>
                            </td>
                            <td class="p-4 font-black text-emerald-600 text-sm">
                                Rp {{ number_format($t->price_per_kg, 0, ',', '.') }} / kg
                            </td>
                            <td class="p-4 text-slate-600 font-semibold">{{ $t->estimated_days }}</td>
                            <td class="p-4 text-right space-x-1">
                                <button onclick="document.getElementById('editTariffModal_{{ $t->id }}').classList.remove('hidden')" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg font-bold" title="Edit Tarif">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('tariffs.destroy', $t->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus tarif ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal per Tariff -->
                        <div id="editTariffModal_{{ $t->id }}" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
                            <div class="bg-white rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl text-left">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                    <h3 class="font-extrabold text-slate-900 text-sm">Edit Master Tarif</h3>
                                    <button onclick="document.getElementById('editTariffModal_{{ $t->id }}').classList.add('hidden')" class="text-slate-400">
                                        <i class="fa-solid fa-xmark text-lg"></i>
                                    </button>
                                </div>
                                <form action="{{ route('tariffs.update', $t->id) }}" method="POST" class="space-y-4">
                                    @csrf
                                    @method('PUT')

                                    <div class="space-y-2 p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                                        <label class="block font-extrabold text-slate-800 uppercase tracking-wider">Wilayah Asal (Origin) *</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-700 mb-1">Provinsi</label>
                                                <select name="origin_province" id="editProvOrig_{{ $t->id }}" onchange="onEditOrigProvChange({{ $t->id }}, this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-semibold text-slate-800">
                                                    <option value="{{ $t->origin_province }}">{{ $t->origin_province ?? '-- Pilih --' }}</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-700 mb-1">Kota / Kab *</label>
                                                <select name="origin_city" id="editCityOrig_{{ $t->id }}" onchange="onEditOrigCityChange({{ $t->id }}, this.value)" required class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-bold text-indigo-700">
                                                    <option value="{{ $t->origin_city }}">{{ $t->origin_city }}</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-700 mb-1">Kecamatan</label>
                                                <select name="origin_district" id="editDistOrig_{{ $t->id }}" onchange="onEditOrigDistChange({{ $t->id }}, this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-semibold text-slate-800">
                                                    <option value="{{ $t->origin_district }}">{{ $t->origin_district ?? '-- Pilih --' }}</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-700 mb-1">Kelurahan</label>
                                                <select name="origin_subdistrict" id="editSubdistOrig_{{ $t->id }}" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-semibold text-slate-800">
                                                    <option value="{{ $t->origin_subdistrict }}">{{ $t->origin_subdistrict ?? '-- Pilih --' }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-2 p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                                        <label class="block font-extrabold text-slate-800 uppercase tracking-wider">Wilayah Tujuan (Destination) *</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-700 mb-1">Provinsi</label>
                                                <select name="destination_province" id="editProvDest_{{ $t->id }}" onchange="onEditDestProvChange({{ $t->id }}, this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-semibold text-slate-800">
                                                    <option value="{{ $t->destination_province }}">{{ $t->destination_province ?? '-- Pilih --' }}</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-700 mb-1">Kota / Kab *</label>
                                                <select name="destination_city" id="editCityDest_{{ $t->id }}" onchange="onEditDestCityChange({{ $t->id }}, this.value)" required class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-bold text-indigo-700">
                                                    <option value="{{ $t->destination_city }}">{{ $t->destination_city }}</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-700 mb-1">Kecamatan</label>
                                                <select name="destination_district" id="editDistDest_{{ $t->id }}" onchange="onEditDestDistChange({{ $t->id }}, this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-semibold text-slate-800">
                                                    <option value="{{ $t->destination_district }}">{{ $t->destination_district ?? '-- Pilih --' }}</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-700 mb-1">Kelurahan</label>
                                                <select name="destination_subdistrict" id="editSubdistDest_{{ $t->id }}" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-semibold text-slate-800">
                                                    <option value="{{ $t->destination_subdistrict }}">{{ $t->destination_subdistrict ?? '-- Pilih --' }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Layanan *</label>
                                            <select name="service_type" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                                                <option value="Regular" {{ $t->service_type == 'Regular' ? 'selected' : '' }}>Regular</option>
                                                <option value="Express" {{ $t->service_type == 'Express' ? 'selected' : '' }}>Express</option>
                                                <option value="SameDay" {{ $t->service_type == 'SameDay' ? 'selected' : '' }}>SameDay</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Harga per KG (Rp) *</label>
                                            <input type="number" name="price_per_kg" value="{{ $t->price_per_kg }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Estimasi Hari Sampai *</label>
                                        <input type="text" name="estimated_days" value="{{ $t->estimated_days }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                                    </div>
                                    <input type="hidden" name="min_weight_kg" value="1.0">

                                    <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                                        <button type="button" onclick="document.getElementById('editTariffModal_{{ $t->id }}').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs">Simpan Perubahan Tarif</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400 text-xs font-semibold">
                                Tidak ada data master tarif ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tariffs->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $tariffs->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Create Master Tarif -->
<div id="newTariffModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm">Tambah Master Tarif Baru</h3>
            <button onclick="document.getElementById('newTariffModal').classList.add('hidden')" class="text-slate-400">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form action="{{ route('tariffs.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Origin Regional Selection -->
            <div class="space-y-2 p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                <label class="block font-extrabold text-slate-800 uppercase tracking-wider">Wilayah Asal (Origin) *</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-700 mb-1">Provinsi Asal *</label>
                        <select name="origin_province" id="newProvOrig" onchange="onNewOrigProvChange(this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-semibold text-slate-800">
                            <option value="">-- Pilih --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-700 mb-1">Kota / Kab Asal *</label>
                        <select name="origin_city" id="newCityOrig" onchange="onNewOrigCityChange(this.value)" required class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-bold text-indigo-700">
                            <option value="">-- Pilih --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-700 mb-1">Kecamatan Asal</label>
                        <select name="origin_district" id="newDistOrig" onchange="onNewOrigDistChange(this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-semibold text-slate-800">
                            <option value="">-- Pilih --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-700 mb-1">Kelurahan Asal</label>
                        <select name="origin_subdistrict" id="newSubdistOrig" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-semibold text-slate-800">
                            <option value="">-- Pilih --</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Destination Regional Selection -->
            <div class="space-y-2 p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                <label class="block font-extrabold text-slate-800 uppercase tracking-wider">Wilayah Tujuan (Destination) *</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-700 mb-1">Provinsi Tujuan *</label>
                        <select name="destination_province" id="newProvDest" onchange="onNewDestProvChange(this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-semibold text-slate-800">
                            <option value="">-- Pilih --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-700 mb-1">Kota / Kab Tujuan *</label>
                        <select name="destination_city" id="newCityDest" onchange="onNewDestCityChange(this.value)" required class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-bold text-indigo-700">
                            <option value="">-- Pilih --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-700 mb-1">Kecamatan Tujuan</label>
                        <select name="destination_district" id="newDistDest" onchange="onNewDestDistChange(this.value)" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-semibold text-slate-800">
                            <option value="">-- Pilih --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-700 mb-1">Kelurahan Tujuan</label>
                        <select name="destination_subdistrict" id="newSubdistDest" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 font-semibold text-slate-800">
                            <option value="">-- Pilih --</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Layanan *</label>
                    <select name="service_type" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        <option value="Regular">Regular</option>
                        <option value="Express" selected>Express</option>
                        <option value="SameDay">SameDay</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Harga per KG (Rp) *</label>
                    <input type="number" name="price_per_kg" required placeholder="20000" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Estimasi Hari Sampai *</label>
                <input type="text" name="estimated_days" value="1-2 Hari" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>
            <input type="hidden" name="min_weight_kg" value="1.0">

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('newTariffModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs">Simpan Tarif</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let globalProvinces = [];

    function loadProvinces() {
        fetch("{{ route('api.provinces') }}")
            .then(res => res.json())
            .then(provinces => {
                globalProvinces = provinces;

                populateProvSelect('calcOriginProv', provinces);
                populateProvSelect('calcDestProv', provinces);

                populateProvSelect('filterProvOrig', provinces);
                populateProvSelect('filterProvDest', provinces);

                populateProvSelect('newProvOrig', provinces);
                populateProvSelect('newProvDest', provinces);

                const curOrigProv = "{{ $originProvince ?? '' }}";
                const curOrigCity = "{{ $originCity ?? '' }}";
                const curOrigDist = "{{ $originDistrict ?? '' }}";
                const curOrigSubdist = "{{ $originSubdistrict ?? '' }}";

                if (curOrigProv) {
                    document.getElementById('filterProvOrig').value = curOrigProv;
                    fetchCitiesForProv(curOrigProv, 'filterCityOrig', curOrigCity, () => {
                        if (curOrigCity) {
                            fetchDistrictsForCity('filterCityOrig', 'filterDistOrig', curOrigDist, () => {
                                if (curOrigDist) {
                                    fetchSubdistrictsForDistrict('filterDistOrig', 'filterSubdistOrig', curOrigSubdist);
                                }
                            });
                        }
                    });
                }

                const curDestProv = "{{ $destProvince ?? '' }}";
                const curDestCity = "{{ $destCity ?? '' }}";
                const curDestDist = "{{ $destDistrict ?? '' }}";
                const curDestSubdist = "{{ $destSubdistrict ?? '' }}";

                if (curDestProv) {
                    document.getElementById('filterProvDest').value = curDestProv;
                    fetchCitiesForProv(curDestProv, 'filterCityDest', curDestCity, () => {
                        if (curDestCity) {
                            fetchDistrictsForCity('filterCityDest', 'filterDistDest', curDestDist, () => {
                                if (curDestDist) {
                                    fetchSubdistrictsForDistrict('filterDistDest', 'filterSubdistDest', curDestSubdist);
                                }
                            });
                        }
                    });
                }

                if (provinces.length > 0) {
                    const dki = provinces.find(p => p.name.toLowerCase().includes('jakarta')) || provinces[0];
                    const jabar = provinces.find(p => p.name.toLowerCase().includes('jawa barat')) || provinces[0];
                    
                    document.getElementById('calcOriginProv').value = dki.name;
                    onCalcOriginProvChange(dki.name, 'Jakarta Selatan');

                    document.getElementById('calcDestProv').value = jabar.name;
                    onCalcDestProvChange(jabar.name, 'Kota Bandung');

                    document.getElementById('newProvOrig').value = dki.name;
                    onNewOrigProvChange(dki.name);

                    document.getElementById('newProvDest').value = jabar.name;
                    onNewDestProvChange(jabar.name);
                }
            });
    }

    function populateProvSelect(selectId, provinces) {
        const sel = document.getElementById(selectId);
        if (!sel) return;
        sel.innerHTML = '<option value="">-- Semua --</option>';
        provinces.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.name;
            opt.dataset.id = p.id;
            opt.textContent = p.name;
            sel.appendChild(opt);
        });
    }

    function fetchCitiesForProv(provName, citySelectId, targetCity = null, callback = null) {
        const citySel = document.getElementById(citySelectId);
        if (!citySel) return;
        citySel.innerHTML = '<option value="">-- Semua --</option>';

        const pObj = globalProvinces.find(p => p.name === provName);
        if (!pObj) return;

        fetch(`/api/cities/${pObj.id}`)
            .then(res => res.json())
            .then(cities => {
                citySel.innerHTML = '<option value="">-- Semua --</option>';
                cities.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.name;
                    opt.dataset.id = c.id;
                    opt.textContent = c.name;
                    citySel.appendChild(opt);
                });

                if (targetCity) {
                    const found = Array.from(citySel.options).find(o => o.value.toLowerCase().includes(targetCity.toLowerCase()));
                    if (found) citySel.value = found.value;
                }
                if (callback) callback(citySel.value);
            });
    }

    function fetchDistrictsForCity(citySelId, distSelId, targetDist = null, callback = null) {
        const citySel = document.getElementById(citySelId);
        const distSel = document.getElementById(distSelId);
        if (!citySel || !distSel) return;
        distSel.innerHTML = '<option value="">-- Semua --</option>';

        const selectedOpt = Array.from(citySel.options).find(o => o.value === citySel.value);
        const cityId = selectedOpt ? selectedOpt.dataset.id : null;
        if (!cityId) return;

        fetch(`/api/districts/${cityId}`)
            .then(res => res.json())
            .then(districts => {
                distSel.innerHTML = '<option value="">-- Semua --</option>';
                districts.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.name;
                    opt.dataset.id = d.id;
                    opt.textContent = d.name;
                    distSel.appendChild(opt);
                });

                if (targetDist) {
                    const found = Array.from(distSel.options).find(o => o.value.toLowerCase().includes(targetDist.toLowerCase()));
                    if (found) distSel.value = found.value;
                }
                if (callback) callback(distSel.value);
            });
    }

    function fetchSubdistrictsForDistrict(distSelId, subdistSelId, targetSubdist = null) {
        const distSel = document.getElementById(distSelId);
        const subdistSel = document.getElementById(subdistSelId);
        if (!distSel || !subdistSel) return;
        subdistSel.innerHTML = '<option value="">-- Semua --</option>';

        const selectedOpt = Array.from(distSel.options).find(o => o.value === distSel.value);
        const distId = selectedOpt ? selectedOpt.dataset.id : null;
        if (!distId) return;

        fetch(`/api/subdistricts/${distId}`)
            .then(res => res.json())
            .then(subdistricts => {
                subdistSel.innerHTML = '<option value="">-- Semua --</option>';
                subdistricts.forEach(sd => {
                    const opt = document.createElement('option');
                    opt.value = sd.name;
                    opt.textContent = sd.name;
                    subdistSel.appendChild(opt);
                });

                if (targetSubdist) {
                    const found = Array.from(subdistSel.options).find(o => o.value.toLowerCase().includes(targetSubdist.toLowerCase()));
                    if (found) subdistSel.value = found.value;
                }
            });
    }

    // Filter Panel Handlers
    function onFilterOrigProvChange(provName) {
        fetchCitiesForProv(provName, 'filterCityOrig', null, () => {
            fetchDistrictsForCity('filterCityOrig', 'filterDistOrig', null, () => {
                fetchSubdistrictsForDistrict('filterDistOrig', 'filterSubdistOrig');
            });
        });
    }

    function onFilterOrigCityChange(cityName) {
        fetchDistrictsForCity('filterCityOrig', 'filterDistOrig', null, () => {
            fetchSubdistrictsForDistrict('filterDistOrig', 'filterSubdistOrig');
        });
    }

    function onFilterOrigDistChange(distName) {
        fetchSubdistrictsForDistrict('filterDistOrig', 'filterSubdistOrig');
    }

    function onFilterDestProvChange(provName) {
        fetchCitiesForProv(provName, 'filterCityDest', null, () => {
            fetchDistrictsForCity('filterCityDest', 'filterDistDest', null, () => {
                fetchSubdistrictsForDistrict('filterDistDest', 'filterSubdistDest');
            });
        });
    }

    function onFilterDestCityChange(cityName) {
        fetchDistrictsForCity('filterCityDest', 'filterDistDest', null, () => {
            fetchSubdistrictsForDistrict('filterDistDest', 'filterSubdistDest');
        });
    }

    function onFilterDestDistChange(distName) {
        fetchSubdistrictsForDistrict('filterDistDest', 'filterSubdistDest');
    }

    // Calculator Callbacks
    function onCalcOriginProvChange(provName, targetCity = null) {
        fetchCitiesForProv(provName, 'calcOrigin', targetCity, () => {
            fetchDistrictsForCity('calcOrigin', 'calcOriginDist', null, () => {
                fetchSubdistrictsForDistrict('calcOriginDist', 'calcOriginSubdist');
            });
        });
    }

    function onCalcOriginCityChange(cityName) {
        fetchDistrictsForCity('calcOrigin', 'calcOriginDist', null, () => {
            fetchSubdistrictsForDistrict('calcOriginDist', 'calcOriginSubdist');
        });
    }

    function onCalcOriginDistChange(distName) {
        fetchSubdistrictsForDistrict('calcOriginDist', 'calcOriginSubdist');
    }

    function onCalcDestProvChange(provName, targetCity = null) {
        fetchCitiesForProv(provName, 'calcDest', targetCity, () => {
            fetchDistrictsForCity('calcDest', 'calcDestDist', null, () => {
                fetchSubdistrictsForDistrict('calcDestDist', 'calcDestSubdist');
            });
        });
    }

    function onCalcDestCityChange(cityName) {
        fetchDistrictsForCity('calcDest', 'calcDestDist', null, () => {
            fetchSubdistrictsForDistrict('calcDestDist', 'calcDestSubdist');
        });
    }

    function onCalcDestDistChange(distName) {
        fetchSubdistrictsForDistrict('calcDestDist', 'calcDestSubdist');
    }

    // New Modal Handlers
    function onNewOrigProvChange(provName) {
        fetchCitiesForProv(provName, 'newCityOrig', null, (cityName) => {
            fetchDistrictsForCity('newCityOrig', 'newDistOrig', null, (distName) => {
                fetchSubdistrictsForDistrict('newDistOrig', 'newSubdistOrig');
            });
        });
    }

    function onNewOrigCityChange(cityName) {
        fetchDistrictsForCity('newCityOrig', 'newDistOrig', null, (distName) => {
            fetchSubdistrictsForDistrict('newDistOrig', 'newSubdistOrig');
        });
    }

    function onNewOrigDistChange(distName) {
        fetchSubdistrictsForDistrict('newDistOrig', 'newSubdistOrig');
    }

    function onNewDestProvChange(provName) {
        fetchCitiesForProv(provName, 'newCityDest', null, (cityName) => {
            fetchDistrictsForCity('newCityDest', 'newDistDest', null, (distName) => {
                fetchSubdistrictsForDistrict('newDistDest', 'newSubdistDest');
            });
        });
    }

    function onNewDestCityChange(cityName) {
        fetchDistrictsForCity('newCityDest', 'newDistDest', null, (distName) => {
            fetchSubdistrictsForDistrict('newDistDest', 'newSubdistDest');
        });
    }

    function onNewDestDistChange(distName) {
        fetchSubdistrictsForDistrict('newDistDest', 'newSubdistDest');
    }

    // Edit Modal Handlers
    function onEditOrigProvChange(id, provName) {
        fetchCitiesForProv(provName, `editCityOrig_${id}`, null, () => {
            fetchDistrictsForCity(`editCityOrig_${id}`, `editDistOrig_${id}`, null, () => {
                fetchSubdistrictsForDistrict(`editDistOrig_${id}`, `editSubdistOrig_${id}`);
            });
        });
    }

    function onEditOrigCityChange(id, cityName) {
        fetchDistrictsForCity(`editCityOrig_${id}`, `editDistOrig_${id}`, null, () => {
            fetchSubdistrictsForDistrict(`editDistOrig_${id}`, `editSubdistOrig_${id}`);
        });
    }

    function onEditOrigDistChange(id, distName) {
        fetchSubdistrictsForDistrict(`editDistOrig_${id}`, `editSubdistOrig_${id}`);
    }

    function onEditDestProvChange(id, provName) {
        fetchCitiesForProv(provName, `editCityDest_${id}`, null, () => {
            fetchDistrictsForCity(`editCityDest_${id}`, `editDistDest_${id}`, null, () => {
                fetchSubdistrictsForDistrict(`editDistDest_${id}`, `editSubdistDest_${id}`);
            });
        });
    }

    function onEditDestCityChange(id, cityName) {
        fetchDistrictsForCity(`editCityDest_${id}`, `editDistDest_${id}`, null, () => {
            fetchSubdistrictsForDistrict(`editDistDest_${id}`, `editSubdistDest_${id}`);
        });
    }

    function onEditDestDistChange(id, distName) {
        fetchSubdistrictsForDistrict(`editDistDest_${id}`, `editSubdistDest_${id}`);
    }

    function runCalculator() {
        const origin = document.getElementById('calcOrigin').value;
        const destination = document.getElementById('calcDest').value;
        const originDistrict = document.getElementById('calcOriginDist') ? document.getElementById('calcOriginDist').value : '';
        const originSubdistrict = document.getElementById('calcOriginSubdist') ? document.getElementById('calcOriginSubdist').value : '';
        const destDistrict = document.getElementById('calcDestDist') ? document.getElementById('calcDestDist').value : '';
        const destSubdistrict = document.getElementById('calcDestSubdist') ? document.getElementById('calcDestSubdist').value : '';
        const weight = document.getElementById('calcWeight').value;
        const service = document.getElementById('calcService').value;

        fetch("{{ route('tariffs.calculate') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                origin_city: origin,
                destination_city: destination,
                origin_district: originDistrict,
                origin_subdistrict: originSubdistrict,
                destination_district: destDistrict,
                destination_subdistrict: destSubdistrict,
                weight_kg: weight,
                service_type: service
            })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('calcResultFee').innerText = 'Rp ' + data.total_fee.toLocaleString('id-ID');
            document.getElementById('calcResultEst').innerText = '(' + data.estimated_days + ')';
        })
        .catch(err => alert('Gagal menghitung ongkir'));
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadProvinces();
    });
</script>
@endpush
@endsection
