<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchHubController;
use App\Http\Controllers\CourierAssignmentController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\EpodController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\TariffController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

// Public Routes (Bebas Akses Tanpa Login)
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/track', [TrackingController::class, 'index'])->name('tracking.index');
Route::match(['get', 'post'], '/tariffs/calculate', [TariffController::class, 'calculate'])->name('tariffs.calculate');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ePOD Mobile Portal for Delivery Boy (Accessible via QR Code Scan on AWB)
Route::get('/epod/{tracking_number}', [EpodController::class, 'show'])->name('epod.show');
Route::post('/epod/{tracking_number}', [EpodController::class, 'store'])->name('epod.store');
Route::post('/epod/{tracking_number}/transit', [EpodController::class, 'storeTransit'])->name('epod.store-transit');

// Protected Internal Management Routes
Route::middleware(['auth'])->group(function () {
    // Executive Dashboard (Accessible to all authenticated users based on role view)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware(['permission'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Customer Management
    Route::resource('customers', CustomerController::class);

    // Invoices Management (Penagihan Biaya Pengiriman)
    Route::get('/invoices/{id}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    Route::post('/invoices/{id}/payment', [InvoiceController::class, 'updateStatus'])->name('invoices.payment');
    Route::resource('invoices', InvoiceController::class);

    // Delivery Orders (DO Customer / Surat Jalan)
    Route::get('/delivery-orders/{id}/print', [DeliveryOrderController::class, 'print'])->name('delivery-orders.print');
    Route::get('/delivery-orders/template-csv', [DeliveryOrderController::class, 'downloadTemplate'])->name('delivery-orders.template-csv');
    Route::post('/delivery-orders/import-csv', [DeliveryOrderController::class, 'importRecipientsCsv'])->name('delivery-orders.import-csv');
    Route::post('/delivery-orders/{id}/generate-shipments', [DeliveryOrderController::class, 'generateShipments'])->name('delivery-orders.generate-shipments');
    Route::resource('delivery-orders', DeliveryOrderController::class);

    // Menu Kasir / POS Resi
    Route::get('/pos', [\App\Http\Controllers\PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/store-customer', [\App\Http\Controllers\PosController::class, 'storeCustomer'])->name('pos.store-customer');
    Route::post('/pos/store-shipment', [\App\Http\Controllers\PosController::class, 'storeShipment'])->name('pos.store-shipment');
    Route::get('/pos/{id}/print-receipt', [\App\Http\Controllers\PosController::class, 'printReceipt'])->name('pos.print-receipt');

    // Resi / Shipments (AWB)
    Route::get('/shipments/{id}/print-label', [ShipmentController::class, 'printLabel'])->name('shipments.print-label');
    Route::post('/shipments/{id}/status', [ShipmentController::class, 'updateStatus'])->name('shipments.update-status');
    Route::post('/shipments/{id}/complete-task', [ShipmentController::class, 'completeTask'])->name('shipments.complete-task');
    Route::resource('shipments', ShipmentController::class);

    // Penugasan Kurir & Manifes
    Route::get('/courier-assignments/{id}/manifest', [CourierAssignmentController::class, 'manifest'])->name('courier-assignments.manifest');
    Route::post('/courier-assignments/{id}/complete', [CourierAssignmentController::class, 'complete'])->name('courier-assignments.complete');
    Route::resource('courier-assignments', CourierAssignmentController::class);

    // Penggajian & Komisi Kurir
    Route::get('/courier-payrolls/{id}/print-slip', [\App\Http\Controllers\CourierPayrollController::class, 'printSlip'])->name('courier-payrolls.print-slip');
    Route::post('/courier-payrolls/generate', [\App\Http\Controllers\CourierPayrollController::class, 'generate'])->name('courier-payrolls.generate');
    Route::resource('courier-payrolls', \App\Http\Controllers\CourierPayrollController::class)->only(['index', 'show']);

    // Kasbon Kurir (Cash Advance)
    Route::get('/courier-cash-advances/{id}/print', [\App\Http\Controllers\CourierCashAdvanceController::class, 'printVoucher'])->name('courier-cash-advances.print');
    Route::post('/courier-cash-advances/{id}/approve', [\App\Http\Controllers\CourierCashAdvanceController::class, 'approve'])->name('courier-cash-advances.approve');
    Route::post('/courier-cash-advances/{id}/reject', [\App\Http\Controllers\CourierCashAdvanceController::class, 'reject'])->name('courier-cash-advances.reject');
    Route::resource('courier-cash-advances', \App\Http\Controllers\CourierCashAdvanceController::class)->except(['create', 'show', 'edit']);

    // Pembelian Barang & Master Vendor
    Route::resource('vendors', \App\Http\Controllers\VendorController::class)->except(['create', 'show', 'edit']);
    Route::get('/purchases/{id}/print', [\App\Http\Controllers\PurchaseController::class, 'print'])->name('purchases.print');
    Route::post('/purchases/{id}/status', [\App\Http\Controllers\PurchaseController::class, 'updateStatus'])->name('purchases.update-status');
    Route::resource('purchases', \App\Http\Controllers\PurchaseController::class);

    // Modul Akuntansi & Keuangan
    Route::get('/accounting/coa', [\App\Http\Controllers\AccountingController::class, 'coaIndex'])->name('accounting.coa.index');
    Route::post('/accounting/coa', [\App\Http\Controllers\AccountingController::class, 'coaStore'])->name('accounting.coa.store');
    Route::put('/accounting/coa/{id}', [\App\Http\Controllers\AccountingController::class, 'coaUpdate'])->name('accounting.coa.update');
    Route::delete('/accounting/coa/{id}', [\App\Http\Controllers\AccountingController::class, 'coaDestroy'])->name('accounting.coa.destroy');

    Route::get('/accounting/initial-balances', [\App\Http\Controllers\AccountingController::class, 'initialBalancesIndex'])->name('accounting.initial-balances.index');
    Route::post('/accounting/initial-balances', [\App\Http\Controllers\AccountingController::class, 'initialBalancesStore'])->name('accounting.initial-balances.store');

    Route::get('/accounting/journals', [\App\Http\Controllers\AccountingController::class, 'journalsIndex'])->name('accounting.journals.index');
    Route::get('/accounting/journals/create', [\App\Http\Controllers\AccountingController::class, 'journalsCreate'])->name('accounting.journals.create');
    Route::post('/accounting/journals', [\App\Http\Controllers\AccountingController::class, 'journalsStore'])->name('accounting.journals.store');
    Route::get('/accounting/journals/{id}', [\App\Http\Controllers\AccountingController::class, 'journalsShow'])->name('accounting.journals.show');

    Route::get('/accounting/ledger', [\App\Http\Controllers\AccountingController::class, 'ledgerIndex'])->name('accounting.ledger.index');
    Route::get('/accounting/reports/profit-loss', [\App\Http\Controllers\AccountingController::class, 'profitLossIndex'])->name('accounting.profit-loss.index');
    Route::get('/accounting/reports/balance-sheet', [\App\Http\Controllers\AccountingController::class, 'balanceSheetIndex'])->name('accounting.balance-sheet.index');

    Route::get('/accounting/closing', [\App\Http\Controllers\AccountingController::class, 'closingIndex'])->name('accounting.closing.index');
    Route::post('/accounting/closing', [\App\Http\Controllers\AccountingController::class, 'closingStore'])->name('accounting.closing.store');

    // Master Data: Hub, Armada, Kurir, Tarif, User Management, Roles & Menus
    Route::resource('branch-hubs', BranchHubController::class)->except(['create', 'show', 'edit']);
    Route::resource('vehicles', VehicleController::class)->except(['create', 'show', 'edit']);
    Route::resource('couriers', CourierController::class)->except(['create', 'show', 'edit']);
    Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
    
    // Role & Permission Management
    Route::get('/roles/{id}/permissions', [\App\Http\Controllers\RoleController::class, 'permissions'])->name('roles.permissions');
    Route::post('/roles/{id}/permissions', [\App\Http\Controllers\RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
    Route::resource('roles', \App\Http\Controllers\RoleController::class)->except(['create', 'show', 'edit']);

    // Menu Management
    Route::resource('menus', \App\Http\Controllers\MenuController::class)->except(['create', 'show', 'edit']);

    Route::resource('tariffs', TariffController::class)->except(['create', 'show', 'edit']);
    Route::put('/tariffs/{id}', [TariffController::class, 'update'])->name('tariffs.update');

    // Master Wilayah & Kode Pos Management
    Route::get('/regions', [\App\Http\Controllers\RegionController::class, 'index'])->name('regions.index');
    Route::get('/regions/export', [\App\Http\Controllers\RegionController::class, 'exportCsv'])->name('regions.export');
    Route::get('/regions/template-csv', [\App\Http\Controllers\RegionController::class, 'templateCsv'])->name('regions.template-csv');
    Route::post('/regions/import', [\App\Http\Controllers\RegionController::class, 'importCsv'])->name('regions.import');
    
    // Store, Update, Destroy Region Entities
    Route::post('/regions/province', [\App\Http\Controllers\RegionController::class, 'storeProvince'])->name('regions.province.store');
    Route::put('/regions/province/{id}', [\App\Http\Controllers\RegionController::class, 'updateProvince'])->name('regions.province.update');
    Route::delete('/regions/province/{id}', [\App\Http\Controllers\RegionController::class, 'destroyProvince'])->name('regions.province.destroy');

    Route::post('/regions/city', [\App\Http\Controllers\RegionController::class, 'storeCity'])->name('regions.city.store');
    Route::put('/regions/city/{id}', [\App\Http\Controllers\RegionController::class, 'updateCity'])->name('regions.city.update');
    Route::delete('/regions/city/{id}', [\App\Http\Controllers\RegionController::class, 'destroyCity'])->name('regions.city.destroy');

    Route::post('/regions/district', [\App\Http\Controllers\RegionController::class, 'storeDistrict'])->name('regions.district.store');
    Route::put('/regions/district/{id}', [\App\Http\Controllers\RegionController::class, 'updateDistrict'])->name('regions.district.update');
    Route::delete('/regions/district/{id}', [\App\Http\Controllers\RegionController::class, 'destroyDistrict'])->name('regions.district.destroy');

    Route::post('/regions/subdistrict', [\App\Http\Controllers\RegionController::class, 'storeSubdistrict'])->name('regions.subdistrict.store');
    Route::put('/regions/subdistrict/{id}', [\App\Http\Controllers\RegionController::class, 'updateSubdistrict'])->name('regions.subdistrict.update');
    Route::delete('/regions/subdistrict/{id}', [\App\Http\Controllers\RegionController::class, 'destroySubdistrict'])->name('regions.subdistrict.destroy');

    // JSON API Endpoints for Dynamic Regional Dropdowns
    Route::get('/api/provinces', [\App\Http\Controllers\RegionController::class, 'getProvinces'])->name('api.provinces');
    Route::get('/api/cities/{province_id}', [\App\Http\Controllers\RegionController::class, 'getCities'])->name('api.cities');
    Route::get('/api/districts/{city_id}', [\App\Http\Controllers\RegionController::class, 'getDistricts'])->name('api.districts');
    Route::get('/api/subdistricts/{district_id}', [\App\Http\Controllers\RegionController::class, 'getSubdistricts'])->name('api.subdistricts');

    // Web Settings (White-Label Branding, Logo & Theme)
    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
    });
});
