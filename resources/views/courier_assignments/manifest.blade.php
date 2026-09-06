<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>MANIFES PENUGASAN KURIR - {{ $assignment->assignment_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 20px; color: #111; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
        .title { font-size: 16px; font-weight: bold; }
        .grid { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .box { width: 48%; border: 1px solid #ccc; padding: 8px; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #333; }
        th { background: #eee; padding: 6px; font-size: 10px; text-align: left; }
        td { padding: 6px; font-size: 10px; }
        .sig { display: flex; justify-content: space-between; text-align: center; margin-top: 30px; }
        .sig-box { width: 45%; }
        .sig-space { height: 50px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #4f46e5; color: #fff; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">
            Cetak Lembar Manifes Tugas Kurir
        </button>
    </div>

    <div class="header">
        <div>
            <div class="title">RADJA EXPRESS LOGISTICS</div>
            <div>LEMBAR MANIFES PENUGASAN KURIR (DISPATCH SHEET)</div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 16px; font-weight: bold; font-family: monospace;">{{ $assignment->assignment_number }}</div>
            <div>Tanggal Tugas: {{ $assignment->assignment_date->format('d/m/Y') }}</div>
        </div>
    </div>

    <div class="grid">
        <div class="box">
            <strong>KURIR PETUGAS:</strong> {{ $assignment->courier->name }} ({{ $assignment->courier->courier_code }})<br>
            <strong>NO. TELEPON:</strong> {{ $assignment->courier->phone }}<br>
            <strong>HUB KURIR:</strong> {{ $assignment->courier->branchHub ? $assignment->courier->branchHub->name : 'Semua Hub' }}<br>
            @if($assignment->assignment_type === 'pickup' && $assignment->destinationHub)
                <strong>HUB TUJUAN SETOR:</strong> <span style="color: #4f46e5; font-weight: bold;">{{ $assignment->destinationHub->name }} ({{ $assignment->destinationHub->city }})</span>
            @elseif($assignment->assignment_type === 'transfer')
                <strong>RUTE TRANSFER:</strong> <span style="color: #0891b2; font-weight: bold;">{{ $assignment->originHub ? $assignment->originHub->name : 'Hub Asal' }} &rarr; {{ $assignment->destinationHub ? $assignment->destinationHub->name : 'Hub Tujuan' }}</span>
            @elseif($assignment->assignment_type === 'delivery' && $assignment->originHub)
                <strong>HUB PENGANTARAN:</strong> <span style="color: #059669; font-weight: bold;">{{ $assignment->originHub->name }} ({{ $assignment->originHub->city }})</span>
            @endif
        </div>
        <div class="box">
            <strong>ARMADA:</strong> {{ $assignment->vehicle ? $assignment->vehicle->plate_number . ' (' . $assignment->vehicle->vehicle_type . ')' : 'Kendaraan Pribadi' }}<br>
            <strong>TIPE PENUGASAN:</strong> {{ strtoupper($assignment->assignment_type) }}<br>
            <strong>TOTAL DOKUMEN/RESI:</strong> {{ count($assignment->items) }} Paket
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">No. Resi AWB</th>
                <th style="width: 30%;">Penerima & Alamat</th>
                <th style="width: 15%;">No. Telepon</th>
                <th style="width: 10%;">Berat</th>
                <th style="width: 20%;">Tanda Tangan / Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assignment->items as $idx => $item)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td><strong style="font-family: monospace;">{{ $item->shipment->tracking_number }}</strong></td>
                    <td>
                        <strong>{{ $item->shipment->recipient_name }}</strong><br>
                        {{ $item->shipment->recipient_address }}, {{ $item->shipment->recipient_city }}
                    </td>
                    <td>{{ $item->shipment->recipient_phone }}</td>
                    <td>{{ $item->shipment->weight_kg }} kg</td>
                    <td style="text-align: center; vertical-align: bottom; font-size: 9px; color: #777;">
                        ( Scan QR ePOD / TTD )
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="sig">
        <div class="sig-box">
            <div>Dispatcher / Staf Hub</div>
            <div class="sig-space"></div>
            <div>( _____________________ )</div>
        </div>
        <div class="sig-box">
            <div>Kurir Yang Bertugas</div>
            <div class="sig-space"></div>
            <div>( {{ $assignment->courier->name }} )</div>
        </div>
    </div>
</body>
</html>
