<?php

namespace App\Http\Controllers;

use App\Models\Safe;
use App\Models\Tree4;
use Illuminate\View\View;
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
        $safes = Safe::get();
        return view('safe.index', compact('safes'));
    }

    public function store(StoreSafeRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $safe = Safe::create(
                attributes: [
                    'name' => $request->name,
                ],
            );

            $tree4 = Tree4::whereTree3_code(1202)->orderBy('id', 'DESC')->first();
            $tree4_code = $tree4 ? $tree4->tree4_code + 1 : '120200001';

            Tree4::create(
                attributes: [
                    'tree4_name' => $safe->name,
                    'tree4_code' => $tree4_code,
                    'tree3_id' => 2,
                    'tree3_code' => 1202,
                    'user_id' => Auth::id(),
                ],
            );

            Db::commit();
            return back()->with('success_created', 'تمت اضافة البيانات بنجاح');
        } catch (\Exception $exception) {
            DB::rollback();
            return redirect()
                ->route('safe.index')
                ->withErrors(['error' => $exception->getMessage()]);
        }
    }

    public function update(UpdateSafeRequest $request, Safe $safe): RedirectResponse
    {
        try {
            Db::beginTransaction();

            $tree4 = Tree4::whereTree4_name($safe->name);
            $tree4->update([
                'tree4_name' => $request->name,
            ]);

            $safe->update([
                'name' => $request->name,
            ]);
            Db::commit();
            return back()->with('success_updated', 'تم تحديث البيانات بنجاج');
        } catch (\Exception $exception) {
            DB::rollback();
            return redirect()
                ->route('safe.index')
                ->withErrors(['error' => $exception->getMessage()]);
        }
    }

    public function destroy(Safe $safe): RedirectResponse
    {
        try {
            DB::beginTransaction();
            Tree4::whereTree4_name($safe->name)->delete();
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

    public function toggleStatus(Safe $safe): JsonResponse
    {
        Tree4::whereTree4_name($safe->name)->update([
            'status' => !$safe->status,
        ]);
        $safe->update(['status' => !$safe->status]);
        return response()->json(['status' => $safe->status]);
    }
}
