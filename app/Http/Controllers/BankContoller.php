<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Tree4;
use Illuminate\View\View;
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
        $banks = Bank::get();
        return view('bank.index', compact('banks'));
    }

    public function store(StoreBankRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $bank = Bank::create(
                attributes: [
                    'name' => $request->name,
                    'no_bank' => $request->no_bank,
                ],
            );

            $tree4 = Tree4::whereTree3_code(1203)->orderBy('id', 'DESC')->first();
            $tree4_code = $tree4 ? $tree4->tree4_code + 1 : '120300001';

            Tree4::create(
                attributes: [
                    'tree4_name' => $bank->name,
                    'tree4_code' => $tree4_code,
                    'tree3_id' => 3,
                    'tree3_code' => 1203,
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

    public function update(UpdateBankRequest $request, Bank $bank): RedirectResponse
    {
        try {
            Db::beginTransaction();

            $tree4 = Tree4::whereTree4_name($bank->name);
            $tree4->update([
                'tree4_name' => $request->name,
            ]);

            $bank->update([
                'name' => $request->name,
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

    public function destroy(Bank $bank): RedirectResponse
    {
        try {
            DB::beginTransaction();
            Tree4::whereTree4_name($bank->name)->delete();
            $bank->delete();
            DB::commit();
            return back()->with('success_deleted', 'تم الحذف بنجاح ');
        } catch (\Exception $exception) {
            DB::rollback();
            return redirect()
                ->route('bank.index')
                ->withErrors(['error' => $exception->getMessage()]);
        }
    }

    public function toggleStatus(Bank $bank): JsonResponse
    {
        Tree4::whereTree4_name($bank->name)->update([
            'status' => !$bank->status,
        ]);
        $bank->update(['status' => !$bank->status]);
        return response()->json(['status' => $bank->status]);
    }
}
