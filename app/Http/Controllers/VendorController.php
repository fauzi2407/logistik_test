<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('vendor_code', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $vendors = $query->orderBy('name')->paginate(15);

        return view('vendors.index', compact('vendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $vendorCode = 'VND-' . str_pad(Vendor::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['vendor_code'] = $vendorCode;

        Vendor::create($validated);

        return redirect()->route('vendors.index')->with('success', "Vendor/Supplier {$validated['name']} ({$vendorCode}) berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $vendor->update($validated);

        return redirect()->route('vendors.index')->with('success', "Data Vendor {$vendor->name} berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);
        
        if ($vendor->purchases()->exists()) {
            return redirect()->back()->with('error', "Vendor {$vendor->name} tidak dapat dihapus karena memiliki riwayat transaksi Purchase Order.");
        }

        $vendor->delete();
        return redirect()->route('vendors.index')->with('success', "Vendor {$vendor->name} berhasil dihapus.");
    }
}
