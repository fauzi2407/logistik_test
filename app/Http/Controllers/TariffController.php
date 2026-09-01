<?php

namespace App\Http\Controllers;

use App\Models\BranchHub;
use App\Models\Tariff;
use Illuminate\Http\Request;

class TariffController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $originProvince = $request->input('origin_province');
        $originCity = $request->input('origin_city');
        $originDistrict = $request->input('origin_district');
        $originSubdistrict = $request->input('origin_subdistrict');

        $destProvince = $request->input('destination_province');
        $destCity = $request->input('destination_city');
        $destDistrict = $request->input('destination_district');
        $destSubdistrict = $request->input('destination_subdistrict');

        $serviceType = $request->input('service_type');

        $tariffs = Tariff::when($search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('origin_city', 'like', "%{$search}%")
                        ->orWhere('origin_province', 'like', "%{$search}%")
                        ->orWhere('origin_district', 'like', "%{$search}%")
                        ->orWhere('origin_subdistrict', 'like', "%{$search}%")
                        ->orWhere('destination_city', 'like', "%{$search}%")
                        ->orWhere('destination_province', 'like', "%{$search}%")
                        ->orWhere('destination_district', 'like', "%{$search}%")
                        ->orWhere('destination_subdistrict', 'like', "%{$search}%")
                        ->orWhere('service_type', 'like', "%{$search}%");
                });
            })
            ->when($originProvince, fn($q, $val) => $q->where('origin_province', $val))
            ->when($originCity, fn($q, $val) => $q->where('origin_city', $val))
            ->when($originDistrict, fn($q, $val) => $q->where('origin_district', $val))
            ->when($originSubdistrict, fn($q, $val) => $q->where('origin_subdistrict', $val))
            ->when($destProvince, fn($q, $val) => $q->where('destination_province', $val))
            ->when($destCity, fn($q, $val) => $q->where('destination_city', $val))
            ->when($destDistrict, fn($q, $val) => $q->where('destination_district', $val))
            ->when($destSubdistrict, fn($q, $val) => $q->where('destination_subdistrict', $val))
            ->when($serviceType, fn($q, $val) => $q->where('service_type', $val))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $provinces = \App\Models\Province::orderBy('name')->pluck('name');

        return view('tariffs.index', compact(
            'tariffs', 'provinces', 'search',
            'originProvince', 'originCity', 'originDistrict', 'originSubdistrict',
            'destProvince', 'destCity', 'destDistrict', 'destSubdistrict',
            'serviceType'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'origin_province' => 'nullable|string|max:100',
            'origin_city' => 'required|string|max:100',
            'origin_district' => 'nullable|string|max:100',
            'origin_subdistrict' => 'nullable|string|max:100',
            'destination_province' => 'nullable|string|max:100',
            'destination_city' => 'required|string|max:100',
            'destination_district' => 'nullable|string|max:100',
            'destination_subdistrict' => 'nullable|string|max:100',
            'service_type' => 'required|in:Regular,Express,SameDay',
            'price_per_kg' => 'required|numeric|min:0',
            'min_weight_kg' => 'required|numeric|min:0.1',
            'estimated_days' => 'required|string|max:50',
        ]);

        Tariff::create($validated);
        return redirect()->route('tariffs.index')->with('success', 'Master tarif berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $tariff = Tariff::findOrFail($id);
        $validated = $request->validate([
            'origin_province' => 'nullable|string|max:100',
            'origin_city' => 'required|string|max:100',
            'origin_district' => 'nullable|string|max:100',
            'origin_subdistrict' => 'nullable|string|max:100',
            'destination_province' => 'nullable|string|max:100',
            'destination_city' => 'required|string|max:100',
            'destination_district' => 'nullable|string|max:100',
            'destination_subdistrict' => 'nullable|string|max:100',
            'service_type' => 'required|in:Regular,Express,SameDay',
            'price_per_kg' => 'required|numeric|min:0',
            'min_weight_kg' => 'required|numeric|min:0.1',
            'estimated_days' => 'required|string|max:50',
        ]);

        $tariff->update($validated);
        return redirect()->route('tariffs.index')->with('success', 'Master tarif berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tariff = Tariff::findOrFail($id);
        $tariff->delete();
        return redirect()->route('tariffs.index')->with('success', 'Tarif berhasil dihapus.');
    }

    public function calculate(Request $request)
    {
        $origin = $request->input('origin_city');
        $destination = $request->input('destination_city');
        $originDistrict = $request->input('origin_district');
        $originSubdistrict = $request->input('origin_subdistrict');
        $destDistrict = $request->input('destination_district');
        $destSubdistrict = $request->input('destination_subdistrict');

        $rawWeight = (float) $request->input('weight_kg', 1);
        $chargedWeight = max(1, (int) ceil($rawWeight));

        $rawService = $request->input('service_type', 'Regular');
        $service = match (strtoupper($rawService)) {
            'EXP', 'EXPRESS' => 'Express',
            'SAMEDAY', 'SAME DAY' => 'SameDay',
            'CARGO', 'KARGO' => 'Cargo',
            default => 'Regular',
        };

        $tariff = Tariff::findTariff($origin, $destination, $service, $originDistrict, $originSubdistrict, $destDistrict, $destSubdistrict);

        if ($tariff) {
            $pricePerKg = (float) $tariff->price_per_kg;
            $estimatedDays = $tariff->estimated_days;
        } else {
            $pricePerKg = match ($service) {
                'Express' => 20000,
                'SameDay' => 35000,
                'Cargo' => 8000,
                default => 12000,
            };
            $estimatedDays = match ($service) {
                'Express' => '1 Hari',
                'SameDay' => 'Hari Ini Tiba',
                'Cargo' => '3-5 Hari',
                default => '2-3 Hari',
            };
        }

        $shippingFee = $pricePerKg * $chargedWeight;
        $declaredValue = max(0, (float) $request->input('declared_value', 0));
        $insuranceFee = $declaredValue > 0 ? max(5000, $declaredValue * 0.002) : 0;
        $totalFee = $shippingFee + $insuranceFee;

        return response()->json([
            'status' => 'success',
            'found' => (bool) $tariff,
            'origin_city' => $origin,
            'destination_city' => $destination,
            'service_type' => $service,
            'weight_kg' => $rawWeight,
            'charged_weight_kg' => $chargedWeight,
            'price_per_kg' => $pricePerKg,
            'rate_per_kg' => $pricePerKg,
            'shipping_fee' => $shippingFee,
            'insurance_fee' => $insuranceFee,
            'total_fee' => $totalFee,
            'total_amount' => $totalFee,
            'estimated_days' => $estimatedDays,
        ]);
    }
}
