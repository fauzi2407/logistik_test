<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Massal Resi AWB - Antaran Warga Ekspress</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 15px; color: #000; background: #f8fafc; }
        .label-grid { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
        .label-card { width: 360px; border: 2px solid #0c2340; padding: 12px; border-radius: 8px; background: #fff; page-break-inside: avoid; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .brand-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #0c2340; padding-bottom: 8px; margin-bottom: 8px; }
        .service-badge { font-size: 12px; font-weight: bold; background: #f58220; color: #fff; padding: 3px 8px; border-radius: 4px; }
        .resi-box { text-align: center; border-bottom: 2px solid #0c2340; padding-bottom: 8px; margin-bottom: 8px; }
        .tracking-num { font-size: 18px; font-weight: 900; font-family: monospace; letter-spacing: 1px; margin-top: 4px; color: #0c2340; }
        .grid-addresses { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; border-bottom: 2px solid #0c2340; padding-bottom: 8px; margin-bottom: 8px; }
        .addr-box { font-size: 10px; line-height: 1.3; }
        .addr-title { font-weight: bold; font-size: 9px; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #cbd5e1; padding-bottom: 2px; margin-bottom: 4px; }
        .qr-section { display: flex; align-items: center; justify-content: space-between; padding-top: 4px; }
        .qr-info { font-size: 8.5px; color: #475569; max-width: 220px; }
        .print-btn { padding: 10px 24px; background: #0c2340; color: #fff; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 13px; }
        .print-btn:hover { background: #f58220; }
        @media print { 
            body { background: #fff; margin: 0; }
            .no-print { display: none !important; }
            .label-card { box-shadow: none; }
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" class="print-btn">🖨️ Cetak Semua Resi AWB ({{ count($shipments) }} Resi)</button>
        <button onclick="window.close()" style="padding: 10px 18px; margin-left: 10px; background: #e2e8f0; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">Tutup</button>
    </div>

    <div class="label-grid">
        @foreach($shipments as $index => $shipment)
            <div class="label-card">
                <!-- Header -->
                <div class="brand-header">
                    <img src="{{ asset('images/awx-logo-horizontal.svg') }}" alt="AWX" style="height: 28px; object-fit: contain;">
                    <div class="service-badge">{{ strtoupper($shipment->service_type) }}</div>
                </div>

                <!-- Tracking Number -->
                <div class="resi-box">
                    <div style="font-size: 8.5px; font-weight: bold; text-transform: uppercase; color: #64748b;">NOMOR RESI / WAYBILL</div>
                    <div class="tracking-num">{{ $shipment->tracking_number }}</div>
                    <div style="font-size: 8.5px; margin-top: 2px; color: #475569;">
                        ASAL: <strong>{{ strtoupper($shipment->sender_city ?? 'JAKARTA BARAT') }}</strong> &rarr; TUJUAN: <strong>{{ strtoupper($shipment->recipient_city) }}</strong>
                    </div>
                </div>

                <!-- Addresses -->
                <div class="grid-addresses">
                    <div class="addr-box">
                        <div class="addr-title">PENGIRIM (SENDER):</div>
                        <strong>{{ $shipment->sender_name }}</strong><br>
                        Telp: {{ $shipment->sender_phone }}<br>
                        <span style="font-size: 9px; color: #475569;">{{ $shipment->sender_address }}</span>
                    </div>

                    <div class="addr-box">
                        <div class="addr-title">PENERIMA (RECIPIENT):</div>
                        <strong>{{ $shipment->recipient_name }}</strong><br>
                        Telp: {{ $shipment->recipient_phone }}<br>
                        <span style="font-size: 9px; color: #475569;">{{ $shipment->recipient_address }}</span>
                    </div>
                </div>

                <!-- Package Specs -->
                <div style="font-size: 9.5px; border-bottom: 2px solid #0c2340; padding-bottom: 5px; margin-bottom: 6px; display: flex; justify-content: space-between;">
                    <div>BERAT: <strong>{{ $shipment->weight_kg }} KG</strong></div>
                    <div>METODE: <strong>{{ strtoupper($shipment->payment_method ?: 'CREDIT') }}</strong></div>
                    <div>ONGKIR: <strong>RP {{ number_format($shipment->total_amount ?: 5000, 0, ',', '.') }}</strong></div>
                </div>

                <!-- QR Code for Delivery Boy ePOD Mobile Portal Scan -->
                <div class="qr-section">
                    <div class="qr-info">
                        <strong>AWX ePOD VERIFIED:</strong><br>
                        Scan QR Code menggunakan ponsel kurir untuk upload <strong>Foto Penerima</strong> & <strong>Tanda Tangan Digital</strong>.
                    </div>
                    <div id="qrcode_{{ $index }}"></div>
                </div>
            </div>

            <script>
                new QRCode(document.getElementById("qrcode_{{ $index }}"), {
                    text: "{{ route('epod.show', $shipment->tracking_number) }}",
                    width: 70,
                    height: 70,
                    colorDark : "#0c2340",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.M
                });
            </script>
        @endforeach
    </div>
</body>
</html>
