<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $appName = \App\Models\AppSetting::get('app_name', 'Radja Express Logistics');
        $appLogoUrl = \App\Models\AppSetting::get('app_logo_url', null);
        $appLogoIcon = \App\Models\AppSetting::get('app_logo_icon', 'fa-truck-fast');
    @endphp
    <title>Lacak Pengiriman - {{ $appName }}</title>
    
    <!-- Favicon (Samakan Logo Aplikasi & Tab Bar Browser) -->
    @if($appLogoUrl)
        <link rel="icon" type="image/png" href="{{ asset($appLogoUrl) }}">
        <link rel="shortcut icon" href="{{ asset($appLogoUrl) }}">
        <link rel="apple-touch-icon" href="{{ asset($appLogoUrl) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 rx=%2225%22 fill=%22%234f46e5%22/><text y=%22.85em%22 x=%2250%25%22 text-anchor=%22middle%22 font-size=%2255%22>🚚</text></svg>">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-full text-slate-100 flex flex-col justify-between">
    <!-- Navbar -->
    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ route('tracking.index') }}" class="flex items-center space-x-3 text-white font-extrabold text-lg">
                @if($appLogoUrl)
                    <img src="{{ asset($appLogoUrl) }}" alt="Logo" class="w-9 h-9 object-contain rounded-xl">
                @else
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-400 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <i class="fa-solid {{ $appLogoIcon }} text-white"></i>
                    </div>
                @endif
                <span>{{ $appName }}</span>
            </a>
            <div class="flex items-center space-x-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs transition">
                        <i class="fa-solid fa-gauge ml-1"></i> Ke Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs transition border border-slate-700">
                        <i class="fa-solid fa-right-to-bracket mr-1"></i> Login Portal
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-4xl w-full mx-auto px-6 py-12">
        <!-- Hero Title -->
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight mb-3">Lacak Real-Time Pengiriman</h1>
            <p class="text-slate-400 text-sm md:text-base max-w-xl mx-auto">Masukkan Nomor Resi AWB (contoh: <code class="text-indigo-400">TRK-20260822-0001</code>) atau Surat Jalan DO untuk memantau perjalanan paket Anda.</p>
        </div>

        <!-- Search Bar -->
        <form action="{{ route('tracking.index') }}" method="GET" class="mb-12">
            <div class="relative max-w-2xl mx-auto">
                <input type="text" name="code" value="{{ $code }}" placeholder="Masukkan Nomor Resi atau Nomor DO (Contoh: TRK-20260822-0001)..." required
                    class="w-full pl-6 pr-36 py-4 rounded-2xl bg-slate-900 border-2 border-indigo-500/40 text-white placeholder-slate-500 font-semibold text-sm focus:outline-none focus:border-indigo-500 shadow-2xl shadow-indigo-900/30">
                <button type="submit" class="absolute right-2 top-2 bottom-2 px-6 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white font-bold text-sm transition flex items-center shadow-lg shadow-indigo-600/30">
                    <i class="fa-solid fa-magnifying-glass mr-2"></i> Lacak Paket
                </button>
            </div>
        </form>

        <!-- Tracking Results Card -->
        @if($code)
            @if($shipment)
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 md:p-8 shadow-2xl mb-8">
                    <!-- Resi Header -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-slate-800 gap-4">
                        <div>
                            <div class="text-xs font-bold uppercase tracking-wider text-indigo-400">Nomor Resi AWB</div>
                            <div class="text-2xl font-black text-white tracking-wide font-mono mt-0.5">{{ $shipment->tracking_number }}</div>
                            <div class="text-xs text-slate-400 mt-1">Layanan: <span class="text-white font-bold">{{ $shipment->service_type }}</span> • Berat: <span class="text-white font-bold">{{ $shipment->weight_kg }} kg</span></div>
                        </div>
                        <div>
                            @php
                                $badgeClass = match($shipment->status) {
                                    'delivered' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/40',
                                    'out_for_delivery' => 'bg-amber-500/20 text-amber-400 border-amber-500/40 animate-pulse',
                                    'in_transit' => 'bg-sky-500/20 text-sky-400 border-sky-500/40',
                                    default => 'bg-slate-800 text-slate-300 border-slate-700'
                                };
                            @endphp
                            <span class="inline-block px-4 py-2 rounded-2xl border text-xs font-black uppercase tracking-wider text-center {{ $badgeClass }}">
                                {{ str_replace('_', ' ', strtoupper($shipment->status)) }}
                            </span>
                        </div>
                    </div>

                    <!-- Progress Step Bar -->
                    <div class="py-8 border-b border-slate-800">
                        @php
                            $statuses = ['picked_up' => 'Penjemputan', 'in_sorting_hub' => 'In Hub', 'in_transit' => 'In Transit', 'out_for_delivery' => 'Pengantaran', 'delivered' => 'Terkirim'];
                            $currentIdx = match($shipment->status) {
                                'picked_up' => 0,
                                'in_sorting_hub' => 1,
                                'in_transit' => 2,
                                'out_for_delivery' => 3,
                                'delivered' => 4,
                                default => 0
                            };
                        @endphp
                        <div class="grid grid-cols-5 gap-2 text-center">
                            @foreach(array_values($statuses) as $idx => $label)
                                <div class="flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs mb-2 {{ $idx <= $currentIdx ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/50' : 'bg-slate-800 text-slate-500 border border-slate-700' }}">
                                        @if($idx < $currentIdx)
                                            <i class="fa-solid fa-check text-xs"></i>
                                        @else
                                            {{ $idx + 1 }}
                                        @endif
                                    </div>
                                    <span class="text-[10px] font-semibold tracking-wider uppercase {{ $idx <= $currentIdx ? 'text-indigo-300' : 'text-slate-500' }}">{{ $label }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Sender & Receiver Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 py-6 border-b border-slate-800">
                        <div class="bg-slate-950/60 p-4 rounded-2xl border border-slate-800/80">
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2"><i class="fa-solid fa-upload text-indigo-400 mr-1.5"></i> Pengirim</div>
                            <div class="text-sm font-bold text-white">{{ $shipment->sender_name }}</div>
                            <div class="text-xs text-slate-400 mt-1">{{ $shipment->sender_city }}</div>
                        </div>

                        <div class="bg-slate-950/60 p-4 rounded-2xl border border-slate-800/80">
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2"><i class="fa-solid fa-location-dot text-emerald-400 mr-1.5"></i> Penerima</div>
                            <div class="text-sm font-bold text-white">{{ $shipment->recipient_name }}</div>
                            <div class="text-xs text-slate-400 mt-1">{{ $shipment->recipient_city }}</div>
                        </div>
                    </div>

                    <!-- ePOD Verification Box (If Delivered) -->
                    @if($shipment->status === 'delivered')
                        <div class="my-6 p-4 rounded-2xl bg-emerald-950/40 border border-emerald-800/60 text-emerald-200">
                            <div class="flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-emerald-400 mb-2">
                                <i class="fa-solid fa-certificate text-emerald-400"></i>
                                <span>Bukti Serah Terima Elektronik (ePOD) Verified</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                <div>
                                    <p><span class="text-slate-400">Penerima Paket:</span> <strong>{{ $shipment->pod_receiver_name }}</strong> ({{ $shipment->pod_receiver_relation }})</p>
                                    <p><span class="text-slate-400">Waktu Diterima:</span> {{ $shipment->pod_delivered_at ? $shipment->pod_delivered_at->format('d M Y H:i WIB') : '-' }}</p>
                                </div>
                                @if($shipment->pod_signature)
                                    <div class="bg-white p-2 rounded-xl border border-emerald-500/40 w-36 h-16 flex items-center justify-center">
                                        <img src="{{ $shipment->pod_signature }}" alt="Tanda Tangan ePOD" class="max-h-full max-w-full">
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Tracking History Logs Timeline -->
                    <div class="mt-6">
                        <h3 class="text-sm font-bold text-slate-300 uppercase tracking-wider mb-4"><i class="fa-solid fa-clock-rotate-left mr-1.5 text-indigo-400"></i> Riwayat Perjalanan Paket</h3>
                        <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-800">
                            @foreach($shipment->trackingLogs as $log)
                                <div class="relative">
                                    <div class="absolute -left-[23px] top-1 w-3.5 h-3.5 rounded-full bg-indigo-500 border-2 border-slate-900 shadow"></div>
                                    <div class="text-xs text-indigo-400 font-mono font-semibold">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }} WIB</div>
                                    <div class="text-sm font-bold text-white mt-0.5">{{ $log->description }}</div>
                                    <div class="text-xs text-slate-400"><i class="fa-solid fa-map-pin mr-1 text-slate-500"></i> {{ $log->location }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="p-8 rounded-3xl bg-rose-950/40 border border-rose-800/60 text-center">
                    <i class="fa-solid fa-circle-exclamation text-rose-400 text-3xl mb-3"></i>
                    <h3 class="text-lg font-bold text-white">Nomor Resi / DO Tidak Ditemukan</h3>
                    <p class="text-slate-400 text-xs mt-1">Pastikan kode yang Anda masukkan sudah benar (Contoh: <code class="text-indigo-300">TRK-20260822-0001</code>).</p>
                </div>
            @endif
        @endif
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-900 py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Radja Express Logistics. Express Logistics & Supply Chain Platform.
    </footer>
</body>
</html>
