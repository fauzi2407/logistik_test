@extends('layouts.app')

@section('title', 'Master Armada Kendaraan')

@section('content')
<div class="space-y-6">
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Master Armada Kendaraan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola data armada truk, van, pickup, dan sepeda motor operasional logistik serta integrasi aset akuntansi.</p>
        </div>
        <button onclick="document.getElementById('newVehicleModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition flex items-center">
            <i class="fa-solid fa-plus mr-1.5"></i> Tambah Armada Baru
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-truck text-indigo-600 mr-1.5"></i> Daftar Armada Kendaraan & Aset
            </h3>
            <form method="GET" action="{{ route('vehicles.index') }}" class="flex items-center space-x-2">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="🔍 Cari plat nomor, jenis, kepemilikan..." class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 w-64 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition">
                    Cari
                </button>
                @if(!empty($search))
                    <a href="{{ route('vehicles.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs hover:bg-slate-200">
                        Reset
                    </a>
                @endif
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-4">Plat Nomor</th>
                        <th class="p-4">Jenis Kendaraan</th>
                        <th class="p-4">Status Kepemilikan & Aset</th>
                        <th class="p-4">Kapasitas Muat</th>
                        <th class="p-4">Status Operational</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($vehicles as $v)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4">
                                <div class="font-mono font-black text-slate-900 text-sm">{{ $v->plate_number }}</div>
                                <div class="text-[10px] text-slate-400 font-bold mt-0.5">
                                    <i class="fa-solid fa-users text-slate-400 mr-1"></i> {{ $v->couriers_count }} Kurir Ditugaskan
                                </div>
                            </td>
                            <td class="p-4 font-bold text-slate-700">
                                {{ $v->vehicle_type }}
                            </td>
                            <td class="p-4">
                                @if($v->ownership_type === 'company')
                                    <div>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-indigo-100 text-indigo-800 border border-indigo-200 inline-flex items-center gap-1">
                                            <i class="fa-solid fa-building"></i> Milik Perusahaan (Aset)
                                        </span>
                                    </div>
                                    @if($v->asset_value > 0)
                                        <div class="text-xs font-black text-slate-800 font-mono mt-1">
                                            Rp {{ number_format($v->asset_value, 0, ',', '.') }}
                                        </div>
                                    @endif
                                    @if($v->journalEntry)
                                        <div class="mt-0.5">
                                            <a href="{{ route('accounting.journals.index', ['search' => $v->journalEntry->journal_number]) }}" class="text-[10px] font-mono font-bold text-emerald-600 hover:underline inline-flex items-center gap-1" title="Lihat Jurnal Akuntansi">
                                                <i class="fa-solid fa-book-journal-whills"></i> {{ $v->journalEntry->journal_number }}
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-700 border border-slate-200 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-user"></i> Milik Sendiri (Pribadi Kurir)
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-slate-900 font-bold">
                                {{ number_format($v->capacity_kg) }} kg
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $v->status == 'active' ? 'bg-emerald-100 text-emerald-800' : ($v->status == 'in_delivery' ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ str_replace('_', ' ', $v->status) }}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-1">
                                <button onclick="document.getElementById('editVehicleModal_{{ $v->id }}').classList.remove('hidden')" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg font-bold" title="Edit Armada">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('vehicles.destroy', $v->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus armada kendaraan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg" title="Hapus Armada">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal per Vehicle -->
                        <div id="editVehicleModal_{{ $v->id }}" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
                            <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl text-left max-h-[90vh] overflow-y-auto">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                    <h3 class="font-extrabold text-slate-900 text-sm">Edit Armada {{ $v->plate_number }}</h3>
                                    <button onclick="document.getElementById('editVehicleModal_{{ $v->id }}').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                                        <i class="fa-solid fa-xmark text-lg"></i>
                                    </button>
                                </div>
                                <form action="{{ route('vehicles.update', $v->id) }}" method="POST" class="space-y-4">
                                    @csrf
                                    @method('PUT')

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Kendaraan *</label>
                                            <input type="text" name="vehicle_type" value="{{ $v->vehicle_type }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kapasitas Muat (kg) *</label>
                                            <input type="number" name="capacity_kg" value="{{ $v->capacity_kg }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kepemilikan Armada *</label>
                                        <select name="ownership_type" id="edit_ownership_{{ $v->id }}" onchange="toggleEditAssetSection({{ $v->id }})" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                                            <option value="company" {{ $v->ownership_type == 'company' ? 'selected' : '' }}>Milik Perusahaan (Tercatat Sebagai Aset)</option>
                                            <option value="personal" {{ $v->ownership_type == 'personal' ? 'selected' : '' }}>Milik Sendiri (Kendaraan Pribadi Kurir)</option>
                                        </select>
                                    </div>

                                    <div id="edit_asset_section_{{ $v->id }}" class="space-y-3 p-3.5 rounded-xl bg-indigo-50/60 border border-indigo-100 {{ $v->ownership_type == 'personal' ? 'hidden' : '' }}">
                                        <div>
                                            <label class="block text-xs font-bold text-indigo-900 uppercase tracking-wider mb-1">Nilai Perolehan Aset (Rp)</label>
                                            <input type="number" name="asset_value" value="{{ $v->asset_value }}" min="0" step="100000" class="w-full px-3 py-2 rounded-xl border border-indigo-200 text-xs font-black text-indigo-700">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-indigo-900 uppercase tracking-wider mb-1">Akun Sumber Dana / Pembayaran</label>
                                            <select name="funding_account_id" class="w-full px-3 py-2 rounded-xl border border-indigo-200 text-xs font-semibold text-slate-800">
                                                @foreach($fundingAccounts as $acc)
                                                    <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }} ({{ $acc->account_type }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Operational *</label>
                                        <select name="status" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                                            <option value="active" {{ $v->status == 'active' ? 'selected' : '' }}>Active (Siap Pakai)</option>
                                            <option value="in_delivery" {{ $v->status == 'in_delivery' ? 'selected' : '' }}>In Delivery (Sedang Pengantaran)</option>
                                            <option value="maintenance" {{ $v->status == 'maintenance' ? 'selected' : '' }}>Maintenance (Perbaikan Service)</option>
                                        </select>
                                    </div>

                                    <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                                        <button type="button" onclick="document.getElementById('editVehicleModal_{{ $v->id }}').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs">Simpan Perubahan Armada</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 font-semibold">
                                Tidak ada data armada ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vehicles->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $vehicles->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Create -->
<div id="newVehicleModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-truck"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Tambah Kendaraan Baru</h3>
                    <p class="text-[11px] text-slate-500">Input armada & catat otomatis aset ke modul Akuntansi</p>
                </div>
            </div>
            <button onclick="document.getElementById('newVehicleModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form action="{{ route('vehicles.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Plat Nomor Kendaraan *</label>
                <input type="text" name="plate_number" required placeholder="Contoh: B 9123 LGS" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 uppercase">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Kendaraan *</label>
                    <input type="text" name="vehicle_type" required placeholder="Contoh: Blind Van Gran Max" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kapasitas Muat (kg) *</label>
                    <input type="number" name="capacity_kg" value="1000" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kepemilikan Armada *</label>
                <select name="ownership_type" id="create_ownership_select" onchange="toggleCreateAssetSection(this.value)" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="company" selected>Milik Perusahaan (Tercatat Sebagai Aset Akuntansi)</option>
                    <option value="personal">Milik Sendiri (Kendaraan Pribadi Kurir)</option>
                </select>
            </div>

            <!-- Integrasi Akuntansi: Pencatatan Aset Otomatis -->
            <div id="create_asset_section" class="space-y-3 p-3.5 rounded-xl bg-indigo-50/60 border border-indigo-100">
                <div class="flex items-center gap-1.5 text-xs font-black text-indigo-900 uppercase tracking-wider">
                    <i class="fa-solid fa-coins text-indigo-600"></i> Integrasi Aset Akuntansi (Otomatis Jurnal)
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nilai Perolehan Aset (Rp)</label>
                    <input type="number" name="asset_value" value="0" min="0" step="100000" placeholder="Contoh: 165000000" class="w-full px-3 py-2 rounded-xl border border-indigo-200 text-xs font-black text-indigo-700">
                    <p class="text-[10px] text-slate-400 mt-0.5">Akan otomatis mendebet akun <span class="font-bold text-indigo-600">1201 - Armada Kendaraan & Inventaris</span>.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Sumber Dana Pembelian / Modal</label>
                    <select name="funding_account_id" class="w-full px-3 py-2 rounded-xl border border-indigo-200 text-xs font-semibold text-slate-800">
                        @foreach($fundingAccounts as $acc)
                            <option value="{{ $acc->id }}" {{ $acc->account_code == '1101' ? 'selected' : '' }}>
                                {{ $acc->account_code }} - {{ $acc->account_name }} ({{ $acc->account_type }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-400 mt-0.5">Akun kas, bank, atau ekuitas yang dikredit.</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Operational *</label>
                <select name="status" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="active">Active (Siap Pakai)</option>
                    <option value="maintenance">Maintenance (Perbaikan Service)</option>
                </select>
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('newVehicleModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs">Simpan Kendaraan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleCreateAssetSection(val) {
        const sec = document.getElementById('create_asset_section');
        if (val === 'company') {
            sec.classList.remove('hidden');
        } else {
            sec.classList.add('hidden');
        }
    }

    function toggleEditAssetSection(id) {
        const sel = document.getElementById(`edit_ownership_${id}`);
        const sec = document.getElementById(`edit_asset_section_${id}`);
        if (sel && sec) {
            if (sel.value === 'company') {
                sec.classList.remove('hidden');
            } else {
                sec.classList.add('hidden');
            }
        }
    }
</script>
@endpush
@endsection
