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
        $appAddress = \App\Models\AppSetting::get('company_address', 'Jl. Logistik Utama No. 88, Jakarta');
        $appLogoUrl = \App\Models\AppSetting::get('app_logo_url', null);
    @endphp
    <title>STRUK KASIR - {{ $shipment->tracking_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            body { font-size: 10pt; background: #fff !important; margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .print-area { border: none !important; box-shadow: none !important; width: 100% !important; padding: 0 !important; }
        }
        .dashed-line { border-bottom: 1px dashed #94a3b8; }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-mono p-4 min-h-screen flex flex-col items-center justify-center">

    <!-- Action Bar Print -->
    <div class="w-full max-w-sm mb-4 flex items-center justify-between no-print">
        <a href="{{ route('pos.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 flex items-center">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Kasir POS
        </a>
        <div class="flex items-center space-x-2">
            <a href="{{ route('shipments.print-label', $shipment->id) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-800 text-white font-bold text-xs shadow hover:bg-slate-900 transition">
                <i class="fa-solid fa-barcode mr-1"></i> Label AWB
            </a>
            <button onclick="window.print()" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg transition">
                <i class="fa-solid fa-print mr-1"></i> Cetak Struk
            </button>
        </div>
    </div>

    <!-- Printable Thermal Receipt (80mm Width) -->
    <div class="print-area w-full max-w-sm bg-white p-6 rounded-2xl shadow-xl border border-slate-200 text-xs space-y-4">
        <!-- Receipt Header -->
        <div class="text-center space-y-1">
            <h1 class="text-base font-black uppercase tracking-tight text-slate-900">{{ $appName }}</h1>
            <p class="text-[10px] text-slate-500 leading-tight">{{ $appAddress }}</p>
            <p class="text-[10px] text-slate-500">Telp: {{ $appPhone }}</p>
            <div class="dashed-line pt-2"></div>
        </div>

        <!-- Transaction Details -->
        <div class="space-y-1 text-[11px]">
            <div class="flex justify-between">
                <span class="text-slate-500">No. Resi AWB:</span>
                <span class="font-bold font-mono text-indigo-700">{{ $shipment->tracking_number }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Tanggal:</span>
                <span>{{ $shipment->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Kasir:</span>
                <span>{{ Auth::user() ? Auth::user()->name : 'Kasir POS' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Customer:</span>
                <span class="font-bold text-slate-900">{{ $shipment->customer ? $shipment->customer->name : 'Walk-In Customer' }}</span>
            </div>
            <div class="dashed-line pt-2"></div>
        </div>

        <!-- Sender & Recipient Summary -->
        <div class="space-y-2 text-[11px]">
            <div>
                <div class="text-[10px] font-bold uppercase text-slate-400">PENGIRIM:</div>
                <div class="font-bold text-slate-900">{{ $shipment->sender_name }} ({{ $shipment->sender_phone }})</div>
                <div class="text-slate-600 text-[10px]">{{ $shipment->sender_city }}</div>
            </div>
            <div>
                <div class="text-[10px] font-bold uppercase text-slate-400">PENERIMA:</div>
                <div class="font-bold text-slate-900">{{ $shipment->recipient_name }} ({{ $shipment->recipient_phone }})</div>
                <div class="text-slate-600 text-[10px]">{{ $shipment->recipient_address }}, {{ $shipment->recipient_city }} {{ $shipment->recipient_postal_code ? '('.$shipment->recipient_postal_code.')' : '' }}</div>
            </div>
            <div class="dashed-line pt-2"></div>
        </div>

        <!-- Shipment Details -->
        <div class="space-y-1.5 text-[11px]">
            <div class="flex justify-between">
                <span class="text-slate-600">Layanan & Berat:</span>
                <span class="font-bold text-slate-900">{{ $shipment->service_type }} ({{ $shipment->weight_kg }} kg)</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-600">Ongkos Kirim:</span>
                <span class="font-bold">Rp {{ number_format($shipment->shipping_fee, 0, ',', '.') }}</span>
            </div>
            @if($shipment->insurance_fee > 0)
                <div class="flex justify-between">
                    <span class="text-slate-600">Asuransi (0.2%):</span>
                    <span class="font-bold">Rp {{ number_format($shipment->insurance_fee, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="dashed-line pt-2"></div>
        </div>

        <!-- Payment Breakdown -->
        <div class="space-y-1.5 text-xs">
            <div class="flex justify-between font-black text-sm text-slate-900 pt-1">
                <span>TOTAL BAYAR:</span>
                <span class="text-emerald-700">Rp {{ number_format($shipment->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-[11px]">
                <span class="text-slate-600">Metode Bayar:</span>
                <span class="font-bold text-slate-900">{{ $shipment->payment_method }} ({{ $shipment->payment_status }})</span>
            </div>
            <div class="dashed-line pt-2"></div>
        </div>

        <!-- Barcode / QR Section -->
        <div class="text-center pt-2 space-y-1">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($shipment->tracking_number) }}" alt="QR AWB" class="w-24 h-24 mx-auto border p-1 rounded">
            <p class="font-mono text-[10px] font-bold text-indigo-700">{{ $shipment->tracking_number }}</p>
            <p class="text-[9px] text-slate-400 mt-2">Terima kasih telah mempercayakan pengiriman Anda bersama {{ $appName }}. Simpan struk ini sebagai bukti pembayaran resmi.</p>
        </div>
    </div>
</body>
</html>
