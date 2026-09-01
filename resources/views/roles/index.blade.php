@extends('layouts.app')

@section('title', 'Manajemen Role & Hak Akses')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Manajemen Role & Hak Akses Sistem</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola role pengguna dan konfigurasikan matriks hak akses CRUD (View, Create, Edit, Delete) per menu.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('menus.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                <i class="fa-solid fa-list-check mr-1.5"></i> Kelola Struktur Menu
            </a>
            <button onclick="document.getElementById('newRoleModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
                <i class="fa-solid fa-plus mr-1.5"></i> Tambah Role Baru
            </button>
        </div>
    </div>

    <!-- Roles Grid Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($roles as $role)
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 flex flex-col justify-between space-y-4 hover:border-indigo-300 transition">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $role->is_system ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800' }}">
                            {{ $role->is_system ? 'System Role' : 'Custom Role' }}
                        </span>
                        <span class="text-[11px] text-slate-400 font-mono font-semibold">slug: {{ $role->slug }}</span>
                    </div>

                    <h3 class="text-base font-extrabold text-slate-900 leading-snug">{{ $role->name }}</h3>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $role->description ?: 'Tidak ada deskripsi.' }}</p>
                </div>

                <div class="pt-3 border-t border-slate-100 space-y-2">
                    <a href="{{ route('roles.permissions', $role->id) }}" class="w-full py-2 px-3 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center space-x-1.5 transition">
                        <i class="fa-solid fa-shield-halved text-sm"></i>
                        <span>Atur Matriks CRUD</span>
                    </a>

                    <div class="flex items-center justify-end space-x-2 pt-1">
                        <button onclick="openEditRoleModal({{ json_encode($role) }})" class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-slate-100 font-bold text-xs" title="Edit Role">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        @if(!$role->is_system)
                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus role \'{{ $role->name }}\'?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 font-bold text-xs" title="Hapus Role">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Modal Tambah Role Baru -->
<div id="newRoleModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm">Tambah Role Pengguna Baru</h3>
            <button onclick="document.getElementById('newRoleModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('roles.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Role *</label>
                <input type="text" name="name" required placeholder="Contoh: Manager Operasional" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Role</label>
                <textarea name="description" rows="3" placeholder="Jelaskan cakupan wewenang role ini..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800"></textarea>
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('newRoleModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md">Simpan & Atur Hak Akses</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Role -->
<div id="editRoleModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm">Edit Data Role</h3>
            <button onclick="document.getElementById('editRoleModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="editRoleForm" action="" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Role *</label>
                <input type="text" name="name" id="editRoleName" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Role</label>
                <textarea name="description" id="editRoleDescription" rows="3" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800"></textarea>
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('editRoleModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openEditRoleModal(role) {
        document.getElementById('editRoleForm').action = `/roles/${role.id}`;
        document.getElementById('editRoleName').value = role.name;
        document.getElementById('editRoleDescription').value = role.description || '';
        document.getElementById('editRoleModal').classList.remove('hidden');
    }
</script>
@endpush
@endsection
