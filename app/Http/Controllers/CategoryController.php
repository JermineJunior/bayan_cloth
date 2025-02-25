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
        $categories = Category::get();
        return view('category.index', compact('categories'));
    }

    public function store(StoreCategoryRequest $request):RedirectResponse
    {
        Category::create(attributes: ['name' => $request->name]);
        return back()->with('success_created','تمت اضافة البيانات بنجاح');
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update(['name' => $request->name]);
        return back()->with('success_updated','تم تحديث البيانات بنجاج');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        return back()->with('success_deleted','تم الحذف بنجاح ');
    }

    public function toggleStatus(Category $category): JsonResponse
    {
        $category->update(['status' => !$category->status]);
        return response()->json(['status' => $category->status]);
    }
}
