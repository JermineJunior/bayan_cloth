<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\View\View;
use App\Models\StockDetail;
use Illuminate\Http\Request;

class BurrenController extends Controller
{
    public function show(): View
    {
        $stocks = Stock::get();
        $products = Product::get();
        return view('burren.show', compact('stocks', 'products'));
    }

    public function info(Request $request)
    {
        $start = $request->start;
        $end = $request->end;
        $stock_id = $request->stock_id ?? null;
        $product_id = $request->product_id ?? null;

        $burrens = StockDetail::when($request->stock_id, function ($q) use ($request) {
            $q->where('stock_id', $request->stock_id);
        })
            ->when($request->product_id, function ($q) use ($request) {
                $q->where('product_id', $request->product_id);
            })
            ->where('branch_id', session('branch_id'))
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->get();
        return view('burren.info', compact('burrens', 'start', 'end', 'stock_id','product_id'));
    }
}
