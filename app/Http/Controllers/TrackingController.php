<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\Shipment;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        $code = trim($request->input('code'));
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

        return view('tracking.index', compact('code', 'shipment', 'deliveryOrder'));
    }
}
