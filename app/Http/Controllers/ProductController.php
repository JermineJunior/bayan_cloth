<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Category;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')->orderBy('id', 'DESC')->get();
        return view('product.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::statusActive()->get();
        $stocks = Stock::statusActive()->get();
        $units = Unit::statusActive()->get();
        return view('product.create', compact('categories', 'stocks', 'units'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $barcode = $request->get('barcode') ?? 'bayan_item' . rand(1000000, 999999999);

            // إنشاء المنتج
            $product = Product::create([
                'barcode' => $barcode,
                'name' => $request->name,
                'category_id' => $request->category_id,
                'expire_date' => $request->expire_date,
                'unit_purchase_price' => $request->unit_purchase_price,
                'unit_reorder_level' => $request->unit_reorder_level,
                'unit_qty' => $request->unit_opening_balance,
                'purchase_price' => $request->purchase_price,
            ]);

            // تجميع بيانات الوحدات
            $units = collect($request->units)->mapWithKeys(function ($unit) {
                return [
                    $unit['unit_id'] => [
                        'price' => $unit['price'],
                        'conversion_factor' => $unit['conversion_factor'],
                    ],
                ];
            });

            // تعريف المتغيرات مسبقًا لتجنب الأخطاء
            $reorder_level = $purchase_price = null;

            foreach ($request->units as $unit) {
                if ($unit['unit_id'] == $request->unit_reorder_level) {
                    $reorder_level = $request->reorder_level * $unit['conversion_factor'];
                }

                if ($unit['unit_id'] == $request->unit_purchase_price) {
                    $purchase_price = $request->purchase_price * $unit['conversion_factor'];
                }
            }

            // التحقق من أن جميع القيم المطلوبة موجودة
            $missingFields = [];
            if (is_null($reorder_level)) {
                $missingFields[] = 'الوحدة المستخدمة في حد الطلب غير متوفرة.';
            }
            if (is_null($purchase_price)) {
                $missingFields[] = 'الوحدة المستخدمة في سعر الشراء غير متوفرة.';
            }

            if (!empty($missingFields)) {
                DB::rollBack();
                return redirect()
                    ->route('product.index')
                    ->withErrors(['error' => implode(' ', $missingFields)]);
            }

            // تحديث بيانات المنتج
            $product->update([
                'reorder_level' => $reorder_level,
            ]);

            // ربط المنتج مع الوحدات
            $product->units()->attach($units);

            $stocks = Stock::get();

            // إدراج بيانات المخزون لكل المخازن المرتبطة بالمنتج
            foreach ($stocks as $stock) {
                $product->stocks()->attach($stock->id, [
                    'quantity' => 0,
                    'unit_stock' => $product->units()->first()->pivot->unit_id,
                ]);
            }

            DB::commit();

            return redirect()->route('product.index')->with('success_created', 'تمت إضافة المنتج بنجاح.');
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()
                ->route('product.index')
                ->withErrors(['error' => $exception->getMessage()]);
        }
    }

    public function edit($id): View
    {
        $product = Product::with(['category', 'units', 'stocks'])->findOrFail($id);
        $categories = Category::statusActive()->get();
        $stocks = Stock::statusActive()->get();
        $units = Unit::statusActive()->get();
        foreach ($product->units as $unit) {
            if ($unit->pivot->unit_id == $product->unit_reorder_level) {
                $reorder_level = $product->reorder_level / $unit->pivot->conversion_factor;
            }
        }

        return view('product.edit', compact('product', 'categories', 'stocks', 'units', 'reorder_level'));
    }

    public function update(Product $product, UpdateProductRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $productStocks = DB::table('product_stocks')->where('product_id', $product->id)->get();

            $product->delete();

            $barcode = $request->get('barcode') ?? 'bayan_item' . rand(1000000, 999999999);

            // إنشاء المنتج
            $product = Product::create([
                'barcode' => $barcode,
                'name' => $request->name,
                'category_id' => $request->category_id,
                'expire_date' => $request->expire_date,
                'unit_purchase_price' => $request->unit_purchase_price,
                'unit_reorder_level' => $request->unit_reorder_level,
                'unit_qty' => $request->unit_opening_balance,
                'purchase_price' => $request->purchase_price,
            ]);

            // تجميع بيانات الوحدات
            $units = collect($request->units)->mapWithKeys(function ($unit) {
                return [
                    $unit['unit_id'] => [
                        'price' => $unit['price'],
                        'conversion_factor' => $unit['conversion_factor'],
                    ],
                ];
            });

            foreach ($productStocks as $stock) {
                DB::table('product_stocks')->insert([
                    'product_id' => $product->id,
                   'stock_id' => $stock->stock_id,
                    'quantity' => $stock->quantity,
                    'unit_stock' => $stock->unit_stock,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }


            // تعريف المتغيرات مسبقًا لتجنب الأخطاء
            $reorder_level = $purchase_price = null;

            foreach ($request->units as $unit) {
                if ($unit['unit_id'] == $request->unit_reorder_level) {
                    $reorder_level = $request->reorder_level * $unit['conversion_factor'];
                }

                if ($unit['unit_id'] == $request->unit_purchase_price) {
                    $purchase_price = $request->purchase_price * $unit['conversion_factor'];
                }
            }

            // التحقق من أن جميع القيم المطلوبة موجودة
            $missingFields = [];
            if (is_null($reorder_level)) {
                $missingFields[] = 'الوحدة المستخدمة في حد الطلب غير متوفرة.';
            }

            if (is_null($purchase_price)) {
                $missingFields[] = 'الوحدة المستخدمة في سعر الشراء غير متوفرة.';
            }

            if (!empty($missingFields)) {
                DB::rollBack();
                return redirect()
                    ->route('product.index')
                    ->withErrors(['error' => implode(' ', $missingFields)]);
            }

            // تحديث بيانات المنتج
            $product->update([
                'reorder_level' => $reorder_level,
            ]);

            // ربط المنتج مع الوحدات
            $product->units()->attach($units);

            DB::commit();

            return redirect()
                ->route('product.index')
                ->with(['success_updated' => 'Product updated successfully.']);
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->route('product.index')->withErrors('error', $exception->getMessage())->withInput();
        }
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
        return back()->with('success_deleted', 'تم الحذف بنجاح ');
    }

    public function toggleStatus(Product $product): JsonResponse
    {
        $product->update(['status' => !$product->status]);
        return response()->json(['success' => 'Product updated successfully']);
    }

    public function checkBarcode(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $exists = Product::where('barcode', $request->barcode)->where('id', '!=', $id)->exists(); // تحقق من وجود الباركود في قاعدة البيانات
        return response()->json(['isUnique' => !$exists]);
    }

    public function checkName(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $exists = Product::where('name', $request->name)->where('id', '!=', $id)->exists(); // تحقق من وجود الاسم في قاعدة البيانات
        return response()->json(['isUnique' => !$exists]); // إذا كانت النتيجة false تعني أن الاسم فريد
    }

    public function getProduct(Request $request): JsonResponse
    {
        $productName = $request->input('name'); // استخدام الاسم في الطلب

        // البحث عن المنتج مع الوحدات المرتبطة
        $product = Product::with(['units:id,name', 'stocks:id,name']) // تحميل علاقة الوحدات
            ->where(function ($query) use ($productName) {
                $query
                    ->where(column: 'name', operator: $productName) // البحث عن الاسم
                    ->orWhere(column: 'barcode', operator: $productName); // أو البحث عن الباركود
            })
            ->whereStatus(1)
            ->first(); // جلب أول منتج مطابق

        // إذا لم يتم العثور على المنتج
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'هذا المنتج غير متوفر .',
            ]);
        }
        // إذا كان تاريخ الصلاحية قد انتهى
        if ($product->expire_date && $product->expire_date < now()) {
            return response()->json([
                'success' => false,
                'message' => 'هذا المنتج منتهي الصلاحية .',
            ]);
        }

        if (!empty($request->stock_id) && ($product->stocks->firstWhere('id', $request->stock_id)->pivot->quantity = 0)) {
            return response()->json([
                'success' => false,
                'message' => 'هذا المنتج اقل من المتوفر.',
            ]);
        }

        $total = 0;
        $stocks = $product->stocks()->orderByDesc('id')->get();
        foreach ($stocks as $stock) {
            $total += $stock->pivot->quantity;
        }

        if ($total == 0) {
            return response()->json(
                data: [
                    'success' => false,
                    'message' => 'المنتج غير متوفرة في المخازن.',
                    'product' => $product,
                ],
            );
        }

        // إذا كان المنتج موجودًا ومفعلًا ولم تنتهِ صلاحيته
        return response()->json([
            'success' => true,
            'product' => $product,
            'units' => $product->units,
            'stocks' => Stock::where('id', '!=', $product->stocks()->where('quantity', '!=', 0)->first()->id)
                ->statusActive()
                ->get(),
            'stock_from' => $product->stocks()->where('quantity', '>=', 0)->get(),
        ]);
    }

    public function getUnitPrice(Request $request): JsonResponse
    {
        $productId = $request->input('productId');
        $unitId = $request->input('unitId');

        $product = Product::with('units')->whereId($productId)->first();

        foreach ($product->units as $unit) {
            if ($unit->id == $unitId) {
                return response()->json([
                    'success' => true,
                    'price' => $unit->pivot->price,
                    'firstPrice' => $product->units[0]->pivot->price,
                ]);
            }
        }
        return response()->json([
            'success' => false,
            'message' => 'الوحدة غير متوفرة',
        ]);
    }

    public function checkQty(Request $request): JsonResponse
    {
        $unit_id = $request->input('unit_id');
        $product_id = $request->input('product_id');
        $qty = $request->input('qty', 1);

        // التحقق من وجود المنتج
        $product = Product::with(relations: 'units')->find($product_id);

        if (!$product) {
            return response()->json(
                data: [
                    'success' => false,
                    'message' => 'المنتج غير موجود.',
                    'product' => $product,
                ],
            );
        }

        $unit = $product->units->firstWhere('id', $unit_id);

        if ($unit) {
            $qty = $qty * $unit->pivot->conversion_factor;

            $product_qty = $product->stocks->firstWhere('id', $request->stock_id)->pivot->quantity;

            if ($qty > $product_qty) {
                return response()->json(
                    data: [
                        'success' => false,
                        'message' => 'الكمية غير متوفرة.',
                        'product' => $product,
                    ],
                );
            }
        }

        return response()->json(
            data: [
                'success' => true,
                'qty' => $qty,
                'product' => $product,
            ],
        );
    }

    public function opening(Product $product): View
    {
        $stocks = Stock::statusActive()->get();
        return view('product.price', compact('product', 'stocks'));
    }

    public function opening_store(Product $product, Request $request): RedirectResponse
    {
        $unitIds = $product->units()->pluck('unit_id')->toArray();
        $stocIds = $product->stocks()->pluck('stock_id')->toArray();

        $units = collect($product->units)->mapWithKeys(function ($unit) {
            return [
                $unit->pivot['unit_id'] => [
                    'price' => $unit->pivot['price'],
                    'conversion_factor' => $unit->pivot['conversion_factor'],
                ],
            ];
        });

        // تجهيز بيانات المخزون مع حساب الكمية
        $stocks = collect($request->stocks)
            ->map(function ($stock) use ($units) {
                return [
                    'stock_id' => $stock['stock_id'],
                    'quantity' => $stock['quantity'] * ($units[$stock['unit_stock']]['conversion_factor'] ?? 1),
                    'unit_stock' => $stock['unit_stock'],
                ];
            })
            ->toArray();

        // التحقق من أن الوحدات في المخزون موجودة في قائمة الوحدات
        foreach ($stocks as $stock) {
            if (!in_array($stock['unit_stock'], $unitIds)) {
                DB::rollBack();
                return redirect()
                    ->route('product.index')
                    ->withErrors(['error' => 'خطأ! بعض الوحدات المستخدمة في المخزون غير متوفرة في قائمة الوحدات المدخلة.']);
            }
        }

        // إدراج بيانات المخزون لكل المخازن المرتبطة بالمنتج
        foreach ($stocks as $stock) {
            $product->stocks()->sync(
                [
                    $stock['stock_id'] => [
                        'quantity' => $stock['quantity'],
                        'unit_stock' => $stock['unit_stock'],
                    ],
                ],
                false,
            );
        }

        return redirect()->route('product.index')->with('success_created', 'successfully created');
    }
}
