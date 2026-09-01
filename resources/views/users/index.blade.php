@extends('layouts.app')

@section('title', 'Manajemen Pengguna Aplikasi')

@section('content')
<div class="space-y-6">
    <!-- Header & Action Bar -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Manajemen Pengguna Aplikasi (User Management)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola akun pengguna aplikasi logistik untuk role Administrator, Staf Operasional, Kurir, dan Customer.</p>
        </div>
        <button onclick="document.getElementById('newUserModal').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition flex items-center justify-center space-x-1.5">
            <i class="fa-solid fa-user-plus"></i>
            <span>+ Tambah Pengguna Baru</span>
        </button>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cari Pengguna</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, no. HP..." class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Saring Berdasarkan Peran (Role)</label>
                <select name="role" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="">-- Semua Peran --</option>
                    <option value="admin" {{ $role == 'admin' ? 'selected' : '' }}>Administrator (Admin)</option>
                    <option value="staff" {{ $role == 'staff' ? 'selected' : '' }}>Staf Operasional Hub (Staff)</option>
                    <option value="courier" {{ $role == 'courier' ? 'selected' : '' }}>Kurir / Driver (Courier)</option>
                    <option value="customer" {{ $role == 'customer' ? 'selected' : '' }}>Customer / Klien (Customer)</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition">
                    <i class="fa-solid fa-filter mr-1"></i> Terapkan Filter
                </button>
                @if($search || $role)
                    <a href="{{ route('users.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-4">Pengguna</th>
                        <th class="p-4">Email & Kontak</th>
                        <th class="p-4">Peran (Role)</th>
                        <th class="p-4">Tanggal Dibuat</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-800 text-white font-bold flex items-center justify-center text-xs flex-shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-extrabold text-slate-900 text-sm">{{ $u->name }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">ID: #{{ $u->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-slate-700">
                                <div class="font-semibold text-slate-900"><i class="fa-solid fa-envelope text-slate-400 mr-1 text-[10px]"></i> {{ $u->email }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5"><i class="fa-solid fa-phone text-slate-400 mr-1 text-[10px]"></i> {{ $u->phone ?? '-' }}</div>
                            </td>
                            <td class="p-4">
                                @if($u->role === 'admin')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-purple-100 text-purple-800 border border-purple-200">
                                        <i class="fa-solid fa-user-shield mr-1"></i> Administrator
                                    </span>
                                @elseif($u->role === 'staff')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-blue-100 text-blue-800 border border-blue-200">
                                        <i class="fa-solid fa-user-gear mr-1"></i> Staf Operasional
                                    </span>
                                @elseif($u->role === 'courier')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fa-solid fa-truck-fast mr-1"></i> Kurir / Driver
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-800 border border-amber-200">
                                        <i class="fa-solid fa-building-user mr-1"></i> Customer
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-slate-500 font-medium">
                                {{ $u->created_at ? $u->created_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="p-4 text-center space-x-2">
                                <button onclick="openEditUserModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ addslashes($u->email) }}', '{{ addslashes($u->phone) }}', '{{ $u->role }}')" class="p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 font-bold transition" title="Edit Pengguna">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                
                                @if(Auth::id() !== $u->id)
                                    <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $u->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold transition" title="Hapus Pengguna">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="p-1.5 rounded-lg bg-slate-100 text-slate-400 font-bold cursor-not-allowed" title="Akun Anda Sendiri">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400">Tidak ada data pengguna aplikasi ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Modal 1: Tambah Pengguna Baru -->
<div id="newUserModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm flex items-center">
                <i class="fa-solid fa-user-plus text-indigo-600 mr-2"></i> Tambah Pengguna Aplikasi Baru
            </h3>
            <button onclick="document.getElementById('newUserModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-3 text-left">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap *</label>
                <input type="text" name="name" required placeholder="Contoh: Budi Gunawan" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Email *</label>
                <input type="email" name="email" required placeholder="budi@logistik.com" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor HP / WhatsApp *</label>
                <input type="text" name="phone" required placeholder="081234567890" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Peran Aplikasi (Role) *</label>
                <select name="role" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="admin">Administrator (Admin Akses Penuh)</option>
                    <option value="staff">Staf Operasional Hub (Staff)</option>
                    <option value="courier">Kurir / Driver Pengiriman (Courier)</option>
                    <option value="customer">Customer / Klien Pengirim (Customer)</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Password *</label>
                    <input type="password" name="password" required placeholder="Min 6 karakter" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Konfirmasi Password *</label>
                    <input type="password" name="password_confirmation" required placeholder="Ulangi password" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('newUserModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Edit Data Pengguna -->
<div id="editUserModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-sm flex items-center">
                <i class="fa-solid fa-user-pen text-amber-600 mr-2"></i> Edit Data Pengguna Aplikasi
            </h3>
            <button onclick="document.getElementById('editUserModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="editUserForm" method="POST" class="space-y-3 text-left">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap *</label>
                <input type="text" name="name" id="editUserName" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Email *</label>
                <input type="email" name="email" id="editUserEmail" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor HP / WhatsApp *</label>
                <input type="text" name="phone" id="editUserPhone" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Peran Aplikasi (Role) *</label>
                <select name="role" id="editUserRole" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                    <option value="admin">Administrator (Admin Akses Penuh)</option>
                    <option value="staff">Staf Operasional Hub (Staff)</option>
                    <option value="courier">Kurir / Driver Pengiriman (Courier)</option>
                    <option value="customer">Customer / Klien Pengirim (Customer)</option>
                </select>
            </div>

            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200/80 text-[11px] text-amber-800">
                <i class="fa-solid fa-circle-info text-amber-600 mr-1"></i> Kosongkan password di bawah jika Anda tidak ingin mengubah password pengguna ini.
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Password Baru (Opsional)</label>
                    <input type="password" name="password" placeholder="Password baru..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('editUserModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openEditUserModal(id, name, email, phone, role) {
        document.getElementById('editUserForm').action = "/users/" + id;
        document.getElementById('editUserName').value = name;
        document.getElementById('editUserEmail').value = email;
        document.getElementById('editUserPhone').value = phone;
        document.getElementById('editUserRole').value = role;
        document.getElementById('editUserModal').classList.remove('hidden');
    }
</script>
@endpush
@endsection
