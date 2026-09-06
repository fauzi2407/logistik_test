<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $appName = \App\Models\AppSetting::get('app_name', 'Radja Express Logistics');
        $appTagline = \App\Models\AppSetting::get('app_tagline', 'Logistics & Supply Chain Management System');
        $appPhone = \App\Models\AppSetting::get('company_phone', '021-7654321');
        $appEmail = \App\Models\AppSetting::get('company_email', 'info@logistik.com');
        $appAddress = \App\Models\AppSetting::get('company_address', 'Jl. Logistik Utama No. 88, Jakarta Selatan');
        $appLogoUrl = \App\Models\AppSetting::get('app_logo_url', null);
    @endphp
    <title>BUKTI KASBON - {{ $advance->advance_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            body { font-size: 11pt; background: #fff !important; }
            .no-print { display: none !important; }
            .print-border { border: 1px solid #cbd5e1 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans p-4 md:p-8 min-h-screen">

    <!-- Action Bar Print -->
    <div class="max-w-2xl mx-auto mb-4 flex items-center justify-between no-print">
        <a href="{{ route('courier-cash-advances.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-700">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Daftar Kasbon
        </a>
        <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg transition">
            <i class="fa-solid fa-print mr-1.5"></i> Cetak / Simpan PDF
        </button>
    </div>

    <!-- Official Voucher Card -->
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-xl border border-slate-200 print-border space-y-6">
        <!-- Header Company -->
        <div class="flex items-center justify-between border-b border-slate-200 pb-5">
            <div class="flex items-center space-x-3">
                @if($appLogoUrl)
                    <img src="{{ asset($appLogoUrl) }}" alt="Logo" class="w-12 h-12 object-contain">
                @else
                    <div class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center text-slate-950 font-black text-xl">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                @endif
                <div>
                    <h1 class="text-lg font-black text-slate-900 uppercase tracking-tight">{{ $appName }}</h1>
                    <p class="text-xs text-slate-500">{{ $appAddress }}</p>
                    <p class="text-xs text-slate-500">Telp: {{ $appPhone }} • Email: {{ $appEmail }}</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-[11px] font-extrabold uppercase tracking-wider text-amber-700">VOUCHER KASBON KURIR</div>
                <div class="text-sm font-black font-mono text-slate-900 mt-0.5">{{ $advance->advance_number }}</div>
                <div class="text-xs text-slate-500 mt-1">
                    Tanggal: <strong>{{ $advance->request_date ? \Carbon\Carbon::parse($advance->request_date)->format('d F Y') : $advance->created_at->format('d F Y') }}</strong>
                </div>
            </div>
        </div>

        <!-- Status Banner -->
        <div class="p-3.5 rounded-xl border flex items-center justify-between {{ $advance->status_badge_class }}">
            <div class="flex items-center space-x-2">
                <i class="fa-solid {{ $advance->status === 'approved' || $advance->status === 'settled' ? 'fa-circle-check text-emerald-600' : ($advance->status === 'rejected' ? 'fa-ban text-rose-600' : 'fa-hourglass-half text-amber-600') }} text-base"></i>
                <span class="font-extrabold uppercase text-xs">Status: {{ $advance->status_label }}</span>
            </div>
            @if($advance->status === 'settled' && $advance->payroll)
                <span class="text-[11px] font-bold text-blue-700">Dipotong pada Slip: {{ $advance->payroll->payroll_code }}</span>
            @endif
        </div>

        <!-- Courier Information -->
        <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs">
            <div>
                <div class="text-slate-400 font-bold uppercase text-[10px]">Nama Kurir / Driver:</div>
                <div class="font-extrabold text-slate-900 text-sm mt-0.5">{{ $advance->courier->name ?? 'Kurir' }}</div>
                <div class="font-mono text-indigo-600 font-bold mt-0.5">Kode: {{ $advance->courier->courier_code ?? '-' }}</div>
                <div class="text-slate-600 mt-0.5">No. Telepon: {{ $advance->courier->phone ?? '-' }}</div>
            </div>
            <div>
                <div class="text-slate-400 font-bold uppercase text-[10px]">Hub / Penempatan:</div>
                <div class="font-bold text-slate-800 mt-0.5">{{ $advance->courier->branchHub ? $advance->courier->branchHub->name : 'Hub Utama' }}</div>
                <div class="text-slate-600 mt-0.5">Armada: <strong>{{ $advance->courier->vehicle ? $advance->courier->vehicle->plate_number : 'Kendaraan Pribadi' }}</strong></div>
                <div class="text-slate-600 mt-0.5">Gaji Pokok: Rp {{ number_format($advance->courier->basic_salary ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Cash Advance Amount Details -->
        <div class="border border-slate-200 rounded-xl overflow-hidden">
            <div class="bg-slate-50 px-4 py-2.5 border-b border-slate-200 font-bold text-xs uppercase text-slate-700">
                Rincian Pinjaman Kasbon
            </div>
            <div class="p-4 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <span class="text-xs text-slate-600 font-semibold">Nominal Kasbon yang Diterima</span>
                    <span class="text-xl font-black font-mono text-indigo-700">
                        Rp {{ number_format($advance->amount, 0, ',', '.') }}
                    </span>
                </div>

                <div class="text-xs space-y-1">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Keperluan / Keterangan:</span>
                    <p class="font-medium text-slate-800 bg-slate-50 p-3 rounded-lg border border-slate-100 whitespace-pre-line">{{ $advance->reason }}</p>
                </div>

                @if($advance->approval_notes)
                    <div class="text-xs space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Catatan Persetujuan Admin:</span>
                        <p class="font-semibold text-slate-700 bg-emerald-50/50 p-2.5 rounded-lg border border-emerald-100 text-[11px]">{{ $advance->approval_notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Terms Note -->
        <div class="text-[10px] text-slate-500 bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-1">
            <div class="font-bold uppercase text-slate-700">Syarat & Ketentuan Pengambilan Kasbon:</div>
            <ul class="list-disc pl-4 space-y-0.5">
                <li>Kurir bersedia dipotong gajinya pada periode penggajian berjalan sejumlah nominal kasbon yang tercantum di atas.</li>
                <li>Bukti kasbon ini sah bila ditandatangani oleh pemohon dan disetujui oleh pejabat berwenang/manajemen.</li>
            </ul>
        </div>

        <!-- Signatures Area -->
        <div class="grid grid-cols-3 gap-4 text-center pt-4 text-xs">
            <div>
                <div class="text-[10px] text-slate-400 font-bold uppercase">Pemohon (Kurir)</div>
                <div class="h-16 flex items-end justify-center">
                    <span class="text-[10px] text-slate-300 italic">(Tanda Tangan)</span>
                </div>
                <div class="font-bold text-slate-900 border-t border-slate-300 pt-1 mt-1">
                    {{ $advance->courier->name ?? 'Kurir' }}
                </div>
                <div class="text-[10px] text-slate-400">{{ $advance->courier->courier_code ?? '' }}</div>
            </div>

            <div>
                <div class="text-[10px] text-slate-400 font-bold uppercase">Kasir / Keuangan</div>
                <div class="h-16 flex items-end justify-center">
                    <span class="text-[10px] text-slate-300 italic">(Tanda Tangan)</span>
                </div>
                <div class="font-bold text-slate-900 border-t border-slate-300 pt-1 mt-1">
                    Bagian Finance
                </div>
                <div class="text-[10px] text-slate-400">Kasir Hub</div>
            </div>

            <div>
                <div class="text-[10px] text-slate-400 font-bold uppercase">Disetujui Oleh</div>
                <div class="h-16 flex items-end justify-center">
                    @if($advance->approved_by)
                        <div class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                            <i class="fa-solid fa-check-circle mr-1"></i> VERIFIED
                        </div>
                    @else
                        <span class="text-[10px] text-slate-300 italic">(Tanda Tangan)</span>
                    @endif
                </div>
                <div class="font-bold text-slate-900 border-t border-slate-300 pt-1 mt-1">
                    {{ $advance->approver->name ?? 'Admin / HRD' }}
                </div>
                <div class="text-[10px] text-slate-400">
                    {{ $advance->approved_at ? \Carbon\Carbon::parse($advance->approved_at)->format('d/m/Y') : 'Tanggal Persetujuan' }}
                </div>
            </div>
        </div>

        <div class="text-center text-[10px] text-slate-400 border-t border-slate-100 pt-3">
            Dokumen ini dicetak otomatis oleh Sistem {{ $appName }} pada {{ now()->translatedFormat('d F Y, H:i') }}.
        </div>
    </div>

</body>
</html>
