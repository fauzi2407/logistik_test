<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $appName = \App\Models\AppSetting::get('app_name', 'Radja Express Logistics');
        $appTagline = \App\Models\AppSetting::get('app_tagline', 'Logistics & Supply Chain Management System');
        $appLogoIcon = \App\Models\AppSetting::get('app_logo_icon', 'fa-truck-fast');
        $appLogoUrl = \App\Models\AppSetting::get('app_logo_url', null);
        $appTheme = \App\Models\AppSetting::get('app_theme', 'indigo');

        $themeBtn = [
            'indigo' => 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-600/30 text-indigo-600',
            'emerald' => 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/30 text-emerald-600',
            'amber' => 'bg-amber-500 hover:bg-amber-600 shadow-amber-500/30 text-amber-600',
            'rose' => 'bg-rose-600 hover:bg-rose-700 shadow-rose-600/30 text-rose-600',
            'slate' => 'bg-slate-800 hover:bg-slate-900 shadow-slate-800/30 text-slate-800',
        ];
        $themeHex = ['indigo' => '%234f46e5', 'emerald' => '%23059669', 'amber' => '%23d97706', 'rose' => '%23e11d48', 'slate' => '%231e293b'];
        $hex = $themeHex[$appTheme] ?? '%234f46e5';
        $btnStyle = $themeBtn[$appTheme] ?? $themeBtn['indigo'];
    @endphp

    <title>Login - {{ $appName }}</title>
    
    <!-- Favicon (Samakan Logo Aplikasi & Tab Bar Browser) -->
    @if($appLogoUrl)
        <link rel="icon" type="image/png" href="{{ asset($appLogoUrl) }}">
        <link rel="shortcut icon" href="{{ asset($appLogoUrl) }}">
        <link rel="apple-touch-icon" href="{{ asset($appLogoUrl) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 rx=%2225%22 fill=%22{{ $hex }}%22/><text y=%22.85em%22 x=%2250%25%22 text-anchor=%22middle%22 font-size=%2255%22>🚚</text></svg>">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="h-full flex items-center justify-center p-6 bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950">
    <div class="w-full max-w-md bg-white/95 backdrop-blur-xl p-8 rounded-3xl shadow-2xl border border-white/20">
        <!-- Logo Header -->
        <div class="text-center mb-8">
            @if($appLogoUrl)
                <img src="{{ asset($appLogoUrl) }}" alt="Logo" class="h-16 mx-auto object-contain mb-4">
            @else
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-600 to-indigo-400 mx-auto flex items-center justify-center shadow-xl shadow-indigo-500/30 mb-4 text-white">
                    <i class="fa-solid {{ $appLogoIcon }} text-2xl"></i>
                </div>
            @endif
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight uppercase">{{ $appName }}</h2>
            <p class="text-xs text-slate-500 mt-1 font-medium">{{ $appTagline }}</p>
        </div>

        @if(session('error'))
            <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold text-center">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-envelope text-sm"></i>
                    </div>
                    <input type="email" id="emailInput" name="email" value="admin@logistik.com" required
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-sm font-medium transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </div>
                    <input type="password" id="passwordInput" name="password" value="password" required
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-sm font-medium transition">
                </div>
            </div>

            <div class="flex items-center justify-between py-1">
                <label class="flex items-center space-x-2 text-xs font-medium text-slate-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded text-indigo-600 focus:ring-indigo-500">
                    <span>Ingat Saya</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm transition shadow-lg shadow-indigo-600/30">
                Masuk ke Sistem <i class="fa-solid fa-arrow-right ml-1"></i>
            </button>
        </form>

        <!-- Quick Demo Credentials Box -->
        <div class="mt-8 pt-6 border-t border-slate-100">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 text-center">Akun Demo (Klik untuk Isi Quick Login):</p>
            <div class="grid grid-cols-2 gap-2 text-xs font-medium">
                <button onclick="fillDemo('admin@logistik.com', 'password')" class="p-2 rounded-lg bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 border border-slate-200 text-slate-700 text-left transition">
                    <div class="font-bold">Admin Logistik</div>
                    <div class="text-[10px] text-slate-400 truncate">admin@logistik.com</div>
                </button>

                <button onclick="fillDemo('staff@logistik.com', 'password')" class="p-2 rounded-lg bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 border border-slate-200 text-slate-700 text-left transition">
                    <div class="font-bold">Staf Operasional</div>
                    <div class="text-[10px] text-slate-400 truncate">staff@logistik.com</div>
                </button>

                <button onclick="fillDemo('kurir1@logistik.com', 'password')" class="p-2 rounded-lg bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 border border-slate-200 text-slate-700 text-left transition">
                    <div class="font-bold">Kurir / Driver</div>
                    <div class="text-[10px] text-slate-400 truncate">kurir1@logistik.com</div>
                </button>

                <button onclick="fillDemo('customer@indofood.com', 'password')" class="p-2 rounded-lg bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 border border-slate-200 text-slate-700 text-left transition">
                    <div class="font-bold">Customer / Klien</div>
                    <div class="text-[10px] text-slate-400 truncate">customer@indofood.com</div>
                </button>
            </div>
        </div>
    </div>

    <script>
        function fillDemo(email, pass) {
            document.getElementById('emailInput').value = email;
            document.getElementById('passwordInput').value = pass;
        }
    </script>
</body>
</html>
