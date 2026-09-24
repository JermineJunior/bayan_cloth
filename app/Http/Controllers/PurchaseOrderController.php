<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Representative;
use App\Models\Tree4;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $orders = PurchaseOrder::get();
        return view('purchase_order.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = Tree4::whereTree3_code(2301)->get();
        $products = Product::with('stocks', 'units')->take(7)->inRandomOrder()->get();
        return view('purchase_order.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $purchaseOrder = PurchaseOrder::create($request->all());

        foreach ($request->product_id as $index => $product_id) {
            $purchaseOrder->details()->create([
                'product_id' => $product_id,
                'qty' => $request->qty[$index],
            ]);
        }

        return redirect()->route('purchase_order.show',$purchaseOrder->id);
    }

    public function show(PurchaseOrder $purchase_order)
    {
        $purchase_order = $purchase_order->load('details');
        return view('purchase_order.print',compact('purchase_order'));
    }

    public function edit(PurchaseOrder $purchase_order, Request $request) 
    {
        $purchase_order = $purchase_order->load('details');
        $suppliers = Tree4::whereTree3_code(2301)->get();
        $products = Product::with('stocks', 'units')->take(7)->inRandomOrder()->get();
        return view('purchase_order.edit', compact('purchase_order','suppliers','products'));
    }

    public function update(Request $request, PurchaseOrder $purchase_order)
    {
        $purchase_order->delete();
        $purchaseOrder = PurchaseOrder::create($request->all());

        foreach ($request->product_id as $index => $product_id) {
            $purchaseOrder->details()->create([
                'product_id' => $product_id,
                'qty' => $request->qty[$index],
            ]);
        }

        return redirect()->route('purchase_order.index')->with('success_updated', 'ok');
    }

    public function destroy(PurchaseOrder $purchase_order)
    {
        $purchase_order->delete();
        return redirect()->route('purchase_order.index')->with('success_deleted','ok');
    }
}
