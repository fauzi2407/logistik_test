<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $loggedInCustomer = $user->isCustomer() ? $user->customerProfile : null;

        $search = $request->input('search');
        $status = $request->input('status');

        $query = Invoice::with(['customer', 'deliveryOrder'])
            ->when($loggedInCustomer, function ($q) use ($loggedInCustomer) {
                $q->where('customer_id', $loggedInCustomer->id);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($cq) use ($search) {
                            $cq->where('name', 'like', "%{$search}%")->orWhere('company_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('deliveryOrder', function ($doq) use ($search) {
                            $doq->where('do_number', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->orderBy('id', 'desc');

        $invoices = $query->paginate(15);

        // Calculate Widgets Statistics
        $statQuery = Invoice::query()
            ->when($loggedInCustomer, function ($q) use ($loggedInCustomer) {
                $q->where('customer_id', $loggedInCustomer->id);
            });

        $totalInvoiceCount = (clone $statQuery)->count();
        $totalUnpaidAmount = (clone $statQuery)->where('status', 'unpaid')->sum('total_amount');
        $totalPaidAmount = (clone $statQuery)->where('status', 'paid')->sum('total_amount');

        return view('invoices.index', compact(
            'invoices',
            'search',
            'status',
            'totalInvoiceCount',
            'totalUnpaidAmount',
            'totalPaidAmount'
        ));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        if ($user->isCustomer()) {
            abort(403, 'Akses ditolak: Penerbitan invoice hanya dapat dilakukan oleh Staf Operasional atau Admin.');
        }

        $doId = $request->input('do_id');
        $selectedDo = $doId ? DeliveryOrder::with(['customer', 'items.shipment', 'shipments'])->find($doId) : null;

        $customers = Customer::orderBy('name')->get();
        $deliveryOrders = DeliveryOrder::with(['customer', 'shipments', 'items.shipment'])
            ->where('status', 'approved')
            ->orderBy('id', 'desc')
            ->get();

        return view('invoices.create', compact('customers', 'deliveryOrders', 'selectedDo'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->isCustomer()) {
            abort(403, 'Akses ditolak: Penerbitan invoice hanya dapat dilakukan oleh Staf Operasional atau Admin.');
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'delivery_order_id' => 'nullable|exists:delivery_orders,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $subtotal = (float) $validated['subtotal'];
        $tax = (float) ($validated['tax_amount'] ?? 0);
        $discount = (float) ($validated['discount_amount'] ?? 0);
        $total = max(0, $subtotal + $tax - $discount);

        $invNumber = 'INV-' . date('Ym') . '-' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT);

        $invoice = Invoice::create([
            'invoice_number' => $invNumber,
            'customer_id' => $validated['customer_id'],
            'delivery_order_id' => $validated['delivery_order_id'] ?? null,
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'],
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'discount_amount' => $discount,
            'total_amount' => $total,
            'status' => 'unpaid',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('invoices.show', $invoice->id)->with('success', 'Invoice penagihan biaya pengiriman berhasil diterbitkan.');
    }

    public function show($id)
    {
        $user = Auth::user();
        $loggedInCustomer = $user->isCustomer() ? $user->customerProfile : null;

        $invoice = Invoice::with(['customer', 'deliveryOrder.items.shipment', 'deliveryOrder.shipments'])->findOrFail($id);

        if ($loggedInCustomer && $invoice->customer_id != $loggedInCustomer->id) {
            abort(403, 'Akses ditolak: Anda hanya dapat melihat invoice Anda sendiri.');
        }

        return view('invoices.show', compact('invoice'));
    }

    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isCustomer()) {
            abort(403, 'Akses ditolak: Pembaharuan status pembayaran invoice hanya dapat dilakukan oleh Admin / Staf.');
        }

        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:unpaid,paid,cancelled',
            'payment_method' => 'nullable|string|max:100',
            'payment_date' => 'nullable|date',
        ]);

        $data = [
            'status' => $validated['status'],
            'payment_method' => $validated['payment_method'] ?? $invoice->payment_method,
        ];

        if ($validated['status'] === 'paid') {
            $data['payment_date'] = $validated['payment_date'] ?? now();
        } elseif ($validated['status'] === 'unpaid') {
            $data['payment_date'] = null;
        }

        $invoice->update($data);

        return redirect()->route('invoices.show', $invoice->id)->with('success', 'Status pembayaran invoice berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->isCustomer()) {
            abort(403, 'Akses ditolak: Menghapus invoice hanya dapat dilakukan oleh Admin.');
        }

        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice penagihan berhasil dihapus.');
    }

    public function print($id)
    {
        $user = Auth::user();
        $loggedInCustomer = $user->isCustomer() ? $user->customerProfile : null;

        $invoice = Invoice::with(['customer', 'deliveryOrder.items.shipment', 'deliveryOrder.shipments'])->findOrFail($id);

        if ($loggedInCustomer && $invoice->customer_id != $loggedInCustomer->id) {
            abort(403, 'Akses ditolak.');
        }

        return view('invoices.print', compact('invoice'));
    }
}
