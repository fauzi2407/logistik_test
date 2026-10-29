<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\Shipment;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        $code = trim($request->input('code', $request->input('tracking_number', '')));
        $shipment = null;
        $deliveryOrder = null;

        if ($code) {
            $shipment = Shipment::with(['originHub', 'destinationHub', 'currentHub', 'courier', 'vehicle', 'trackingLogs'])
                ->where('tracking_number', $code)
                ->first();

            if (!$shipment) {
                $deliveryOrder = DeliveryOrder::with(['items', 'customer', 'shipment.trackingLogs'])
                    ->where('do_number', $code)
                    ->first();
                if ($deliveryOrder && $deliveryOrder->shipment) {
                    $shipment = $deliveryOrder->shipment;
                }
            }
        }

        if ($request->expectsJson() || $request->ajax() || $request->input('format') === 'json') {
            return $this->formatShipmentJson($code, $shipment);
        }

        return view('tracking.index', compact('code', 'shipment', 'deliveryOrder'));
    }

    public function apiTrack(Request $request)
    {
        $code = trim($request->input('code', $request->input('tracking_number', '')));
        $shipment = null;

        if ($code) {
            $shipment = Shipment::with(['originHub', 'destinationHub', 'currentHub', 'courier', 'vehicle', 'trackingLogs'])
                ->where('tracking_number', $code)
                ->first();

            if (!$shipment) {
                $deliveryOrder = DeliveryOrder::with(['items', 'customer', 'shipment.trackingLogs'])
                    ->where('do_number', $code)
                    ->first();
                if ($deliveryOrder && $deliveryOrder->shipment) {
                    $shipment = $deliveryOrder->shipment;
                }
            }
        }

        return $this->formatShipmentJson($code, $shipment);
    }

    private function formatShipmentJson($code, $shipment)
    {
        if (!$code) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nomor resi atau kode pelacakan tidak boleh kosong.'
            ], 400);
        }

        if (!$shipment) {
            return response()->json([
                'status' => 'error',
                'message' => "Nomor resi / DO '{$code}' tidak ditemukan di sistem AWX. Mohon periksa kembali nomor resi Anda."
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'tracking_number' => $shipment->tracking_number,
                'status' => $shipment->status,
                'status_label' => strtoupper(str_replace('_', ' ', $shipment->status)),
                'service_type' => $shipment->service_type,
                'weight_kg' => $shipment->weight_kg,
                'total_amount' => $shipment->total_amount,
                'sender_name' => $shipment->sender_name,
                'sender_city' => $shipment->sender_city,
                'sender_address' => $shipment->sender_address,
                'recipient_name' => $shipment->recipient_name,
                'recipient_city' => $shipment->recipient_city,
                'recipient_address' => $shipment->recipient_address,
                'pod_receiver_name' => $shipment->pod_receiver_name,
                'pod_receiver_relation' => $shipment->pod_receiver_relation,
                'pod_delivered_at' => $shipment->pod_delivered_at ? $shipment->pod_delivered_at->format('d M Y, H:i') . ' WIB' : null,
                'pod_signature' => $shipment->pod_signature,
                'created_at' => $shipment->created_at->format('d M Y, H:i') . ' WIB',
                'logs' => $shipment->trackingLogs->map(function($l) {
                    return [
                        'date' => \Carbon\Carbon::parse($l->created_at)->format('d M Y, H:i') . ' WIB',
                        'location' => $l->location,
                        'description' => $l->description,
                        'status' => $l->status,
                    ];
                })
            ]
        ]);
    }
}
