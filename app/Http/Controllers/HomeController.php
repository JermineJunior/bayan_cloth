<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Restriction;
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
        // أسماء الأشهر بالعربية
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
        $sales = SaleInvoice::selectRaw('SUM(total_amount) as total, MONTH(created_at) as month')->groupBy('month')->orderBy('month')->get()->keyBy('month');

        // إحضار بيانات المشتريات حسب الأشهر
        $purchases = PurchaseInvoice::selectRaw('SUM(total_amount) as total, MONTH(created_at) as month')->groupBy('month')->orderBy('month')->get()->keyBy('month');

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
        $totalSales = SaleInvoice::whereMonth('created_at', now()->month)->count();
        $totalSalesReturns = SaleInvoiceReturn::whereMonth('created_at', now()->month)->count();

        // حساب إجمالي المشتريات والمردودات
        $totalPurchases = PurchaseInvoice::whereMonth('created_at', now()->month)->count();
        $totalPurchaseReturns = PurchaseInvoiceReturn::whereMonth('created_at', now()->month)->count();

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

        return view('dashboard.index', compact('totalSales','totalSalesReturns','totalPurchases','totalPurchaseReturns','salesChart', 'purchaseChart', 'salesComparisonChart', 'purchaseComparisonChart'));
    }
}
