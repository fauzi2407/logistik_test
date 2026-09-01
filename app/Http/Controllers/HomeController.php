<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\BranchHub;
use App\Models\City;
use App\Models\Shipment;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $appName = AppSetting::get('app_name', 'Radja Express Logistics');
        $appTagline = AppSetting::get('app_tagline', 'Logistics & Supply Chain Management System');
        $companyPhone = AppSetting::get('company_phone', '021-7654321');
        $companyEmail = AppSetting::get('company_email', 'info@logistik.com');
        $companyAddress = AppSetting::get('company_address', 'Jl. Logistik Utama No. 88, Jakarta Selatan');
        $appLogoUrl = AppSetting::get('app_logo_url', null);

        // Company Profile Dynamic Settings
        $heroBadge = AppSetting::get('hero_badge', 'Penyedia Jasa Ekspedisi & Supply Chain Logistik #1');
        $heroTitle = AppSetting::get('hero_title', 'Solusi Pengiriman Cargo & Logistik Terpercaya di Indonesia');
        $heroSubtitle = AppSetting::get('hero_subtitle', 'Pengiriman paket cepat, handal, dan aman hingga ke pelosok nusantara. Dilengkapi pelacakan real-time GPS, konfirmasi bukti penerimaan digital (ePOD), & jaminan SLA tepat waktu.');
        $aboutUsDescription = AppSetting::get('about_us_description', 'Kami mengintegrasikan teknologi logistik modern dengan jaringan hub transit yang luas untuk memberikan kenyamanan dan kepastian dalam setiap pengiriman.');
        $operationalHours = AppSetting::get('operational_hours', 'Senin - Sabtu: 08:00 - 20:00 WIB');

        $totalHubs = BranchHub::count();
        $totalDelivered = Shipment::where('status', 'delivered')->count();
        if ($totalDelivered < 100) {
            $totalDelivered = 15480; // Display impressive milestone counter
        }

        $cities = City::orderBy('name')->get();
        $hubs = BranchHub::orderBy('name')->get();

        return view('welcome', compact(
            'appName',
            'appTagline',
            'companyPhone',
            'companyEmail',
            'companyAddress',
            'appLogoUrl',
            'heroBadge',
            'heroTitle',
            'heroSubtitle',
            'aboutUsDescription',
            'operationalHours',
            'totalHubs',
            'totalDelivered',
            'cities',
            'hubs'
        ));
    }
}
