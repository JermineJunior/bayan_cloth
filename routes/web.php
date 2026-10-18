<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BankContoller;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DailyContoller;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SafeController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\Tree1Controller;
use App\Http\Controllers\Tree2Controller;
use App\Http\Controllers\Tree3Controller;
use App\Http\Controllers\Tree4Controller;
use App\Http\Controllers\BurrenController;
use App\Http\Controllers\OpeningContoller;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CompoundContoller;
use App\Http\Controllers\CurrencyTransferController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\CustomerInvoicePaymentController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CurenceyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SaleInvoiceContoller;
use App\Http\Controllers\RepresentativeController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\SaleInvoiceReturnController;
use App\Http\Controllers\PreliminaryInvoiceController;
use App\Http\Controllers\PurchaseInvoiceReportContorller;
use App\Http\Controllers\PurchaseInvoiceReturnController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SaleInvoiceReportController;
use App\Http\Middleware\CheckSession;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])
    ->name('welcome')
    ->middleware(['auth', CheckSession::class]);

Route::group(
    attributes: ['prefix' => 'admin', 'middleware' => ['auth']],
    routes: function () {
        // Unit Route
        Route::resource(name: 'unit', controller: UnitController::class)->except(methods: ['show', 'edit', 'create']);
        Route::post(uri: 'unit/toggleStatus/{unit}', action: [UnitController::class, 'toggleStatus'])->name(name: 'unit.toggleStatus');
        Route::get(uri: 'unit/checkName', action: [UnitController::class, 'checkName'])->name('unit.checkName');
        // Category Route
        Route::resource(name: 'category', controller: CategoryController::class)->except(methods: ['show', 'edit', 'create']);
        Route::post(uri: 'category/toggleStatus/{category}', action: [CategoryController::class, 'toggleStatus'])->name(name: 'category.toggleStatus');
        Route::get(uri: 'category/checkName', action: [CategoryController::class, 'checkName'])->name(name: 'category.checkName');
        Route::post(uri: '/category/update-order', action: [CategoryController::class, 'updateOrder'])->name(name: 'category.updateOrder');
        // Branch Route
        Route::resource(name: 'branch', controller: BranchController::class)->except(methods: ['show', 'edit', 'create']);
        Route::post(uri: 'branch/toggleStatus/{branch}', action: [BranchController::class, 'toggleStatus'])->name(name: 'branch.toggleStatus');
        Route::get(uri: 'select-branch', action: [BranchController::class, 'selectBranch'])->name(name: 'branch.select');
        Route::get(uri: 'branch-choose/{branch}', action: [BranchController::class, 'branchChoose'])->name(name: 'branch.choose');
        Route::get(uri: 'branch/checkName', action: [BranchController::class, 'checkName'])->name(name: 'branch.checkName');
        // Stock Route
        Route::resource(name: 'stock', controller: StockController::class)->except(methods: ['edit', 'create']);
        Route::post(uri: '/stock/toggleStatus/{stock}', action: [StockController::class, 'toggleStatus'])->name(name: 'stock.toggleiStatus');
        Route::get(uri: '/stock/transform/list', action: [StockController::class, 'transform_list'])->name(name: 'stock.transform.list');
        Route::get(uri: '/stock/transform/create', action: [StockController::class, 'transform_create'])->name(name: 'stock.transform.create');
        Route::post(uri: '/stock/transform', action: [StockController::class, 'transform_store'])->name(name: 'stock.transform.store');
        Route::get(uri: '/stock/transform/edit/{transform}', action: [StockController::class, 'transform_edit'])->name(name: 'stock.transform.edit');
        Route::put(uri: '/stock/transform/update/{transform}', action: [StockController::class, 'transform_update'])->name(name: 'stock.transform.update');
        Route::delete('stock/transform/{transform}', [StockController::class, 'destory'])->name('stock.transform.destroy');
        Route::get(uri: '/stock/transform/index', action: [StockController::class, 'transform_index'])->name(name: 'stock.transform.index');
        Route::post(uri: '/stock/transform/show', action: [StockController::class, 'transform_show'])->name(name: 'stock.transform.show');
        // Product Route
        Route::resource(name: 'product', controller: ProductController::class)->except(methods: 'show');
        Route::post(uri: 'product/toggleStatus/{product}', action: [ProductController::class, 'toggleStatus'])->name(name: 'product.toggleStatus');
        Route::get(uri: '/check-barcode', action: [ProductController::class, 'checkBarcode'])->name(name: 'product.checkBarcode');
        Route::get(uri: '/check-name', action: [ProductController::class, 'checkName'])->name(name: 'product.checkName');
        Route::get(uri: '/product/opening', action: [ProductController::class, 'opening'])->name(name: 'product.opening');
        Route::post(uri: '/product/opening', action: [ProductController::class, 'opening_store'])->name(name: 'product.opening.store');
        Route::post(uri: 'product/checkQty', action: [ProductController::class, 'updateQty'])->name(name: 'product.updateQty');
        Route::get(uri: 'product/excel/create', action: [ProductController::class, 'excel_create'])->name(name: 'product.excel.create');
        Route::post(uri: 'product/excel/store', action: [ProductController::class, 'excel_store'])->name(name: 'product.excel.store');
        Route::post(uri:'product/percent',action:[ProductController::class,'percent'])->name(name:'product.percent');
        // Customer Route
        Route::resource(name: 'customer', controller: CustomerController::class)->except(methods: ['show', 'edit', 'create']);
        Route::post(uri: 'customer/toggleStatus/{customer}', action: [CustomerController::class, 'toggleStatus'])->name(name: 'customer.toggleStatus');
        Route::post(uri: 'addCustomer', action: [CustomerController::class, 'addCustomer'])->name(name: 'add.customer');
        Route::get(uri: '/customer/representative/{id}', action: [CustomerController::class, 'getRepresentative'])->name(name: 'customer.representative');

        Route::get(uri: '/check-name-customer', action: [CustomerController::class, 'checkName'])->name(name: 'customer.checkName');
        Route::get(uri: '/check-email-customer', action: [CustomerController::class, 'checkEmail'])->name(name: 'customer.checkEmail');
        Route::get(uri: '/check-phone-customer', action: [CustomerController::class, 'checkPhone'])->name(name: 'customer.checkPhone');
        // Bank Route
        Route::resource(name: 'bank', controller: BankContoller::class)->except(methods: ['show', 'edit', 'create']);
        Route::post(uri: 'bank/toggleStatus/{bank}', action: [BankContoller::class, 'toggleStatus'])->name(name: 'bank.toggleStatus');
        Route::get(uri: 'bank/checkName', action: [BankContoller::class, 'checkName'])->name('bank.checkName');
        // Safe Route
        Route::resource(name: 'safe', controller: SafeController::class)->except(methods: ['show', 'edit', 'create']);
        Route::post(uri: 'safe/toggleStatus/{safe}', action: [SafeController::class, 'toggleStatus'])->name(name: 'safe.toggleStatus');
        Route::get(uri: 'safe/checkName', action: [SafeController::class, 'checkName'])->name('safe.checkName');
        // Purchase Invoice Route
        Route::resource(name: 'purchase_invoice', controller: PurchaseInvoiceController::class);
        Route::get(uri: 'extenal/purchase/create{purchase_invoice}', action: [PurchaseInvoiceController::class, 'externalPurchase_create'])->name(name: 'external.purchase.create');
        Route::post(uri: 'extenal/purchase/{purchase_invoice}', action: [PurchaseInvoiceController::class, 'externalPurchase_store'])->name(name: 'external.purchase.store');
        // Sale Invoice Route
        Route::resource(name: 'sale_invoice', controller: SaleInvoiceContoller::class)->except(methods: ['show']);
        Route::post(uri: '/get-product', action: [ProductController::class, 'getProduct'])->name(name: 'product.getProduct');
        Route::post(uri: '/get-unit-price-product', action: [ProductController::class, 'getUnitPrice'])->name(name: 'product.getUnitPrice');
        Route::post(uri: '/check-qty', action: [ProductController::class, 'checkQty'])->name(name: 'product.checkQty');
        Route::post(uri: 'sale_invoice/getProduct', action: [SaleInvoiceContoller::class, 'getProduct'])->name(name: 'invoice.getProduct');
        Route::post(uri: 'sale_invoice/search', action: [SaleInvoiceContoller::class, 'search'])->name(name: 'sale_invoice.search');
        Route::get(uri: 'invoices/princt/{sale_invoice}', action: [SaleInvoiceContoller::class, 'print'])->name(name: 'sale_invoice.print');
        Route::get(uri: 'sale_invoice/pay/create/{sale_invoice}', action: [SaleInvoiceContoller::class, 'pay_create'])->name(name: 'sale_invoice.pay.create');
        Route::post(uri: 'sale_invoice/pay/store{sale_invoice}', action: [SaleInvoiceContoller::class, 'pay_store'])->name(name: 'sale_invoice.pay.store');
        // Supplier Route
        Route::resource(name: 'supplier', controller: SupplierController::class)->except(methods: 'show');
        Route::post(uri: 'addSupplier', action: [SupplierController::class, 'addSupplier'])->name(name: 'add.supplier');
        Route::get(uri: '/check-name-supplier', action: [SupplierController::class, 'checkName'])->name(name: 'supplier.checkName');
        Route::get(uri: '/check-email-supplier', action: [SupplierController::class, 'checkEmail'])->name(name: 'supplier.checkEmail');
        Route::get(uri: '/check-phone-supplier', action: [SupplierController::class, 'checkPhone'])->name(name: 'supplier.checkPhone');
        // Representative Route
        Route::resource(name: 'representative', controller: RepresentativeController::class)->except(methods: 'show');
        Route::post(uri: 'representative/toggleStatus/{representative}', action: [RepresentativeController::class, 'toggleStatus'])->name(name: 'representative.toggleStatus');
        Route::post(uri: 'addRepresentative', action: [RepresentativeController::class, 'addRepresentative'])->name(name: 'add.representative');
        Route::get(uri: '/check-name-representative', action: [RepresentativeController::class, 'checkName'])->name(name: 'representative.checkName');
        Route::get(uri: '/check-email-representative', action: [RepresentativeController::class, 'checkEmail'])->name(name: 'representative.checkEmail');
        Route::get(uri: '/check-phone-representative', action: [RepresentativeController::class, 'checkPhone'])->name(name: 'representative.checkPhone');
        // Purchase Invoice Return Route
        // Route::resource(name: 'purchase_invoice_return', controller: PurchaseInvoiceReturnController::class)->only(methods: ['store']); // احتمال الغي هذا الراوت على حسب العميل
        // Route::get(uri: 'purchase_invoice_return/{purchase_invoice}', action: [PurchaseInvoiceReturnController::class, 'create'])->name(name: 'purchase_invoice_return.create');
        Route::resource(name: 'purchase_invoice_return', controller: PurchaseInvoiceReturnController::class);
        // Sales Invoice Return Route
        Route::resource(name: 'sale_invoice_return', controller: SaleInvoiceReturnController::class);
        Route::get('/sale_invoice/{id}/pdf', [SaleInvoiceContoller::class, 'generatePDF'])->name('sale_invoice.pdf');
        // Tree Route For Account
        Route::resource(name: 'tree1', controller: Tree1Controller::class)->only(methods: ['index', 'store', 'update']);
        Route::resource(name: 'tree2', controller: Tree2Controller::class)->only(methods: ['index', 'store', 'update']);
        Route::resource(name: 'tree3', controller: Tree3Controller::class)->only(methods: ['index', 'store', 'update']);
        Route::resource(name: 'tree4', controller: Tree4Controller::class)->except(methods: ['create', 'edit', 'show']);

        Route::get('/tree4/filter', [Tree4Controller::class, 'filter'])->name('tree4.filter');

        // Select2 Ajax Route
        // Route::get(uri: 'select2/tree4', action: [Tree4Controller::class, 'select2'])->name(name: 'select2.tree4');
        Route::get(uri: 'select2/customer', action: [CustomerController::class, 'select2'])->name(name: 'select2.customer');
        Route::get(uri: 'select2/supplier', action: [SupplierController::class, 'select2'])->name(name: 'select2.supplier');
        Route::get(uri: 'select2/stock', action: [StockController::class, 'select2'])->name(name: 'select2.stock');
        // Report Route
        Route::get(uri: '/account-statement', action: [ReportController::class, 'create_statement'])->name(name: 'account.statement');
        Route::post(uri: '/account-statement', action: [ReportController::class, 'show_statement'])->name(name: 'account.statement.show');
        Route::get(uri: '/report/journal', action: [ReportController::class, 'journal'])->name(name: 'journal.index');
        Route::post(uri: '/report/journal', action: [ReportController::class, 'show_journal'])->name(name: 'journal.show');
        Route::get(uri: '/report/journal-entry', action: [ReportController::class, 'journalEntry'])->name(name: 'journalEntry.index');
        Route::get(uri: '/report/journal-entry/{id}', action: [ReportController::class, 'show_journalEntry'])->name(name: 'journalEntry.show');
        Route::get(uri: '/report/accounting-books', action: [ReportController::class, 'accountingBooks'])->name(name: 'accountingBooks.index');
        Route::post(uri: '/report/accounting-books', action: [ReportController::class, 'show_accountingBooks'])->name(name: 'accountingBooks.show');
        Route::get(uri: '/report/product/price', action: [ReportController::class, 'select_representative'])->name(name: 'select.representative');
        Route::post(uri: '/report/product/price', action: [ReportController::class, 'price'])->name(name: 'product.price');
        // Daily , Compound and Opening Rotes
        Route::resource(name: 'daily', controller: DailyContoller::class)->except(methods: 'show');
        Route::get(uri: '/daily/{constant_number}', action: [DailyContoller::class, 'show'])->name(name: 'daily.show');
        Route::resource(name: 'compound', controller: CompoundContoller::class)->except(methods: 'show');
        Route::resource(name: 'currency-transfer', controller: CurrencyTransferController::class)->only(methods: ['index', 'create', 'store', 'show']);
        Route::resource(name: 'opening', controller: OpeningContoller::class)->except(methods: 'show');

        // Route Preliminary Invoice
        Route::get(uri: 'PreliminaryInvoice', action: [PreliminaryInvoiceController::class, 'index'])->name('preliminaryInvoice.index');
        Route::get(uri: 'PreliminaryInvoice/create', action: [PreliminaryInvoiceController::class, 'create'])->name('preliminaryInvoice.create');
        Route::post(uri: 'PreliminaryInvoice', action: [PreliminaryInvoiceController::class, 'store'])->name('preliminaryInvoice.store');
        Route::get(uri: 'PreliminaryInvoice/print/{preliminary_invoice}', action: [PreliminaryInvoiceController::class, 'print'])->name(name: 'PreliminaryInvoice.print');
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
        Route::get(uri: 'user/checkUsername', action: [UserController::class, 'checkUserName'])->name('user.checkUserName');
        Route::get(uri: 'user/profile', action: [UserController::class, 'profile'])->name(name: 'user.profile');
        Route::post(uri: 'user/profile', action: [UserController::class, 'updateProfile'])->name(name: 'user.profile.update');
        // Route Role
        Route::resource('role', RoleController::class);

        // Report Route
        Route::get(uri: '/report/top-products', action: [ReportController::class, 'topProducts'])->name(name: 'report.top-products');
        Route::get(uri: '/report/down-products', action: [ReportController::class, 'downProducts'])->name(name: 'report.down-products');
        Route::get(uri: 'report/customer/sales', action: [ReportController::class, 'customerSales'])->name(name: 'report.customer_sales');
        Route::post(uri: 'report/customer/sales', action: [ReportController::class, 'show_customerSales'])->name(name: 'report.customer_sales.show');
        Route::get(uri: 'report/customer/account', action: [ReportController::class, 'customerAccount'])->name(name: 'report.customer.account');
        Route::post(uri: 'report/customer/account', action: [ReportController::class, 'customerAccountShow'])->name(name: 'report.customer.show');
        Route::get(uri: 'report/supplier/account', action: [ReportController::class, 'supplierAccount'])->name(name: 'report.supplier.account');
        Route::post(uri: 'report/supplier/account', action: [ReportController::class, 'supplierAccountShow'])->name(name: 'report.supplier.show');
        Route::get(uri: 'report/safe/account', action: [ReportController::class, 'safeAccount'])->name(name: 'report.safe.account');
        Route::post(uri: 'report/safe/account', action: [ReportController::class, 'safeAccountShow'])->name(name: 'report.safe.show');
        Route::get(uri: 'report/bank/account', action: [ReportController::class, 'bankAccount'])->name(name: 'report.bank.account');
        Route::post(uri: 'report/bank/account', action: [ReportController::class, 'bankAccountShow'])->name(name: 'report.bank.show');
        Route::get(uri: 'report/supplier/purchases', action: [ReportController::class, 'supplierPurchases'])->name(name: 'report.supplier_purchases');
        Route::post(uri: 'report/supplier/purchases', action: [ReportController::class, 'show_supplierPurchases'])->name(name: 'report.supplier_purchases.show');
        Route::get(uri: '/report/expiring-products', action: [ReportController::class, 'expiringProducts'])->name(name: 'report.expiring-products');
        Route::get(uri: '/report/expired-products', action: [ReportController::class, 'expiredProducts'])->name(name: 'report.expired-products');
        Route::get(uri: '/report/low-stock-products', action: [ReportController::class, 'lowStockProducts'])->name(name: 'report.low_stock');
        Route::get(uri: 'report/revenue/account', action: [ReportController::class, 'revenueAccount'])->name(name: 'report.revenue');
        Route::post(uri: 'report/revenue/account', action: [ReportController::class, 'revenueAccountShow'])->name(name: 'report.revenue.show');
        Route::get(uri: 'report/expense/account', action: [ReportController::class, 'expenseAccount'])->name(name: 'report.expense');
        Route::post(uri: 'report/expense/account', action: [ReportController::class, 'expenseAccountShow'])->name(name: 'report.expense.show');
        Route::get(uri: '/report/sale/index', action: [ReportController::class, 'sale'])->name(name: 'report.sale.index');
        Route::post(uri: '/report/sale/index', action: [ReportController::class, 'show_sale'])->name(name: 'report.sale.show');
        Route::get(uri: '/report/purchase/index', action: [ReportController::class, 'purchase'])->name(name: 'report.purchase.index');
        Route::post(uri: '/report/purchase/index', action: [ReportController::class, 'show_purchase'])->name(name: 'report.purchase.show');
        Route::get(uri: '/report/sale_return/index', action: [ReportController::class, 'saleReturn'])->name(name: 'report.sale_return.index');
        Route::post(uri: '/report/sale_return/index', action: [ReportController::class, 'showSaleReturn'])->name(name: 'report.sale_return.show');
        Route::get(uri: '/report/purchase_return/index', action: [ReportController::class, 'purchaseReturn'])->name(name: 'report.purchase_return.index');
        Route::post(uri: '/report/purchase_return/index', action: [ReportController::class, 'showPurchaseReturn'])->name(name: 'report.purchase_return.show');
        Route::get(uri: '/report/stock/details', action: [ReportController::class, 'stock_detail_index'])->name(name: 'report.stock.details.index');
        Route::post(uri: '/report/stock/details', action: [ReportController::class, 'stock_detail_show'])->name(name: 'report.stock.details.show');
        Route::get(uri: 'report/burren/sale_price/index', action: [ReportController::class, 'burrenSaleIndex'])->name(name: 'report.burren.sale_price.index');
        Route::post(uri: 'report/burren/sale_price/index', action: [ReportController::class, 'burrenSaleShow'])->name(name: 'report.burren.sale_price.show');
        Route::get(uri: 'report/burren/purchase_price/index', action: [ReportController::class, 'burrenPurchaseIndex'])->name(name: 'report.burren.purchase_price.index');
        Route::post(uri: 'report/burren/purchase_price/index', action: [ReportController::class, 'burrenPurchaseShow'])->name(name: 'report.burren.purchase_price.show');
        Route::post(uri: 'report/burren/sale/search', action: [ReportController::class, 'burrenSaleSearch'])->name(name: 'report.burren.sale.search');
        Route::post(uri: 'report/burren/purchase/search', action: [ReportController::class, 'burrenPurchaseSearch'])->name(name: 'report.burren.purchase.search');
        // Route Sale and Return Sale
        Route::get('report/sale_invoice/customer', [SaleInvoiceReportController::class, 'customer_sale_index'])->name('report.sale_invoice.customer.index');
        Route::post('report/sale_invoice/customer', [SaleInvoiceReportController::class, 'customer_sale_show'])->name('report.sale_invoice.customer.show');
        Route::get('report/sale_return_invoice/customer', [SaleInvoiceReportController::class, 'customer_return_index'])->name('report.sale_return_invoice.customer.index');
        Route::post('report/sale_return_invoice/customer', [SaleInvoiceReportController::class, 'customer_return_show'])->name('report.sale_return_invoice.customer.show');
        Route::get('report/sale_invoice/agent', [SaleInvoiceReportController::class, 'agent_index'])->name('report.sale_invoice.agent.index');
        Route::post('report/sale_invoice/agent', [SaleInvoiceReportController::class, 'agent_show'])->name('report.sale_invoice.agent.show');
        Route::get('report/sale_return_invoice/agent', [SaleInvoiceReportController::class, 'agent_return_index'])->name('report.sale_return_invoice.agent.index');
        Route::post('report/sale_return_invoice/agent', [SaleInvoiceReportController::class, 'agent_return_show'])->name('report.sale_return_invoice.agent.show');
        Route::get('report/sale_invoice/category', [SaleInvoiceReportController::class, 'category_sale_index'])->name('report.sale_invoice.category.index');
        Route::post('report/sale_invoice/category', [SaleInvoiceReportController::class, 'category_sale_show'])->name('report.sale_invoice.category.show');
        Route::get('report/sale_invoice/product', [SaleInvoiceReportController::class, 'product_sale_index'])->name('report.sale_invoice.product.index');
        Route::post('report/sale_invoice/product', [SaleInvoiceReportController::class, 'product_sale_show'])->name('report.sale_invoice.product.show');
        Route::get('report/sale_return_invoice/category', [SaleInvoiceReportController::class, 'category_return_index'])->name('report.sale_return_invoice.category.index');
        Route::post('report/sale_return_invoice/category', [SaleInvoiceReportController::class, 'category_return_show'])->name('report.sale_return_invoice.category.show');
        Route::get('report/sale_invoice/details', [SaleInvoiceReportController::class, 'details_sale_index'])->name('report.sale_invoice.details.index');
        Route::post('report/sale_invoice/details', [SaleInvoiceReportController::class, 'details_sale_show'])->name('report.sale_invoice.details.show');
        Route::get('report/sale_return_invoice/details', [SaleInvoiceReportController::class, 'details_return_index'])->name('report.sale_return_invoice.details.index');
        Route::post('report/sale_return_invoice/details', [SaleInvoiceReportController::class, 'details_return_show'])->name('report.sale_return_invoice.details.show');
        Route::get('report/sale_invoice/summary', [SaleInvoiceReportController::class, 'summary_sale_index'])->name('report.sale_invoice.summary.index');
        Route::post('report/sale_invoice/summary', [SaleInvoiceReportController::class, 'summary_sale_show'])->name('report.sale_invoice.summary.show');
        Route::get('report/sale_return_invoice/summary', [SaleInvoiceReportController::class, 'summary_return_index'])->name('report.sale_return_invoice.summary.index');
        Route::post('report/sale_return_invoice/summary', [SaleInvoiceReportController::class, 'summary_return_show'])->name('report.sale_return_invoice.summary.show');
        Route::get('/report/represenative/sale_invoices', [SaleInvoiceReportController::class, 'representative_index'])->name('report.sale_invoice.representative.index');
        Route::post('/report/represenative/sale_invoices', [SaleInvoiceReportController::class, 'representative_show'])->name('report.sale_invoice.representative.show');
        Route::get('/report/represenative/sale_invoices_return', [SaleInvoiceReportController::class, 'representative_return_index'])->name('report.sale_invoice_return.representative.index');
        Route::post('/report/represenative/sale_invoices_return', [SaleInvoiceReportController::class, 'representative_return_show'])->name('report.sale_invoice_return.representative.show');
        Route::get('report/representative/customer', [SaleInvoiceReportController::class, 'representative_customer'])->name('report.representative.customer.index');
        Route::post('report/representative/customer', [SaleInvoiceReportController::class, 'representative_customer_show'])->name('report.representative.customer.show');
        Route::get('report/customer/accounting', [ReportController::class, 'customer_accouht_index'])->name('report.customer.account.index');
        Route::post('report/customer/accounting', [ReportController::class, 'customer_accouht_show'])->name('report.customer.account.show');

        // Route Top Customers Report
        Route::get('report/sale_invoice/top_customers', [SaleInvoiceReportController::class, 'top_customers_index'])->name('report.sale_invoice.top_customers.index');
        Route::post('report/sale_invoice/top_customers', [SaleInvoiceReportController::class, 'top_customers_show'])->name('report.sale_invoice.top_customers.show');

        // Route Complete Report
        Route::get('report/complete', [ReportController::class, 'complete_index'])->name('report.complete.index');
        Route::post('report/complete', [ReportController::class, 'complete_show'])->name('report.complete.show');

        // Route Purchase and Return Purchase
        Route::get('report/purchase_invoice/supplier', [PurchaseInvoiceReportContorller::class, 'supplier_purchase_index'])->name('report.purchase_ivoice.supplier.index');
        Route::post('report/purchase_invoice/supplier', [PurchaseInvoiceReportContorller::class, 'supplier_purchase_show'])->name('report.purchase_invoice.supplier.show');
        Route::get('report/purchase_return_invoice/supplier', [PurchaseInvoiceReportContorller::class, 'supplier_return_index'])->name('report.purchase_return_invoice.supplier.index');
        Route::post('report/purchase_return_invoice/supplier', [PurchaseInvoiceReportContorller::class, 'supplier_return_show'])->name('report.purchase_return_invoice.supplier.show');
        Route::get('report/purchase_invoice/agent', [PurchaseInvoiceReportContorller::class, 'agent_index'])->name('report.purchase_ivoice.agent.index');
        Route::post('report/purchase_invoice/agent', [PurchaseInvoiceReportContorller::class, 'agent_show'])->name('report.purchase_invoice.agent.show');
        Route::get('report/purchase_return_invoice/agent', [PurchaseInvoiceReportContorller::class, 'agent_return_index'])->name('report.purchase_return_invoice.agent.index');
        Route::post('report/purchase_return_invoice/agent', [PurchaseInvoiceReportContorller::class, 'agent_return_show'])->name('report.purchase_return_invoice.agent.show');
        Route::get('report/purchase_invoice/category', [PurchaseInvoiceReportContorller::class, 'category_purchase_index'])->name('report.purchase_invoice.category.index');
        Route::post('report/purchase_invoice/category', [PurchaseInvoiceReportContorller::class, 'category_purchase_show'])->name('report.purchase_invoice.category.show');
        Route::get('report/purchase_invoice/product', [PurchaseInvoiceReportContorller::class, 'product_purchase_index'])->name('report.purchase_invoice.product.index');
        Route::post('report/purchase_invoice/product', [PurchaseInvoiceReportContorller::class, 'product_purchase_show'])->name('report.purchase_invoice.product.show');
        Route::get('report/purchase_return_invoice/category', [PurchaseInvoiceReportContorller::class, 'category_return_index'])->name('report.purchase_return_invoice.category.index');
        Route::post('report/purchase_return_invoice/category', [PurchaseInvoiceReportContorller::class, 'category_return_show'])->name('report.purchase_return_invoice.category.show');
        Route::get('report/purchase_invoice/details', [PurchaseInvoiceReportContorller::class, 'details_purchase_index'])->name('report.purchase_invoice.details.index');
        Route::post('report/purchase_invoice/details', [PurchaseInvoiceReportContorller::class, 'details_purchase_show'])->name('report.purchase_invoice.details.show');
        Route::get('report/purchase_return_invoice/details', [PurchaseInvoiceReportContorller::class, 'details_return_index'])->name('report.purchase_return_invoice.details.index');
        Route::post('report/purchase_return_invoice/details', [PurchaseInvoiceReportContorller::class, 'details_return_show'])->name('report.purchase_return_invoice.details.show');
        Route::get('report/purchase_invoice/summary', [PurchaseInvoiceReportContorller::class, 'summary_purchase_index'])->name('report.purchase_invoice.summary.index');
        Route::post('report/purchase_invoice/summary', [PurchaseInvoiceReportContorller::class, 'summary_purchase_show'])->name('report.purchase_invoice.summary.show');
        Route::get('report/purchase_return_invoice/summary', [PurchaseInvoiceReportContorller::class, 'summary_return_index'])->name('report.purchase_return_invoice.summary.index');
        Route::post('report/purchase_return_invoice/summary', [PurchaseInvoiceReportContorller::class, 'summary_return_show'])->name('report.purchase_return_invoice.summary.show');

        // Route Company Info
        Route::get(uri: '/company/info', action: [SettingController::class, 'index'])->name('setting.index');
        Route::put(uri: '/company/info', action: [SettingController::class, 'update'])->name('setting.update');

        // Route Burren
        // Route::get(uri: '/burren', action: [BurrenController::class, 'index'])->name(name: 'burren.index');
        Route::get(uri: '/show_burren', action: [BurrenController::class, 'show'])->name(name: 'burren.show');
        Route::post(uri: '/show_burren', action: [BurrenController::class, 'info'])->name(name: 'burren.info');
        // Route::post(uri: '/burren', action: [BurrenController::class, 'store'])->name(name: 'burren.store');

        // Route expense and Revenue
        Route::resource(name: 'expense', controller: ExpenseController::class);
        Route::resource(name: 'revenue', controller: RevenueController::class);

        // Route Customer Invoice Payment (تحصيل فواتير العملاء)
        Route::get(uri: 'customer-invoice-payment/create', action: [CustomerInvoicePaymentController::class, 'create'])->name(name: 'customer-invoice-payment.create');
        Route::post(uri: 'customer-invoice-payment/unpaid', action: [CustomerInvoicePaymentController::class, 'unpaid'])->name(name: 'customer-invoice-payment.unpaid');
        Route::post(uri: 'customer-invoice-payment', action: [CustomerInvoicePaymentController::class, 'store'])->name(name: 'customer-invoice-payment.store');

        // Route Curencey
        Route::resource(name: 'curencey', controller: CurenceyController::class)->only(methods: ['index', 'store', 'update','destroy']);
        Route::get(uri: 'curencey/checkName', action: [CurenceyController::class, 'checkName'])->name(name: 'curencey.checkName');
        // Route Purchase Order
        Route::resource('purchase_order',PurchaseOrderController::class);
    },
);
