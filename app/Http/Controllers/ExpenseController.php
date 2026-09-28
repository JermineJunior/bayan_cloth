<?php

namespace App\Http\Controllers;

use App\Models\Tree4;
use App\Models\Expense;
use Illuminate\View\View;
use App\Models\Restriction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Requests\Expense\UpdateExpenseRequest;

class ExpenseController extends Controller
{
    public function index(): View
    {
        $expenses = Expense::where('branch_id', session('branch_id'))->orderByDesc('id')->get();
        $banks = Tree4::where('tree3_code', 1203)->get();
        $safes = Tree4::where('tree3_code', 1202)->get();
        $madins = Tree4::get();
        return view('expense.index', compact('expenses', 'madins', 'banks', 'safes'));
    }

    public function store(StoreExpenseRequest $request): RedirectResponse|View
    {
        try {
            DB::beginTransaction();
            $sum = 0;
            $transaction_no = (float) str_replace(',', '', $request->dain_name[1] ?? '') > 0 ? $request->transaction_no : null;
            $expense = Expense::create([
                'madin_id' => $request->madin,
                'bank_id' => $request->dain_id[1] ?? null,
                'cach_id' => $request->dain_id[0] ?? null,
                'cach_name' => $request->dain_name[0] ?? null,
                'bank_name' => $request->dain_name[1] ?? null,
                'transaction_no' => $transaction_no,
                'branch_id' => session('branch_id'),
                'note' => $request->note,
            ]);

            $last_constant_number = Restriction::where('branch_id', session('branch_id'))->max('constant_number');
            $constant_number = $last_constant_number ? $last_constant_number + 1 : 1;

            foreach ($request->dain_id as $index => $dainId) {
                if (!$dainId || !isset($request->dain_name[$index]) || !$request->dain_name[$index]) {
                    continue;
                }
                $dain = Tree4::findOrFail($dainId);

                $this->restriction($dain->id, $dain->tree4_code, 0, $request->dain_name[$index], $constant_number, $expense, $request->note, $index == 1 ? $transaction_no : null);
                $sum += $request->dain_name[$index];
            }

            $madin = Tree4::findOrFail($request->madin);
            $this->restriction($madin->id, $madin->tree4_code, $sum, 0, $constant_number, $expense, $request->note, $transaction_no);

            DB::commit();
            return redirect()->route('expense.show', $expense->id);
        } catch (\Exception $exception) {
            DB::rollback();
            return back()->withErrors(['error' => $exception->getMessage()]);
        }
    }

    public function show(Expense $expense): View
    {
        return view('expense.print', compact('expense'));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense): RedirectResponse|View
    {
        try {
            DB::beginTransaction();
            $expense->delete();
            $sum = 0;
            $transaction_no = (float) str_replace(',', '', $request->dain_name[1] ?? '') > 0 ? $request->transaction_no : null;

            $expense = Expense::create([
                'madin_id' => $request->madin,
                'bank_id' => $request->dain_id[1] ?? null,
                'cach_id' => $request->dain_id[0] ?? null,
                'cach_name' => $request->dain_name[0] ?? null,
                'bank_name' => $request->dain_name[1] ?? null,
                'transaction_no' => $transaction_no,
                'branch_id' => session('branch_id'),
                'note' => $request->note,
            ]);

            $last_constant_number = Restriction::where('branch_id', session('branch_id'))->max('constant_number');
            $constant_number = $last_constant_number ? $last_constant_number + 1 : 1;

            foreach ($request->dain_id as $index => $dainId) {
                if (!$dainId || !isset($request->dain_name[$index]) || !$request->dain_name[$index]) {
                    continue;
                }
                $dain = Tree4::findOrFail($dainId);

                $this->restriction($dain->id, $dain->tree4_code, 0, $request->dain_name[$index], $constant_number, $expense, $request->note, $index == 1 ? $transaction_no : null);
                $sum += $request->dain_name[$index];
            }

            $madin = Tree4::findOrFail($request->madin);
            $this->restriction($madin->id, $madin->tree4_code, $sum, 0, $constant_number, $expense, $request->note, $transaction_no);

            DB::commit();
            return redirect()->route('expense.show', $expense->id);
        } catch (\Exception $exception) {
            DB::rollback();
            return back()->withErrors(['error' => $exception->getMessage()]);
        }
    }

    public function destroy(Expense $expense)
    {
        try {
            DB::beginTransaction();
            $expense->delete();
            DB::commit();
            return back()->with('success_deleted', 'successfully deleted');
        } catch (\Exception $exception) {
            DB::rollback();
            return back()->withErrors(['error' => $exception->getMessage()]);
        }
    }

    private function restriction($tree_id, $tree4_code, $madin, $dain, $constant_number, $expense, $node, $transactionNo = null): Restriction
    {
        return Restriction::create(
            attributes: [
                'tree4_id' => $tree_id,
                'tree4_code' => $tree4_code,
                'madin' => $madin,
                'dain' => $dain,
                'date' => today(),
                'node' => $node,
                'constant_number' => $constant_number,
                'transaction_no' => $transactionNo,
                'expense_id' => $expense->id,
                'type' => 9,
                'branch_id' => session('branch_id'),
                'user_id' => Auth::id(),
            ],
        );
    }
}
