<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\CourierCashAdvance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourierCashAdvanceController extends Controller
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
        $loggedInCourier = $this->getLoggedInCourier();

        // 1. Courier View: Only view own cash advances
        if ($loggedInCourier) {
            $advances = CourierCashAdvance::with(['approver', 'payroll'])
                ->where('courier_id', $loggedInCourier->id)
                ->latest()
                ->get();

            $totalRequested = $advances->sum('amount');
            $totalPending = $advances->where('status', 'pending')->sum('amount');
            $totalApproved = $advances->where('status', 'approved')->sum('amount');
            $totalSettled = $advances->where('status', 'settled')->sum('amount');

            return view('courier_cash_advances.courier_index', compact(
                'advances',
                'loggedInCourier',
                'totalRequested',
                'totalPending',
                'totalApproved',
                'totalSettled'
            ));
        }

        // 2. Admin / Staff View: All couriers
        $query = CourierCashAdvance::with(['courier.branchHub', 'approver', 'payroll']);

        // Filters
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('courier_id') && $request->courier_id !== 'all') {
            $query->where('courier_id', $request->courier_id);
        }

        if ($request->filled('month')) {
            $query->whereMonth('request_date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('request_date', $request->year);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('advance_number', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%")
                  ->orWhereHas('courier', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('courier_code', 'like', "%{$search}%");
                  });
            });
        }

        $advances = $query->latest()->paginate(15)->withQueryString();

        // Stats calculations
        $statTotalPending = CourierCashAdvance::where('status', 'pending')->count();
        $statTotalPendingAmount = CourierCashAdvance::where('status', 'pending')->sum('amount');
        $statTotalApproved = CourierCashAdvance::where('status', 'approved')->count();
        $statTotalApprovedAmount = CourierCashAdvance::where('status', 'approved')->sum('amount');
        $statTotalSettledAmount = CourierCashAdvance::where('status', 'settled')->sum('amount');
        $statTotalRejected = CourierCashAdvance::where('status', 'rejected')->count();

        $couriers = Courier::where('status', '!=', 'off')->orderBy('name')->get();

        return view('courier_cash_advances.index', compact(
            'advances',
            'couriers',
            'statTotalPending',
            'statTotalPendingAmount',
            'statTotalApproved',
            'statTotalApprovedAmount',
            'statTotalSettledAmount',
            'statTotalRejected'
        ));
    }

    public function store(Request $request)
    {
        $loggedInCourier = $this->getLoggedInCourier();

        $rules = [
            'amount' => 'required|numeric|min:10000',
            'reason' => 'required|string|max:1000',
            'request_date' => 'nullable|date',
        ];

        if (!$loggedInCourier) {
            $rules['courier_id'] = 'required|exists:couriers,id';
        }

        $request->validate($rules);

        $courierId = $loggedInCourier ? $loggedInCourier->id : $request->courier_id;
        $courier = Courier::findOrFail($courierId);

        // Generate clean Advance Code: KSB-YYYYMMDD-XXXX
        $dateStr = date('Ymd');
        $countToday = CourierCashAdvance::whereDate('created_at', today())->count() + 1;
        $advanceNumber = 'KSB-' . $dateStr . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

        // Check if advanceNumber collision, loop if needed
        while (CourierCashAdvance::where('advance_number', $advanceNumber)->exists()) {
            $countToday++;
            $advanceNumber = 'KSB-' . $dateStr . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);
        }

        $requestDate = $request->input('request_date', date('Y-m-d'));
        $status = 'pending';
        $approvedBy = null;
        $approvedAt = null;
        $approvalNotes = null;

        // If admin created it and chose directly approve
        if (!$loggedInCourier && $request->input('direct_approve') == '1') {
            $status = 'approved';
            $approvedBy = Auth::id();
            $approvedAt = now();
            $approvalNotes = $request->input('approval_notes', 'Disetujui langsung saat pembuatan oleh Admin');
        }

        $advance = CourierCashAdvance::create([
            'advance_number' => $advanceNumber,
            'courier_id' => $courierId,
            'amount' => $request->amount,
            'request_date' => $requestDate,
            'reason' => $request->reason,
            'status' => $status,
            'approved_by' => $approvedBy,
            'approved_at' => $approvedAt,
            'approval_notes' => $approvalNotes,
        ]);

        $formattedAmount = 'Rp ' . number_format($request->amount, 0, ',', '.');
        $msg = $loggedInCourier
            ? "Permohonan kasbon ({$advanceNumber}) sebesar {$formattedAmount} berhasil diajukan dan sedang menunggu persetujuan Admin."
            : "Kasbon ({$advanceNumber}) sebesar {$formattedAmount} untuk kurir {$courier->name} berhasil dibuat.";

        return redirect()->route('courier-cash-advances.index')->with('success', $msg);
    }

    public function approve(Request $request, $id)
    {
        $loggedInCourier = $this->getLoggedInCourier();
        if ($loggedInCourier) {
            abort(403, 'Akses ditolak: Kurir tidak memiliki wewenang untuk menyetujui kasbon.');
        }

        $advance = CourierCashAdvance::with('courier')->findOrFail($id);

        if ($advance->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya permohonan kasbon dengan status Menunggu Persetujuan (Pending) yang dapat disetujui.');
        }

        $notes = $request->input('approval_notes', 'Disetujui oleh ' . Auth::user()->name);

        $advance->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);

        $formattedAmount = 'Rp ' . number_format($advance->amount, 0, ',', '.');
        return redirect()->back()->with('success', "Kasbon {$advance->advance_number} ({$formattedAmount}) untuk kurir {$advance->courier->name} telah BERHASIL DISETUJUI.");
    }

    public function reject(Request $request, $id)
    {
        $loggedInCourier = $this->getLoggedInCourier();
        if ($loggedInCourier) {
            abort(403, 'Akses ditolak: Kurir tidak memiliki wewenang untuk menolak kasbon.');
        }

        $advance = CourierCashAdvance::with('courier')->findOrFail($id);

        if ($advance->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya permohonan kasbon dengan status Menunggu Persetujuan (Pending) yang dapat ditolak.');
        }

        $notes = $request->input('approval_notes');
        if (empty($notes)) {
            $notes = 'Ditolak oleh ' . Auth::user()->name . ' (Tanpa catatan)';
        }

        $advance->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);

        return redirect()->back()->with('success', "Kasbon {$advance->advance_number} untuk kurir {$advance->courier->name} telah DITOLAK.");
    }

    public function destroy($id)
    {
        $advance = CourierCashAdvance::with('courier')->findOrFail($id);
        $loggedInCourier = $this->getLoggedInCourier();

        if ($loggedInCourier) {
            if ($advance->courier_id !== $loggedInCourier->id) {
                abort(403, 'Akses ditolak: Anda hanya dapat membatalkan pengajuan kasbon milik sendiri.');
            }
            if ($advance->status !== 'pending') {
                return redirect()->back()->with('error', 'Pengajuan kasbon yang sudah diproses oleh admin (disetujui/ditolak) tidak dapat dibatalkan.');
            }
        } else {
            if ($advance->status === 'settled') {
                return redirect()->back()->with('error', 'Kasbon yang sudah dipotong gaji (Lunas) tidak dapat dihapus.');
            }
        }

        $code = $advance->advance_number;
        $advance->delete();

        return redirect()->back()->with('success', "Pengajuan kasbon {$code} berhasil dibatalkan / dihapus.");
    }

    public function printVoucher($id)
    {
        $advance = CourierCashAdvance::with(['courier.branchHub', 'courier.vehicle', 'approver', 'payroll'])->findOrFail($id);
        $loggedInCourier = $this->getLoggedInCourier();

        if ($loggedInCourier && $advance->courier_id !== $loggedInCourier->id) {
            abort(403, 'Akses ditolak: Anda hanya dapat mencetak bukti kasbon milik sendiri.');
        }

        return view('courier_cash_advances.print_voucher', compact('advance'));
    }
}
