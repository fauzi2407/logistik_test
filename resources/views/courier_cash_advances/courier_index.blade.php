@extends('layouts.app')

@section('title', 'Pengajuan Kasbon Kurir')

@section('content')
<div class="max-w-4xl mx-auto space-y-5 md:space-y-6">
    <!-- Header Banner -->
    <div class="p-5 md:p-6 rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-13 h-13 rounded-2xl bg-amber-500/20 border border-amber-400/30 flex items-center justify-center text-amber-300 text-2xl font-bold shrink-0 shadow-inner p-3">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        Portal Kurir
                    </span>
                    <span class="text-xs font-mono font-bold text-slate-300">{{ $loggedInCourier->courier_code ?? 'KUR' }}</span>
                </div>
                <h1 class="text-xl md:text-2xl font-black text-white tracking-tight mt-0.5">Kasbon & Uang Muka</h1>
                <p class="text-[11px] text-slate-400">Pengajuan kasbon darurat dan riwayat pemotongan gaji Anda</p>
            </div>
        </div>

        <button onclick="openModal('createKasbonModal')" class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs shadow-lg shadow-amber-500/20 hover:scale-[1.02] active:scale-[0.98] transition flex items-center justify-center gap-2">
            <i class="fa-solid fa-plus-circle text-sm"></i>
            <span>Ajukan Kasbon Baru</span>
        </button>
    </div>

    <!-- Stats Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 md:gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="text-[10px] uppercase font-extrabold text-slate-400 tracking-wider">Total Diajukan</div>
            <div class="text-base md:text-lg font-black text-slate-800 mt-1">Rp {{ number_format($totalRequested, 0, ',', '.') }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5 font-medium">{{ count($advances) }} Permohonan</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-amber-200/80 shadow-sm bg-amber-50/30">
            <div class="text-[10px] uppercase font-extrabold text-amber-700 tracking-wider">Menunggu Admin</div>
            <div class="text-base md:text-lg font-black text-amber-600 mt-1">Rp {{ number_format($totalPending, 0, ',', '.') }}</div>
            <div class="text-[10px] text-amber-600/80 mt-0.5 font-medium">{{ $advances->where('status', 'pending')->count() }} Pengajuan Pending</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-emerald-200/80 shadow-sm bg-emerald-50/30">
            <div class="text-[10px] uppercase font-extrabold text-emerald-700 tracking-wider">Disetujui (Aktif)</div>
            <div class="text-base md:text-lg font-black text-emerald-600 mt-1">Rp {{ number_format($totalApproved, 0, ',', '.') }}</div>
            <div class="text-[10px] text-emerald-600/80 mt-0.5 font-medium">{{ $advances->where('status', 'approved')->count() }} Belum Dipotong</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-blue-200/80 shadow-sm bg-blue-50/30">
            <div class="text-[10px] uppercase font-extrabold text-blue-700 tracking-wider">Lunas (Dipotong)</div>
            <div class="text-base md:text-lg font-black text-blue-600 mt-1">Rp {{ number_format($totalSettled, 0, ',', '.') }}</div>
            <div class="text-[10px] text-blue-600/80 mt-0.5 font-medium">{{ $advances->where('status', 'settled')->count() }} Pada Slip Gaji</div>
        </div>
    </div>

    <!-- Flash Error / Info if any -->
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 font-bold text-xs flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <!-- Kasbon History List -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 md:p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-xs md:text-sm uppercase tracking-wider">Riwayat Pengajuan Kasbon</h3>
                    <p class="text-[11px] text-slate-400">Daftar semua permohonan kasbon dan status verifikasi</p>
                </div>
            </div>
            <span class="text-xs font-bold text-slate-500">{{ count($advances) }} Pengajuan</span>
        </div>

        @if($advances->isEmpty())
            <div class="p-10 text-center space-y-3">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <h4 class="font-black text-slate-700 text-sm">Belum Ada Pengajuan Kasbon</h4>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">Anda belum pernah mengajukan pinjaman kasbon. Klik tombol di bawah untuk mengajukan permohonan baru.</p>
                <button onclick="openModal('createKasbonModal')" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition">
                    Ajukan Kasbon Sekarang
                </button>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($advances as $adv)
                    <div class="p-4 md:p-5 hover:bg-slate-50/70 transition space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex items-center flex-wrap gap-2">
                                <span class="font-mono font-black text-slate-900 text-xs md:text-sm">{{ $adv->advance_number }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase border {{ $adv->status_badge_class }}">
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
                                <span class="text-[11px] text-slate-400">
                                    <i class="fa-regular fa-calendar mr-1"></i> {{ $adv->request_date ? \Carbon\Carbon::parse($adv->request_date)->format('d M Y') : $adv->created_at->format('d M Y') }}
                                </span>
                            </div>

                            <div class="text-left sm:text-right">
                                <div class="text-base md:text-lg font-black text-indigo-700 font-mono">
                                    Rp {{ number_format($adv->amount, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <!-- Keperluan / Alasan -->
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-700">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Keperluan:</div>
                            <p class="font-medium whitespace-pre-line">{{ $adv->reason }}</p>
                        </div>

                        <!-- Approval Note or Rejection Reason -->
                        @if($adv->approval_notes)
                            <div class="p-3 rounded-2xl text-xs {{ $adv->status === 'rejected' ? 'bg-rose-50 border border-rose-200 text-rose-800' : 'bg-emerald-50 border border-emerald-200 text-emerald-800' }}">
                                <div class="text-[10px] font-extrabold uppercase tracking-wider mb-0.5 flex items-center gap-1.5">
                                    <i class="fa-solid {{ $adv->status === 'rejected' ? 'fa-circle-xmark text-rose-600' : 'fa-circle-check text-emerald-600' }}"></i>
                                    <span>Catatan Admin {{ $adv->approver ? '(' . $adv->approver->name . ')' : '' }}:</span>
                                </div>
                                <p class="font-semibold">{{ $adv->approval_notes }}</p>
                                @if($adv->approved_at)
                                    <div class="text-[10px] text-slate-400 mt-1">
                                        Waktu: {{ \Carbon\Carbon::parse($adv->approved_at)->translatedFormat('d F Y, H:i') }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Footer Actions -->
                        <div class="flex items-center justify-between pt-1 text-xs">
                            <div class="text-[11px] text-slate-400">
                                @if($adv->status === 'settled' && $adv->payroll)
                                    <span class="text-blue-600 font-semibold">
                                        <i class="fa-solid fa-receipt mr-1"></i> Dipotong pada Slip Gaji: {{ $adv->payroll->payroll_code }}
                                    </span>
                                @elseif($adv->status === 'approved')
                                    <span class="text-emerald-600 font-semibold">
                                        <i class="fa-solid fa-check-double mr-1"></i> Siap dicairkan / dipotong pada periode penggajian mendatang
                                    </span>
                                @elseif($adv->status === 'pending')
                                    <span class="text-amber-600 font-semibold">
                                        <i class="fa-solid fa-clock mr-1"></i> Menunggu review dan persetujuan dari Admin
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                @if($adv->status === 'approved' || $adv->status === 'settled')
                                    <a href="{{ route('courier-cash-advances.print', $adv->id) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5">
                                        <i class="fa-solid fa-print"></i> Cetak Bukti
                                    </a>
                                @endif

                                @if($adv->status === 'pending')
                                    <form action="{{ route('courier-cash-advances.destroy', $adv->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan kasbon {{ $adv->advance_number }} sebesar Rp {{ number_format($adv->amount, 0, ',', '.') }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition flex items-center gap-1.5 border border-rose-200">
                                            <i class="fa-solid fa-trash-can"></i> Batalkan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Modal Form Ajukan Kasbon Baru -->
<div id="createKasbonModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
        <div class="p-5 bg-gradient-to-r from-slate-900 to-indigo-950 text-white flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-300 flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm md:text-base">Pengajuan Kasbon Kurir</h3>
                    <p class="text-[11px] text-slate-300">{{ Auth::user()->name }} ({{ $loggedInCourier->courier_code ?? 'KUR' }})</p>
                </div>
            </div>
            <button onclick="closeModal('createKasbonModal')" class="text-slate-400 hover:text-white p-1 rounded-lg">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('courier-cash-advances.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <!-- Nominal Kasbon -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nominal Kasbon (Rp) *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-black text-sm">Rp</span>
                    <input type="number" id="inputAmount" name="amount" required min="10000" step="5000" placeholder="Contoh: 500000" oninput="updateAmountPreview(this.value)" class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm font-black text-slate-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
                </div>
                <div id="amountPreview" class="text-xs font-bold text-amber-600 mt-1 hidden">
                    Terbilang perkiraan: <span id="amountPreviewText"></span>
                </div>

                <!-- Quick amount selection chips -->
                <div class="flex items-center flex-wrap gap-1.5 mt-2">
                    <span class="text-[10px] text-slate-400 font-bold mr-1">Pilihan Cepat:</span>
                    <button type="button" onclick="setAmount(100000)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-amber-100 text-slate-700 hover:text-amber-900 font-bold text-[10px] transition">Rp 100 rb</button>
                    <button type="button" onclick="setAmount(200000)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-amber-100 text-slate-700 hover:text-amber-900 font-bold text-[10px] transition">Rp 200 rb</button>
                    <button type="button" onclick="setAmount(500000)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-amber-100 text-slate-700 hover:text-amber-900 font-bold text-[10px] transition">Rp 500 rb</button>
                    <button type="button" onclick="setAmount(1000000)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-amber-100 text-slate-700 hover:text-amber-900 font-bold text-[10px] transition">Rp 1 Juta</button>
                </div>
            </div>

            <!-- Tanggal Pengajuan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Permohonan *</label>
                <input type="date" name="request_date" required value="{{ date('Y-m-d') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-amber-500 outline-none">
            </div>

            <!-- Alasan / Keperluan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alasan / Keperluan Kasbon *</label>
                <textarea name="reason" rows="3" required placeholder="Jelaskan kebutuhan pengajuan kasbon (contoh: Biaya servis motor dinas, kebutuhan darurat keluarga, dll.)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-amber-500 outline-none"></textarea>
                <p class="text-[10px] text-slate-400 mt-1">Berikan penjelasan yang jelas agar permohonan dapat cepat diverifikasi oleh Admin.</p>
            </div>

            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-1">
                <div class="font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info text-amber-600"></i> Ketentuan Kasbon:
                </div>
                <p class="text-[11px] text-amber-800">Kasbon yang telah disetujui akan otomatis dipotongkan pada perhitungan slip gaji bulan berjalan.</p>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <button type="button" onclick="closeModal('createKasbonModal')" class="px-4 py-2.5 rounded-xl border border-slate-300 font-bold text-xs text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Pengajuan
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

    function setAmount(val) {
        const input = document.getElementById('inputAmount');
        input.value = val;
        updateAmountPreview(val);
    }

    function updateAmountPreview(val) {
        const preview = document.getElementById('amountPreview');
        const text = document.getElementById('amountPreviewText');
        if (!val || val <= 0) {
            preview.classList.add('hidden');
            return;
        }
        preview.classList.remove('hidden');
        text.innerText = 'Rp ' + Number(val).toLocaleString('id-ID');
    }
</script>
@endpush
@endsection
