<?php

namespace App\Http\Controllers;

use App\Models\BranchHub;
use App\Models\Shipment;
use App\Models\ShipmentTrackingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EpodController extends Controller
{
    public function show($tracking_number)
    {
        $shipment = Shipment::with(['originHub', 'destinationHub', 'courier', 'vehicle', 'trackingLogs.updatedBy'])
            ->where('tracking_number', $tracking_number)
            ->firstOrFail();

        $hubs = BranchHub::orderBy('name')->get();

        return view('epod.show', compact('shipment', 'hubs'));
    }

    public function storeTransit(Request $request, $tracking_number)
    {
        $shipment = Shipment::where('tracking_number', $tracking_number)->firstOrFail();

        $validated = $request->validate([
            'status' => 'required|string|in:in_sorting_hub,in_transit,out_for_delivery',
            'location' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $statusLabels = [
            'in_sorting_hub' => 'Tiba di Hub Sorting / Gudang Transit',
            'in_transit' => 'Dalam Perjalanan Antar Hub (Linehaul)',
            'out_for_delivery' => 'Dibawa Kurir (Out for Delivery)',
        ];

        $statusLabel = $statusLabels[$validated['status']] ?? $validated['status'];
        $notesText = $validated['notes'] ? " ({$validated['notes']})" : "";

        $shipment->update([
            'status' => $validated['status'],
        ]);

        ShipmentTrackingLog::create([
            'shipment_id' => $shipment->id,
            'status' => $validated['status'],
            'location' => $validated['location'],
            'description' => "Status Transit Diperbarui via Scan QR AWB: {$statusLabel}{$notesText}.",
            'updated_by_user_id' => Auth::id(),
        ]);

        $message = "Status transit resi {$shipment->tracking_number} berhasil diperbarui menjadi: {$statusLabel}!";
        if ($validated['status'] === 'out_for_delivery') {
            $message .= " Paket kini siap diserahterimakan dan form ePOD telah diaktifkan.";
        }

        return redirect()->route('epod.show', $tracking_number)->with('success', $message);
    }

    public function store(Request $request, $tracking_number)
    {
        $shipment = Shipment::where('tracking_number', $tracking_number)->firstOrFail();

        $validated = $request->validate([
            'pod_receiver_name' => 'required|string|max:255',
            'pod_receiver_relation' => 'required|string|max:100',
            'pod_notes' => 'nullable|string',
            'pod_signature' => 'nullable|string', // Base64 data URL
            'pod_photo' => 'nullable|image|max:5120', // Upload photo
            'pod_latitude' => 'nullable|string|max:50',
            'pod_longitude' => 'nullable|string|max:50',
            'pod_location_name' => 'nullable|string|max:255',
        ]);

        $photoPath = $shipment->pod_photo;
        if ($request->hasFile('pod_photo')) {
            $photoPath = $request->file('pod_photo')->store('pod_photos', 'public');
        }

        $shipment->update([
            'status' => 'delivered',
            'pod_receiver_name' => $validated['pod_receiver_name'],
            'pod_receiver_relation' => $validated['pod_receiver_relation'],
            'pod_notes' => $validated['pod_notes'],
            'pod_signature' => $validated['pod_signature'] ?? $shipment->pod_signature,
            'pod_photo' => $photoPath,
            'pod_latitude' => $validated['pod_latitude'] ?? $shipment->pod_latitude,
            'pod_longitude' => $validated['pod_longitude'] ?? $shipment->pod_longitude,
            'pod_location_name' => $validated['pod_location_name'] ?? $shipment->pod_location_name,
            'pod_delivered_at' => now(),
        ]);

        $gpsInfo = "";
        if (!empty($validated['pod_latitude']) && !empty($validated['pod_longitude'])) {
            $gpsInfo = " (GPS: {$validated['pod_latitude']}, {$validated['pod_longitude']})";
        }

        ShipmentTrackingLog::create([
            'shipment_id' => $shipment->id,
            'status' => 'delivered',
            'location' => $validated['pod_location_name'] ?: $shipment->recipient_city,
            'description' => "Paket telah diterima oleh {$validated['pod_receiver_name']} ({$validated['pod_receiver_relation']}). Bukti ePOD (Foto, Tanda Tangan & Lokasi GPS Real-Time{$gpsInfo}) berhasil diverifikasi.",
            'updated_by_user_id' => Auth::id(),
        ]);

        \App\Models\CourierAssignment::markShipmentTaskCompleted($shipment->id);

        // Notify Customer (and Admin automatically receives a copy!)
        if ($shipment->customer_id) {
            \App\Models\AppNotification::sendToCustomer($shipment->customer_id, [
                'type' => 'shipment',
                'title' => 'Paket Berhasil Diterima (DELIVERED)',
                'message' => "Paket Resi {$shipment->tracking_number} telah sukses diserahterimakan kepada {$validated['pod_receiver_name']} ({$validated['pod_receiver_relation']}).",
                'icon' => 'fa-box-open',
                'color' => 'emerald',
                'url' => route('shipments.show', $shipment->id),
                'data' => ['shipment_id' => $shipment->id, 'tracking_number' => $shipment->tracking_number],
            ]);
        }

        return redirect()->route('epod.show', $tracking_number)->with('success', 'ePOD Berhasil Disimpan! Paket telah resmi terkirim (DELIVERED).');
    }

    public function storeFailed(Request $request, $tracking_number)
    {
        $shipment = Shipment::where('tracking_number', $tracking_number)->firstOrFail();

        $validated = $request->validate([
            'failure_reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|max:5120',
            'pod_latitude' => 'nullable|string|max:50',
            'pod_longitude' => 'nullable|string|max:50',
            'pod_location_name' => 'nullable|string|max:255',
        ]);

        $reason = $validated['failure_reason'];
        $notes = !empty($validated['notes']) ? " - " . $validated['notes'] : "";
        $gpsInfo = (!empty($validated['pod_latitude']) && !empty($validated['pod_longitude'])) ? " (GPS: {$validated['pod_latitude']}, {$validated['pod_longitude']})" : "";
        $description = "Pengiriman Gagal via Scan ePOD: {$reason}{$notes}{$gpsInfo}";

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('pod_photos', 'public');
        }

        $location = $validated['pod_location_name'] ?: $shipment->recipient_city;

        $updateData = [
            'status' => 'failed',
            'pod_notes' => "Kendala Pengantaran: {$reason}{$notes}",
            'pod_latitude' => $validated['pod_latitude'] ?? $shipment->pod_latitude,
            'pod_longitude' => $validated['pod_longitude'] ?? $shipment->pod_longitude,
            'pod_location_name' => $validated['pod_location_name'] ?? $shipment->pod_location_name,
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

        \App\Models\CourierAssignment::markShipmentTaskFailed($shipment->id, $description);

        if ($shipment->customer_id) {
            \App\Models\AppNotification::sendToCustomer($shipment->customer_id, [
                'type' => 'shipment',
                'title' => 'Pengiriman Mengalami Kendala: ' . $reason,
                'message' => "Paket Resi {$shipment->tracking_number} gagal diantar oleh Kurir. Kendala: {$reason}. Hubungi kami untuk konfirmasi alamat/penerima.",
                'icon' => 'fa-triangle-exclamation',
                'color' => 'rose',
                'url' => route('shipments.show', $shipment->id),
                'data' => ['shipment_id' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'status' => 'failed'],
            ]);
        }

        return redirect()->route('epod.show', $tracking_number)->with('success', 'Laporan Pengiriman Gagal berhasil disimpan. Status paket kini HOLD / GAGAL.');
    }
}
