<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ANTARAN WARGA EKSPRESS (AWX) - Cepat • Aman • Terpercaya</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/awx-logo-horizontal.svg') }}">
    <link rel="shortcut icon" href="{{ asset('images/awx-logo-horizontal.svg') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hero-navy {
            background: linear-gradient(135deg, #07172b 0%, #0c2340 50%, #0f2b52 100%);
            position: relative;
        }
        .orange-grad {
            background: linear-gradient(135deg, #ff7300 0%, #f35a00 100%);
        }
        .text-orange-awx {
            color: #f58220;
        }
        .bg-orange-awx {
            background-color: #f58220;
        }
        .border-orange-awx {
            border-color: #f58220;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 30px -10px rgba(12, 35, 64, 0.15);
        }
        @media print {
            body * {
                visibility: hidden !important;
            }
            #liveTrackingContainer, #liveTrackingContainer * {
                visibility: visible !important;
            }
            #liveTrackingContainer {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0 !important;
                padding: 16px !important;
                background: white !important;
                color: black !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen antialiased selection:bg-orange-500 selection:text-white">

    <!-- Top Official Contacts Bar (Matching PDF Page 22) -->
    <div class="bg-slate-950 text-slate-300 text-xs py-2.5 px-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-2">
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-x-5 gap-y-1">
                <a href="tel:089634072621" class="hover:text-orange-400 transition flex items-center">
                    <i class="fa-solid fa-phone text-orange-500 mr-1.5"></i> 089634072621 / 081316600119
                </a>
                <a href="mailto:admin@antaranwargaekspress.com" class="hover:text-orange-400 transition flex items-center">
                    <i class="fa-solid fa-envelope text-orange-500 mr-1.5"></i> admin@antaranwargaekspress.com
                </a>
                <span class="hidden lg:flex items-center text-slate-400">
                    <i class="fa-solid fa-shield-halved text-orange-500 mr-1.5"></i> CEPAT &bull; AMAN &bull; TERPERCAYA
                </span>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-[11px] font-semibold text-slate-400">Cover Area: <strong>Seluruh Wilayah Indonesia</strong></span>
                @auth
                    <a href="{{ route('dashboard') }}" class="text-orange-400 font-bold hover:underline flex items-center">
                        <i class="fa-solid fa-user-gear mr-1"></i> Dashboard Saya
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-orange-400 font-bold hover:underline flex items-center">
                        <i class="fa-solid fa-right-to-bracket mr-1"></i> TMS Login Portal
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/90 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                <img src="{{ asset('images/awx-logo.svg') }}" alt="AWX Antaran Warga Ekspress" class="h-11 sm:h-12 w-auto object-contain transition transform group-hover:scale-105">
            </a>

            <!-- Navigation Links -->
            <nav class="hidden xl:flex items-center space-x-6 text-[11px] font-extrabold uppercase tracking-wider text-slate-700">
                <a href="#beranda" class="hover:text-orange-600 transition">Beranda</a>
                <a href="#filosofi-logo" class="hover:text-orange-600 transition">Filosofi Logo</a>
                <a href="#visi-misi" class="hover:text-orange-600 transition">Visi & Misi</a>
                <a href="#tentang-kami" class="hover:text-orange-600 transition">Tentang AWX</a>
                <a href="#layanan" class="hover:text-orange-600 transition">Layanan & Service</a>
                <a href="#jaringan" class="hover:text-orange-600 transition">Jaringan Domestik</a>
                <a href="#teknologi" class="hover:text-orange-600 transition">Teknologi & E-POD</a>
                <a href="#klien" class="hover:text-orange-600 transition">Klien Kami</a>
                <a href="#kontak" class="hover:text-orange-600 transition">Kontak</a>
            </nav>

            <!-- Mobile Hamburger Toggle -->
            <div class="xl:hidden flex items-center">
                <button type="button" onclick="toggleMobileMenu()" class="p-2 rounded-xl text-slate-800 hover:bg-slate-100 transition focus:outline-none">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation Drawer Overlay -->
    <div id="mobileMenuDrawer" class="hidden fixed inset-0 z-50 bg-slate-950/95 backdrop-blur-xl flex flex-col justify-between p-6 transition-all duration-300 xl:hidden">
        <div class="space-y-6">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <img src="{{ asset('images/awx-logo.svg') }}" alt="AWX" class="h-10 object-contain">
                <button onclick="toggleMobileMenu()" class="text-slate-400 hover:text-white p-2 text-2xl">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <nav class="flex flex-col space-y-2 font-extrabold text-sm uppercase text-slate-200">
                <a href="#beranda" onclick="toggleMobileMenu()" class="p-3 rounded-xl hover:bg-slate-900 hover:text-orange-400 transition">Beranda</a>
                <a href="#filosofi-logo" onclick="toggleMobileMenu()" class="p-3 rounded-xl hover:bg-slate-900 hover:text-orange-400 transition">Filosofi Logo</a>
                <a href="#visi-misi" onclick="toggleMobileMenu()" class="p-3 rounded-xl hover:bg-slate-900 hover:text-orange-400 transition">Visi & Misi</a>
                <a href="#tentang-kami" onclick="toggleMobileMenu()" class="p-3 rounded-xl hover:bg-slate-900 hover:text-orange-400 transition">Tentang Perusahaan</a>
                <a href="#layanan" onclick="toggleMobileMenu()" class="p-3 rounded-xl hover:bg-slate-900 hover:text-orange-400 transition">Layanan & Service</a>
                <a href="#jaringan" onclick="toggleMobileMenu()" class="p-3 rounded-xl hover:bg-slate-900 hover:text-orange-400 transition">Jaringan Domestik</a>
                <a href="#teknologi" onclick="toggleMobileMenu()" class="p-3 rounded-xl hover:bg-slate-900 hover:text-orange-400 transition">Sistem Teknologi & E-POD</a>
                <a href="#klien" onclick="toggleMobileMenu()" class="p-3 rounded-xl hover:bg-slate-900 hover:text-orange-400 transition">Klien Kami</a>
                <a href="#kontak" onclick="toggleMobileMenu()" class="p-3 rounded-xl hover:bg-slate-900 hover:text-orange-400 transition">Kontak Resmi</a>
            </nav>
        </div>

        <div class="pt-4 border-t border-slate-800 space-y-3">
            <a href="{{ route('login') }}" class="w-full py-3.5 rounded-xl bg-orange-awx hover:bg-orange-600 text-white font-black text-center text-xs uppercase tracking-wider block shadow-lg">
                <i class="fa-solid fa-right-to-bracket mr-2"></i> Masuk ke Portal TMS AWX
            </a>
        </div>
    </div>

    <!-- HERO SECTION (Matching PDF Page 1) -->
    <section id="beranda" class="hero-navy text-white pt-12 pb-24 px-4 overflow-hidden relative">
        <!-- Ambient Glow and Curves -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-10 items-center relative z-10">
            <!-- Left Hero Content -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <!-- Company Profile Tag Badge -->
                <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-orange-400 text-xs font-black uppercase tracking-wider shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                    <span>COMPANY PROFILE &bull; ANTARAN WARGA EKSPRESS</span>
                </div>

                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-5xl font-black tracking-tight leading-tight text-white">
                    Solusi Pengiriman Barang <br>
                    <span class="text-orange-400">Cepat, Aman, dan Terpercaya</span>
                </h1>

                <p class="text-sm sm:text-base text-slate-300 max-w-2xl leading-relaxed mx-auto lg:mx-0">
                    AWX (PT. ANTARAN WARGA EKSPRESS) lahir menjadi perusahaan Courier & Cargo berskala Nasional yang mumpuni, siap melayani pengiriman logistik, kargo darat, laut, udara, dan perbankan ke seluruh pelosok Indonesia didukung kurir Android, E-POD digital & pelacakan real-time.
                </p>

                <!-- 3 Core Pillars (CEPAT, AMAN, TERPERCAYA - Page 1 & 8) -->
                <div class="grid grid-cols-3 gap-3 pt-2 max-w-lg mx-auto lg:mx-0 text-left">
                    <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-2xl border border-white/15">
                        <div class="w-8 h-8 rounded-xl bg-orange-500/30 flex items-center justify-center text-orange-400 mb-2">
                            <i class="fa-solid fa-gauge-high text-sm"></i>
                        </div>
                        <div class="text-xs font-black uppercase text-white">CEPAT</div>
                        <div class="text-[10px] text-slate-300 mt-0.5">Pengiriman cepat & tepat waktu</div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-2xl border border-white/15">
                        <div class="w-8 h-8 rounded-xl bg-blue-500/30 flex items-center justify-center text-blue-400 mb-2">
                            <i class="fa-solid fa-shield-halved text-sm"></i>
                        </div>
                        <div class="text-xs font-black uppercase text-white">AMAN</div>
                        <div class="text-[10px] text-slate-300 mt-0.5">Penanganan barang terbaik</div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-2xl border border-white/15">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/30 flex items-center justify-center text-emerald-400 mb-2">
                            <i class="fa-solid fa-handshake text-sm"></i>
                        </div>
                        <div class="text-xs font-black uppercase text-white">TERPERCAYA</div>
                        <div class="text-[10px] text-slate-300 mt-0.5">Dipercaya dengan layanan terbaik</div>
                    </div>
                </div>

                <!-- Truck Graphic Preview -->
                <div class="pt-4 max-w-md mx-auto lg:mx-0">
                    <img src="{{ asset('images/awx-truck.svg') }}" alt="Armada Truk AWX Antaran Warga Ekspress" class="w-full h-auto drop-shadow-2xl">
                </div>
            </div>

            <!-- Right Interactive Form Widget (Live Tracking & Cek Ongkir) -->
            <div class="lg:col-span-5">
                <div class="glass-card p-6 rounded-3xl shadow-2xl space-y-5">
                    <!-- Tabs Navigation -->
                    <div class="flex rounded-xl bg-slate-900/90 p-1 border border-slate-700/60 text-xs font-extrabold">
                        <button onclick="switchTab('track')" id="tabTrackBtn" class="flex-1 py-2.5 rounded-lg bg-orange-awx text-white transition text-center shadow-md">
                            <i class="fa-solid fa-magnifying-glass-location mr-1.5"></i> Lacak Resi AWB
                        </button>
                        <button onclick="switchTab('rate')" id="tabRateBtn" class="flex-1 py-2.5 rounded-lg text-slate-400 hover:text-white transition text-center">
                            <i class="fa-solid fa-calculator mr-1.5"></i> Cek Tarif Ongkir
                        </button>
                    </div>

                    <!-- Tab 1: Live AWB Tracking -->
                    <div id="tabTrackContent" class="space-y-4">
                        <div class="text-xs text-slate-300 leading-relaxed">
                            Masukkan Nomor Air Waybill (AWB) untuk mengecek status pergerakan paket Anda secara live langsung di halaman ini:
                        </div>
                        <form id="heroTrackingForm" onsubmit="handleLiveTrack(event)" class="space-y-3">
                            <div class="relative">
                                <input type="text" id="heroTrackingInput" name="tracking_number" required placeholder="Contoh: 01100163001740530" value="{{ $searchedCode ?? '' }}" oninput="toggleClearBtn()" class="w-full pl-10 pr-24 py-3 rounded-xl bg-slate-900/90 border border-slate-700 text-white font-mono font-bold text-xs focus:ring-2 focus:ring-orange-500 uppercase outline-none">
                                <i class="fa-solid fa-barcode absolute left-3.5 top-3.5 text-slate-400 text-sm"></i>
                                <button type="button" onclick="clearTrackingInput()" id="clearTrackBtn" class="{{ empty($searchedCode) ? 'hidden' : '' }} absolute right-3 top-2.5 px-2 py-1 text-slate-400 hover:text-white text-xs">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            <!-- Quick Resi Samples -->
                            <div class="flex flex-wrap items-center gap-1.5 text-[11px] text-slate-300">
                                <span class="text-slate-400 font-medium">Contoh:</span>
                                <button type="button" onclick="setAndTrackResi('01100163001740530')" class="px-2 py-0.5 rounded-lg bg-white/10 hover:bg-orange-500 hover:text-white transition font-mono text-[10px] border border-white/15">
                                    01100163001740530 <span class="text-emerald-400 font-bold ml-1">&bull; Terkirim</span>
                                </button>
                                <button type="button" onclick="setAndTrackResi('TRK-20260822-0001')" class="px-2 py-0.5 rounded-lg bg-white/10 hover:bg-orange-500 hover:text-white transition font-mono text-[10px] border border-white/15">
                                    TRK-20260822-0001 <span class="text-blue-400 font-bold ml-1">&bull; Sorting Hub</span>
                                </button>
                                <button type="button" onclick="setAndTrackResi('01100163001736166')" class="px-2 py-0.5 rounded-lg bg-white/10 hover:bg-orange-500 hover:text-white transition font-mono text-[10px] border border-white/15">
                                    01100163001736166 <span class="text-amber-400 font-bold ml-1">&bull; In Transit</span>
                                </button>
                            </div>
                            <button type="submit" id="btnHeroTrackSubmit" class="w-full py-3.5 rounded-xl bg-orange-awx hover:bg-orange-600 text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-orange-900/40 transition flex items-center justify-center space-x-2">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <span id="btnHeroTrackText">Lacak Posisi Paket Sekarang</span>
                            </button>
                        </form>
                    </div>

                    <!-- Tab 2: Rate Calculator Widget -->
                    <div id="tabRateContent" class="hidden space-y-4">
                        <div class="text-xs text-slate-300">
                            Hitung estimasi ongkos kirim berdasarkan kota asal, kota tujuan, dan jenis layanan:
                        </div>
                        <div class="space-y-3 text-xs">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Kota Asal (Origin) *</label>
                                <select id="heroOriginSelect" class="w-full px-3 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white font-semibold outline-none">
                                    <option value="">-- Pilih Kota Asal --</option>
                                    @foreach($cities as $c)
                                        <option value="{{ $c->name }}">{{ $c->name }} ({{ $c->province->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Kota Tujuan (Destination) *</label>
                                <select id="heroDestSelect" class="w-full px-3 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white font-semibold outline-none">
                                    <option value="">-- Pilih Kota Tujuan --</option>
                                    @foreach($cities as $c)
                                        <option value="{{ $c->name }}">{{ $c->name }} ({{ $c->province->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Layanan</label>
                                    <select id="heroServiceSelect" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white font-semibold outline-none">
                                        <option value="Regular">REG (Regular Service)</option>
                                        <option value="Express">ODS (One Day Service)</option>
                                        <option value="SameDay">SDS (Same Day Service)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Berat (Kg)</label>
                                    <input type="number" id="heroWeightInput" value="1" min="1" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white font-bold text-center outline-none">
                                </div>
                            </div>
                            <button type="button" onclick="checkHeroTariff()" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-900/40 transition">
                                <i class="fa-solid fa-calculator mr-1.5"></i> Hitung Tarif Pengiriman
                            </button>
                            <div id="heroTariffResult" class="hidden p-3 rounded-xl bg-slate-900 border border-emerald-500/50 text-center font-bold"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- LIVE TRACKING DIRECT RESULT SECTION (Tanpa Pindah ke Halaman /track) -->
    <section id="liveTrackingContainer" class="{{ empty($searchedCode) ? 'hidden' : '' }} max-w-7xl mx-auto px-4 -mt-10 mb-16 relative z-30 transition-all duration-500">
        @if(!empty($searchedCode))
            @if($searchedShipment)
                <div class="bg-slate-900/95 backdrop-blur-xl border border-slate-700/80 rounded-3xl p-6 sm:p-8 md:p-10 shadow-2xl text-white space-y-8">
                    <!-- Resi Header -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-slate-800 gap-4">
                        <div>
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="px-3 py-1 rounded-full bg-orange-500/20 text-orange-400 border border-orange-500/30 text-[10px] font-black uppercase tracking-wider flex items-center">
                                    <i class="fa-solid fa-satellite-dish mr-1 animate-pulse"></i> Live Real-Time Tracking
                                </span>
                                <span class="text-xs text-slate-400 font-medium">Layanan: <strong class="text-white">{{ $searchedShipment->service_type }}</strong> &bull; Berat: <strong class="text-white">{{ $searchedShipment->weight_kg }} kg</strong></span>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 mt-1">
                                <h2 class="text-2xl sm:text-3xl font-black font-mono tracking-wide text-white">{{ $searchedShipment->tracking_number }}</h2>
                                <button type="button" onclick="copyResiCode('{{ $searchedShipment->tracking_number }}')" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 hover:text-white text-xs font-bold transition flex items-center">
                                    <i class="fa-solid fa-copy mr-1.5"></i> Salin Resi
                                </button>
                                <span id="copyResiTooltip" class="hidden text-emerald-400 text-xs font-bold">Tersalin!</span>
                            </div>
                        </div>
                        <div>
                            @php
                                $badgeClass = match($searchedShipment->status) {
                                    'delivered' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/40 shadow-emerald-500/10',
                                    'out_for_delivery' => 'bg-amber-500/20 text-amber-400 border-amber-500/40 shadow-amber-500/10 animate-pulse',
                                    'in_transit' => 'bg-sky-500/20 text-sky-400 border-sky-500/40 shadow-sky-500/10',
                                    'in_sorting_hub' => 'bg-blue-500/20 text-blue-400 border-blue-500/40 shadow-blue-500/10',
                                    default => 'bg-slate-800 text-slate-300 border-slate-700'
                                };
                            @endphp
                            <span class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-2xl border text-xs font-black uppercase tracking-wider shadow-lg {{ $badgeClass }}">
                                <span class="w-2.5 h-2.5 rounded-full bg-current"></span>
                                <span>{{ strtoupper(str_replace('_', ' ', $searchedShipment->status)) }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- Progress Step Bar (5 Steps) -->
                    <div class="py-6 border-b border-slate-800">
                        @php
                            $stepIndex = match($searchedShipment->status) {
                                'picked_up' => 0,
                                'in_sorting_hub' => 1,
                                'in_transit' => 2,
                                'out_for_delivery' => 3,
                                'delivered' => 4,
                                default => 0
                            };
                            $stepLabels = [
                                ['title' => 'Penjemputan', 'sub' => 'Paket Di-Pickup'],
                                ['title' => 'Hub Sortir', 'sub' => 'Sortasi AWX'],
                                ['title' => 'In Transit', 'sub' => 'Armada Linehaul'],
                                ['title' => 'Pengantaran', 'sub' => 'Kurir ke Alamat'],
                                ['title' => 'Terkirim', 'sub' => 'ePOD Selesai'],
                            ];
                        @endphp
                        <div class="grid grid-cols-5 gap-2 sm:gap-4 text-center">
                            @foreach($stepLabels as $idx => $st)
                                <div class="flex flex-col items-center">
                                    <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full flex items-center justify-center font-black text-xs sm:text-sm mb-2 transition {{ $idx <= $stepIndex ? 'bg-orange-awx text-white shadow-lg shadow-orange-500/40 ring-4 ring-orange-500/20' : 'bg-slate-800 text-slate-500 border border-slate-700' }}">
                                        @if($idx < $stepIndex)
                                            <i class="fa-solid fa-check"></i>
                                        @else
                                            {{ $idx + 1 }}
                                        @endif
                                    </div>
                                    <span class="text-xs font-extrabold uppercase {{ $idx <= $stepIndex ? 'text-orange-400' : 'text-slate-500' }}">{{ $st['title'] }}</span>
                                    <span class="text-[10px] text-slate-400 hidden sm:block">{{ $st['sub'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Sender, Receiver & ePOD Verification Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <!-- Left Details: Sender, Receiver & ePOD -->
                        <div class="lg:col-span-6 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="bg-slate-950/80 p-4 rounded-2xl border border-slate-800">
                                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center">
                                        <i class="fa-solid fa-arrow-up-from-bracket text-orange-400 mr-1.5"></i> Pengirim (Origin)
                                    </div>
                                    <div class="text-sm font-black text-white">{{ $searchedShipment->sender_name }}</div>
                                    <div class="text-xs text-orange-400 font-semibold mt-1">{{ $searchedShipment->sender_city }}</div>
                                    <div class="text-[11px] text-slate-400 mt-1 line-clamp-2 leading-relaxed">{{ $searchedShipment->sender_address }}</div>
                                </div>

                                <div class="bg-slate-950/80 p-4 rounded-2xl border border-slate-800">
                                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center">
                                        <i class="fa-solid fa-location-dot text-emerald-400 mr-1.5"></i> Penerima (Destination)
                                    </div>
                                    <div class="text-sm font-black text-white">{{ $searchedShipment->recipient_name }}</div>
                                    <div class="text-xs text-emerald-400 font-semibold mt-1">{{ $searchedShipment->recipient_city }}</div>
                                    <div class="text-[11px] text-slate-400 mt-1 line-clamp-2 leading-relaxed">{{ $searchedShipment->recipient_address }}</div>
                                </div>
                            </div>

                            <!-- ePOD Verification Box (If delivered) -->
                            @if($searchedShipment->status === 'delivered')
                                <div class="p-5 rounded-2xl bg-emerald-950/50 border border-emerald-800/80 text-emerald-200 space-y-3">
                                    <div class="flex items-center justify-between border-b border-emerald-800/60 pb-3">
                                        <div class="flex items-center space-x-2 text-xs font-black uppercase tracking-wider text-emerald-400">
                                            <i class="fa-solid fa-circle-check text-base"></i>
                                            <span>Bukti Serah Terima Elektronik (ePOD) Terverifikasi</span>
                                        </div>
                                        <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-900/60 text-emerald-300 font-mono font-bold">VERIFIED</span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                        <div class="space-y-1">
                                            <div><span class="text-slate-400">Penerima:</span> <strong class="text-white">{{ $searchedShipment->pod_receiver_name ?: $searchedShipment->recipient_name }}</strong></div>
                                            <div><span class="text-slate-400">Hubungan:</span> <span class="text-emerald-300 font-semibold">{{ $searchedShipment->pod_receiver_relation ?: 'Penerima Langsung' }}</span></div>
                                            <div><span class="text-slate-400">Waktu Diterima:</span> <span class="font-mono text-white">{{ $searchedShipment->pod_delivered_at ? $searchedShipment->pod_delivered_at->format('d M Y, H:i') . ' WIB' : '-' }}</span></div>
                                        </div>
                                        @if($searchedShipment->pod_signature)
                                            <div class="flex flex-col items-start sm:items-end justify-center">
                                                <span class="text-[10px] text-slate-400 uppercase tracking-wider mb-1">Tanda Tangan Digital:</span>
                                                <div class="bg-white p-2 rounded-xl border border-emerald-500/50 w-36 h-16 flex items-center justify-center shadow-md">
                                                    <img src="{{ $searchedShipment->pod_signature }}" alt="Tanda Tangan ePOD" class="max-h-full max-w-full object-contain">
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Right Details: Vertical Timeline Logs -->
                        <div class="lg:col-span-6 bg-slate-950/80 p-5 rounded-2xl border border-slate-800">
                            <h3 class="text-xs font-black text-slate-300 uppercase tracking-wider mb-4 flex items-center justify-between">
                                <span><i class="fa-solid fa-clock-rotate-left mr-1.5 text-orange-400"></i> Riwayat Perjalanan Paket</span>
                                <span class="text-[10px] text-slate-500 font-mono">Real-Time TMS Sync</span>
                            </h3>

                            <div class="relative pl-6 space-y-5 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-800 max-h-96 overflow-y-auto pr-2">
                                @forelse($searchedShipment->trackingLogs as $log)
                                    <div class="relative">
                                        <div class="absolute -left-[23px] top-1.5 w-3.5 h-3.5 rounded-full {{ $loop->last ? 'bg-emerald-500 shadow-emerald-500/50' : 'bg-orange-500' }} border-2 border-slate-900 shadow"></div>
                                        <div class="flex items-center space-x-2 text-[11px] font-mono text-orange-400 font-bold">
                                            <span>{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }} WIB</span>
                                            <span class="text-slate-500">&bull;</span>
                                            <span class="text-slate-300 font-sans font-semibold text-[10px] uppercase px-1.5 py-0.5 rounded bg-slate-800">{{ $log->status }}</span>
                                        </div>
                                        <div class="text-xs sm:text-sm font-bold text-white mt-0.5 leading-snug">{{ $log->description }}</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5 flex items-center">
                                            <i class="fa-solid fa-location-dot mr-1 text-slate-500"></i> {{ $log->location }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-xs text-slate-400 py-4 text-center">
                                        <i class="fa-solid fa-box-archive text-2xl text-slate-600 mb-2"></i>
                                        <p>Data tracking awal terdaftar di sistem AWX.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="flex flex-wrap items-center justify-between pt-6 border-t border-slate-800 gap-3">
                        <div class="flex items-center space-x-2">
                            <button type="button" onclick="focusTrackingInput()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs uppercase tracking-wider transition">
                                <i class="fa-solid fa-rotate-left mr-1.5"></i> Lacak Resi Lain
                            </button>
                        </div>
                        <a href="https://wa.me/6289634072621?text=Halo%20CS%20AWX,%20saya%20ingin%20menanyakan%20status%20resi%20{{ $searchedShipment->tracking_number }}" target="_blank" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-emerald-900/40 transition flex items-center">
                            <i class="fa-brands fa-whatsapp text-sm mr-1.5"></i> Hubungi Customer Service
                        </a>
                    </div>
                </div>
            @else
                <!-- Resi Not Found Box -->
                <div class="bg-slate-900/95 backdrop-blur-xl border border-rose-900/60 rounded-3xl p-8 sm:p-12 text-center text-white shadow-2xl">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-rose-500/20 text-rose-400 mb-4">
                        <i class="fa-solid fa-circle-exclamation text-3xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black mb-2">Nomor Resi / DO Tidak Ditemukan</h3>
                    <p class="text-slate-300 text-xs sm:text-sm max-w-lg mx-auto leading-relaxed mb-6">
                        Nomor resi <code class="px-2 py-0.5 rounded bg-slate-800 text-rose-400 font-mono font-bold">{{ $searchedCode }}</code> belum terdaftar dalam sistem pengiriman AWX. Silakan periksa kembali atau pilih contoh nomor resi yang valid.
                    </p>
                    <div class="inline-flex flex-wrap items-center justify-center gap-2">
                        <button type="button" onclick="focusTrackingInput()" class="px-5 py-2.5 rounded-xl bg-orange-awx hover:bg-orange-600 text-white font-bold text-xs uppercase tracking-wider transition">
                            <i class="fa-solid fa-rotate-left mr-1.5"></i> Coba Nomor Lain
                        </button>
                        <a href="https://wa.me/6289634072621?text=Halo%20CS%20AWX,%20resi%20{{ urlencode($searchedCode) }}%20tidak%20ditemukan" target="_blank" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider transition">
                            <i class="fa-brands fa-whatsapp mr-1.5"></i> Hubungi Customer Service
                        </a>
                    </div>
                </div>
            @endif
        @endif
    </section>

    <!-- SECTION: FILOSOFI LOGO AWX (Matching PDF Page 2) -->
    <section id="filosofi-logo" class="py-16 px-4 max-w-7xl mx-auto space-y-10">
        <div class="text-center space-y-3 max-w-2xl mx-auto">
            <span class="px-4 py-1.5 rounded-full bg-orange-100 border border-orange-200 text-orange-700 text-xs font-black uppercase tracking-wider">
                Identitas & Makna Brand
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                Filosofi Logo AWX
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 font-medium">
                Setiap elemen bentuk dan warna dalam logo AWX mencerminkan komitmen kami untuk memberikan layanan pengiriman terbaik.
            </p>
        </div>

        <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200/90 shadow-xl grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            <!-- Left Logo Preview -->
            <div class="lg:col-span-5 flex flex-col items-center justify-center p-6 bg-slate-50 rounded-2xl border border-slate-200/70 text-center">
                <img src="{{ asset('images/awx-logo.svg') }}" alt="Logo AWX Antaran Warga Ekspress" class="w-full max-w-sm h-auto object-contain">
                <div class="mt-6 pt-4 border-t border-slate-200 w-full text-center">
                    <span class="text-xs font-extrabold uppercase text-slate-700 tracking-wider">
                        CEPAT &bull; AMAN &bull; TERPERCAYA
                    </span>
                </div>
            </div>

            <!-- Right 6 Philosophy Points (Direct from PDF Page 2) -->
            <div class="lg:col-span-7 space-y-4">
                <div class="flex items-start space-x-3.5 p-3.5 rounded-2xl bg-slate-50 hover:bg-orange-50/50 transition border border-slate-100">
                    <div class="w-8 h-8 rounded-xl bg-orange-500 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm mt-0.5">
                        <i class="fa-solid fa-signature"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-slate-900 uppercase">Inisial AWX</h4>
                        <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                            Merepresentasikan <strong>Antaran Warga Ekspress</strong>, sebagai identitas utama yang kuat, modern, dan mudah diingat oleh seluruh lapisan masyarakat.
                        </p>
                    </div>
                </div>

                <div class="flex items-start space-x-3.5 p-3.5 rounded-2xl bg-slate-50 hover:bg-orange-50/50 transition border border-slate-100">
                    <div class="w-8 h-8 rounded-xl bg-orange-500 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm mt-0.5">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-slate-900 uppercase">Panah ke Depan (Huruf X)</h4>
                        <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                            Membentuk huruf X melambangkan <strong>kecepatan, kemajuan, dan komitmen</strong> untuk selalu bergerak maju memberikan layanan terbaik.
                        </p>
                    </div>
                </div>

                <div class="flex items-start space-x-3.5 p-3.5 rounded-2xl bg-slate-50 hover:bg-orange-50/50 transition border border-slate-100">
                    <div class="w-8 h-8 rounded-xl bg-orange-500 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm mt-0.5">
                        <i class="fa-solid fa-box"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-slate-900 uppercase">Kotak / Paket di Bawah Huruf</h4>
                        <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                            Melambangkan layanan pengiriman barang yang <strong>aman, rapi, dan terpercaya</strong> dalam setiap penanganan paket kiriman.
                        </p>
                    </div>
                </div>

                <div class="flex items-start space-x-3.5 p-3.5 rounded-2xl bg-slate-50 hover:bg-orange-50/50 transition border border-slate-100">
                    <div class="w-8 h-8 rounded-xl bg-orange-500 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm mt-0.5">
                        <i class="fa-solid fa-route"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-slate-900 uppercase">Garis Lengkung Dinamis</h4>
                        <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                            Menggambarkan perjalanan yang <strong>lancar dan konektivitas luas</strong> ke seluruh kota dan tujuan di nusantara.
                        </p>
                    </div>
                </div>

                <div class="flex items-start space-x-3.5 p-3.5 rounded-2xl bg-slate-50 hover:bg-orange-50/50 transition border border-slate-100">
                    <div class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm mt-0.5">
                        <i class="fa-solid fa-palette"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-slate-900 uppercase">Sinergi Warna Biru & Oranye</h4>
                        <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                            <strong>Warna Biru</strong> = terpercaya, profesional dan stabil.<br>
                            <strong>Warna Oranye</strong> = semangat, cepat, dekat dengan masyarakat.<br>
                            Sinergi keduanya mencerminkan layanan yang cepat, aman, dan selalu dapat diandalkan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: VISI & MISI (Matching PDF Page 3) -->
    <section id="visi-misi" class="py-16 px-4 bg-slate-100/70">
        <div class="max-w-7xl mx-auto space-y-12">
            <div class="text-center space-y-3 max-w-2xl mx-auto">
                <span class="px-4 py-1.5 rounded-full bg-slate-200 text-slate-800 text-xs font-black uppercase tracking-wider">
                    Arah & Tujuan Perusahaan
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                    Visi & Misi Kami
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 font-medium">
                    Landasan utama kami dalam melayani jasa pengiriman barang berkualitas di seluruh wilayah Indonesia.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- VISI CARD -->
                <div class="bg-white p-8 sm:p-10 rounded-3xl border border-slate-200/90 shadow-sm card-hover relative overflow-hidden flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="w-14 h-14 rounded-2xl bg-slate-900 text-orange-400 flex items-center justify-center text-2xl font-black shadow-md">
                            <i class="fa-solid fa-bullseye"></i>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight">VISI</h3>
                        <p class="text-slate-700 text-sm sm:text-base leading-relaxed font-semibold">
                            &ldquo;Menjadi perusahaan terbaik dan terpercaya dalam bidang jasa pengiriman barang di wilayah Indonesia dengan memberikan jasa layanan yang berkualitas, tepat waktu, dan bertanggung-jawab serta amanah.&rdquo;
                        </p>
                    </div>
                    <div class="mt-8 pt-4 border-t border-slate-100 text-xs font-bold text-orange-600 flex items-center">
                        <i class="fa-solid fa-circle-check mr-2"></i> Kualitas &bull; Tepat Waktu &bull; Amanah
                    </div>
                </div>

                <!-- MISI CARD -->
                <div class="bg-white p-8 sm:p-10 rounded-3xl border border-slate-200/90 shadow-sm card-hover relative overflow-hidden flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="w-14 h-14 rounded-2xl bg-orange-awx text-white flex items-center justify-center text-2xl font-black shadow-md shadow-orange-500/30">
                            <i class="fa-solid fa-rocket"></i>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight">MISI</h3>
                        <p class="text-slate-700 text-sm leading-relaxed">
                            Peningkatan jasa layanan dan sumber daya (manusia, metode, teknologi, infrastruktur) secara berkesinambungan, sekaligus memperluas jaringan kerja dengan dukungan tenaga-tenaga ahli di bidangnya, profesional, berpengalaman serta penuh tanggung jawab.
                        </p>
                    </div>
                    <div class="mt-8 pt-4 border-t border-slate-100 text-xs font-bold text-slate-600 flex items-center">
                        <i class="fa-solid fa-circle-check mr-2 text-emerald-500"></i> Sumber Daya Handal & Teknologi Mutakhir
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: TENTANG KAMI & STRUKTUR MANAJEMEN (Matching PDF Pages 4 & 5) -->
    <section id="tentang-kami" class="py-16 px-4 max-w-7xl mx-auto space-y-12">
        <div class="text-center space-y-3 max-w-2xl mx-auto">
            <span class="px-4 py-1.5 rounded-full bg-orange-100 border border-orange-200 text-orange-700 text-xs font-black uppercase tracking-wider">
                Profil Perusahaan
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                Tentang ANTARAN WARGA EKSPRESS
            </h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Narrative Column (Page 4) -->
            <div class="lg:col-span-7 bg-white p-8 rounded-3xl border border-slate-200/90 shadow-sm space-y-5">
                <h3 class="text-xl font-black text-slate-900">
                    Jasa Pengiriman Courier & Cargo Berskala Nasional
                </h3>
                
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    <strong>AWX (ANTARAN WARGA EKSPRESS) PT. ANTARAN WARGA EKSPRESS</strong> lahir menjadi perusahaan Courier & Cargo berskala Nasional yang mumpuni dan menjelma menjadi perusahaan jasa pengiriman & logistik yang siap bersaing dalam era globalisasi dengan selalu memberikan kepuasan pelanggan di setiap hal.
                </p>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Sebagai sebuah jasa pengiriman yang memiliki motto <strong>&ldquo;Cepat, Tepat dan Terpercaya&rdquo;</strong>, ANTARAN WARGA EKSPRESS selalu berusaha memberikan kualitas yang terbaik. Kecepatan, keakuratan, dan keselamatan paket dan barang yang dikirimkan menjadi prioritas utama bagi ANTARAN WARGA EKSPRESS untuk kepuasan pelanggan.
                </p>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700">
                    <strong class="text-slate-900">Layanan Inti Kami:</strong> Cargo Domestic Express, City courier, domestic express, international express, logistic & warehouse, Mailroom Management, and dedicated courier.
                </div>
            </div>

            <!-- Legal Detail & Management Board (Page 5) -->
            <div class="lg:col-span-5 bg-slate-900 text-white p-8 rounded-3xl shadow-xl space-y-5">
                <div class="border-b border-slate-800 pb-3">
                    <span class="text-[10px] font-black uppercase text-orange-400 tracking-wider">Legalitas & Manajemen</span>
                    <h3 class="text-lg font-black text-white">PT. ANTARAN WARGA EKSPRESS</h3>
                </div>

                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between border-b border-slate-800/80 pb-1.5">
                        <span class="text-slate-400">Branding:</span>
                        <span class="font-bold text-orange-400">ANTARAN WARGA EXPRESS (AWX)</span>
                    </div>

                    <div class="flex justify-between border-b border-slate-800/80 pb-1.5">
                        <span class="text-slate-400">Komisaris Utama:</span>
                        <span class="font-bold text-white uppercase">WARSITO</span>
                    </div>

                    <div class="flex justify-between border-b border-slate-800/80 pb-1.5">
                        <span class="text-slate-400">Komisaris:</span>
                        <span class="font-bold text-white uppercase">SRI SURYATI</span>
                    </div>

                    <div class="flex justify-between border-b border-slate-800/80 pb-1.5">
                        <span class="text-slate-400">Direktur Utama:</span>
                        <span class="font-bold text-white uppercase">LINA HERWANTI</span>
                    </div>

                    <div class="flex justify-between border-b border-slate-800/80 pb-1.5">
                        <span class="text-slate-400">Manager IT:</span>
                        <span class="font-bold text-white uppercase">MOHAMAD RAMA ZULFIKAR</span>
                    </div>

                    <div class="flex justify-between border-b border-slate-800/80 pb-1.5">
                        <span class="text-slate-400">Manager Operasional:</span>
                        <span class="font-bold text-white uppercase">ADE ISKANDAR</span>
                    </div>

                    <div class="flex justify-between border-b border-slate-800/80 pb-1.5">
                        <span class="text-slate-400">Manager HRD:</span>
                        <span class="font-bold text-white uppercase">IIS MINTARSIH</span>
                    </div>

                    <div class="flex justify-between border-b border-slate-800/80 pb-1.5">
                        <span class="text-slate-400">Cover Area:</span>
                        <span class="font-bold text-emerald-400 uppercase">SELURUH WILAYAH INDONESIA</span>
                    </div>
                </div>

                <div class="pt-2 text-[11px] text-slate-400 leading-tight">
                    <i class="fa-solid fa-location-dot text-orange-400 mr-1"></i>
                    Jalan Kramat Pangeran Syarief No.13 Rt. 001 Rw. 08, Kel. Lubang Buaya, Kec. Cipayung, Jakarta Timur
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: LAYANAN & SERVICE TIERS (Matching PDF Page 6) -->
    <section id="layanan" class="py-16 px-4 bg-slate-100/70">
        <div class="max-w-7xl mx-auto space-y-12">
            <div class="text-center space-y-3 max-w-2xl mx-auto">
                <span class="px-4 py-1.5 rounded-full bg-orange-100 border border-orange-200 text-orange-700 text-xs font-black uppercase tracking-wider">
                    Layanan & Service
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                    Solusi Pengiriman Menyeluruh
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 font-medium">
                    Didukung armada darat, laut, udara, dan warehouse modern untuk memenuhi segala kebutuhan pengiriman barang Anda.
                </p>
            </div>

            <!-- 8 Layanan Grid (Direct from PDF Page 6) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center space-y-2 card-hover">
                    <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 mx-auto flex items-center justify-center text-xl">
                        <i class="fa-solid fa-city"></i>
                    </div>
                    <h4 class="text-xs font-black text-slate-900">Kurir Dalam Kota & Domestik</h4>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center space-y-2 card-hover">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 mx-auto flex items-center justify-center text-xl">
                        <i class="fa-solid fa-plane-departure"></i>
                    </div>
                    <h4 class="text-xs font-black text-slate-900">Pengiriman Internasional & Kargo</h4>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center space-y-2 card-hover">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 mx-auto flex items-center justify-center text-xl">
                        <i class="fa-solid fa-warehouse"></i>
                    </div>
                    <h4 class="text-xs font-black text-slate-900">Pergudangan & Distribusi</h4>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center space-y-2 card-hover">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 mx-auto flex items-center justify-center text-xl">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                    <h4 class="text-xs font-black text-slate-900">Mailing Room Management</h4>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center space-y-2 card-hover">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 mx-auto flex items-center justify-center text-xl">
                        <i class="fa-solid fa-truck"></i>
                    </div>
                    <h4 class="text-xs font-black text-slate-900">Transportasi Darat (Trucking)</h4>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center space-y-2 card-hover">
                    <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 mx-auto flex items-center justify-center text-xl">
                        <i class="fa-solid fa-ship"></i>
                    </div>
                    <h4 class="text-xs font-black text-slate-900">Pengiriman via Udara & Laut</h4>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center space-y-2 card-hover">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 mx-auto flex items-center justify-center text-xl">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <h4 class="text-xs font-black text-slate-900">Kurir Khusus / Dedicated</h4>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center space-y-2 card-hover">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 mx-auto flex items-center justify-center text-xl">
                        <i class="fa-solid fa-passport"></i>
                    </div>
                    <h4 class="text-xs font-black text-slate-900">Forwarding, Customs Clearance</h4>
                </div>
            </div>

            <!-- 3 Tingkatan Service Tiers (SDS, ODS, REG - PDF Page 6) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
                <!-- SDS -->
                <div class="bg-white rounded-3xl p-6 border-t-4 border-amber-400 border-x border-b border-slate-200 shadow-sm space-y-4 card-hover">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-black tracking-wider">SDS</span>
                        <span class="text-xs font-bold text-slate-400">Same Day Service</span>
                    </div>
                    <h3 class="text-xl font-black text-slate-900">Same Day Service</h3>
                    <ul class="text-xs text-slate-600 space-y-2.5">
                        <li class="flex items-center"><i class="fa-solid fa-check text-amber-500 mr-2"></i> Barang dapat diterima di hari yang sama</li>
                        <li class="flex items-center"><i class="fa-solid fa-check text-amber-500 mr-2"></i> Prioritas penanganan khusus</li>
                        <li class="flex items-center"><i class="fa-solid fa-check text-amber-500 mr-2"></i> Didukung oleh transportasi udara</li>
                        <li class="flex items-center"><i class="fa-solid fa-check text-amber-500 mr-2"></i> Tersedia untuk pengiriman antar ibukota</li>
                    </ul>
                </div>

                <!-- ODS -->
                <div class="bg-white rounded-3xl p-6 border-t-4 border-emerald-500 border-x border-b border-slate-200 shadow-sm space-y-4 card-hover">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-black tracking-wider">ODS</span>
                        <span class="text-xs font-bold text-slate-400">One Day Service</span>
                    </div>
                    <h3 class="text-xl font-black text-slate-900">One Day Service</h3>
                    <ul class="text-xs text-slate-600 space-y-2.5">
                        <li class="flex items-center"><i class="fa-solid fa-check text-emerald-500 mr-2"></i> Barang bisa diterima keesokan harinya</li>
                        <li class="flex items-center"><i class="fa-solid fa-check text-emerald-500 mr-2"></i> Penanganan prioritas utama</li>
                        <li class="flex items-center"><i class="fa-solid fa-check text-emerald-500 mr-2"></i> Didukung oleh transportasi udara</li>
                        <li class="flex items-center"><i class="fa-solid fa-check text-emerald-500 mr-2"></i> Tidak ada batasan waktu pemesanan</li>
                        <li class="flex items-center"><i class="fa-solid fa-check text-emerald-500 mr-2"></i> Tersedia untuk pengiriman antar kota besar</li>
                    </ul>
                </div>

                <!-- REG -->
                <div class="bg-white rounded-3xl p-6 border-t-4 border-sky-500 border-x border-b border-slate-200 shadow-sm space-y-4 card-hover">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full bg-sky-100 text-sky-800 text-xs font-black tracking-wider">REG</span>
                        <span class="text-xs font-bold text-slate-400">Regular Service</span>
                    </div>
                    <h3 class="text-xl font-black text-slate-900">Regular Service</h3>
                    <ul class="text-xs text-slate-600 space-y-2.5">
                        <li class="flex items-center"><i class="fa-solid fa-check text-sky-500 mr-2"></i> Opsi pengiriman paling ekonomis</li>
                        <li class="flex items-center"><i class="fa-solid fa-check text-sky-500 mr-2"></i> Barang bisa diterima antara 1-7 hari</li>
                        <li class="flex items-center"><i class="fa-solid fa-check text-sky-500 mr-2"></i> Tidak ada batasan waktu pemesanan</li>
                        <li class="flex items-center"><i class="fa-solid fa-check text-sky-500 mr-2"></i> Hingga ke daerah terpencil dan pelosok</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: DOMESTIC ONLINE NETWORK (Matching PDF Page 7) -->
    <section id="jaringan" class="py-16 px-4 max-w-7xl mx-auto space-y-10">
        <div class="text-center space-y-3 max-w-2xl mx-auto">
            <span class="px-4 py-1.5 rounded-full bg-orange-100 border border-orange-200 text-orange-700 text-xs font-black uppercase tracking-wider">
                Jangkauan Nasional
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                Domestic Online Network
            </h2>
            <p class="text-xs sm:text-sm text-slate-600 font-semibold">
                &ldquo;Antaran Warga Ekspress didukung jaringan dan network yang kuat tersebar di seluruh wilayah Indonesia.&rdquo;
            </p>
        </div>

        <!-- Network Hub Grid Cards -->
        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl space-y-6">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 text-xs">
                @php
                    $regions = [
                        'Banda Aceh', 'Medan', 'Pekanbaru', 'Padang', 'Jambi', 'Pangkal Pinang',
                        'Palembang', 'Bengkulu', 'Bandar Lampung', 'Banten', 'Jawa Barat', 'Jawa Tengah',
                        'Jawa Timur', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 'Kalimantan Barat',
                        'Kalimantan Tengah', 'Kalimantan Timur', 'Kalimantan Selatan', 'Gorontalo',
                        'Sulawesi Tengah', 'Sulawesi Barat', 'Sulawesi Selatan', 'Sulawesi Tenggara',
                        'Sulawesi Utara', 'Maluku', 'Maluku Utara', 'Irian Jaya Barat', 'Papua'
                    ];
                @endphp
                @foreach($regions as $reg)
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 hover:border-orange-400 hover:bg-orange-50/50 transition flex items-center space-x-2">
                        <i class="fa-solid fa-location-dot text-orange-500 text-xs"></i>
                        <span class="font-bold text-slate-800 text-[11px] truncate">{{ $reg }}</span>
                    </div>
                @endforeach
            </div>
            
            <div class="text-center pt-4 border-t border-slate-100 text-xs font-semibold text-slate-500">
                Terhubung dengan sistem tracking terpadu secara real-time dari Sabang hingga Merauke.
            </div>
        </div>
    </section>

    <!-- SECTION: SISTEM TEKNOLOGI & E-POD (Matching PDF Pages 8, 9, 10, 11) -->
    <section id="teknologi" class="py-16 px-4 bg-slate-900 text-white">
        <div class="max-w-7xl mx-auto space-y-12">
            <div class="text-center space-y-3 max-w-2xl mx-auto">
                <span class="px-4 py-1.5 rounded-full bg-orange-500/20 border border-orange-400/30 text-orange-400 text-xs font-black uppercase tracking-wider">
                    Sistem Canggih & Inovasi
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                    Sistem Teknologi & Informasi yang Canggih
                </h2>
                <p class="text-xs sm:text-sm text-slate-300">
                    Didukung sistem back-end canggih, Antaran Warga Ekspress melayani update pelacakan hingga integrasi Marketplace paling rumit dengan otomatisasi status pembayaran dan pengiriman.
                </p>
            </div>

            <!-- 8 Key Capabilities (Page 8) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 space-y-2">
                    <i class="fa-solid fa-mobile-screen text-orange-400 text-2xl"></i>
                    <h4 class="text-xs font-black uppercase text-white">Kurir Berbasis Android</h4>
                    <p class="text-[11px] text-slate-400">Petugas kurir lapangan dilengkapi ponsel pintar untuk update status instan.</p>
                </div>

                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 space-y-2">
                    <i class="fa-solid fa-cloud text-sky-400 text-2xl"></i>
                    <h4 class="text-xs font-black uppercase text-white">Komputasi Berbasis Cloud</h4>
                    <p class="text-[11px] text-slate-400">Jaminan keamanan data pelanggan 24 jam melalui sistem terdedikasi.</p>
                </div>

                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 space-y-2">
                    <i class="fa-solid fa-location-crosshairs text-emerald-400 text-2xl"></i>
                    <h4 class="text-xs font-black uppercase text-white">Geotagging & GPS</h4>
                    <p class="text-[11px] text-slate-400">Penandaan geografis koordinat presisi saat paket diserahkan ke penerima.</p>
                </div>

                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 space-y-2">
                    <i class="fa-solid fa-code-compare text-amber-400 text-2xl"></i>
                    <h4 class="text-xs font-black uppercase text-white">Integrasi API</h4>
                    <p class="text-[11px] text-slate-400">Otomatisasi pengiriman dan update status pembayaran ke sistem client.</p>
                </div>
            </div>

            <!-- E-POD & Dedicate CS Spotlight (Page 10) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- E-POD Box -->
                <div class="p-8 rounded-3xl bg-white/5 border border-white/10 space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center text-lg font-bold">
                            <i class="fa-solid fa-file-pdf"></i>
                        </div>
                        <h3 class="text-xl font-black text-white">E-POD (Electronic Proof of Delivery)</h3>
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Sistem ANTARAN WARGA EKSPRESS menyajikan laporan E-POD (status penerima dengan foto penerima dan tanda tangan digital) dalam format PDF yang dapat diunduh setiap saat secara mandiri oleh Client dengan fasilitas LOGIN system yang kami berikan.
                    </p>
                </div>

                <!-- Dedicated CS Box -->
                <div class="p-8 rounded-3xl bg-white/5 border border-white/10 space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500 text-white flex items-center justify-center text-lg font-bold">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        <h3 class="text-xl font-black text-white">Dedicated Customer Service</h3>
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        ANTARAN WARGA EKSPRESS menyiapkan Dedicated CS untuk menjadi jembatan informasi yang responsif dan siap memberikan report Daily maupun update setiap waktu dengan cepat dalam format PDF maupun EXCEL sesuai kebutuhan para pelanggan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: OUR CLIENT (Matching PDF Page 21) -->
    <section id="klien" class="py-16 px-4 max-w-7xl mx-auto space-y-10">
        <div class="text-center space-y-3 max-w-2xl mx-auto">
            <span class="px-4 py-1.5 rounded-full bg-orange-100 border border-orange-200 text-orange-700 text-xs font-black uppercase tracking-wider">
                Kepercayaan Mitra
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                OUR CLIENT
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 font-medium">
                Dipercaya oleh puluhan institusi perbankan, BUMN, korporasi terkemuka, dan e-commerce di Indonesia.
            </p>
        </div>

        <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200/90 shadow-sm">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6 items-center text-center">
                @php
                    $clients = [
                        'Astra Sedaya Finance (ACC)', 'Bank Central Asia (BCA)', 'Bank Mandiri', 'Bank BNI',
                        'Shinhan Bank', 'BTPN Syariah', 'Pegadaian', 'Kalbe Blackmores',
                        'Ruangguru', 'MNC Media', 'Tempo Scan', 'Metranet Telkom',
                        'Link Net', 'Bapenda Jakarta', 'Kantor Pajak Pratama', 'GudangAda',
                        'PRU Parts Resources', 'KreditPlus', 'MMS Group', 'Trinity Sejahtera',
                        'Mitra Maju Gemilang', 'MHU Coal', 'Juva by ZAP', 'Clairmont'
                    ];
                @endphp
                @foreach($clients as $client)
                    <div class="p-3 rounded-2xl bg-slate-50/80 hover:bg-white border border-slate-100 hover:border-orange-300 transition shadow-sm flex items-center justify-center min-h-[64px] font-extrabold text-xs text-slate-700">
                        {{ $client }}
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- SECTION: KONTAK & HEAD OFFICE (Matching PDF Page 22) -->
    <section id="kontak" class="py-16 px-4 bg-slate-950 text-white">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Left Info -->
            <div class="lg:col-span-6 space-y-6">
                <img src="{{ asset('images/awx-logo.svg') }}" alt="AWX" class="h-14 object-contain">
                <h3 class="text-2xl font-black text-white">PT. ANTARAN WARGA EKSPRESS</h3>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">
                    CEPAT &bull; AMAN &bull; TERPERCAYA
                </p>

                <div class="space-y-4 text-xs text-slate-300 pt-2">
                    <div class="flex items-start space-x-3">
                        <i class="fa-solid fa-location-dot text-orange-500 text-sm mt-0.5"></i>
                        <div>
                            <strong class="text-white block uppercase">HEAD OFFICE / KANTOR OPERASIONAL:</strong>
                            Jalan Kramat Pangeran Syarief No.13 Rt. 001 Rw. 08, Kel. Lubang Buaya, Kec. Cipayung, Jakarta Timur
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <i class="fa-solid fa-warehouse text-orange-500 text-sm mt-0.5"></i>
                        <div>
                            <strong class="text-white block uppercase">WAREHOUSE:</strong>
                            Cluster Al Kalam 4, Blok B1, Rt.010 Rw.01 Jalan Kramat Pangeran Syarief, Gang Ambalat, Lubang Buaya, Cipayung, Jakarta Timur
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-envelope text-orange-500 text-sm"></i>
                        <div>
                            <strong class="text-white">Email Resmi:</strong>
                            <a href="mailto:admin@antaranwargaekspress.com" class="text-orange-400 hover:underline">admin@antaranwargaekspress.com</a>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <i class="fa-solid fa-phone text-orange-500 text-sm mt-0.5"></i>
                        <div>
                            <strong class="text-white block">Telepon / WhatsApp:</strong>
                            <div class="grid grid-cols-2 gap-2 mt-1 font-mono text-slate-300">
                                <span>&bull; 089634072621</span>
                                <span>&bull; 081316600119</span>
                                <span>&bull; 087775724428</span>
                                <span>&bull; 085811436068</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Quick Message Form -->
            <div class="lg:col-span-6 bg-slate-900 p-8 rounded-3xl border border-slate-800 space-y-4">
                <h4 class="text-lg font-black text-white">Hubungi Tim Layanan Pelanggan AWX</h4>
                <p class="text-xs text-slate-400">Silakan kirimkan pertanyaan atau permintaan kerjasama pengiriman kargo dan korporasi.</p>

                @if(session('contact_success'))
                    <div class="p-3.5 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-bold flex items-center space-x-2">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
                        <span>{{ session('contact_success') }}</span>
                    </div>
                @endif

                <div id="contactInquiryFeedback" class="hidden"></div>

                <form id="contactInquiryForm" action="{{ route('contact-inquiries.store') }}" method="POST" onsubmit="handleContactInquirySubmit(event)" class="space-y-3">
                    @csrf
                    <input type="text" name="name" placeholder="Nama Lengkap / Perusahaan" required class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs outline-none focus:border-orange-500">
                    <input type="email" name="email" placeholder="Alamat Email" required class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs outline-none focus:border-orange-500">
                    <input type="text" name="phone" placeholder="Nomor Telepon / WhatsApp" required class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs outline-none focus:border-orange-500">
                    <textarea name="message" rows="3" placeholder="Pesan atau Kebutuhan Pengiriman..." required class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs outline-none focus:border-orange-500"></textarea>
                    <button type="submit" id="btnSubmitContactInquiry" class="w-full py-3 rounded-xl bg-orange-awx hover:bg-orange-600 text-white font-bold text-xs uppercase tracking-wider transition shadow-md shadow-orange-500/20 flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span id="btnSubmitContactInquiryText">Kirim Pesan</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="max-w-7xl mx-auto pt-8 mt-12 border-t border-slate-800/80 text-center text-xs text-slate-500 font-medium">
            &copy; {{ date('Y') }} PT. ANTARAN WARGA EKSPRESS (AWX). All Rights Reserved. &bull; Cepat &bull; Aman &bull; Terpercaya
        </div>
    </section>

    <!-- JavaScript Handlers -->
    <script>
        function toggleMobileMenu() {
            const drawer = document.getElementById('mobileMenuDrawer');
            drawer.classList.toggle('hidden');
        }

        function switchTab(type) {
            const trackBtn = document.getElementById('tabTrackBtn');
            const rateBtn = document.getElementById('tabRateBtn');
            const trackContent = document.getElementById('tabTrackContent');
            const rateContent = document.getElementById('tabRateContent');

            if (type === 'track') {
                trackBtn.className = 'flex-1 py-2.5 rounded-lg bg-orange-awx text-white transition text-center shadow-md';
                rateBtn.className = 'flex-1 py-2.5 rounded-lg text-slate-400 hover:text-white transition text-center';
                trackContent.classList.remove('hidden');
                rateContent.classList.add('hidden');
            } else {
                rateBtn.className = 'flex-1 py-2.5 rounded-lg bg-orange-awx text-white transition text-center shadow-md';
                trackBtn.className = 'flex-1 py-2.5 rounded-lg text-slate-400 hover:text-white transition text-center';
                rateContent.classList.remove('hidden');
                trackContent.classList.add('hidden');
            }
        }

        async function checkHeroTariff() {
            const origin = document.getElementById('heroOriginSelect').value;
            const dest = document.getElementById('heroDestSelect').value;
            const weight = parseFloat(document.getElementById('heroWeightInput').value) || 1;
            const resultBox = document.getElementById('heroTariffResult');

            if (!origin || !dest) {
                alert('Silakan pilih kota asal dan kota tujuan terlebih dahulu.');
                return;
            }

            resultBox.classList.remove('hidden');
            resultBox.innerHTML = '<span class="text-slate-400 text-xs"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Menghitung ongkir...</span>';

            try {
                const response = await fetch(`{{ route('tariffs.calculate') }}?origin_city=${encodeURIComponent(origin)}&destination_city=${encodeURIComponent(dest)}&weight_kg=${weight}`);
                const data = await response.json();

                if (data.status === 'success' && data.data && data.data.length > 0) {
                    let html = '<div class="space-y-1.5 text-xs text-left text-slate-200">';
                    data.data.forEach(t => {
                        html += `<div class="flex justify-between items-center py-1 border-b border-slate-800">
                            <div><strong class="text-orange-400">${t.service_type}</strong> (${t.estimated_days || '1-3 Hari'})</div>
                            <div class="font-mono text-emerald-400 font-bold">Rp ${Number(t.total_price).toLocaleString('id-ID')}</div>
                        </div>`;
                    });
                    html += '</div>';
                    resultBox.innerHTML = html;
                } else {
                    const fallbackPrice = weight * 12000;
                    resultBox.innerHTML = `
                        <div class="text-xs text-left text-slate-200">
                            <div class="flex justify-between py-1 border-b border-slate-800">
                                <div><strong class="text-orange-400">REGULER</strong> (2-3 Hari)</div>
                                <div class="font-mono text-emerald-400 font-bold">Rp ${fallbackPrice.toLocaleString('id-ID')}</div>
                            </div>
                        </div>
                    `;
                }
            } catch (err) {
                const fallbackPrice = weight * 12000;
                resultBox.innerHTML = `
                    <div class="text-xs text-left text-slate-200">
                        <div class="flex justify-between py-1 border-b border-slate-800">
                            <div><strong class="text-orange-400">REGULER</strong> (2-3 Hari)</div>
                            <div class="font-mono text-emerald-400 font-bold">Rp ${fallbackPrice.toLocaleString('id-ID')}</div>
                        </div>
                    </div>
                `;
            }
        }

        // Live Package Tracking on Homepage (Without navigating to /track)
        function toggleClearBtn() {
            const input = document.getElementById('heroTrackingInput');
            const clearBtn = document.getElementById('clearTrackBtn');
            if (input && clearBtn) {
                if (input.value.trim().length > 0) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                }
            }
        }

        function clearTrackingInput() {
            const input = document.getElementById('heroTrackingInput');
            if (input) {
                input.value = '';
                input.focus();
            }
            toggleClearBtn();
        }

        function focusTrackingInput(event) {
            if (event) event.preventDefault();
            switchTab('track');
            const input = document.getElementById('heroTrackingInput');
            if (input) {
                input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                input.focus();
                input.select();
            }
        }

        function setAndTrackResi(code) {
            switchTab('track');
            const input = document.getElementById('heroTrackingInput');
            if (input) {
                input.value = code;
                toggleClearBtn();
            }
            fetchAndRenderTracking(code);
        }

        async function handleLiveTrack(event) {
            if (event) event.preventDefault();
            const input = document.getElementById('heroTrackingInput');
            const code = input ? input.value.trim() : '';
            if (!code) {
                alert('Silakan masukkan nomor resi AWB atau DO terlebih dahulu.');
                if (input) input.focus();
                return;
            }
            await fetchAndRenderTracking(code);
        }

        async function fetchAndRenderTracking(code) {
            const container = document.getElementById('liveTrackingContainer');
            const btnText = document.getElementById('btnHeroTrackText');
            const btnSubmit = document.getElementById('btnHeroTrackSubmit');

            if (container) {
                container.classList.remove('hidden');
                container.innerHTML = `
                    <div class="bg-slate-900/95 backdrop-blur-xl border border-slate-700/80 rounded-3xl p-8 sm:p-12 text-center text-white shadow-2xl space-y-4">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-orange-500/20 text-orange-400 mb-2 animate-bounce">
                            <i class="fa-solid fa-satellite-dish text-2xl"></i>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-black">Melacak Status Resi: <span class="font-mono text-orange-400">${escapeHtml(code)}</span></h3>
                        <p class="text-slate-400 text-xs sm:text-sm max-w-md mx-auto">Menghubungkan ke server AWX Cloud TMS & sinkronisasi data kurir lapangan...</p>
                        <div class="max-w-xs mx-auto pt-2">
                            <div class="w-full h-2 bg-slate-800 rounded-full overflow-hidden">
                                <div class="w-full h-full bg-gradient-to-r from-orange-500 via-amber-400 to-emerald-400 animate-pulse"></div>
                            </div>
                        </div>
                    </div>
                `;
                container.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            if (btnText) btnText.textContent = 'Melacak Paket...';
            if (btnSubmit) btnSubmit.disabled = true;

            try {
                const response = await fetch(`{{ route('api.track') }}?code=${encodeURIComponent(code)}`);
                const json = await response.json();

                if (json.status === 'success' && json.data) {
                    renderTrackingSuccess(json.data);
                    // Update URL silently so direct link can be shared without page reload
                    const url = new URL(window.location.href);
                    url.searchParams.set('code', code);
                    window.history.replaceState({}, '', url);
                } else {
                    renderTrackingNotFound(code, json.message);
                }
            } catch (err) {
                renderTrackingNotFound(code, 'Terjadi kendala saat menghubungi server pelacakan AWX. Silakan coba kembali.');
            } finally {
                if (btnText) btnText.textContent = 'Lacak Posisi Paket Sekarang';
                if (btnSubmit) btnSubmit.disabled = false;
            }
        }

        function renderTrackingSuccess(data) {
            const container = document.getElementById('liveTrackingContainer');
            if (!container) return;

            const statusMap = {
                'picked_up': { label: 'PICKED UP (DIJEMPUT)', color: 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30', step: 1 },
                'in_sorting_hub': { label: 'IN SORTING HUB', color: 'bg-blue-500/20 text-blue-300 border-blue-500/30', step: 2 },
                'in_transit': { label: 'IN TRANSIT (PERJALANAN)', color: 'bg-sky-500/20 text-sky-300 border-sky-500/30', step: 3 },
                'out_for_delivery': { label: 'OUT FOR DELIVERY (PENGANTARAN)', color: 'bg-amber-500/20 text-amber-300 border-amber-500/30 animate-pulse', step: 4 },
                'delivered': { label: 'DELIVERED (TERKIRIM)', color: 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40', step: 5 },
                'failed': { label: 'KENDALA PENGIRIMAN', color: 'bg-rose-500/20 text-rose-300 border-rose-500/30', step: 4 },
                'returned': { label: 'RETURNED TO SENDER', color: 'bg-rose-500/20 text-rose-300 border-rose-500/30', step: 4 }
            };

            const statusInfo = statusMap[data.status] || {
                label: data.status_label || (data.status ? data.status.toUpperCase() : 'PROCESSED'),
                color: 'bg-slate-800 text-slate-300 border-slate-700',
                step: 1
            };

            const steps = [
                { num: 1, title: 'Penjemputan', sub: 'Paket Di-Pickup' },
                { num: 2, title: 'Hub Sortir', sub: 'Sortasi AWX' },
                { num: 3, title: 'In Transit', sub: 'Armada Linehaul' },
                { num: 4, title: 'Pengantaran', sub: 'Kurir ke Alamat' },
                { num: 5, title: 'Terkirim', sub: 'ePOD Selesai' }
            ];

            let stepsHtml = '';
            steps.forEach((st, idx) => {
                const isPassed = (idx + 1) < statusInfo.step;
                const isCurrent = (idx + 1) === statusInfo.step;
                const circleClass = (idx + 1) <= statusInfo.step
                    ? 'bg-orange-awx text-white shadow-lg shadow-orange-500/40 ring-4 ring-orange-500/20'
                    : 'bg-slate-800 text-slate-500 border border-slate-700';
                const textClass = (idx + 1) <= statusInfo.step ? 'text-orange-400' : 'text-slate-500';

                stepsHtml += `
                    <div class="flex flex-col items-center">
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full flex items-center justify-center font-black text-xs sm:text-sm mb-2 transition ${circleClass}">
                            ${isPassed ? '<i class="fa-solid fa-check"></i>' : (idx + 1)}
                        </div>
                        <span class="text-xs font-extrabold uppercase ${textClass}">${st.title}</span>
                        <span class="text-[10px] text-slate-400 hidden sm:block">${st.sub}</span>
                    </div>
                `;
            });

            // ePOD proof box
            let epodHtml = '';
            if (data.status === 'delivered') {
                const sigImg = data.pod_signature ? `
                    <div class="flex flex-col items-start sm:items-end justify-center">
                        <span class="text-[10px] text-slate-400 uppercase tracking-wider mb-1">Tanda Tangan Digital:</span>
                        <div class="bg-white p-2 rounded-xl border border-emerald-500/50 w-36 h-16 flex items-center justify-center shadow-md">
                            <img src="${data.pod_signature}" alt="Tanda Tangan ePOD" class="max-h-full max-w-full object-contain">
                        </div>
                    </div>
                ` : '';

                epodHtml = `
                    <div class="p-5 rounded-2xl bg-emerald-950/50 border border-emerald-800/80 text-emerald-200 space-y-3">
                        <div class="flex items-center justify-between border-b border-emerald-800/60 pb-3">
                            <div class="flex items-center space-x-2 text-xs font-black uppercase tracking-wider text-emerald-400">
                                <i class="fa-solid fa-circle-check text-base"></i>
                                <span>Bukti Serah Terima Elektronik (ePOD) Terverifikasi</span>
                            </div>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-900/60 text-emerald-300 font-mono font-bold">VERIFIED</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="space-y-1">
                                <div><span class="text-slate-400">Penerima:</span> <strong class="text-white">${escapeHtml(data.pod_receiver_name || data.recipient_name)}</strong></div>
                                <div><span class="text-slate-400">Hubungan:</span> <span class="text-emerald-300 font-semibold">${escapeHtml(data.pod_receiver_relation || 'Penerima Langsung')}</span></div>
                                <div><span class="text-slate-400">Waktu Diterima:</span> <span class="font-mono text-white">${escapeHtml(data.pod_delivered_at || '-')}</span></div>
                            </div>
                            ${sigImg}
                        </div>
                    </div>
                `;
            }

            // Timeline logs
            let logsHtml = '';
            if (data.logs && data.logs.length > 0) {
                data.logs.forEach((log, index) => {
                    const isLatest = index === data.logs.length - 1;
                    const dotClass = isLatest ? 'bg-emerald-500 shadow-emerald-500/50' : 'bg-orange-500';
                    logsHtml += `
                        <div class="relative">
                            <div class="absolute -left-[23px] top-1.5 w-3.5 h-3.5 rounded-full ${dotClass} border-2 border-slate-900 shadow"></div>
                            <div class="flex items-center space-x-2 text-[11px] font-mono text-orange-400 font-bold">
                                <span>${escapeHtml(log.date)}</span>
                                <span class="text-slate-500">&bull;</span>
                                <span class="text-slate-300 font-sans font-semibold text-[10px] uppercase px-1.5 py-0.5 rounded bg-slate-800">${escapeHtml(log.status)}</span>
                            </div>
                            <div class="text-xs sm:text-sm font-bold text-white mt-0.5 leading-snug">${escapeHtml(log.description)}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5 flex items-center">
                                <i class="fa-solid fa-location-dot mr-1 text-slate-500"></i> ${escapeHtml(log.location || '-')}
                            </div>
                        </div>
                    `;
                });
            } else {
                logsHtml = `
                    <div class="text-xs text-slate-400 py-4 text-center">
                        <i class="fa-solid fa-box-archive text-2xl text-slate-600 mb-2"></i>
                        <p>Data tracking awal terdaftar di sistem AWX.</p>
                    </div>
                `;
            }

            container.innerHTML = `
                <div class="bg-slate-900/95 backdrop-blur-xl border border-slate-700/80 rounded-3xl p-6 sm:p-8 md:p-10 shadow-2xl text-white space-y-8 animate-fadeIn">
                    <!-- Resi Header -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-slate-800 gap-4">
                        <div>
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="px-3 py-1 rounded-full bg-orange-500/20 text-orange-400 border border-orange-500/30 text-[10px] font-black uppercase tracking-wider flex items-center">
                                    <i class="fa-solid fa-satellite-dish mr-1 animate-pulse"></i> Live Real-Time Tracking
                                </span>
                                <span class="text-xs text-slate-400 font-medium">Layanan: <strong class="text-white">${escapeHtml(data.service_type || 'REGULER')}</strong> &bull; Berat: <strong class="text-white">${escapeHtml(data.weight_kg || '1.0')} kg</strong></span>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 mt-1">
                                <h2 class="text-2xl sm:text-3xl font-black font-mono tracking-wide text-white">${escapeHtml(data.tracking_number)}</h2>
                                <button type="button" onclick="copyResiCode('${escapeHtml(data.tracking_number)}')" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 hover:text-white text-xs font-bold transition flex items-center">
                                    <i class="fa-solid fa-copy mr-1.5"></i> Salin Resi
                                </button>
                                <span id="copyResiTooltip" class="hidden text-emerald-400 text-xs font-bold">Tersalin!</span>
                            </div>
                        </div>
                        <div>
                            <span class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-2xl border text-xs font-black uppercase tracking-wider shadow-lg ${statusInfo.color}">
                                <span class="w-2.5 h-2.5 rounded-full bg-current"></span>
                                <span>${escapeHtml(statusInfo.label)}</span>
                            </span>
                        </div>
                    </div>

                    <!-- Progress Step Bar (5 Steps) -->
                    <div class="py-6 border-b border-slate-800">
                        <div class="grid grid-cols-5 gap-2 sm:gap-4 text-center">
                            ${stepsHtml}
                        </div>
                    </div>

                    <!-- Sender, Receiver & ePOD Verification Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <!-- Left Details: Sender, Receiver & ePOD -->
                        <div class="lg:col-span-6 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="bg-slate-950/80 p-4 rounded-2xl border border-slate-800">
                                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center">
                                        <i class="fa-solid fa-arrow-up-from-bracket text-orange-400 mr-1.5"></i> Pengirim (Origin)
                                    </div>
                                    <div class="text-sm font-black text-white">${escapeHtml(data.sender_name || '-')}</div>
                                    <div class="text-xs text-orange-400 font-semibold mt-1">${escapeHtml(data.sender_city || '-')}</div>
                                    <div class="text-[11px] text-slate-400 mt-1 line-clamp-2 leading-relaxed">${escapeHtml(data.sender_address || '-')}</div>
                                </div>

                                <div class="bg-slate-950/80 p-4 rounded-2xl border border-slate-800">
                                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center">
                                        <i class="fa-solid fa-location-dot text-emerald-400 mr-1.5"></i> Penerima (Destination)
                                    </div>
                                    <div class="text-sm font-black text-white">${escapeHtml(data.recipient_name || '-')}</div>
                                    <div class="text-xs text-emerald-400 font-semibold mt-1">${escapeHtml(data.recipient_city || '-')}</div>
                                    <div class="text-[11px] text-slate-400 mt-1 line-clamp-2 leading-relaxed">${escapeHtml(data.recipient_address || '-')}</div>
                                </div>
                            </div>

                            ${epodHtml}
                        </div>

                        <!-- Right Details: Vertical Timeline Logs -->
                        <div class="lg:col-span-6 bg-slate-950/80 p-5 rounded-2xl border border-slate-800">
                            <h3 class="text-xs font-black text-slate-300 uppercase tracking-wider mb-4 flex items-center justify-between">
                                <span><i class="fa-solid fa-clock-rotate-left mr-1.5 text-orange-400"></i> Riwayat Perjalanan Paket</span>
                                <span class="text-[10px] text-slate-500 font-mono">Real-Time TMS Sync</span>
                            </h3>

                            <div class="relative pl-6 space-y-5 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-800 max-h-96 overflow-y-auto pr-2">
                                ${logsHtml}
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="flex flex-wrap items-center justify-between pt-6 border-t border-slate-800 gap-3">
                        <div class="flex items-center space-x-2">
                            <button type="button" onclick="focusTrackingInput()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs uppercase tracking-wider transition">
                                <i class="fa-solid fa-rotate-left mr-1.5"></i> Lacak Resi Lain
                            </button>
                        </div>
                        <a href="https://wa.me/6289634072621?text=Halo%20CS%20AWX,%20saya%20ingin%20menanyakan%20status%20resi%20${encodeURIComponent(data.tracking_number)}" target="_blank" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-emerald-900/40 transition flex items-center">
                            <i class="fa-brands fa-whatsapp text-sm mr-1.5"></i> Hubungi Customer Service
                        </a>
                    </div>
                </div>
            `;
            container.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function renderTrackingNotFound(code, message) {
            const container = document.getElementById('liveTrackingContainer');
            if (!container) return;

            container.innerHTML = `
                <div class="bg-slate-900/95 backdrop-blur-xl border border-rose-900/60 rounded-3xl p-8 sm:p-12 text-center text-white shadow-2xl">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-rose-500/20 text-rose-400 mb-4">
                        <i class="fa-solid fa-circle-exclamation text-3xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black mb-2">Nomor Resi / DO Tidak Ditemukan</h3>
                    <p class="text-slate-300 text-xs sm:text-sm max-w-lg mx-auto leading-relaxed mb-6">
                        ${escapeHtml(message || `Nomor resi ${code} belum terdaftar dalam sistem pengiriman AWX.`)}
                    </p>
                    <div class="inline-flex flex-wrap items-center justify-center gap-2">
                        <button type="button" onclick="focusTrackingInput()" class="px-5 py-2.5 rounded-xl bg-orange-awx hover:bg-orange-600 text-white font-bold text-xs uppercase tracking-wider transition">
                            <i class="fa-solid fa-rotate-left mr-1.5"></i> Coba Nomor Lain
                        </button>
                        <a href="https://wa.me/6289634072621?text=Halo%20CS%20AWX,%20resi%20${encodeURIComponent(code)}%20tidak%20ditemukan" target="_blank" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider transition">
                            <i class="fa-brands fa-whatsapp mr-1.5"></i> Hubungi Customer Service
                        </a>
                    </div>
                </div>
            `;
            container.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function copyResiCode(code) {
            if (!code) {
                const input = document.getElementById('heroTrackingInput');
                code = input ? input.value : '';
            }
            if (!code) return;
            navigator.clipboard.writeText(code).then(() => {
                const tooltip = document.getElementById('copyResiTooltip');
                if (tooltip) {
                    tooltip.classList.remove('hidden');
                    setTimeout(() => {
                        tooltip.classList.add('hidden');
                    }, 2500);
                }
            });
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        async function handleContactInquirySubmit(event) {
            event.preventDefault();
            const form = document.getElementById('contactInquiryForm');
            const feedback = document.getElementById('contactInquiryFeedback');
            const btn = document.getElementById('btnSubmitContactInquiry');
            const btnText = document.getElementById('btnSubmitContactInquiryText');

            if (!form) return;

            if (btn) btn.disabled = true;
            if (btnText) btnText.textContent = 'Mengirim Pesan...';
            if (feedback) feedback.className = 'hidden';

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                const result = await response.json();

                if (response.ok && result.status === 'success') {
                    if (feedback) {
                        feedback.className = 'p-3.5 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-bold flex items-center space-x-2';
                        feedback.innerHTML = `<i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i><span>${escapeHtml(result.message)}</span>`;
                    }
                    form.reset();
                } else {
                    const errMsg = result.message || 'Gagal mengirim pesan. Mohon periksa kembali isian Anda.';
                    if (feedback) {
                        feedback.className = 'p-3.5 rounded-xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs font-bold flex items-center space-x-2';
                        feedback.innerHTML = `<i class="fa-solid fa-circle-exclamation text-rose-400 text-sm"></i><span>${escapeHtml(errMsg)}</span>`;
                    }
                }
            } catch (err) {
                form.submit();
                return;
            } finally {
                if (btn) btn.disabled = false;
                if (btnText) btnText.textContent = 'Kirim Pesan';
            }
        }

        // On page load, if tracking number was supplied in URL, smoothly scroll to it
        document.addEventListener('DOMContentLoaded', function() {
            toggleClearBtn();
            @if(!empty($searchedCode))
                const container = document.getElementById('liveTrackingContainer');
                if (container) {
                    setTimeout(() => {
                        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 300);
                }
            @endif
        });
    </script>
</body>
</html>
