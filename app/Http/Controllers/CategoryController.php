<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::orderBy('order', 'asc')->get();
        return view('category.index', compact('categories'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        Category::create(attributes: ['name' => $request->name]);
        return back()->with('success_created', 'تمت اضافة البيانات بنجاح');
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update(['name' => $request->name]);
        return back()->with('success_updated', 'تم تحديث البيانات بنجاج');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->count() > 0) {
            return back()->withErrors('لا يمكن حذف هذه الشركة لارتباطها بمجموعة من المنتجات');
        }
        $category->delete();
        return back()->with('success_deleted', 'تم الحذف بنجاح ');
    }


    public function checkName(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $exists = Category::where('name', $request->name)->where('id', '!=', $id)->exists(); // تحقق من وجود الباركود في قاعدة البيانات
        return response()->json(['isUnique' => !$exists]);
    }

    public function updateOrder(Request $request)
    {
        try {
            foreach ($request->order as $item) {
                Category::where('id', $item['id'])->update([
                    'order' => $item['position'],
                ]);
            }

            return response()->json(['status' => 'success']);
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }
}
