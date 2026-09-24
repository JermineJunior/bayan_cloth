<?php

namespace App\Http\Controllers;

use App\Models\Tree4;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Safe\StoreSafeRequest;
use App\Http\Requests\Safe\UpdateSafeRequest;

class SafeController extends Controller
{
    public function index(): View
    {
        $safes = Tree4::where('tree3_code', 1202)->get();
        return view('safe.index', compact('safes'));
    }

    public function store(StoreSafeRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $tree4 = Tree4::whereTree3_code(1202)->orderBy('id', 'DESC')->first();
            $tree4_code = $tree4 ? $tree4->tree4_code + 1 : '120200001';

            $x = Tree4::create(
                attributes: [
                    'tree4_name' => $request->name,
                    'tree4_code' => $tree4_code,
                    'tree3_id' => 2,
                    'tree3_code' => 1202,
                    'user_id' => Auth::id(),
                ],
            );


            DB::commit();
            return back()->with('success_created', 'تمت اضافة البيانات بنجاح');
        } catch (\Exception $exception) {
            DB::rollback();
            return redirect()
                ->route('safe.index')
                ->withErrors(['error' => $exception->getMessage()]);
        }
    }

    public function update(UpdateSafeRequest $request, Tree4 $safe): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $safe->update([
                'tree4_name' => $request->name,
            ]);

            DB::commit();
            return back()->with('success_updated', 'تم تحديث البيانات بنجاج');
        } catch (\Exception $exception) {
            DB::rollback();
            return redirect()
                ->route('safe.index')
                ->withErrors(['error' => $exception->getMessage()]);
        }
    }

    public function destroy(Tree4 $safe): RedirectResponse
    {
        try {
            DB::beginTransaction();
            if ($safe->restrictions()->count() > 0) {
                DB::rollBack();
                return back()->withErrors('لا يمكن حذف هذه الخزنة لانها مرتبطة ببعض الحسابات');
            }
            $safe->delete();
            DB::commit();
            return back()->with('success_deleted', 'تم الحذف بنجاح ');
        } catch (\Exception $exception) {
            DB::rollback();
            return redirect()
                ->route('safe.index')
                ->withErrors(['error' => $exception->getMessage()]);
        }
    }

    public function checkName(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $exists = Tree4::where('tree4_name', $request->name)->where('id', '!=', $id)->exists(); // تحقق من وجود الباركود في قاعدة البيانات
        return response()->json(['isUnique' => !$exists]);
    }
}
