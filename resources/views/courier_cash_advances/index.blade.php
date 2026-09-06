@extends('layouts.app')

@section('title', 'Manajemen Kasbon Kurir')

@section('content')
<div class="space-y-6">
    <!-- Top Action & Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800 border border-amber-200">
                    Operasional & Keuangan
                </span>
                <span class="text-xs text-slate-400 font-bold">•</span>
                <span class="text-xs font-bold text-slate-500">Persetujuan Uang Muka</span>
            </div>
            <h1 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight mt-1 flex items-center gap-2.5">
                <i class="fa-solid fa-hand-holding-dollar text-amber-500"></i>
                <span>Kasbon & Pinjaman Kurir</span>
            </h1>
            <p class="text-xs text-slate-500">Kelola permohonan kasbon, persetujuan (approve/reject), dan integrasi pemotongan slip gaji kurir</p>
        </div>

        <button onclick="openModal('createAdminKasbonModal')" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md hover:shadow-lg transition flex items-center justify-center gap-2">
            <i class="fa-solid fa-plus text-amber-400"></i>
            <span>Tambah Kasbon Manual</span>
        </button>
    </div>

    <!-- Stats Overview Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Pending Card -->
        <div class="bg-white p-4 md:p-5 rounded-2xl border border-amber-200 shadow-sm relative overflow-hidden bg-gradient-to-br from-amber-50/50 via-white to-white">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700">Menunggu Approval</span>
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
            </div>
            <div class="text-xl md:text-2xl font-black text-amber-600 font-mono mt-1.5">
                {{ $statTotalPending }} <span class="text-xs font-bold text-slate-500">Pengajuan</span>
            </div>
            <div class="text-xs font-black text-slate-700 mt-1 font-mono">
                Rp {{ number_format($statTotalPendingAmount, 0, ',', '.') }}
            </div>
        </div>

        <!-- Approved (Active) Card -->
        <div class="bg-white p-4 md:p-5 rounded-2xl border border-emerald-200 shadow-sm bg-gradient-to-br from-emerald-50/50 via-white to-white">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700">Disetujui (Aktif)</span>
                <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
            </div>
            <div class="text-xl md:text-2xl font-black text-emerald-600 font-mono mt-1.5">
                {{ $statTotalApproved }} <span class="text-xs font-bold text-slate-500">Disetujui</span>
            </div>
            <div class="text-xs font-black text-slate-700 mt-1 font-mono">
                Rp {{ number_format($statTotalApprovedAmount, 0, ',', '.') }}
            </div>
        </div>

        <!-- Settled Card -->
        <div class="bg-white p-4 md:p-5 rounded-2xl border border-blue-200 shadow-sm bg-gradient-to-br from-blue-50/50 via-white to-white">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-blue-700">Lunas Dipotong Gaji</span>
                <i class="fa-solid fa-receipt text-blue-500 text-sm"></i>
            </div>
            <div class="text-xl md:text-2xl font-black text-blue-600 font-mono mt-1.5">
                <span class="text-xs font-bold text-slate-500">Total:</span> Rp {{ number_format($statTotalSettledAmount, 0, ',', '.') }}
            </div>
            <p class="text-[10px] text-slate-400 mt-1 font-medium">Sudah dipotong pada slip gaji kurir</p>
        </div>

        <!-- Rejected Card -->
        <div class="bg-white p-4 md:p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Ditolak</span>
                <i class="fa-solid fa-ban text-rose-500 text-sm"></i>
            </div>
            <div class="text-xl md:text-2xl font-black text-slate-700 font-mono mt-1.5">
                {{ $statTotalRejected }} <span class="text-xs font-bold text-slate-500">Pengajuan</span>
            </div>
            <p class="text-[10px] text-slate-400 mt-1 font-medium">Pengajuan tidak memenuhi syarat</p>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 md:p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
        <form method="GET" action="{{ route('courier-cash-advances.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
            <!-- Search -->
            <div class="md:col-span-2">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cari No. Kasbon / Nama Kurir</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik No. Kasbon atau nama kurir..." class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status</label>
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Menunggu Approval (Pending)</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Disetujui (Approved)</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Ditolak (Rejected)</option>
                    <option value="settled" {{ request('status') == 'settled' ? 'selected' : '' }}>🧾 Lunas Dipotong Gaji (Settled)</option>
                </select>
            </div>

            <!-- Kurir Filter -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pilih Kurir</label>
                <select name="courier_id" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="all">Semua Kurir</option>
                    @foreach($couriers as $c)
                        <option value="{{ $c->id }}" {{ request('courier_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->courier_code ?? 'KUR' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Action Filter Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                <a href="{{ route('courier-cash-advances.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 md:p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="font-extrabold text-slate-800 text-xs uppercase tracking-wider">Daftar Pengajuan Kasbon</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-100 text-slate-600">
                    {{ $advances->total() }} Data
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-black text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4">No. Kasbon / Tgl</th>
                        <th class="py-3.5 px-4">Kurir & Hub</th>
                        <th class="py-3.5 px-4">Nominal</th>
                        <th class="py-3.5 px-4">Keperluan / Alasan</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4">Info Persetujuan</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($advances as $adv)
                        <tr class="hover:bg-slate-50/80 transition {{ $adv->status === 'pending' ? 'bg-amber-50/20' : '' }}">
                            <!-- No Kasbon / Tgl -->
                            <td class="py-3.5 px-4 align-top">
                                <div class="font-mono font-black text-slate-900 text-xs">{{ $adv->advance_number }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    <i class="fa-regular fa-calendar mr-1"></i>{{ $adv->request_date ? \Carbon\Carbon::parse($adv->request_date)->format('d/m/Y') : $adv->created_at->format('d/m/Y') }}
                                </div>
                            </td>

                            <!-- Kurir & Hub -->
                            <td class="py-3.5 px-4 align-top">
                                <div class="font-bold text-slate-800 flex items-center gap-1.5">
                                    <i class="fa-solid fa-user text-indigo-500 text-[10px]"></i>
                                    <span>{{ $adv->courier->name ?? 'Kurir' }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                    {{ $adv->courier->courier_code ?? '-' }} • {{ $adv->courier->branchHub->name ?? 'Hub Utama' }}
                                </div>
                            </td>

                            <!-- Nominal -->
                            <td class="py-3.5 px-4 align-top">
                                <div class="font-mono font-black text-sm text-indigo-700">
                                    Rp {{ number_format($adv->amount, 0, ',', '.') }}
                                </div>
                            </td>

                            <!-- Keperluan / Alasan -->
                            <td class="py-3.5 px-4 align-top max-w-xs">
                                <div class="text-slate-700 font-medium line-clamp-2" title="{{ $adv->reason }}">
                                    {{ $adv->reason }}
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4 align-top text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase border {{ $adv->status_badge_class }}">
                                    @if($adv->status === 'pending')
                                        <i class="fa-solid fa-hourglass-half mr-1 animate-pulse"></i>
                                    @elseif($adv->status === 'approved')
                                        <i class="fa-solid fa-check mr-1"></i>
                                    @elseif($adv->status === 'rejected')
                                        <i class="fa-solid fa-ban mr-1"></i>
                                    @elseif($adv->status === 'settled')
                                        <i class="fa-solid fa-receipt mr-1"></i>
                                    @endif
                                    {{ $adv->status_label }}
                                </span>
                            </td>

                            <!-- Info Persetujuan -->
                            <td class="py-3.5 px-4 align-top text-[11px] max-w-xs">
                                @if($adv->approved_by || $adv->approval_notes)
                                    <div class="font-semibold text-slate-700">
                                        {{ $adv->approver->name ?? 'Admin' }}
                                    </div>
                                    @if($adv->approved_at)
                                        <div class="text-[10px] text-slate-400">
                                            {{ \Carbon\Carbon::parse($adv->approved_at)->format('d M Y, H:i') }}
                                        </div>
                                    @endif
                                    @if($adv->approval_notes)
                                        <div class="text-[10px] italic text-slate-500 mt-0.5 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                            "{{ $adv->approval_notes }}"
                                        </div>
                                    @endif
                                @elseif($adv->status === 'pending')
                                    <span class="text-amber-600 font-semibold text-[11px]">Menunggu Verifikasi</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif

                                @if($adv->status === 'settled' && $adv->payroll)
                                    <div class="text-[10px] text-blue-600 font-bold mt-1">
                                        <i class="fa-solid fa-receipt"></i> Slip Gaji: {{ $adv->payroll->payroll_code }}
                                    </div>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 align-top text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    @if($adv->status === 'pending')
                                        <!-- Tombol Setujui -->
                                        <button type="button" onclick="showApproveModal('{{ $adv->id }}', '{{ $adv->advance_number }}', '{{ $adv->courier->name }}', '{{ number_format($adv->amount, 0, ',', '.') }}')" class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[11px] shadow-sm transition flex items-center gap-1" title="Setujui Kasbon">
                                            <i class="fa-solid fa-check"></i> Setujui
                                        </button>

                                        <!-- Tombol Tolak -->
                                        <button type="button" onclick="showRejectModal('{{ $adv->id }}', '{{ $adv->advance_number }}', '{{ $adv->courier->name }}', '{{ number_format($adv->amount, 0, ',', '.') }}')" class="px-2.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-[11px] shadow-sm transition flex items-center gap-1" title="Tolak Kasbon">
                                            <i class="fa-solid fa-xmark"></i> Tolak
                                        </button>
                                    @endif

                                    <!-- Tombol Cetak Bukti -->
                                    <a href="{{ route('courier-cash-advances.print', $adv->id) }}" target="_blank" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 transition" title="Cetak Bukti Kasbon">
                                        <i class="fa-solid fa-print text-xs"></i>
                                    </a>

                                    @if($adv->status !== 'settled')
                                        <form action="{{ route('courier-cash-advances.destroy', $adv->id) }}" method="POST" onsubmit="return confirm('Hapus data pengajuan kasbon {{ $adv->advance_number }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-100 text-slate-400 hover:text-rose-600 transition" title="Hapus">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2 text-xl">
                                    <i class="fa-solid fa-inbox"></i>
                                </div>
                                <div class="font-bold text-slate-700 text-sm">Tidak ada data kasbon</div>
                                <p class="text-xs text-slate-400 mt-0.5">Belum ada data pengajuan kasbon yang sesuai filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($advances->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $advances->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Setujui (Approve) Kasbon -->
<div id="approveModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
        <div class="p-5 bg-gradient-to-r from-emerald-700 to-teal-800 text-white flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm md:text-base">Setujui Pengajuan Kasbon</h3>
                    <p id="approveAdvanceNumber" class="text-[11px] text-emerald-100 font-mono"></p>
                </div>
            </div>
            <button onclick="closeModal('approveModal')" class="text-white/80 hover:text-white p-1 rounded-lg">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="approveForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs">
                <div class="font-bold text-slate-700">Kurir: <span id="approveCourierName" class="font-black text-slate-900"></span></div>
                <div class="font-bold text-slate-700 mt-0.5">Nominal Kasbon: <span id="approveAmount" class="font-black text-emerald-700 text-sm font-mono"></span></div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Catatan Persetujuan (Opsional)</label>
                <textarea name="approval_notes" rows="2" placeholder="Contoh: Disetujui, dana dicairkan via kasir hub / transfer bank" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
            </div>

            <p class="text-[11px] text-slate-500">
                <i class="fa-solid fa-circle-info text-emerald-600 mr-1"></i> Setelah disetujui, kasbon ini siap dicairkan ke kurir dan akan otomatis tercatat sebagai potongan gaji.
            </p>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <button type="button" onclick="closeModal('approveModal')" class="px-4 py-2.5 rounded-xl border border-slate-300 font-bold text-xs text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-check-circle"></i> Ya, Setujui Kasbon
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tolak (Reject) Kasbon -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
        <div class="p-5 bg-gradient-to-r from-rose-700 to-red-800 text-white flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-ban"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm md:text-base">Tolak Pengajuan Kasbon</h3>
                    <p id="rejectAdvanceNumber" class="text-[11px] text-rose-100 font-mono"></p>
                </div>
            </div>
            <button onclick="closeModal('rejectModal')" class="text-white/80 hover:text-white p-1 rounded-lg">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="rejectForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs">
                <div class="font-bold text-slate-700">Kurir: <span id="rejectCourierName" class="font-black text-slate-900"></span></div>
                <div class="font-bold text-slate-700 mt-0.5">Nominal Kasbon: <span id="rejectAmount" class="font-black text-rose-700 text-sm font-mono"></span></div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alasan Penolakan *</label>
                <textarea name="approval_notes" rows="3" required placeholder="Tuliskan alasan penolakan agar kurir mengetahui alasannya (misal: Sisa plafon tidak mencukupi, dll.)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-rose-500 outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <button type="button" onclick="closeModal('rejectModal')" class="px-4 py-2.5 rounded-xl border border-slate-300 font-bold text-xs text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-ban"></i> Tolak Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Kasbon Manual oleh Admin -->
<div id="createAdminKasbonModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
        <div class="p-5 bg-gradient-to-r from-slate-900 to-indigo-950 text-white flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-300 flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm md:text-base">Input Kasbon Kurir (Admin)</h3>
                    <p class="text-[11px] text-slate-300">Buat permohonan kasbon langsung untuk kurir</p>
                </div>
            </div>
            <button onclick="closeModal('createAdminKasbonModal')" class="text-slate-400 hover:text-white p-1 rounded-lg">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('courier-cash-advances.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <!-- Pilih Kurir -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Kurir *</label>
                <select name="courier_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="">-- Pilih Kurir Penerima Kasbon --</option>
                    @foreach($couriers as $c)
                        <option value="{{ $c->id }}">
                            {{ $c->name }} ({{ $c->courier_code ?? 'KUR' }}) - Gaji Pokok: Rp {{ number_format($c->basic_salary ?? 0, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Nominal & Tanggal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nominal (Rp) *</label>
                    <input type="number" name="amount" required min="10000" step="5000" placeholder="500000" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-black text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal *</label>
                    <input type="date" name="request_date" required value="{{ date('Y-m-d') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
            </div>

            <!-- Alasan / Keperluan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Keperluan Kasbon *</label>
                <textarea name="reason" rows="2" required placeholder="Contoh: Pengajuan pinjaman darurat perbaikan kendaraan operasional" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <!-- Langsung Setujui Checkbox -->
            <div class="p-3.5 rounded-2xl bg-indigo-50/70 border border-indigo-200 space-y-2">
                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="direct_approve" value="1" checked class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                    <span class="text-xs font-bold text-indigo-950">Langsung Setujui Kasbon (Approved)</span>
                </label>
                <div class="text-[11px] text-slate-500 pl-6.5">
                    Jika dicentang, kasbon langsung berstatus <strong>Disetujui</strong> tanpa melewati tahap pending.
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <button type="button" onclick="closeModal('createAdminKasbonModal')" class="px-4 py-2.5 rounded-xl border border-slate-300 font-bold text-xs text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-check"></i> Simpan Kasbon
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function showApproveModal(id, number, name, amount) {
        document.getElementById('approveAdvanceNumber').innerText = number;
        document.getElementById('approveCourierName').innerText = name;
        document.getElementById('approveAmount').innerText = 'Rp ' + amount;
        
        const form = document.getElementById('approveForm');
        form.action = "{{ url('/courier-cash-advances') }}/" + id + "/approve";
        
        openModal('approveModal');
    }

    function showRejectModal(id, number, name, amount) {
        document.getElementById('rejectAdvanceNumber').innerText = number;
        document.getElementById('rejectCourierName').innerText = name;
        document.getElementById('rejectAmount').innerText = 'Rp ' + amount;
        
        const form = document.getElementById('rejectForm');
        form.action = "{{ url('/courier-cash-advances') }}/" + id + "/reject";
        
        openModal('rejectModal');
    }
</script>
@endpush
@endsection
