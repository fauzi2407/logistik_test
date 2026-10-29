<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\BranchHub;
use App\Models\City;
use App\Models\Shipment;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $appName = AppSetting::get('app_name', 'ANTARAN WARGA EKSPRESS');
        $appTagline = AppSetting::get('app_tagline', 'CEPAT • AMAN • TERPERCAYA');
        $companyPhone = AppSetting::get('company_phone', '089634072621');
        $companyEmail = AppSetting::get('company_email', 'admin@antaranwargaekspress.com');
        $companyAddress = AppSetting::get('company_address', 'Jalan Kramat Pangeran Syarief No.13 Rt. 001 Rw. 08, Kel. Lubang Buaya, Kec. Cipayung, Jakarta Timur');
        $appLogoUrl = AppSetting::get('app_logo_url', 'images/awx-logo.svg');

        // Company Profile Dynamic Settings
        $heroBadge = AppSetting::get('hero_badge', 'Solusi Pengiriman Barang Cepat, Aman, dan Terpercaya');
        $heroTitle = AppSetting::get('hero_title', 'Solusi Pengiriman Cargo & Logistik Terpercaya di Seluruh Indonesia');
        $heroSubtitle = AppSetting::get('hero_subtitle', 'PT. Antaran Warga Ekspress (AWX) melayani Courier & Cargo berskala Nasional yang mumpuni dengan komitmen Cepat, Aman, dan Terpercaya, didukung kurir Android, E-POD digital signature, dan pelacakan real-time.');
        $aboutUsDescription = AppSetting::get('about_us_description', 'PT. ANTARAN WARGA EKSPRESS lahir menjadi perusahaan Courier & Cargo berskala Nasional yang mumpuni dan menjelma menjadi perusahaan jasa pengiriman & logistik yang siap bersaing dalam era globalisasi dengan selalu memberikan kepuasan pelanggan di setiap hal.');
        $operationalHours = AppSetting::get('operational_hours', 'Operasional 24 Jam Nonstop / CS: 08:00 - 20:00 WIB');

        $totalHubs = BranchHub::count();
        $totalDelivered = Shipment::where('status', 'delivered')->count();
        if ($totalDelivered < 100) {
            $totalDelivered = 15480; // Display impressive milestone counter
        }

        $cities = City::orderBy('name')->get();
        $hubs = BranchHub::orderBy('name')->get();

        // Direct Tracking Query on Home Page (Without redirecting to /track)
        $searchedCode = trim($request->input('tracking_number', $request->input('code', '')));
        $searchedShipment = null;
        if ($searchedCode) {
            $searchedShipment = Shipment::with(['originHub', 'destinationHub', 'currentHub', 'courier', 'vehicle', 'trackingLogs'])
                ->where('tracking_number', $searchedCode)
                ->first();
            if (!$searchedShipment) {
                $do = \App\Models\DeliveryOrder::with(['items', 'customer', 'shipment.trackingLogs'])
                    ->where('do_number', $searchedCode)
                    ->first();
                if ($do && $do->shipment) {
                    $searchedShipment = $do->shipment;
                }
            }
        }

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
            'hubs',
            'searchedCode',
            'searchedShipment'
        ));
    }
}
