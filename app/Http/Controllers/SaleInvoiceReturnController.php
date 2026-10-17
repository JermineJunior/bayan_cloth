<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Tree4;
use App\Models\Product;
use Illuminate\View\View;
use App\Models\Restriction;
use App\Models\StockDetail;
use Illuminate\Http\Request;
use App\Models\SaleInvoiceReturn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\SaleInvoice\UpdateSaleInvoiceRequest;
use App\Http\Requests\SaleInvoiceReturn\StoreSaleInvoiceReturnRequest;

class SaleInvoiceReturnController extends Controller
{
    public function index()
    {
        $sale_invoice_returns = SaleInvoiceReturn::where('branch_id', session('branch_id'))->orderByDesc('invoice_number')->get();
        return view('sale_return.index', compact('sale_invoice_returns'));
    }
    public function create(Request $request): View
    {
        $invoice_number = SaleInvoiceReturn::where('branch_id', session('branch_id'))->max('invoice_number') + 1;
        $customers = Tree4::whereTree3_code(1205)->orWhere('tree3_code','2301')->get();
        $representatives = Tree4::where('tree3_code', '1206')->get();
        $stocks = Stock::get();
        $safes = Tree4::where('tree3_code', '1202')->get();
        $banks = Tree4::where('tree3_code', '1203')->get();
        $products = Product::with('stocks', 'units')->take(7)->inRandomOrder()->get();
        return view('sale_return.create', compact('customers', 'representatives', 'invoice_number', 'stocks', 'safes', 'banks', 'products'));
    }

    public function store(StoreSaleInvoiceReturnRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $invoice_status = $request->deferend_amount != '' ? 0 : 1;
            $transaction_no = (float) str_replace(',', '', $request->bank_amount_total ?? '') > 0 ? $request->transaction_no : null;

            $rep_percentage = 0;
            $rep_cut_amount = 0;
            if (!empty($request->representative_id)) {
                $rep_percentage = (float) (Tree4::find($request->representative_id)->rep_percentage ?? 0);
                $rep_base_amount = (float) $request->total - ($request->tax_status ? (float) $request->tax : 0);
                $rep_cut_amount = round($rep_base_amount * $rep_percentage / 100, 2);
            }

            $sale_invoice_return = SaleInvoiceReturn::create(
                attributes: [
                    'customer_id' => $request->get('customer_id'),
                    'representative_id' => $request->get('representative_id'),
                    'invoice_number' => $request->get('invoice_number'),
                    'date' => $request->get('date'),
                    'stock_id' => $request->get('stock_id'),
                    'sub_total_amount' => $request->get('sub_total'),
                    'discount_amount' => $request->get('discount'),
                    'tax_amount' => $request->get('tax'),
                    'tax_status' => $request->get('tax_status') ? 1 : 0,
                    'total_amount' => $request->get('total'),
                    'status' => 1,
                    'cash_name' => $request->get('cash_name'),
                    'bank_name' => $request->get('bank_name'),
                    'cash_amount_total' => $request->get('cash_amount_total'),
                    'bank_amount_total' => $request->get('bank_amount_total'),
                    'transaction_no' => $transaction_no,
                    'process_no' => $request->get('process_no'),
                    'deferend_amount' => $request->get('deferend_amount'),
                    'invoice_status' => $invoice_status,
                    'rep_percentage' => $rep_percentage,
                    'rep_cut_amount' => $rep_cut_amount,
                    'note' => $request->note,
                    'branch_id' => session('branch_id'),
                    'user_id' => Auth::id(),
                ],
            );

            foreach ($request->product_id as $index => $product_id) {
                $sale_invoice_return->details()->create(
                    attributes: [
                        'product_id' => $product_id,
                        'qty' => $request->qty[$index],
                        'bounce' => $request->bounce[$index] ?? 0,
                        'expire_date' => $request->expire_date[$index],
                        'price' => $request->price[$index],
                        'unit_id' => $request->unit_id[$index],
                        'row_sub_total' => $request->row_sub_total[$index],
                    ],
                );

                $product = Product::whereId($product_id)->first();
                $product_unit = $product->units()->where('unit_id', $request->unit_id[$index])->first();
                $qty = ($request->qty[$index] + $request->bounce[$index]) * $product_unit->pivot->conversion_factor;

                $productStock = $product->stocks()->where('stock_id', $request->stock_id)->first();

                StockDetail::create([
                    'product_id' => $product_id,
                    'unit_id' => $request->unit_id[$index],
                    'stock_id' => $request->stock_id,
                    'wared' => $qty,
                    'user_id' => Auth::id(),
                    'type' => 3,
                    'branch_id' => session('branch_id'),
                    'sale_invoice_return_id' => $sale_invoice_return->id,
                ]);

                if ($productStock) {
                    $newQuantity = $productStock->pivot->quantity + $qty;
                    $product->stocks()->syncWithoutDetaching([
                        $request->stock_id => ['quantity' => $newQuantity],
                    ]);
                }
            }

            $last_constant_number = Restriction::where('branch_id', session('branch_id'))->max('constant_number');

            $constant_number = $last_constant_number ? $last_constant_number + 1 : 1;

            if (empty($request->supplier_id)) {
                if (!empty($request->cash_amount_total)) {
                    $cash = Tree4::find($request->cash_name);

                    $this->restriction(4, 520100001, $request->cash_amount_total, 0, $constant_number, $sale_invoice_return, 'فاتورة مشتريات تم الدفع عن طريق الكاش', $sale_invoice_return->date);

                    $this->restriction($cash->id, $cash->tree4_code, 0, $request->cash_amount_total, $constant_number, $sale_invoice_return, 'فاتورة مشتريات تم الدفع عن طريق الكاش', $sale_invoice_return->date);
                }

                if (!empty($request->bank_amount_total)) {
                    $bank = Tree4::find($request->bank_name);

                    $this->restriction(4, 520100001, $request->bank_amount_total, 0, $constant_number, $sale_invoice_return, 'فاتورة مشتريات تم الدفع عن طريق البنك', $sale_invoice_return->date, $transaction_no);

                    $this->restriction($bank->id, $bank->tree4_code, 0, $request->bank_amount_total, $constant_number, $sale_invoice_return, 'فاتورة مشتريات تم الدفع عن طريق البنك', $sale_invoice_return->date, $transaction_no);
                }
            } else {
                $supplier = Tree4::find($request->supplier_id);

                $this->restriction(4, 520100001, $request->total, 0, $constant_number, $sale_invoice_return, 'فاتورة مشتريات', $sale_invoice_return->date);

                $this->restriction($supplier->id, $supplier->tree4_code, 0, $request->total, $constant_number, $sale_invoice_return, 'فاتورة مشتريات', $sale_invoice_return->date);

                if (!empty($request->cash_amount_total)) {
                    $cash = Tree4::find($request->cash_name);

                    $this->restriction($cash->id, $cash->tree4_code, 0, $request->cash_amount_total, $constant_number, $sale_invoice_return, 'سداد كاش للمورد', $sale_invoice_return->date);

                    $this->restriction($supplier->id, $supplier->tree4_code, $request->cash_amount_total, 0, $constant_number, $sale_invoice_return, 'سداد كاش للمورد', $sale_invoice_return->date);
                }

                if (!empty($request->bank_amount_total)) {
                    $bank = Tree4::find($request->bank_name);

                    $this->restriction($bank->id, $bank->tree4_code, 0, $request->bank_amount_total, $constant_number, $sale_invoice_return, 'سداد بنك للمورد', $sale_invoice_return->date, $transaction_no);

                    $this->restriction($supplier->id, $supplier->tree4_code, $request->bank_amount_total, 0, $constant_number, $sale_invoice_return, 'سداد بنك للمورد', $sale_invoice_return->date, $transaction_no);
                }
            }

            DB::commit();

            return redirect()->route('sale_invoice_return.show', $sale_invoice_return->id);
        } catch (\Exception $exception) {
            DB::rollback();
            return redirect()
                ->route('sale_invoice_return.create')
                ->withErrors(['error' => $exception->getMessage()])
                ->withInput();
        }
    }

    public function show(SaleInvoiceReturn $sale_invoice_return)
    {
        $sale_invoice_return = $sale_invoice_return->load('details');
        return view('sale_return.print', compact('sale_invoice_return'));
    }

    public function edit(SaleInvoiceReturn $sale_invoice_return): View
    {
        $customers = Tree4::whereTree3_code(1205)->orWhere('tree3_code','2301')->get();
        $stocks = Stock::get();
        $representatives = Tree4::where('tree3_code', '1206')->get();
        $safes = Tree4::where('tree3_code', '1202')->get();
        $banks = Tree4::where('tree3_code', '1203')->get();
        $products = Product::with('stocks', 'units')->take(7)->inRandomOrder()->get();
        $productIds = $sale_invoice_return->details()->pluck('product_id')->toArray();
        return view('sale_return.edit', compact('sale_invoice_return', 'customers', 'stocks', 'representatives', 'safes', 'banks', 'products', 'productIds'));
    }

    public function update(UpdateSaleInvoiceRequest $request, SaleInvoiceReturn $sale_invoice_return): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $invoice_status = $request->deferend_amount != '' ? 0 : 1;
            $transaction_no = (float) str_replace(',', '', $request->bank_amount_total ?? '') > 0 ? $request->transaction_no : null;

            $old_representative_id = $sale_invoice_return->representative_id;
            $old_rep_percentage = (float) $sale_invoice_return->rep_percentage;

            $rep_percentage = 0;
            $rep_cut_amount = 0;
            if (!empty($request->representative_id)) {
                if ((int) $request->representative_id === (int) $old_representative_id) {
                    $rep_percentage = $old_rep_percentage;
                } else {
                    $rep_percentage = (float) (Tree4::find($request->representative_id)->rep_percentage ?? 0);
                }
                $rep_base_amount = (float) $request->total - ($request->tax_status ? (float) $request->tax : 0);
                $rep_cut_amount = round($rep_base_amount * $rep_percentage / 100, 2);
            }

            $oldDetails = $sale_invoice_return->details;

            foreach ($oldDetails as $item) {
                $product = Product::with(['stocks', 'units'])->find($item->product_id);
                if (!$product) {
                    continue;
                }

                $unit = $product->units()->where('unit_id', $item->unit_id)->first();
                $stock = $product->stocks()->where('stock_id', $sale_invoice_return->stock_id)->first();

                if (!$unit || !$stock) {
                    continue;
                }

                $qtyBack = ($item->qty + ($item->bounce ?? 0)) * $unit->pivot->conversion_factor;

                $product->stocks()->updateExistingPivot($sale_invoice_return->stock_id, ['quantity' => $stock->pivot->quantity - $qtyBack]);
            }

            StockDetail::where('sale_invoice_return_id', $sale_invoice_return->id)->where('branch_id', session('branch_id'))->delete();

            $sale_invoice_return->delete();

            $sale_invoice_return = SaleInvoiceReturn::create(
                attributes: [
                    'customer_id' => $request->get('customer_id'),
                    'representative_id' => $request->get('representative_id'),
                    'invoice_number' => $request->get('invoice_number'),
                    'date' => $request->get('date'),
                    'stock_id' => $request->get('stock_id'),
                    'sub_total_amount' => $request->get('sub_total'),
                    'discount_amount' => $request->get('discount'),
                    'tax_amount' => $request->get('tax'),
                    'tax_status' => $request->get('tax_status') ? 1 : 0,
                    'total_amount' => $request->get('total'),
                    'payment_status' => $request->get('payment_status'),
                    'status' => 1,
                    'cash_name' => $request->get('cash_name'),
                    'bank_name' => $request->get('bank_name'),
                    'cash_amount_total' => $request->get('cash_amount_total'),
                    'bank_amount_total' => $request->get('bank_amount_total'),
                    'transaction_no' => $transaction_no,
                    'process_no' => $request->get('process_no'),
                    'deferend_amount' => $request->get('deferend_amount'),
                    'invoice_status' => $invoice_status,
                    'rep_percentage' => $rep_percentage,
                    'rep_cut_amount' => $rep_cut_amount,
                    'note' => $request->note,
                    'user_id' => Auth::id(),
                    'branch_id' => session('branch_id'),
                ],
            );

            foreach ($request->product_id as $index => $product_id) {
                $sale_invoice_return->details()->create(
                    attributes: [
                        'product_id' => $product_id,
                        'qty' => $request->qty[$index],
                        'bounce' => $request->bounce[$index] ?? 0,
                        'expire_date' => $request->expire_date[$index],
                        'price' => $request->price[$index],
                        'unit_id' => $request->unit_id[$index],
                        'row_sub_total' => $request->row_sub_total[$index],
                    ],
                );

                $product = Product::whereId($product_id)->first();
                $unit = $product->units()->where('unit_id', $request->unit_id[$index])->first();
                $stock = $product->stocks()->where('stock_id', $request->stock_id)->first();

                $qty = ($request->qty[$index] + $request->bounce[$index]) * $unit->pivot->conversion_factor;

                $product->stocks()->updateExistingPivot($request->stock_id, ['quantity' => $stock->pivot->quantity + $qty]);

                StockDetail::create([
                    'product_id' => $product_id,
                    'unit_id' => $request->unit_id[$index],
                    'stock_id' => $request->stock_id,
                    'wared' => $qty,
                    'user_id' => Auth::id(),
                    'type' => 3,
                    'branch_id' => session('branch_id'),
                    'sale_invoice_return_id' => $sale_invoice_return->id,
                ]);
            }

            $last_constant_number = Restriction::where('branch_id', session('branch_id'))->max('constant_number');

            $constant_number = $last_constant_number ? $last_constant_number + 1 : 1;

            if (empty($request->supplier_id)) {
                if (!empty($request->cash_amount_total)) {
                    $cash = Tree4::find($request->cash_name);

                    $this->restriction(4, 520100001, $request->cash_amount_total, 0, $constant_number, $sale_invoice_return, 'فاتورة مشتريات تم الدفع عن طريق الكاش', $sale_invoice_return->date);

                    $this->restriction($cash->id, $cash->tree4_code, 0, $request->cash_amount_total, $constant_number, $sale_invoice_return, 'فاتورة مشتريات تم الدفع عن طريق الكاش', $sale_invoice_return->date);
                }

                if (!empty($request->bank_amount_total)) {
                    $bank = Tree4::find($request->bank_name);

                    $this->restriction(4, 520100001, $request->bank_amount_total, 0, $constant_number, $sale_invoice_return, 'فاتورة مشتريات تم الدفع عن طريق البنك', $sale_invoice_return->date, $transaction_no);

                    $this->restriction($bank->id, $bank->tree4_code, 0, $request->bank_amount_total, $constant_number, $sale_invoice_return, 'فاتورة مشتريات تم الدفع عن طريق البنك', $sale_invoice_return->date, $transaction_no);
                }
            } else {
                $supplier = Tree4::find($request->supplier_id);

                $this->restriction(4, 520100001, $request->total, 0, $constant_number, $sale_invoice_return, 'فاتورة مشتريات', $sale_invoice_return->date);

                $this->restriction($supplier->id, $supplier->tree4_code, 0, $request->total, $constant_number, $sale_invoice_return, 'فاتورة مشتريات', $sale_invoice_return->date);

                if (!empty($request->cash_amount_total)) {
                    $cash = Tree4::find($request->cash_name);

                    $this->restriction($cash->id, $cash->tree4_code, 0, $request->cash_amount_total, $constant_number, $sale_invoice_return, 'سداد كاش للمورد', $sale_invoice_return->date);

                    $this->restriction($supplier->id, $supplier->tree4_code, $request->cash_amount_total, 0, $constant_number, $sale_invoice_return, 'سداد كاش للمورد', $sale_invoice_return->date);
                }

                if (!empty($request->bank_amount_total)) {
                    $bank = Tree4::find($request->bank_name);

                    $this->restriction($bank->id, $bank->tree4_code, 0, $request->bank_amount_total, $constant_number, $sale_invoice_return, 'سداد بنك للمورد', $sale_invoice_return->date, $transaction_no);

                    $this->restriction($supplier->id, $supplier->tree4_code, $request->bank_amount_total, 0, $constant_number, $sale_invoice_return, 'سداد بنك للمورد', $sale_invoice_return->date, $transaction_no);
                }
            }

            DB::commit();

            return redirect()->route('sale_invoice_return.index')->with('success_created', 'Product created successfully.');
        } catch (\Exception $exception) {
            DB::rollback();
            return back()
                ->withErrors(['error' => $exception->getMessage()])
                ->withInput();
        }
    }

    public function destroy(SaleInvoiceReturn $sale_invoice_return): RedirectResponse
    {
        foreach ($sale_invoice_return->details as $item) {
            $product = Product::with('stocks', 'units')->find($item->product_id);
            $product_unit = $product->units()->where('unit_id', $item->unit_id)->first();
            $producStock = $product->stocks()->where('stock_id', $sale_invoice_return->stock_id)->first();
            $qty = ($item->qty + $item->bounce) * $product_unit->pivot->conversion_factor;
            $newQuantity = $producStock->pivot->quantity - $qty;
            $product->stocks()->syncWithoutDetaching([
                $sale_invoice_return->stock_id => ['quantity' => $newQuantity],
            ]);
        }

        $sale_invoice_return->delete();
        return back()->with('success_deleted', 'تم الحذف بنجاح ');
    }

    private function restriction($tree_id, $tree4_code, $madin, $dain, $constant_number, $sale_invoice_return, $node, $date, $transactionNo = null): Restriction
    {
        return Restriction::create(
            attributes: [
                'tree4_id' => $tree_id,
                'tree4_code' => $tree4_code,
                'madin' => $madin,
                'dain' => $dain,
                'date' => $date,
                'node' => $node,
                'constant_number' => $constant_number,
                'transaction_no' => $transactionNo,
                'type' => 3,
                'sale_invoice_return_id' => $sale_invoice_return->id,
                'branch_id' => session('branch_id'),
                'user_id' => Auth::id(),
            ],
        );
    }
}
