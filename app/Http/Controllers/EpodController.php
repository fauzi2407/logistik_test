<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentTrackingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EpodController extends Controller
{
    public function show($tracking_number)
    {
        $shipment = Shipment::with(['originHub', 'destinationHub', 'courier', 'vehicle'])
            ->where('tracking_number', $tracking_number)
            ->firstOrFail();

        return view('epod.show', compact('shipment'));
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
        ]);

        return redirect()->route('epod.show', $tracking_number)->with('success', 'ePOD Berhasil Disimpan! Paket telah resmi terkirim (DELIVERED).');
    }
}
