@extends('layouts.app')

@section('title', 'Manajemen Menu Aplikasi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Manajemen Menu & Navigasi Aplikasi</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola daftar menu, icon FontAwesome, route name, dan urutan tampil sidebar.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('roles.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                <i class="fa-solid fa-shield-halved mr-1.5"></i> Kelola Role & Hak Akses
            </a>
            <button onclick="document.getElementById('newMenuModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
                <i class="fa-solid fa-plus mr-1.5"></i> Tambah Menu Baru
            </button>
        </div>
    </div>

    <!-- Menus Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-list text-indigo-600 mr-2"></i> Daftar Struktur Menu Active
            </h3>
            <span class="text-xs font-bold text-slate-500">Total: {{ count($menus) }} Menu</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/70 text-slate-600 uppercase tracking-wider font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-4 w-12 text-center">Urutan</th>
                        <th class="p-4">Icon & Judul Menu</th>
                        <th class="p-4">Route Name / URL Path</th>
                        <th class="p-4">Parent Menu</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($menus as $m)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 text-center font-bold text-indigo-600 font-mono">#{{ $m->sort_order }}</td>
                            <td class="p-4 font-bold text-slate-900">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center text-xs shadow-sm">
                                        <i class="{{ $m->icon }}"></i>
                                    </div>
                                    <span class="text-sm">{{ $m->title }}</span>
                                </div>
                            </td>
                            <td class="p-4 font-mono text-slate-600 font-semibold">{{ $m->route ?: '-' }}</td>
                            <td class="p-4 font-bold text-slate-700">
                                {{ $m->parent ? $m->parent->title : '-' }}
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $m->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $m->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-1">
                                <button onclick="openEditMenuModal({{ json_encode($m) }})" class="p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 font-bold" title="Edit Menu">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('menus.destroy', $m->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus menu \'{{ $m->title }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold" title="Hapus Menu">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Belum ada data menu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Menu Baru -->
<div id="newMenuModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm">Tambah Menu Aplikasi Baru</h3>
            <button onclick="document.getElementById('newMenuModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('menus.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Menu *</label>
                <input type="text" name="title" required placeholder="Contoh: Laporan Keuangan" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Route Name (opsional)</label>
                <input type="text" name="route" placeholder="Contoh: reports.index" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono text-slate-800">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Icon FontAwesome</label>
                    <input type="text" name="icon" value="fa-solid fa-circle" placeholder="fa-solid fa-box" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Urutan Tampil *</label>
                    <input type="number" name="sort_order" value="0" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Parent Menu (Grup Induk)</label>
                <select name="parent_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="">-- Tanpa Parent (Menu Utama) --</option>
                    @foreach($parentMenus as $pm)
                        <option value="{{ $pm->id }}">{{ $pm->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Aktif *</label>
                <select name="is_active" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="1">Aktif Tampil</option>
                    <option value="0">Sembunyikan (Non-Aktif)</option>
                </select>
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('newMenuModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md">Simpan Menu Baru</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Menu -->
<div id="editMenuModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm">Edit Data Menu</h3>
            <button onclick="document.getElementById('editMenuModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="editMenuForm" action="" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Menu *</label>
                <input type="text" name="title" id="editMenuTitle" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Route Name (opsional)</label>
                <input type="text" name="route" id="editMenuRoute" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono text-slate-800">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Icon FontAwesome</label>
                    <input type="text" name="icon" id="editMenuIcon" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Urutan Tampil *</label>
                    <input type="number" name="sort_order" id="editMenuSortOrder" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Parent Menu (Grup Induk)</label>
                <select name="parent_id" id="editMenuParentId" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="">-- Tanpa Parent (Menu Utama) --</option>
                    @foreach($parentMenus as $pm)
                        <option value="{{ $pm->id }}">{{ $pm->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Aktif *</label>
                <select name="is_active" id="editMenuIsActive" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="1">Aktif Tampil</option>
                    <option value="0">Sembunyikan (Non-Aktif)</option>
                </select>
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('editMenuModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openEditMenuModal(menu) {
        document.getElementById('editMenuForm').action = `/menus/${menu.id}`;
        document.getElementById('editMenuTitle').value = menu.title;
        document.getElementById('editMenuRoute').value = menu.route || '';
        document.getElementById('editMenuIcon').value = menu.icon || '';
        document.getElementById('editMenuSortOrder').value = menu.sort_order;
        document.getElementById('editMenuParentId').value = menu.parent_id || '';
        document.getElementById('editMenuIsActive').value = menu.is_active ? "1" : "0";
        document.getElementById('editMenuModal').classList.remove('hidden');
    }
</script>
@endpush
@endsection
