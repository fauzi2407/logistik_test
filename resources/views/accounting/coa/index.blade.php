@extends('layouts.app')

@section('title', 'Master Chart of Accounts (COA)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Master Chart of Accounts (COA)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pengelolaan bagan akun standar akuntansi perusahaaan logistik.</p>
        </div>
        <button onclick="document.getElementById('addCoaModal').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition">
            <i class="fa-solid fa-plus mr-1.5"></i> Tambah Akun COA
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                <i class="fa-solid fa-list-ol text-indigo-600 mr-2"></i> Daftar Akun Keuangan (Total {{ count($accounts) }} Akun)
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/70 text-slate-600 uppercase tracking-wider font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-4">Kode Akun</th>
                        <th class="p-4">Nama Akun COA</th>
                        <th class="p-4">Tipe Akun</th>
                        <th class="p-4">Saldo Normal</th>
                        <th class="p-4">Keterangan</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($accounts as $acc)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-black text-indigo-600 text-sm">
                                {{ $acc->account_code }}
                            </td>
                            <td class="p-4">
                                <div class="font-extrabold text-slate-900 text-sm">{{ $acc->account_name }}</div>
                                @if($acc->parent)
                                    <div class="text-[11px] text-slate-400">Induk: {{ $acc->parent->account_code }} - {{ $acc->parent->account_name }}</div>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase border {{ $acc->type_badge_class }}">
                                    {{ $acc->account_type }}
                                </span>
                            </td>
                            <td class="p-4 font-extrabold {{ $acc->normal_balance === 'Debit' ? 'text-indigo-600' : 'text-emerald-600' }}">
                                {{ $acc->normal_balance }}
                            </td>
                            <td class="p-4 text-slate-500 max-w-xs truncate">
                                {{ $acc->description ?: '-' }}
                            </td>
                            <td class="p-4">
                                @if($acc->is_active)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="p-4 text-right space-x-1">
                                <button onclick="editCoa({{ json_encode($acc) }})" class="p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-xs transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('accounting.coa.destroy', $acc->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus akun COA {{ $acc->account_code }}?')">
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
                            <td colspan="7" class="p-8 text-center text-slate-400">Belum ada akun COA terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="addCoaModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Tambah Akun COA Baru</h3>
            <button onclick="document.getElementById('addCoaModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('accounting.coa.store') }}" method="POST" class="space-y-4 text-xs font-semibold">
            @csrf
            <div>
                <label class="block text-slate-700 mb-1">Kode Akun *</label>
                <input type="text" name="account_code" required placeholder="Contoh: 1105" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono font-bold text-slate-800">
            </div>
            <div>
                <label class="block text-slate-700 mb-1">Nama Akun COA *</label>
                <input type="text" name="account_name" required placeholder="Contoh: Bank Mandiri Transit" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-700 mb-1">Tipe Akun *</label>
                    <select name="account_type" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
                        <option value="Asset">Asset (Aktiva)</option>
                        <option value="Liability">Liability (Kewajiban)</option>
                        <option value="Equity">Equity (Modal)</option>
                        <option value="Revenue">Revenue (Pendapatan)</option>
                        <option value="Expense">Expense (Beban)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Saldo Normal *</label>
                    <select name="normal_balance" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
                        <option value="Debit">Debit</option>
                        <option value="Credit">Credit</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-slate-700 mb-1">Keterangan / Deskripsi</label>
                <input type="text" name="description" placeholder="Deskripsi akun..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
            </div>
            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('addCoaModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-md">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editCoaModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Edit Akun COA</h3>
            <button onclick="document.getElementById('editCoaModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editCoaForm" method="POST" class="space-y-4 text-xs font-semibold">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-slate-700 mb-1">Kode Akun *</label>
                <input type="text" name="account_code" id="edit_account_code" required class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono font-bold text-slate-800">
            </div>
            <div>
                <label class="block text-slate-700 mb-1">Nama Akun COA *</label>
                <input type="text" name="account_name" id="edit_account_name" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-700 mb-1">Tipe Akun *</label>
                    <select name="account_type" id="edit_account_type" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
                        <option value="Asset">Asset (Aktiva)</option>
                        <option value="Liability">Liability (Kewajiban)</option>
                        <option value="Equity">Equity (Modal)</option>
                        <option value="Revenue">Revenue (Pendapatan)</option>
                        <option value="Expense">Expense (Beban)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Saldo Normal *</label>
                    <select name="normal_balance" id="edit_normal_balance" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
                        <option value="Debit">Debit</option>
                        <option value="Credit">Credit</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-slate-700 mb-1">Keterangan / Deskripsi</label>
                <input type="text" name="description" id="edit_description" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-800">
            </div>
            <div class="flex items-center space-x-2">
                <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="rounded border-slate-300 text-indigo-600">
                <label for="edit_is_active" class="text-xs text-slate-700">Akun Aktif</label>
            </div>
            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('editCoaModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-md">Update Akun</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editCoa(acc) {
        document.getElementById('editCoaForm').action = "/accounting/coa/" + acc.id;
        document.getElementById('edit_account_code').value = acc.account_code;
        document.getElementById('edit_account_name').value = acc.account_name;
        document.getElementById('edit_account_type').value = acc.account_type;
        document.getElementById('edit_normal_balance').value = acc.normal_balance;
        document.getElementById('edit_description').value = acc.description || '';
        document.getElementById('edit_is_active').checked = acc.is_active;
        document.getElementById('editCoaModal').classList.remove('hidden');
    }
</script>
@endsection
