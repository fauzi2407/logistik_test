<?php

namespace App\Http\Controllers;

use App\Models\BranchHub;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\Shipment;
use App\Models\ShipmentTrackingLog;
use App\Models\Tariff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryOrderController extends Controller
{
    private function getLoggedInCustomer()
    {
        $user = Auth::user();
        if ($user && $user->role === 'customer') {
            return Customer::where('email', $user->email)->orWhere('user_id', $user->id)->first();
        }
        return null;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $loggedInCustomer = $this->getLoggedInCustomer();

        $deliveryOrders = DeliveryOrder::with(['customer', 'items', 'shipment.originHub', 'shipments.originHub'])
            ->when($loggedInCustomer, function ($q) use ($loggedInCustomer) {
                $q->where('customer_id', $loggedInCustomer->id);
            })
            ->when($search, function ($q, $search) {
                $q->where('do_number', 'like', "%{$search}%")
                    ->orWhereHas('items', function ($iq) use ($search) {
                        $iq->where('recipient_name', 'like', "%{$search}%")
                            ->orWhere('recipient_city', 'like', "%{$search}%")
                            ->orWhere('account_ref', 'like', "%{$search}%");
                    });
            })
            ->when($status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->latest()
            ->paginate(10);

        return view('delivery_orders.index', compact('deliveryOrders', 'search', 'status'));
    }

    public function create()
    {
        $loggedInCustomer = $this->getLoggedInCustomer();
        if ($loggedInCustomer) {
            $customers = Customer::where('id', $loggedInCustomer->id)->get();
        } else {
            $customers = Customer::orderBy('name')->get();
        }

        $hubs = BranchHub::orderBy('name')->get();

        return view('delivery_orders.create', compact('customers', 'hubs'));
    }

    public function store(Request $request)
    {
        $loggedInCustomer = $this->getLoggedInCustomer();

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'origin_hub_id' => 'nullable|exists:branch_hubs,id',
            'order_date' => 'required|date',
            'pickup_province' => 'nullable|string|max:100',
            'pickup_city' => 'nullable|string|max:100',
            'pickup_district' => 'nullable|string|max:100',
            'pickup_subdistrict' => 'nullable|string|max:100',
            'pickup_postal_code' => 'nullable|string|max:20',
            'pickup_address' => 'nullable|string',
            'service_type' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.recipient_name' => 'required|string|max:255',
            'items.*.recipient_phone' => 'required|string|max:30',
            'items.*.recipient_province' => 'nullable|string|max:100',
            'items.*.recipient_city' => 'required|string|max:100',
            'items.*.recipient_district' => 'nullable|string|max:100',
            'items.*.recipient_subdistrict' => 'nullable|string|max:100',
            'items.*.recipient_postal_code' => 'nullable|string|max:20',
            'items.*.recipient_address' => 'required|string',
            'items.*.account_ref' => 'nullable|string|max:100',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit' => 'required|string|max:50',
            'items.*.weight_kg' => 'required|numeric|min:0.1',
        ]);

        if ($loggedInCustomer && $validated['customer_id'] != $loggedInCustomer->id) {
            abort(403, 'Akses ditolak: Anda hanya dapat membuat DO untuk customer Anda sendiri.');
        }

        $customer = Customer::findOrFail($validated['customer_id']);
        $doNumber = 'DO-' . date('Ymd') . '-' . str_pad(DeliveryOrder::count() + 1, 4, '0', STR_PAD_LEFT);

        $isCustomer = Auth::user() && Auth::user()->role === 'customer';
        $doStatus = $isCustomer ? 'pending' : 'approved';

        $senderCity = !empty($validated['pickup_city']) ? $validated['pickup_city'] : $customer->city;
        $senderDistrict = !empty($validated['pickup_district']) ? $validated['pickup_district'] : $customer->district;
        $senderSubdistrict = !empty($validated['pickup_subdistrict']) ? $validated['pickup_subdistrict'] : $customer->subdistrict;

        $senderAddress = !empty($validated['pickup_address']) ? $validated['pickup_address'] : $customer->address;

        $serviceType = !empty($validated['service_type']) ? $validated['service_type'] : 'Express';

        $originHubId = $validated['origin_hub_id'] ?? null;
        if (!$originHubId) {
            $matchedHub = BranchHub::where('city', 'like', "%{$senderCity}%")->first();
            if (!$matchedHub) {
                $matchedHub = BranchHub::first();
            }
            $originHubId = $matchedHub ? $matchedHub->id : 1;
        }

        $do = DeliveryOrder::create([
            'do_number' => $doNumber,
            'customer_id' => $customer->id,
            'origin_hub_id' => $originHubId,
            'order_date' => $validated['order_date'],
            'delivery_date' => $validated['delivery_date'] ?? $validated['order_date'],
            'sender_name' => $customer->company_name ?: $customer->name,
            'sender_phone' => $customer->phone,
            'sender_address' => $senderAddress,
            'sender_city' => $senderCity,
            'recipient_name' => 'Multi Tujuan (' . count($validated['items']) . ' Penerima)',
            'recipient_phone' => $customer->phone,
            'recipient_address' => 'Berbagai Alamat Tujuan',
            'recipient_city' => 'Multi Kota',
            'status' => $doStatus,
            'notes' => $validated['notes'],
        ]);

        foreach ($validated['items'] as $itemData) {
            $weight = (float) $itemData['weight_kg'];
            $trackingNumber = null;

            if (!$isCustomer) {
                $trackingNumber = 'TRK-' . date('Ymd') . '-' . str_pad(Shipment::count() + 1, 4, '0', STR_PAD_LEFT);

                $tariff = Tariff::findTariff(
                    $senderCity,
                    $itemData['recipient_city'],
                    $serviceType,
                    $senderDistrict,
                    $senderSubdistrict,
                    $itemData['recipient_district'] ?? null,
                    $itemData['recipient_subdistrict'] ?? null
                );

                $pricePerKg = $tariff ? $tariff->price_per_kg : ($serviceType == 'Express' ? 20000 : 12000);
                $chargedWeight = max(1, (int) ceil($weight));
                $totalAmount = $pricePerKg * $chargedWeight;

                $shipment = Shipment::create([
                    'tracking_number' => $trackingNumber,
                    'delivery_order_id' => $do->id,
                    'customer_id' => $customer->id,
                    'origin_hub_id' => $originHubId,
                    'sender_name' => $customer->company_name ?: $customer->name,
                    'sender_phone' => $customer->phone,
                    'sender_address' => $senderAddress,
                    'sender_city' => $senderCity,
                    'recipient_name' => $itemData['recipient_name'],
                    'recipient_phone' => $itemData['recipient_phone'],
                    'recipient_province' => $itemData['recipient_province'] ?? null,
                    'recipient_city' => $itemData['recipient_city'],
                    'recipient_district' => $itemData['recipient_district'] ?? null,
                    'recipient_subdistrict' => $itemData['recipient_subdistrict'] ?? null,
                    'recipient_postal_code' => $itemData['recipient_postal_code'] ?? null,
                    'recipient_address' => $itemData['recipient_address'],
                    'service_type' => $serviceType,
                    'weight_kg' => $weight,
                    'shipping_fee' => $totalAmount,
                    'total_amount' => $totalAmount,
                    'payment_method' => 'Transfer',
                    'payment_status' => 'Pending',
                    'status' => 'pending',
                ]);

                ShipmentTrackingLog::create([
                    'shipment_id' => $shipment->id,
                    'status' => 'pending',
                    'location' => $senderCity,
                    'description' => "Resi AWB {$trackingNumber} diterbitkan untuk item {$itemData['item_name']} via Delivery Order ({$doNumber}).",
                    'updated_by_user_id' => Auth::id(),
                ]);
            }

            DeliveryOrderItem::create([
                'delivery_order_id' => $do->id,
                'recipient_name' => $itemData['recipient_name'],
                'recipient_phone' => $itemData['recipient_phone'],
                'recipient_province' => $itemData['recipient_province'] ?? null,
                'recipient_city' => $itemData['recipient_city'],
                'recipient_district' => $itemData['recipient_district'] ?? null,
                'recipient_subdistrict' => $itemData['recipient_subdistrict'] ?? null,
                'recipient_postal_code' => $itemData['recipient_postal_code'] ?? null,
                'recipient_address' => $itemData['recipient_address'],
                'account_ref' => $itemData['account_ref'] ?? null,
                'tracking_number' => $trackingNumber,
                'item_name' => $itemData['item_name'],
                'qty' => $itemData['qty'],
                'unit' => $itemData['unit'],
                'weight_kg' => $weight,
            ]);
        }

        $message = $isCustomer
            ? 'Delivery Order (DO) berhasil dibuat dan menunggu verifikasi admin untuk penerbitan resi AWB.'
            : 'DO Multi-Tujuan & Resi AWB berhasil dibuat.';

        return redirect()->route('delivery-orders.show', $do->id)->with('success', $message);
    }

    public function show($id)
    {
        $deliveryOrder = DeliveryOrder::with(['customer', 'items.shipment', 'invoice'])->findOrFail($id);
        $loggedInCustomer = $this->getLoggedInCustomer();

        if ($loggedInCustomer && $deliveryOrder->customer_id != $loggedInCustomer->id) {
            abort(403, 'Akses ditolak: Anda tidak dapat melihat DO milik customer lain.');
        }

        return view('delivery_orders.show', compact('deliveryOrder'));
    }

    public function edit($id)
    {
        $deliveryOrder = DeliveryOrder::with(['customer', 'items.shipment'])->findOrFail($id);
        $loggedInCustomer = $this->getLoggedInCustomer();

        if ($loggedInCustomer || (Auth::user() && Auth::user()->role === 'customer')) {
            abort(403, 'Akses ditolak: Customer hanya memiliki akses membuat dan melihat DO serta tagihan. Edit DO hanya dapat dilakukan oleh Admin Operasional.');
        }

        if ($deliveryOrder->isPickedUp()) {
            return redirect()->route('delivery-orders.show', $deliveryOrder->id)
                ->with('error', 'Gagal: Delivery Order ini tidak dapat diubah karena paket sudah diambil oleh kurir atau sedang dalam proses pengiriman.');
        }

        $customers = Customer::orderBy('name')->get();
        $hubs = BranchHub::orderBy('name')->get();

        return view('delivery_orders.edit', compact('deliveryOrder', 'customers', 'hubs'));
    }

    public function update(Request $request, $id)
    {
        $do = DeliveryOrder::with('items.shipment')->findOrFail($id);
        $loggedInCustomer = $this->getLoggedInCustomer();

        if ($loggedInCustomer || (Auth::user() && Auth::user()->role === 'customer')) {
            abort(403, 'Akses ditolak: Customer hanya memiliki akses membuat dan melihat DO serta tagihan. Edit DO hanya dapat dilakukan oleh Admin Operasional.');
        }

        if ($do->isPickedUp()) {
            return redirect()->route('delivery-orders.show', $do->id)
                ->with('error', 'Gagal: Delivery Order ini tidak dapat diubah karena paket sudah diambil oleh kurir atau sedang dalam proses pengiriman.');
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'origin_hub_id' => 'nullable|exists:branch_hubs,id',
            'order_date' => 'required|date',
            'delivery_date' => 'nullable|date',
            'pickup_province' => 'nullable|string|max:100',
            'pickup_city' => 'nullable|string|max:100',
            'pickup_district' => 'nullable|string|max:100',
            'pickup_subdistrict' => 'nullable|string|max:100',
            'pickup_postal_code' => 'nullable|string|max:20',
            'pickup_address' => 'nullable|string',
            'service_type' => 'nullable|string|max:50',
            'status' => 'required|in:draft,pending,approved,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.recipient_name' => 'required|string|max:255',
            'items.*.recipient_phone' => 'required|string|max:30',
            'items.*.recipient_province' => 'nullable|string|max:100',
            'items.*.recipient_city' => 'required|string|max:100',
            'items.*.recipient_district' => 'nullable|string|max:100',
            'items.*.recipient_subdistrict' => 'nullable|string|max:100',
            'items.*.recipient_postal_code' => 'nullable|string|max:20',
            'items.*.recipient_address' => 'required|string',
            'items.*.account_ref' => 'nullable|string|max:100',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit' => 'required|string|max:50',
            'items.*.weight_kg' => 'required|numeric|min:0.1',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);

        $senderCity = !empty($validated['pickup_city']) ? $validated['pickup_city'] : $customer->city;
        $senderDistrict = !empty($validated['pickup_district']) ? $validated['pickup_district'] : $customer->district;
        $senderSubdistrict = !empty($validated['pickup_subdistrict']) ? $validated['pickup_subdistrict'] : $customer->subdistrict;
        $senderAddress = !empty($validated['pickup_address']) ? $validated['pickup_address'] : $customer->address;

        $originHubId = $validated['origin_hub_id'] ?? null;
        if (!$originHubId) {
            $matchedHub = BranchHub::where('city', 'like', "%{$senderCity}%")->first();
            if (!$matchedHub) {
                $matchedHub = BranchHub::first();
            }
            $originHubId = $matchedHub ? $matchedHub->id : 1;
        }

        $do->update([
            'customer_id' => $customer->id,
            'origin_hub_id' => $originHubId,
            'order_date' => $validated['order_date'],
            'delivery_date' => $validated['delivery_date'] ?? $validated['order_date'],
            'sender_name' => $customer->company_name ?: $customer->name,
            'sender_phone' => $customer->phone,
            'sender_address' => $senderAddress,
            'sender_city' => $senderCity,
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        // Bulk update any existing shipments tied to this DO
        Shipment::where('delivery_order_id', $do->id)->update(['origin_hub_id' => $originHubId]);

        $serviceType = !empty($validated['service_type']) ? $validated['service_type'] : 'Express';

        $do->items()->delete();

        foreach ($validated['items'] as $itemData) {
            $weight = (float) $itemData['weight_kg'];
            $trackingNumber = $itemData['tracking_number'] ?? null;

            if (!$trackingNumber) {
                $trackingNumber = 'TRK-' . date('Ymd') . '-' . str_pad(Shipment::count() + 1, 4, '0', STR_PAD_LEFT);
            }

            DeliveryOrderItem::create([
                'delivery_order_id' => $do->id,
                'recipient_name' => $itemData['recipient_name'],
                'recipient_phone' => $itemData['recipient_phone'],
                'recipient_province' => $itemData['recipient_province'] ?? null,
                'recipient_city' => $itemData['recipient_city'],
                'recipient_district' => $itemData['recipient_district'] ?? null,
                'recipient_subdistrict' => $itemData['recipient_subdistrict'] ?? null,
                'recipient_postal_code' => $itemData['recipient_postal_code'] ?? null,
                'recipient_address' => $itemData['recipient_address'],
                'account_ref' => $itemData['account_ref'] ?? null,
                'tracking_number' => $trackingNumber,
                'item_name' => $itemData['item_name'],
                'qty' => $itemData['qty'],
                'unit' => $itemData['unit'],
                'weight_kg' => $weight,
            ]);

            // Sync or update associated Shipment if present
            $tariff = Tariff::findTariff(
                $senderCity,
                $itemData['recipient_city'],
                $serviceType,
                $senderDistrict,
                $senderSubdistrict,
                $itemData['recipient_district'] ?? null,
                $itemData['recipient_subdistrict'] ?? null
            );

            $pricePerKg = $tariff ? $tariff->price_per_kg : ($serviceType == 'Express' ? 20000 : 12000);
            $chargedWeight = max(1, (int) ceil($weight));
            $totalAmount = $pricePerKg * $chargedWeight;

            $shipment = Shipment::where('tracking_number', $trackingNumber)->first();
            if ($shipment) {
                $shipment->update([
                    'customer_id' => $customer->id,
                    'origin_hub_id' => $originHubId,
                    'sender_name' => $customer->company_name ?: $customer->name,
                    'sender_phone' => $customer->phone,
                    'sender_address' => $senderAddress,
                    'sender_city' => $senderCity,
                    'recipient_name' => $itemData['recipient_name'],
                    'recipient_phone' => $itemData['recipient_phone'],
                    'recipient_province' => $itemData['recipient_province'] ?? null,
                    'recipient_city' => $itemData['recipient_city'],
                    'recipient_district' => $itemData['recipient_district'] ?? null,
                    'recipient_subdistrict' => $itemData['recipient_subdistrict'] ?? null,
                    'recipient_postal_code' => $itemData['recipient_postal_code'] ?? null,
                    'recipient_address' => $itemData['recipient_address'],
                    'service_type' => $serviceType,
                    'weight_kg' => $weight,
                    'shipping_fee' => $totalAmount,
                    'total_amount' => $totalAmount,
                ]);
            }
        }

        return redirect()->route('delivery-orders.show', $do->id)->with('success', 'DO Multi-Tujuan berhasil diperbarui.');
    }

    public function print($id)
    {
        $deliveryOrder = DeliveryOrder::with(['customer', 'items'])->findOrFail($id);
        $loggedInCustomer = $this->getLoggedInCustomer();

        if ($loggedInCustomer && $deliveryOrder->customer_id != $loggedInCustomer->id) {
            abort(403, 'Akses ditolak: Anda tidak dapat mencetak DO milik customer lain.');
        }

        return view('delivery_orders.print', compact('deliveryOrder'));
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_penerima_bank.csv"',
        ];

        $columns = ['Nama Penerima', 'No HP', 'Provinsi', 'Kota/Kab', 'Kecamatan', 'Kelurahan', 'Kode Pos', 'Alamat Lengkap', 'No Ref Kartu / Tagihan', 'Jenis Barang', 'Berat KG'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $sampleData = [
                ['Bpk. Anton Wijaya', '081299887766', 'Jawa Barat', 'Kota Bandung', 'Coblong', 'Dago', '40135', 'Jl. Dago No. 120', 'CC-4512-8899-01', 'Kartu Kredit BCA Visa Platinum', '0.2'],
                ['Ibu Dewi Lestari', '081377665544', 'Jawa Timur', 'Kota Surabaya', 'Genteng', 'Embong Kaliasin', '60271', 'Jl. Pemuda No. 45', 'CC-4512-8899-02', 'Kartu Kredit BCA Everyday Card', '0.2'],
                ['Bpk. Rahmat Subagyo', '081744556677', 'Jawa Barat', 'Kota Bandung', 'Cibeunying Kaler', 'Cihaurgeulis', '40122', 'Jl. Riau No. 88', 'CC-4512-8899-03', 'Tagihan Kartu Kredit BCA Solitaire', '0.1'],
                ['Bpk. Susilo Bambang', '085211223344', 'DKI Jakarta', 'Jakarta Selatan', 'Cilandak', 'Cilandak Barat', '12430', 'Jl. Fatmawati No. 10', 'CC-4512-8899-04', 'Kartu Kredit BCA Signature', '0.2'],
            ];

            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importRecipientsCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,xls,xlsx|max:5120',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        $recipients = [];
        $header = fgetcsv($handle, 1000, ',');

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            if (count($data) >= 4 && !empty(trim($data[0]))) {
                $recipients[] = [
                    'recipient_name' => trim($data[0] ?? ''),
                    'recipient_phone' => trim($data[1] ?? ''),
                    'recipient_province' => trim($data[2] ?? 'Jawa Barat'),
                    'recipient_city' => trim($data[3] ?? 'Kota Bandung'),
                    'recipient_district' => trim($data[4] ?? 'Coblong'),
                    'recipient_subdistrict' => trim($data[5] ?? 'Dago'),
                    'recipient_postal_code' => trim($data[6] ?? '40135'),
                    'recipient_address' => trim($data[7] ?? ''),
                    'account_ref' => trim($data[8] ?? ''),
                    'item_name' => trim($data[9] ?? 'Kartu Kredit Bank'),
                    'weight_kg' => (float) (trim($data[10] ?? '0.2') ?: 0.2),
                    'qty' => 1,
                    'unit' => 'Amplop',
                ];
            }
        }
        fclose($handle);

        return response()->json([
            'success' => true,
            'message' => count($recipients) . ' data penerima berhasil di-import dari berkas!',
            'data' => $recipients,
        ]);
    }

    public function destroy($id)
    {
        $do = DeliveryOrder::with(['items.shipment'])->findOrFail($id);
        $loggedInCustomer = $this->getLoggedInCustomer();

        if ($loggedInCustomer && $do->customer_id != $loggedInCustomer->id) {
            abort(403, 'Akses ditolak: Anda tidak dapat menghapus DO milik customer lain.');
        }

        $hasResi = $do->items->contains(fn($item) => !empty($item->tracking_number) || $item->shipment != null);
        if ($hasResi) {
            return redirect()->back()->with('error', 'Gagal: Delivery Order yang sudah memiliki resi AWB tidak dapat dihapus.');
        }

        $do->items()->delete();
        $do->delete();

        return redirect()->route('delivery-orders.index')->with('success', 'Delivery Order tanpa resi berhasil dihapus.');
    }

    public function generateShipments($id)
    {
        $do = DeliveryOrder::with(['customer', 'items.shipment'])->findOrFail($id);

        $loggedInCustomer = $this->getLoggedInCustomer();
        if ($loggedInCustomer && $do->customer_id != $loggedInCustomer->id) {
            abort(403, 'Akses ditolak: Anda tidak dapat memproses DO milik customer lain.');
        }

        $customer = $do->customer ?: Customer::find($do->customer_id);
        $originHub = BranchHub::first();
        $originHubId = $originHub ? $originHub->id : 1;

        $createdCount = 0;

        foreach ($do->items as $item) {
            if (!$item->shipment) {
                $weight = (float) $item->weight_kg;
                $trackingNumber = 'TRK-' . date('Ymd') . '-' . str_pad(Shipment::count() + 1, 4, '0', STR_PAD_LEFT);

                $serviceType = $do->service_type ?? 'Express';

                $tariff = Tariff::findTariff(
                    $do->sender_city,
                    $item->recipient_city,
                    $serviceType,
                    $do->pickup_district ?? null,
                    $do->pickup_subdistrict ?? null,
                    $item->recipient_district ?? null,
                    $item->recipient_subdistrict ?? null
                );

                $pricePerKg = $tariff ? $tariff->price_per_kg : ($serviceType == 'Express' ? 20000 : 12000);
                $chargedWeight = max(1, (int) ceil($weight));
                $totalAmount = $pricePerKg * $chargedWeight;

                $shipment = Shipment::create([
                    'tracking_number' => $trackingNumber,
                    'delivery_order_id' => $do->id,
                    'customer_id' => $do->customer_id,
                    'origin_hub_id' => $originHubId,
                    'sender_name' => $do->sender_name,
                    'sender_phone' => $do->sender_phone,
                    'sender_address' => $do->sender_address,
                    'sender_city' => $do->sender_city,
                    'recipient_name' => $item->recipient_name,
                    'recipient_phone' => $item->recipient_phone,
                    'recipient_province' => $item->recipient_province ?? null,
                    'recipient_city' => $item->recipient_city,
                    'recipient_district' => $item->recipient_district ?? null,
                    'recipient_subdistrict' => $item->recipient_subdistrict ?? null,
                    'recipient_postal_code' => $item->recipient_postal_code ?? null,
                    'recipient_address' => $item->recipient_address,
                    'service_type' => $serviceType,
                    'weight_kg' => $weight,
                    'shipping_fee' => $totalAmount,
                    'total_amount' => $totalAmount,
                    'payment_method' => 'Transfer',
                    'payment_status' => 'Pending',
                    'status' => 'picked_up',
                ]);

                ShipmentTrackingLog::create([
                    'shipment_id' => $shipment->id,
                    'status' => 'picked_up',
                    'location' => $do->sender_city,
                    'description' => "Resi AWB {$trackingNumber} diterbitkan untuk item {$item->item_name} via Delivery Order ({$do->do_number}).",
                    'updated_by_user_id' => Auth::id(),
                ]);

                $item->update(['tracking_number' => $trackingNumber]);
                $createdCount++;
            }
        }

        $do->update(['status' => 'approved']);

        $msg = $createdCount > 0 
            ? "Berhasil menerbitkan {$createdCount} Resi AWB otomatis untuk DO {$do->do_number}!" 
            : "Semua item dalam DO {$do->do_number} sudah memiliki Resi AWB.";

        return redirect()->back()->with('success', $msg);
    }
}
