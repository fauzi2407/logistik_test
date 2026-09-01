@extends('layouts.app')

@section('title', 'Pengaturan Web & Company Profile')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-indigo-100 text-indigo-800 mb-1">
                Company Profile & White-Label Configuration
            </span>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Pengaturan Website Company Profile & Branding</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola konten landing page depan (Logistico theme), logo, banner hero, & informasi kontak resmi.</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl font-bold">
            <i class="fa-solid fa-sliders"></i>
        </div>
    </div>

    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Section 1: Konten Landing Page Hero Banner -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2 flex items-center">
                <i class="fa-solid fa-rectangle-ad text-indigo-600 mr-2"></i> 1. Banner Hero Halaman Depan (Logistico Landing Page)
            </h3>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Badge Tagline Hero</label>
                <input type="text" name="hero_badge" value="{{ old('hero_badge', $settings['hero_badge']) }}" placeholder="Contoh: Penyedia Jasa Ekspedisi & Supply Chain Logistik #1" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                <p class="text-[11px] text-slate-400 mt-1">Badge kecil dengan animasi indikator hijau di bagian atas banner utama.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Utama Hero Banner (Title) *</label>
                <input type="text" name="hero_title" value="{{ old('hero_title', $settings['hero_title']) }}" required placeholder="Solusi Pengiriman Cargo & Logistik Terpercaya di Indonesia" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-black text-slate-900">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Singkat Banner Hero (Subtitle)</label>
                <textarea name="hero_subtitle" rows="2" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">{{ old('hero_subtitle', $settings['hero_subtitle']) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Tentang Kami (About Us)</label>
                <textarea name="about_us_description" rows="2" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">{{ old('about_us_description', $settings['about_us_description']) }}</textarea>
            </div>
        </div>

        <!-- Section 2: Branding (Nama & Tagline) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2 flex items-center">
                <i class="fa-solid fa-signature text-indigo-600 mr-2"></i> 2. Identitas Nama & Tagline Perusahaan
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Perusahaan / Aplikasi *</label>
                    <input type="text" name="app_name" value="{{ old('app_name', $settings['app_name']) }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Slogan / Tagline Sub-Header</label>
                    <input type="text" name="app_tagline" value="{{ old('app_tagline', $settings['app_tagline']) }}" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
            </div>
        </div>

        <!-- Section 3: Logo Web -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2 flex items-center">
                <i class="fa-solid fa-icons text-indigo-600 mr-2"></i> 3. Pengaturan Logo Aplikasi
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Custom Image Upload -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Unggah Logo Gambar (PNG / SVG / JPG)</label>
                    <input type="file" name="logo_file" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 transition">
                    
                    @if($settings['app_logo_url'])
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center space-x-3 mt-2">
                            <img src="{{ asset($settings['app_logo_url']) }}" alt="Logo Saat Ini" class="h-10 object-contain">
                            <span class="text-xs font-bold text-slate-600">Logo kustom aktif saat ini</span>
                        </div>
                    @endif
                </div>

                <!-- Icon Fallback Selector -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Ikon Bawaan</label>
                    <div class="grid grid-cols-4 gap-2" id="iconGrid">
                        @php
                            $icons = [
                                'fa-truck-fast' => 'Truk Express',
                                'fa-boxes-stacked' => 'Gudang Paket',
                                'fa-plane-departure' => 'Kargo Udara',
                                'fa-ship' => 'Kargo Laut',
                                'fa-warehouse' => 'Hub Logistik',
                                'fa-shield-halved' => 'Keamanan',
                                'fa-dolly' => 'Kurir Dolly',
                                'fa-envelope-open-text' => 'Dokumen Bank',
                            ];
                        @endphp
                        @foreach($icons as $iconCode => $iconName)
                            <div onclick="selectIcon('{{ $iconCode }}', this)" 
                                 class="icon-card relative p-3 border-2 rounded-xl flex flex-col items-center justify-center cursor-pointer transition select-none {{ $settings['app_logo_icon'] == $iconCode ? 'border-indigo-600 bg-indigo-50/90 text-indigo-700 font-bold shadow-sm' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300' }}">
                                
                                <input type="radio" name="app_logo_icon" value="{{ $iconCode }}" class="hidden" {{ $settings['app_logo_icon'] == $iconCode ? 'checked' : '' }}>
                                <i class="fa-solid {{ $iconCode }} text-xl mb-1"></i>
                                <span class="text-[10px] text-center font-medium leading-tight">{{ $iconName }}</span>
                                
                                <div class="check-badge absolute top-1 right-1 {{ $settings['app_logo_icon'] == $iconCode ? '' : 'hidden' }}">
                                    <i class="fa-solid fa-circle-check text-indigo-600 text-xs"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Tema Warna UI -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2 flex items-center">
                <i class="fa-solid fa-palette text-indigo-600 mr-2"></i> 4. Skema Tema Warna Aplikasi
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3" id="themeGrid">
                @php
                    $themes = [
                        'indigo' => ['name' => 'Indigo Slate', 'color' => 'bg-indigo-600', 'desc' => 'Modern Corporate'],
                        'emerald' => ['name' => 'Emerald Green', 'color' => 'bg-emerald-600', 'desc' => 'Supply Chain Eco'],
                        'amber' => ['name' => 'Amber Gold', 'color' => 'bg-amber-500', 'desc' => 'Premium Cargo'],
                        'rose' => ['name' => 'Crimson Red', 'color' => 'bg-rose-600', 'desc' => 'Express Delivery'],
                        'slate' => ['name' => 'Midnight Slate', 'color' => 'bg-slate-800', 'desc' => 'Dark Tech Theme'],
                    ];
                @endphp

                @foreach($themes as $themeKey => $tInfo)
                    <div onclick="selectTheme('{{ $themeKey }}', this)" 
                         class="theme-card relative p-3 border-2 rounded-xl flex flex-col items-center text-center cursor-pointer transition select-none {{ $settings['app_theme'] == $themeKey ? 'border-indigo-600 bg-indigo-50/90 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                        
                        <input type="radio" name="app_theme" value="{{ $themeKey }}" class="hidden" {{ $settings['app_theme'] == $themeKey ? 'checked' : '' }}>
                        <div class="w-8 h-8 rounded-full {{ $tInfo['color'] }} shadow-md mb-2"></div>
                        <span class="text-xs font-extrabold text-slate-900">{{ $tInfo['name'] }}</span>
                        <span class="text-[10px] text-slate-400 mt-0.5">{{ $tInfo['desc'] }}</span>

                        <div class="check-badge absolute top-1.5 right-1.5 {{ $settings['app_theme'] == $themeKey ? '' : 'hidden' }}">
                            <i class="fa-solid fa-circle-check text-indigo-600 text-xs"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Section 5: Kontak Perusahaan -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2 flex items-center">
                <i class="fa-solid fa-building text-indigo-600 mr-2"></i> 5. Informasi Kontak & Jam Operasional Perusahaan
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Telepon Perusahaan</label>
                    <input type="text" name="company_phone" value="{{ old('company_phone', $settings['company_phone']) }}" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Perusahaan</label>
                    <input type="email" name="company_email" value="{{ old('company_email', $settings['company_email']) }}" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jam Operasional CS</label>
                    <input type="text" name="operational_hours" value="{{ old('operational_hours', $settings['operational_hours']) }}" placeholder="Senin - Sabtu: 08:00 - 20:00 WIB" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Kantor Pusat</label>
                <textarea name="company_address" rows="2" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">{{ old('company_address', $settings['company_address']) }}</textarea>
            </div>
        </div>

        <!-- Section 6: Rekening Bank -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2 flex items-center">
                <i class="fa-solid fa-building-columns text-indigo-600 mr-2"></i> 6. Rekening Bank Perusahaan (Pembayaran Invoice & Landing Page)
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Bank *</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $settings['bank_name']) }}" placeholder="Contoh: Bank BCA" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor Rekening *</label>
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $settings['bank_account_number']) }}" placeholder="Contoh: 8877-6655-44" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Atas Nama (Pemilik Rekening) *</label>
                    <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $settings['bank_account_name']) }}" placeholder="Contoh: PT Radja Express Logistics" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <button type="submit" class="px-8 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg shadow-indigo-600/30 transition">
                <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan Pengaturan Company Profile
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function selectIcon(iconCode, el) {
        document.querySelectorAll('#iconGrid .icon-card').forEach(card => {
            card.classList.remove('border-indigo-600', 'bg-indigo-50/90', 'text-indigo-700', 'font-bold', 'shadow-sm');
            card.classList.add('border-slate-200', 'bg-white', 'text-slate-600');
            card.querySelector('input[type="radio"]').checked = false;
            const badge = card.querySelector('.check-badge');
            if (badge) badge.classList.add('hidden');
        });

        el.classList.remove('border-slate-200', 'bg-white', 'text-slate-600');
        el.classList.add('border-indigo-600', 'bg-indigo-50/90', 'text-indigo-700', 'font-bold', 'shadow-sm');
        const radio = el.querySelector('input[type="radio"]');
        radio.checked = true;
        const badge = el.querySelector('.check-badge');
        if (badge) badge.classList.remove('hidden');
    }

    function selectTheme(themeKey, el) {
        document.querySelectorAll('#themeGrid .theme-card').forEach(card => {
            card.classList.remove('border-indigo-600', 'bg-indigo-50/90', 'shadow-sm');
            card.classList.add('border-slate-200', 'bg-white');
            card.querySelector('input[type="radio"]').checked = false;
            const badge = card.querySelector('.check-badge');
            if (badge) badge.classList.add('hidden');
        });

        el.classList.remove('border-slate-200', 'bg-white');
        el.classList.add('border-indigo-600', 'bg-indigo-50/90', 'shadow-sm');
        const radio = el.querySelector('input[type="radio"]');
        radio.checked = true;
        const badge = el.querySelector('.check-badge');
        if (badge) badge.classList.remove('hidden');
    }
</script>
@endpush
@endsection
