<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $appLogoUrl = \App\Models\AppSetting::get('app_logo_url', null);
        $statusBadgeColors = [
            'draft' => 'bg-slate-700 text-slate-300 border-slate-600',
            'picked_up' => 'bg-blue-500/20 text-blue-300 border-blue-500/40',
            'in_sorting_hub' => 'bg-amber-500/20 text-amber-300 border-amber-500/40',
            'in_transit' => 'bg-purple-500/20 text-purple-300 border-purple-500/40',
            'out_for_delivery' => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/40',
            'delivered' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40',
            'failed' => 'bg-rose-500/20 text-rose-300 border-rose-500/40',
        ];
        $statusLabels = [
            'draft' => 'Draft',
            'picked_up' => 'Diterima di Hub / Picked Up',
            'in_sorting_hub' => 'In Sorting Hub',
            'in_transit' => 'In Transit (Antar Hub)',
            'out_for_delivery' => 'Out for Delivery (Kurir)',
            'delivered' => 'Delivered (Terkirim)',
            'failed' => 'Gagal / Pending',
        ];
    @endphp
    <title>Scan QR AWB - {{ $shipment->tracking_number }}</title>
    
    @if($appLogoUrl)
        <link rel="icon" type="image/png" href="{{ asset($appLogoUrl) }}">
        <link rel="shortcut icon" href="{{ asset($appLogoUrl) }}">
        <link rel="apple-touch-icon" href="{{ asset($appLogoUrl) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 rx=%2225%22 fill=%22%234f46e5%22/><text y=%22.85em%22 x=%2250%25%22 text-anchor=%22middle%22 font-size=%2255%22>🚚</text></svg>">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        canvas { touch-action: none; cursor: crosshair; }
    </style>
</head>
<body class="min-h-full flex flex-col justify-between p-4 max-w-lg mx-auto space-y-6">
    <div class="space-y-6">
        <!-- Header Portal Scan QR -->
        <div class="text-center pt-2 border-b border-slate-800 pb-4">
            <div class="inline-flex items-center space-x-2 text-indigo-400 font-extrabold text-xs mb-1 uppercase tracking-wider">
                <i class="fa-solid fa-qrcode"></i>
                <span>Portal Scan Mobile AWB</span>
            </div>
            <h1 class="text-2xl font-black font-mono text-white tracking-wide">{{ $shipment->tracking_number }}</h1>
            <p class="text-xs text-slate-400 mt-1">Sistem Pembaruan Status Transit & ePOD Serah Terima Paket</p>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-bold text-center">
                <i class="fa-solid fa-circle-check text-lg block mb-1"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Shipment Overview Card -->
        <div class="bg-slate-800/90 p-5 rounded-2xl border border-slate-700/80 space-y-3">
            <div class="flex items-center justify-between text-xs pb-2.5 border-b border-slate-700">
                <span class="text-slate-400">Layanan: <strong class="text-white font-bold">{{ $shipment->service_type }}</strong></span>
                <span class="px-2.5 py-1 rounded-lg border font-black uppercase text-[10px] {{ $statusBadgeColors[$shipment->status] ?? 'bg-slate-700 text-white' }}">
                    {{ $statusLabels[$shipment->status] ?? strtoupper($shipment->status) }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs pt-1">
                <div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase">PENGIRIM:</div>
                    <div class="font-bold text-white truncate">{{ $shipment->sender_name }}</div>
                    <div class="text-slate-400 text-[11px]">{{ $shipment->sender_city }}</div>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase">PENERIMA:</div>
                    <div class="font-bold text-white truncate">{{ $shipment->recipient_name }}</div>
                    <div class="text-slate-400 text-[11px]">{{ $shipment->recipient_city }}</div>
                </div>
            </div>

            <div class="text-xs pt-2 border-t border-slate-700/80">
                <div class="text-slate-400 text-[10px] uppercase font-bold">Alamat Lengkap Tujuan:</div>
                <div class="text-slate-200 font-medium text-[11px] mt-0.5">{{ $shipment->recipient_address }}, {{ $shipment->recipient_city }} {{ $shipment->recipient_postal_code ? '('.$shipment->recipient_postal_code.')' : '' }}</div>
                <div class="text-indigo-400 font-bold text-xs mt-1"><i class="fa-solid fa-phone mr-1"></i> {{ $shipment->recipient_phone }}</div>
            </div>
        </div>

        @if(!in_array($shipment->status, ['out_for_delivery', 'delivered']))
            <!-- MODE 1: FORM UPDATE STATUS TRANSIT (Jika Status Belum Out for Delivery / Delivered) -->
            <div class="bg-slate-800/90 p-5 rounded-2xl border border-slate-700/80 space-y-4">
                <div class="flex items-center space-x-2 border-b border-slate-700 pb-3">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-truck-ramp-box"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-white">Update Status Transit Paket</h3>
                        <p class="text-[11px] text-slate-400">Scan pergerakan transit hub / gudang logistik.</p>
                    </div>
                </div>

                <form action="{{ route('epod.store-transit', $shipment->tracking_number) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-amber-300 uppercase tracking-wider mb-1.5">Pilih Status Transit Terbaru *</label>
                        <select name="status" id="transitStatusSelect" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white font-bold text-xs focus:ring-2 focus:ring-amber-500">
                            <option value="in_sorting_hub" {{ $shipment->status == 'in_sorting_hub' ? 'selected' : '' }}>📦 Tiba di Hub Sorting / Gudang Transit (In Sorting Hub)</option>
                            <option value="in_transit" {{ $shipment->status == 'in_transit' ? 'selected' : '' }}>🚛 Dalam Perjalanan Antar Hub (Linehaul Transit)</option>
                            <option value="out_for_delivery">🛵 Dibawa Kurir untuk Pengantaran Terakhir (Out for Delivery)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-amber-300 uppercase tracking-wider mb-1.5">Lokasi / Nama Hub Transit *</label>
                        <input type="text" name="location" value="{{ old('location', $shipment->destinationHub ? $shipment->destinationHub->name : $shipment->sender_city) }}" required placeholder="Contoh: Hub Sorting Jakarta Selatan..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white font-semibold text-xs focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-amber-300 uppercase tracking-wider mb-1.5">Catatan Transit (Opsional)</label>
                        <input type="text" name="notes" placeholder="Contoh: Paket disortir ke rak B-12..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white font-semibold text-xs">
                    </div>

                    <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black text-xs uppercase tracking-wider shadow-lg shadow-amber-500/20 transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-arrows-rotate text-sm"></i>
                        <span>Update Status Transit Resi</span>
                    </button>
                </form>
            </div>

            <!-- History Logs Section -->
            <div class="bg-slate-800/50 p-5 rounded-2xl border border-slate-700/60 space-y-3">
                <h4 class="text-xs font-extrabold uppercase text-slate-400 tracking-wider flex items-center">
                    <i class="fa-solid fa-clock-rotate-left mr-2 text-indigo-400"></i> Riwayat Pergerakan Resi
                </h4>
                <div class="space-y-2.5 max-h-48 overflow-y-auto text-xs pr-1">
                    @foreach($shipment->trackingLogs as $log)
                        <div class="p-2.5 rounded-xl bg-slate-900/80 border border-slate-800 space-y-1">
                            <div class="flex justify-between text-[10px]">
                                <span class="font-bold text-indigo-300">{{ $log->location }}</span>
                                <span class="text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="text-slate-300 text-[11px] leading-snug">{{ $log->description }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

        @else
            <!-- MODE 2: FORM EPOD ELECTRONIC PROOF OF DELIVERY (Jika Status Out for Delivery / Delivered) -->
            @if($shipment->status === 'delivered')
                <div class="p-4 rounded-2xl bg-emerald-950/60 border border-emerald-500/50 text-emerald-200 text-xs space-y-3">
                    <div class="flex items-center space-x-2 font-bold text-sm text-emerald-400">
                        <i class="fa-solid fa-award text-lg"></i>
                        <span>Paket Resmi TERKIRIM (DELIVERED)</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-[11px] pt-2 border-t border-emerald-800/60">
                        <div>
                            <span class="text-slate-400">Penerima:</span>
                            <div class="font-bold text-white">{{ $shipment->pod_receiver_name }}</div>
                            <div class="text-slate-300 text-[10px]">({{ $shipment->pod_receiver_relation }})</div>
                        </div>
                        <div>
                            <span class="text-slate-400">Waktu Terkirim:</span>
                            <div class="font-bold text-white">{{ $shipment->pod_delivered_at ? $shipment->pod_delivered_at->format('d/m/Y H:i') : '-' }}</div>
                        </div>
                    </div>
                    @if($shipment->pod_photo)
                        <div class="pt-2">
                            <span class="text-slate-400 text-[10px] block mb-1">Foto Bukti Penerimaan:</span>
                            <img src="{{ asset('storage/' . $shipment->pod_photo) }}" alt="POD Photo" class="w-full h-40 object-cover rounded-xl border border-emerald-500/30">
                        </div>
                    @endif
                    @if($shipment->pod_signature)
                        <div class="pt-2">
                            <span class="text-slate-400 text-[10px] block mb-1">Tanda Tangan Digital Penerima:</span>
                            <div class="bg-white p-2 rounded-xl">
                                <img src="{{ $shipment->pod_signature }}" alt="POD Signature" class="h-20 mx-auto">
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- ePOD Submission Form -->
            <form action="{{ route('epod.store', $shipment->tracking_number) }}" method="POST" enctype="multipart/form-data" onsubmit="saveSignature()" class="space-y-5 bg-slate-800/80 p-5 rounded-2xl border border-slate-700">
                @csrf

                <div class="flex items-center space-x-2 border-b border-slate-700 pb-3">
                    <div class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-signature"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-white">Form ePOD Serah Terima Paket</h3>
                        <p class="text-[11px] text-slate-400">Isi bukti tanda tangan & foto saat paket diserahkan.</p>
                    </div>
                </div>

                <input type="hidden" name="pod_signature" id="podSignatureInput" value="{{ $shipment->pod_signature }}">
                <input type="hidden" name="pod_latitude" id="podLatitudeInput" value="{{ $shipment->pod_latitude }}">
                <input type="hidden" name="pod_longitude" id="podLongitudeInput" value="{{ $shipment->pod_longitude }}">
                <input type="hidden" name="pod_location_name" id="podLocationNameInput" value="{{ $shipment->pod_location_name }}">

                <!-- Real-time GPS Location Status Card -->
                <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-700 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                            </span>
                            <span class="text-xs font-extrabold text-emerald-400 uppercase tracking-wider">Lokasi Real-Time GPS</span>
                        </div>
                        <button type="button" onclick="getRealtimeGPSLocation()" class="text-[10px] font-bold text-indigo-400 hover:text-indigo-300">
                            <i class="fa-solid fa-rotate mr-1"></i> Refresh GPS
                        </button>
                    </div>
                    <div id="gpsStatusText" class="text-xs text-slate-300 font-medium">
                        <i class="fa-solid fa-spinner fa-spin mr-1"></i> Mengambil koordinat GPS lokasi Anda saat ini...
                    </div>
                    <div id="gpsMapLink" class="{{ $shipment->pod_latitude ? '' : 'hidden' }} text-[11px] font-bold text-indigo-400 pt-1 border-t border-slate-800">
                        <a href="{{ $shipment->pod_latitude ? 'https://maps.google.com/?q='.$shipment->pod_latitude.','.$shipment->pod_longitude : '#' }}" target="_blank" id="googleMapsUrl" class="hover:underline flex items-center">
                            <i class="fa-solid fa-map-location-dot mr-1"></i> Buka Koordinat di Google Maps
                        </a>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-indigo-300 uppercase tracking-wider mb-1.5">Nama Penerima Paket *</label>
                    <input type="text" name="pod_receiver_name" value="{{ old('pod_receiver_name', $shipment->pod_receiver_name ?? $shipment->recipient_name) }}" required
                        placeholder="Nama orang yang menerima paket..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white font-semibold text-xs focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-indigo-300 uppercase tracking-wider mb-1.5">Hubungan dengan Penerima *</label>
                    <select name="pod_receiver_relation" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white font-semibold text-xs focus:ring-2 focus:ring-indigo-500">
                        <option value="Penerima Langsung (Ybs)">Penerima Langsung (Ybs)</option>
                        <option value="Anggota Keluarga (Suami/Istri/Anak)">Anggota Keluarga (Suami/Istri/Anak)</option>
                        <option value="Rekan Kerja / Staf Kantor">Rekan Kerja / Staf Kantor</option>
                        <option value="Satpam / Security">Satpam / Security</option>
                        <option value="Tetangga">Tetangga</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-indigo-300 uppercase tracking-wider mb-1.5">Foto Bukti Penerimaan (POD Photo)</label>
                    <input type="file" name="pod_photo" accept="image/*" capture="environment" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-xs text-slate-300">
                </div>

                <!-- Touch Signature Canvas -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-indigo-300 uppercase tracking-wider">Tanda Tangan Digital Penerima *</label>
                        <button type="button" onclick="clearSignature()" class="text-[10px] text-rose-400 hover:underline">Clear Canvas</button>
                    </div>
                    <div class="bg-white rounded-xl overflow-hidden border-2 border-indigo-500/50 shadow-inner">
                        <canvas id="sigCanvas" width="340" height="150" class="w-full h-36"></canvas>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">Usap / gores layar di atas untuk membubuhkan tanda tangan.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-indigo-300 uppercase tracking-wider mb-1.5">Catatan Tambahan (Opsional)</label>
                    <input type="text" name="pod_notes" value="{{ old('pod_notes', $shipment->pod_notes) }}" placeholder="Catatan kondisi serah terima..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white font-semibold text-xs">
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-extrabold text-sm shadow-xl shadow-emerald-900/40 transition">
                    <i class="fa-solid fa-check-double mr-1.5"></i> Simpan & Konfirmasi TERKIRIM
                </button>
            </form>
        @endif
    </div>

    <!-- Footer -->
    <footer class="text-center text-[10px] text-slate-500 pt-6 pb-2">
        Radja Express Logistics Mobile Portal &copy; {{ date('Y') }}
    </footer>

    <script>
        const canvas = document.getElementById('sigCanvas');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            let isDrawing = false;

            function getPos(e) {
                const rect = canvas.getBoundingClientRect();
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                return {
                    x: (clientX - rect.left) * (canvas.width / rect.width),
                    y: (clientY - rect.top) * (canvas.height / rect.height)
                };
            }

            function startDrawing(e) {
                isDrawing = true;
                const pos = getPos(e);
                ctx.beginPath();
                ctx.moveTo(pos.x, pos.y);
                e.preventDefault();
            }

            function draw(e) {
                if (!isDrawing) return;
                const pos = getPos(e);
                ctx.lineWidth = 2.5;
                ctx.lineCap = 'round';
                ctx.strokeStyle = '#1e1b4b';
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();
                e.preventDefault();
            }

            function stopDrawing() {
                isDrawing = false;
            }

            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);

            canvas.addEventListener('touchstart', startDrawing);
            canvas.addEventListener('touchmove', draw);
            canvas.addEventListener('touchend', stopDrawing);
        }

        function clearSignature() {
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            if (document.getElementById('podSignatureInput')) document.getElementById('podSignatureInput').value = '';
        }

        function saveSignature() {
            if (!canvas) return;
            const dataUrl = canvas.toDataURL('image/png');
            if (document.getElementById('podSignatureInput')) document.getElementById('podSignatureInput').value = dataUrl;
        }

        function getRealtimeGPSLocation() {
            const statusBox = document.getElementById('gpsStatusText');
            const mapLink = document.getElementById('gpsMapLink');
            const gUrl = document.getElementById('googleMapsUrl');
            
            if (!statusBox) return;

            if (!navigator.geolocation) {
                statusBox.innerHTML = '<span class="text-amber-400">⚠️ Browser tidak mendukung Geolocation GPS.</span>';
                return;
            }

            statusBox.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Memindai koordinat GPS real-time lokasi Anda saat ini...';

            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    const accuracy = Math.round(pos.coords.accuracy);

                    if (document.getElementById('podLatitudeInput')) document.getElementById('podLatitudeInput').value = lat;
                    if (document.getElementById('podLongitudeInput')) document.getElementById('podLongitudeInput').value = lng;

                    statusBox.innerHTML = `<span class="text-emerald-400 font-bold"><i class="fa-solid fa-location-dot mr-1"></i> GPS Terhubung: ${lat.toFixed(6)}, ${lng.toFixed(6)}</span> <span class="text-slate-400 text-[10px]">(Akurasi: ±${accuracy}m)</span>`;

                    if (mapLink && gUrl) {
                        gUrl.href = `https://maps.google.com/?q=${lat},${lng}`;
                        mapLink.classList.remove('hidden');
                    }
                },
                function(err) {
                    console.log("GPS Error: ", err);
                    statusBox.innerHTML = '<span class="text-amber-400"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Akses GPS belum diizinkan. Silakan aktifkan Izin Lokasi di browser Anda.</span>';
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        document.addEventListener('DOMContentLoaded', function() {
            getRealtimeGPSLocation();
        });
    </script>
</body>
</html>
