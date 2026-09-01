<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\CourierPayroll;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourierPayrollController extends Controller
{
    private function getLoggedInCourier()
    {
        $user = Auth::user();
        if ($user && $user->role === 'courier') {
            return Courier::where('user_id', $user->id)->orWhere('name', $user->name)->first();
        }
        return null;
    }

    public function index(Request $request)
    {
        $month = (int) $request->input('month', date('m'));
        $year = (int) $request->input('year', date('Y'));
        $loggedInCourier = $this->getLoggedInCourier();

        $monthsList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // 1. If logged in user is a Courier, display only their paid payroll slips
        if ($loggedInCourier) {
            $paidPayrolls = CourierPayroll::with(['courier.branchHub', 'courier.vehicle'])
                ->where('courier_id', $loggedInCourier->id)
                ->where('status', 'paid')
                ->latest('period_year')
                ->latest('period_month')
                ->get();

            return view('courier_payrolls.courier_index', compact(
                'paidPayrolls',
                'loggedInCourier',
                'month',
                'year',
                'monthsList'
            ));
        }

        // 2. Admin / Staff view for all couriers
        $couriers = Courier::with(['branchHub', 'vehicle'])
            ->where('status', '!=', 'off')
            ->orderBy('name')
            ->get();

        $payrollsData = [];
        $grandTotalDeliveries = 0;
        $grandTotalBasicSalary = 0;
        $grandTotalCommission = 0;
        $grandTotalNetSalary = 0;
        $totalPaidCount = 0;

        foreach ($couriers as $courier) {
            $deliveredCount = Shipment::where('courier_id', $courier->id)
                ->where('status', 'delivered')
                ->where(function ($q) use ($year, $month) {
                    $q->where(function ($sub) use ($year, $month) {
                        $sub->whereNotNull('pod_delivered_at')
                            ->whereYear('pod_delivered_at', $year)
                            ->whereMonth('pod_delivered_at', $month);
                    })->orWhere(function ($sub) use ($year, $month) {
                        $sub->whereNull('pod_delivered_at')
                            ->whereYear('updated_at', $year)
                            ->whereMonth('updated_at', $month);
                    });
                })
                ->count();

            $commissionRate = (float) $courier->commission_per_delivery;
            $totalCommission = $deliveredCount * $commissionRate;

            $savedPayroll = CourierPayroll::where('courier_id', $courier->id)
                ->where('period_year', $year)
                ->where('period_month', $month)
                ->first();

            $basicSalary = (float) ($savedPayroll ? $savedPayroll->basic_salary : ($courier->basic_salary ?? 0));
            $bonus = $savedPayroll ? $savedPayroll->bonus_amount : 0;
            $deduction = $savedPayroll ? $savedPayroll->deduction_amount : 0;
            $netSalary = $savedPayroll ? $savedPayroll->net_salary : (($basicSalary + $totalCommission + $bonus) - $deduction);
            $status = $savedPayroll ? $savedPayroll->status : 'draft';

            if ($status === 'paid') {
                $totalPaidCount++;
            }

            $grandTotalDeliveries += $deliveredCount;
            $grandTotalBasicSalary += $basicSalary;
            $grandTotalCommission += $totalCommission;
            $grandTotalNetSalary += $netSalary;

            $payrollsData[] = [
                'courier' => $courier,
                'delivered_count' => $deliveredCount,
                'basic_salary' => $basicSalary,
                'commission_rate' => $commissionRate,
                'total_commission' => $totalCommission,
                'bonus' => $bonus,
                'deduction' => $deduction,
                'net_salary' => $netSalary,
                'status' => $status,
                'payroll' => $savedPayroll,
            ];
        }

        return view('courier_payrolls.index', compact(
            'payrollsData',
            'month',
            'year',
            'monthsList',
            'grandTotalDeliveries',
            'grandTotalBasicSalary',
            'grandTotalCommission',
            'grandTotalNetSalary',
            'totalPaidCount'
        ));
    }

    public function show($id)
    {
        $payroll = CourierPayroll::with(['courier.branchHub', 'courier.vehicle'])->findOrFail($id);
        $loggedInCourier = $this->getLoggedInCourier();

        if ($loggedInCourier) {
            if ($payroll->courier_id != $loggedInCourier->id || $payroll->status !== 'paid') {
                abort(403, 'Akses ditolak: Kurir hanya dapat melihat slip gaji milik sendiri yang berstatus Paid (Lunas).');
            }
        }

        $deliveredShipments = Shipment::where('courier_id', $payroll->courier_id)
            ->where('status', 'delivered')
            ->where(function ($q) use ($payroll) {
                $q->where(function ($sub) use ($payroll) {
                    $sub->whereNotNull('pod_delivered_at')
                        ->whereYear('pod_delivered_at', $payroll->period_year)
                        ->whereMonth('pod_delivered_at', $payroll->period_month);
                })->orWhere(function ($sub) use ($payroll) {
                    $sub->whereNull('pod_delivered_at')
                        ->whereYear('updated_at', $payroll->period_year)
                        ->whereMonth('updated_at', $payroll->period_month);
                });
            })
            ->latest()
            ->get();

        return view('courier_payrolls.show', compact('payroll', 'deliveredShipments'));
    }

    public function generate(Request $request)
    {
        $loggedInCourier = $this->getLoggedInCourier();
        if ($loggedInCourier) {
            abort(403, 'Akses ditolak: Kurir tidak diperbolehkan mengubah data penggajian.');
        }

        $courierId = $request->input('courier_id');
        $month = (int) $request->input('period_month', date('m'));
        $year = (int) $request->input('period_year', date('Y'));

        $courier = Courier::findOrFail($courierId);

        $deliveredCount = Shipment::where('courier_id', $courier->id)
            ->where('status', 'delivered')
            ->where(function ($q) use ($year, $month) {
                $q->where(function ($sub) use ($year, $month) {
                    $sub->whereNotNull('pod_delivered_at')
                        ->whereYear('pod_delivered_at', $year)
                        ->whereMonth('pod_delivered_at', $month);
                })->orWhere(function ($sub) use ($year, $month) {
                    $sub->whereNull('pod_delivered_at')
                        ->whereYear('updated_at', $year)
                        ->whereMonth('updated_at', $month);
                });
            })
            ->count();

        $basicSalary = $request->has('basic_salary') ? (float) $request->input('basic_salary') : (float) ($courier->basic_salary ?? 0);
        $commissionRate = (float) $courier->commission_per_delivery;
        $totalCommission = $deliveredCount * $commissionRate;
        $bonus = (float) $request->input('bonus_amount', 0);
        $deduction = (float) $request->input('deduction_amount', 0);
        $netSalary = ($basicSalary + $totalCommission + $bonus) - $deduction;

        $payrollCode = 'PAY-' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($courier->id, 3, '0', STR_PAD_LEFT);

        $payroll = CourierPayroll::updateOrCreate(
            [
                'courier_id' => $courier->id,
                'period_year' => $year,
                'period_month' => $month,
            ],
            [
                'payroll_code' => $payrollCode,
                'total_deliveries' => $deliveredCount,
                'basic_salary' => $basicSalary,
                'commission_per_delivery' => $commissionRate,
                'total_commission' => $totalCommission,
                'bonus_amount' => $bonus,
                'deduction_amount' => $deduction,
                'net_salary' => $netSalary,
                'status' => $request->input('status', 'draft'),
                'payment_method' => $request->input('payment_method', 'Transfer Bank'),
                'payment_date' => $request->input('status') === 'paid' ? now() : null,
                'notes' => $request->input('notes'),
            ]
        );

        return redirect()->route('courier-payrolls.show', $payroll->id)->with('success', "Slip Gaji Komisi Kurir {$courier->name} berhasil diperbarui.");
    }

    public function printSlip($id)
    {
        $payroll = CourierPayroll::with(['courier.branchHub', 'courier.vehicle'])->findOrFail($id);
        $loggedInCourier = $this->getLoggedInCourier();

        if ($loggedInCourier) {
            if ($payroll->courier_id != $loggedInCourier->id || $payroll->status !== 'paid') {
                abort(403, 'Akses ditolak: Kurir hanya dapat mencetak slip gaji milik sendiri yang berstatus Paid (Lunas).');
            }
        }

        $deliveredShipments = Shipment::where('courier_id', $payroll->courier_id)
            ->where('status', 'delivered')
            ->where(function ($q) use ($payroll) {
                $q->where(function ($sub) use ($payroll) {
                    $sub->whereNotNull('pod_delivered_at')
                        ->whereYear('pod_delivered_at', $payroll->period_year)
                        ->whereMonth('pod_delivered_at', $payroll->period_month);
                })->orWhere(function ($sub) use ($payroll) {
                    $sub->whereNull('pod_delivered_at')
                        ->whereYear('updated_at', $payroll->period_year)
                        ->whereMonth('updated_at', $payroll->period_month);
                });
            })
            ->latest()
            ->get();

        return view('courier_payrolls.print_slip', compact('payroll', 'deliveredShipments'));
    }
}
