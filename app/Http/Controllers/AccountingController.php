<?php

namespace App\Http\Controllers;

use App\Models\AccountingInitialBalance;
use App\Models\ChartOfAccount;
use App\Models\FiscalYearClosing;
use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    // ==========================================
    // 1. MASTER CHART OF ACCOUNTS (COA)
    // ==========================================
    public function coaIndex()
    {
        $accounts = ChartOfAccount::with('parent')
            ->orderBy('account_code')
            ->get();

        return view('accounting.coa.index', compact('accounts'));
    }

    public function coaStore(Request $request)
    {
        $validated = $request->validate([
            'account_code' => 'required|string|max:50|unique:chart_of_accounts,account_code',
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
            'normal_balance' => 'required|in:Debit,Credit',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
        ]);

        ChartOfAccount::create($validated);

        return redirect()->route('accounting.coa.index')->with('success', 'Akun COA baru berhasil ditambahkan.');
    }

    public function coaUpdate(Request $request, $id)
    {
        $account = ChartOfAccount::findOrFail($id);

        $validated = $request->validate([
            'account_code' => 'required|string|max:50|unique:chart_of_accounts,account_code,' . $id,
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
            'normal_balance' => 'required|in:Debit,Credit',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $account->update($validated);

        return redirect()->route('accounting.coa.index')->with('success', 'Akun COA berhasil diperbarui.');
    }

    public function coaDestroy($id)
    {
        $account = ChartOfAccount::findOrFail($id);
        
        if ($account::has('journalItems')->where('id', $id)->exists()) {
            return redirect()->back()->with('error', 'Gagal menghapus: Akun ini sudah memiliki riwayat transaksi jurnal.');
        }

        $account->delete();
        return redirect()->route('accounting.coa.index')->with('success', 'Akun COA berhasil dihapus.');
    }

    // ==========================================
    // 2. SALDO AWAL AKUN (INITIAL BALANCES)
    // ==========================================
    public function initialBalancesIndex(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        
        $accounts = ChartOfAccount::where('is_active', true)
            ->orderBy('account_code')
            ->get();

        $savedBalances = AccountingInitialBalance::where('fiscal_year', $year)
            ->get()
            ->keyBy('chart_of_account_id');

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $acc) {
            $bal = $savedBalances->get($acc->id);
            $totalDebit += $bal ? $bal->debit_amount : 0;
            $totalCredit += $bal ? $bal->credit_amount : 0;
        }

        return view('accounting.initial_balances.index', compact('accounts', 'savedBalances', 'year', 'totalDebit', 'totalCredit'));
    }

    public function initialBalancesStore(Request $request)
    {
        $year = (int) $request->input('fiscal_year', date('Y'));
        $balances = $request->input('balances', []);

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($balances as $accId => $val) {
            $deb = (float) ($val['debit'] ?? 0);
            $cred = (float) ($val['credit'] ?? 0);

            $totalDebit += $deb;
            $totalCredit += $cred;

            if ($deb > 0 || $cred > 0) {
                AccountingInitialBalance::updateOrCreate(
                    [
                        'chart_of_account_id' => $accId,
                        'fiscal_year' => $year,
                    ],
                    [
                        'debit_amount' => $deb,
                        'credit_amount' => $cred,
                    ]
                );
            } else {
                AccountingInitialBalance::where('chart_of_account_id', $accId)
                    ->where('fiscal_year', $year)
                    ->delete();
            }
        }

        $isBalanced = abs($totalDebit - $totalCredit) < 0.01;
        $msg = "Saldo awal tahun buku {$year} berhasil disimpan.";
        if (!$isBalanced) {
            $msg .= " Peringatan: Total Debit (Rp " . number_format($totalDebit, 0, ',', '.') . ") & Kredit (Rp " . number_format($totalCredit, 0, ',', '.') . ") belum seimbang (selisih Rp " . number_format(abs($totalDebit - $totalCredit), 0, ',', '.') . ").";
        }

        return redirect()->route('accounting.initial-balances.index', ['year' => $year])->with('success', $msg);
    }

    // ==========================================
    // 3. PENJURNALAN (GENERAL JOURNAL)
    // ==========================================
    public function journalsIndex(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-t'));

        $query = JournalEntry::with(['items.account', 'createdUser'])
            ->whereBetween('entry_date', [$startDate, $endDate]);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('journal_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $journals = $query->latest('entry_date')->latest('id')->paginate(20);

        return view('accounting.journals.index', compact('journals', 'startDate', 'endDate'));
    }

    public function journalsCreate()
    {
        $accounts = ChartOfAccount::where('is_active', true)
            ->orderBy('account_code')
            ->get();

        return view('accounting.journals.create', compact('accounts'));
    }

    public function journalsStore(Request $request)
    {
        $request->validate([
            'entry_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'description' => 'required|string',
            'items' => 'required|array|min:2',
            'items.*.chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.debit_amount' => 'nullable|numeric|min:0',
            'items.*.credit_amount' => 'nullable|numeric|min:0',
            'items.*.memo' => 'nullable|string',
        ]);

        $items = $request->input('items');
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($items as $item) {
            $totalDebit += (float) ($item['debit_amount'] ?? 0);
            $totalCredit += (float) ($item['credit_amount'] ?? 0);
        }

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return redirect()->back()->withInput()->with('error', 'Penjurnalan gagal: Total Debit (Rp ' . number_format($totalDebit, 0, ',', '.') . ') dan Kredit (Rp ' . number_format($totalCredit, 0, ',', '.') . ') harus seimbang (Balance).');
        }

        if ($totalDebit <= 0) {
            return redirect()->back()->withInput()->with('error', 'Penjurnalan gagal: Nominal transaksi harus lebih dari 0.');
        }

        $journalNumber = 'JRN-' . date('Ym', strtotime($request->input('entry_date'))) . '-' . str_pad(JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($request, $journalNumber, $totalDebit, $totalCredit, $items) {
            $journal = JournalEntry::create([
                'journal_number' => $journalNumber,
                'entry_date' => $request->input('entry_date'),
                'reference_number' => $request->input('reference_number'),
                'description' => $request->input('description'),
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'created_by_user_id' => Auth::id(),
            ]);

            foreach ($items as $item) {
                $deb = (float) ($item['debit_amount'] ?? 0);
                $cred = (float) ($item['credit_amount'] ?? 0);

                if ($deb > 0 || $cred > 0) {
                    JournalEntryItem::create([
                        'journal_entry_id' => $journal->id,
                        'chart_of_account_id' => $item['chart_of_account_id'],
                        'debit_amount' => $deb,
                        'credit_amount' => $cred,
                        'memo' => $item['memo'] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('accounting.journals.index')->with('success', "Transaksi Jurnal Umum {$journalNumber} berhasil disimpan.");
    }

    public function journalsShow($id)
    {
        $journal = JournalEntry::with(['items.account', 'createdUser'])->findOrFail($id);
        return view('accounting.journals.show', compact('journal'));
    }

    // ==========================================
    // 4. BUKU BESAR (GENERAL LEDGER)
    // ==========================================
    public function ledgerIndex(Request $request)
    {
        $accountId = $request->input('chart_of_account_id');
        $startDate = $request->input('start_date', date('Y-01-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        $accounts = ChartOfAccount::where('is_active', true)->orderBy('account_code')->get();
        $selectedAccount = $accountId ? ChartOfAccount::find($accountId) : $accounts->first();

        $ledgerEntries = collect();
        $openingBalance = 0;

        if ($selectedAccount) {
            $year = (int) date('Y', strtotime($startDate));

            // Initial balance for this fiscal year
            $initBal = AccountingInitialBalance::where('chart_of_account_id', $selectedAccount->id)
                ->where('fiscal_year', $year)
                ->first();

            $initVal = 0;
            if ($initBal) {
                $initVal = ($selectedAccount->normal_balance === 'Debit')
                    ? ($initBal->debit_amount - $initBal->credit_amount)
                    : ($initBal->credit_amount - $initBal->debit_amount);
            }

            // Prior transactions in current fiscal year before start_date
            $priorTransactions = JournalEntryItem::where('chart_of_account_id', $selectedAccount->id)
                ->whereHas('journalEntry', function ($q) use ($year, $startDate) {
                    $q->whereYear('entry_date', $year)
                      ->where('entry_date', '<', $startDate);
                })
                ->get();

            $priorNet = 0;
            foreach ($priorTransactions as $pt) {
                if ($selectedAccount->normal_balance === 'Debit') {
                    $priorNet += ($pt->debit_amount - $pt->credit_amount);
                } else {
                    $priorNet += ($pt->credit_amount - $pt->debit_amount);
                }
            }

            $openingBalance = $initVal + $priorNet;

            // Current date range transactions
            $ledgerEntries = JournalEntryItem::with('journalEntry')
                ->where('chart_of_account_id', $selectedAccount->id)
                ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('entry_date', [$startDate, $endDate]);
                })
                ->get()
                ->sortBy(function ($item) {
                    return $item->journalEntry->entry_date->format('Y-m-d') . '_' . $item->journalEntry->id;
                });
        }

        return view('accounting.ledger.index', compact('accounts', 'selectedAccount', 'ledgerEntries', 'openingBalance', 'startDate', 'endDate'));
    }

    // ==========================================
    // 5. LAPORAN LABA RUGI (PROFIT & LOSS)
    // ==========================================
    public function profitLossIndex(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-01-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        $revenueAccounts = ChartOfAccount::where('account_type', 'Revenue')->get();
        $expenseAccounts = ChartOfAccount::where('account_type', 'Expense')->get();

        $revenueData = [];
        $totalRevenue = 0;

        foreach ($revenueAccounts as $acc) {
            $sumCredit = JournalEntryItem::where('chart_of_account_id', $acc->id)
                ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('entry_date', [$startDate, $endDate]);
                })->sum('credit_amount');

            $sumDebit = JournalEntryItem::where('chart_of_account_id', $acc->id)
                ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('entry_date', [$startDate, $endDate]);
                })->sum('debit_amount');

            $net = $sumCredit - $sumDebit;
            if ($net != 0) {
                $revenueData[] = ['account' => $acc, 'amount' => $net];
                $totalRevenue += $net;
            }
        }

        $expenseData = [];
        $totalExpense = 0;

        foreach ($expenseAccounts as $acc) {
            $sumDebit = JournalEntryItem::where('chart_of_account_id', $acc->id)
                ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('entry_date', [$startDate, $endDate]);
                })->sum('debit_amount');

            $sumCredit = JournalEntryItem::where('chart_of_account_id', $acc->id)
                ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('entry_date', [$startDate, $endDate]);
                })->sum('credit_amount');

            $net = $sumDebit - $sumCredit;
            if ($net != 0) {
                $expenseData[] = ['account' => $acc, 'amount' => $net];
                $totalExpense += $net;
            }
        }

        $netProfitLoss = $totalRevenue - $totalExpense;

        return view('accounting.reports.profit_loss', compact('revenueData', 'expenseData', 'totalRevenue', 'totalExpense', 'netProfitLoss', 'startDate', 'endDate'));
    }

    // ==========================================
    // 6. LAPORAN NERACA (BALANCE SHEET)
    // ==========================================
    public function balanceSheetIndex(Request $request)
    {
        $asOfDate = $request->input('as_of_date', date('Y-m-d'));
        $year = (int) date('Y', strtotime($asOfDate));

        $assetAccounts = ChartOfAccount::where('account_type', 'Asset')->get();
        $liabilityAccounts = ChartOfAccount::where('account_type', 'Liability')->get();
        $equityAccounts = ChartOfAccount::where('account_type', 'Equity')->get();

        $assetData = [];
        $totalAssets = 0;

        foreach ($assetAccounts as $acc) {
            $initBal = AccountingInitialBalance::where('chart_of_account_id', $acc->id)->where('fiscal_year', $year)->first();
            $initVal = $initBal ? ($initBal->debit_amount - $initBal->credit_amount) : 0;

            $trans = JournalEntryItem::where('chart_of_account_id', $acc->id)
                ->whereHas('journalEntry', function ($q) use ($asOfDate) {
                    $q->where('entry_date', '<=', $asOfDate);
                })->get();

            $transNet = 0;
            foreach ($trans as $t) {
                $transNet += ($t->debit_amount - $t->credit_amount);
            }

            $bal = $initVal + $transNet;
            if ($bal != 0) {
                $assetData[] = ['account' => $acc, 'balance' => $bal];
                $totalAssets += $bal;
            }
        }

        $liabilityData = [];
        $totalLiabilities = 0;

        foreach ($liabilityAccounts as $acc) {
            $initBal = AccountingInitialBalance::where('chart_of_account_id', $acc->id)->where('fiscal_year', $year)->first();
            $initVal = $initBal ? ($initBal->credit_amount - $initBal->debit_amount) : 0;

            $trans = JournalEntryItem::where('chart_of_account_id', $acc->id)
                ->whereHas('journalEntry', function ($q) use ($asOfDate) {
                    $q->where('entry_date', '<=', $asOfDate);
                })->get();

            $transNet = 0;
            foreach ($trans as $t) {
                $transNet += ($t->credit_amount - $t->debit_amount);
            }

            $bal = $initVal + $transNet;
            if ($bal != 0) {
                $liabilityData[] = ['account' => $acc, 'balance' => $bal];
                $totalLiabilities += $bal;
            }
        }

        $equityData = [];
        $totalEquity = 0;

        foreach ($equityAccounts as $acc) {
            $initBal = AccountingInitialBalance::where('chart_of_account_id', $acc->id)->where('fiscal_year', $year)->first();
            $initVal = $initBal ? ($initBal->credit_amount - $initBal->debit_amount) : 0;

            $trans = JournalEntryItem::where('chart_of_account_id', $acc->id)
                ->whereHas('journalEntry', function ($q) use ($asOfDate) {
                    $q->where('entry_date', '<=', $asOfDate);
                })->get();

            $transNet = 0;
            foreach ($trans as $t) {
                $transNet += ($t->credit_amount - $t->debit_amount);
            }

            $bal = $initVal + $transNet;
            if ($bal != 0) {
                $equityData[] = ['account' => $acc, 'balance' => $bal];
                $totalEquity += $bal;
            }
        }

        // Calculate Net Income for current year up to asOfDate
        $revTotal = JournalEntryItem::whereHas('account', function ($q) { $q->where('account_type', 'Revenue'); })
            ->whereHas('journalEntry', function ($q) use ($asOfDate) { $q->where('entry_date', '<=', $asOfDate); })
            ->sum('credit_amount') - JournalEntryItem::whereHas('account', function ($q) { $q->where('account_type', 'Revenue'); })
            ->whereHas('journalEntry', function ($q) use ($asOfDate) { $q->where('entry_date', '<=', $asOfDate); })
            ->sum('debit_amount');

        $expTotal = JournalEntryItem::whereHas('account', function ($q) { $q->where('account_type', 'Expense'); })
            ->whereHas('journalEntry', function ($q) use ($asOfDate) { $q->where('entry_date', '<=', $asOfDate); })
            ->sum('debit_amount') - JournalEntryItem::whereHas('account', function ($q) { $q->where('account_type', 'Expense'); })
            ->whereHas('journalEntry', function ($q) use ($asOfDate) { $q->where('entry_date', '<=', $asOfDate); })
            ->sum('credit_amount');

        $currentYearNetIncome = $revTotal - $expTotal;
        $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity + $currentYearNetIncome;

        return view('accounting.reports.balance_sheet', compact(
            'assetData', 'liabilityData', 'equityData',
            'totalAssets', 'totalLiabilities', 'totalEquity',
            'currentYearNetIncome', 'totalLiabilitiesAndEquity', 'asOfDate'
        ));
    }

    // ==========================================
    // 7. TUTUP BUKU AKHIR TAHUN (YEAR-END CLOSING)
    // ==========================================
    public function closingIndex(Request $request)
    {
        $year = (int) $request->input('year', date('Y') - 1);
        $closedHistory = FiscalYearClosing::with(['closingJournalEntry', 'closedUser'])
            ->orderBy('fiscal_year', 'desc')
            ->get();

        $isClosed = FiscalYearClosing::where('fiscal_year', $year)->exists();

        // Calculate P&L for selected fiscal year
        $startDate = "{$year}-01-01";
        $endDate = "{$year}-12-31";

        $totalRevenue = JournalEntryItem::whereHas('account', function ($q) { $q->where('account_type', 'Revenue'); })
            ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) { $q->whereBetween('entry_date', [$startDate, $endDate]); })
            ->sum('credit_amount') - JournalEntryItem::whereHas('account', function ($q) { $q->where('account_type', 'Revenue'); })
            ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) { $q->whereBetween('entry_date', [$startDate, $endDate]); })
            ->sum('debit_amount');

        $totalExpense = JournalEntryItem::whereHas('account', function ($q) { $q->where('account_type', 'Expense'); })
            ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) { $q->whereBetween('entry_date', [$startDate, $endDate]); })
            ->sum('debit_amount') - JournalEntryItem::whereHas('account', function ($q) { $q->where('account_type', 'Expense'); })
            ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) { $q->whereBetween('entry_date', [$startDate, $endDate]); })
            ->sum('credit_amount');

        $netProfitLoss = $totalRevenue - $totalExpense;

        $retainedAccount = ChartOfAccount::where('account_code', '3201')->first() ?: ChartOfAccount::where('account_type', 'Equity')->first();

        return view('accounting.closing.index', compact(
            'year', 'closedHistory', 'isClosed', 'totalRevenue', 'totalExpense', 'netProfitLoss', 'retainedAccount'
        ));
    }

    public function closingStore(Request $request)
    {
        $year = (int) $request->input('fiscal_year');

        if (FiscalYearClosing::where('fiscal_year', $year)->exists()) {
            return redirect()->back()->with('error', "Proses tutup buku untuk tahun {$year} sudah pernah dilakukan sebelumnya.");
        }

        $startDate = "{$year}-01-01";
        $endDate = "{$year}-12-31";

        $retainedAccount = ChartOfAccount::where('account_code', '3201')->first() ?: ChartOfAccount::where('account_type', 'Equity')->firstOrFail();

        DB::transaction(function () use ($year, $startDate, $endDate, $retainedAccount, $request) {
            $revenueAccounts = ChartOfAccount::where('account_type', 'Revenue')->get();
            $expenseAccounts = ChartOfAccount::where('account_type', 'Expense')->get();

            $totalRevenue = 0;
            $totalExpense = 0;
            $journalItems = [];

            // 1. Close Revenue Accounts (Debit Revenue, Credit Total)
            foreach ($revenueAccounts as $acc) {
                $net = JournalEntryItem::where('chart_of_account_id', $acc->id)
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) { $q->whereBetween('entry_date', [$startDate, $endDate]); })
                    ->sum('credit_amount') - JournalEntryItem::where('chart_of_account_id', $acc->id)
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) { $q->whereBetween('entry_date', [$startDate, $endDate]); })
                    ->sum('debit_amount');

                if ($net > 0) {
                    $journalItems[] = ['account_id' => $acc->id, 'debit' => $net, 'credit' => 0, 'memo' => "Penutupan Akun Pendapatan {$acc->account_name} Tahun {$year}"];
                    $totalRevenue += $net;
                }
            }

            // 2. Close Expense Accounts (Credit Expense, Debit Total)
            foreach ($expenseAccounts as $acc) {
                $net = JournalEntryItem::where('chart_of_account_id', $acc->id)
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) { $q->whereBetween('entry_date', [$startDate, $endDate]); })
                    ->sum('debit_amount') - JournalEntryItem::where('chart_of_account_id', $acc->id)
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) { $q->whereBetween('entry_date', [$startDate, $endDate]); })
                    ->sum('credit_amount');

                if ($net > 0) {
                    $journalItems[] = ['account_id' => $acc->id, 'debit' => 0, 'credit' => $net, 'memo' => "Penutupan Akun Beban {$acc->account_name} Tahun {$year}"];
                    $totalExpense += $net;
                }
            }

            $netProfitLoss = $totalRevenue - $totalExpense;

            // 3. Transfer Net Profit/Loss to Retained Earnings
            if ($netProfitLoss > 0) {
                $journalItems[] = ['account_id' => $retainedAccount->id, 'debit' => 0, 'credit' => $netProfitLoss, 'memo' => "Transfer Laba Bersih Tahun {$year} ke Laba Ditahan"];
            } elseif ($netProfitLoss < 0) {
                $journalItems[] = ['account_id' => $retainedAccount->id, 'debit' => abs($netProfitLoss), 'credit' => 0, 'memo' => "Transfer Rugi Bersih Tahun {$year} ke Laba Ditahan"];
            }

            $journalNum = "CLS-{$year}-3112";
            $journal = JournalEntry::create([
                'journal_number' => $journalNum,
                'entry_date' => $endDate,
                'reference_number' => "TUTUP-BUKU-{$year}",
                'description' => "Jurnal Penutup Akhir Tahun Buku {$year}",
                'total_debit' => max($totalRevenue, $totalExpense),
                'total_credit' => max($totalRevenue, $totalExpense),
                'is_closing_entry' => true,
                'created_by_user_id' => Auth::id(),
            ]);

            foreach ($journalItems as $ji) {
                JournalEntryItem::create([
                    'journal_entry_id' => $journal->id,
                    'chart_of_account_id' => $ji['account_id'],
                    'debit_amount' => $ji['debit'],
                    'credit_amount' => $ji['credit'],
                    'memo' => $ji['memo'],
                ]);
            }

            // 4. Record Fiscal Year Closing Log
            FiscalYearClosing::create([
                'fiscal_year' => $year,
                'closed_at' => now(),
                'net_profit_loss' => $netProfitLoss,
                'closing_journal_entry_id' => $journal->id,
                'closed_by_user_id' => Auth::id(),
                'notes' => $request->input('notes'),
            ]);

            // 5. Automatically Carry Forward Real Account Ending Balances as Initial Balances for Next Year (Year + 1)
            $nextYear = $year + 1;
            $realAccounts = ChartOfAccount::whereIn('account_type', ['Asset', 'Liability', 'Equity'])->get();

            foreach ($realAccounts as $acc) {
                $initBal = AccountingInitialBalance::where('chart_of_account_id', $acc->id)->where('fiscal_year', $year)->first();
                $initDebit = $initBal ? $initBal->debit_amount : 0;
                $initCredit = $initBal ? $initBal->credit_amount : 0;

                $transDebit = JournalEntryItem::where('chart_of_account_id', $acc->id)
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) { $q->whereBetween('entry_date', [$startDate, $endDate]); })
                    ->sum('debit_amount');

                $transCredit = JournalEntryItem::where('chart_of_account_id', $acc->id)
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) { $q->whereBetween('entry_date', [$startDate, $endDate]); })
                    ->sum('credit_amount');

                $totalDebitAcc = $initDebit + $transDebit;
                $totalCreditAcc = $initCredit + $transCredit;

                if ($acc->normal_balance === 'Debit') {
                    $netEnding = $totalDebitAcc - $totalCreditAcc;
                    $nextDebit = $netEnding > 0 ? $netEnding : 0;
                    $nextCredit = $netEnding < 0 ? abs($netEnding) : 0;
                } else {
                    $netEnding = $totalCreditAcc - $totalDebitAcc;
                    $nextCredit = $netEnding > 0 ? $netEnding : 0;
                    $nextDebit = $netEnding < 0 ? abs($netEnding) : 0;
                }

                if ($nextDebit > 0 || $nextCredit > 0) {
                    AccountingInitialBalance::updateOrCreate(
                        ['chart_of_account_id' => $acc->id, 'fiscal_year' => $nextYear],
                        ['debit_amount' => $nextDebit, 'credit_amount' => $nextCredit]
                    );
                }
            }
        });

        return redirect()->route('accounting.closing.index')->with('success', "Proses Tutup Buku Akhir Tahun {$year} telah BERHASIL dilaksanakan. Jurnal penutup diterbitkan & Saldo awal tahun {$year} + 1 diaktifkan.");
    }
}
