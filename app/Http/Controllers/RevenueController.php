<?php

namespace App\Http\Controllers;

use App\Models\Tree4;
use Illuminate\View\View;
use App\Models\Restriction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Revenue\StoreRevenueRequest;
use App\Http\Requests\Revenue\UpdateRevenueRequest;
use App\Models\Revenue;
use App\Models\SaleInvoice;

class RevenueController extends Controller
{
    public function index(): View
    {
        $revenues = Revenue::withCount('allocations')->where('branch_id', session('branch_id'))->orderByDesc('id')->get();
        $banks = Tree4::where('tree3_code', 1203)->get();
        $safes = Tree4::where('tree3_code', 1202)->get();
        $dains = Tree4::where('tree3_code',1205)->get(); // pharmacies only(customers)
        $representatives = Tree4::where('tree3_code', 1206)->get();
        return view('revenue.index', compact('revenues', 'dains', 'banks', 'safes', 'representatives'));
    }

    public function store(StoreRevenueRequest $request): View|RedirectResponse
    {
        try {
            DB::beginTransaction();
            $sum = 0;
            $transaction_no = (float) str_replace(',', '', $request->madin_name[1] ?? '') > 0 ? $request->transaction_no : null;

            $representative_id = $request->representative_id ?: null;
            $rep_percentage = 0;
            $rep_cut_amount = 0;
            if ($representative_id) {
                $rep_percentage = (float) (Tree4::find($representative_id)->rep_percentage ?? 0);
                $base = (float) ($request->madin_name[0] ?? 0) + (float) ($request->madin_name[1] ?? 0);
                $rep_cut_amount = round($base * $rep_percentage / 100, 2);
            }

            $revenue = Revenue::create([
                'dain_id' => $request->dain,
                'bank_id' => $request->madin_id[1] ?? null,
                'cach_id' => $request->madin_id[0] ?? null,
                'cach_name' => $request->madin_name[0] ?? null,
                'bank_name' => $request->madin_name[1] ?? null,
                'representative_id' => $representative_id,
                'rep_percentage' => $rep_percentage,
                'rep_cut_amount' => $rep_cut_amount,
                'transaction_no' => $transaction_no,
                'branch_id' => session('branch_id'),
                'note' => $request->note,
            ]);

            $last_constant_number = Restriction::where('branch_id', session('branch_id'))->max('constant_number');
            $constant_number = $last_constant_number ? $last_constant_number + 1 : 1;

            foreach ($request->madin_id as $index => $madinId) {
                if (!$madinId || !isset($request->madin_name[$index]) || !$request->madin_name[$index]) {
                    continue;
                }
                $madin = Tree4::findOrFail($madinId);
                $this->restriction($madin->id, $madin->tree4_code, $request->madin_name[$index], 0, $constant_number, $revenue, $request->note, $index == 1 ? $transaction_no : null);
                $sum += $request->madin_name[$index];
            }
            $dain = Tree4::findOrFail($request->dain);
            $this->restriction($dain->id, $dain->tree4_code, 0, $sum, $constant_number, $revenue, $request->note, $transaction_no);
            DB::commit();
            // return view('revenue.print', ['revenue' => $operation]);
            return redirect()->route('revenue.show', $revenue->id);
        } catch (\Exception $exception) {
            DB::rollback();
            return back()->withErrors(['error' => $exception->getMessage()]);
        }
    }

    public function show(Revenue $revenue): View
    {
        return view('revenue.print', compact('revenue'));
    }

    public function update(UpdateRevenueRequest $request, Revenue $revenue): RedirectResponse|View
    {
        if ($revenue->allocations()->exists()) {
            return back()->withErrors(['error' => 'لا يمكن تعديل هذا الايراد لانه مرتبط بتحصيل فواتير العملاء']);
        }
        try {
            DB::beginTransaction();

            $old_representative_id = $revenue->representative_id;
            $old_rep_percentage = (float) $revenue->rep_percentage;

            $revenue->delete();
            $sum = 0;
            $transaction_no = (float) str_replace(',', '', $request->madin_name[1] ?? '') > 0 ? $request->transaction_no : null;

            $representative_id = $request->representative_id ?: null;
            $rep_percentage = 0;
            $rep_cut_amount = 0;
            $base = (float) ($request->madin_name[0] ?? 0) + (float) ($request->madin_name[1] ?? 0);
            if ($representative_id) {
                if ((int) $representative_id === (int) $old_representative_id) {
                    $rep_percentage = $old_rep_percentage;
                } else {
                    $rep_percentage = (float) (Tree4::find($representative_id)->rep_percentage ?? 0);
                }
                $rep_cut_amount = round($base * $rep_percentage / 100, 2);
            }

            $revenue = Revenue::create([
                'dain_id' => $request->dain,
                'bank_id' => $request->madin_id[1] ?? null,
                'cach_id' => $request->madin_id[0] ?? null,
                'cach_name' => $request->madin_name[0] ?? null,
                'bank_name' => $request->madin_name[1] ?? null,
                'representative_id' => $representative_id,
                'rep_percentage' => $rep_percentage,
                'rep_cut_amount' => $rep_cut_amount,
                'transaction_no' => $transaction_no,
                'branch_id' => session('branch_id'),
                'note' => $request->note,
            ]);

            $last_constant_number = Restriction::where('branch_id', session('branch_id'))->max('constant_number');
            $constant_number = $last_constant_number ? $last_constant_number + 1 : 1;

            foreach ($request->madin_id as $index => $madinId) {
                if (!$madinId || !isset($request->madin_name[$index]) || !$request->madin_name[$index]) {
                    continue;
                }
                $madin = Tree4::findOrFail($madinId);
                $this->restriction($madin->id, $madin->tree4_code, $request->madin_name[$index], 0, $constant_number, $revenue, $request->note, $index == 1 ? $transaction_no : null);
                $sum += $request->madin_name[$index];
            }
            $dain = Tree4::findOrFail($request->dain);
            $this->restriction($dain->id, $dain->tree4_code, 0, $sum, $constant_number, $revenue, $request->note, $transaction_no);
            DB::commit();
            return redirect()->route('revenue.show', $revenue->id);
        } catch (\Exception $exception) {
            DB::rollback();
            return back()->withErrors(['error' => $exception->getMessage()]);
        }
    }

    public function destroy(Revenue $revenue)
    {
        try {
            DB::beginTransaction();
            foreach ($revenue->allocations as $allocation) {
                $sale_invoice = SaleInvoice::find($allocation->sale_invoice_id);
                if ($sale_invoice) {
                    $sale_invoice->update([
                        'deferend_amount' => round((float) $sale_invoice->deferend_amount + (float) $allocation->amount, 2),
                        'invoice_status' => 0,
                    ]);
                }
            }
            $revenue->delete();
            DB::commit();
            return back()->with('success_deleted', 'successfully deleted');
        } catch (\Exception $exception) {
            DB::rollback();
            return back()->withErrors(['error' => $exception->getMessage()]);
        }
    }

    private function restriction($tree_id, $tree4_code, $madin, $dain, $constant_number, $revenue, $node, $transactionNo = null): Restriction
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
                'revenue_id' => $revenue->id,
                'type' => 8,
                'branch_id' => session('branch_id'),
                'user_id' => Auth::id(),
            ],
        );
    }
}
