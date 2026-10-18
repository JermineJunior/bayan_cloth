<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Curencey;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Restriction;
use App\Models\SaleInvoice;
use App\Models\SaleInvoiceDetail;
use App\Models\SaleInvoiceReturn;
use App\Models\SaleReturnDetail;
use App\Models\Tree4;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SaleInvoiceReportController extends Controller
{
    public function customer_sale_index(): View
    {
        $customers = Tree4::where('tree3_code', 1205)->get();
        $curenceies = Curencey::get();
        $type = 1;
        return view('report.invoice.index_customer', compact('customers', 'type', 'curenceies'));
    }

    public function customer_sale_show(Request $request): View
    {
        $curencey = Curencey::find($request->curencey_id);
        $type = 1;
        $start = $request->start;
        $end = $request->end;
        $customer = Tree4::find($request->customer_id);
        $operations = SaleInvoice::with('details')
            ->where('branch_id', session('branch_id'))
            ->when($start, function ($query, $start) {
                $query->whereDate('date', '>=', $start);
            })
            ->when($end, function ($query2, $end) {
                $query2->whereDate('date', '<=', $end);
            })
            ->where('customer_id', $request->customer_id)
            ->get();
        return view('report.invoice.show_customer', compact('customer', 'operations', 'start', 'end', 'type', 'curencey'));
    }

    public function customer_return_index(): View
    {
        $customers = Tree4::where('tree3_code', 1205)->get();
        $curenceies = Curencey::get();
        $type = 3;
        return view('report.invoice.index_customer', compact('customers', 'type', 'curenceies'));
    }

    public function customer_return_show(Request $request): View
    {
        $type = 3;
        $curencey = Curencey::find($request->curencey_id);
        $start = $request->start;
        $end = $request->end;
        $customer = Tree4::find($request->customer_id);
        $operations = SaleInvoiceReturn::with('details')
            ->where('branch_id', session('branch_id'))
            ->where('customer_id', $request->customer_id)
            ->when($start, function ($query, $start) {
                $query->whereDate('date', '>=', $start);
            })
            ->when($end, function ($query2, $end) {
                $query2->whereDate('date', '<=', $end);
            })
            ->get();
        return view('report.invoice.show_customer', compact('customer', 'operations', 'start', 'end', 'type', 'curencey'));
    }

    public function top_customers_index(): View
    {
        $curenceies = Curencey::get();
        return view('report.invoice.index_top_customers', compact('curenceies'));
    }

    public function top_customers_show(Request $request): View
    {
        $curencey = Curencey::find($request->curencey_id);
        $start = $request->start;
        $end = $request->end;

        $limit = $request->limit ?: 10;
        $limit = in_array($limit, ['10', '20', '50'], true) ? (int) $limit : null;

        $customers = SaleInvoice::with('customer')
            ->where('branch_id', session('branch_id'))
            ->whereNotNull('customer_id')
            ->when($start, function ($query, $start) {
                $query->whereDate('date', '>=', $start);
            })
            ->when($end, function ($query, $end) {
                $query->whereDate('date', '<=', $end);
            })
            ->groupBy('customer_id')
            ->selectRaw('customer_id, COUNT(*) as invoices_count, SUM(total_amount) as total_sales')
            ->orderByDesc('total_sales')
            ->when($limit, function ($query, $limit) {
                $query->limit($limit);
            })
            ->get();

        $returns = SaleInvoiceReturn::where('branch_id', session('branch_id'))
            ->whereNotNull('customer_id')
            ->when($start, function ($query, $start) {
                $query->whereDate('date', '>=', $start);
            })
            ->when($end, function ($query, $end) {
                $query->whereDate('date', '<=', $end);
            })
            ->groupBy('customer_id')
            ->selectRaw('customer_id, SUM(total_amount) as total_returns')
            ->get()
            ->keyBy('customer_id');

        $customers = $customers->map(function ($customer) use ($returns) {
            $customer->total_returns = $returns->get($customer->customer_id)->total_returns ?? 0;
            $customer->net_sales = $customer->total_sales - $customer->total_returns;
            return $customer;
        });

        return view('report.invoice.show_top_customers', compact('customers', 'start', 'end', 'curencey'));
    }

    public function agent_index(): View
    {
        $type = 1;
        $curenceies = Curencey::get();
        $users = User::where('id', '!=', 1)
            ->whereHas('branches', function ($query) {
                $query->where('branch_id', session('branch_id'));
            })
            ->get();
        return view('report.invoice.index_agent', compact('users', 'type', 'curenceies'));
    }

    public function agent_show(Request $request): View
    {
        $type = 1;
        $curencey = Curencey::find($request->curencey_id);
        $start = $request->start;
        $end = $request->end;
        $user = User::find($request->user_id);
        $operations = SaleInvoice::with('details')->where('user_id', $request->user_id)->whereDate('date', '>=', $start)->whereDate('date', '<=', $end)->get();
        return view('report.invoice.show_agent', compact('user', 'operations', 'start', 'end', 'type', 'curencey'));
    }

    public function agent_return_index(): View
    {
        $type = 3;
        $curenceies = Curencey::get();
        $users = User::where('id', '!=', 1)
            ->whereHas('branches', function ($query) {
                $query->where('branch_id', session('branch_id'));
            })
            ->get();
        return view('report.invoice.index_agent', compact('users', 'type', 'curenceies'));
    }

    public function agent_return_show(Request $request): View
    {
        $type = 3;
        $curencey = Curencey::find($request->curencey_id);
        $start = $request->start;
        $end = $request->end;
        $user = User::find($request->user_id);
        $operations = SaleInvoiceReturn::with('details')->where('user_id', $request->user_id)->whereDate('date', '>=', $start)->whereDate('date', '<=', $end)->get();
        return view('report.invoice.show_agent', compact('user', 'operations', 'start', 'end', 'type', 'curencey'));
    }

    public function category_sale_index(): View
    {
        $type = 1;
        $categories = Category::get();
        $curenceies = Curencey::get();
        return view('report.invoice.index_category', compact('categories', 'type', 'curenceies'));
    }

    public function category_sale_show(Request $request): View
    {
        $type = 1;
        $curencey = Curencey::find($request->curencey_id);
        $start = $request->start;
        $end = $request->end;
        $category = Category::find($request->category_id);
        $operations = SaleInvoiceDetail::with('product.category', 'product')
            ->whereHas('product.category', function ($q) use ($request) {
                $q->where('id', $request->category_id);
            })
            ->whereHas('sale_invoice', function ($q2) use ($request) {
                $q2->whereDate('date', '>=', $request->start)->whereDate('date', '<=', $request->end)->where('branch_id', session('branch_id'));
            })
            ->selectRaw('product_id, SUM(qty) as total_qty, SUM(row_sub_total) as total_amount')
            ->groupBy('product_id')
            ->get();
        return view('report.invoice.show_category', compact('start', 'end', 'category', 'operations', 'type', 'curencey'));
    }

    public function product_sale_index(): View
    {
        $type = 1;
        $products = Product::get();
        $curenceies = Curencey::get();
        return view('report.invoice.index_product', compact('products', 'type', 'curenceies'));
    }

    public function product_sale_show(Request $request): View
    {
        $type = 1;
        $curencey = Curencey::find($request->curencey_id);
        $product_name = Product::find($request->product_id)->value('name');
        $start = $request->start;
        $end = $request->end;
        $operations = SaleInvoiceDetail::with('sale_invoice')
            ->where('product_id', $request->product_id)
            ->whereHas('sale_invoice', function ($q2) use ($request) {
                $q2->whereDate('date', '>=', $request->start)->whereDate('date', '<=', $request->end)->where('branch_id', session('branch_id'));
            })
            ->get();
        return view('report.invoice.show_product', compact('product_name', 'start', 'end', 'operations', 'type', 'curencey'));
    }

    public function category_return_index(): View
    {
        $type = 3;
        $categories = Category::get();
        $curenceies = Curencey::get();
        return view('report.invoice.index_category', compact('categories', 'type', 'curenceies'));
    }

    public function category_return_show(Request $request): View
    {
        $type = 3;
        $curencey = Curencey::find($request->curencey_id);
        $start = $request->start;
        $end = $request->end;
        $category = Category::find($request->category_id);
        $operations = SaleReturnDetail::with('product.category', 'product')
            ->whereHas('product.category', function ($q) use ($request) {
                $q->where('id', $request->category_id);
            })
            ->whereHas('sale_invoice_return', function ($q) use ($start, $end) {
                $q->whereDate('date', '>=', $start)->whereDate('date', '<=', $end)->where('branch_id', session('branch_id'));
            })
            ->selectRaw('product_id, SUM(qty) as total_qty, SUM(row_sub_total) as total_amount')
            ->groupBy('product_id')
            ->get();
        return view('report.invoice.show_category', compact('start', 'end', 'category', 'operations', 'type', 'curencey'));
    }

    public function details_sale_index(): View
    {
        $type = 1;
        $curenceies = Curencey::get();
        return view('report.invoice.index_details', compact('type', 'curenceies'));
    }

    public function details_sale_show(Request $request): View
    {
        $type = 1;
        $start = $request->start;
        $end = $request->end;
        $curencey = Curencey::find($request->curencey_id);
        $operations = SaleInvoice::with('details')->where('branch_id', session('branch_id'))->whereDate('date', '>=', $start)->whereDate('date', '<=', $end)->get();
        return view('report.invoice.show_details', compact('start', 'end', 'operations', 'type', 'curencey'));
    }

    public function details_return_index(): View
    {
        $type = 3;
        $curenceies = Curencey::get();
        return view('report.invoice.index_details', compact('type', 'curenceies'));
    }

    public function details_return_show(Request $request): View
    {
        $type = 3;
        $curencey = Curencey::find($request->curencey_id);
        $start = $request->start;
        $end = $request->end;
        $operations = SaleInvoiceReturn::with('details')->where('branch_id', session('branch_id'))->whereDate('date', '>=', $start)->whereDate('date', '<=', $end)->get();
        return view('report.invoice.show_details', compact('start', 'end', 'operations', 'type', 'curencey'));
    }

    public function summary_sale_index(): View
    {
        $type = 1;
        $curenceies = Curencey::get();
        return view('report.invoice.index_summary', compact('type', 'curenceies'));
    }

    public function summary_sale_show(Request $request): View
    {
        $type = 1;
        $curencey = Curencey::find($request->curencey_id);
        $start = $request->start;
        $end = $request->end;
        $operations = SaleInvoice::with('details')->where('branch_id', session('branch_id'))->whereDate('date', '>=', $start)->whereDate('date', '<=', $end)->get();
        foreach ($operations as $operation) {
            $cost_amount = 0;
            foreach ($operation->details as $item) {
                $product = Product::with('units')->find($item->product_id);
                $unit = DB::table('product_units')->where('product_id', $product->id)->where('unit_id', $item->unit_id)->first();

                if ($unit) {
                    $item_cost = $unit->price_purchase * ($item->qty * $unit->conversion_factor);
                } else {
                    $item_cost = 0;
                }
                $cost_amount += $item_cost;
            }
            $operation->profit = $operation->total_amount - $cost_amount;
        }
        return view('report.invoice.show_summary', compact('start', 'end', 'operations', 'type', 'curencey'));
    }

    public function summary_return_index(): View
    {
        $type = 3;
        $curenceies = Curencey::get();
        return view('report.invoice.index_summary', compact('type', 'curenceies'));
    }

    public function summary_return_show(Request $request): View
    {
        $type = 3;
        $curencey = Curencey::find($request->curencey_id);
        $start = $request->start;
        $end = $request->end;
        $operations = SaleInvoiceReturn::where('branch_id', session('branch_id'))->whereDate('date', '>=', $start)->whereDate('date', '<=', $end)->get();
        return view('report.invoice.show_summary', compact('start', 'end', 'operations', 'type', 'curencey'));
    }

    public function representative_index()
    {
        $type = 1;
        $curenceies = Curencey::get();
        $representatives = Tree4::whereTree3_code('1206')->get();
        return view('report.representative.index', compact('representatives', 'type', 'curenceies'));
    }

    public function representative_show(Request $request)
    {
        $type = 1;
        $curencey = Curencey::find($request->curencey_id);
        $start = $request->start;
        $end = $request->end;
        $operations = SaleInvoice::with('details', 'details.product', 'details.unit', 'representative')->where('branch_id', session('branch_id'))->where('representative_id', $request->representative_id)->whereDate('date', '>=', $start)->whereDate('date', '<=', $end)->get();
        return view('report.representative.show', compact('start', 'end', 'operations', 'type', 'curencey'));
    }

    public function representative_return_index()
    {
        $type = 2;
        $curenceies = Curencey::get();
        $representatives = Tree4::whereTree3_code('1206')->get();
        return view('report.representative.index', compact('representatives', 'type', 'curenceies'));
    }

    public function representative_return_show(Request $request)
    {
        $type = 2;
        $curencey = Curencey::find($request->curencey_id);
        $start = $request->start;
        $end = $request->end;
        $operations = SaleInvoiceReturn::with('details', 'details.product', 'details.unit', 'representative')->where('branch_id', session('branch_id'))->where('representative_id', $request->representative_id)->whereDate('date', '>=', $start)->whereDate('date', '<=', $end)->get();
        return view('report.representative.show', compact('start', 'end', 'operations', 'type', 'curencey'));
    }

    public function representative_customer()
    {
        $representatives = Tree4::whereTree3_code('1206')->get();
        $curenceies = Curencey::get();
        return view('report.representative.customer_index', compact('representatives', 'curenceies'));
    }

    public function representative_customer_show(Request $request)
    {
        $curencey = Curencey::find($request->curencey_id);
        $representative = Tree4::find($request->representative_id);

        // جلب العملاء من فواتير البيع وفواتير الإرجاع
        $sale_invoice_ids = SaleInvoice::where('branch_id', session('branch_id'))->where('representative_id', $request->representative_id)->pluck('customer_id');
        $sale_invoice_return_ids = SaleInvoiceReturn::where('branch_id', session('branch_id'))->where('representative_id', $request->representative_id)->pluck('customer_id');

        // دمج وتصفية التكرارات
        $customer_ids = $sale_invoice_ids->merge($sale_invoice_return_ids)->unique();

        // جلب القيود الخاصة بالعملاء
        $restrictions = Restriction::whereIn('tree4_id', $customer_ids)->get();

        $results = [];

        foreach ($restrictions->groupBy('tree4_id') as $tree4_id => $group) {
            $total_madin = $group->sum('madin');
            $total_dain = $group->sum('dain');

            // استبعاد الحالات التي يكون فيها الفرق صفر
            if ($total_madin == $total_dain) {
                continue;
            }

            $tree = Tree4::find($tree4_id);

            if (!$tree) {
                continue;
            }

            // تحديد الرصيد: له أو عليه
            $balance_diff = $total_dain - $total_madin;

            $last_transaction_date = $group->sortByDesc('date')->first()->date ?? null;

            $results[] = [
                'customer_name' => $tree->tree4_name,
                'customer_phone' => $tree->phone,
                'customer_address' => $tree->address,
                'to' => $balance_diff > 0 ? abs($balance_diff) : null,
                'from' => $balance_diff < 0 ? abs($balance_diff) : null,
                'last_transaction' => $last_transaction_date,
            ];
        }

        return view('report.representative.customer_show', compact('results', 'representative', 'curencey'));
    }
}
