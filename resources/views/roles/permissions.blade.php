@extends('layouts.app')

@section('title', 'Matriks Hak Akses CRUD - ' . $role->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $role->is_system ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800' }}">
                    {{ $role->name }}
                </span>
                <span class="text-xs text-slate-400 font-mono">({{ $role->slug }})</span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight mt-1">Matriks Pengaturan Hak Akses CRUD</h2>
            <p class="text-xs text-slate-500 mt-0.5">Centang akses Lihat (Read), Tambah (Create), Edit (Update), dan Hapus (Delete) untuk setiap menu aplikasi.</p>
        </div>
        <a href="{{ route('roles.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Daftar Role
        </a>
    </div>

    @if($role->slug === 'admin')
        <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-900 text-xs font-semibold flex items-center space-x-3">
            <i class="fa-solid fa-crown text-indigo-600 text-xl"></i>
            <div>
                <strong>Perhatian Super Admin:</strong> Role <strong>Administrator</strong> merupakan role tertinggi sistem dan secara otomatis memiliki hak akses penuh (CRUD) ke seluruh menu aplikasi.
            </div>
        </div>
    @endif

    <form action="{{ route('roles.permissions.update', $role->id) }}" method="POST">
        @csrf
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                    <i class="fa-solid fa-table-cells text-indigo-600 mr-2"></i> Matriks Akses Per Menu
                </h3>

                <div class="flex items-center space-x-4 text-xs font-bold text-slate-600">
                    <button type="button" onclick="toggleAllCheckboxes(true)" class="hover:text-indigo-600 transition">
                        <i class="fa-solid fa-check-double mr-1"></i> Pilih Semua
                    </button>
                    <span>•</span>
                    <button type="button" onclick="toggleAllCheckboxes(false)" class="hover:text-rose-600 transition">
                        <i class="fa-solid fa-xmark mr-1"></i> Hapus Semua
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100/70 text-slate-600 uppercase tracking-wider font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-4 w-12 text-center">#</th>
                            <th class="p-4">Nama Menu Aplikasi</th>
                            <th class="p-4">Route / Path</th>
                            <th class="p-4 text-center w-28 bg-indigo-50/60 text-indigo-900">
                                <label class="cursor-pointer inline-flex items-center space-x-1">
                                    <input type="checkbox" onchange="toggleColumn('view', this.checked)" class="rounded text-indigo-600">
                                    <span>Lihat (Read)</span>
                                </label>
                            </th>
                            <th class="p-4 text-center w-28 bg-emerald-50/60 text-emerald-900">
                                <label class="cursor-pointer inline-flex items-center space-x-1">
                                    <input type="checkbox" onchange="toggleColumn('create', this.checked)" class="rounded text-emerald-600">
                                    <span>Tambah (Create)</span>
                                </label>
                            </th>
                            <th class="p-4 text-center w-28 bg-amber-50/60 text-amber-900">
                                <label class="cursor-pointer inline-flex items-center space-x-1">
                                    <input type="checkbox" onchange="toggleColumn('edit', this.checked)" class="rounded text-amber-600">
                                    <span>Edit (Update)</span>
                                </label>
                            </th>
                            <th class="p-4 text-center w-28 bg-rose-50/60 text-rose-900">
                                <label class="cursor-pointer inline-flex items-center space-x-1">
                                    <input type="checkbox" onchange="toggleColumn('delete', this.checked)" class="rounded text-rose-600">
                                    <span>Hapus (Delete)</span>
                                </label>
                            </th>
                            <th class="p-4 text-center w-32 bg-purple-50/60 text-purple-900">
                                <label class="cursor-pointer inline-flex items-center space-x-1">
                                    <input type="checkbox" onchange="toggleColumn('view_salary', this.checked)" class="rounded text-purple-600">
                                    <span>Lihat Gaji</span>
                                </label>
                            </th>
                            <th class="p-4 text-center w-32 bg-fuchsia-50/60 text-fuchsia-900">
                                <label class="cursor-pointer inline-flex items-center space-x-1">
                                    <input type="checkbox" onchange="toggleColumn('edit_salary', this.checked)" class="rounded text-fuchsia-600">
                                    <span>Edit Gaji</span>
                                </label>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($menus as $menu)
                            @php
                                $perm = $existingPermissions[$menu->id] ?? null;
                                $canView = $perm ? $perm->can_view : true;
                                $canCreate = $perm ? $perm->can_create : true;
                                $canEdit = $perm ? $perm->can_edit : true;
                                $canDelete = $perm ? $perm->can_delete : false;
                                $canViewSalary = $perm ? $perm->can_view_salary : true;
                                $canEditSalary = $perm ? $perm->can_edit_salary : true;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-4 text-center text-slate-400 font-mono">{{ $loop->iteration }}</td>
                                <td class="p-4 font-bold text-slate-900">
                                    <div class="flex items-center space-x-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600 text-xs">
                                            <i class="{{ $menu->icon }}"></i>
                                        </div>
                                        <span>{{ $menu->title }}</span>
                                    </div>
                                </td>
                                <td class="p-4 font-mono text-slate-500 text-[11px]">{{ $menu->route ?: '-' }}</td>

                                <!-- Checkbox View -->
                                <td class="p-4 text-center bg-indigo-50/30">
                                    <input type="checkbox" name="permissions[{{ $menu->id }}][view]" value="1" {{ $canView ? 'checked' : '' }} class="perm-checkbox perm-view w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500 cursor-pointer">
                                </td>

                                <!-- Checkbox Create -->
                                <td class="p-4 text-center bg-emerald-50/30">
                                    <input type="checkbox" name="permissions[{{ $menu->id }}][create]" value="1" {{ $canCreate ? 'checked' : '' }} class="perm-checkbox perm-create w-4 h-4 rounded text-emerald-600 border-slate-300 focus:ring-emerald-500 cursor-pointer">
                                </td>

                                <!-- Checkbox Edit -->
                                <td class="p-4 text-center bg-amber-50/30">
                                    <input type="checkbox" name="permissions[{{ $menu->id }}][edit]" value="1" {{ $canEdit ? 'checked' : '' }} class="perm-checkbox perm-edit w-4 h-4 rounded text-amber-600 border-slate-300 focus:ring-amber-500 cursor-pointer">
                                </td>

                                <!-- Checkbox Delete -->
                                <td class="p-4 text-center bg-rose-50/30">
                                    <input type="checkbox" name="permissions[{{ $menu->id }}][delete]" value="1" {{ $canDelete ? 'checked' : '' }} class="perm-checkbox perm-delete w-4 h-4 rounded text-rose-600 border-slate-300 focus:ring-rose-500 cursor-pointer">
                                </td>

                                <!-- Checkbox View Salary -->
                                <td class="p-4 text-center bg-purple-50/30">
                                    <input type="checkbox" name="permissions[{{ $menu->id }}][view_salary]" value="1" {{ $canViewSalary ? 'checked' : '' }} class="perm-checkbox perm-view_salary w-4 h-4 rounded text-purple-600 border-slate-300 focus:ring-purple-500 cursor-pointer" title="Akses melihat Gaji & Komisi">
                                </td>

                                <!-- Checkbox Edit Salary -->
                                <td class="p-4 text-center bg-fuchsia-50/30">
                                    <input type="checkbox" name="permissions[{{ $menu->id }}][edit_salary]" value="1" {{ $canEditSalary ? 'checked' : '' }} class="perm-checkbox perm-edit_salary w-4 h-4 rounded text-fuchsia-600 border-slate-300 focus:ring-fuchsia-500 cursor-pointer" title="Akses mengubah Gaji & Komisi">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('roles.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-300 transition">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg shadow-indigo-600/30 transition">
                    <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan Matriks Hak Akses Role
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function toggleColumn(type, isChecked) {
        const checkboxes = document.querySelectorAll(`.perm-${type}`);
        checkboxes.forEach(cb => cb.checked = isChecked);
    }

    function toggleAllCheckboxes(isChecked) {
        const checkboxes = document.querySelectorAll('.perm-checkbox');
        checkboxes.forEach(cb => cb.checked = isChecked);
    }
</script>
@endpush
@endsection
