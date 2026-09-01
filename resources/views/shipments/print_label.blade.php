<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LABEL AWB - {{ $shipment->tracking_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; color: #000; }
        .label-card { width: 380px; border: 2px solid #000; padding: 12px; border-radius: 8px; background: #fff; margin: 0 auto; }
        .brand-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 8px; }
        .brand-title { font-size: 16px; font-weight: 900; letter-spacing: -0.5px; }
        .service-badge { font-size: 14px; font-weight: bold; background: #000; color: #fff; padding: 3px 8px; border-radius: 4px; }
        .resi-box { text-align: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 8px; }
        .tracking-num { font-size: 20px; font-weight: 900; font-family: monospace; letter-spacing: 1px; margin-top: 4px; }
        .grid-addresses { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 8px; }
        .addr-box { font-size: 10px; line-height: 1.3; }
        .addr-title { font-weight: bold; font-size: 9px; text-transform: uppercase; color: #444; border-bottom: 1px solid #ccc; padding-bottom: 2px; margin-bottom: 4px; }
        .qr-section { display: flex; align-items: center; justify-content: space-between; padding-top: 4px; }
        .qr-info { font-size: 9px; color: #333; max-width: 220px; }
        .print-btn { display: block; margin: 0 auto 15px auto; padding: 8px 16px; background: #4f46e5; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
        @media print { .no-print { display: none; } }
    </style>
    <!-- Simple JS QR Code Generator -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
    <div class="no-print" style="text-align: center;">
        <button onclick="window.print()" class="print-btn">Cetak Label Resi (AWB QR Code)</button>
    </div>

    <div class="label-card">
        <!-- Header -->
        <div class="brand-header">
            <div class="brand-title">RADJA EXPRESS</div>
            <div class="service-badge">{{ strtoupper($shipment->service_type) }}</div>
        </div>

        <!-- Tracking Number & Barcode representation -->
        <div class="resi-box">
            <div style="font-size: 9px; font-weight: bold; text-transform: uppercase; color: #555;">NOMOR RESI / WAYBILL</div>
            <div class="tracking-num">{{ $shipment->tracking_number }}</div>
            <div style="font-size: 9px; margin-top: 2px; color: #555;">KOTA ASAL: <strong>{{ strtoupper($shipment->sender_city) }}</strong> &rarr; TUJUAN: <strong>{{ strtoupper($shipment->recipient_city) }}</strong></div>
        </div>

        <!-- Addresses -->
        <div class="grid-addresses">
            <div class="addr-box">
                <div class="addr-title">PENGIRIM (SENDER):</div>
                <strong>{{ $shipment->sender_name }}</strong><br>
                Telp: {{ $shipment->sender_phone }}<br>
                {{ $shipment->sender_address }}, {{ $shipment->sender_city }}
            </div>

            <div class="addr-box">
                <div class="addr-title">PENERIMA (RECIPIENT):</div>
                <strong>{{ $shipment->recipient_name }}</strong><br>
                Telp: {{ $shipment->recipient_phone }}<br>
                {{ $shipment->recipient_address }}, {{ $shipment->recipient_city }}
            </div>
        </div>

        <!-- Package Specs -->
        <div style="font-size: 10px; border-bottom: 2px solid #000; padding-bottom: 6px; margin-bottom: 8px; display: flex; justify-content: space-between;">
            <div>BERAT: <strong>{{ $shipment->weight_kg }} KG</strong></div>
            <div>COD/METODE: <strong>{{ strtoupper($shipment->payment_method) }}</strong></div>
            <div>BIAYA: <strong>RP {{ number_format($shipment->total_amount, 0, ',', '.') }}</strong></div>
        </div>

        <!-- QR Code for Delivery Boy ePOD Mobile Portal Scan -->
        <div class="qr-section">
            <div class="qr-info">
                <strong>ePOD MOBILE SCAN FOR COURIER:</strong><br>
                Imbauan Kurir: Scan QR Code di samping menggunakan kamera HP untuk mengunggah <strong>Foto Bukti Terima</strong> & <strong>Tanda Tangan Digital Penerima</strong>.
            </div>
            <div id="qrcode"></div>
        </div>
    </div>

    <script>
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ $epodUrl ?? route('epod.show', $shipment->tracking_number) }}",
            width: 80,
            height: 80,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    </script>
</body>
</html>
