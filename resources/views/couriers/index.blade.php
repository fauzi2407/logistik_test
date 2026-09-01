@extends('layouts.app')

@section('title', 'Master Data Kurir')

@section('content')
<div class="space-y-6">
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Master Data Kurir / Driver</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola data personalia kurir, gaji pokok, komisi pengiriman, hirarki alamat rumah & armada.</p>
        </div>
        <button onclick="openNewCourierModal()" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition flex items-center">
            <i class="fa-solid fa-plus mr-1.5"></i> Tambah Kurir Baru
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-id-card-clip text-indigo-600 mr-1.5"></i> Daftar Kurir Operasional
            </h3>
            <form method="GET" action="{{ route('couriers.index') }}" class="flex items-center space-x-2">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="🔍 Cari nama, kode, HP, kota kurir..." class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 w-64 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition">
                    Cari
                </button>
                @if(!empty($search))
                    <a href="{{ route('couriers.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs hover:bg-slate-200">
                        Reset
                    </a>
                @endif
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-4">Kode & Nama Kurir</th>
                        <th class="p-4">No. HP & Kontak Darurat</th>
                        <th class="p-4">Alamat Rumah (Hirarki Indonesia)</th>
                        <th class="p-4">Umur & SIM</th>
                        <th class="p-4">Armada & Hub</th>
                        <th class="p-4">Gaji Pokok & Komisi</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($couriers as $cr)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $cr->name }}</div>
                                <div class="text-[11px] text-indigo-600 font-mono font-bold">{{ $cr->courier_code }}</div>
                            </td>
                            <td class="p-4 text-slate-700">
                                <div class="font-bold"><i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $cr->phone }}</div>
                                <div class="text-[11px] text-slate-400"><i class="fa-solid fa-phone-flip text-slate-400 mr-1"></i> {{ $cr->emergency_phone ?? '-' }}</div>
                            </td>
                            <td class="p-4 text-slate-700">
                                <div class="font-bold text-slate-900 max-w-xs truncate">{{ $cr->address ?? '-' }}</div>
                                @if($cr->city || $cr->province)
                                    <div class="text-[11px] text-indigo-600 font-semibold mt-0.5 max-w-xs truncate">
                                        <i class="fa-solid fa-location-dot text-indigo-500 mr-1"></i>
                                        {{ implode(', ', array_filter([$cr->subdistrict, $cr->district, $cr->city, $cr->province])) }}
                                        {{ $cr->postal_code ? '('.$cr->postal_code.')' : '' }}
                                    </div>
                                @endif
                            </td>
                            <td class="p-4 text-slate-700">
                                <div class="font-bold">{{ $cr->age ? $cr->age . ' thn' : '-' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono font-semibold">{{ $cr->license_number ?? 'No SIM' }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-900">{{ $cr->vehicle ? $cr->vehicle->plate_number : 'Tanpa Armada' }}</div>
                                <div class="text-[11px] text-indigo-600 font-bold">{{ $cr->branchHub ? $cr->branchHub->name : 'Tanpa Hub' }}</div>
                            </td>
                            <td class="p-4">
                                @if(Auth::user()->hasPermission('couriers.index', 'view_salary'))
                                    <div class="font-bold text-slate-900">
                                        <span class="text-[10px] text-slate-400 uppercase font-semibold">Gaji Pokok:</span> 
                                        <span class="font-black text-indigo-700 font-mono">Rp {{ number_format($cr->basic_salary ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="text-[11px] text-emerald-600 font-bold mt-0.5">
                                        <span class="text-slate-400 font-normal">Komisi:</span> Rp {{ number_format($cr->commission_per_delivery ?? 0, 0, ',', '.') }} / pkt
                                    </div>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-400 font-mono text-[10px] font-bold inline-flex items-center" title="Akses melihat gaji & komisi dibatasi">
                                        <i class="fa-solid fa-eye-slash mr-1"></i> Rahasia
                                    </span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $cr->status == 'available' ? 'bg-emerald-100 text-emerald-800' : ($cr->status == 'on_duty' ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $cr->status }}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-1">
                                <button onclick="openEditCourierModal({{ $cr->id }}, '{{ addslashes($cr->province ?? '') }}', '{{ addslashes($cr->city ?? '') }}', '{{ addslashes($cr->district ?? '') }}', '{{ addslashes($cr->subdistrict ?? '') }}')" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg font-bold" title="Edit Kurir">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('couriers.destroy', $cr->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kurir ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal per Courier -->
                        <div id="editCourierModal_{{ $cr->id }}" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
                            <div class="bg-white rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl text-left max-h-[90vh] overflow-y-auto">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                    <h3 class="font-extrabold text-slate-900 text-sm">Edit Data Kurir - {{ $cr->name }}</h3>
                                    <button onclick="document.getElementById('editCourierModal_{{ $cr->id }}').classList.add('hidden')" class="text-slate-400">
                                        <i class="fa-solid fa-xmark text-lg"></i>
                                    </button>
                                </div>
                                <form action="{{ route('couriers.update', $cr->id) }}" method="POST" class="space-y-4">
                                    @csrf
                                    @method('PUT')

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap Kurir *</label>
                                            <input type="text" name="name" value="{{ $cr->name }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor HP / WhatsApp *</label>
                                            <input type="text" name="phone" value="{{ $cr->phone }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kontak Darurat (Emergency)</label>
                                            <input type="text" name="emergency_phone" value="{{ $cr->emergency_phone }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Umur (Tahun)</label>
                                            <input type="number" name="age" value="{{ $cr->age }}" min="17" max="70" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                                        </div>
                                    </div>

                                    <!-- Hirarki Wilayah Indonesia untuk Edit Alamat Kurir -->
                                    <div class="space-y-1.5 pt-1">
                                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">
                                            <i class="fa-solid fa-map-location-dot text-indigo-600 mr-1"></i> Hirarki Wilayah Alamat Tinggal Kurir
                                        </label>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2.5 text-xs bg-slate-50 p-3 rounded-xl border border-slate-200">
                                            <div>
                                                <label class="block font-bold text-slate-700 mb-0.5">Provinsi</label>
                                                <select name="province" id="edit_courier_prov_select_{{ $cr->id }}" onchange="onEditCourierProvChange({{ $cr->id }}, this.value)" class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 font-semibold text-slate-800">
                                                    <option value="">-- Pilih Provinsi --</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block font-bold text-slate-700 mb-0.5">Kota / Kab</label>
                                                <select name="city" id="edit_courier_city_select_{{ $cr->id }}" onchange="onEditCourierCityChange({{ $cr->id }}, this.value)" class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 font-bold text-indigo-700">
                                                    <option value="">-- Pilih Kota/Kab --</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block font-bold text-slate-700 mb-0.5">Kecamatan</label>
                                                <select name="district" id="edit_courier_dist_select_{{ $cr->id }}" onchange="onEditCourierDistChange({{ $cr->id }}, this.value)" class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 font-semibold text-slate-800">
                                                    <option value="">-- Pilih Kecamatan --</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block font-bold text-slate-700 mb-0.5">Kelurahan</label>
                                                <select name="subdistrict" id="edit_courier_subdist_select_{{ $cr->id }}" onchange="onEditCourierSubdistChange({{ $cr->id }}, this.value)" class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 font-semibold text-slate-800">
                                                    <option value="">-- Pilih Kelurahan --</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kode Pos Rumah</label>
                                            <input type="text" name="postal_code" id="edit_courier_postal_input_{{ $cr->id }}" value="{{ $cr->postal_code }}" placeholder="12430" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-indigo-600">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Jalan / Detail Rumah</label>
                                            <input type="text" name="address" value="{{ $cr->address }}" placeholder="Jl. Merdeka No. 45 RT 02/05" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                                        </div>
                                    </div>

                                    <!-- Financial Info: Gaji Pokok & Komisi -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-indigo-50/60 p-3 rounded-xl border border-indigo-100">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Gaji Pokok Kurir (Rp) *</label>
                                            <input type="number" name="basic_salary" value="{{ old('basic_salary', $cr->basic_salary ?? 0) }}" min="0" step="1000" required {{ Auth::user()->hasPermission('couriers.index', 'edit_salary') ? '' : 'readonly title="Akses edit gaji dibatasi"' }} class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-black text-indigo-700 {{ Auth::user()->hasPermission('couriers.index', 'edit_salary') ? '' : 'bg-slate-100 cursor-not-allowed' }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Komisi per Paket (Rp) *</label>
                                            <input type="number" name="commission_per_delivery" value="{{ $cr->commission_per_delivery }}" min="0" step="500" required {{ Auth::user()->hasPermission('couriers.index', 'edit_salary') ? '' : 'readonly title="Akses edit komisi dibatasi"' }} class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-black text-emerald-600 {{ Auth::user()->hasPermission('couriers.index', 'edit_salary') ? '' : 'bg-slate-100 cursor-not-allowed' }}">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor SIM</label>
                                            <input type="text" name="license_number" value="{{ $cr->license_number }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Armada</label>
                                            <select name="vehicle_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                                                <option value="">-- Tanpa Kendaraan --</option>
                                                @foreach($vehicles as $vh)
                                                    <option value="{{ $vh->id }}" {{ $cr->vehicle_id == $vh->id ? 'selected' : '' }}>{{ $vh->plate_number }} ({{ $vh->vehicle_type }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Hub Cabang</label>
                                            <select name="branch_hub_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                                                <option value="">-- Tanpa Hub --</option>
                                                @foreach($hubs as $hb)
                                                    <option value="{{ $hb->id }}" {{ $cr->branch_hub_id == $hb->id ? 'selected' : '' }}>{{ $hb->name }} ({{ $hb->city }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Operational *</label>
                                        <select name="status" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                                            <option value="available" {{ $cr->status == 'available' ? 'selected' : '' }}>Available</option>
                                            <option value="on_duty" {{ $cr->status == 'on_duty' ? 'selected' : '' }}>On Duty</option>
                                            <option value="off" {{ $cr->status == 'off' ? 'selected' : '' }}>Off</option>
                                        </select>
                                    </div>

                                    <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                                        <button type="button" onclick="document.getElementById('editCourierModal_{{ $cr->id }}').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs">Simpan Perubahan Kurir</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400 font-semibold">
                                Tidak ada data kurir ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($couriers->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $couriers->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Create Baru -->
<div id="newCourierModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm">Tambah Data Kurir Baru</h3>
            <button onclick="document.getElementById('newCourierModal').classList.add('hidden')" class="text-slate-400">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form action="{{ route('couriers.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap Kurir *</label>
                    <input type="text" name="name" required placeholder="Contoh: Rahmat Hidayat" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. HP / WhatsApp *</label>
                    <input type="text" name="phone" required placeholder="081299887766" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. HP Kontak Darurat</label>
                    <input type="text" name="emergency_phone" placeholder="081377665544 (Kerabat)" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Umur (Tahun)</label>
                    <input type="number" name="age" value="25" min="17" max="70" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
            </div>

            <!-- Hirarki Wilayah Indonesia untuk Alamat Rumah Kurir Baru -->
            <div class="space-y-1.5 pt-1">
                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">
                    <i class="fa-solid fa-map-location-dot text-indigo-600 mr-1"></i> Hirarki Wilayah Alamat Tinggal Kurir
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2.5 text-xs bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <div>
                        <label class="block font-bold text-slate-700 mb-0.5">Provinsi</label>
                        <select name="province" id="create_courier_prov_select" onchange="onCreateCourierProvChange(this.value)" class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 font-semibold text-slate-800">
                            <option value="">-- Pilih Provinsi --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-0.5">Kota / Kab</label>
                        <select name="city" id="create_courier_city_select" onchange="onCreateCourierCityChange(this.value)" class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 font-bold text-indigo-700">
                            <option value="">-- Pilih Kota/Kab --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-0.5">Kecamatan</label>
                        <select name="district" id="create_courier_dist_select" onchange="onCreateCourierDistChange(this.value)" class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 font-semibold text-slate-800">
                            <option value="">-- Pilih Kecamatan --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-0.5">Kelurahan</label>
                        <select name="subdistrict" id="create_courier_subdist_select" onchange="onCreateCourierSubdistChange(this.value)" class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 font-semibold text-slate-800">
                            <option value="">-- Pilih Kelurahan --</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kode Pos Rumah</label>
                    <input type="text" name="postal_code" id="create_courier_postal_input" placeholder="12430" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-indigo-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Jalan / Detail Rumah</label>
                    <input type="text" name="address" placeholder="Jl. Merdeka No. 45 RT 02/05..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
            </div>

            <!-- Financial Info: Gaji Pokok & Komisi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-indigo-50/60 p-3 rounded-xl border border-indigo-100">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Gaji Pokok Kurir (Rp) *</label>
                    <input type="number" name="basic_salary" value="3500000" min="0" step="1000" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-black text-indigo-700">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Komisi per Paket (Rp) *</label>
                    <input type="number" name="commission_per_delivery" value="5000" min="0" step="500" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-black text-emerald-600">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor SIM</label>
                    <input type="text" name="license_number" placeholder="SIM-C-998811" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Armada</label>
                    <select name="vehicle_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        <option value="">-- Tanpa Kendaraan --</option>
                        @foreach($vehicles as $vh)
                            <option value="{{ $vh->id }}">{{ $vh->plate_number }} ({{ $vh->vehicle_type }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Hub Cabang</label>
                    <select name="branch_hub_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                        @foreach($hubs as $hb)
                            <option value="{{ $hb->id }}">{{ $hb->name }} ({{ $hb->city }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Operational *</label>
                <select name="status" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="available">Available (Siap Bertugas)</option>
                    <option value="on_duty">On Duty (Sedang Bertugas)</option>
                    <option value="off">Off (Libur/Cuti)</option>
                </select>
            </div>
            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('newCourierModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs">Simpan Data Kurir</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openNewCourierModal() {
        document.getElementById('newCourierModal').classList.remove('hidden');
        initCreateCourierProvinces();
    }

    function openEditCourierModal(id, targetProv, targetCity, targetDist, targetSubdist) {
        document.getElementById(`editCourierModal_${id}`).classList.remove('hidden');
        initEditCourierProvinces(id, targetProv, targetCity, targetDist, targetSubdist);
    }

    // Create Modal Regional Cascading
    function initCreateCourierProvinces() {
        const provSelect = document.getElementById('create_courier_prov_select');
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

    function onCreateCourierProvChange(provName) {
        const provSelect = document.getElementById('create_courier_prov_select');
        const citySelect = document.getElementById('create_courier_city_select');
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

    function onCreateCourierCityChange(cityName) {
        const citySelect = document.getElementById('create_courier_city_select');
        const distSelect = document.getElementById('create_courier_dist_select');
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

    function onCreateCourierDistChange(distName) {
        const distSelect = document.getElementById('create_courier_dist_select');
        const subdistSelect = document.getElementById('create_courier_subdist_select');
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

    function onCreateCourierSubdistChange(subdistName) {
        const subdistSelect = document.getElementById('create_courier_subdist_select');
        const postalInput = document.getElementById('create_courier_postal_input');
        const selectedOpt = Array.from(subdistSelect.options).find(o => o.value === subdistName);

        if (selectedOpt && selectedOpt.dataset.postal) {
            postalInput.value = selectedOpt.dataset.postal;
        }
    }

    // Edit Modal Regional Cascading
    function initEditCourierProvinces(courierId, targetProv = null, targetCity = null, targetDist = null, targetSubdist = null) {
        const provSelect = document.getElementById(`edit_courier_prov_select_${courierId}`);
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
                }
                onEditCourierProvChange(courierId, provSelect.value, targetCity, targetDist, targetSubdist);
            });
    }

    function onEditCourierProvChange(courierId, provName, targetCity = null, targetDist = null, targetSubdist = null) {
        const provSelect = document.getElementById(`edit_courier_prov_select_${courierId}`);
        const citySelect = document.getElementById(`edit_courier_city_select_${courierId}`);
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
                }
                onEditCourierCityChange(courierId, citySelect.value, targetDist, targetSubdist);
            });
    }

    function onEditCourierCityChange(courierId, cityName, targetDist = null, targetSubdist = null) {
        const citySelect = document.getElementById(`edit_courier_city_select_${courierId}`);
        const distSelect = document.getElementById(`edit_courier_dist_select_${courierId}`);
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

                if (targetDist) {
                    const found = Array.from(distSelect.options).find(o => o.value.toLowerCase().includes(targetDist.toLowerCase()));
                    if (found) distSelect.value = found.value;
                }
                onEditCourierDistChange(courierId, distSelect.value, targetSubdist);
            });
    }

    function onEditCourierDistChange(courierId, distName, targetSubdist = null) {
        const distSelect = document.getElementById(`edit_courier_dist_select_${courierId}`);
        const subdistSelect = document.getElementById(`edit_courier_subdist_select_${courierId}`);
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
                onEditCourierSubdistChange(courierId, subdistSelect.value);
            });
    }

    function onEditCourierSubdistChange(courierId, subdistName) {
        const subdistSelect = document.getElementById(`edit_courier_subdist_select_${courierId}`);
        const postalInput = document.getElementById(`edit_courier_postal_input_${courierId}`);
        const selectedOpt = Array.from(subdistSelect.options).find(o => o.value === subdistName);

        if (selectedOpt && selectedOpt.dataset.postal) {
            postalInput.value = selectedOpt.dataset.postal;
        }
    }
</script>
@endpush
@endsection
