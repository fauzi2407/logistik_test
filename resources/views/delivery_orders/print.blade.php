<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan DO Multi-Tujuan - {{ $deliveryOrder->do_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #1e293b; margin: 20px; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .logo { font-size: 18px; font-weight: bold; color: #4338ca; }
        .do-title { font-size: 16px; font-weight: bold; text-align: right; text-transform: uppercase; }
        .info-box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; border-radius: 6px; margin-bottom: 15px; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .items-table th, .items-table td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        .items-table th { background: #f1f5f9; font-weight: bold; text-transform: uppercase; font-size: 9px; }
        .signature-table { width: 100%; margin-top: 40px; text-align: center; }
        .signature-space { height: 50px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="background: #4338ca; color: white; border: none; padding: 8px 16px; font-weight: bold; border-radius: 4px; cursor: pointer;">
            🖨️ Cetak Surat Jalan Delivery Order (DO)
        </button>
    </div>

    <table class="header-table">
        <tr>
            <td>
                <div class="logo">RADJA EXPRESS LOGISTICS</div>
                <div>Supply Chain & Distribution Logistics</div>
                <div>Jl. Logistik Utama No. 88, Jakarta Selatan</div>
            </td>
            <td style="text-align: right;">
                <div class="do-title">SURAT JALAN DELIVERY ORDER (DO)</div>
                <div style="font-size: 13px; font-weight: bold; font-family: monospace;">{{ $deliveryOrder->do_number }}</div>
                <div>Tanggal Order: {{ $deliveryOrder->order_date->format('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    <div class="info-box">
        <strong>CUSTOMER PENGIRIM:</strong><br>
        <span style="font-size: 13px; font-weight: bold;">{{ $deliveryOrder->sender_name }}</span><br>
        Telepon: {{ $deliveryOrder->sender_phone }} | Alamat: {{ $deliveryOrder->sender_address }}, {{ $deliveryOrder->sender_city }}
    </div>

    <div style="font-weight: bold; font-size: 11px; margin-bottom: 5px;">
        DAFTAR TUJUAN PENERIMA & DETAIL BARANG:
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 30px;">No.</th>
                <th>Nama Penerima & No. HP</th>
                <th>Kota & Alamat Tujuan Lengkap</th>
                <th>No. Ref / PO / Kontrak</th>
                <th>Jenis Barang</th>
                <th style="width: 110px;">Nomor Resi AWB</th>
                <th style="width: 60px;">Paraf</th>
            </tr>
        </thead>
        <tbody>
            @foreach($deliveryOrder->items as $idx => $item)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $item->recipient_name }}</strong><br>
                        <span style="color: #64748b;">{{ $item->recipient_phone }}</span>
                    </td>
                    <td>
                        <strong>{{ $item->recipient_city }}</strong><br>
                        <span style="color: #475569;">{{ $item->recipient_address }}</span>
                    </td>
                    <td style="font-family: monospace; font-weight: bold;">
                        {{ $item->account_ref ?? '-' }}
                    </td>
                    <td>{{ $item->item_name }} ({{ $item->qty }} {{ $item->unit ?? 'Paket' }}, {{ $item->weight_kg }} kg)</td>
                    <td style="font-family: monospace; font-weight: bold; color: #4338ca;">
                        {{ $item->tracking_number ?? '-' }}
                    </td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td style="width: 33%;">
                Diserahkan Oleh (Pengirim)<br>
                <div class="signature-space"></div>
                ( ______________________ )
            </td>
            <td style="width: 33%;">
                Disetujui Operations Logistik<br>
                <div class="signature-space"></div>
                ( ______________________ )
            </td>
            <td style="width: 33%;">
                Kurir Penanggung Jawab<br>
                <div class="signature-space"></div>
                ( ______________________ )
            </td>
        </tr>
    </table>
</body>
</html>
