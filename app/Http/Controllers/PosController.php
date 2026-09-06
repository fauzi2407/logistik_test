<?php

namespace App\Http\Controllers;

use App\Models\BranchHub;
use App\Models\Courier;
use App\Models\Customer;
use App\Models\Shipment;
use App\Models\ShipmentTrackingLog;
use App\Models\Tariff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PosController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('name')->get();
        $hubs = BranchHub::orderBy('name')->get();
        $couriers = Courier::where('status', '!=', 'off')->orderBy('name')->get();

        return view('pos.index', compact('customers', 'hubs', 'couriers'));
    }

    public function storeCustomer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'province' => 'nullable|string|max:100',
            'city' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'subdistrict' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'address' => 'required|string',
        ]);

        $code = 'CUST-' . str_pad(Customer::count() + 1, 4, '0', STR_PAD_LEFT);

        // Auto Create User Account if email provided or generate dummy email
        $email = $validated['email'] ?: 'customer.' . strtolower(str_replace(' ', '', $validated['name'])) . rand(100, 999) . '@logistik.com';
        $user = User::create([
            'name' => $validated['name'],
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $customer = Customer::create([
            'user_id' => $user->id,
            'customer_code' => $code,
            'name' => $validated['name'],
            'company_name' => $validated['company_name'] ?? null,
            'phone' => $validated['phone'],
            'email' => $email,
            'province' => $validated['province'] ?? null,
            'city' => $validated['city'],
            'district' => $validated['district'] ?? null,
            'subdistrict' => $validated['subdistrict'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'address' => $validated['address'],
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'customer' => $customer,
                'message' => "Customer baru '{$customer->name}' berhasil didaftarkan!",
            ]);
        }

        return redirect()->route('pos.index')->with('success', "Customer baru '{$customer->name}' berhasil didaftarkan!");
    }

    public function storeShipment(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'origin_hub_id' => 'required|exists:branch_hubs,id',
            'destination_hub_id' => 'required|exists:branch_hubs,id',
            'courier_id' => 'nullable|exists:couriers,id',
            'sender_name' => 'required|string|max:255',
            'sender_phone' => 'required|string|max:30',
            'sender_province' => 'nullable|string|max:100',
            'sender_city' => 'required|string|max:100',
            'sender_district' => 'nullable|string|max:100',
            'sender_subdistrict' => 'nullable|string|max:100',
            'sender_postal_code' => 'nullable|string|max:20',
            'sender_address' => 'required|string',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:30',
            'recipient_province' => 'nullable|string|max:100',
            'recipient_city' => 'required|string|max:100',
            'recipient_district' => 'nullable|string|max:100',
            'recipient_subdistrict' => 'nullable|string|max:100',
            'recipient_postal_code' => 'nullable|string|max:20',
            'recipient_address' => 'required|string',
            'service_type' => 'required|string|in:Regular,Express,SameDay',
            'weight_kg' => 'required|numeric|min:0.1',
            'declared_value' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'cash_tendered' => 'nullable|numeric|min:0',
            'cash_change' => 'nullable|numeric|min:0',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);

        $tariff = Tariff::findTariff(
            $validated['sender_city'],
            $validated['recipient_city'],
            $validated['service_type'],
            $validated['sender_district'] ?? null,
            $validated['sender_subdistrict'] ?? null,
            $validated['recipient_district'] ?? null,
            $validated['recipient_subdistrict'] ?? null
        );

        $pricePerKg = $tariff ? $tariff->price_per_kg : ($validated['service_type'] == 'Express' ? 20000 : 12000);
        $chargedWeight = max(1, (int) ceil((float) $validated['weight_kg']));
        $shippingFee = $pricePerKg * $chargedWeight;
        $insuranceFee = ($validated['declared_value'] ?? 0) > 0 ? ($validated['declared_value'] * 0.002) : 0;
        $totalAmount = $shippingFee + $insuranceFee;

        $trackingNumber = 'TRK-' . date('Ymd') . '-' . str_pad(Shipment::count() + 1, 4, '0', STR_PAD_LEFT);
        $paymentStatus = in_array($validated['payment_method'], ['Cash', 'Transfer', 'QRIS']) ? 'Paid' : 'Pending';

        $shipment = Shipment::create([
            'tracking_number' => $trackingNumber,
            'customer_id' => $customer->id,
            'origin_hub_id' => $validated['origin_hub_id'],
            'destination_hub_id' => $validated['destination_hub_id'],
            'courier_id' => $validated['courier_id'] ?? null,
            'sender_name' => $validated['sender_name'],
            'sender_phone' => $validated['sender_phone'],
            'sender_province' => $validated['sender_province'] ?? null,
            'sender_city' => $validated['sender_city'],
            'sender_district' => $validated['sender_district'] ?? null,
            'sender_subdistrict' => $validated['sender_subdistrict'] ?? null,
            'sender_postal_code' => $validated['sender_postal_code'] ?? null,
            'sender_address' => $validated['sender_address'],
            'recipient_name' => $validated['recipient_name'],
            'recipient_phone' => $validated['recipient_phone'],
            'recipient_province' => $validated['recipient_province'] ?? null,
            'recipient_city' => $validated['recipient_city'],
            'recipient_district' => $validated['recipient_district'] ?? null,
            'recipient_subdistrict' => $validated['recipient_subdistrict'] ?? null,
            'recipient_postal_code' => $validated['recipient_postal_code'] ?? null,
            'recipient_address' => $validated['recipient_address'],
            'service_type' => $validated['service_type'],
            'weight_kg' => $validated['weight_kg'],
            'declared_value' => $validated['declared_value'] ?? 0,
            'shipping_fee' => $shippingFee,
            'insurance_fee' => $insuranceFee,
            'total_amount' => $totalAmount,
            'payment_method' => $validated['payment_method'],
            'payment_status' => $paymentStatus,
            'status' => 'picked_up',
        ]);

        ShipmentTrackingLog::create([
            'shipment_id' => $shipment->id,
            'status' => 'picked_up',
            'location' => $validated['sender_city'],
            'description' => "Resi diterbitkan via Kasir POS Kasir. Pembayaran {$paymentStatus} ({$validated['payment_method']}).",
            'updated_by_user_id' => Auth::id(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'shipment_id' => $shipment->id,
                'tracking_number' => $trackingNumber,
                'receipt_url' => route('pos.print-receipt', $shipment->id),
                'label_url' => route('shipments.print-label', $shipment->id),
                'message' => "Resi Kasir POS {$trackingNumber} berhasil diterbitkan!",
            ]);
        }

        return redirect()->route('pos.print-receipt', $shipment->id);
    }

    public function printReceipt($id)
    {
        $shipment = Shipment::with(['customer', 'originHub', 'destinationHub', 'courier'])->findOrFail($id);
        return view('pos.print_receipt', compact('shipment'));
    }
}
