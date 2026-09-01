@extends('layouts.app')

@section('title', 'Master Armada Kendaraan')

@section('content')
<div class="space-y-6">
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Master Armada Kendaraan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola data kendaraan truk, van, pickup, dan sepeda motor operasional logistik.</p>
        </div>
        <button onclick="document.getElementById('newVehicleModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
            <i class="fa-solid fa-plus mr-1.5"></i> Tambah Armada Baru
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-truck text-indigo-600 mr-1.5"></i> Daftar Armada Kendaraan
            </h3>
            <form method="GET" action="{{ route('vehicles.index') }}" class="flex items-center space-x-2">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="🔍 Cari plat nomor, jenis..." class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 w-56 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
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
                        <th class="p-4">Kapasitas Maksimal (kg)</th>
                        <th class="p-4">Status Operational</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($vehicles as $v)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-bold text-slate-900 text-sm">{{ $v->plate_number }}</td>
                            <td class="p-4 font-bold text-slate-700">{{ $v->vehicle_type }}</td>
                            <td class="p-4 text-slate-900 font-bold">{{ number_format($v->capacity_kg) }} kg</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $v->status == 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $v->status }}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-1">
                                <button onclick="document.getElementById('editVehicleModal_{{ $v->id }}').classList.remove('hidden')" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg font-bold" title="Edit Armada">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('vehicles.destroy', $v->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus armada kendaraan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal per Vehicle -->
                        <div id="editVehicleModal_{{ $v->id }}" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
                            <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl text-left">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                    <h3 class="font-extrabold text-slate-900 text-sm">Edit Armada {{ $v->plate_number }}</h3>
                                    <button onclick="document.getElementById('editVehicleModal_{{ $v->id }}').classList.add('hidden')" class="text-slate-400">
                                        <i class="fa-solid fa-xmark text-lg"></i>
                                    </button>
                                </div>
                                <form action="{{ route('vehicles.update', $v->id) }}" method="POST" class="space-y-4">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Kendaraan *</label>
                                        <input type="text" name="vehicle_type" value="{{ $v->vehicle_type }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kapasitas Muat (kg) *</label>
                                        <input type="number" name="capacity_kg" value="{{ $v->capacity_kg }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
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
                            <td colspan="5" class="p-8 text-center text-slate-400 font-semibold">
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
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm">Tambah Kendaraan Baru</h3>
            <button onclick="document.getElementById('newVehicleModal').classList.add('hidden')" class="text-slate-400">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form action="{{ route('vehicles.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Plat Nomor Kendaraan *</label>
                <input type="text" name="plate_number" required placeholder="Contoh: B 9123 LGS" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Kendaraan *</label>
                <input type="text" name="vehicle_type" required placeholder="Contoh: Truck Box Hino / Blind Van Gran Max" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kapasitas Muat (kg) *</label>
                <input type="number" name="capacity_kg" value="1000" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
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
@endsection
