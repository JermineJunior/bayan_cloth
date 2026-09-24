<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Transform;
use Illuminate\View\View;
use App\Models\StockDetail;
use Illuminate\Http\Request;
use App\Models\Transform_details;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Stock\StoreStockRequest;
use App\Http\Requests\Stock\UpdateStockRequest;
use App\Http\Requests\Transform\StoreTransformRequest;

class StockController extends Controller
{
    public function index(): View
    {
        $stocks = Stock::get();
        return view('stock.index', compact('stocks'));
    }

    public function store(StoreStockRequest $request): RedirectResponse
    {
        $stock = Stock::create(attributes: ['name' => $request->name]);
        $products = Product::get();
        foreach ($products as $product) {
            if ($product->stocks()->count() > 0) {
                $product->stocks()->attach($stock->id, [
                    'quantity' => 0,
                    'unit_stock' => $product->units()->first()->pivot->unit_id ?? 0,
                ]);
            }
        }
        return back()->with('success_created', 'تمت اضافة البيانات بنجاح');
    }

    public function show($id)
    {
        $stock = Stock::with('products.stocks')->findOrFail($id);
        return view('stock.show', compact('stock'));
    }

    public function update(UpdateStockRequest $request, Stock $stock): RedirectResponse
    {
        $stock->update(['name' => $request->name]);
        return back()->with('success_updated', 'تم تحديث البيانات بنجاج');
    }

    public function destroy(Stock $stock): RedirectResponse
    {
        if ($stock->products()->count() > 0) {
            return back()->withErrors('لا يمكن حذف هذا المخزن لانه مرتبط ببعض المنتجات');
        }
        
        $stock->delete();
        return back()->with('success_deleted', 'تم الحذف بنجاح ');
    }
    public function transform_list(): View
    {
        $transforms = Transform::with('fromStock', 'toStock')->where('branch_id', session('branch_id'))->get();
        return view('stock.transform.list', compact('transforms'));
    }
    public function transform_create(): View
    {
        $stocks_from = Stock::get();
        $products = Product::with('stocks', 'units')
            ->whereHas('stocks', function ($q) use ($stocks_from) {
                $q->where('stock_id', $stocks_from[0]);
            })
            ->inRandomOrder()
            ->take(7)
            ->get();
        $stocks_to = Stock::where('id', '!=', $stocks_from->first()->id)
            ->get();
        return view('stock.transform.create', compact('products', 'stocks_from', 'stocks_to'));
    }

    public function transform_store(StoreTransformRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $data = [];
            $transform = Transform::create([
                'stock_from' => $request->stock_id_from,
                'stock_to' => $request->stock_id_to,
                'branch_id' => session('branch_id'),
            ]);

            foreach ($request->product_id as $index => $product_id) {
                $product = Product::with('stocks', 'units')->find($product_id);

                $conversion_factor = $product->units()->firstWhere('unit_id', $request->unit_id[$index])->pivot->conversion_factor;
                $qty = $request->qty[$index] * $conversion_factor;
                StockDetail::create([
                    'product_id' => $product_id,
                    'unit_id' => $request->unit_id[$index],
                    'stock_id' => $request->stock_id_from,
                    'sader' => $qty,
                    'user_id' => Auth::id(),
                    'type' => 6,
                    'branch_id' => session('branch_id'),
                    'transform_id' => $transform->id,
                ]);

                StockDetail::create([
                    'product_id' => $product_id,
                    'unit_id' => $request->unit_id[$index],
                    'stock_id' => $request->stock_id_to,
                    'wared' => $qty,
                    'user_id' => Auth::id(),
                    'type' => 6,
                    'branch_id' => session('branch_id'),
                    'transform_id' => $transform->id,
                ]);

                // الحصول على المخازن المصدر والمخازن المستهدف من العلاقة
                $fromStock = $product->stocks()->where('stock_id', $request->stock_id_from)->first();
                $toStock = $product->stocks()->where('stock_id', $request->stock_id_to)->first();
                $newQuantityFrom = $fromStock->pivot->quantity - $qty;
                if ($qty > $fromStock->pivot->quantity) {
                    DB::rollBack();
                    return back()->withErrors([
                        'error' => 'الكمية باحد المنتجات أكبر من الكمية الموجودة في المخزن.',
                    ]);
                }
                $product->stocks()->syncWithoutDetaching([
                    $request->stock_id_from => ['quantity' => $newQuantityFrom],
                ]);

                $newQuantityto = $toStock->pivot->quantity + $qty;
                $product->stocks()->syncWithoutDetaching([
                    $request->stock_id_to => ['quantity' => $newQuantityto],
                ]);

                $data[] = [
                    'transform_id' => $transform->id,
                    'product_id' => $product_id,
                    'quantity' => $qty,
                    'unit_id' => $request->unit_id[$index],
                    'stock_id_from' => $request->stock_id_from,
                    'stock_id_to' => $request->stock_id_to,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // إدخال البيانات دفعة واحدة
            Transform_details::insert($data);

            DB::commit();
            return redirect()->route('stock.transform.list')->with('success_created', 'Transformation successful');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors($e->getMessage());
        }
    }

    public function transform_edit(Transform $transform)
    {
        $products = Product::with('stocks', 'units')->inRandomOrder()->take(7)->get();
        $stocks_from = Stock::get();
        $stocks_to = Stock::where('id', '!=', $transform->stock_from)->get();
        $units = Unit::get();
        $transform = $transform->load(['transformDetails']);
        return view('stock.transform.edit', compact('transform', 'stocks_from', 'stocks_to', 'units'));
    }

    public function transform_update(Request $request, Transform $transform)
    {
        try {
            DB::beginTransaction();
            foreach ($transform->transformDetails as $item) {
                $product = Product::with('units', 'stocks')->find($item->product_id);
                $productStockFrom = $product->stocks->where('id', $request->stock_id_from)->first();
                $productStockTo = $product->stocks->where('id', $request->stock_id_to)->first();
                $newQuantityFrom = $productStockFrom->pivot->quantity + $item->quantity;
                $newQuantityTo = $productStockTo->pivot->quantity - $item->quantity;
                $product->stocks()->syncWithoutDetaching([
                    $request->stock_id_from => ['quantity' => $newQuantityFrom],
                ]);
                $product->stocks()->syncWithoutDetaching([
                    $request->stock_id_to => ['quantity' => $newQuantityTo],
                ]);
            }

            $transform->delete();

            // الجزئية دي عشان احسب

            $data = [];
            $transform = Transform::create([
                'stock_from' => $request->stock_id_from,
                'stock_to' => $request->stock_id_to,
                'branch_id' => session('branch_id'),
            ]);

            foreach ($request->product_id as $index => $product_id) {
                $product = Product::with('stocks', 'units')->find($product_id);

                $conversion_factor = $product->units()->firstWhere('unit_id', $request->unit_id[$index])->pivot->conversion_factor;
                $qty = $request->qty[$index] * $conversion_factor;
                StockDetail::create([
                    'product_id' => $product_id,
                    'unit_id' => $request->unit_id[$index],
                    'stock_id' => $request->stock_id_from,
                    'sader' => $qty,
                    'user_id' => Auth::id(),
                    'type' => 6,
                    'branch_id' => session('branch_id'),
                    'transform_id' => $transform->id,
                ]);

                StockDetail::create([
                    'product_id' => $product_id,
                    'unit_id' => $request->unit_id[$index],
                    'stock_id' => $request->stock_id_to,
                    'wared' => $qty,
                    'user_id' => Auth::id(),
                    'type' => 6,
                    'branch_id' => session('branch_id'),
                    'transform_id' => $transform->id,
                ]);

                // الحصول على المخازن المصدر والمخازن المستهدف من العلاقة
                $fromStock = $product->stocks()->where('stock_id', $request->stock_id_from)->first();
                $toStock = $product->stocks()->where('stock_id', $request->stock_id_to)->first();
                if ($qty > $fromStock->pivot->quantity) {
                    DB::rollBack();
                    return back()->withErrors([
                        'error' => 'الكمية باحد المنتجات أكبر من الكمية الموجودة في المخزن.',
                    ]);
                }
                $newQuantityFrom = $fromStock->pivot->quantity - $qty;
                $product->stocks()->syncWithoutDetaching([
                    $request->stock_id_from => ['quantity' => $newQuantityFrom],
                ]);

                $newQuantityto = $toStock->pivot->quantity + $qty;
                $product->stocks()->syncWithoutDetaching([
                    $request->stock_id_to => ['quantity' => $newQuantityto],
                ]);

                $data[] = [
                    'transform_id' => $transform->id,
                    'product_id' => $product_id,
                    'quantity' => $qty,
                    'unit_id' => $request->unit_id[$index],
                    'stock_id_from' => $request->stock_id_from,
                    'stock_id_to' => $request->stock_id_to,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // إدخال البيانات دفعة واحدة
            Transform_details::insert($data);

            DB::commit();
            return redirect()->route('stock.transform.list')->with('success_created', 'Transformation successful');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors($e->getMessage());
        }
    }

    public function destory(Transform $transform)
    {
        try {
            DB::beginTransaction();
            foreach ($transform->transformDetails as $item) {
                $product = Product::with('units', 'stocks')->find($item->product_id);
                $productStockFrom = $product->stocks->where('id', $transform->stock_from)->first();
                $productStockTo = $product->stocks->where('id', $transform->stock_to)->first();
                $newQuantityFrom = $productStockFrom->pivot->quantity + $item->quantity;
                $newQuantityTo = $productStockTo->pivot->quantity - $item->quantity;
                $product->stocks()->syncWithoutDetaching([
                    $transform->stock_from => ['quantity' => $newQuantityFrom],
                ]);
                $product->stocks()->syncWithoutDetaching([
                    $transform->stock_to => ['quantity' => $newQuantityTo],
                ]);
            }

            $transform->delete();
            Db::commit();

            return back()->with('success_deleted', 'ok');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors($e->getMessage());
        }
    }
    public function transform_index(): View
    {
        return view('stock.transform.index');
    }

    public function transform_show(Request $request)
    {
        $start = $request->start;
        $end = $request->end;
        $transforms = Transform_details::with('product.units', 'stockFrom', 'stockTo', 'unit')->whereDate('created_at', '>=', $request->start)->whereDate('created_at', '<=', $request->end)->get();
        return view('stock.transform.show', compact('transforms', 'start', 'end'));
    }

    public function select2(Request $request)
    {
        $stocks = Stock::whereNot('id', $request->stockId)->get();
        $stocks_from = Stock::with('products')->findOrFail($request->stockId)->products()->get();
        foreach ($stocks_from as $product) {
            $product->qty = $product->convertQuantity($product->pivot->quantity);
        }
        return response()->json([
            'stocks' => $stocks,
            'products' => $stocks_from,
            'success' => true,
        ]);
    }
}
