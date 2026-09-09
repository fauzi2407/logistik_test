<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\BranchHub;
use App\Models\Courier;
use App\Models\CourierAssignment;
use App\Models\CourierAssignmentItem;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\Shipment;
use App\Models\ShipmentTrackingLog;
use App\Models\Tariff;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShipmentController extends Controller
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
        $serviceType = $request->input('service_type');
        $courierId = $request->input('courier_id');
        $loggedInCustomer = $this->getLoggedInCustomer();
        $loggedInCourier = Auth::user() && Auth::user()->role === 'courier'
            ? Courier::where('user_id', Auth::id())->orWhere('name', Auth::user()->name)->first()
            : null;

        $query = Shipment::with(['customer', 'courier', 'originHub', 'destinationHub', 'latestLog'])
            ->when($loggedInCustomer, function ($q) use ($loggedInCustomer) {
                $q->where('customer_id', $loggedInCustomer->id);
            })
            ->when($loggedInCourier, function ($q) use ($loggedInCourier) {
                $q->where('courier_id', $loggedInCourier->id);
            })
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('tracking_number', 'like', "%{$search}%")
                        ->orWhere('sender_name', 'like', "%{$search}%")
                        ->orWhere('recipient_name', 'like', "%{$search}%")
                        ->orWhere('recipient_city', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->when($serviceType, function ($q, $serviceType) {
                $q->where('service_type', $serviceType);
            })
            ->when($courierId, function ($q, $courierId) {
                $q->where('courier_id', $courierId);
            });

        // Quick KPI Stats scoped to role
        $baseStatsQuery = Shipment::query()
            ->when($loggedInCustomer, fn($q) => $q->where('customer_id', $loggedInCustomer->id))
            ->when($loggedInCourier, fn($q) => $q->where('courier_id', $loggedInCourier->id));

        $statTotal = (clone $baseStatsQuery)->count();
        $statInTransit = (clone $baseStatsQuery)->whereIn('status', ['picked_up', 'in_sorting_hub', 'in_transit', 'out_for_delivery'])->count();
        $statDelivered = (clone $baseStatsQuery)->where('status', 'delivered')->count();
        $statPending = (clone $baseStatsQuery)->where('status', 'pending')->count();

        $shipments = $query->latest()->paginate(15)->withQueryString();

        $couriers = Courier::orderBy('name')->get();
        $availableServiceTypes = ['Regular', 'Express', 'SameDay'];
        $dbServiceTypes = Shipment::distinct()->whereNotNull('service_type')->pluck('service_type')->toArray();
        $serviceTypes = array_values(array_unique(array_filter(array_merge($availableServiceTypes, $dbServiceTypes))));

        return view('shipments.index', compact(
            'shipments',
            'search',
            'status',
            'serviceType',
            'courierId',
            'couriers',
            'serviceTypes',
            'statTotal',
            'statInTransit',
            'statDelivered',
            'statPending',
            'loggedInCustomer',
            'loggedInCourier'
        ));
    }

    public function create(Request $request)
    {
        $doId = $request->input('do_id');
        $deliveryOrder = $doId ? DeliveryOrder::with(['customer', 'items'])->find($doId) : null;

        $loggedInCustomer = $this->getLoggedInCustomer();
        $customers = $loggedInCustomer ? Customer::where('id', $loggedInCustomer->id)->get() : Customer::orderBy('name')->get();
        $hubs = BranchHub::orderBy('name')->get();
        $couriers = Courier::where('status', '!=', 'off')->orderBy('name')->get();
        $vehicles = Vehicle::where('status', '!=', 'maintenance')->orderBy('plate_number')->get();

        return view('shipments.create', compact('customers', 'hubs', 'couriers', 'vehicles', 'deliveryOrder'));
    }

    public function store(Request $request)
    {
        $loggedInCustomer = $this->getLoggedInCustomer();
        $isCustomer = Auth::user() && Auth::user()->role === 'customer';

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'origin_hub_id' => 'required|exists:branch_hubs,id',
            'destination_hub_id' => 'required|exists:branch_hubs,id',
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
            'dimensions' => 'nullable|string',
            'declared_value' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string|in:Cash,Transfer,COD',
        ]);

        if ($loggedInCustomer) {
            $validated['customer_id'] = $loggedInCustomer->id;
        }

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

        $paymentStatus = $isCustomer ? 'Pending' : ($validated['payment_method'] == 'Cash' ? 'Paid' : 'Pending');

        $shipment = Shipment::create([
            'tracking_number' => $trackingNumber,
            'customer_id' => $customer->id,
            'origin_hub_id' => $validated['origin_hub_id'],
            'destination_hub_id' => $validated['destination_hub_id'],
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
            'dimensions' => $validated['dimensions'] ?? null,
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
            'location' => $customer->city,
            'description' => "Paket telah diterima di Hub Asal ({$customer->city}).",
            'updated_by_user_id' => Auth::id(),
        ]);

        return redirect()->route('shipments.show', $shipment->id)->with('success', "Resi pengiriman {$trackingNumber} berhasil diterbitkan! Total harga yang harus dibayar: Rp " . number_format($totalAmount, 0, ',', '.') . " (Status Pembayaran: {$paymentStatus})");
    }

    public function show($id)
    {
        $shipment = Shipment::with(['customer', 'courier', 'vehicle', 'originHub', 'destinationHub', 'trackingLogs.updatedBy'])->findOrFail($id);
        $loggedInCustomer = $this->getLoggedInCustomer();

        if ($loggedInCustomer && $shipment->customer_id != $loggedInCustomer->id) {
            abort(403, 'Akses ditolak: Anda tidak dapat melihat resi milik customer lain.');
        }

        $hubs = BranchHub::orderBy('name')->get();
        $couriers = Courier::where('status', '!=', 'off')->orderBy('name')->get();

        return view('shipments.show', compact('shipment', 'hubs', 'couriers'));
    }

    public function edit($id)
    {
        $shipment = Shipment::with(['customer', 'courier', 'vehicle', 'originHub', 'destinationHub'])->findOrFail($id);
        $loggedInCustomer = $this->getLoggedInCustomer();

        if ($loggedInCustomer && $shipment->customer_id != $loggedInCustomer->id) {
            abort(403, 'Akses ditolak: Anda tidak dapat mengedit resi milik customer lain.');
        }

        $customers = $loggedInCustomer ? Customer::where('id', $loggedInCustomer->id)->get() : Customer::orderBy('name')->get();
        $hubs = BranchHub::orderBy('name')->get();
        $couriers = Courier::where('status', '!=', 'off')->orderBy('name')->get();
        $vehicles = Vehicle::where('status', '!=', 'maintenance')->orderBy('plate_number')->get();

        return view('shipments.edit', compact('shipment', 'customers', 'hubs', 'couriers', 'vehicles'));
    }

    public function update(Request $request, $id)
    {
        $shipment = Shipment::findOrFail($id);
        $loggedInCustomer = $this->getLoggedInCustomer();
        $isCustomer = Auth::user() && Auth::user()->role === 'customer';

        if ($loggedInCustomer && $shipment->customer_id != $loggedInCustomer->id) {
            abort(403, 'Akses ditolak: Anda tidak dapat mengubah resi milik customer lain.');
        }

        $validated = $request->validate([
            'origin_hub_id' => 'required|exists:branch_hubs,id',
            'destination_hub_id' => 'required|exists:branch_hubs,id',
            'courier_id' => 'nullable|exists:couriers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'sender_name' => 'required|string|max:255',
            'sender_phone' => 'required|string|max:30',
            'sender_address' => 'required|string',
            'sender_city' => 'required|string|max:100',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:30',
            'recipient_address' => 'required|string',
            'recipient_city' => 'required|string|max:100',
            'service_type' => 'required|string|in:Regular,Express,SameDay',
            'weight_kg' => 'required|numeric|min:0.1',
            'dimensions' => 'nullable|string',
            'declared_value' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string|in:Cash,Transfer,COD',
            'payment_status' => $isCustomer ? 'nullable|string|in:Pending,Paid' : 'required|string|in:Pending,Paid',
            'status' => $isCustomer ? 'nullable|string' : 'required|string',
        ]);

        $tariff = Tariff::findTariff($validated['sender_city'], $validated['recipient_city'], $validated['service_type']);

        $pricePerKg = $tariff ? $tariff->price_per_kg : ($validated['service_type'] == 'Express' ? 20000 : 12000);
        $chargedWeight = max(1, (int) ceil((float) $validated['weight_kg']));
        $shippingFee = $pricePerKg * $chargedWeight;
        $insuranceFee = ($validated['declared_value'] ?? 0) > 0 ? ($validated['declared_value'] * 0.002) : 0;
        $totalAmount = $shippingFee + $insuranceFee;

        $paymentStatus = $isCustomer ? ($shipment->payment_status ?? 'Pending') : ($validated['payment_status'] ?? $shipment->payment_status);
        $status = $isCustomer ? ($shipment->status ?? 'picked_up') : ($validated['status'] ?? $shipment->status);

        $shipment->update([
            'courier_id' => $validated['courier_id'] ?? null,
            'vehicle_id' => $validated['vehicle_id'] ?? null,
            'origin_hub_id' => $validated['origin_hub_id'],
            'destination_hub_id' => $validated['destination_hub_id'],
            'sender_name' => $validated['sender_name'],
            'sender_phone' => $validated['sender_phone'],
            'sender_address' => $validated['sender_address'],
            'sender_city' => $validated['sender_city'],
            'recipient_name' => $validated['recipient_name'],
            'recipient_phone' => $validated['recipient_phone'],
            'recipient_address' => $validated['recipient_address'],
            'recipient_city' => $validated['recipient_city'],
            'service_type' => $validated['service_type'],
            'weight_kg' => $validated['weight_kg'],
            'dimensions' => $validated['dimensions'],
            'declared_value' => $validated['declared_value'] ?? 0,
            'shipping_fee' => $shippingFee,
            'insurance_fee' => $insuranceFee,
            'total_amount' => $totalAmount,
            'payment_method' => $validated['payment_method'],
            'payment_status' => $paymentStatus,
            'status' => $status,
        ]);

        return redirect()->route('shipments.show', $shipment->id)->with('success', 'Data Resi Pengiriman berhasil diperbarui.');
    }

    public function updateStatus(Request $request, $id)
    {
        $shipment = Shipment::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string',
            'hub_id' => 'nullable|exists:branch_hubs,id',
            'location' => 'nullable|string|max:255',
            'description' => 'required|string',
        ]);

        $hubId = $validated['hub_id'] ?? null;
        $location = $validated['location'] ?? null;

        if ($hubId) {
            $hub = BranchHub::find($hubId);
            if ($hub) {
                $location = $hub->name . ' (' . $hub->city . ')';
            }
        } elseif (!$location) {
            $location = $shipment->currentHub ? $shipment->currentHub->name : ($shipment->originHub ? $shipment->originHub->name : $shipment->recipient_city);
        }

        $updateData = [
            'status' => $validated['status'],
        ];
        if ($hubId) {
            $updateData['current_hub_id'] = $hubId;
        }

        $shipment->update($updateData);

        ShipmentTrackingLog::create([
            'shipment_id' => $shipment->id,
            'status' => $validated['status'],
            'location' => $location,
            'description' => $validated['description'],
            'updated_by_user_id' => Auth::id(),
        ]);

        if (in_array($validated['status'], ['delivered', 'in_sorting_hub'])) {
            CourierAssignment::markShipmentTaskCompleted($shipment->id);
        } elseif ($validated['status'] === 'failed') {
            CourierAssignment::markShipmentTaskFailed($shipment->id, $validated['description']);
        }

        // Send notification to customer if package is marked delivered or failed
        if ($validated['status'] === 'delivered' && $shipment->customer_id) {
            AppNotification::sendToCustomer($shipment->customer_id, [
                'type' => 'shipment',
                'title' => 'Paket Berhasil Diterima (DELIVERED)',
                'message' => "Paket Resi {$shipment->tracking_number} telah selesai diantar (DELIVERED). Lokasi: {$location}.",
                'icon' => 'fa-circle-check',
                'color' => 'emerald',
                'url' => route('shipments.show', $shipment->id),
                'data' => ['shipment_id' => $shipment->id, 'tracking_number' => $shipment->tracking_number],
            ]);
        } elseif ($validated['status'] === 'failed' && $shipment->customer_id) {
            AppNotification::sendToCustomer($shipment->customer_id, [
                'type' => 'shipment',
                'title' => 'Pengiriman Paket Mengalami Kendala (GAGAL / HOLD)',
                'message' => "Pengiriman paket resi {$shipment->tracking_number} belum berhasil diantar. Keterangan: {$validated['description']}.",
                'icon' => 'fa-triangle-exclamation',
                'color' => 'rose',
                'url' => route('shipments.show', $shipment->id),
                'data' => ['shipment_id' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'status' => 'failed'],
            ]);
        }

        return redirect()->back()->with('success', 'Status posisi pengiriman resi berhasil diperbarui.');
    }

    public function reportFailedDelivery(Request $request, $id)
    {
        $shipment = Shipment::findOrFail($id);

        $validated = $request->validate([
            'failure_reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|max:5120',
            'location' => 'nullable|string|max:255',
        ]);

        $reason = $validated['failure_reason'];
        $notes = !empty($validated['notes']) ? " - " . $validated['notes'] : "";
        $user = Auth::user();
        $courierName = $user ? $user->name : 'Kurir';
        $description = "Pengiriman Gagal oleh {$courierName}: {$reason}{$notes}";

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('pod_photos', 'public');
        }

        $location = $validated['location'] ?: ($shipment->recipient_address . ', ' . $shipment->recipient_city);

        $updateData = [
            'status' => 'failed',
            'pod_notes' => "Kendala Pengantaran: {$reason}{$notes}",
        ];
        if ($photoPath) {
            $updateData['pod_photo'] = $photoPath;
        }

        $shipment->update($updateData);

        ShipmentTrackingLog::create([
            'shipment_id' => $shipment->id,
            'status' => 'failed',
            'location' => $location,
            'description' => $description,
            'updated_by_user_id' => Auth::id(),
        ]);

        CourierAssignment::markShipmentTaskFailed($shipment->id, $description);

        if ($shipment->customer_id) {
            AppNotification::sendToCustomer($shipment->customer_id, [
                'type' => 'shipment',
                'title' => 'Pengiriman Mengalami Kendala: ' . $reason,
                'message' => "Paket Resi {$shipment->tracking_number} gagal diantar oleh Kurir. Kendala: {$reason}. Silakan hubungi customer service kami.",
                'icon' => 'fa-triangle-exclamation',
                'color' => 'rose',
                'url' => route('shipments.show', $shipment->id),
                'data' => ['shipment_id' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'status' => 'failed'],
            ]);
        }

        return redirect()->back()->with('success', "Laporan kendala pengiriman resi {$shipment->tracking_number} berhasil disimpan (Status: Gagal / Hold).");
    }

    public function completeTask(Request $request, $id)
    {
        $shipment = Shipment::with(['courier', 'originHub', 'destinationHub', 'currentHub'])->findOrFail($id);
        $user = Auth::user();
        $courierName = $user ? $user->name : ($shipment->courier ? $shipment->courier->name : 'Kurir');

        // Check the latest assignment for this shipment
        $assignmentItem = CourierAssignmentItem::with('courierAssignment')->where('shipment_id', $shipment->id)->latest()->first();
        $assignment = $assignmentItem ? $assignmentItem->courierAssignment : null;
        $assignmentType = $assignment ? $assignment->assignment_type : 'pickup';

        if ($assignmentType === 'pickup') {
            $destHub = $assignment ? $assignment->destinationHub : ($shipment->originHub ?? ($shipment->courier ? $shipment->courier->branchHub : null));
            $hubName = $destHub ? $destHub->name : 'Gudang Hub Sortir';
            $destHubId = $destHub ? $destHub->id : null;

            $shipmentUpdates = [
                'status' => 'in_sorting_hub',
            ];
            if ($destHubId) {
                $shipmentUpdates['origin_hub_id'] = $destHubId;
                $shipmentUpdates['current_hub_id'] = $destHubId;
            }
            $shipment->update($shipmentUpdates);

            ShipmentTrackingLog::create([
                'shipment_id' => $shipment->id,
                'status' => 'in_sorting_hub',
                'location' => $hubName,
                'description' => "Penjemputan selesai oleh Kurir {$courierName}. Paket telah disetorkan ke {$hubName} (Status: In-Hub).",
                'updated_by_user_id' => Auth::id(),
            ]);

            CourierAssignment::markShipmentTaskCompleted($shipment->id);

            return redirect()->back()->with('success', "Tugas Pickup resi {$shipment->tracking_number} berhasil diselesaikan! Paket telah masuk status In-Hub & Manifes otomatis diperbarui.");
        } elseif ($assignmentType === 'transfer') {
            $destHub = $assignment ? $assignment->destinationHub : $shipment->destinationHub;
            $hubName = $destHub ? $destHub->name : 'Gudang Hub Tujuan';
            $destHubId = $destHub ? $destHub->id : null;

            $shipmentUpdates = [
                'status' => 'in_sorting_hub',
            ];
            if ($destHubId) {
                $shipmentUpdates['current_hub_id'] = $destHubId;
            }
            $shipment->update($shipmentUpdates);

            ShipmentTrackingLog::create([
                'shipment_id' => $shipment->id,
                'status' => 'in_sorting_hub',
                'location' => $hubName,
                'description' => "Transfer Antar Hub selesai oleh Kurir {$courierName}. Paket telah tiba di {$hubName} (Status: In-Hub).",
                'updated_by_user_id' => Auth::id(),
            ]);

            CourierAssignment::markShipmentTaskCompleted($shipment->id);

            return redirect()->back()->with('success', "Tugas Transfer resi {$shipment->tracking_number} berhasil diselesaikan! Manifes otomatis diperbarui.");
        } else {
            // Delivery task -> mark as delivered or redirect to ePOD
            return redirect()->route('epod.show', $shipment->tracking_number);
        }
    }

    public function printLabel($id)
    {
        $shipment = Shipment::with(['customer', 'originHub', 'destinationHub'])->findOrFail($id);
        $loggedInCustomer = $this->getLoggedInCustomer();

        if ($loggedInCustomer && $shipment->customer_id != $loggedInCustomer->id) {
            abort(403, 'Akses ditolak: Anda tidak dapat mencetak label milik customer lain.');
        }

        $epodUrl = route('epod.show', $shipment->tracking_number);

        return view('shipments.print_label', compact('shipment', 'epodUrl'));
    }

    public function destroy($id)
    {
        $shipment = Shipment::findOrFail($id);
        $loggedInCustomer = $this->getLoggedInCustomer();

        if ($loggedInCustomer && $shipment->customer_id != $loggedInCustomer->id) {
            abort(403, 'Akses ditolak: Anda tidak dapat menghapus resi milik customer lain.');
        }

        $shipment->delete();
        return redirect()->route('shipments.index')->with('success', 'Resi pengiriman berhasil dihapus.');
    }
}
