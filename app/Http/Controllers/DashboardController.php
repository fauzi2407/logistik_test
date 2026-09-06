<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\CourierAssignment;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\Shipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Dedicated Role View for Couriers / Drivers
        if ($user && $user->role === 'courier') {
            $courier = Courier::with(['branchHub', 'vehicle'])->where('user_id', $user->id)->first();
            
            $assignedShipments = collect();
            $myAssignments = collect();

            if ($courier) {
                $assignedShipments = Shipment::with(['originHub', 'destinationHub', 'customer', 'deliveryOrder', 'currentHub'])
                    ->where('courier_id', $courier->id)
                    ->latest()
                    ->get();

                $myAssignments = CourierAssignment::with([
                    'items.shipment.originHub',
                    'items.shipment.destinationHub',
                    'items.shipment.deliveryOrder',
                    'vehicle',
                    'originHub',
                    'destinationHub'
                ])
                ->where('courier_id', $courier->id)
                ->latest()
                ->get();
            }

            return view('courier.dashboard', compact('courier', 'assignedShipments', 'myAssignments'));
        }

        // 2. Dedicated Role View for Customers / Corporate Clients (Data Isolation)
        if ($user && $user->role === 'customer') {
            $customer = Customer::where('email', $user->email)->orWhere('user_id', $user->id)->first();

            $myDeliveryOrders = collect();
            $myShipments = collect();

            if ($customer) {
                $myDeliveryOrders = DeliveryOrder::with(['items', 'shipment'])
                    ->where('customer_id', $customer->id)
                    ->latest()
                    ->get();

                $myShipments = Shipment::with(['originHub', 'destinationHub'])
                    ->where('customer_id', $customer->id)
                    ->latest()
                    ->get();
            }

            return view('customer.dashboard', compact('customer', 'myDeliveryOrders', 'myShipments'));
        }

        // 3. Executive Admin & Operational Staff View
        $totalShipments = Shipment::count();
        $totalRevenue = Shipment::sum('total_amount');
        $activeInTransit = Shipment::whereIn('status', ['picked_up', 'in_sorting_hub', 'in_transit', 'out_for_delivery'])->count();
        $deliveredCount = Shipment::where('status', 'delivered')->count();
        $slaSuccessRate = $totalShipments > 0 ? round(($deliveredCount / $totalShipments) * 100, 1) : 100;
        $totalCustomers = Customer::count();
        $totalActiveDO = DeliveryOrder::whereIn('status', ['approved', 'processing', 'shipped'])->count();

        // Status Breakdown for Donut Chart
        $statusCounts = Shipment::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $allStatuses = [
            'draft' => 'Draft',
            'picked_up' => 'Penjemputan',
            'in_sorting_hub' => 'In Sorting Hub',
            'in_transit' => 'In Transit',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Terkirim (Delivered)',
            'failed' => 'Gagal / Hold',
        ];

        $chartStatusLabels = array_values($allStatuses);
        $chartStatusData = [];
        foreach (array_keys($allStatuses) as $statusKey) {
            $chartStatusData[] = $statusCounts[$statusKey] ?? 0;
        }

        // Top 5 Destination Cities for Bar Chart
        $topCities = Shipment::select('recipient_city', DB::raw('count(*) as total'))
            ->groupBy('recipient_city')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Recent Shipments
        $recentShipments = Shipment::with(['customer', 'courier', 'latestLog'])
            ->latest()
            ->limit(8)
            ->get();

        // Executive Alert: Shipments pending in sorting hub or transit > 24 hours
        $delayedShipments = Shipment::whereIn('status', ['in_sorting_hub', 'in_transit', 'picked_up'])
            ->where('updated_at', '<', now()->subHours(24))
            ->get();

        // Real-Time Fleet & Courier Package Monitoring
        $activeCouriersMonitoring = Courier::with(['vehicle', 'branchHub', 'shipments' => function($q) {
            $q->whereIn('status', ['picked_up', 'in_sorting_hub', 'in_transit', 'out_for_delivery']);
        }])->get();

        return view('dashboard.index', compact(
            'totalShipments',
            'totalRevenue',
            'activeInTransit',
            'deliveredCount',
            'slaSuccessRate',
            'totalCustomers',
            'totalActiveDO',
            'chartStatusLabels',
            'chartStatusData',
            'topCities',
            'recentShipments',
            'delayedShipments',
            'activeCouriersMonitoring'
        ));
    }
}
