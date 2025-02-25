<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Product;
use App\Models\Transform;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
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
        Stock::create(attributes: ['name' => $request->name]);
        return back()->with('success_created', 'تمت اضافة البيانات بنجاح');
    }

    public function update(UpdateStockRequest $request, Stock $stock): RedirectResponse
    {
        $stock->update(['name' => $request->name]);
        return back()->with('success_updated', 'تم تحديث البيانات بنجاج');
    }

    public function destroy(Stock $stock): RedirectResponse
    {
        $stock->delete();
        return back()->with('success_deleted', 'تم الحذف بنجاح ');
    }

    public function toggleStatus(Stock $stock): JsonResponse
    {
        $stock->update(['status' => !$stock->status]);
        return response()->json(['status' => $stock->status]);
    }

    public function transform_create(): View
    {
        return view('stock.transform.create');
    }

    public function transform_index(): View
    {
        $stockTransforms = Transform::get();
        return view('stock.transform.index', compact('stockTransforms'));
    }

    public function transform_store(StoreTransformRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $data = [];

            foreach ($request->product_id as $index => $product_id) {
                $product = Product::with('stocks', 'units')->find($product_id);

                $conversion_factor = $product->units()->firstWhere('unit_id', $request->unit_id[$index])->pivot->conversion_factor;
                $qty = $request->qty[$index] * $conversion_factor;

                $fromStock = $product->stocks->where('id', $request->stock_id_from[$index])->first();
                $toStock = $product->stocks->where('id', $request->stock_id_to[$index])->first();

                // الحصول على المخزون المصدر والمخزون المستهدف من العلاقة
                $fromStock = $product->stocks()->where('stock_id', $request->stock_id_from[$index])->first();
                $toStock = $product->stocks()->where('stock_id', $request->stock_id_to[$index])->first();

                $newQuantityFrom = $fromStock->pivot->quantity - $qty;
                $product->stocks()->syncWithoutDetaching([
                    $request->stock_id_from[$index] => ['quantity' => $newQuantityFrom],
                ]);

                $newQuantityto = $toStock->pivot->quantity + $qty;
                $product->stocks()->syncWithoutDetaching([
                    $request->stock_id_to[$index] => ['quantity' => $newQuantityto],
                ]);

                $data[] = [
                    'product_id' => $product_id,
                    'quantity' => $request->qty[$index],
                    'unit_id' => $request->unit_id[$index],
                    'stock_id_from' => $request->stock_id_from[$index],
                    'stock_id_to' => $request->stock_id_to[$index],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // إدخال البيانات دفعة واحدة
            Transform::insert($data);

            DB::commit();
            return redirect()->route('stock.transform.index')->with('success_created', 'Transformation successful');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('stock.transform.index')->withErrors($e->getMessage());
        }
    }

    public function transform_delete(Transform $transform): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $transform->delete();

            $product = Product::with('stocks')->find($transform->product_id);

            $fromStock = $product->stocks()->where('stock_id', $transform->stock_id_from)->first();
            $toStock = $product->stocks()->where('stock_id', $transform->stock_id_to)->first();

            $newQuantityFrom = $fromStock->pivot->quantity + $transform->quantity;
            $product->stocks()->syncWithoutDetaching([
                $transform->stock_id_from => ['quantity' => $newQuantityFrom],
            ]);

            $newQuantityto = $toStock->pivot->quantity - $transform->quantity;
            $product->stocks()->syncWithoutDetaching([
                $transform->stock_id_to => ['quantity' => $newQuantityto],
            ]);

            DB::commit();
            return back()->with('success_deleted', 'Transformation deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors($e->getMessage());
        }
    }
}
