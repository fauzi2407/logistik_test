<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $customers = Customer::withCount(['deliveryOrders', 'shipments'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('customer_code', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('customers.index', compact('customers', 'search'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'province' => 'nullable|string|max:100',
            'city' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'subdistrict' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'customer_type' => 'required|in:individual,corporate',
            'create_account' => 'nullable|boolean',
        ]);

        $customerCode = 'CUST-' . date('Y') . '-' . str_pad(Customer::count() + 1, 3, '0', STR_PAD_LEFT);

        $userId = null;
        if ($request->boolean('create_account') && $request->filled('email')) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'role' => 'customer',
                'password' => Hash::make('password123'),
            ]);
            $userId = $user->id;
        }

        Customer::create([
            'user_id' => $userId,
            'customer_code' => $customerCode,
            'name' => $validated['name'],
            'company_name' => $validated['company_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'province' => $validated['province'] ?? null,
            'city' => $validated['city'],
            'district' => $validated['district'] ?? null,
            'subdistrict' => $validated['subdistrict'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'customer_type' => $validated['customer_type'],
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer berhasil ditambahkan dengan Kode: ' . $customerCode);
    }

    public function show($id)
    {
        $customer = Customer::with(['deliveryOrders', 'shipments.latestLog'])->findOrFail($id);
        return view('customers.show', compact('customer'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'province' => 'nullable|string|max:100',
            'city' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'subdistrict' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'customer_type' => 'required|in:individual,corporate',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Data Customer berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer berhasil dihapus.');
    }
}
