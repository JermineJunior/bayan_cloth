<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Tree4;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Bank\StoreBankRequest;
use App\Http\Requests\Bank\UpdateBankRequest;

class BankContoller extends Controller
{
    public function index(): View
    {
        $banks = Tree4::where('tree3_code', 1203)->get();
        return view('bank.index', compact('banks'));
    }

    public function store(StoreBankRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $tree4 = Tree4::whereTree3_code(1203)->orderBy('id', 'DESC')->first();
            $tree4_code = $tree4 ? $tree4->tree4_code + 1 : '120300001';

            Tree4::create(
                attributes: [
                    'tree4_name' => $request->name,
                    'tree4_code' => $tree4_code,
                    'tree3_id' => 3,
                    'tree3_code' => 1203,
                    'no_bank' => $request->no_bank,
                    'user_id' => Auth::id(),
                ],
            );

            Db::commit();
            return back()->with('success_created', 'تمت اضافة البيانات بنجاح');
        } catch (\Exception $exception) {
            DB::rollback();
            return redirect()
                ->route('bank.index')
                ->withErrors(['error' => $exception->getMessage()]);
        }
    }

    public function update(UpdateBankRequest $request, Tree4 $bank): RedirectResponse
    {
        try {
            Db::beginTransaction();

            $bank->update([
                'tree4_name' => $request->name,
                'no_bank' => $request->no_bank,
            ]);
            Db::commit();
            return back()->with('success_updated', 'تم تحديث البيانات بنجاج');
        } catch (\Exception $exception) {
            DB::rollback();
            return redirect()
                ->route('bank.index')
                ->withErrors(['error' => $exception->getMessage()]);
        }
    }

    public function destroy(Tree4 $bank): RedirectResponse
    {
       
            if ($bank->restrictions()->count() > 0) {
                return back()->withErrors('لا يمكن حذف هذا البنك لارتباطه ببعض الحسابات');
            }
            $bank->delete();
            return back()->with('success_deleted', 'تم الحذف بنجاح ');
    }

    public function checkName(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $exists = Tree4::where('tree4_name', $request->name)->where('id', '!=', $id)->exists(); // تحقق من وجود الباركود في قاعدة البيانات
        return response()->json(['isUnique' => !$exists]);
    }
}
