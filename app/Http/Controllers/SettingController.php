<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'app_name' => AppSetting::get('app_name', 'Radja Express Logistics'),
            'app_tagline' => AppSetting::get('app_tagline', 'Logistics & Supply Chain Management System'),
            'app_logo_icon' => AppSetting::get('app_logo_icon', 'fa-truck-fast'),
            'app_logo_url' => AppSetting::get('app_logo_url', null),
            'app_theme' => AppSetting::get('app_theme', 'indigo'),
            'company_phone' => AppSetting::get('company_phone', '021-7654321'),
            'company_email' => AppSetting::get('company_email', 'info@logistik.com'),
            'company_address' => AppSetting::get('company_address', 'Jl. Logistik Utama No. 88, Jakarta Selatan'),
            'bank_name' => AppSetting::get('bank_name', 'Bank BCA'),
            'bank_account_number' => AppSetting::get('bank_account_number', '8877-6655-44'),
            'bank_account_name' => AppSetting::get('bank_account_name', 'PT Radja Express Logistics'),
            
            // Website Company Profile & Hero Banner Settings
            'hero_badge' => AppSetting::get('hero_badge', 'Penyedia Jasa Ekspedisi & Supply Chain Logistik #1'),
            'hero_title' => AppSetting::get('hero_title', 'Solusi Pengiriman Cargo & Logistik Terpercaya di Indonesia'),
            'hero_subtitle' => AppSetting::get('hero_subtitle', 'Pengiriman paket cepat, handal, dan aman hingga ke pelosok nusantara. Dilengkapi pelacakan real-time GPS, konfirmasi bukti penerimaan digital (ePOD), & jaminan SLA tepat waktu.'),
            'about_us_description' => AppSetting::get('about_us_description', 'Kami mengintegrasikan teknologi logistik modern dengan jaringan hub transit yang luas untuk memberikan kenyamanan dan kepastian dalam setiap pengiriman.'),
            'operational_hours' => AppSetting::get('operational_hours', 'Senin - Sabtu: 08:00 - 20:00 WIB'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_tagline' => 'nullable|string|max:255',
            'app_logo_icon' => 'required|string|max:100',
            'app_theme' => 'required|string|in:indigo,emerald,amber,rose,slate',
            'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'company_phone' => 'nullable|string|max:100',
            'company_email' => 'nullable|string|max:100',
            'company_address' => 'nullable|string',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_account_name' => 'nullable|string|max:150',
            
            'hero_badge' => 'nullable|string|max:255',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'about_us_description' => 'nullable|string',
            'operational_hours' => 'nullable|string|max:255',
        ]);

        AppSetting::set('app_name', $validated['app_name']);
        AppSetting::set('app_tagline', $validated['app_tagline'] ?? '');
        AppSetting::set('app_logo_icon', $validated['app_logo_icon']);
        AppSetting::set('app_theme', $validated['app_theme']);
        AppSetting::set('company_phone', $validated['company_phone'] ?? '');
        AppSetting::set('company_email', $validated['company_email'] ?? '');
        AppSetting::set('company_address', $validated['company_address'] ?? '');
        AppSetting::set('bank_name', $validated['bank_name'] ?? '');
        AppSetting::set('bank_account_number', $validated['bank_account_number'] ?? '');
        AppSetting::set('bank_account_name', $validated['bank_account_name'] ?? '');

        // Save Company Profile Settings
        AppSetting::set('hero_badge', $validated['hero_badge'] ?? '');
        AppSetting::set('hero_title', $validated['hero_title'] ?? '');
        AppSetting::set('hero_subtitle', $validated['hero_subtitle'] ?? '');
        AppSetting::set('about_us_description', $validated['about_us_description'] ?? '');
        AppSetting::set('operational_hours', $validated['operational_hours'] ?? '');

        // Upload custom logo image if provided
        if ($request->hasFile('logo_file')) {
            $path = $request->file('logo_file')->store('settings', 'public');
            AppSetting::set('app_logo_url', '/storage/' . $path);
        }

        return redirect()->route('settings.index')->with('success', 'Pengaturan Web Company Profile & Branding berhasil diperbarui.');
    }
}
