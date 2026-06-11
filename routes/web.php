<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProductImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StockTakeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\ProductReturnController;
use App\Http\Controllers\BankDepositController;
use App\Http\Controllers\FinancialYearController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SupplierPaymentController;

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/',         [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard',[DashboardController::class, 'index']);

    // Branches
    Route::resource('branches', BranchController::class);
    Route::patch('branches/{id}/restore',      [BranchController::class, 'restore'])->name('branches.restore');
    Route::delete('branches/{id}/force-delete',[BranchController::class, 'forceDelete'])->name('branches.force-delete');

    // Users
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::patch('users/{id}/restore',         [UserController::class, 'restore'])->name('users.restore');
    Route::delete('users/{id}/force-delete',   [UserController::class, 'forceDelete'])->name('users.force-delete');

    // Categories
    Route::resource('categories', ProductCategoryController::class);
    Route::patch('categories/{id}/restore',       [ProductCategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('categories/{id}/force-delete', [ProductCategoryController::class, 'forceDelete'])->name('categories.force-delete');

    // Products
    Route::resource('products', ProductController::class);
    Route::patch('products/{id}/restore',       [ProductController::class, 'restore'])->name('products.restore');
    Route::delete('products/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('products.force-delete');

    // Inventory
    Route::get('inventory',                    [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('inventory/movements',          [InventoryController::class, 'movements'])->name('inventory.movements');
    Route::get('inventory/{product}/adjust',   [InventoryController::class, 'adjust'])->name('inventory.adjust');
    Route::post('inventory/{product}/adjust',  [InventoryController::class, 'storeAdjustment'])->name('inventory.store-adjustment');

    // POS
    Route::get('pos',              [PosController::class, 'index'])->name('pos.index');
    Route::post('pos/process',     [PosController::class, 'process'])->name('pos.process');
    Route::get('pos/receipt/{sale}',[PosController::class, 'receipt'])->name('pos.receipt');

    // Sales
    Route::get('sales',              [SaleController::class, 'index'])->name('sales.index');
    Route::get('sales/{sale}',       [SaleController::class, 'show'])->name('sales.show');
    Route::patch('sales/{sale}/cancel',[SaleController::class, 'cancel'])->name('sales.cancel');
    Route::patch('sales/{id}/restore', [SaleController::class, 'restore'])->name('sales.restore');

    // Transfers
    Route::resource('transfers', StockTransferController::class);
    Route::patch('transfers/{transfer}/approve',  [StockTransferController::class, 'approve'])->name('transfers.approve');
    Route::patch('transfers/{transfer}/dispatch', [StockTransferController::class, 'dispatch'])->name('transfers.dispatch');
    Route::patch('transfers/{transfer}/receive',  [StockTransferController::class, 'receive'])->name('transfers.receive');
    Route::patch('transfers/{transfer}/reject',   [StockTransferController::class, 'reject'])->name('transfers.reject');
    Route::patch('transfers/{id}/restore',        [StockTransferController::class, 'restore'])->name('transfers.restore');

    // Returns
    Route::resource('returns', ProductReturnController::class);
    Route::patch('returns/{return}/approve', [ProductReturnController::class, 'approve'])->name('returns.approve');
    Route::patch('returns/{return}/reject',  [ProductReturnController::class, 'reject'])->name('returns.reject');
    Route::patch('returns/{id}/restore',     [ProductReturnController::class, 'restore'])->name('returns.restore');

    // Deposits
    Route::resource('deposits', BankDepositController::class);
    Route::patch('deposits/{deposit}/verify',     [BankDepositController::class, 'verify'])->name('deposits.verify');
    Route::patch('deposits/{deposit}/reject',     [BankDepositController::class, 'reject'])->name('deposits.reject');
    Route::patch('deposits/{id}/restore',         [BankDepositController::class, 'restore'])->name('deposits.restore');
    Route::delete('deposits/{id}/force-delete',   [BankDepositController::class, 'forceDelete'])->name('deposits.force-delete');

    // Financial years
    Route::resource('financial-years', FinancialYearController::class);
    Route::patch('financial-years/{financialYear}/activate', [FinancialYearController::class, 'activate'])->name('financial-years.activate');
    Route::patch('financial-years/{financialYear}/close',    [FinancialYearController::class, 'close'])->name('financial-years.close');
    Route::patch('financial-years/{id}/restore',             [FinancialYearController::class, 'restore'])->name('financial-years.restore');

    // Customers
    Route::resource('customers', CustomerController::class);
    Route::patch('customers/{id}/restore',        [CustomerController::class, 'restore'])->name('customers.restore');
    Route::delete('customers/{id}/force-delete',  [CustomerController::class, 'forceDelete'])->name('customers.force-delete');

    // Reports
    Route::get('reports',                  [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/sales',            [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/inventory',        [ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('reports/transfers',        [ReportController::class, 'transfers'])->name('reports.transfers');
    Route::get('reports/deposits',         [ReportController::class, 'deposits'])->name('reports.deposits');
    Route::get('reports/export/sales',     [ReportController::class, 'exportSales'])->name('reports.export.sales');
    Route::get('reports/export/inventory', [ReportController::class, 'exportInventory'])->name('reports.export.inventory');
    Route::get('reports/product',          [ReportController::class, 'product'])->name('reports.product');
    Route::get('reports/profit-loss',      [ReportController::class, 'profitLoss'])->name('reports.profit-loss');
    Route::get('reports/stock-balance',    [ReportController::class, 'stockBalance'])->name('reports.stock-balance');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    // Profile
    Route::get('profile',           [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile',           [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password',  [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Audit
    Route::get('audit',      [AuditController::class, 'index'])->name('audit.index');
    Route::get('audit/{id}', [AuditController::class, 'show'])->name('audit.show');

    // Suppliers
    Route::resource('suppliers', SupplierController::class);
    Route::patch('suppliers/{id}/restore',       [SupplierController::class, 'restore'])->name('suppliers.restore');
    Route::delete('suppliers/{id}/force-delete', [SupplierController::class, 'forceDelete'])->name('suppliers.force-delete');

    // Purchase orders
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::patch('purchase-orders/{purchaseOrder}/approve',      [PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve');
    Route::patch('purchase-orders/{purchaseOrder}/mark-ordered', [PurchaseOrderController::class, 'markOrdered'])->name('purchase-orders.mark-ordered');
    Route::post('purchase-orders/{purchaseOrder}/receive',       [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
    Route::patch('purchase-orders/{purchaseOrder}/cancel',       [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');
    Route::patch('purchase-orders/{id}/restore',                 [PurchaseOrderController::class, 'restore'])->name('purchase-orders.restore');

    // Supplier payments
    Route::resource('supplier-payments', SupplierPaymentController::class)->only(['index','create','store','show']);

    // Settings
    Route::get('settings',              [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings/{group}',      [SettingsController::class, 'update'])->name('settings.update');

    // Stock takes
    Route::resource('stock-takes', StockTakeController::class);
    Route::post('stock-takes/{stockTake}/count',   [StockTakeController::class, 'count'])->name('stock-takes.count');
    Route::patch('stock-takes/{stockTake}/submit', [StockTakeController::class, 'submit'])->name('stock-takes.submit');
    Route::patch('stock-takes/{stockTake}/approve',[StockTakeController::class, 'approve'])->name('stock-takes.approve');
    Route::patch('stock-takes/{stockTake}/cancel', [StockTakeController::class, 'cancel'])->name('stock-takes.cancel');
    Route::patch('stock-takes/{id}/restore',       [StockTakeController::class, 'restore'])->name('stock-takes.restore');

    // PDF routes
    Route::prefix('pdf')->name('pdf.')->group(function () {
        Route::get('receipt/{sale}',                 [PdfController::class, 'receipt'])->name('receipt');
        Route::get('sales-report',                   [PdfController::class, 'salesReport'])->name('sales-report');
        Route::get('inventory-report',               [PdfController::class, 'inventoryReport'])->name('inventory-report');
        Route::get('purchase-order/{purchaseOrder}', [PdfController::class, 'purchaseOrder'])->name('purchase-order');
        Route::get('stock-transfer/{transfer}',      [PdfController::class, 'stockTransfer'])->name('stock-transfer');
        Route::get('stock-take/{stockTake}',         [PdfController::class, 'stockTake'])->name('stock-take');
        Route::get('return-receipt/{return}',        [PdfController::class, 'returnReceipt'])->name('return-receipt'); // ← add this
        Route::get('product-report',                 [PdfController::class, 'productReport'])->name('product-report');
        Route::get('profit-loss',                    [PdfController::class, 'profitLossReport'])->name('profit-loss');
        Route::get('stock-balance',                  [PdfController::class, 'stockBalance'])->name('stock-balance');
    });

    // In routes/web.php — inside auth middleware group
    Route::prefix('settings/roles')->name('roles.')->group(function () {
        Route::get('/',                      [RolePermissionController::class, 'index'])->name('index');
        Route::get('/create',                [RolePermissionController::class, 'create'])->name('create');
        Route::post('/',                     [RolePermissionController::class, 'store'])->name('store');
        Route::get('/{role}/edit',           [RolePermissionController::class, 'edit'])->name('edit');
        Route::put('/{role}',                [RolePermissionController::class, 'update'])->name('update');
        Route::delete('/{role}',             [RolePermissionController::class, 'destroy'])->name('destroy');
        Route::get('/permissions',           [RolePermissionController::class, 'permissions'])->name('permissions');
        Route::get('/user-roles',            [RolePermissionController::class, 'userRoles'])->name('user-roles');
        Route::put('/user-roles/{user}',     [RolePermissionController::class, 'updateUserRole'])->name('update-user-role');
    });

    // Product import
    Route::get('product/import',          [ProductImportController::class, 'index'])->name('products.import');
    Route::post('product/import/preview', [ProductImportController::class, 'preview'])->name('products.import.preview');
    Route::get('product/import/confirm',  [ProductImportController::class, 'confirm'])->name('products.import.confirm');
    Route::post('product/import/process', [ProductImportController::class, 'process'])->name('products.import.process');
    Route::get('product/import/template', [ProductImportController::class, 'downloadTemplate'])->name('products.import.template');
});
