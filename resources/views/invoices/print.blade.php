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
        $bankName = \App\Models\AppSetting::get('bank_name', 'Bank BCA');
        $bankNo = \App\Models\AppSetting::get('bank_account_number', '8877-6655-44');
        $bankHolder = \App\Models\AppSetting::get('bank_account_name', 'PT Radja Express Logistics');
    @endphp
    <title>INVOICE - {{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        @media print {
            .no-print { display: none !important; }
            body { background: #ffffff !important; padding: 0 !important; }
            .print-container { border: none !important; shadow: none !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="p-6">

    <!-- Action Bar (Hidden on Print) -->
    <div class="max-w-3xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('invoices.show', $invoice->id) }}" class="text-xs font-bold text-slate-500 hover:text-slate-700">
            &larr; Kembali ke Detail Invoice
        </a>
        <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition flex items-center space-x-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            <span>Cetak Invoice Sekarang</span>
        </button>
    </div>

    <!-- Official Invoice Paper Sheet -->
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-2xl border border-slate-200 shadow-xl print-container space-y-6">
        <!-- Header Brand & Invoice Title -->
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-6">
            <div class="flex items-center space-x-3">
                @if($appLogoUrl)
                    <img src="{{ asset($appLogoUrl) }}" alt="Logo" class="w-12 h-12 object-contain">
                @else
                    <div class="w-12 h-12 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-black text-xl shadow-lg">
                        R
                    </div>
                @endif
                <div>
                    <h1 class="text-xl font-black text-slate-900 uppercase tracking-tight">{{ $appName }}</h1>
                    <p class="text-xs text-slate-500 font-medium">{{ $appTagline }}</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $appAddress }} • Telp: {{ $appPhone }}</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-2xl font-black text-indigo-600 tracking-wider">INVOICE</h2>
                <div class="font-mono font-extrabold text-slate-800 text-sm mt-1">{{ $invoice->invoice_number }}</div>
                <div class="mt-1">
                    @if($invoice->isPaid())
                        <span class="inline-block px-3 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">LUNAS / PAID</span>
                    @else
                        <span class="inline-block px-3 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-800 border border-amber-300">BELUM DIBAYAR / UNPAID</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Billing Info Grid -->
        <div class="grid grid-cols-2 gap-6 text-xs">
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Ditagihkan Kepada:</div>
                <div class="font-black text-slate-900 text-sm">{{ $invoice->customer ? ($invoice->customer->company_name ?: $invoice->customer->name) : 'Customer Umum' }}</div>
                <div class="text-slate-600 font-medium mt-0.5">U.p: {{ $invoice->customer ? $invoice->customer->name : '-' }}</div>
                <div class="text-slate-600 font-medium">Telepon: {{ $invoice->customer ? $invoice->customer->phone : '-' }}</div>
                <div class="text-slate-500 mt-1 max-w-xs">{{ $invoice->customer ? $invoice->customer->address : '-' }}, {{ $invoice->customer ? $invoice->customer->city : '' }}</div>
            </div>

            <div class="text-right space-y-1">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Informasi Tagihan:</div>
                <div><span class="text-slate-400">Tanggal Invoice:</span> <span class="font-bold text-slate-800">{{ $invoice->invoice_date->format('d M Y') }}</span></div>
                <div><span class="text-slate-400">Tanggal Jatuh Tempo:</span> <span class="font-bold text-rose-600">{{ $invoice->due_date->format('d M Y') }}</span></div>
                @if($invoice->deliveryOrder)
                    <div><span class="text-slate-400">Ref Surat Jalan DO:</span> <span class="font-mono font-bold text-slate-800">{{ $invoice->deliveryOrder->do_number }}</span></div>
                @endif
                @if($invoice->payment_date)
                    <div><span class="text-slate-400">Tanggal Pembayaran:</span> <span class="font-bold text-emerald-600">{{ $invoice->payment_date->format('d M Y') }}</span></div>
                @endif
            </div>
        </div>

        <!-- Itemized Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 uppercase tracking-wider font-extrabold border-y border-slate-300">
                        <th class="py-2.5 px-3">No.</th>
                        <th class="py-2.5 px-3">Rincian Pengiriman Barang</th>
                        <th class="py-2.5 px-3">Kota Tujuan</th>
                        <th class="py-2.5 px-3">No. Resi AWB</th>
                        <th class="py-2.5 px-3 text-right">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-medium text-slate-800">
                    @if($invoice->deliveryOrder && $invoice->deliveryOrder->items->count() > 0)
                        @foreach($invoice->deliveryOrder->items as $idx => $item)
                            @php
                                $itemFee = $item->shipment ? $item->shipment->total_amount : (20000 * max(1, (int) ceil((float)$item->weight_kg)));
                            @endphp
                            <tr>
                                <td class="py-2.5 px-3 font-bold text-slate-400">{{ $idx + 1 }}</td>
                                <td class="py-2.5 px-3">
                                    <div class="font-bold text-slate-900">{{ $item->item_name }} ({{ $item->weight_kg }} kg)</div>
                                    <div class="text-[11px] text-slate-500">Penerima: {{ $item->recipient_name }}</div>
                                </td>
                                <td class="py-2.5 px-3 font-semibold">{{ $item->recipient_city }}</td>
                                <td class="py-2.5 px-3 font-mono font-bold text-indigo-700">
                                    {{ $item->shipment ? $item->shipment->tracking_number : '-' }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-black">
                                    Rp {{ number_format($itemFee, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="py-2.5 px-3 font-bold text-slate-400">1</td>
                            <td colspan="3" class="py-2.5 px-3 font-bold text-slate-900">Jasa Biaya Pengiriman Logistik Cargo</td>
                            <td class="py-2.5 px-3 text-right font-black">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Calculation & Bank Info Footer -->
        <div class="flex justify-between items-start gap-4 pt-2 border-t border-slate-200">
            <div class="text-xs space-y-2 max-w-sm">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 space-y-1">
                    <div class="font-extrabold uppercase text-[10px] text-slate-800">Pembayaran Transfer Rekening Resmi:</div>
                    <div class="text-[11px]">{{ $bankName }}: <span class="font-mono font-bold text-slate-900">{{ $bankNo }}</span></div>
                    <div class="text-[11px]">Atas Nama: <span class="font-bold text-slate-900">{{ $bankHolder }}</span></div>
                </div>
                @if($invoice->notes)
                    <div class="text-[11px] text-slate-500 italic"><span class="font-bold">Catatan / Instruksi:</span> {{ $invoice->notes }}</div>
                @endif
            </div>

            <div class="w-60 space-y-1.5 text-xs font-semibold text-slate-700">
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span>Subtotal Ongkir:</span>
                    <span class="font-bold text-slate-900">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($invoice->tax_amount > 0)
                    <div class="flex justify-between py-1 border-b border-slate-100 text-indigo-700">
                        <span>PPN (11%):</span>
                        <span class="font-bold">+ Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if($invoice->discount_amount > 0)
                    <div class="flex justify-between py-1 border-b border-slate-100 text-rose-700">
                        <span>Diskon:</span>
                        <span class="font-bold">- Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between py-2 border-t-2 border-slate-900 text-sm font-black text-slate-900">
                    <span>TOTAL TAGIHAN:</span>
                    <span class="text-indigo-700 text-base">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Signatures & Legal Stamp -->
        <div class="grid grid-cols-2 gap-6 pt-8 text-center text-xs">
            <div>
                <p class="text-slate-400 font-medium">Hormat Kami,</p>
                <div class="h-16 flex items-center justify-center font-bold text-indigo-600 italic">
                    [ Official Stamp ]
                </div>
                <p class="font-extrabold text-slate-900 border-t border-slate-300 pt-1 max-w-[160px] mx-auto">Finance & Billing Dept</p>
            </div>
            <div>
                <p class="text-slate-400 font-medium">Penerima Tagihan,</p>
                <div class="h-16"></div>
                <p class="font-extrabold text-slate-900 border-t border-slate-300 pt-1 max-w-[160px] mx-auto">{{ $invoice->customer ? ($invoice->customer->company_name ?: $invoice->customer->name) : 'Customer' }}</p>
            </div>
        </div>
    </div>
</body>
</html>
