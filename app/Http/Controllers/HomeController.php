<?php

namespace App\Http\Controllers;

use App\Models\SaleInvoice;
use App\Models\PurchaseInvoice;
use App\Models\SaleInvoiceReturn;
use App\Models\PurchaseInvoiceReturn;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $arabicMonths = [
            1 => 'يناير',
            2 => 'فبراير',
            3 => 'مارس',
            4 => 'أبريل',
            5 => 'مايو',
            6 => 'يونيو',
            7 => 'يوليو',
            8 => 'أغسطس',
            9 => 'سبتمبر',
            10 => 'أكتوبر',
            11 => 'نوفمبر',
            12 => 'ديسمبر',
        ];

        // إحضار بيانات المبيعات حسب الأشهر
        $sales = SaleInvoice::selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')->whereYear('created_at', now()->year)->where('branch_id', session('branch_id'))->groupByRaw('MONTH(created_at)')->orderByRaw('MONTH(created_at)')->get()->keyBy('month');
        // إحضار بيانات المشتريات حسب الأشهر
        $purchases = PurchaseInvoice::selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')->whereYear('created_at', now()->year)->where('branch_id', session('branch_id'))->groupByRaw('MONTH(created_at)')->orderByRaw('MONTH(created_at)')->get()->keyBy('month');
        // تجهيز بيانات الأشهر لكل المخططات
        $months = [];
        $salesTotals = [];
        $purchaseTotals = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[] = $arabicMonths[$i];
            $salesTotals[] = $sales[$i]->total ?? 0; // إجمالي المبيعات لكل شهر
            $purchaseTotals[] = $purchases[$i]->total ?? 0; // إجمالي المشتريات لكل شهر
        }

        // حساب إجمالي المبيعات والمردودات
        $totalSales = SaleInvoice::whereMonth('created_at', now()->month)->where('branch_id', session('branch_id'))->count();
        $totalSalesReturns = SaleInvoiceReturn::whereMonth('created_at', now()->month)->where('branch_id', session('branch_id'))->count();

        // حساب إجمالي المشتريات والمردودات
        $totalPurchases = PurchaseInvoice::whereMonth('created_at', now()->month)->where('branch_id', session('branch_id'))->count();
        $totalPurchaseReturns = PurchaseInvoiceReturn::whereMonth('created_at', now()->month)->where('branch_id', session('branch_id'))->count();

        // مخطط المبيعات (Bar Chart)
        $salesChart = new Chart();
        $salesChart
            ->labels($months)
            ->dataset('إجمالي المبيعات', 'bar', $salesTotals)
            ->options([
                'responsive' => true,
                'maintainAspectRatio' => false,
            ]);

        // مخطط المشتريات (Bar Chart)
        $purchaseChart = new Chart();
        $purchaseChart
            ->labels($months)
            ->dataset('إجمالي المشتريات', 'bar', $purchaseTotals)
            ->options([
                'responsive' => true,
                'maintainAspectRatio' => false,
            ]);

        // مخطط مقارنة المبيعات ومردودات المبيعات (Pie Chart)
        $salesComparisonChart = new Chart();
        $salesComparisonChart
            ->labels(['إجمالي المبيعات', 'مردودات المبيعات'])
            ->dataset('المبيعات مقابل مردودات المبيعات', 'pie', [$totalSales, $totalSalesReturns])
            ->options([
                'responsive' => true,
                'maintainAspectRatio' => false,
            ]);

        // مخطط مقارنة المشتريات ومردودات المشتريات (Pie Chart)
        $purchaseComparisonChart = new Chart();
        $purchaseComparisonChart
            ->labels(['إجمالي المشتريات', 'مردودات المشتريات'])
            ->dataset('المشتريات مقابل مردودات المشتريات', 'pie', [$totalPurchases, $totalPurchaseReturns])
            ->options([
                'responsive' => true,
                'maintainAspectRatio' => false,
            ]);
        return view('dashboard.index', compact('totalSales', 'totalSalesReturns', 'totalPurchases', 'totalPurchaseReturns', 'salesChart', 'purchaseChart', 'salesComparisonChart', 'purchaseComparisonChart'));
    }
}
