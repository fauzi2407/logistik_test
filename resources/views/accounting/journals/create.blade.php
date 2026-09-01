@extends('layouts.app')

@section('title', 'Buat Jurnal Umum Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Form Transaksi Jurnal Umum</h2>
            <p class="text-xs text-slate-500 mt-0.5">Input entri ganda (double-entry) transaksi keuangan perusahaan.</p>
        </div>
        <a href="{{ route('accounting.journals.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold text-xs transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('accounting.journals.store') }}" method="POST" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-semibold">
            <div>
                <label class="block text-slate-700 font-bold uppercase tracking-wider mb-1">Tanggal Transaksi *</label>
                <input type="date" name="entry_date" value="{{ old('entry_date', date('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-bold text-slate-800">
            </div>

            <div>
                <label class="block text-slate-700 font-bold uppercase tracking-wider mb-1">No. Referensi (Opsional)</label>
                <input type="text" name="reference_number" value="{{ old('reference_number') }}" placeholder="Contoh: INV-001 / AWB-889 / BKM-01" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-800">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi / Keterangan Transaksi *</label>
            <input type="text" name="description" value="{{ old('description') }}" required placeholder="Contoh: Penerimaan pembayaran invoice pengiriman PT ABC..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
        </div>

        <!-- Journal Entry Items Table -->
        <div class="space-y-3 pt-2">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                    <i class="fa-solid fa-list-check text-indigo-600 mr-1.5"></i> Baris Perkiraan Akun Jurnal
                </h3>
                <button type="button" onclick="addJournalRow()" class="px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold text-xs transition">
                    <i class="fa-solid fa-plus mr-1"></i> Tambah Baris
                </button>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-left text-xs" id="journalTable">
                    <thead class="bg-slate-100/80 text-slate-600 uppercase font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-3">Akun COA *</th>
                            <th class="p-3 w-44">Debit (Rp)</th>
                            <th class="p-3 w-44">Kredit (Rp)</th>
                            <th class="p-3">Memo / Catatan</th>
                            <th class="p-3 w-10 text-center">Hapus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium" id="journalRows">
                        <!-- Row 1 (Debit default) -->
                        <tr>
                            <td class="p-2">
                                <select name="items[0][chart_of_account_id]" required class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800 text-xs">
                                    <option value="">-- Pilih Akun --</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }} ({{ $acc->account_type }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="p-2">
                                <input type="number" step="0.01" min="0" name="items[0][debit_amount]" oninput="calculateTotals()" placeholder="0" class="debit-input w-full px-2.5 py-2 rounded-xl border border-slate-300 text-right font-mono font-bold text-slate-900 text-xs">
                            </td>
                            <td class="p-2">
                                <input type="number" step="0.01" min="0" name="items[0][credit_amount]" oninput="calculateTotals()" placeholder="0" class="credit-input w-full px-2.5 py-2 rounded-xl border border-slate-300 text-right font-mono font-bold text-slate-900 text-xs">
                            </td>
                            <td class="p-2">
                                <input type="text" name="items[0][memo]" placeholder="Memo baris..." class="w-full px-2.5 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs">
                            </td>
                            <td class="p-2 text-center">
                                <button type="button" onclick="removeRow(this)" class="text-rose-500 hover:text-rose-700 text-sm font-bold">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Row 2 (Credit default) -->
                        <tr>
                            <td class="p-2">
                                <select name="items[1][chart_of_account_id]" required class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800 text-xs">
                                    <option value="">-- Pilih Akun --</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }} ({{ $acc->account_type }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="p-2">
                                <input type="number" step="0.01" min="0" name="items[1][debit_amount]" oninput="calculateTotals()" placeholder="0" class="debit-input w-full px-2.5 py-2 rounded-xl border border-slate-300 text-right font-mono font-bold text-slate-900 text-xs">
                            </td>
                            <td class="p-2">
                                <input type="number" step="0.01" min="0" name="items[1][credit_amount]" oninput="calculateTotals()" placeholder="0" class="credit-input w-full px-2.5 py-2 rounded-xl border border-slate-300 text-right font-mono font-bold text-slate-900 text-xs">
                            </td>
                            <td class="p-2">
                                <input type="text" name="items[1][memo]" placeholder="Memo baris..." class="w-full px-2.5 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs">
                            </td>
                            <td class="p-2 text-center">
                                <button type="button" onclick="removeRow(this)" class="text-rose-500 hover:text-rose-700 text-sm font-bold">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-slate-50 font-bold border-t border-slate-200">
                        <tr>
                            <td class="p-3 text-right">TOTAL:</td>
                            <td class="p-3 text-right font-mono text-indigo-700 text-sm" id="displayTotalDebit">Rp 0</td>
                            <td class="p-3 text-right font-mono text-emerald-700 text-sm" id="displayTotalCredit">Rp 0</td>
                            <td colspan="2" class="p-3 font-semibold text-xs" id="displayBalanceStatus">
                                <span class="text-slate-400">Silakan isi nominal Debit & Kredit.</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="pt-4 flex justify-end space-x-2 border-t border-slate-100">
            <a href="{{ route('accounting.journals.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Batal</a>
            <button type="submit" id="submitBtn" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg">
                <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan Transaksi Jurnal
            </button>
        </div>
    </form>
</div>

<script>
    let rowIndex = 2;
    const accountOptionsHtml = `{!! addslashes(implode('', array_map(function($acc) { return "<option value='{$acc->id}'>{$acc->account_code} - {$acc->account_name} ({$acc->account_type})</option>"; }, $accounts->all()))) !!}`;

    function addJournalRow() {
        const tbody = document.getElementById('journalRows');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="p-2">
                <select name="items[${rowIndex}][chart_of_account_id]" required class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800 text-xs">
                    <option value="">-- Pilih Akun --</option>
                    ${accountOptionsHtml}
                </select>
            </td>
            <td class="p-2">
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][debit_amount]" oninput="calculateTotals()" placeholder="0" class="debit-input w-full px-2.5 py-2 rounded-xl border border-slate-300 text-right font-mono font-bold text-slate-900 text-xs">
            </td>
            <td class="p-2">
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][credit_amount]" oninput="calculateTotals()" placeholder="0" class="credit-input w-full px-2.5 py-2 rounded-xl border border-slate-300 text-right font-mono font-bold text-slate-900 text-xs">
            </td>
            <td class="p-2">
                <input type="text" name="items[${rowIndex}][memo]" placeholder="Memo baris..." class="w-full px-2.5 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs">
            </td>
            <td class="p-2 text-center">
                <button type="button" onclick="removeRow(this)" class="text-rose-500 hover:text-rose-700 text-sm font-bold">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        rowIndex++;
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('#journalRows tr');
        if (rows.length <= 2) {
            alert('Penjurnalan minimal terdiri dari 2 baris akun.');
            return;
        }
        btn.closest('tr').remove();
        calculateTotals();
    }

    function calculateTotals() {
        let totalDebit = 0;
        let totalCredit = 0;

        document.querySelectorAll('.debit-input').forEach(input => {
            totalDebit += parseFloat(input.value) || 0;
        });

        document.querySelectorAll('.credit-input').forEach(input => {
            totalCredit += parseFloat(input.value) || 0;
        });

        document.getElementById('displayTotalDebit').innerText = 'Rp ' + totalDebit.toLocaleString('id-ID');
        document.getElementById('displayTotalCredit').innerText = 'Rp ' + totalCredit.toLocaleString('id-ID');

        const statusBox = document.getElementById('displayBalanceStatus');
        const submitBtn = document.getElementById('submitBtn');

        const diff = Math.abs(totalDebit - totalCredit);
        if (totalDebit > 0 && diff < 0.01) {
            statusBox.innerHTML = '<span class="text-emerald-600 font-bold"><i class="fa-solid fa-circle-check mr-1"></i> Balance / Seimbang</span>';
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            statusBox.innerHTML = `<span class="text-rose-600 font-bold"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Belum Seimbang (Selisih: Rp ${diff.toLocaleString('id-ID')})</span>`;
        }
    }
</script>
@endsection
