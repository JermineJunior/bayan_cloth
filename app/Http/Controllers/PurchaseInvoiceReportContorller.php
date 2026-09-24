<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Curencey;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceDetail;
use App\Models\PurchaseInvoiceReturn;
use App\Models\PurchaseReturnDetail;
use App\Models\Tree4;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PurchaseInvoiceReportContorller extends Controller
{
    public function supplier_purchase_index(): View
    {
        $type = 2;
        $curenceies = Curencey::get();
        $customers = Tree4::where('tree3_code', 2301)->get();
        return view('report.invoice.index_customer', compact('customers', 'type', 'curenceies'));
    }

    public function supplier_purchase_show(Request $request): View
    {
        $type = 2;
        $curencey = Curencey::find($request->curencey_id);
        $start = $request->start;
        $end = $request->end;
        $customer = Tree4::find($request->customer_id);
        $operations = PurchaseInvoice::with('details')
            ->where('branch_id', session('branch_id'))
            ->where('supplier_id', $request->customer_id)
            ->when($start, function ($query, $start) {
                $query->whereDate('date', '>=', $start);
            })
            ->when($end, function ($query2, $end) {
                $query2->whereDate('date', '<=', $end);
            })
            ->get();
        return view('report.invoice.show_customer', compact('customer', 'operations', 'start', 'end', 'type', 'curencey'));
    }

    public function supplier_return_index(): View
    {
        $type = 4;
        $curenceies = Curencey::get();
        $customers = Tree4::where('tree3_code', 2301)->get();
        return view('report.invoice.index_customer', compact('customers', 'type', 'curenceies'));
    }

    public function supplier_return_show(Request $request): View
    {
        $type = 4;
        $curencey = Curencey::find($request->curencey_id);
        $start = $request->start;
        $end = $request->end;
        $customer = Tree4::find($request->customer_id);
        $operations = PurchaseInvoiceReturn::with('details')
            ->where('branch_id', session('branch_id'))
            ->where('supplier_id', $request->customer_id)
            ->when($start, function ($query, $start) {
                $query->whereDate('date', '>=', $start);
            })
            ->when($end, function ($query2, $end) {
                $query2->whereDate('date', '<=', $end);
            })
            ->get();
        return view('report.invoice.show_customer', compact('customer', 'operations', 'start', 'end', 'type', 'curencey'));
    }

    public function agent_index(): View
    {
        $type = 2;
        $curenceies = Curencey::get();
        $users = User::whereHas('branches', function ($query) {
            $query->where('branch_id', session('branch_id'));
        })
            ->where('id', '!=', 1)
            ->get();
        return view('report.invoice.index_agent', compact('users', 'type', 'curenceies'));
    }

    public function agent_show(Request $request): View
    {
        $type = 2;
        $curencey = Curencey::find($request->curencey_id);
        $start = $request->start;
        $end = $request->end;
        $user = User::find($request->user_id);
        $operations = PurchaseInvoice::with('details')->where('user_id', $request->user_id)->whereDate('date', '>=', $start)->whereDate('date', '<=', $end)->get();
        return view('report.invoice.show_agent', compact('user', 'operations', 'start', 'end', 'type', 'curencey'));
    }

    public function agent_return_index(): View
    {
        $type = 4;
        $curenceies = Curencey::get();
        $users = User::whereHas('branches', function ($query) {
            $query->where('branch_id', session('branch_id'));
        })
            ->where('id', '!=', 1)
            ->get();
        return view('report.invoice.index_agent', compact('users', 'type', 'curenceies'));
    }

    public function agent_return_show(Request $request): View
    {
        $type = 4;
        $curencey = Curencey::find($request->curencey_id);
        $start = $request->start;
        $end = $request->end;
        $user = User::find($request->user_id);
        $operations = PurchaseInvoiceReturn::with('details')->where('user_id', $request->user_id)->whereDate('date', '>=', $start)->whereDate('date', '<=', $end)->get();
        return view('report.invoice.show_agent', compact('user', 'operations', 'start', 'end', 'type', 'curencey'));
    }

    public function category_purchase_index(): View
    {
        $type = 2;
        $curenceies = Curencey::get();
        $categories = Category::get();
        return view('report.invoice.index_category', compact('categories', 'type', 'curenceies'));
    }

    public function category_purchase_show(Request $request)
    {
        $type = 2;
        $curencey = Curencey::find($request->curencey_id);
        $start = $request->start;
        $end = $request->end;
        $category = Category::find($request->category_id);
        $operations = PurchaseInvoiceDetail::with('product.category','product')
            ->whereHas('product.category', function ($q) use ($request) {
                $q->where('id', $request->category_id);
            })
            ->whereHas('purchase_invoice', function ($q) use ($start, $end) {
                $q->whereDate('date', '>=', $start)->whereDate('date', '<=', $end)->where('branch_id', session('branch_id'));
            })
            ->selectRaw('product_id, SUM(qty) as total_qty, SUM(row_sub_total) as total_amount')
            ->groupBy('product_id')
            ->get();
        return view('report.invoice.show_category', compact('start', 'end', 'category', 'operations', 'type', 'curencey'));
    }

    public function product_purchase_index(): View
    {
        $type = 2;
        $products = Product::get();
        $curenceies = Curencey::get();
        return view('report.invoice.index_product', compact('products', 'type', 'curenceies'));
    }

    public function product_purchase_show(Request $request): View
    {
        $type = 2;
        $curencey = Curencey::find($request->curencey_id);
        $product_name = Product::find($request->product_id)->value('name');
        $start = $request->start;
        $end = $request->end;
        $operations = PurchaseInvoiceDetail::with('purchase_invoice')
            ->where('product_id', $request->product_id)
            ->whereHas('purchase_invoice', function ($q2) use ($request) {
                $q2->whereDate('date', '>=', $request->start)->whereDate('date', '<=', $request->end)->where('branch_id', session('branch_id'));
            })
            ->get();
        return view('report.invoice.show_product', compact('product_name', 'start', 'end', 'operations', 'type', 'curencey'));
    }

    public function category_return_index(): View
    {
        $type = 4;
        $categories = Category::get();
        $curenceies = Curencey::get();
        return view('report.invoice.index_category', compact('categories', 'type', 'curenceies'));
    }

    public function category_return_show(Request $request)
    {
        $type = 4;
        $curencey = Curencey::find($request->curencey_id);
        $start = $request->start;
        $end = $request->end;
        $category = Category::find($request->category_id);
        $operations = PurchaseReturnDetail::with('product.category','product')
            ->whereHas('product.category', function ($q) use ($request) {
                $q->where('id', $request->category_id);
            })
            ->whereHas('purchase_invoice_return', function ($q) use ($start, $end) {
                $q->whereDate('date', '>=', $start)->whereDate('date', '<=', $end)->where('branch_id', session('branch_id'));
            })
            ->selectRaw('product_id, SUM(qty) as total_qty,SUM(row_sub_total) as total_amount')
            ->groupBy('product_id')
            ->get();
        return view('report.invoice.show_category', compact('start', 'end', 'category', 'operations', 'type', 'curencey'));
    }

    public function details_purchase_index(): View
    {
        $type = 2;
        $curenceies = Curencey::get();
        return view('report.invoice.index_details', compact('type', 'curenceies'));
    }

    public function details_purchase_show(Request $request): View
    {
        $type = 2;
        $curencey = Curencey::find($request->curencey_id);
        $start = $request->start;
        $end = $request->end;
        $operations = PurchaseInvoice::where('branch_id', session('branch_id'))->with('details')->whereDate('date', '>=', $start)->whereDate('date', '<=', $end)->get();
        return view('report.invoice.show_details', compact('start', 'end', 'operations', 'type', 'curencey'));
    }

    public function details_return_index(): View
    {
        $type = 4;
        $curenceies = Curencey::get();
        return view('report.invoice.index_details', compact('type', 'curenceies'));
    }

    public function details_return_show(Request $request): View
    {
        $type = 4;
        $curencey = Curencey::find($request->curencey_id);
        $start = $request->start;
        $end = $request->end;
        $operations = PurchaseInvoiceReturn::with('details')->where('branch_id', session('branch_id'))->whereDate('date', '>=', $start)->whereDate('date', '<=', $end)->get();
        return view('report.invoice.show_details', compact('start', 'end', 'operations', 'type', 'curencey'));
    }

    public function summary_purchase_index(): View
    {
        $type = 2;
        $curenceies = Curencey::get();
        return view('report.invoice.index_summary', compact('type', 'curenceies'));
    }

    public function summary_purchase_show(Request $request): View
    {
        $type = 2;
        $curencey = Curencey::find($request->curencey_id);
        $start = $request->start;
        $end = $request->end;
        $operations = PurchaseInvoice::where('branch_id', session('branch_id'))->whereDate('date', '>=', $start)->whereDate('date', '<=', $end)->get();
        return view('report.invoice.show_summary', compact('start', 'end', 'operations', 'type', 'curencey'));
    }

    public function summary_return_index(): View
    {
        $type = 4;
        $curenceies = Curencey::get();
        return view('report.invoice.index_summary', compact('type', 'curenceies'));
    }

    public function summary_return_show(Request $request): View
    {
        $type = 4;
        $curencey = Curencey::find($request->curencey_id);
        $start = $request->start;
        $end = $request->end;
        $operations = PurchaseInvoiceReturn::where('branch_id', session('branch_id'))->whereDate('date', '>=', $start)->whereDate('date', '<=', $end)->get();
        return view('report.invoice.show_summary', compact('start', 'end', 'operations', 'type', 'curencey'));
    }
}
