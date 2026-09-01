<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\Customer;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // 1. SLA Performance Summary
        $periodShipments = Shipment::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        $totalPeriod = (clone $periodShipments)->count();
        $deliveredPeriod = (clone $periodShipments)->where('status', 'delivered')->count();
        $failedPeriod = (clone $periodShipments)->where('status', 'failed')->count();
        $inTransitPeriod = (clone $periodShipments)->whereIn('status', ['picked_up', 'in_sorting_hub', 'in_transit', 'out_for_delivery'])->count();
        $slaRate = $totalPeriod > 0 ? round(($deliveredPeriod / $totalPeriod) * 100, 1) : 100;
        $totalRevenuePeriod = (clone $periodShipments)->sum('total_amount');

        // 2. Courier Performance Ranking
        $courierStats = Courier::withCount(['shipments' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }])->get();

        // 3. Top Customers Report
        $topCustomers = Customer::select('customers.*', DB::raw('COUNT(shipments.id) as shipment_count'), DB::raw('COALESCE(SUM(shipments.total_amount), 0) as total_spent'))
            ->leftJoin('shipments', function ($join) use ($startDate, $endDate) {
                $join->on('customers.id', '=', 'shipments.customer_id')
                    ->whereBetween('shipments.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->groupBy('customers.id')
            ->orderByDesc('total_spent')
            ->get();

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'totalPeriod',
            'deliveredPeriod',
            'failedPeriod',
            'inTransitPeriod',
            'slaRate',
            'totalRevenuePeriod',
            'courierStats',
            'topCustomers'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $shipments = Shipment::with(['customer', 'courier', 'originHub', 'destinationHub'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get();

        $filename = "Laporan_Logistik_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($shipments) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Nomor Resi',
                'Tanggal',
                'Pengirim',
                'Kota Asal',
                'Penerima',
                'Kota Tujuan',
                'Layanan',
                'Berat (kg)',
                'Biaya Total (Rp)',
                'Status',
                'Kurir',
                'Penerima ePOD'
            ]);

            foreach ($shipments as $s) {
                fputcsv($file, [
                    $s->tracking_number,
                    $s->created_at->format('Y-m-d H:i'),
                    $s->sender_name,
                    $s->sender_city,
                    $s->recipient_name,
                    $s->recipient_city,
                    $s->service_type,
                    $s->weight_kg,
                    $s->total_amount,
                    strtoupper($s->status),
                    $s->courier ? $s->courier->name : '-',
                    $s->pod_receiver_name ?? '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
