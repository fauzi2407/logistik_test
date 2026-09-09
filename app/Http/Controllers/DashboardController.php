<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\CourierAssignment;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
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

            $totalDOCount = 0;
            $totalShipmentCount = 0;
            $inTransitCount = 0;
            $deliveredCount = 0;

            $searchDo = $request->input('search_do', $request->input('search'));
            $statusDo = $request->input('status_do');

            $searchShipment = $request->input('search_shipment', $request->input('search'));
            $statusShipment = $request->input('status_shipment');

            if ($customer) {
                $totalDOCount = DeliveryOrder::where('customer_id', $customer->id)->count();
                $totalShipmentCount = Shipment::where('customer_id', $customer->id)->count();
                $inTransitCount = Shipment::where('customer_id', $customer->id)->where('status', '!=', 'delivered')->count();
                $deliveredCount = Shipment::where('customer_id', $customer->id)->where('status', 'delivered')->count();

                $myDeliveryOrders = DeliveryOrder::with(['items', 'shipment'])
                    ->where('customer_id', $customer->id)
                    ->when($searchDo, function ($q, $search) {
                        $q->where(function ($sub) use ($search) {
                            $sub->where('do_number', 'like', "%{$search}%")
                                ->orWhere('recipient_name', 'like', "%{$search}%")
                                ->orWhere('recipient_city', 'like', "%{$search}%")
                                ->orWhereHas('items', function ($iq) use ($search) {
                                    $iq->where('recipient_name', 'like', "%{$search}%")
                                        ->orWhere('recipient_city', 'like', "%{$search}%")
                                        ->orWhere('item_name', 'like', "%{$search}%")
                                        ->orWhere('account_ref', 'like', "%{$search}%");
                                });
                        });
                    })
                    ->when($statusDo, function ($q, $status) {
                        if ($status === 'completed' || $status === 'komplit') {
                            $q->whereIn('status', ['completed', 'komplit']);
                        } else {
                            $q->where('status', $status);
                        }
                    })
                    ->latest()
                    ->paginate(10, ['*'], 'do_page')
                    ->withQueryString();

                $myShipments = Shipment::with(['originHub', 'destinationHub'])
                    ->where('customer_id', $customer->id)
                    ->when($searchShipment, function ($q, $search) {
                        $q->where(function ($sub) use ($search) {
                            $sub->where('tracking_number', 'like', "%{$search}%")
                                ->orWhere('recipient_name', 'like', "%{$search}%")
                                ->orWhere('recipient_city', 'like', "%{$search}%")
                                ->orWhere('service_type', 'like', "%{$search}%");
                        });
                    })
                    ->when($statusShipment, function ($q, $status) {
                        $q->where('status', $status);
                    })
                    ->latest()
                    ->paginate(10, ['*'], 'shipment_page')
                    ->withQueryString();
            } else {
                $myDeliveryOrders = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1, ['pageName' => 'do_page']);
                $myShipments = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1, ['pageName' => 'shipment_page']);
            }

            return view('customer.dashboard', compact(
                'customer',
                'myDeliveryOrders',
                'myShipments',
                'totalDOCount',
                'totalShipmentCount',
                'inTransitCount',
                'deliveredCount',
                'searchDo',
                'statusDo',
                'searchShipment',
                'statusShipment'
            ));
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
