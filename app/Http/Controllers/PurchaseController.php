<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-t'));

        $query = Purchase::with(['vendor', 'createdUser'])
            ->whereBetween('purchase_date', [$startDate, $endDate]);

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->input('vendor_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('purchase_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $purchases = $query->latest('purchase_date')->latest('id')->paginate(15);
        $vendors = Vendor::where('status', 'active')->orderBy('name')->get();

        return view('purchases.index', compact('purchases', 'vendors', 'startDate', 'endDate'));
    }

    public function create()
    {
        $vendors = Vendor::where('status', 'active')->orderBy('name')->get();
        if ($vendors->isEmpty()) {
            return redirect()->route('vendors.index')->with('warning', 'Silakan tambahkan minimal 1 Master Vendor/Supplier sebelum membuat transaksi Pembelian Barang.');
        }

        return view('purchases.create', compact('vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_date' => 'required|date',
            'vendor_id' => 'required|exists:vendors,id',
            'payment_status' => 'required|in:unpaid,partial,paid',
            'payment_method' => 'nullable|string|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.category' => 'required|string|max:100',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'required|string|max:50',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        $itemsData = $request->input('items');
        $subtotal = 0;

        foreach ($itemsData as $it) {
            $qty = (int) $it['quantity'];
            $price = (float) $it['unit_price'];
            $subtotal += ($qty * $price);
        }

        $discount = (float) $request->input('discount_amount', 0);
        $tax = (float) $request->input('tax_amount', 0);
        $grandTotal = ($subtotal - $discount) + $tax;

        $purchaseNumber = 'PO-' . date('Ym', strtotime($request->input('purchase_date'))) . '-' . str_pad(Purchase::count() + 1, 4, '0', STR_PAD_LEFT);

        $purchase = DB::transaction(function () use ($request, $purchaseNumber, $subtotal, $discount, $tax, $grandTotal, $itemsData) {
            $po = Purchase::create([
                'purchase_number' => $purchaseNumber,
                'purchase_date' => $request->input('purchase_date'),
                'vendor_id' => $request->input('vendor_id'),
                'status' => 'ordered',
                'payment_status' => $request->input('payment_status', 'unpaid'),
                'payment_method' => $request->input('payment_method', 'Transfer Bank'),
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'tax_amount' => $tax,
                'total_amount' => $grandTotal,
                'notes' => $request->input('notes'),
                'created_by_user_id' => Auth::id(),
            ]);

            foreach ($itemsData as $it) {
                $qty = (int) $it['quantity'];
                $price = (float) $it['unit_price'];
                $lineTotal = $qty * $price;

                PurchaseItem::create([
                    'purchase_id' => $po->id,
                    'item_name' => $it['item_name'],
                    'category' => $it['category'],
                    'quantity' => $qty,
                    'unit' => $it['unit'],
                    'unit_price' => $price,
                    'total_price' => $lineTotal,
                    'notes' => $it['notes'] ?? null,
                ]);
            }

            // Optional Auto-Journal Entry for Accounting if configured
            $this->createJournalForPurchase($po);

            return $po;
        });

        return redirect()->route('purchases.show', $purchase->id)->with('success', "Transaksi Pembelian Barang {$purchaseNumber} berhasil dibuat.");
    }

    public function show($id)
    {
        $purchase = Purchase::with(['vendor', 'items', 'createdUser'])->findOrFail($id);
        return view('purchases.show', compact('purchase'));
    }

    public function updateStatus(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:draft,ordered,received,cancelled',
            'payment_status' => 'required|in:unpaid,partial,paid',
            'payment_method' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $purchase->update($validated);

        return redirect()->route('purchases.show', $purchase->id)->with('success', "Status Purchase Order {$purchase->purchase_number} berhasil diperbarui.");
    }

    public function print($id)
    {
        $purchase = Purchase::with(['vendor', 'items', 'createdUser'])->findOrFail($id);
        return view('purchases.print', compact('purchase'));
    }

    private function createJournalForPurchase(Purchase $purchase)
    {
        // Find matching COA accounts
        $expenseAcc = ChartOfAccount::where('account_code', '5102')->first() 
            ?: ChartOfAccount::where('account_type', 'Expense')->first();
            
        $cashBankAcc = ChartOfAccount::where('account_code', '1102')->first() 
            ?: ChartOfAccount::where('account_code', '2101')->first() 
            ?: ChartOfAccount::where('account_type', 'Asset')->first();

        if ($expenseAcc && $cashBankAcc) {
            $journalNum = 'JRN-PO-' . date('Ym') . '-' . str_pad(JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT);
            
            $journal = JournalEntry::create([
                'journal_number' => $journalNum,
                'entry_date' => $purchase->purchase_date,
                'reference_number' => $purchase->purchase_number,
                'description' => "Pembelian Barang/Perlengkapan dari Vendor {$purchase->vendor->name} ({$purchase->purchase_number})",
                'total_debit' => $purchase->total_amount,
                'total_credit' => $purchase->total_amount,
                'created_by_user_id' => Auth::id(),
            ]);

            // Debit Expense / Asset
            JournalEntryItem::create([
                'journal_entry_id' => $journal->id,
                'chart_of_account_id' => $expenseAcc->id,
                'debit_amount' => $purchase->total_amount,
                'credit_amount' => 0,
                'memo' => "Pembelian barang PO {$purchase->purchase_number}",
            ]);

            // Credit Cash / Payable
            JournalEntryItem::create([
                'journal_entry_id' => $journal->id,
                'chart_of_account_id' => $cashBankAcc->id,
                'debit_amount' => 0,
                'credit_amount' => $purchase->total_amount,
                'memo' => "Pembayaran PO {$purchase->purchase_number} ke Vendor {$purchase->vendor->name}",
            ]);
        }
    }
}
