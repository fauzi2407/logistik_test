<!DOCTYPE html>
<html lang="id" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $appName = \App\Models\AppSetting::get('app_name', 'ANTARAN WARGA EKSPRESS');
        $appTagline = \App\Models\AppSetting::get('app_tagline', 'CEPAT • AMAN • TERPERCAYA');
        $appLogoUrl = \App\Models\AppSetting::get('app_logo_url', 'images/awx-logo.svg');
    @endphp

    <title>Login TMS - {{ $appName }}</title>
    
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/awx-logo-horizontal.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .tms-bg {
            background: linear-gradient(135deg, #f59e0b 0%, #ea580c 50%, #f97316 100%);
            position: relative;
            overflow: hidden;
        }
        .tms-diagonal {
            clip-path: polygon(12% 0, 100% 0, 100% 100%, 0% 100%);
        }
        .login-btn-orange {
            background-color: #f58220;
            transition: all 0.2s ease-in-out;
        }
        .login-btn-orange:hover {
            background-color: #e06c0c;
            transform: translateY(-1px);
            box-shadow: 0 8px 20px -4px rgba(245, 130, 32, 0.4);
        }
    </style>
</head>
<body class="h-full flex flex-col justify-between bg-slate-50 text-slate-800 antialiased">

    <div class="min-h-full flex flex-1">
        <!-- Left Side: Login Form (Matching PDF Page 12) -->
        <div class="flex-1 flex flex-col justify-center py-10 px-6 sm:px-10 lg:px-16 xl:px-24 bg-white z-10">
            <div class="mx-auto w-full max-w-sm">
                
                <!-- AWX Brand Logo -->
                <div class="text-center mb-8">
                    <a href="{{ route('home') }}" class="inline-block group transition transform hover:scale-[1.02]">
                        <img src="{{ asset('images/awx-logo.svg') }}" alt="AWX Antaran Warga Ekspress" class="h-24 mx-auto object-contain">
                    </a>
                    <p class="text-xs text-slate-400 mt-2 font-medium tracking-wide">Login with your account</p>
                </div>

                @if($errors->any())
                    <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold text-center">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold text-center">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <div class="relative">
                            <input type="text" id="emailInput" name="email" value="{{ old('email') }}" required placeholder="Username / Email"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 text-sm font-medium text-slate-800 placeholder-slate-400 outline-none transition bg-slate-50/50">
                        </div>
                    </div>

                    <div>
                        <div class="relative">
                            <input type="password" id="passwordInput" name="password" required placeholder="Password"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 text-sm font-medium text-slate-800 placeholder-slate-400 outline-none transition bg-slate-50/50">
                        </div>
                    </div>

                    <div class="pt-1">
                        <button type="submit" class="w-full py-3 rounded-xl text-white font-bold text-sm shadow-md login-btn-orange">
                            Login
                        </button>
                    </div>

                    <div class="text-center pt-2">
                        <a href="javascript:void(0)" onclick="alert('Silakan hubungi Administrator IT AWX: admin@antaranwargaekspress.com / WA: 089634072621 untuk pemulihan kata sandi.')" class="text-xs text-slate-400 hover:text-orange-600 transition font-medium">
                            Forgot Password ?
                        </a>
                    </div>
                </form>

                <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                    <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-500 hover:text-orange-600 transition inline-flex items-center">
                        <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Profil Perusahaan AWX
                    </a>
                </div>

            </div>
        </div>

        <!-- Right Side: TMS Brand Banner & Isometric 3D Graphic (Matching PDF Page 12) -->
        <div class="hidden lg:flex lg:w-1/2 relative tms-bg flex-col justify-between p-12 overflow-hidden shadow-2xl">
            <!-- Background Polygons & Watermark -->
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-white/10 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-orange-700/20 blur-3xl pointer-events-none"></div>

            <!-- Central TMS Heading & Isometric Logistics Illustration -->
            <div class="relative z-10 my-auto text-center flex flex-col items-center">
                <div class="inline-block px-5 py-2 rounded-2xl bg-white/20 backdrop-blur-md text-white font-black text-4xl tracking-wider mb-2 shadow-inner">
                    TMS
                </div>
                <h2 class="text-white font-extrabold text-xl tracking-wide drop-shadow-sm">
                    Antaran Warga Ekspress
                </h2>
                <p class="text-white/80 text-xs font-medium max-w-sm mt-1">
                    Transportation Management System & Customer Real-Time Portal
                </p>

                <!-- 3D Isometric Logistics Graphic -->
                <div class="w-full max-w-md mt-6 transform hover:scale-105 transition duration-500">
                    <img src="{{ asset('images/awx-tms-illustration.svg') }}" alt="TMS Isometric Antaran Warga Ekspress" class="w-full h-auto drop-shadow-2xl">
                </div>
            </div>

            <!-- Footer Badge -->
            <div class="relative z-10 text-center text-[11px] text-white/70 font-semibold tracking-wider uppercase">
                Cepat • Aman • Terpercaya &bull; Seluruh Wilayah Indonesia
            </div>
        </div>
    </div>
</body>
</html>
