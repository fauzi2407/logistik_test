@extends('layouts.app')

@section('title', 'Master Wilayah & Kode Pos')

@section('content')
<div class="space-y-6">
    <!-- Header & Quick Actions -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Master Data Wilayah & Kode Pos Indonesia</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola data Provinsi, Kota/Kab, Kecamatan, Kelurahan & Kode Pos (Tambah, Edit, Hapus, Filter, Ekspor & Impor CSV).</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button onclick="document.getElementById('importCsvModal').classList.remove('hidden')" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition">
                <i class="fa-solid fa-file-import mr-1"></i> Import CSV Massal
            </button>
            <a href="{{ route('regions.template-csv') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                <i class="fa-solid fa-file-csv text-emerald-600 mr-1"></i> Template CSV
            </a>
            <a href="{{ route('regions.export') }}" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition">
                <i class="fa-solid fa-file-export mr-1"></i> Export All CSV
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('regions.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cari Wilayah / Kode Pos</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama provinsi, kota, kelurahan, kode pos..." class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Saring Berdasarkan Provinsi</label>
                <select name="province_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="">-- Semua Provinsi --</option>
                    @foreach($provinces as $p)
                        <option value="{{ $p->id }}" {{ $provinceId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition">
                    <i class="fa-solid fa-filter mr-1"></i> Terapkan Filter
                </button>
                @if($search || $provinceId)
                    <a href="{{ route('regions.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Region Data Tabs -->
    <div class="space-y-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 bg-white p-2 rounded-2xl border border-slate-200/80 shadow-sm text-center text-xs font-bold">
            <button onclick="switchTab('prov')" class="tab-btn py-2.5 rounded-xl bg-indigo-600 text-white transition" id="tab-prov">
                Provinsi ({{ count($provinces) }})
            </button>
            <button onclick="switchTab('city')" class="tab-btn py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 transition" id="tab-city">
                Kota / Kab ({{ $cities->total() }})
            </button>
            <button onclick="switchTab('dist')" class="tab-btn py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 transition" id="tab-dist">
                Kecamatan ({{ $districts->total() }})
            </button>
            <button onclick="switchTab('subdist')" class="tab-btn py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 transition" id="tab-subdist">
                Kelurahan & Kode Pos ({{ $subdistricts->total() }})
            </button>
        </div>

        <!-- Add Manual Entry Action Bar -->
        <div class="flex items-center justify-between px-2 text-xs">
            <span class="text-slate-500 font-medium">Klik tombol tambah di kanan untuk menambah entitas wilayah baru secara manual:</span>
            <div class="space-x-1.5">
                <button onclick="document.getElementById('addProvModal').classList.remove('hidden')" class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold">
                    + Provinsi
                </button>
                <button onclick="document.getElementById('addCityModal').classList.remove('hidden')" class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 font-bold">
                    + Kota/Kab
                </button>
                <button onclick="document.getElementById('addDistModal').classList.remove('hidden')" class="px-3 py-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 font-bold">
                    + Kecamatan
                </button>
                <button onclick="document.getElementById('addSubdistModal').classList.remove('hidden')" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-800 hover:bg-slate-200 font-bold">
                    + Kelurahan & Kode Pos
                </button>
            </div>
        </div>

        <!-- Tab 1: Provinsi Table -->
        <div id="section-prov" class="tab-section bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 font-extrabold text-xs text-slate-900 uppercase tracking-wider">Daftar Provinsi Indonesia</div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                        <tr>
                            <th class="p-4">Kode</th>
                            <th class="p-4">Nama Provinsi</th>
                            <th class="p-4">Jumlah Kota / Kabupaten</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($provinces as $p)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-4 font-mono font-bold text-indigo-600">{{ $p->code }}</td>
                                <td class="p-4 font-extrabold text-slate-900 text-sm">{{ $p->name }}</td>
                                <td class="p-4 font-bold text-slate-700">{{ $p->cities_count }} Kota/Kab</td>
                                <td class="p-4 text-center space-x-2">
                                    <button onclick="openEditProvModal({{ $p->id }}, '{{ addslashes($p->name) }}')" class="p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 font-bold" title="Edit Provinsi">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form action="{{ route('regions.province.destroy', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus provinsi {{ $p->name }} beserta seluruh kota di dalamnya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold" title="Hapus Provinsi">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-400">Tidak ada provinsi ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 2: Kota/Kab Table -->
        <div id="section-city" class="tab-section hidden bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 font-extrabold text-xs text-slate-900 uppercase tracking-wider">Daftar Kota & Kabupaten</div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                        <tr>
                            <th class="p-4">Nama Kota / Kabupaten</th>
                            <th class="p-4">Jenis</th>
                            <th class="p-4">Provinsi</th>
                            <th class="p-4">Jumlah Kecamatan</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($cities as $c)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-4 font-extrabold text-slate-900">{{ $c->name }}</td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] {{ $c->type == 'Kota' ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ $c->type }}
                                    </span>
                                </td>
                                <td class="p-4 font-bold text-slate-700">{{ $c->province ? $c->province->name : '-' }}</td>
                                <td class="p-4 font-bold text-slate-700">{{ $c->districts_count }} Kecamatan</td>
                                <td class="p-4 text-center space-x-2">
                                    <button onclick="openEditCityModal({{ $c->id }}, {{ $c->province_id }}, '{{ addslashes($c->name) }}', '{{ $c->type }}')" class="p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 font-bold" title="Edit Kota/Kab">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form action="{{ route('regions.city.destroy', $c->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus {{ $c->name }} beserta seluruh kecamatannya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold" title="Hapus Kota/Kab">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-400">Tidak ada kota/kabupaten ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                {{ $cities->appends(request()->query())->links() }}
            </div>
        </div>

        <!-- Tab 3: Kecamatan Table -->
        <div id="section-dist" class="tab-section hidden bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 font-extrabold text-xs text-slate-900 uppercase tracking-wider">Daftar Kecamatan</div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                        <tr>
                            <th class="p-4">Nama Kecamatan</th>
                            <th class="p-4">Kota / Kabupaten</th>
                            <th class="p-4">Provinsi</th>
                            <th class="p-4">Jumlah Kelurahan</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($districts as $d)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-4 font-extrabold text-slate-900">{{ $d->name }}</td>
                                <td class="p-4 font-bold text-slate-700">{{ $d->city ? $d->city->name : '-' }}</td>
                                <td class="p-4 text-slate-600">{{ $d->city && $d->city->province ? $d->city->province->name : '-' }}</td>
                                <td class="p-4 font-bold text-slate-700">{{ $d->subdistricts_count }} Kelurahan</td>
                                <td class="p-4 text-center space-x-2">
                                    <button onclick="openEditDistModal({{ $d->id }}, {{ $d->city_id }}, '{{ addslashes($d->name) }}')" class="p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 font-bold" title="Edit Kecamatan">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form action="{{ route('regions.district.destroy', $d->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kecamatan {{ $d->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold" title="Hapus Kecamatan">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-400">Tidak ada kecamatan ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                {{ $districts->appends(request()->query())->links() }}
            </div>
        </div>

        <!-- Tab 4: Kelurahan & Kode Pos Table -->
        <div id="section-subdist" class="tab-section hidden bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 font-extrabold text-xs text-slate-900 uppercase tracking-wider">Daftar Kelurahan & Kode Pos</div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                        <tr>
                            <th class="p-4">Nama Kelurahan</th>
                            <th class="p-4">Kode Pos</th>
                            <th class="p-4">Kecamatan</th>
                            <th class="p-4">Kota / Kabupaten</th>
                            <th class="p-4">Provinsi</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($subdistricts as $sd)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-4 font-extrabold text-slate-900">{{ $sd->name }}</td>
                                <td class="p-4 font-mono font-bold text-indigo-600 text-sm">{{ $sd->postal_code }}</td>
                                <td class="p-4 font-bold text-slate-700">{{ $sd->district ? $sd->district->name : '-' }}</td>
                                <td class="p-4 text-slate-700">{{ $sd->district && $sd->district->city ? $sd->district->city->name : '-' }}</td>
                                <td class="p-4 text-slate-500">{{ $sd->district && $sd->district->city && $sd->district->city->province ? $sd->district->city->province->name : '-' }}</td>
                                <td class="p-4 text-center space-x-2">
                                    <button onclick="openEditSubdistModal({{ $sd->id }}, {{ $sd->district_id }}, '{{ addslashes($sd->name) }}', '{{ $sd->postal_code }}')" class="p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 font-bold" title="Edit Kelurahan">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form action="{{ route('regions.subdistrict.destroy', $sd->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kelurahan {{ $sd->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold" title="Hapus Kelurahan">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400">Tidak ada kelurahan atau kode pos ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                {{ $subdistricts->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Import CSV Massal Wilayah -->
<div id="importCsvModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm">Import Massal Master Wilayah dari CSV</h3>
            <button onclick="document.getElementById('importCsvModal').classList.add('hidden')" class="text-slate-400">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form action="{{ route('regions.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Berkas CSV Wilayah *</label>
                <input type="file" name="csv_file" accept=".csv,.txt,.xls,.xlsx" required class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs text-slate-700">
                <p class="text-[10px] text-slate-400 mt-1">Unduh <a href="{{ route('regions.template-csv') }}" class="font-bold text-indigo-600 underline">Template CSV</a> untuk penataan format kolom yang benar.</p>
            </div>
            <div class="pt-2 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('importCsvModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md">
                    <i class="fa-solid fa-upload mr-1"></i> Upload & Process Import
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 1: Add Province -->
<div id="addProvModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm">Tambah Provinsi Baru</h3>
            <button onclick="document.getElementById('addProvModal').classList.add('hidden')" class="text-slate-400">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form action="{{ route('regions.province.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Provinsi *</label>
                <input type="text" name="name" required placeholder="Contoh: Jawa Barat" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>
            <div class="pt-2 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('addProvModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs">Simpan Provinsi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 1 (Edit): Edit Province -->
<div id="editProvModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm">Edit Data Provinsi</h3>
            <button onclick="document.getElementById('editProvModal').classList.add('hidden')" class="text-slate-400">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="editProvForm" method="POST" class="space-y-3">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Provinsi *</label>
                <input type="text" name="name" id="editProvName" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>
            <div class="pt-2 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('editProvModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Add City -->
<div id="addCityModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm">Tambah Kota / Kabupaten Baru</h3>
            <button onclick="document.getElementById('addCityModal').classList.add('hidden')" class="text-slate-400">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form action="{{ route('regions.city.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Cari & Pilih Provinsi *</label>
                <div class="relative mb-1.5">
                    <input type="text" onkeyup="filterSelectOptions(this, 'cityProvSelect')" placeholder="🔍 Ketik nama provinsi untuk mencari..." class="w-full px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <select name="province_id" id="cityProvSelect" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="">-- Pilih Provinsi --</option>
                    @foreach($provinces as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Kota / Kabupaten *</label>
                <input type="text" name="name" required placeholder="Contoh: Kota Bandung" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis *</label>
                <select name="type" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="Kota">Kota</option>
                    <option value="Kabupaten">Kabupaten</option>
                </select>
            </div>
            <div class="pt-2 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('addCityModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs">Simpan Kota/Kab</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2 (Edit): Edit City -->
<div id="editCityModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm">Edit Data Kota / Kabupaten</h3>
            <button onclick="document.getElementById('editCityModal').classList.add('hidden')" class="text-slate-400">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="editCityForm" method="POST" class="space-y-3">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Cari & Pilih Provinsi *</label>
                <div class="relative mb-1.5">
                    <input type="text" onkeyup="filterSelectOptions(this, 'editCityProvSelect')" placeholder="🔍 Ketik nama provinsi..." class="w-full px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <select name="province_id" id="editCityProvSelect" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="">-- Pilih Provinsi --</option>
                    @foreach($provinces as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Kota / Kabupaten *</label>
                <input type="text" name="name" id="editCityName" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis *</label>
                <select name="type" id="editCityType" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="Kota">Kota</option>
                    <option value="Kabupaten">Kabupaten</option>
                </select>
            </div>
            <div class="pt-2 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('editCityModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Add District -->
<div id="addDistModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm">Tambah Kecamatan Baru</h3>
            <button onclick="document.getElementById('addDistModal').classList.add('hidden')" class="text-slate-400">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form action="{{ route('regions.district.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Cari & Pilih Kota / Kabupaten *</label>
                <div class="relative mb-1.5">
                    <input type="text" onkeyup="filterSelectOptions(this, 'distCitySelect')" placeholder="🔍 Ketik nama kota / kab..." class="w-full px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <select name="city_id" id="distCitySelect" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="">-- Pilih Kota/Kab --</option>
                    @foreach($cities as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->province ? $c->province->name : '' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Kecamatan *</label>
                <input type="text" name="name" required placeholder="Contoh: Coblong" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>
            <div class="pt-2 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('addDistModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs">Simpan Kecamatan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3 (Edit): Edit District -->
<div id="editDistModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm">Edit Data Kecamatan</h3>
            <button onclick="document.getElementById('editDistModal').classList.add('hidden')" class="text-slate-400">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="editDistForm" method="POST" class="space-y-3">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Cari & Pilih Kota / Kabupaten *</label>
                <div class="relative mb-1.5">
                    <input type="text" onkeyup="filterSelectOptions(this, 'editDistCitySelect')" placeholder="🔍 Ketik nama kota / kab..." class="w-full px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <select name="city_id" id="editDistCitySelect" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="">-- Pilih Kota/Kab --</option>
                    @foreach($cities as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->province ? $c->province->name : '' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Kecamatan *</label>
                <input type="text" name="name" id="editDistName" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>
            <div class="pt-2 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('editDistModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 4: Add Subdistrict -->
<div id="addSubdistModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm">Tambah Kelurahan & Kode Pos Baru</h3>
            <button onclick="document.getElementById('addSubdistModal').classList.add('hidden')" class="text-slate-400">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form action="{{ route('regions.subdistrict.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Cari & Pilih Kecamatan *</label>
                <div class="relative mb-1.5">
                    <input type="text" onkeyup="filterSelectOptions(this, 'subdistDistSelect')" placeholder="🔍 Ketik nama kecamatan..." class="w-full px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <select name="district_id" id="subdistDistSelect" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="">-- Pilih Kecamatan --</option>
                    @foreach($districts as $d)
                        <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->city ? $d->city->name : '' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Kelurahan *</label>
                <input type="text" name="name" required placeholder="Contoh: Dago" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kode Pos *</label>
                <input type="text" name="postal_code" required placeholder="Contoh: 40135" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-indigo-600">
            </div>
            <div class="pt-2 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('addSubdistModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs">Simpan Kelurahan & Kode Pos</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 4 (Edit): Edit Subdistrict -->
<div id="editSubdistModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm">Edit Data Kelurahan & Kode Pos</h3>
            <button onclick="document.getElementById('editSubdistModal').classList.add('hidden')" class="text-slate-400">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="editSubdistForm" method="POST" class="space-y-3">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Cari & Pilih Kecamatan *</label>
                <div class="relative mb-1.5">
                    <input type="text" onkeyup="filterSelectOptions(this, 'editSubdistDistSelect')" placeholder="🔍 Ketik nama kecamatan..." class="w-full px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <select name="district_id" id="editSubdistDistSelect" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="">-- Pilih Kecamatan --</option>
                    @foreach($districts as $d)
                        <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->city ? $d->city->name : '' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Kelurahan *</label>
                <input type="text" name="name" id="editSubdistName" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kode Pos *</label>
                <input type="text" name="postal_code" id="editSubdistPostal" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-indigo-600">
            </div>
            <div class="pt-2 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('editSubdistModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function switchTab(tabKey) {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white');
            btn.classList.add('text-slate-600', 'hover:bg-slate-100');
        });

        document.querySelectorAll('.tab-section').forEach(sec => {
            sec.classList.add('hidden');
        });

        document.getElementById('tab-' + tabKey).classList.remove('text-slate-600', 'hover:bg-slate-100');
        document.getElementById('tab-' + tabKey).classList.add('bg-indigo-600', 'text-white');
        document.getElementById('section-' + tabKey).classList.remove('hidden');
    }

    function filterSelectOptions(inputElem, selectId) {
        const filter = inputElem.value.toLowerCase();
        const select = document.getElementById(selectId);
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

    function openEditProvModal(id, name) {
        document.getElementById('editProvForm').action = "/regions/province/" + id;
        document.getElementById('editProvName').value = name;
        document.getElementById('editProvModal').classList.remove('hidden');
    }

    function openEditCityModal(id, provId, name, type) {
        document.getElementById('editCityForm').action = "/regions/city/" + id;
        document.getElementById('editCityProvSelect').value = provId;
        document.getElementById('editCityName').value = name;
        document.getElementById('editCityType').value = type;
        document.getElementById('editCityModal').classList.remove('hidden');
    }

    function openEditDistModal(id, cityId, name) {
        document.getElementById('editDistForm').action = "/regions/district/" + id;
        document.getElementById('editDistCitySelect').value = cityId;
        document.getElementById('editDistName').value = name;
        document.getElementById('editDistModal').classList.remove('hidden');
    }

    function openEditSubdistModal(id, distId, name, postal) {
        document.getElementById('editSubdistForm').action = "/regions/subdistrict/" + id;
        document.getElementById('editSubdistDistSelect').value = distId;
        document.getElementById('editSubdistName').value = name;
        document.getElementById('editSubdistPostal').value = postal;
        document.getElementById('editSubdistModal').classList.remove('hidden');
    }
</script>
@endpush
@endsection
