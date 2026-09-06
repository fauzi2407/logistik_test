<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $vehicles = Vehicle::withCount('couriers')
            ->with('journalEntry')
            ->when($search, function ($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                  ->orWhere('vehicle_type', 'like', "%{$search}%")
                  ->orWhere('ownership_type', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $fundingAccounts = ChartOfAccount::where('is_active', true)
            ->whereIn('account_type', ['Asset', 'Equity', 'Liability'])
            ->where('account_code', '!=', '1201')
            ->orderBy('account_code')
            ->get();

        return view('vehicles.index', compact('vehicles', 'fundingAccounts', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number',
            'vehicle_type' => 'required|string|max:100',
            'ownership_type' => 'required|in:company,personal',
            'capacity_kg' => 'required|numeric|min:1',
            'asset_value' => 'nullable|numeric|min:0',
            'funding_account_id' => 'nullable|exists:chart_of_accounts,id',
            'status' => 'required|in:active,maintenance,in_delivery',
        ]);

        $assetValue = ($validated['ownership_type'] === 'company') ? ($validated['asset_value'] ?? 0) : 0;

        DB::transaction(function () use ($validated, $assetValue, $request) {
            $vehicle = Vehicle::create([
                'plate_number' => strtoupper(trim($validated['plate_number'])),
                'vehicle_type' => $validated['vehicle_type'],
                'ownership_type' => $validated['ownership_type'],
                'capacity_kg' => $validated['capacity_kg'],
                'asset_value' => $assetValue,
                'status' => $validated['status'],
            ]);

            if ($validated['ownership_type'] === 'company' && $assetValue > 0) {
                $this->createAssetJournalEntry($vehicle, $request->input('funding_account_id'));
            }
        });

        $msg = $validated['ownership_type'] === 'company' && $assetValue > 0
            ? 'Armada milik perusahaan berhasil ditambahkan dan otomatis tercatat sebagai Aset di Akuntansi.'
            : 'Armada berhasil ditambahkan.';

        return redirect()->route('vehicles.index')->with('success', $msg);
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $validated = $request->validate([
            'vehicle_type' => 'required|string|max:100',
            'ownership_type' => 'required|in:company,personal',
            'capacity_kg' => 'required|numeric|min:1',
            'asset_value' => 'nullable|numeric|min:0',
            'funding_account_id' => 'nullable|exists:chart_of_accounts,id',
            'status' => 'required|in:active,maintenance,in_delivery',
        ]);

        $assetValue = ($validated['ownership_type'] === 'company') ? ($validated['asset_value'] ?? 0) : 0;

        DB::transaction(function () use ($vehicle, $validated, $assetValue, $request) {
            $oldOwnership = $vehicle->ownership_type;
            $oldAssetValue = $vehicle->asset_value;

            $vehicle->update([
                'vehicle_type' => $validated['vehicle_type'],
                'ownership_type' => $validated['ownership_type'],
                'capacity_kg' => $validated['capacity_kg'],
                'asset_value' => $assetValue,
                'status' => $validated['status'],
            ]);

            if ($validated['ownership_type'] === 'company' && $assetValue > 0) {
                if (!$vehicle->journal_entry_id) {
                    $this->createAssetJournalEntry($vehicle, $request->input('funding_account_id'));
                } else {
                    $this->updateAssetJournalEntry($vehicle, $assetValue, $request->input('funding_account_id'));
                }
            }
        });

        return redirect()->route('vehicles.index')->with('success', 'Data armada berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        if ($vehicle->journal_entry_id) {
            $journal = JournalEntry::find($vehicle->journal_entry_id);
            if ($journal) {
                $journal->items()->delete();
                $journal->delete();
            }
        }
        $vehicle->delete();
        return redirect()->route('vehicles.index')->with('success', 'Armada berhasil dihapus.');
    }

    private function createAssetJournalEntry(Vehicle $vehicle, $fundingAccountId = null)
    {
        $assetAcc = ChartOfAccount::where('account_code', '1201')->first()
            ?: ChartOfAccount::where('account_type', 'Asset')->where('account_code', 'like', '12%')->first();

        $creditAcc = $fundingAccountId 
            ? ChartOfAccount::find($fundingAccountId)
            : (ChartOfAccount::where('account_code', '1101')->first() 
               ?: ChartOfAccount::where('account_code', '1102')->first() 
               ?: ChartOfAccount::where('account_code', '3101')->first());

        if ($assetAcc && $creditAcc) {
            $journalNum = 'JRN-AST-' . date('Ym') . '-' . str_pad(JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT);

            $journal = JournalEntry::create([
                'journal_number' => $journalNum,
                'entry_date' => now()->toDateString(),
                'reference_number' => $vehicle->plate_number,
                'description' => "Pencatatan Perolehan Aset Armada Perusahaan ({$vehicle->plate_number} - {$vehicle->vehicle_type})",
                'total_debit' => $vehicle->asset_value,
                'total_credit' => $vehicle->asset_value,
                'created_by_user_id' => Auth::id(),
            ]);

            // Debit Asset (1201 - Armada Kendaraan & Inventaris)
            JournalEntryItem::create([
                'journal_entry_id' => $journal->id,
                'chart_of_account_id' => $assetAcc->id,
                'debit_amount' => $vehicle->asset_value,
                'credit_amount' => 0,
                'memo' => "Perolehan Aset Kendaraan {$vehicle->plate_number} ({$vehicle->vehicle_type})",
            ]);

            // Credit Cash/Bank/Equity
            JournalEntryItem::create([
                'journal_entry_id' => $journal->id,
                'chart_of_account_id' => $creditAcc->id,
                'debit_amount' => 0,
                'credit_amount' => $vehicle->asset_value,
                'memo' => "Sumber Dana Perolehan Aset Kendaraan {$vehicle->plate_number}",
            ]);

            $vehicle->update(['journal_entry_id' => $journal->id]);
        }
    }

    private function updateAssetJournalEntry(Vehicle $vehicle, $newAssetValue, $fundingAccountId = null)
    {
        $journal = JournalEntry::find($vehicle->journal_entry_id);
        if ($journal) {
            $journal->update([
                'total_debit' => $newAssetValue,
                'total_credit' => $newAssetValue,
            ]);

            $debitItem = $journal->items()->where('debit_amount', '>', 0)->first();
            if ($debitItem) {
                $debitItem->update(['debit_amount' => $newAssetValue]);
            }

            $creditItem = $journal->items()->where('credit_amount', '>', 0)->first();
            if ($creditItem) {
                $updateData = ['credit_amount' => $newAssetValue];
                if ($fundingAccountId) {
                    $updateData['chart_of_account_id'] = $fundingAccountId;
                }
                $creditItem->update($updateData);
            }
        }
    }
}
