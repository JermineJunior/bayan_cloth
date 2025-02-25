<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BankContoller;
use App\Http\Controllers\DailyContoller;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SafeController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\Tree1Controller;
use App\Http\Controllers\Tree2Controller;
use App\Http\Controllers\Tree3Controller;
use App\Http\Controllers\Tree4Controller;
use App\Http\Controllers\OpeningContoller;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CompoundContoller;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SaleInvoiceContoller;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\SaleInvoiceReturnController;
use App\Http\Controllers\PreliminaryInvoiceController;
use App\Http\Controllers\PurchaseInvoiceReturnController;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])
    ->name('welcome')
    ->middleware('auth');

Route::group(
    attributes: ['prefix' => 'admin', 'middleware' => 'auth'],
    routes: function () {
        // Unit Route
        Route::resource(name: 'unit', controller: UnitController::class)->except(methods: ['show', 'edit', 'create']);
        Route::post(uri: 'unit/toggleStatus/{unit}', action: [UnitController::class, 'toggleStatus'])->name(name: 'unit.toggleStatus');
        // Category Route
        Route::resource(name: 'category', controller: CategoryController::class)->except(methods: ['show', 'edit', 'create']);
        Route::post(uri: 'category/toggleStatus/{category}', action: [CategoryController::class, 'toggleStatus'])->name(name: 'category.toggleStatus');
        // Stock Route
        Route::resource(name: 'stock', controller: StockController::class)->except(methods: ['show', 'edit', 'create']);
        Route::post(uri: 'stock/toggleStatus/{stock}', action: [StockController::class, 'toggleStatus'])->name(name: 'stock.toggleStatus');
        Route::get(uri: 'stock/transform/create', action: [StockController::class, 'transform_create'])->name(name: 'stock.transform.create');
        Route::post(uri: 'stock/transform', action: [StockController::class, 'transform_store'])->name(name: 'stock.transform.store');
        Route::get(uri: 'stock/transform', action: [StockController::class, 'transform_index'])->name(name: 'stock.transform.index');
        Route::delete(uri: 'stock/transform/{transform}', action: [StockController::class, 'transform_delete'])->name(name: 'stock.transform.delete');

        // Product Route
        Route::resource(name: 'product', controller: ProductController::class)->except(methods: 'show');
        Route::post(uri: 'product/toggleStatus/{product}', action: [ProductController::class, 'toggleStatus'])->name(name: 'product.toggleStatus');
        Route::get(uri: '/check-barcode', action: [ProductController::class, 'checkBarcode'])->name(name: 'product.checkBarcode');
        Route::get(uri: '/check-name', action: [ProductController::class, 'checkName'])->name(name: 'product.checkName');
        Route::get(uri: '/product/opening/{product}', action: [ProductController::class, 'opening'])->name(name: 'product.opening');
        Route::post(uri: '/product/opening/{product}', action: [ProductController::class, 'opening_store'])->name(name: 'product.opening.store');
        // Customer Route
        Route::resource(name: 'customer', controller: CustomerController::class)->except(methods: ['show', 'edit', 'create']);
        Route::post(uri: 'customer/toggleStatus/{customer}', action: [CustomerController::class, 'toggleStatus'])->name(name: 'customer.toggleStatus');
        // Bank Route
        Route::resource(name: 'bank', controller: BankContoller::class)->except(methods: ['show', 'edit', 'create']);
        Route::post(uri: 'bank/toggleStatus/{bank}', action: [BankContoller::class, 'toggleStatus'])->name(name: 'bank.toggleStatus');
        // Safe Route
        Route::resource(name: 'safe', controller: SafeController::class)->except(methods: ['show', 'edit', 'create']);
        Route::post(uri: 'safe/toggleStatus/{safe}', action: [SafeController::class, 'toggleStatus'])->name(name: 'safe.toggleStatus');
        // Purchase Invoice Route
        Route::resource(name: 'purchase_invoice', controller: PurchaseInvoiceController::class)->except(methods: ['show']);
        Route::post(uri: 'purchase_invoice/toggleStatus/{purchase_invoice}', action: [PurchaseInvoiceController::class, 'toggleStatus'])->name(name: 'purchase_invoice.toggleStatus');
        // Sale Invoice Route
        Route::resource(name: 'sale_invoice', controller: SaleInvoiceContoller::class)->except(methods: ['show']);
        Route::post(uri: 'sale_invoice/toggleStatus/{sale_invoice}', action: [SaleInvoiceContoller::class, 'toggleStatus'])->name(name: 'sale_invoice.toggleStatus');
        Route::post(uri: '/get-product', action: [ProductController::class, 'getProduct'])->name(name: 'product.getProduct');
        Route::post(uri: '/get-unit-price-product', action: [ProductController::class, 'getUnitPrice'])->name(name: 'product.getUnitPrice');
        Route::post(uri: '/check-qty', action: [ProductController::class, 'checkQty'])->name(name: 'product.checkQty');
        Route::get(uri: 'sale_invoice/select2', action: [SaleInvoiceContoller::class, 'select2'])->name(name: 'sale_invoice.select2');
        // Supplier Route
        Route::resource(name: 'supplier', controller: SupplierController::class)->except(methods: 'show');
        Route::post(uri: 'supplier/toggleStatus/{supplier}', action: [SupplierController::class, 'toggleStatus'])->name(name: 'supplier.toggleStatus');
        // Purchase Invoice Return Route
        Route::resource(name: 'purchase_invoice_return', controller: PurchaseInvoiceReturnController::class)->only(methods: ['store']); // احتمال الغي هذا الراوت على حسب العميل
        Route::get(uri: 'purchase_invoice_return/{purchase_invoice}', action: [PurchaseInvoiceReturnController::class, 'create'])->name(name: 'purchase_invoice_return.create');
        Route::post(uri: 'purchase_invoice/update_qty_product', action: [PurchaseInvoiceController::class, 'updateQtyProduct'])->name(name: 'purchase_invoice.updateQty');
        // Sales Invoice Return Route
        Route::resource(name: 'sale_invoice_return', controller: SaleInvoiceReturnController::class)->only(methods: ['store']);
        Route::get(uri: 'sale_invoice_return/{sale_invoice}', action: [SaleInvoiceReturnController::class, 'create'])->name(name: 'sale_invoice_return.create');
        Route::post(uri: 'sale_invoice/update_qty_product', action: [SaleInvoiceContoller::class, 'updateQtyProduct'])->name(name: 'sale_invoice.updateQty');
        // Tree Route For Account
        Route::resource(name: 'tree1', controller: Tree1Controller::class)->only(methods: ['index', 'store', 'update']);
        Route::resource(name: 'tree2', controller: Tree2Controller::class)->only(methods: ['index', 'store', 'update']);
        Route::resource(name: 'tree3', controller: Tree3Controller::class)->only(methods: ['index', 'store', 'update']);
        Route::resource(name: 'tree4', controller: Tree4Controller::class)->except(methods: ['create', 'edit']);
        // Select2 Ajax Route
        Route::get(uri: 'select2/tree4', action: [Tree4Controller::class, 'select2'])->name(name: 'select2.tree4');
        Route::get(uri: 'select2/tree3', action: [Tree3Controller::class, 'select2'])->name(name: 'select2.tree3');
        Route::get(uri: 'select2/customer', action: [CustomerController::class, 'select2'])->name(name: 'select2.customer');
        Route::get(uri: 'select2/supplier', action: [SupplierController::class, 'select2'])->name(name: 'select2.supplier');
        // Report Route
        Route::get(uri: '/account-statement', action: [ReportController::class, 'create_statement'])->name(name: 'account.statement');
        Route::post(uri: '/account-statement', action: [ReportController::class, 'show_statement'])->name(name: 'account.statement.show');
        Route::get(uri: '/reports/journal', action: [ReportController::class, 'journal'])->name(name: 'journal.index');
        Route::post(uri: '/reports/journal', action: [ReportController::class, 'show_journal'])->name(name: 'journal.show');
        Route::get(uri: '/reports/journal-entry', action: [ReportController::class, 'journalEntry'])->name(name: 'journalEntry.index');
        Route::get(uri: '/reports/journal-entry/{id}', action: [ReportController::class, 'show_journalEntry'])->name(name: 'journalEntry.show');
        Route::get(uri: '/reports/accounting-books', action: [ReportController::class, 'accountingBooks'])->name(name: 'accountingBooks.index');
        Route::post(uri: '/reports/accounting-books', action: [ReportController::class, 'show_accountingBooks'])->name(name: 'accountingBooks.show');
        // Daily , Compound and Opening Rotes
        Route::resource(name: 'daily', controller: DailyContoller::class)->except(methods: 'show');
        Route::resource(name: 'compound', controller: CompoundContoller::class)->except(methods: 'show');
        Route::resource(name: 'opening', controller: OpeningContoller::class)->except(methods: 'show');

        // Route Preliminary Invoice
        Route::get(uri: 'PreliminaryInvoice', action: [PreliminaryInvoiceController::class, 'index'])->name('preliminaryInvoice.index');
        Route::get(uri: 'PreliminaryInvoice/create', action: [PreliminaryInvoiceController::class, 'create'])->name('preliminaryInvoice.create');
        Route::post(uri: 'PreliminaryInvoice', action: [PreliminaryInvoiceController::class, 'store'])->name('preliminaryInvoice.store');
        Route::get(uri: 'PreliminaryInvoice/transform/{preliminaryInvoice}', action: [PreliminaryInvoiceController::class, 'transform'])->name('preliminaryInvoice.transform');
        Route::post(uri: 'PreliminaryInvoice/transform/{preliminaryInvoice}', action: [PreliminaryInvoiceController::class, 'transform_store'])->name('preliminaryInvoice.transform.store');
        Route::delete(uri: 'PreliminaryInvoice/{preliminaryInvoice}', action: [PreliminaryInvoiceController::class, 'destroy'])->name('preliminaryInvoice.destroy');
        // User Route
        Route::resource(name: 'user', controller: UserController::class)->except(methods: 'show');
        Route::post(uri: 'user/toggleStatus/{user}', action: [UserController::class, 'toggleStatus'])->name(name: 'user.toggleStatus');
        Route::get(uri: 'trashed_users', action: [UserController::class, 'trashed_users'])->name(name: 'user.trashed_users');
        Route::post(uri: 'user/restore', action: [UserController::class, 'restore'])->name(name: 'user.restore');
        Route::post(uri: 'user/force_delete', action: [UserController::class, 'force_delete'])->name(name: 'user.force_delete');
        Route::post(uri: 'user/password_reset', action: [UserController::class, 'password_reset'])->name(name: 'user.reset.password');
        Route::get(uri: 'user/checkEmail', action: [UserController::class, 'checkEmail'])->name('user.checkEmail');
        Route::get(uri: 'user/checkPhoneNumber', action: [UserController::class, 'checkPhoneNumber'])->name('user.checkPhoneNumber');
        Route::get(uri: 'user/profile', action: [UserController::class, 'profile'])->name(name: 'user.profile');
        Route::post(uri: 'user/profile', action: [UserController::class, 'updateProfile'])->name(name: 'user.profile.update');
        // Route Role
        Route::resource('role', RoleController::class);
        // Route Report For Sales And Purchases
        Route::get(uri: 'reports/daily-sales', action: [ReportController::class, 'dailySalesReport'])->name(name: 'reports.daily_sales');
        Route::get(uri: 'reports/monthly-sales', action: [ReportController::class, 'monthlySalesReport'])->name(name: 'reports.monthly_sales');
        Route::get(uri: 'reports/yearly-sales', action: [ReportController::class, 'yearlySalesReport'])->name(name: 'reports.yearly_sales');
        Route::get(uri: 'reports/daily-purchases', action: [ReportController::class, 'dailyPurchasesReport'])->name(name: 'reports.daily_purchases');
        Route::get(uri: 'reports/monthly-purchases', action: [ReportController::class, 'monthlyPurchasesReport'])->name(name: 'reports.monthly_purchases');
        Route::get(uri: 'reports/yearly-purchases', action: [ReportController::class, 'yearlyPurchasesReport'])->name(name: 'reports.yearly_purchases');
        Route::get(uri: '/reports/top-products', action: [ReportController::class, 'topProducts'])->name(name: 'reports.top-products');
        Route::get(uri: 'reports/customer/sales', action: [ReportController::class, 'customerSales'])->name(name: 'reports.customer_sales'); 
        Route::post(uri: 'reports/customer/sales', action: [ReportController::class, 'show_customerSales'])->name(name: 'reports.customer_sales.show'); 
        Route::get(uri: 'reports/supplier/purchases', action: [ReportController::class, 'supplierPurchases'])->name(name: 'reports.supplier_purchases'); 
        Route::post(uri: 'reports/supplier/purchases', action: [ReportController::class, 'show_supplierPurchases'])->name(name: 'reports.supplier_purchases.show'); 
        Route::get(uri: '/reports/expiring-products', action: [ReportController::class, 'expiringProducts'])->name(name: 'reports.expiring-products');
        Route::get(uri: '/reports/expired-products', action: [ReportController::class, 'expiredProducts'])->name(name: 'reports.expired-products');
        Route::get(uri: '/reports/low-stock-products', action: [ReportController::class, 'lowStockProducts'])->name(name: 'reports.low_stock');   
        Route::get(uri: '/reports/sale/index', action: [ReportController::class, 'sale'])->name(name: 'reports.sale.index');   
        Route::post(uri: '/reports/sale/index', action: [ReportController::class, 'show_sale'])->name(name: 'reports.sale.show');   
        Route::get(uri: '/reports/purchase/index', action: [ReportController::class, 'purchase'])->name(name: 'reports.purchase.index');   
        Route::post(uri: '/reports/purchase/index', action: [ReportController::class, 'show_purchase'])->name(name: 'reports.purchase.show');   
        Route::get(uri: '/reports/sale_return/index', action: [ReportController::class, 'saleReturn'])->name(name: 'reports.sale_return.index');   
        Route::post(uri: '/reports/sale_return/index', action: [ReportController::class, 'showSaleReturn'])->name(name: 'reports.sale_return.show');   
        Route::get(uri: '/reports/purchase_return/index', action: [ReportController::class, 'purchaseReturn'])->name(name: 'reports.purchase_return.index');   
        Route::post(uri: '/reports/purchase_return/index', action: [ReportController::class, 'showPurchaseReturn'])->name(name: 'reports.purchase_return.show');   
    },
);
