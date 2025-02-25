<?php

namespace App\Http\Controllers;

use App\Models\Tree3;
use App\Models\Tree4;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class Tree4Controller extends Controller
{
    public function index()
    {
        $tree3s = Tree3::get();
        $tree4s = Tree4::orderBy('tree4_code')->get();
        return view('tree4.index', compact('tree3s', 'tree4s'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tree4_name' => 'required|string|max:225|unique:tree4s,tree4_name',
            'tree3_id' => 'required|exists:tree3s,id',
        ]);

        $tree3 = Tree3::with('tree4')->orderByDesc('id')->first();

        Tree4::create([
            'tree4_name' => $request->tree4_name,
            'tree3_id' => $tree3->id,
            'tree4_code' => $tree3->tree4()->count() > 0 ? $tree3->tree4()->orderByDesc('id')->first()->tree4_code + 1 : $tree3->tree3_code * 100000 + 1,
            'tree3_code' => $tree3->tree3_code,
            'user_id' => Auth::id(),
        ]);
        return back()->with('success_created', 'Tree 4 created successfully.');
    }

    public function update(Tree4 $tree4, Request $request): RedirectResponse
    {
        $request->validate([
            'tree4_name' => 'required|string|max:225|unique:tree4s,tree4_name,' . $tree4->id,
        ]);

        $tree4->update([
            'tree4_name' => $request->tree4_name,
        ]);

        return back()->with('success_updated', 'Tree 4 updated successfully.');
    }

    public function destroy(Tree4 $tree4): RedirectResponse
    {
        $tree4->delete();
        return back()->with('success_deleted', 'Tree 4 deleted successfully.');
    }

    public function select2(Request $request): JsonResponse
    {
        $search = $request->get('q'); 
        $exclude = $request->input('exclude', []);
        // بدء الاستعلام الأساسي
        if ($search != null) {
            $query = Tree4::query();

            // الشرط الأساسي للحالة العامة
            $query->where('status', '=', '1');

            // البحث بالنص المدخل
            if (!empty($search)) {
                $query->where('tree4_name', 'like', '%' . $search . '%');
            }

            // استثناء الحساب المختار في "من حساب" إذا وجد
            if (!empty($exclude)) {
                $query->whereNotIn('id', $exclude);
            }

            // تنفيذ الاستعلام وجلب النتائج
            $tree4 = $query->get();

            // تنسيق النتائج لـ Select2
            $formattedResults = $tree4->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->tree4_name, // الحقل الذي سيظهر في Select2
                ];
            });
        }
        return response()->json($formattedResults);
    }
}
