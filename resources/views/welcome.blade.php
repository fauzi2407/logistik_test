<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $appName }} - {{ $appTagline }}</title>

    <!-- Favicon -->
    @if($appLogoUrl)
        <link rel="icon" type="image/png" href="{{ asset($appLogoUrl) }}">
        <link rel="shortcut icon" href="{{ asset($appLogoUrl) }}">
        <link rel="apple-touch-icon" href="{{ asset($appLogoUrl) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 rx=%2225%22 fill=%22%234f46e5%22/><text y=%22.85em%22 x=%2250%25%22 text-anchor=%22middle%22 font-size=%2255%22>🚚</text></svg>">
    @endif

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hero-gradient {
            background: radial-gradient(circle at 10% 20%, rgba(79, 70, 229, 0.25) 0%, rgba(15, 23, 42, 0.95) 90%),
                        linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #090d16 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-6px);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen">

    <!-- Top Contact Bar -->
    <div class="bg-slate-950 text-slate-400 text-xs py-2 px-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <div class="flex items-center space-x-6">
                <span><i class="fa-solid fa-phone text-indigo-400 mr-1.5"></i> {{ $companyPhone }}</span>
                <span><i class="fa-solid fa-envelope text-indigo-400 mr-1.5"></i> {{ $companyEmail }}</span>
                <span class="hidden md:inline"><i class="fa-solid fa-clock text-indigo-400 mr-1.5"></i> {{ $operationalHours }}</span>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-[11px] font-semibold text-slate-400">Jaringan Hub Transit: <strong>{{ $totalHubs }} Hub Nasional</strong></span>
                @auth
                    <a href="{{ route('dashboard') }}" class="text-indigo-400 font-bold hover:underline">
                        <i class="fa-solid fa-user-gear mr-1"></i> Dashboard Saya
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-indigo-400 font-bold hover:underline">
                        <i class="fa-solid fa-right-to-bracket mr-1"></i> Login Portal
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-3">
                @if($appLogoUrl)
                    <img src="{{ asset($appLogoUrl) }}" alt="Logo" class="w-10 h-10 object-contain">
                @else
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-black text-xl shadow-md">
                        🚚
                    </div>
                @endif
                <div>
                    <span class="text-lg font-black text-slate-900 tracking-tight block leading-tight">{{ $appName }}</span>
                    <span class="text-[10px] font-bold text-indigo-600 tracking-wider uppercase block">{{ $appTagline }}</span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden lg:flex items-center space-x-8 text-xs font-extrabold uppercase tracking-wider text-slate-700">
                <a href="#beranda" class="hover:text-indigo-600 transition">Beranda</a>
                <a href="#layanan" class="hover:text-indigo-600 transition">Layanan Kargo</a>
                <a href="#keunggulan" class="hover:text-indigo-600 transition">Keunggulan</a>
                <a href="#cek-ongkir" class="hover:text-indigo-600 transition">Cek Tarif</a>
                <a href="{{ route('tracking.index') }}" class="hover:text-indigo-600 transition">Lacak Resi AWB</a>
                <a href="#kontak" class="hover:text-indigo-600 transition">Hubungi Kami</a>
            </nav>

            <!-- CTA Action Button & Mobile Hamburger Toggle -->
            <div class="flex items-center space-x-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition items-center">
                        <i class="fa-solid fa-chart-line mr-1.5"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition items-center">
                        <i class="fa-solid fa-right-to-bracket mr-1.5"></i> Portal Sign In
                    </a>
                @endauth

                <!-- Hamburger Button (Mobile) -->
                <button type="button" onclick="toggleMobileMenu()" class="lg:hidden p-2 rounded-xl text-slate-800 hover:bg-slate-100 focus:outline-none transition">
                    <i class="fa-solid fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation Drawer Overlay -->
    <div id="mobileMenuDrawer" class="hidden fixed inset-0 z-50 bg-slate-950/95 backdrop-blur-xl flex flex-col justify-between p-6 transition-all duration-300 lg:hidden">
        <div class="space-y-6">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
                <div class="flex items-center space-x-3">
                    @if($appLogoUrl)
                        <img src="{{ asset($appLogoUrl) }}" alt="Logo" class="w-10 h-10 object-contain">
                    @else
                        <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-black text-xl">🚚</div>
                    @endif
                    <div>
                        <span class="text-base font-black text-white block leading-tight">{{ $appName }}</span>
                        <span class="text-[10px] text-indigo-400 font-bold uppercase block tracking-wider">{{ $appTagline }}</span>
                    </div>
                </div>
                <button onclick="toggleMobileMenu()" class="text-slate-400 hover:text-white p-2 text-2xl">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <nav class="flex flex-col space-y-2 font-extrabold text-sm uppercase text-slate-200">
                <a href="#beranda" onclick="toggleMobileMenu()" class="p-3.5 rounded-xl hover:bg-indigo-950/80 hover:text-indigo-400 transition flex items-center space-x-3">
                    <i class="fa-solid fa-house w-6 text-indigo-400 text-lg"></i>
                    <span>Beranda Utama</span>
                </a>
                <a href="#layanan" onclick="toggleMobileMenu()" class="p-3.5 rounded-xl hover:bg-indigo-950/80 hover:text-indigo-400 transition flex items-center space-x-3">
                    <i class="fa-solid fa-truck-fast w-6 text-indigo-400 text-lg"></i>
                    <span>Layanan Kargo</span>
                </a>
                <a href="#keunggulan" onclick="toggleMobileMenu()" class="p-3.5 rounded-xl hover:bg-indigo-950/80 hover:text-indigo-400 transition flex items-center space-x-3">
                    <i class="fa-solid fa-shield-halved w-6 text-indigo-400 text-lg"></i>
                    <span>Keunggulan Kami</span>
                </a>
                <a href="#cek-ongkir" onclick="toggleMobileMenu()" class="p-3.5 rounded-xl hover:bg-indigo-950/80 hover:text-indigo-400 transition flex items-center space-x-3">
                    <i class="fa-solid fa-calculator w-6 text-indigo-400 text-lg"></i>
                    <span>Cek Tarif Ongkir</span>
                </a>
                <a href="{{ route('tracking.index') }}" onclick="toggleMobileMenu()" class="p-3.5 rounded-xl hover:bg-indigo-950/80 hover:text-indigo-400 transition flex items-center space-x-3">
                    <i class="fa-solid fa-magnifying-glass-location w-6 text-indigo-400 text-lg"></i>
                    <span>Lacak Resi AWB</span>
                </a>
                <a href="#kontak" onclick="toggleMobileMenu()" class="p-3.5 rounded-xl hover:bg-indigo-950/80 hover:text-indigo-400 transition flex items-center space-x-3">
                    <i class="fa-solid fa-phone w-6 text-indigo-400 text-lg"></i>
                    <span>Hubungi Kami</span>
                </a>
            </nav>
        </div>

        <div class="pt-4 border-t border-slate-800/80 space-y-3">
            @auth
                <a href="{{ route('dashboard') }}" class="w-full py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-center text-xs uppercase tracking-wider block shadow-lg">
                    <i class="fa-solid fa-chart-line mr-2"></i> Ke Dashboard Saya
                </a>
            @else
                <a href="{{ route('login') }}" class="w-full py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-center text-xs uppercase tracking-wider block shadow-lg">
                    <i class="fa-solid fa-right-to-bracket mr-2"></i> Login Portal Internal
                </a>
            @endauth
        </div>
    </div>

    <!-- Hero Section (Logistico Inspired Dark Hero Banner) -->
    <section id="beranda" class="hero-gradient text-white pt-12 pb-24 px-4 relative overflow-hidden">
        <!-- Background Ambient Circles -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl"></div>

        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center relative z-10">
            <!-- Left Text Content -->
            <div class="lg:col-span-7 space-y-6">
                <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-bold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>{{ $heroBadge }}</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                    {{ $heroTitle }}
                </h1>

                <p class="text-sm sm:text-base text-slate-300 max-w-2xl leading-relaxed">
                    {{ $heroSubtitle }}
                </p>

                <!-- Hero Features Pills -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-2 text-xs font-bold text-slate-200">
                    <div class="flex items-center space-x-2 bg-white/5 p-2.5 rounded-xl border border-white/10">
                        <i class="fa-solid fa-location-dot text-indigo-400 text-base"></i>
                        <span>Live GPS Tracking</span>
                    </div>
                    <div class="flex items-center space-x-2 bg-white/5 p-2.5 rounded-xl border border-white/10">
                        <i class="fa-solid fa-shield-halved text-emerald-400 text-base"></i>
                        <span>Asuransi Pengiriman</span>
                    </div>
                    <div class="flex items-center space-x-2 bg-white/5 p-2.5 rounded-xl border border-white/10">
                        <i class="fa-solid fa-qrcode text-sky-400 text-base"></i>
                        <span>Digital ePOD Bukti Foto</span>
                    </div>
                </div>
            </div>

            <!-- Right Interactive Tabbed Widget (Live Tracking & Rate Check) -->
            <div class="lg:col-span-5">
                <div class="glass-card p-6 rounded-3xl shadow-2xl space-y-5">
                    <!-- Tabs Navigation -->
                    <div class="flex rounded-xl bg-slate-900/80 p-1 border border-slate-700/60 text-xs font-extrabold">
                        <button onclick="switchTab('track')" id="tabTrackBtn" class="flex-1 py-2.5 rounded-lg bg-indigo-600 text-white transition text-center shadow-md">
                            <i class="fa-solid fa-magnifying-glass-location mr-1.5"></i> Lacak Resi AWB
                        </button>
                        <button onclick="switchTab('rate')" id="tabRateBtn" class="flex-1 py-2.5 rounded-lg text-slate-400 hover:text-white transition text-center">
                            <i class="fa-solid fa-calculator mr-1.5"></i> Cek Tarif Ongkir
                        </button>
                    </div>

                    <!-- Tab 1: Live AWB Tracking -->
                    <div id="tabTrackContent" class="space-y-4">
                        <div class="text-xs text-slate-300">
                            Masukkan Nomor Resi (AWB Tracking) pengiriman Anda untuk memantau posisi lokasi paket secara real-time:
                        </div>
                        <form action="{{ route('tracking.index') }}" method="GET" class="space-y-3">
                            <div class="relative">
                                <input type="text" name="tracking_number" required placeholder="Contoh: TRK-20260824-0001" class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-900/90 border border-slate-700 text-white font-mono font-bold text-xs focus:ring-2 focus:ring-indigo-500 uppercase">
                                <i class="fa-solid fa-barcode absolute left-3.5 top-3.5 text-slate-400 text-sm"></i>
                            </div>
                            <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-indigo-900/40 transition">
                                <i class="fa-solid fa-magnifying-glass mr-1.5"></i> Lacak Posisi Resi Sekarang
                            </button>
                        </form>
                    </div>

                    <!-- Tab 2: Rate Calculator Widget -->
                    <div id="tabRateContent" class="hidden space-y-4">
                        <div class="text-xs text-slate-300">
                            Hitung estimasi biaya pengiriman kargo & paket berdasarkan kota asal dan tujuan:
                        </div>
                        <div class="space-y-3 text-xs">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Kota Asal (Origin) *</label>
                                <select id="heroOriginSelect" class="w-full px-3 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white font-semibold">
                                    <option value="">-- Pilih Kota Asal --</option>
                                    @foreach($cities as $c)
                                        <option value="{{ $c->name }}">{{ $c->name }} ({{ $c->province->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Kota Tujuan (Destination) *</label>
                                <select id="heroDestSelect" class="w-full px-3 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white font-semibold">
                                    <option value="">-- Pilih Kota Tujuan --</option>
                                    @foreach($cities as $c)
                                        <option value="{{ $c->name }}">{{ $c->name }} ({{ $c->province->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Layanan</label>
                                    <select id="heroServiceSelect" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white font-semibold">
                                        <option value="Regular">Regular (2-3 Hari)</option>
                                        <option value="Express">Express (1 Hari)</option>
                                        <option value="SameDay">SameDay (Hari Ini Tiba)</option>
                                        <option value="Cargo">Cargo (Muatan Berat)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Berat (Kg)</label>
                                    <input type="number" id="heroWeightInput" value="1" min="1" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white font-bold text-center">
                                </div>
                            </div>
                            <button type="button" onclick="checkHeroTariff()" class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-900/40 transition">
                                <i class="fa-solid fa-calculator mr-1.5"></i> Hitung Estimasi Ongkir
                            </button>
                            <div id="heroTariffResult" class="hidden p-3 rounded-xl bg-slate-900 border border-emerald-500/50 text-center font-bold"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Counter Bar -->
    <section class="bg-white border-y border-slate-200 py-8 px-4 shadow-sm relative -mt-10 max-w-6xl mx-auto rounded-3xl z-20">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div class="space-y-1">
                <div class="text-3xl sm:text-4xl font-black text-indigo-600 font-mono">{{ number_format($totalDelivered) }}+</div>
                <div class="text-xs font-extrabold text-slate-600 uppercase">Paket Terantar Sukses</div>
            </div>
            <div class="space-y-1">
                <div class="text-3xl sm:text-4xl font-black text-emerald-600 font-mono">99.8%</div>
                <div class="text-xs font-extrabold text-slate-600 uppercase">SLA Tepat Waktu</div>
            </div>
            <div class="space-y-1">
                <div class="text-3xl sm:text-4xl font-black text-indigo-600 font-mono">{{ $totalHubs }} Hub</div>
                <div class="text-xs font-extrabold text-slate-600 uppercase">Gudang Transit Nasional</div>
            </div>
            <div class="space-y-1">
                <div class="text-3xl sm:text-4xl font-black text-sky-600 font-mono">24/7</div>
                <div class="text-xs font-extrabold text-slate-600 uppercase">Dukungan Customer Care</div>
            </div>
        </div>
    </section>

    <!-- Services Section (Logistico Grid Layout) -->
    <section id="layanan" class="py-20 px-4 max-w-7xl mx-auto space-y-12">
        <div class="text-center space-y-3 max-w-2xl mx-auto">
            <span class="px-3.5 py-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-extrabold uppercase tracking-wider">
                Layanan Ekspedisi Unggulan
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                Solusi Pengiriman Lengkap Untuk Kebutuhan Anda
            </h2>
            <p class="text-xs sm:text-sm text-slate-500">
                Kami menyediakan berbagai armada dan moda transportasi pengiriman kargo cepat untuk ritel maupun perusahaan.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Service 1 -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm card-hover space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl font-black shadow-inner">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900">Trucking & Express Darat</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Pengiriman armada darat (FTL & LTL) cepat antar kota dan pulau dengan jadwal keberangkatan harian yang teratur.
                </p>
                <div class="pt-2 text-xs font-bold text-indigo-600 flex items-center space-x-1">
                    <span>Layanan Reguler & Express</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </div>
            </div>

            <!-- Service 2 -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm card-hover space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-2xl font-black shadow-inner">
                    <i class="fa-solid fa-plane-departure"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900">Kargo Udara (Air Freight)</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Pengiriman prioritas kargo udara via penerbangan komersial & charter untuk paket yang membutuhkan kecepatan ekstra.
                </p>
                <div class="pt-2 text-xs font-bold text-sky-600 flex items-center space-x-1">
                    <span>Prioritas Garansi Tiba</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </div>
            </div>

            <!-- Service 3 -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm card-hover space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl font-black shadow-inner">
                    <i class="fa-solid fa-ship"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900">Kargo Laut & Kontainer</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Solusi ekonomis pengiriman paket muatan besar (FCL & LCL) via kapal RORO dan kontainer ke seluruh pelosok pulau.
                </p>
                <div class="pt-2 text-xs font-bold text-emerald-600 flex items-center space-x-1">
                    <span>Kargo Tonase Besar</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </div>
            </div>

            <!-- Service 4 -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm card-hover space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl font-black shadow-inner">
                    <i class="fa-solid fa-warehouse"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900">Fulfillment & Gudang Transit</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Layanan penyimpanan stok barang, perakitan, pengemasan (packing), hingga pencetakan resi bagi mitra e-commerce.
                </p>
                <div class="pt-2 text-xs font-bold text-amber-600 flex items-center space-x-1">
                    <span>Manajemen Stok Gudang</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </div>
            </div>

            <!-- Service 5 -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm card-hover space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl font-black shadow-inner">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900">Penagihan B2B & Customer Invoice</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Sistem invoice penagihan biaya pengiriman secara berkala bagi customer Korporat/Bisnis dengan fasilitas TOP (Term of Payment).
                </p>
                <div class="pt-2 text-xs font-bold text-purple-600 flex items-center space-x-1">
                    <span>Fasilitas Penagihan B2B</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </div>
            </div>

            <!-- Service 6 -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm card-hover space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl font-black shadow-inner">
                    <i class="fa-solid fa-barcode"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900">Portal ePOD Mobile Digital</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Setiap serah terima paket dilengkapi bukti penerimaan digital (ePOD) berupa foto penerima, tanda tangan, & lokasi GPS real-time.
                </p>
                <div class="pt-2 text-xs font-bold text-rose-600 flex items-center space-x-1">
                    <span>Verifikasi GPS & Foto</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us / Features Section -->
    <section id="keunggulan" class="bg-slate-900 text-white py-20 px-4">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-6 space-y-6">
                <span class="px-3.5 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-extrabold uppercase tracking-wider">
                    Mengapa Memilih Kami
                </span>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight">
                    Komitmen Kecepatan & Keamanan <span class="text-emerald-400">Pengiriman Kargo Anda</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                    {{ $aboutUsDescription }}
                </p>

                <div class="space-y-4 pt-2">
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 rounded-xl bg-indigo-600/30 border border-indigo-500/40 text-indigo-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-extrabold text-white">Lacak Posisi Real-Time GPS</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Pantau pergerakan armada dan posisi resi paket Anda kapan saja secara akurat.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600/30 border border-emerald-500/40 text-emerald-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-shield-virus"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-extrabold text-white">Asuransi & Perlindungan Paket</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Jaminan perlindungan nilai deklarasi barang dari resiko kerusakan atau kehilangan.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 rounded-xl bg-sky-600/30 border border-sky-500/40 text-sky-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-calculator"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-extrabold text-white">Tarif Transparan & Hemat</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Biaya ongkir transparan berdasarkan berat/volume tanpa biaya tersembunyi.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Network Hub Cards -->
            <div class="lg:col-span-6 space-y-4">
                <div class="p-6 rounded-3xl bg-slate-800/80 border border-slate-700 shadow-xl space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-700 pb-3">
                        <h4 class="text-sm font-extrabold text-white"><i class="fa-solid fa-warehouse text-indigo-400 mr-2"></i> Jaringan Gudang Transit Hub Logistik</h4>
                        <span class="text-xs font-bold text-emerald-400">{{ count($hubs) }} Hub Aktif</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        @foreach($hubs->take(4) as $h)
                            <div class="p-3 rounded-xl bg-slate-900/90 border border-slate-700/60">
                                <div class="font-extrabold text-white">{{ $h->name }}</div>
                                <div class="text-[11px] text-indigo-300 font-mono font-bold mt-0.5">{{ $h->code }}</div>
                                <div class="text-slate-400 text-[10px] mt-1"><i class="fa-solid fa-location-dot text-rose-400 mr-1"></i> {{ $h->city }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Live Shipping Rate Calculator Section -->
    <section id="cek-ongkir" class="py-20 px-4 max-w-4xl mx-auto">
        <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-xl space-y-6">
            <div class="text-center space-y-2">
                <span class="px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-extrabold uppercase">
                    Live Rate Calculator
                </span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900">Hitung Ongkir Pengiriman Paket Anda</h2>
                <p class="text-xs text-slate-500">Pilih rute pengiriman dan sistem akan langsung menghitung estimasi biaya secara akurat.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs font-semibold">
                <div>
                    <label class="block text-slate-700 mb-1">Kota Asal (Origin) *</label>
                    <select id="mainOriginSelect" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-slate-800 font-bold">
                        <option value="">-- Pilih Kota Asal --</option>
                        @foreach($cities as $c)
                            <option value="{{ $c->name }}">{{ $c->name }} ({{ $c->province->name }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-slate-700 mb-1">Kota Tujuan (Destination) *</label>
                    <select id="mainDestSelect" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-slate-800 font-bold">
                        <option value="">-- Pilih Kota Tujuan --</option>
                        @foreach($cities as $c)
                            <option value="{{ $c->name }}">{{ $c->name }} ({{ $c->province->name }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-slate-700 mb-1">Jenis Layanan Pengiriman</label>
                    <select id="mainServiceSelect" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-slate-800 font-bold">
                        <option value="Regular">Regular (Reguler - SLA 2-3 Hari)</option>
                        <option value="Express">Express (Express - SLA 1 Hari / Besok Tiba)</option>
                        <option value="SameDay">SameDay (Hari Ini Tiba)</option>
                        <option value="Cargo">Cargo (Kargo Berat - Min 10 Kg)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-semibold">
                <div>
                    <label class="block text-slate-700 mb-1">Berat Total Paket (Kg) *</label>
                    <input type="number" id="mainWeightInput" value="1" min="1" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 font-bold text-slate-900 text-center">
                </div>

                <div>
                    <label class="block text-slate-700 mb-1">Nilai Deklarasi Barang Asuransi (Rp)</label>
                    <input type="number" id="mainValueInput" value="0" min="0" placeholder="0" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 font-bold text-slate-900 text-right">
                </div>
            </div>

            <button type="button" onclick="checkMainTariff()" class="w-full py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs uppercase tracking-wider shadow-lg transition">
                <i class="fa-solid fa-calculator mr-1.5"></i> Kkalkulasi Biaya Ongkir Sekarang
            </button>

            <!-- Calculation Result Box -->
            <div id="mainTariffResult" class="hidden p-5 rounded-2xl bg-slate-900 text-white space-y-3 font-mono"></div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="kontak" class="bg-slate-950 text-white pt-16 pb-8 border-t border-slate-800 text-xs">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8 pb-12 border-b border-slate-800">
            <!-- Col 1: Brand Info -->
            <div class="space-y-4">
                <div class="flex items-center space-x-3">
                    @if($appLogoUrl)
                        <img src="{{ asset($appLogoUrl) }}" alt="Logo" class="w-10 h-10 object-contain">
                    @else
                        <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-black text-xl">🚚</div>
                    @endif
                    <div>
                        <span class="text-base font-black text-white block leading-tight">{{ $appName }}</span>
                        <span class="text-[10px] text-indigo-400 font-bold tracking-wider uppercase block">{{ $appTagline }}</span>
                    </div>
                </div>
                <p class="text-slate-400 text-xs leading-relaxed">
                    Penyedia solusi jasa ekspedisi kargo & logistik nasional berbasis sistem cerdas real-time tracking dan manajemen terpadu.
                </p>
            </div>

            <!-- Col 2: Navigation -->
            <div class="space-y-3">
                <h4 class="text-sm font-black uppercase text-white tracking-wider">Navigasi Utama</h4>
                <ul class="space-y-2 text-slate-400 font-semibold">
                    <li><a href="#beranda" class="hover:text-white transition">Beranda Utama</a></li>
                    <li><a href="#layanan" class="hover:text-white transition">Layanan Ekspedisi</a></li>
                    <li><a href="#cek-ongkir" class="hover:text-white transition">Kalkulator Ongkir</a></li>
                    <li><a href="{{ route('tracking.index') }}" class="hover:text-white transition">Lacak Resi AWB</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-white transition">Portal Member / Employee Login</a></li>
                </ul>
            </div>

            <!-- Col 3: Contact Info -->
            <div class="space-y-3">
                <h4 class="text-sm font-black uppercase text-white tracking-wider">Hubungi Kantor Pusat</h4>
                <ul class="space-y-2 text-slate-400 font-semibold">
                    <li><i class="fa-solid fa-location-dot text-indigo-400 mr-2"></i> {{ $companyAddress }}</li>
                    <li><i class="fa-solid fa-phone text-indigo-400 mr-2"></i> {{ $companyPhone }}</li>
                    <li><i class="fa-solid fa-envelope text-indigo-400 mr-2"></i> {{ $companyEmail }}</li>
                    <li><i class="fa-solid fa-clock text-indigo-400 mr-2"></i> Operasional: 24 Jam Non-Stop</li>
                </ul>
            </div>

            <!-- Col 4: Bank Details -->
            <div class="space-y-3">
                <h4 class="text-sm font-black uppercase text-white tracking-wider">Rekening Resmi Pembayaran</h4>
                <div class="p-3.5 bg-slate-900 rounded-xl border border-slate-800 space-y-1">
                    <div class="text-slate-400 font-bold uppercase">Bank BCA Operasional</div>
                    <div class="text-base font-black text-emerald-400 font-mono">789-012-3456</div>
                    <div class="text-[11px] text-slate-400">a.n. PT Radja Express Logistics</div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 pt-6 text-center text-slate-500 text-[11px]">
            &copy; {{ date('Y') }} {{ $appName }}. All Rights Reserved. Powered by Logistico Modern Logistics Platform.
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const drawer = document.getElementById('mobileMenuDrawer');
            if (drawer) {
                drawer.classList.toggle('hidden');
            }
        }

        function switchTab(tab) {
            const trackBtn = document.getElementById('tabTrackBtn');
            const rateBtn = document.getElementById('tabRateBtn');
            const trackContent = document.getElementById('tabTrackContent');
            const rateContent = document.getElementById('tabRateContent');

            if (tab === 'track') {
                trackBtn.className = 'flex-1 py-2.5 rounded-lg bg-indigo-600 text-white transition text-center shadow-md';
                rateBtn.className = 'flex-1 py-2.5 rounded-lg text-slate-400 hover:text-white transition text-center';
                trackContent.classList.remove('hidden');
                rateContent.classList.add('hidden');
            } else {
                rateBtn.className = 'flex-1 py-2.5 rounded-lg bg-indigo-600 text-white transition text-center shadow-md';
                trackBtn.className = 'flex-1 py-2.5 rounded-lg text-slate-400 hover:text-white transition text-center';
                rateContent.classList.remove('hidden');
                trackContent.classList.add('hidden');
            }
        }

        function checkHeroTariff() {
            const origin = document.getElementById('heroOriginSelect').value;
            const dest = document.getElementById('heroDestSelect').value;
            const service = document.getElementById('heroServiceSelect').value;
            const weight = parseFloat(document.getElementById('heroWeightInput').value) || 1;
            const resultBox = document.getElementById('heroTariffResult');

            if (!origin || !dest) {
                alert('Silakan pilih Kota Asal dan Kota Tujuan.');
                return;
            }

            resultBox.classList.remove('hidden');
            resultBox.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Menghitung estimasi ongkir...';

            fetch('/tariffs/calculate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    origin_city: origin,
                    destination_city: dest,
                    service_type: service,
                    weight_kg: weight
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' || data.total_fee > 0) {
                    resultBox.innerHTML = `
                        <div class="text-xs text-slate-400">Rute ${origin} &rarr; ${dest} (${service})</div>
                        <div class="text-xl font-black text-emerald-400 mt-1">Rp ${data.total_fee.toLocaleString('id-ID')}</div>
                        <div class="text-[11px] text-indigo-300 font-bold mt-0.5">Estimasi Tiba: ${data.estimated_days} • Rp ${data.price_per_kg.toLocaleString('id-ID')}/kg</div>
                    `;
                } else {
                    resultBox.innerHTML = '<span class="text-amber-400 text-xs"><i class="fa-solid fa-info-circle mr-1"></i> Tarif tidak ditemukan untuk rute ini.</span>';
                }
            })
            .catch(err => {
                resultBox.innerHTML = '<span class="text-rose-400 text-xs">Gagal menghitung tarif.</span>';
            });
        }

        function checkMainTariff() {
            const origin = document.getElementById('mainOriginSelect').value;
            const dest = document.getElementById('mainDestSelect').value;
            const service = document.getElementById('mainServiceSelect').value;
            const weight = parseFloat(document.getElementById('mainWeightInput').value) || 1;
            const declaredVal = parseFloat(document.getElementById('mainValueInput').value) || 0;
            const resultBox = document.getElementById('mainTariffResult');

            if (!origin || !dest) {
                alert('Silakan pilih Kota Asal dan Kota Tujuan.');
                return;
            }

            resultBox.classList.remove('hidden');
            resultBox.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Menghitung kalkulasi ongkir & asuransi...';

            fetch('/tariffs/calculate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    origin_city: origin,
                    destination_city: dest,
                    service_type: service,
                    weight_kg: weight,
                    declared_value: declaredVal
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' || data.total_fee > 0) {
                    resultBox.innerHTML = `
                        <div class="flex items-center justify-between text-xs text-slate-400 border-b border-slate-800 pb-2">
                            <span>Rute Pengiriman: <strong>${origin} &rarr; ${dest}</strong></span>
                            <span class="px-2 py-0.5 rounded bg-indigo-900 text-indigo-300 font-bold">${service} (${data.estimated_days})</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                            <div>
                                <span class="text-slate-400">Tarif Per Kg:</span>
                                <div class="font-bold text-white">Rp ${data.price_per_kg.toLocaleString('id-ID')}</div>
                            </div>
                            <div>
                                <span class="text-slate-400">Subtotal Ongkir Dasar:</span>
                                <div class="font-bold text-white">Rp ${data.shipping_fee.toLocaleString('id-ID')}</div>
                            </div>
                            <div>
                                <span class="text-slate-400">Premi Asuransi Barang:</span>
                                <div class="font-bold text-emerald-400">Rp ${data.insurance_fee.toLocaleString('id-ID')}</div>
                            </div>
                            <div>
                                <span class="text-slate-400">Total Biaya Pengiriman:</span>
                                <div class="text-lg font-black text-emerald-400">Rp ${data.total_fee.toLocaleString('id-ID')}</div>
                            </div>
                        </div>
                    `;
                } else {
                    resultBox.innerHTML = '<span class="text-amber-400 text-xs"><i class="fa-solid fa-info-circle mr-1"></i> Tarif rute pengiriman ini belum terdaftar di database.</span>';
                }
            })
            .catch(err => {
                resultBox.innerHTML = '<span class="text-rose-400 text-xs">Terjadi kesalahan server saat menghitung tarif.</span>';
            });
        }
    </script>
</body>
</html>
