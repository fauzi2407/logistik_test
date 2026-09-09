<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    @php
        $appName = \App\Models\AppSetting::get('app_name', 'Radja Express Logistics');
        $appTagline = \App\Models\AppSetting::get('app_tagline', 'Logistics & Supply Chain Management System');
        $appLogoIcon = \App\Models\AppSetting::get('app_logo_icon', 'fa-truck-fast');
        $appLogoUrl = \App\Models\AppSetting::get('app_logo_url', null);
        $appTheme = \App\Models\AppSetting::get('app_theme', 'indigo');

        $themeColors = [
            'indigo' => ['primary' => 'indigo', 'bg' => 'bg-indigo-600', 'hover' => 'hover:bg-indigo-700', 'text' => 'text-indigo-600', 'border' => 'border-indigo-600', 'hex' => '%234f46e5'],
            'emerald' => ['primary' => 'emerald', 'bg' => 'bg-emerald-600', 'hover' => 'hover:bg-emerald-700', 'text' => 'text-emerald-600', 'border' => 'border-emerald-600', 'hex' => '%23059669'],
            'amber' => ['primary' => 'amber', 'bg' => 'bg-amber-500', 'hover' => 'hover:bg-amber-600', 'text' => 'text-amber-600', 'border' => 'border-amber-500', 'hex' => '%23d97706'],
            'rose' => ['primary' => 'rose', 'bg' => 'bg-rose-600', 'hover' => 'hover:bg-rose-700', 'text' => 'text-rose-600', 'border' => 'border-rose-600', 'hex' => '%23e11d48'],
            'slate' => ['primary' => 'slate', 'bg' => 'bg-slate-800', 'hover' => 'hover:bg-slate-900', 'text' => 'text-slate-800', 'border' => 'border-slate-800', 'hex' => '%231e293b'],
        ];
        $t = $themeColors[$appTheme] ?? $themeColors['indigo'];
    @endphp

    <title>@yield('title', 'Dashboard') - {{ $appName }}</title>
    
    <!-- Favicon (Samakan Logo Aplikasi & Tab Bar Browser) -->
    @if($appLogoUrl)
        <link rel="icon" type="image/png" href="{{ asset($appLogoUrl) }}">
        <link rel="shortcut icon" href="{{ asset($appLogoUrl) }}">
        <link rel="apple-touch-icon" href="{{ asset($appLogoUrl) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 rx=%2225%22 fill=%22{{ $t['hex'] }}%22/><text y=%22.85em%22 x=%2250%25%22 text-anchor=%22middle%22 font-size=%2255%22>🚚</text></svg>">
    @endif
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-tap-highlight-color: transparent; }
        .touch-btn { touch-action: manipulation; }
    </style>
    @stack('styles')
</head>
<body class="h-full flex overflow-hidden bg-slate-100 text-slate-800 antialiased">

    <!-- Mobile Drawer Overlay -->
    <div id="mobileBackdrop" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-30 hidden md:hidden transition-opacity"></div>

    <!-- Sidebar Navigation (Responsive Drawer on Mobile) -->
    <aside id="mainSidebar" class="w-64 bg-slate-900 text-slate-300 flex flex-col shrink-0 shadow-2xl z-40 fixed inset-y-0 left-0 transform -translate-x-full md:translate-x-0 md:static transition-transform duration-300 ease-in-out">
        <!-- Brand Header -->
        <div class="h-16 md:h-20 flex items-center justify-between px-5 border-b border-slate-800 bg-slate-950/60">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                @if($appLogoUrl)
                    <img src="{{ asset($appLogoUrl) }}" alt="Logo" class="w-9 h-9 object-contain rounded-xl">
                @else
                    <div class="w-9 h-9 rounded-xl {{ $t['bg'] }} flex items-center justify-center text-white shadow-lg group-hover:scale-105 transition">
                        <i class="fa-solid {{ $appLogoIcon }} text-base"></i>
                    </div>
                @endif
                <div>
                    <h1 class="font-extrabold text-white text-sm tracking-tight leading-tight uppercase truncate max-w-[130px]">{{ $appName }}</h1>
                    <p class="text-[10px] text-slate-400 font-medium truncate max-w-[130px]">{{ $appTagline }}</p>
                </div>
            </a>
            <button onclick="toggleMobileSidebar()" class="md:hidden text-slate-400 hover:text-white p-1">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            @if(Auth::user()->role === 'courier')
                <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider px-3 mb-2">Portal Kurir / Driver</div>
                
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-xl font-bold text-xs transition {{ request()->routeIs('dashboard') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-truck-ramp-box text-base"></i>
                    <span>Tugas Pengiriman Saya</span>
                </a>

                <a href="{{ route('courier-payrolls.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-xl font-bold text-xs transition {{ request()->routeIs('courier-payrolls.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-money-bill-wave text-base"></i>
                    <span>Slip Gaji Saya (Paid)</span>
                </a>

                <a href="{{ route('courier-cash-advances.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-xl font-bold text-xs transition {{ request()->routeIs('courier-cash-advances.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-hand-holding-dollar text-base"></i>
                    <span>Pengajuan Kasbon</span>
                </a>

                <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3.5 py-3 rounded-xl font-bold text-xs transition {{ request()->routeIs('notifications.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-bell text-base"></i>
                        <span>Notifikasi</span>
                    </div>
                    @php $courierUnread = Auth::user()->unreadNotificationsCount(); @endphp
                    @if($courierUnread > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500 text-white animate-pulse">{{ $courierUnread > 99 ? '99+' : $courierUnread }}</span>
                    @endif
                </a>

                <div class="pt-4">
                    <a href="{{ route('tracking.index') }}" target="_blank" class="flex items-center space-x-3 px-3.5 py-3 rounded-xl font-bold text-xs text-indigo-400 bg-indigo-950/50 hover:bg-indigo-900/50 border border-indigo-800/40 transition">
                        <i class="fa-solid fa-magnifying-glass-location text-base"></i>
                        <span>Portal Lacak Resi Publik</span>
                    </a>
                </div>
            @elseif(Auth::user()->role === 'customer')
                <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider px-3 mb-2">Portal Customer / Klien</div>
                
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('dashboard') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-chart-line text-sm"></i>
                    <span>Dashboard Customer</span>
                </a>

                <a href="{{ route('delivery-orders.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('delivery-orders.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-file-contract text-sm"></i>
                    <span>DO Saya / Surat Jalan</span>
                </a>

                <a href="{{ route('shipments.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('shipments.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-box text-sm"></i>
                    <span>Resi Pengiriman Saya</span>
                </a>

                <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('notifications.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-bell text-sm"></i>
                        <span>Notifikasi</span>
                    </div>
                    @php $customerUnread = Auth::user()->unreadNotificationsCount(); @endphp
                    @if($customerUnread > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500 text-white animate-pulse">{{ $customerUnread > 99 ? '99+' : $customerUnread }}</span>
                    @endif
                </a>

                <div class="pt-4">
                    <a href="{{ route('tracking.index') }}" target="_blank" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs text-indigo-400 bg-indigo-950/50 hover:bg-indigo-900/50 border border-indigo-800/40 transition">
                        <i class="fa-solid fa-magnifying-glass-location text-sm"></i>
                        <span>Portal Lacak Resi Publik</span>
                    </a>
                </div>
            @else
                <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider px-3 mb-2">Utama & Analitik</div>
                
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('dashboard') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-chart-line text-sm"></i>
                    <span>Dashboard Analytics</span>
                </a>

                <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('notifications.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-bell text-sm"></i>
                        <span>Pusat Notifikasi</span>
                    </div>
                    @php $staffUnread = Auth::user()->unreadNotificationsCount(); @endphp
                    @if($staffUnread > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500 text-white animate-pulse">{{ $staffUnread > 99 ? '99+' : $staffUnread }}</span>
                    @endif
                </a>

                @if(Auth::user()->hasPermission('reports.index', 'view'))
                <a href="{{ route('reports.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('reports.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-file-invoice text-sm"></i>
                    <span>Laporan Keputusan SLA</span>
                </a>
                @endif

                <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider px-3 mt-4 mb-2">Operasional Logistik</div>

                @if(Auth::user()->hasPermission('pos.index', 'view'))
                <a href="{{ route('pos.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('pos.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-cash-register text-sm text-emerald-400"></i>
                    <span>Menu Kasir / POS Resi</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('customers.index', 'view'))
                <a href="{{ route('customers.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('customers.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-building-user text-sm"></i>
                    <span>Data Customer & Bank</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('delivery-orders.index', 'view'))
                <a href="{{ route('delivery-orders.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('delivery-orders.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-file-contract text-sm"></i>
                    <span>DO Customer / Surat Jalan</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('invoices.index', 'view'))
                <a href="{{ route('invoices.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('invoices.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-receipt text-sm"></i>
                    <span>Invoice & Penagihan</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('shipments.index', 'view'))
                <a href="{{ route('shipments.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('shipments.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-box text-sm"></i>
                    <span>Resi Pengiriman (AWB)</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('courier-assignments.index', 'view'))
                <a href="{{ route('courier-assignments.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('courier-assignments.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-clipboard-list text-sm"></i>
                    <span>Penugasan Kurir & Manifes</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('courier-payrolls.index', 'view'))
                <a href="{{ route('courier-payrolls.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('courier-payrolls.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-money-bill-wave text-sm"></i>
                    <span>Penggajian & Komisi Kurir</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('courier-cash-advances.index', 'view'))
                <a href="{{ route('courier-cash-advances.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('courier-cash-advances.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-hand-holding-dollar text-sm"></i>
                    <span>Kasbon Kurir</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('purchases.index', 'view'))
                <a href="{{ route('purchases.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('purchases.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-cart-shopping text-sm"></i>
                    <span>Pembelian Barang (PO)</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('vendors.index', 'view'))
                <a href="{{ route('vendors.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('vendors.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-truck-field text-sm"></i>
                    <span>Master Vendor / Supplier</span>
                </a>
                @endif

                <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider px-3 mt-4 mb-2">Akuntansi & Keuangan</div>

                @if(Auth::user()->hasPermission('accounting.coa.index', 'view'))
                <a href="{{ route('accounting.coa.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('accounting.coa.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-list-ol text-sm"></i>
                    <span>Master COA (Bagan Akun)</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('accounting.initial-balances.index', 'view'))
                <a href="{{ route('accounting.initial-balances.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('accounting.initial-balances.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-scale-balanced text-sm"></i>
                    <span>Saldo Awal Akun</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('accounting.journals.index', 'view'))
                <a href="{{ route('accounting.journals.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('accounting.journals.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-book-journal-whills text-sm"></i>
                    <span>Penjurnalan (Jurnal Umum)</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('accounting.ledger.index', 'view'))
                <a href="{{ route('accounting.ledger.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('accounting.ledger.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-book-bookmark text-sm"></i>
                    <span>Buku Besar (General Ledger)</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('accounting.profit-loss.index', 'view'))
                <a href="{{ route('accounting.profit-loss.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('accounting.profit-loss.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-chart-pie text-sm"></i>
                    <span>Laporan Laba Rugi (P&L)</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('accounting.balance-sheet.index', 'view'))
                <a href="{{ route('accounting.balance-sheet.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('accounting.balance-sheet.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-building-columns text-sm"></i>
                    <span>Laporan Neraca (Balance Sheet)</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('accounting.closing.index', 'view'))
                <a href="{{ route('accounting.closing.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('accounting.closing.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-calendar-check text-sm"></i>
                    <span>Tutup Buku Akhir Tahun</span>
                </a>
                @endif

                <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider px-3 mt-4 mb-2">Master Data & Sistem</div>

                @if(Auth::user()->hasPermission('branch-hubs.index', 'view'))
                <a href="{{ route('branch-hubs.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('branch-hubs.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-warehouse text-sm"></i>
                    <span>Master Transit Hub</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('users.index', 'view'))
                <a href="{{ route('users.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('users.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-users-gear text-sm"></i>
                    <span>Manajemen Pengguna</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('roles.index', 'view'))
                <a href="{{ route('roles.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('roles.*') || request()->routeIs('menus.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-shield-halved text-sm"></i>
                    <span>Manajemen Menu & Role</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('couriers.index', 'view'))
                <a href="{{ route('couriers.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('couriers.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-user-gear text-sm"></i>
                    <span>Master Data Kurir</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('vehicles.index', 'view'))
                <a href="{{ route('vehicles.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('vehicles.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-truck text-sm"></i>
                    <span>Armada Kendaraan</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('tariffs.index', 'view'))
                <a href="{{ route('tariffs.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('tariffs.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-tags text-sm"></i>
                    <span>Master Tarif & Ongkir</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('regions.index', 'view'))
                <a href="{{ route('regions.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('regions.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-map-location-dot text-sm"></i>
                    <span>Master Wilayah & Kode Pos</span>
                </a>
                @endif

                @if(Auth::user()->hasPermission('settings.index', 'view'))
                <a href="{{ route('settings.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->routeIs('settings.*') ? $t['bg'] . ' text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-gear text-sm"></i>
                    <span>Pengaturan Web & Logo</span>
                </a>
                @endif

                <div class="pt-2">
                    <a href="{{ route('tracking.index') }}" target="_blank" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-bold text-xs text-indigo-400 bg-indigo-950/50 hover:bg-indigo-900/50 border border-indigo-800/40 transition">
                        <i class="fa-solid fa-magnifying-glass-location text-sm"></i>
                        <span>Portal Lacak Resi Publik</span>
                    </a>
                </div>
            @endif
        </nav>

        <!-- User Profile Bar Footer -->
        <div class="p-3 border-t border-slate-800 bg-slate-950/60 flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center font-bold text-white text-xs">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
                <div>
                    <div class="text-xs font-bold text-white truncate max-w-[100px]">{{ Auth::user()->name ?? 'User' }}</div>
                    <div class="text-[10px] text-slate-400 uppercase font-semibold">{{ Auth::user()->role ?? 'Admin' }}</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition" title="Logout">
                    <i class="fa-solid fa-right-from-bracket text-base"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Top Navbar Header -->
        <header class="h-16 md:h-20 bg-white border-b border-slate-200/80 px-4 md:px-8 flex items-center justify-between shrink-0 shadow-sm">
            <div class="flex items-center space-x-3">
                <!-- Hamburger Button on Mobile -->
                <button onclick="toggleMobileSidebar()" class="md:hidden p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition focus:outline-none">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>

                <div class="flex items-center space-x-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 hidden sm:inline">Status:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] md:text-xs font-bold bg-emerald-100 text-emerald-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span> Online
                    </span>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <!-- Notification Bell & Dropdown -->
                <div class="relative" id="notifDropdownWrapper">
                    @php
                        $headerUnreadCount = Auth::user()->unreadNotificationsCount();
                        $headerNotifications = Auth::user()->appNotifications()->latest()->take(5)->get();
                    @endphp
                    <button type="button" id="notifDropdownBtn" onclick="toggleNotificationDropdown()" class="relative p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 transition focus:outline-none touch-btn" title="Notifikasi">
                        <i class="fa-solid fa-bell text-sm"></i>
                        @if($headerUnreadCount > 0)
                            <span id="notifBadge" class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-rose-500 text-white text-[10px] font-black rounded-full flex items-center justify-center animate-pulse shadow-sm">
                                {{ $headerUnreadCount > 99 ? '99+' : $headerUnreadCount }}
                            </span>
                        @endif
                    </button>

                    <!-- Dropdown Panel -->
                    <div id="notifDropdownMenu" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden transform transition-all">
                        <div class="p-3.5 bg-slate-900 text-white flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-bell text-sm text-indigo-400"></i>
                                <span class="text-xs font-bold uppercase tracking-wider">Notifikasi</span>
                                @if($headerUnreadCount > 0)
                                    <span id="headerUnreadBadge" class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500 text-white">{{ $headerUnreadCount }} baru</span>
                                @endif
                            </div>
                            @if($headerUnreadCount > 0)
                                <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-[10px] font-semibold text-slate-300 hover:text-white underline">Tandai Dibaca</button>
                                </form>
                            @endif
                        </div>

                        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                            @forelse($headerNotifications as $notif)
                                <form action="{{ route('notifications.read', $notif->id) }}" method="POST" class="m-0 p-0">
                                    @csrf
                                    <button type="submit" class="w-full text-left p-3 hover:bg-slate-50 transition flex items-start space-x-3 {{ !$notif->read_at ? 'bg-indigo-50/40' : '' }}">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center shrink-0 text-xs mt-0.5">
                                            <i class="fa-solid {{ $notif->icon ?? 'fa-bell' }}"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-0.5">
                                                <p class="text-xs font-bold text-slate-800 truncate {{ !$notif->read_at ? 'font-black' : '' }}">{{ $notif->title }}</p>
                                                @if(!$notif->read_at)
                                                    <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0 ml-1"></span>
                                                @endif
                                            </div>
                                            <p class="text-[11px] text-slate-600 line-clamp-2 leading-tight">{{ $notif->message }}</p>
                                            <span class="text-[10px] text-slate-400 font-medium mt-1 block">{{ $notif->created_at->diffForHumans() }}</span>
                                        </div>
                                    </button>
                                </form>
                            @empty
                                <div class="p-6 text-center text-slate-400">
                                    <i class="fa-regular fa-bell-slash text-2xl mb-2 text-slate-300"></i>
                                    <p class="text-xs font-semibold">Belum ada notifikasi</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="p-2.5 bg-slate-50 border-t border-slate-100 text-center">
                            <a href="{{ route('notifications.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 inline-flex items-center space-x-1">
                                <span>Lihat Semua Notifikasi</span>
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>

                @if(Auth::user()->role !== 'courier')
                    <a href="{{ route('settings.index') }}" class="p-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition text-xs font-bold hidden sm:inline-flex items-center">
                        <i class="fa-solid fa-sliders mr-1"></i> Pengaturan
                    </a>
                @endif
                <div class="text-xs font-bold text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl">
                    <i class="fa-solid fa-user-check text-emerald-600 mr-1"></i>
                    <span class="hidden sm:inline">{{ Auth::user()->name }}</span>
                    <span class="uppercase text-[10px] bg-slate-200 px-1.5 py-0.5 rounded ml-1 font-extrabold">{{ Auth::user()->role }}</span>
                </div>
            </div>
        </header>

        <!-- Page Content Area -->
        <main class="flex-1 overflow-y-auto p-4 md:p-8">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="mb-4 md:mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold text-xs flex items-center justify-between shadow-sm">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500"><i class="fa-solid fa-xmark"></i></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mainSidebar');
            const backdrop = document.getElementById('mobileBackdrop');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

        function toggleNotificationDropdown() {
            const menu = document.getElementById('notifDropdownMenu');
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }

        document.addEventListener('click', function(event) {
            const wrapper = document.getElementById('notifDropdownWrapper');
            const menu = document.getElementById('notifDropdownMenu');
            if (wrapper && menu && !wrapper.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
