<?php

namespace App\Http\Controllers;

use App\Models\Tree4;
use App\Models\Restriction;
use App\Models\SaleInvoice;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\Revenue;
use App\Models\RevenueAllocation;

class CustomerInvoicePaymentController extends Controller
{
    public function create(): View
    {
        if (true) {
            return view('coming-soon');
        }
        $customers = Tree4::where('tree3_code', 1205)->get();
        $safes = Tree4::where('tree3_code', 1202)->get();
        $banks = Tree4::where('tree3_code', 1203)->get();
        $representatives = Tree4::where('tree3_code', 1206)->get();
        return view('customer_invoice_payment.create', compact('customers', 'safes', 'banks', 'representatives'));
    }

    public function unpaid(Request $request): JsonResponse
    {
        $invoices = SaleInvoice::where('customer_id', $request->customer_id)
            ->where('branch_id', session('branch_id'))
            ->where('status', 1)
            ->where('deferend_amount', '>', 0)
            ->orderBy('date')
            ->orderBy('id')
            ->get()
            ->map(fn($invoice) => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'date' => $invoice->date,
                'total_amount' => (float) $invoice->total_amount,
                'paid' => round((float) $invoice->total_amount - (float) $invoice->deferend_amount, 2),
                'remaining' => (float) $invoice->deferend_amount,
            ]);

        return response()->json($invoices);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'customer_id' => 'required|exists:tree4s,id',
                'representative_id' => 'nullable|exists:tree4s,id',
                'transaction_no' => ['nullable', 'string', 'max:100', Rule::requiredIf(fn() => (float) str_replace(',', '', $request->madin_name[1] ?? '') > 0)],
            ],
            [
                'customer_id.required' => 'يرجى اختيار العميل.',
                'customer_id.exists' => 'العميل غير موجود.',
                'representative_id.exists' => 'المندوب غير موجود.',
                'transaction_no.required' => 'يرجى إدخال رقم عملية البنك.',
            ],
        );

        try {
            $cash = (float) str_replace(',', '', $request->madin_name[0] ?? '');
            $bank = (float) str_replace(',', '', $request->madin_name[1] ?? '');
            $received = round($cash + $bank, 2);

            if ($received <= 0) {
                return back()->withErrors(['error' => 'يجب ان يكون مجموع المبلغ المستلم اكبر من صفر'])->withInput();
            }

            $allocations = [];
            foreach ($request->input('allocations', []) as $sale_invoice_id => $amount) {
                $amount = round((float) str_replace(',', '', (string) $amount), 2);
                if ($amount > 0) {
                    $allocations[(int) $sale_invoice_id] = $amount;
                }
            }

            if (empty($allocations)) {
                return back()->withErrors(['error' => 'يرجى تحديد مبلغ مسدد لأحد الفواتير'])->withInput();
            }

            $sum_allocations = round(array_sum($allocations), 2);
            if ($sum_allocations > $received) {
                return back()->withErrors(['error' => 'مجموع اقساط التحصيل اكبر من المبلغ المستلم'])->withInput();
            }

            DB::beginTransaction();

            $invoices = SaleInvoice::whereIn('id', array_keys($allocations))->lockForUpdate()->get()->keyBy('id');
            foreach ($allocations as $sale_invoice_id => $amount) {
                $invoice = $invoices->get($sale_invoice_id);
                if (!$invoice || (int) $invoice->customer_id !== (int) $request->customer_id || (int) $invoice->branch_id !== (int) session('branch_id')) {
                    throw new \Exception('إحدى الفواتير المحددة غير تابعة لهذا العميل او لهذا الفرع');
                }
                if ($amount > (float) $invoice->deferend_amount) {
                    throw new \Exception('المبلغ المحدد للفاتورة رقم ' . $invoice->invoice_number . ' اكبر من المتبقي عليها');
                }
            }

            $transaction_no = $bank > 0 ? $request->transaction_no : null;

            $representative_id = $request->representative_id ?: null;
            $rep_percentage = 0;
            $rep_cut_amount = 0;
            if ($representative_id) {
                $rep_percentage = (float) (Tree4::find($representative_id)->rep_percentage ?? 0);
                $rep_cut_amount = round($received * $rep_percentage / 100, 2);
            }

            $revenue = Revenue::create([
                'dain_id' => $request->customer_id,
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

            $sum = 0;
            $madin_ids = $request->input('madin_id', []);
            $madin_names = $request->input('madin_name', []);
            foreach ($madin_ids as $index => $madinId) {
                if (!$madinId || !isset($madin_names[$index]) || !$madin_names[$index]) {
                    continue;
                }
                $madin = Tree4::findOrFail($madinId);
                $this->restriction($madin->id, $madin->tree4_code, $madin_names[$index], 0, $constant_number, $revenue, $request->note, $index == 1 ? $transaction_no : null);
                $sum += $madin_names[$index];
            }
            $dain = Tree4::findOrFail($request->customer_id);
            $this->restriction($dain->id, $dain->tree4_code, 0, $sum, $constant_number, $revenue, $request->note, $transaction_no);

            foreach ($allocations as $sale_invoice_id => $amount) {
                RevenueAllocation::create([
                    'revenue_id' => $revenue->id,
                    'sale_invoice_id' => $sale_invoice_id,
                    'amount' => $amount,
                ]);

                $invoice = $invoices->get($sale_invoice_id);
                $new_deferend_amount = round((float) $invoice->deferend_amount - $amount, 2);
                $invoice->update([
                    'deferend_amount' => $new_deferend_amount,
                    'invoice_status' => $new_deferend_amount <= 0 ? 1 : 0,
                ]);
            }

            DB::commit();
            return redirect()->route('revenue.show', $revenue->id);
        } catch (\Exception $exception) {
            DB::rollback();
            return back()->withErrors(['error' => $exception->getMessage()])->withInput();
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
