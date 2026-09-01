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
    <title>SLIP GAJI KOMISI - {{ $payroll->payroll_code }}</title>
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
        <a href="{{ route('courier-payrolls.show', $payroll->id) }}" class="text-xs font-bold text-slate-500 hover:text-slate-700">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Detail
        </a>
        <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg transition">
            <i class="fa-solid fa-print mr-1.5"></i> Cetak / Simpan PDF
        </button>
    </div>

    <!-- Official Payslip Card -->
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-xl border border-slate-200 print-border space-y-6">
        <!-- Header Company -->
        <div class="flex items-center justify-between border-b border-slate-200 pb-5">
            <div class="flex items-center space-x-3">
                @if($appLogoUrl)
                    <img src="{{ asset($appLogoUrl) }}" alt="Logo" class="w-12 h-12 object-contain">
                @else
                    <div class="w-12 h-12 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-black text-xl">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                @endif
                <div>
                    <h1 class="text-lg font-black text-slate-900 uppercase tracking-tight">{{ $appName }}</h1>
                    <p class="text-xs text-slate-500">{{ $appAddress }}</p>
                    <p class="text-xs text-slate-500">Telp: {{ $appPhone }} • Email: {{ $appEmail }}</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400">SLIP GAJI KOMISI KURIR</div>
                <div class="text-sm font-black font-mono text-indigo-600 mt-0.5">{{ $payroll->payroll_code }}</div>
                <div class="text-xs text-slate-500 mt-1">Periode: <strong>{{ $payroll->month_name }} {{ $payroll->period_year }}</strong></div>
            </div>
        </div>

        <!-- Courier Info Grid -->
        <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs">
            <div>
                <div class="text-slate-400 font-bold uppercase">Nama Kurir / Driver:</div>
                <div class="font-extrabold text-slate-900 text-sm mt-0.5">{{ $payroll->courier->name }}</div>
                <div class="font-mono text-indigo-600 font-bold mt-0.5">Kode: {{ $payroll->courier->courier_code }}</div>
            </div>
            <div>
                <div class="text-slate-400 font-bold uppercase">Hub Bertugas / Fleet:</div>
                <div class="font-bold text-slate-800 mt-0.5">{{ $payroll->courier->branchHub ? $payroll->courier->branchHub->name : 'Semua Hub' }}</div>
                <div class="text-slate-600 mt-0.5">Metode Bayar: <strong>{{ $payroll->payment_method ?: 'Transfer Bank' }}</strong></div>
            </div>
        </div>

        <!-- Formula Calculation Breakdown -->
        <div class="space-y-3">
            <h2 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
                Rincian Perhitungan Pendapatan Komisi
            </h2>

            <table class="w-full text-xs">
                <tbody class="divide-y divide-slate-100">
                    <tr class="bg-indigo-50/70 font-bold">
                        <td class="py-2.5 px-2 text-indigo-900">Gaji Pokok Kurir (Basic Salary):</td>
                        <td class="py-2.5 px-2 text-right font-black text-indigo-900">Rp {{ number_format($payroll->basic_salary ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-slate-600">Total Paket Terantar Sukses (ePOD Verified):</td>
                        <td class="py-2 text-right font-bold text-slate-900">{{ number_format($payroll->total_deliveries) }} Paket</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-slate-600">Nominal Tarif Komisi Per Paket:</td>
                        <td class="py-2 text-right font-bold text-slate-900">Rp {{ number_format($payroll->commission_per_delivery, 0, ',', '.') }} / paket</td>
                    </tr>
                    <tr class="bg-slate-50 font-bold">
                        <td class="py-2 px-2 text-slate-800">Subtotal Komisi Pengantaran (Paket x Komisi):</td>
                        <td class="py-2 px-2 text-right font-bold text-slate-900">Rp {{ number_format($payroll->total_commission, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-slate-600">Bonus / Tunjangan Tambahan:</td>
                        <td class="py-2 text-right font-bold text-emerald-600">+ Rp {{ number_format($payroll->bonus_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-slate-600">Potongan Gaji:</td>
                        <td class="py-2 text-right font-bold text-rose-600">- Rp {{ number_format($payroll->deduction_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="border-t-2 border-slate-900 bg-emerald-50 text-sm font-black">
                        <td class="py-3 px-2 text-emerald-900">TOTAL GAJI BERSIH (TAKE HOME PAY):</td>
                        <td class="py-3 px-2 text-right text-emerald-700">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($payroll->notes)
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                <span class="font-bold text-slate-700">Catatan / Keterangan:</span> {{ $payroll->notes }}
            </div>
        @endif

        <!-- Signature Block -->
        <div class="pt-6 border-t border-slate-200 grid grid-cols-2 gap-8 text-center text-xs">
            <div>
                <p class="text-slate-400 font-bold mb-12">Penerima Gaji (Kurir),</p>
                <p class="font-extrabold text-slate-900 underline">{{ $payroll->courier->name }}</p>
                <p class="text-[10px] text-slate-400 font-mono">{{ $payroll->courier->courier_code }}</p>
            </div>
            <div>
                <p class="text-slate-400 font-bold mb-12">Bagian Keuangan / Finance,</p>
                <p class="font-extrabold text-slate-900 underline">PT {{ $appName }}</p>
                <p class="text-[10px] text-slate-400">Finance & Payroll Manager</p>
            </div>
        </div>
    </div>
</body>
</html>
