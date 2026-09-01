@extends('layouts.app')

@section('title', 'Master Data Vendor / Supplier')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Master Vendor / Supplier</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pengelolaan data supplier penyedia perlengkapan logistik, sparepart fleet, & peralatan gudang.</p>
        </div>
        <button onclick="document.getElementById('addVendorModal').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition">
            <i class="fa-solid fa-plus mr-1.5"></i> Tambah Vendor Supplier
        </button>
    </div>

    <!-- Search Bar -->
    <form action="{{ route('vendors.index') }}" method="GET" class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between gap-3 text-xs font-bold">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama vendor, kode, no hp, contact person..." class="px-3.5 py-2 rounded-xl border border-slate-300 w-full max-w-md">
        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition">
            <i class="fa-solid fa-magnifying-glass mr-1"></i> Cari Vendor
        </button>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-truck-field text-indigo-600 mr-2"></i> Daftar Vendor Supplier Terdaftar
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/70 text-slate-600 uppercase tracking-wider font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-4">Kode Vendor</th>
                        <th class="p-4">Nama Perusahaan / Supplier</th>
                        <th class="p-4">Kontak & Telepon</th>
                        <th class="p-4">Alamat Gudang / Kantor</th>
                        <th class="p-4">Info Rekening Bank</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($vendors as $v)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-black text-indigo-600 text-sm">
                                {{ $v->vendor_code }}
                            </td>
                            <td class="p-4">
                                <div class="font-extrabold text-slate-900 text-sm">{{ $v->name }}</div>
                                <div class="text-[11px] text-slate-400">PIC: <strong>{{ $v->contact_person ?: '-' }}</strong></div>
                            </td>
                            <td class="p-4 text-slate-700">
                                <div><i class="fa-solid fa-phone text-indigo-500 mr-1"></i> {{ $v->phone ?: '-' }}</div>
                                <div class="text-[11px] text-slate-400"><i class="fa-solid fa-envelope mr-1"></i> {{ $v->email ?: '-' }}</div>
                            </td>
                            <td class="p-4 text-slate-600 max-w-xs truncate">
                                {{ $v->address ?: '-' }}
                            </td>
                            <td class="p-4 text-slate-800">
                                @if($v->bank_name && $v->bank_account_number)
                                    <div class="font-bold">{{ $v->bank_name }}</div>
                                    <div class="font-mono text-indigo-600 font-bold text-[11px]">{{ $v->bank_account_number }}</div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($v->status === 'active')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="p-4 text-right space-x-1">
                                <button onclick="editVendor({{ json_encode($v) }})" class="p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-xs transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('vendors.destroy', $v->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus vendor {{ $v->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold text-xs transition">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">Belum ada vendor/supplier terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vendors->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $vendors->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Add Modal -->
<div id="addVendorModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Tambah Vendor / Supplier Baru</h3>
            <button onclick="document.getElementById('addVendorModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('vendors.store') }}" method="POST" class="space-y-4 text-xs font-semibold">
            @csrf
            <div>
                <label class="block text-slate-700 mb-1">Nama Perusahaan / Supplier *</label>
                <input type="text" name="name" required placeholder="Contoh: PT Anugerah Plastik Packing" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-700 mb-1">No. Telepon / Whatsapp</label>
                    <input type="text" name="phone" placeholder="0812-3456-7890" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Email Vendor</label>
                    <input type="email" name="email" placeholder="sales@vendor.com" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
                </div>
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Nama Contact Person (PIC)</label>
                <input type="text" name="contact_person" placeholder="Nama sales / penanggung jawab..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-700 mb-1">Bank Rekening</label>
                    <input type="text" name="bank_name" placeholder="BCA / Mandiri" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">No. Rekening Bank</label>
                    <input type="text" name="bank_account_number" placeholder="1234567890" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono text-slate-800">
                </div>
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Alamat Kantor / Gudang Vendor</label>
                <textarea name="address" rows="2" placeholder="Alamat lengkap supplier..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800"></textarea>
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Status Vendor *</label>
                <select name="status" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
                    <option value="active">Aktif</option>
                    <option value="inactive">Non-Aktif</option>
                </select>
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('addVendorModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-md">Simpan Vendor</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editVendorModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Edit Data Vendor</h3>
            <button onclick="document.getElementById('editVendorModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editVendorForm" method="POST" class="space-y-4 text-xs font-semibold">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-slate-700 mb-1">Nama Perusahaan / Supplier *</label>
                <input type="text" name="name" id="edit_vendor_name" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-700 mb-1">No. Telepon / Whatsapp</label>
                    <input type="text" name="phone" id="edit_vendor_phone" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Email Vendor</label>
                    <input type="email" name="email" id="edit_vendor_email" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
                </div>
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Nama Contact Person (PIC)</label>
                <input type="text" name="contact_person" id="edit_vendor_cp" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-700 mb-1">Bank Rekening</label>
                    <input type="text" name="bank_name" id="edit_vendor_bank_name" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">No. Rekening Bank</label>
                    <input type="text" name="bank_account_number" id="edit_vendor_bank_acc" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono text-slate-800">
                </div>
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Alamat Kantor / Gudang Vendor</label>
                <textarea name="address" id="edit_vendor_address" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800"></textarea>
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Status Vendor *</label>
                <select name="status" id="edit_vendor_status" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
                    <option value="active">Aktif</option>
                    <option value="inactive">Non-Aktif</option>
                </select>
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('editVendorModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-md">Update Vendor</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editVendor(v) {
        document.getElementById('editVendorForm').action = "/vendors/" + v.id;
        document.getElementById('edit_vendor_name').value = v.name;
        document.getElementById('edit_vendor_phone').value = v.phone || '';
        document.getElementById('edit_vendor_email').value = v.email || '';
        document.getElementById('edit_vendor_cp').value = v.contact_person || '';
        document.getElementById('edit_vendor_bank_name').value = v.bank_name || '';
        document.getElementById('edit_vendor_bank_acc').value = v.bank_account_number || '';
        document.getElementById('edit_vendor_address').value = v.address || '';
        document.getElementById('edit_vendor_status').value = v.status;
        document.getElementById('editVendorModal').classList.remove('hidden');
    }
</script>
@endsection
