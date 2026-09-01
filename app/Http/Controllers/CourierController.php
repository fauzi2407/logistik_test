<?php

namespace App\Http\Controllers;

use App\Models\BranchHub;
use App\Models\Courier;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $couriers = Courier::with(['branchHub', 'vehicle'])
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('courier_code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $hubs = BranchHub::orderBy('name')->get();
        $vehicles = Vehicle::where('status', 'active')->orderBy('plate_number')->get();

        return view('couriers.index', compact('couriers', 'hubs', 'vehicles', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'subdistrict' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'emergency_phone' => 'nullable|string|max:30',
            'age' => 'nullable|integer|min:17|max:70',
            'basic_salary' => 'nullable|numeric|min:0',
            'commission_per_delivery' => 'required|numeric|min:0',
            'license_number' => 'nullable|string|max:50',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'branch_hub_id' => 'nullable|exists:branch_hubs,id',
            'status' => 'required|in:available,on_duty,off',
        ]);

        $courierCode = 'KUR-' . str_pad(Courier::count() + 1, 3, '0', STR_PAD_LEFT);

        Courier::create([
            'courier_code' => $courierCode,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'province' => $validated['province'] ?? null,
            'city' => $validated['city'] ?? null,
            'district' => $validated['district'] ?? null,
            'subdistrict' => $validated['subdistrict'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'address' => $validated['address'] ?? null,
            'emergency_phone' => $validated['emergency_phone'] ?? null,
            'age' => $validated['age'] ?? null,
            'basic_salary' => $validated['basic_salary'] ?? 0,
            'commission_per_delivery' => $validated['commission_per_delivery'],
            'license_number' => $validated['license_number'] ?? null,
            'vehicle_id' => $validated['vehicle_id'] ?? null,
            'branch_hub_id' => $validated['branch_hub_id'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('couriers.index')->with('success', 'Data Kurir baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $courier = Courier::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'subdistrict' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'emergency_phone' => 'nullable|string|max:30',
            'age' => 'nullable|integer|min:17|max:70',
            'basic_salary' => 'nullable|numeric|min:0',
            'commission_per_delivery' => 'required|numeric|min:0',
            'license_number' => 'nullable|string|max:50',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'branch_hub_id' => 'nullable|exists:branch_hubs,id',
            'status' => 'required|in:available,on_duty,off',
        ]);

        $courier->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'province' => $validated['province'] ?? null,
            'city' => $validated['city'] ?? null,
            'district' => $validated['district'] ?? null,
            'subdistrict' => $validated['subdistrict'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'address' => $validated['address'] ?? null,
            'emergency_phone' => $validated['emergency_phone'] ?? null,
            'age' => $validated['age'] ?? null,
            'basic_salary' => $validated['basic_salary'] ?? 0,
            'commission_per_delivery' => $validated['commission_per_delivery'],
            'license_number' => $validated['license_number'] ?? null,
            'vehicle_id' => $validated['vehicle_id'] ?? null,
            'branch_hub_id' => $validated['branch_hub_id'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('couriers.index')->with('success', 'Data Kurir berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $courier = Courier::findOrFail($id);
        $courier->delete();

        return redirect()->route('couriers.index')->with('success', 'Data Kurir berhasil dihapus.');
    }
}
