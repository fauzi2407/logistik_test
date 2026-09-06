<?php

namespace App\Http\Controllers;

use App\Models\BranchHub;
use App\Models\Courier;
use App\Models\CourierAssignment;
use App\Models\CourierAssignmentItem;
use App\Models\Shipment;
use App\Models\ShipmentTrackingLog;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourierAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $assignmentType = $request->input('assignment_type');
        $courierId = $request->input('courier_id');
        $date = $request->input('date');

        $assignments = CourierAssignment::with(['courier.branchHub', 'vehicle', 'items.shipment', 'originHub', 'destinationHub'])
            ->when($search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('assignment_number', 'like', "%{$search}%")
                      ->orWhereHas('courier', function($cq) use ($search) {
                          $cq->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('vehicle', function($vq) use ($search) {
                          $vq->where('plate_number', 'like', "%{$search}%");
                      });
                });
            })
            ->when($status, fn($q, $val) => $q->where('status', $val))
            ->when($assignmentType, fn($q, $val) => $q->where('assignment_type', $val))
            ->when($courierId, fn($q, $val) => $q->where('courier_id', $val))
            ->when($date, fn($q, $val) => $q->whereDate('assignment_date', $val))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $couriers = Courier::with(['branchHub', 'vehicle'])
            ->where('status', '!=', 'off')
            ->orderBy('name')
            ->get();

        $allCouriers = Courier::orderBy('name')->get();

        $vehicles = Vehicle::where('status', '!=', 'maintenance')->orderBy('plate_number')->get();
        $hubs = BranchHub::orderBy('name')->get();

        $unassignedShipments = Shipment::with(['originHub', 'destinationHub', 'currentHub', 'deliveryOrder', 'customer'])
            ->whereIn('status', ['pending', 'picked_up', 'in_sorting_hub', 'in_transit', 'out_for_delivery'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('courier_assignments.index', compact(
            'assignments', 'couriers', 'allCouriers', 'vehicles', 'hubs', 'unassignedShipments',
            'search', 'status', 'assignmentType', 'courierId', 'date'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'courier_id' => 'required|exists:couriers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'assignment_type' => 'required|in:pickup,delivery,transfer',
            'origin_hub_id' => 'nullable|exists:branch_hubs,id',
            'destination_hub_id' => 'nullable|exists:branch_hubs,id',
            'assignment_date' => 'required|date',
            'shipment_ids' => 'required|array|min:1',
            'shipment_ids.*' => 'exists:shipments,id',
            'notes' => 'nullable|string',
        ]);

        $courier = Courier::findOrFail($validated['courier_id']);
        $courier->update(['status' => 'on_duty']);

        if (!empty($validated['vehicle_id'])) {
            Vehicle::where('id', $validated['vehicle_id'])->update(['status' => 'in_delivery']);
        }

        $asnNumber = 'ASN-' . date('Ymd') . '-' . str_pad(CourierAssignment::count() + 1, 4, '0', STR_PAD_LEFT);

        $assignment = CourierAssignment::create([
            'assignment_number' => $asnNumber,
            'courier_id' => $courier->id,
            'vehicle_id' => $validated['vehicle_id'] ?? $courier->vehicle_id,
            'assignment_type' => $validated['assignment_type'],
            'origin_hub_id' => $validated['origin_hub_id'] ?? null,
            'destination_hub_id' => $validated['destination_hub_id'] ?? null,
            'assignment_date' => $validated['assignment_date'],
            'status' => 'in_progress',
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['shipment_ids'] as $shipmentId) {
            CourierAssignmentItem::create([
                'courier_assignment_id' => $assignment->id,
                'shipment_id' => $shipmentId,
                'status' => 'pending',
            ]);

            // Update shipment courier & status
            $shipment = Shipment::find($shipmentId);
            if ($shipment) {
                $newStatus = match($validated['assignment_type']) {
                    'delivery' => 'out_for_delivery',
                    'transfer' => 'in_transit',
                    default => 'picked_up',
                };
                $descAction = match($validated['assignment_type']) {
                    'delivery' => 'pengantaran ke penerima',
                    'transfer' => 'transfer pengiriman antar hub (Linehaul)',
                    default => 'penjemputan paket dari pengirim',
                };

                $shipmentUpdates = [
                    'courier_id' => $courier->id,
                    'vehicle_id' => $assignment->vehicle_id,
                    'status' => $newStatus,
                ];

                if ($validated['assignment_type'] === 'pickup' && !empty($validated['destination_hub_id'])) {
                    $shipmentUpdates['origin_hub_id'] = $validated['destination_hub_id'];
                    $shipmentUpdates['current_hub_id'] = $validated['destination_hub_id'];
                } elseif ($validated['assignment_type'] === 'transfer') {
                    if (!empty($validated['origin_hub_id'])) {
                        $shipmentUpdates['origin_hub_id'] = $validated['origin_hub_id'];
                    }
                    if (!empty($validated['destination_hub_id'])) {
                        $shipmentUpdates['destination_hub_id'] = $validated['destination_hub_id'];
                    }
                }

                $shipment->update($shipmentUpdates);

                ShipmentTrackingLog::create([
                    'shipment_id' => $shipment->id,
                    'status' => $newStatus,
                    'location' => $shipment->currentHub ? $shipment->currentHub->name : ($shipment->originHub ? $shipment->originHub->name : $shipment->sender_city),
                    'description' => "Paket ditugaskan ke Kurir {$courier->name} untuk proses {$descAction}.",
                    'updated_by_user_id' => Auth::id(),
                ]);
            }
        }

        return redirect()->route('courier-assignments.index')->with('success', 'Penugasan Kurir berhasil dibuat! Nomor Manifes: ' . $asnNumber);
    }

    public function show($id)
    {
        $assignment = CourierAssignment::with(['courier.branchHub', 'vehicle', 'originHub', 'destinationHub', 'items.shipment.originHub', 'items.shipment.destinationHub'])->findOrFail($id);
        return view('courier_assignments.show', compact('assignment'));
    }

    public function edit($id)
    {
        $assignment = CourierAssignment::with(['courier.branchHub', 'vehicle', 'originHub', 'destinationHub', 'items.shipment'])->findOrFail($id);
        $couriers = Courier::with(['branchHub', 'vehicle'])
            ->where('status', '!=', 'off')
            ->orderBy('name')
            ->get();
        $vehicles = Vehicle::where('status', '!=', 'maintenance')->orderBy('plate_number')->get();
        $hubs = BranchHub::orderBy('name')->get();
        $assignedShipmentIds = $assignment->items->pluck('shipment_id')->toArray();
        $unassignedShipments = Shipment::with(['originHub', 'destinationHub', 'currentHub', 'deliveryOrder', 'customer'])
            ->whereIn('status', ['pending', 'picked_up', 'in_sorting_hub', 'in_transit', 'out_for_delivery'])
            ->orWhereIn('id', $assignedShipmentIds)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('courier_assignments.edit', compact('assignment', 'couriers', 'vehicles', 'hubs', 'unassignedShipments', 'assignedShipmentIds'));
    }

    public function update(Request $request, $id)
    {
        $assignment = CourierAssignment::findOrFail($id);

        $validated = $request->validate([
            'courier_id' => 'required|exists:couriers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'assignment_type' => 'required|in:pickup,delivery,transfer',
            'origin_hub_id' => 'nullable|exists:branch_hubs,id',
            'destination_hub_id' => 'nullable|exists:branch_hubs,id',
            'assignment_date' => 'required|date',
            'status' => 'required|in:assigned,in_progress,completed,cancelled',
            'shipment_ids' => 'required|array|min:1',
            'shipment_ids.*' => 'exists:shipments,id',
            'notes' => 'nullable|string',
        ]);

        $courier = Courier::findOrFail($validated['courier_id']);

        $assignment->update([
            'courier_id' => $courier->id,
            'vehicle_id' => $validated['vehicle_id'] ?? $courier->vehicle_id,
            'assignment_type' => $validated['assignment_type'],
            'origin_hub_id' => $validated['origin_hub_id'] ?? null,
            'destination_hub_id' => $validated['destination_hub_id'] ?? null,
            'assignment_date' => $validated['assignment_date'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Re-sync assignment items
        $assignment->items()->delete();
        foreach ($validated['shipment_ids'] as $shipmentId) {
            CourierAssignmentItem::create([
                'courier_assignment_id' => $assignment->id,
                'shipment_id' => $shipmentId,
                'status' => $validated['status'] === 'completed' ? 'completed' : 'pending',
                'completed_at' => $validated['status'] === 'completed' ? now() : null,
            ]);

            $shipment = Shipment::find($shipmentId);
            if ($shipment) {
                $shipmentUpdates = [
                    'courier_id' => $courier->id,
                    'vehicle_id' => $assignment->vehicle_id,
                ];
                if ($validated['assignment_type'] === 'pickup' && !empty($validated['destination_hub_id'])) {
                    $shipmentUpdates['origin_hub_id'] = $validated['destination_hub_id'];
                    $shipmentUpdates['current_hub_id'] = $validated['destination_hub_id'];
                } elseif ($validated['assignment_type'] === 'transfer') {
                    if (!empty($validated['origin_hub_id'])) {
                        $shipmentUpdates['origin_hub_id'] = $validated['origin_hub_id'];
                    }
                    if (!empty($validated['destination_hub_id'])) {
                        $shipmentUpdates['destination_hub_id'] = $validated['destination_hub_id'];
                    }
                }
                $shipment->update($shipmentUpdates);
            }
        }

        // If marked completed, process completion updates (status -> in_sorting_hub)
        if ($validated['status'] === 'completed') {
            $this->processAssignmentCompletion($assignment);
        }

        return redirect()->route('courier-assignments.show', $assignment->id)->with('success', 'Manifes penugasan kurir berhasil diperbarui.');
    }

    public function complete($id)
    {
        $assignment = CourierAssignment::with(['courier.branchHub', 'vehicle', 'items.shipment', 'originHub', 'destinationHub'])->findOrFail($id);

        $assignment->update([
            'status' => 'completed',
        ]);

        $this->processAssignmentCompletion($assignment);

        return redirect()->back()->with('success', "Manifes {$assignment->assignment_number} telah diselesaikan! Status resi terkait otomatis diperbarui menjadi In-Hub (Siap Sortir).");
    }

    private function processAssignmentCompletion(CourierAssignment $assignment)
    {
        $assignmentType = $assignment->assignment_type;
        $courier = $assignment->courier;
        $destHub = $assignment->destinationHub ?? ($courier ? $courier->branchHub : null);
        $hubName = $destHub ? $destHub->name : 'Gudang Sorting Hub';
        $destHubId = $destHub ? $destHub->id : null;

        // Reload items with shipments
        $assignment->load(['items.shipment', 'courier', 'destinationHub', 'originHub']);

        foreach ($assignment->items as $item) {
            $item->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $shipment = $item->shipment;
            if ($shipment) {
                if ($assignmentType === 'pickup') {
                    // Update status dari pickup ke in_sorting_hub (In-Hub)
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
                        'description' => "Proses Pickup selesai (Manifes {$assignment->assignment_number}). Paket telah tiba dan disortir di Gudang {$hubName} (Status: In-Hub).",
                        'updated_by_user_id' => Auth::id(),
                    ]);
                } elseif ($assignmentType === 'transfer') {
                    // Transfer antar hub selesai -> masuk ke hub tujuan (In-Hub)
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
                        'description' => "Transfer Antar Hub selesai (Manifes {$assignment->assignment_number}). Paket telah tiba di {$hubName} (Status: In-Hub).",
                        'updated_by_user_id' => Auth::id(),
                    ]);
                }
            }
        }

        // Release courier and vehicle status if no remaining in_progress assignments
        if ($courier) {
            $hasOtherAssignments = CourierAssignment::where('courier_id', $courier->id)
                ->where('id', '!=', $assignment->id)
                ->where('status', 'in_progress')
                ->exists();

            if (!$hasOtherAssignments) {
                $courier->update(['status' => 'available']);
            }
        }

        if ($assignment->vehicle_id) {
            $hasOtherVehicleAssignments = CourierAssignment::where('vehicle_id', $assignment->vehicle_id)
                ->where('id', '!=', $assignment->id)
                ->where('status', 'in_progress')
                ->exists();

            if (!$hasOtherVehicleAssignments) {
                Vehicle::where('id', $assignment->vehicle_id)->update(['status' => 'active']);
            }
        }
    }

    public function destroy($id)
    {
        $assignment = CourierAssignment::findOrFail($id);
        $assignment->items()->delete();
        $assignment->delete();

        return redirect()->route('courier-assignments.index')->with('success', 'Manifes penugasan kurir berhasil dihapus.');
    }

    public function manifest($id)
    {
        $assignment = CourierAssignment::with(['courier.branchHub', 'vehicle', 'originHub', 'destinationHub', 'items.shipment.originHub', 'items.shipment.destinationHub'])->findOrFail($id);
        return view('courier_assignments.manifest', compact('assignment'));
    }
}
