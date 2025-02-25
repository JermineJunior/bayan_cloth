<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tree3;
use App\Models\Product;
use App\Models\Operation;
use Illuminate\View\View;
use App\Models\Restriction;
use App\Models\SaleInvoice;
use Illuminate\Http\Request;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceReturn;
use App\Models\SaleInvoiceDetail;
use App\Models\SaleInvoiceReturn;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function create_statement(): View
    {
        return view('report.account_statement.index');
    }

    public function show_statement(Request $request): View
    {
        $name = $request->tree4;
        $Madin = Operation::with('tree4_madin', 'tree4_dain')->whereMadin_id($request->tree4)->whereDate('created_at', '>=', $request->start)->whereDate('created_at', '<=', $request->end)->orderBy('id', 'DESC')->get();
        $Dain = Operation::with('tree4_madin', 'tree4_dain')->whereDain_id($request->tree4)->whereDate('created_at', '>=', $request->start)->whereDate('created_at', '<=', $request->end)->orderBy('id', 'DESC')->get();
        $start = $request->start;
        $end = $request->end;

        return view('report.account_statement.show', compact('Madin', 'Dain', 'name', 'start', 'end'));
    }

    public function journal(): View
    {
        return view('report.journal.index');
    }

    public function show_journal(Request $request): View
    {
        $start = $request->start;
        $end = $request->end;
        $operations = Operation::with('tree4_madin', 'tree4_dain')
            ->whereIn('type', [5, 6])
            ->whereDate('created_at', '>=', $request->start)
            ->whereDate('created_at', '<=', $request->end)
            ->get();
        return view('report.journal.show', compact('operations', 'start', 'end'));
    }

    public function journalEntry(): View
    {
        $operations = Operation::with('tree4_madin', 'tree4_dain')
            ->whereIn('type', [5, 6])
            ->get();
        return view('report.journalEntry.index', compact('operations'));
    }

    public function show_journalEntry($id): View
    {
        $operation = Operation::with('tree4_madin', 'tree4_dain')->find($id);
        return view('report.journalEntry.show', compact('operation'));
    }

    public function accountingBooks(): View
    {
        return view('report.book.index');
    }

    public function show_accountingBooks(Request $request): View
    {
        $tree3 = Tree3::find($request->tree3);
        $one = Restriction::where('tree4_code', 'like', $tree3->tree3_code . '%')
            ->whereDate('date', '>=', $request->start)
            ->whereDate('date', '<=', $request->end)
            ->get();
        $Madin = Restriction::where('tree4_code', 'like', $tree3->tree3_code . '%')
            ->whereDate('created_at', '>=', $request->start)
            ->whereDate('created_at', '<=', $request->end)
            ->sum('Madin');
        $Dain = Restriction::where('tree4_code', 'like', $tree3->tree3_code . '%')
            ->whereDate('date', '>=', $request->start)
            ->whereDate('date', '<=', $request->end)
            ->sum('Dain');

        $start = $request->start;
        $end = $request->end;
        return view('report.book.show', compact('one', 'start', 'end', 'Madin', 'Dain'));
    }

    public function dailySalesReport(): View
    {
        $sales = SaleInvoice::whereDate('created_at', Carbon::today())->get();

        if ($sales->isEmpty()) {
            return view('report.sale.daily_sales', [
                'message' => 'لا توجد مبيعات لهذا اليوم.',
            ]);
        }

        $totalSales = $sales->sum('total_amount');
        $totalInvoices = $sales->count();

        return view('report.sale.daily_sales', compact('sales', 'totalSales', 'totalInvoices'));
    }

    public function monthlySalesReport(): View
    {
        $sales = SaleInvoice::whereMonth('created_at', Carbon::now()->month)->get();

        if ($sales->isEmpty()) {
            return view('report.sale.monthly_sales', [
                'message' => 'لا توجد مبيعات لهذا الشهؤ.',
            ]);
        }

        $totalSales = $sales->sum('total_amount');
        $totalInvoices = $sales->count();

        return view('report.sale.monthly_sales', compact('sales', 'totalSales', 'totalInvoices'));
    }

    public function yearlySalesReport(): View
    {
        $sales = SaleInvoice::whereMonth('created_at', Carbon::now()->month)->get();

        if ($sales->isEmpty()) {
            return view('report.sale.yearly_sales', [
                'message' => 'لا توجد مبيعات لهذه السنة.',
            ]);
        }

        $totalSales = $sales->sum('total_amount');
        $totalInvoices = $sales->count();

        return view('report.sale.yearly_sales', compact('sales', 'totalSales', 'totalInvoices'));
    }

    public function dailyPurchasesReport(): View
    {
        $purchases = PurchaseInvoice::whereDate('created_at', Carbon::today())->get();

        if ($purchases->isEmpty()) {
            return view('report.purchase.daily_purchases', [
                'message' => 'لا توجد مبيعات لهذا اليوم.',
            ]);
        }

        $totalPurchases = $purchases->sum('total_amount');
        $totalInvoices = $purchases->count();

        return view('report.purchase.daily_purchases', compact('purchases', 'totalPurchases', 'totalInvoices'));
    }

    public function monthlyPurchasesReport(): View
    {
        $purchases = PurchaseInvoice::whereMonth('created_at', Carbon::now()->month)->get();

        if ($purchases->isEmpty()) {
            return view('report.purchase.monthly_purchases', [
                'message' => 'لا توجد مبيعات لهذا الشهؤ.',
            ]);
        }

        $totalPurchases = $purchases->sum('total_amount');
        $totalInvoices = $purchases->count();

        return view('report.purchase.monthly_purchases', compact('purchases', 'totalPurchases', 'totalInvoices'));
    }

    public function yearlyPurchasesReport(): View
    {
        $purchases = PurchaseInvoice::whereMonth('created_at', Carbon::now()->month)->get();

        if ($purchases->isEmpty()) {
            return view('report.purchase.yearly_purchases', [
                'message' => 'لا توجد مبيعات لهذه السنة.',
            ]);
        }

        $totalPurchases = $purchases->sum('total_amount');
        $totalInvoices = $purchases->count();

        return view('report.purchase.yearly_purchases', compact('purchases', 'totalPurchases', 'totalInvoices'));
    }

    public function topProducts(): View
    {
        // جلب المنتجات الأكثر مبيعًا بناءً على عدد مرات الطلب
        $topProducts = SaleInvoiceDetail::select('product_id')
            ->selectRaw('COUNT(*) as total_orders')
            ->groupBy('product_id')
            ->orderByDesc('total_orders')
            ->with('product') // جلب بيانات المنتج
            ->take(10) // عرض أكثر 10 منتجات مبيعًا
            ->get();

        return view('report.product.top_products', compact('topProducts'));
    }

    public function customerSales(): View
    {
        return view('report.customer.index');
    }

    public function show_customerSales(Request $request): View
    {
        $start = $request->start;
        $end = $request->end;
        $sales = SaleInvoice::whereDate('created_at', '>=', $request->start)->whereDate('created_at', '<=', $request->end)->whereCustomer_id($request->tree4)->orderByDesc('id')->get();
        return view('report.customer.show', compact('sales', 'start', 'end'));
    }

    public function supplierPurchases(): View
    {
        return view('report.supplier.index');
    }

    public function show_supplierPurchases(Request $request): View
    {
        $start = $request->start;
        $end = $request->end;
        $purchases = PurchaseInvoice::whereDate('created_at', '>=', $request->start)->whereDate('created_at', '<=', $request->end)->whereSupplier_id($request->tree4)->orderByDesc('id')->get();
        return view('report.supplier.show', compact('purchases', 'start', 'end'));
    }

    public function expiringProducts(): View
    {
        $today = Carbon::today();
        $nextWeek = $today->copy()->addWeek(); // تاريخ بعد 7 أيام

        // جلب المنتجات التي ستنتهي صلاحيتها خلال أسبوع، مع استبعاد المنتجات المنتهية الصلاحية
        $products = Product::whereBetween('expire_date', [$today, $nextWeek])->get();

        return view('report.product.expiring-products', compact('products'));
    }

    public function expiredProducts(): View
    {
        $today = Carbon::today();

        // جلب المنتجات التي انتهت صلاحيتها
        $products = Product::where('expire_date', '<=', $today)->get();

        return view('report.product.expired_products', compact('products'));
    }

    public function lowStockProducts(): View
    {
        $products = Product::with('stocks')
            ->get()
            ->filter(function ($product) {
                $totalQuantity = $product->stocks->sum('pivot.quantity'); // جمع كميات المنتج عبر جميع المخازن
                return $totalQuantity < $product->reorder_level;
            });

        return view('report.product.low_stock_products', compact('products'));
    }

    public function sale(): View
    {
        return view('report.sale.index');
    }

    public function show_sale(Request $request): View
    {
        $start = $request->start;
        $end = $request->end;
        $sales = SaleInvoice::whereDate('created_at', '>=', $request->start)->whereDate('created_at', '<=', $request->end)->get();
        return view('report.sale.show', compact('sales', 'start', 'end'));
    }

    public function purchase(): View
    {
        return view('report.purchase.index');
    }

    public function show_purchase(Request $request): View
    {
        $start = $request->start;
        $end = $request->end;
        $purchase_returns = PurchaseInvoice::whereDate('created_at', '>=', $request->start)->whereDate('created_at', '<=', $request->end)->get();
        return view('report.purchase.show', compact('purchase_returns', 'start', 'end'));
    }

    public function saleReturn(): View
    {
        return view('report.sale_return.index');
    }

    public function showSaleReturn(Request $request): View
    {
        $start = $request->start;
        $end = $request->end;
        $sale_returns = SaleInvoiceReturn::whereDate('created_at', '>=', $request->start)->whereDate('created_at', '<=', $request->end)->get();
        return view('report.sale_return.show', compact('sale_returns', 'start', 'end'));
    }


    public function purchaseReturn(): View
    {
        return view('report.purchase_return.index');
    }

    public function showPurchaseReturn(Request $request): View
    {
        $start = $request->start;
        $end = $request->end;
        $pruchase_returns = PurchaseInvoiceReturn::whereDate('created_at', '>=', $request->start)->whereDate('created_at', '<=', $request->end)->get();
        return view('report.purchase_return.show', compact('pruchase_returns', 'start', 'end'));
    }
}
