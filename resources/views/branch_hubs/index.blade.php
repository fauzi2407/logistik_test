@extends('layouts.app')

@section('title', 'Master Transit Hub & Cabang')

@section('content')
<div class="space-y-6">
    <!-- Header & Action Bar -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Master Data Transit Hub & Cabang Logistik</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola titik lokasi gudang transit, sorting hub, dan cabang operasional pengiriman barang.</p>
        </div>
        <button onclick="document.getElementById('newHubModal').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition flex items-center justify-center space-x-1.5">
            <i class="fa-solid fa-warehouse"></i>
            <span>+ Tambah Transit Hub Baru</span>
        </button>
    </div>

    <!-- Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('branch-hubs.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode hub, nama hub, kota, atau penanggung jawab..." class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            </div>
            <div class="flex items-center space-x-2 w-full sm:w-auto">
                <button type="submit" class="py-2 px-5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition flex-1 sm:flex-none">
                    <i class="fa-solid fa-filter mr-1"></i> Cari Hub
                </button>
                @if($search)
                    <a href="{{ route('branch-hubs.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition" title="Reset Search">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Branch Hubs Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-4">Kode & Nama Hub</th>
                        <th class="p-4">Kota / Kabupaten</th>
                        <th class="p-4">Penanggung Jawab (PIC)</th>
                        <th class="p-4">No. Telepon & Alamat Hub</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($hubs as $h)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4">
                                <div class="font-mono font-bold text-indigo-600 text-xs">{{ $h->code }}</div>
                                <div class="font-extrabold text-slate-900 text-sm mt-0.5">{{ $h->name }}</div>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-800">
                                    <i class="fa-solid fa-city mr-1"></i> {{ $h->city }}
                                </span>
                            </td>
                            <td class="p-4 font-bold text-slate-800">
                                <i class="fa-solid fa-user-tie text-slate-400 mr-1"></i> {{ $h->person_in_charge }}
                            </td>
                            <td class="p-4 text-slate-700">
                                <div><i class="fa-solid fa-phone text-slate-400 mr-1 text-[10px]"></i> {{ $h->phone }}</div>
                                <div class="text-[11px] text-slate-400 truncate max-w-xs mt-0.5">{{ $h->address }}</div>
                            </td>
                            <td class="p-4 text-center space-x-2">
                                <button onclick="openEditHubModal({{ $h->id }}, '{{ addslashes($h->code) }}', '{{ addslashes($h->name) }}', '{{ addslashes($h->city) }}', '{{ addslashes($h->person_in_charge) }}', '{{ addslashes($h->phone) }}', '{{ addslashes($h->address) }}')" class="p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 font-bold transition" title="Edit Hub">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('branch-hubs.destroy', $h->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transit hub {{ $h->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold transition" title="Hapus Hub">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400">Tidak ada data transit hub ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $hubs->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Modal 1: Tambah Transit Hub Baru -->
<div id="newHubModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm flex items-center">
                <i class="fa-solid fa-warehouse text-indigo-600 mr-2"></i> Tambah Transit Hub Baru
            </h3>
            <button onclick="document.getElementById('newHubModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('branch-hubs.store') }}" method="POST" class="space-y-3 text-left">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kode Hub (Opsional - Otomatis jika kosong)</label>
                <input type="text" name="code" placeholder="Contoh: HUB-JKT01" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 font-mono">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Transit Hub / Gudang *</label>
                <input type="text" name="name" required placeholder="Contoh: Hub Utama Jakarta Selatan" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Cari & Pilih Kota / Kabupaten *</label>
                <input type="text" onkeyup="filterSelectOptions(this, 'newHubCitySelect')" placeholder="🔍 Ketik nama kota..." class="w-full px-3 py-1.5 mb-1.5 rounded-xl bg-slate-50 border border-slate-300 text-xs font-semibold text-slate-800">
                <select name="city" id="newHubCitySelect" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 hub-city-list">
                    <option value="">-- Pilih Kota/Kab --</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Penanggung Jawab (PIC) *</label>
                    <input type="text" name="person_in_charge" required placeholder="Hendra Setiawan" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. Telepon / WA *</label>
                    <input type="text" name="phone" required placeholder="021-7654321" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Lengkap Hub *</label>
                <textarea name="address" rows="2" required placeholder="Jl. Logistik Utama No. 88, Cilandak..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800"></textarea>
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('newHubModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20">Simpan Transit Hub</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Edit Data Transit Hub -->
<div id="editHubModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm flex items-center">
                <i class="fa-solid fa-pen-to-square text-amber-600 mr-2"></i> Edit Data Transit Hub
            </h3>
            <button onclick="document.getElementById('editHubModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="editHubForm" method="POST" class="space-y-3 text-left">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kode Hub *</label>
                <input type="text" name="code" id="editHubCode" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 font-mono">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Transit Hub / Gudang *</label>
                <input type="text" name="name" id="editHubName" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Cari & Pilih Kota / Kabupaten *</label>
                <input type="text" onkeyup="filterSelectOptions(this, 'editHubCitySelect')" placeholder="🔍 Ketik nama kota..." class="w-full px-3 py-1.5 mb-1.5 rounded-xl bg-slate-50 border border-slate-300 text-xs font-semibold text-slate-800">
                <select name="city" id="editHubCitySelect" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 hub-city-list">
                    <option value="">-- Pilih Kota/Kab --</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Penanggung Jawab (PIC) *</label>
                    <input type="text" name="person_in_charge" id="editHubPic" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. Telepon / WA *</label>
                    <input type="text" name="phone" id="editHubPhone" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Lengkap Hub *</label>
                <textarea name="address" id="editHubAddress" rows="2" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800"></textarea>
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('editHubModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
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

    function openEditHubModal(id, code, name, city, pic, phone, address) {
        document.getElementById('editHubForm').action = "/branch-hubs/" + id;
        document.getElementById('editHubCode').value = code;
        document.getElementById('editHubName').value = name;
        document.getElementById('editHubPic').value = pic;
        document.getElementById('editHubPhone').value = phone;
        document.getElementById('editHubAddress').value = address;
        
        const citySelect = document.getElementById('editHubCitySelect');
        citySelect.value = city;

        document.getElementById('editHubModal').classList.remove('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const citySelects = document.querySelectorAll('.hub-city-list');
        fetch("{{ route('api.provinces') }}")
            .then(res => res.json())
            .then(provinces => {
                provinces.forEach(p => {
                    fetch(`/api/cities/${p.id}`)
                        .then(cRes => cRes.json())
                        .then(cities => {
                            cities.forEach(c => {
                                citySelects.forEach(sel => {
                                    const opt = document.createElement('option');
                                    opt.value = c.name;
                                    opt.textContent = c.name + ' (' + p.name + ')';
                                    sel.appendChild(opt);
                                });
                            });
                        });
                });
            });
    });
</script>
@endpush
@endsection
