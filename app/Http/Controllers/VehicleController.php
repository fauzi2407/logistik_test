<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $vehicles = Vehicle::withCount('couriers')
            ->when($search, function ($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                  ->orWhere('vehicle_type', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('vehicles.index', compact('vehicles', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number',
            'vehicle_type' => 'required|string|max:100',
            'capacity_kg' => 'required|numeric|min:1',
            'status' => 'required|in:active,maintenance,in_delivery',
        ]);

        Vehicle::create($validated);
        return redirect()->route('vehicles.index')->with('success', 'Armada berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $validated = $request->validate([
            'vehicle_type' => 'required|string|max:100',
            'capacity_kg' => 'required|numeric|min:1',
            'status' => 'required|in:active,maintenance,in_delivery',
        ]);

        $vehicle->update($validated);
        return redirect()->route('vehicles.index')->with('success', 'Data armada berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();
        return redirect()->route('vehicles.index')->with('success', 'Armada berhasil dihapus.');
    }
}
