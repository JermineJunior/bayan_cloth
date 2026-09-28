<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Stock;
use App\Models\Tree4;
use App\Models\Product;
use Illuminate\View\View;
use App\Models\Restriction;
use App\Models\StockDetail;
use Illuminate\Http\Request;
use App\Models\PurchaseInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Purchase\StorePurchaseRequest;
use App\Http\Requests\Purchase\UpdatePurchaseRequest;

class PurchaseInvoiceController extends Controller
{
    public function index(): View
    {
        $purchase_invoices = PurchaseInvoice::where('branch_id', session('branch_id'))->orderByDesc('invoice_number')->get();
        return view('purchase.index', compact('purchase_invoices'));
    }

    public function create(): View
    {
        $invoice_number = PurchaseInvoice::where('branch_id', session('branch_id'))->max('invoice_number') + 1;
        $suppliers = Tree4::whereTree3_code(1205)->orWhere('tree3_code','2301')->get();
        $representatives = Tree4::where('tree3_code', '1206')->orderByDesc('id')->get();
        $stocks = Stock::get();
        $safes = Tree4::where('tree3_code', '1202')->get();
        $banks = Tree4::where('tree3_code', '1203')->get();
        $products = Product::with('stocks', 'units')->take(7)->inRandomOrder()->get();
        return view('purchase.create', compact('suppliers', 'representatives', 'invoice_number', 'stocks', 'safes', 'banks', 'products'));
    }

    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $invoice_status = $request->deferend_amount != '' ? 0 : 1;
            $transaction_no = (float) str_replace(',', '', $request->bank_amount_total ?? '') > 0 ? $request->transaction_no : null;
            $purchase_invoice = PurchaseInvoice::create(
                attributes: [
                    'supplier_id' => $request->get('supplier_id'),
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
                    'branch_id' => session('branch_id'),
                    'user_id' => Auth::id(),
                ],
            );

            foreach ($request->product_id as $index => $product_id) {
                $purchase_invoice->details()->create(
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
                $qty = ((float) str_replace(',', '', $request->qty[$index]) + (float) str_replace(',', '', $request->bounce[$index])) * $product_unit->pivot->conversion_factor;

                $productStock = $product->stocks()->where('stock_id', $request->stock_id)->first();

                StockDetail::create([
                    'product_id' => $product_id,
                    'unit_id' => $request->unit_id[$index],
                    'stock_id' => $request->stock_id,
                    'wared' => $qty,
                    'user_id' => Auth::id(),
                    'type' => 2,
                    'branch_id' => session('branch_id'),
                    'purchase_invoice_id' => $purchase_invoice->id,
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

                    $this->restriction(4, 520100001, $request->cash_amount_total, 0, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع عن طريق الكاش', $purchase_invoice->date);

                    $this->restriction($cash->id, $cash->tree4_code, 0, $request->cash_amount_total, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع عن طريق الكاش', $purchase_invoice->date);
                }

                if (!empty($request->bank_amount_total)) {
                    $bank = Tree4::find($request->bank_name);

                    $this->restriction(4, 520100001, $request->bank_amount_total, 0, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع عن طريق البنك', $purchase_invoice->date, $transaction_no);

                    $this->restriction($bank->id, $bank->tree4_code, 0, $request->bank_amount_total, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع عن طريق البنك', $purchase_invoice->date, $transaction_no);
                }
            } else {
                $supplier = Tree4::find($request->supplier_id);

                $this->restriction(4, 520100001, $request->total, 0, $constant_number, $purchase_invoice, 'فاتورة مشتريات', $purchase_invoice->date);

                $this->restriction($supplier->id, $supplier->tree4_code, 0, $request->total, $constant_number, $purchase_invoice, 'فاتورة مشتريات', $purchase_invoice->date);

                if (!empty($request->cash_amount_total)) {
                    $cash = Tree4::find($request->cash_name);

                    $this->restriction($cash->id, $cash->tree4_code, 0, $request->cash_amount_total, $constant_number, $purchase_invoice, 'سداد كاش للمورد', $purchase_invoice->date);

                    $this->restriction($supplier->id, $supplier->tree4_code, $request->cash_amount_total, 0, $constant_number, $purchase_invoice, 'سداد كاش للمورد', $purchase_invoice->date);
                }

                if (!empty($request->bank_amount_total)) {
                    $bank = Tree4::find($request->bank_name);

                    $this->restriction($bank->id, $bank->tree4_code, 0, $request->bank_amount_total, $constant_number, $purchase_invoice, 'سداد بنك للمورد', $purchase_invoice->date, $transaction_no);

                    $this->restriction($supplier->id, $supplier->tree4_code, $request->bank_amount_total, 0, $constant_number, $purchase_invoice, 'سداد بنك للمورد', $purchase_invoice->date, $transaction_no);
                }
            }

            DB::commit();

            return redirect()->route('purchase_invoice.show', $purchase_invoice->id);
        } catch (\Exception $exception) {
            DB::rollback();
            return redirect()
                ->route('purchase_invoice.create')
                ->withErrors(['error' => $exception->getMessage()])
                ->withInput();
        }
    }

    public function show(PurchaseInvoice $purchase_invoice)
    {
        $purchase_invoice = $purchase_invoice->load('tree4', 'details', 'details.product', 'details.unit');
        if ($purchase_invoice->supplier_id) {
            $restriction = Restriction::where('purchase_invoice_id', $purchase_invoice->id)->latest()->first();
            $madinTotal = Restriction::where('tree4_id', $purchase_invoice->supplier_id)->where('constant_number', '<', $restriction->constant_number)->sum('madin');
            $dainTotal = Restriction::where('tree4_id', $purchase_invoice->supplier_id)->where('constant_number', '<', $restriction->constant_number)->sum('dain');
            $allMadinTotal = Restriction::where('tree4_id', $purchase_invoice->supplier_id)->sum('madin');

            $allDainTotal = Restriction::where('tree4_id', $purchase_invoice->supplier_id)->sum('dain');
        }

        $bank = Tree4::where('tree3_code', 1203)->select('no_bank')->first();
        return view('purchase.print', compact('purchase_invoice', 'allDainTotal', 'allMadinTotal', 'dainTotal', 'madinTotal', 'bank'));
    }

    public function edit(PurchaseInvoice $purchase_invoice): View
    {
        $suppliers = Tree4::whereTree3_code(1205)->orWhere('tree3_code','2301')->get();
        $representatives = Tree4::where('tree3_code', '1206')->get();
        $stocks = Stock::get();
        $safes = Tree4::where('tree3_code', '1202')->get();
        $banks = Tree4::where('tree3_code', '1203')->get();
        $products = Product::with('stocks', 'units')->take(7)->inRandomOrder()->get();
        $productIds = $purchase_invoice->details()->pluck('product_id')->toArray();
        return view('purchase.edit', compact('purchase_invoice', 'suppliers', 'representatives', 'stocks', 'safes', 'banks', 'products', 'productIds'));
    }

    public function update(UpdatePurchaseRequest $request, PurchaseInvoice $purchase_invoice): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $invoice_status = $request->deferend_amount != '' ? 0 : 1;
            $transaction_no = (float) str_replace(',', '', $request->bank_amount_total ?? '') > 0 ? $request->transaction_no : null;

            $oldDetails = $purchase_invoice->details;

            foreach ($oldDetails as $item) {
                $product = Product::with(['stocks', 'units'])->find($item->product_id);
                if (!$product) {
                    continue;
                }

                $unit = $product->units()->where('unit_id', $item->unit_id)->first();
                $stock = $product->stocks()->where('stock_id', $purchase_invoice->stock_id)->first();

                if (!$unit || !$stock) {
                    continue;
                }

                $qtyBack = ($item->qty + ($item->bounce ?? 0)) * $unit->pivot->conversion_factor;
                $product->stocks()->updateExistingPivot($purchase_invoice->stock_id, ['quantity' => $stock->pivot->quantity - $qtyBack]);
            }

            $purchase_invoice->delete();

            $purchase_invoice = PurchaseInvoice::create(
                attributes: [
                    'supplier_id' => $request->get('supplier_id'),
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
                    'branch_id' => session('branch_id'),
                    'user_id' => Auth::id(),
                ],
            );

            foreach ($request->product_id as $index => $product_id) {
                $purchase_invoice->details()->create(
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
                    'branch_id' => session('branch_id'),
                    'type' => 2,
                    'purchase_invoice_id' => $purchase_invoice->id,
                ]);
            }

            $last_constant_number = Restriction::where('branch_id', session('branch_id'))->max('constant_number');

            $constant_number = $last_constant_number ? $last_constant_number + 1 : 1;

            if (empty($request->supplier_id)) {
                if (!empty($request->cash_amount_total)) {
                    $cash = Tree4::find($request->cash_name);

                    $this->restriction(4, 520100001, $request->cash_amount_total, 0, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع عن طريق الكاش', $purchase_invoice->date);

                    $this->restriction($cash->id, $cash->tree4_code, 0, $request->cash_amount_total, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع عن طريق الكاش', $purchase_invoice->date);
                }

                if (!empty($request->bank_amount_total)) {
                    $bank = Tree4::find($request->bank_name);

                    $this->restriction(4, 520100001, $request->bank_amount_total, 0, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع عن طريق البنك', $purchase_invoice->date, $transaction_no);

                    $this->restriction($bank->id, $bank->tree4_code, 0, $request->bank_amount_total, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع عن طريق البنك', $purchase_invoice->date, $transaction_no);
                }
            } else {
                $supplier = Tree4::find($request->supplier_id);

                $this->restriction(4, 520100001, $request->total, 0, $constant_number, $purchase_invoice, 'فاتورة مشتريات', $purchase_invoice->date);

                $this->restriction($supplier->id, $supplier->tree4_code, 0, $request->total, $constant_number, $purchase_invoice, 'فاتورة مشتريات', $purchase_invoice->date);

                if (!empty($request->cash_amount_total)) {
                    $cash = Tree4::find($request->cash_name);

                    $this->restriction($cash->id, $cash->tree4_code, 0, $request->cash_amount_total, $constant_number, $purchase_invoice, 'سداد كاش للمورد', $purchase_invoice->date);

                    $this->restriction($supplier->id, $supplier->tree4_code, $request->cash_amount_total, 0, $constant_number, $purchase_invoice, 'سداد كاش للمورد', $purchase_invoice->date);
                }

                if (!empty($request->bank_amount_total)) {
                    $bank = Tree4::find($request->bank_name);

                    $this->restriction($bank->id, $bank->tree4_code, 0, $request->bank_amount_total, $constant_number, $purchase_invoice, 'سداد بنك للمورد', $purchase_invoice->date, $transaction_no);

                    $this->restriction($supplier->id, $supplier->tree4_code, $request->bank_amount_total, 0, $constant_number, $purchase_invoice, 'سداد بنك للمورد', $purchase_invoice->date, $transaction_no);
                }
            }

            DB::commit();

            return redirect()->route('purchase_invoice.index')->with('success_created', 'Product created successfully.');
        } catch (\Exception $exception) {
            DB::rollback();
            return back()
                ->withErrors(['error' => $exception->getMessage()])
                ->withInput();
        }
    }

    public function destroy(PurchaseInvoice $purchase_invoice): RedirectResponse
    {
        foreach ($purchase_invoice->details as $item) {
            $product = Product::with('stocks', 'units')->find($item->product_id);
            $product_unit = $product->units()->where('unit_id', $item->unit_id)->first();
            $producStock = $product->stocks()->where('stock_id', $purchase_invoice->stock_id)->first();
            $qty = ($item->qty + $item->bounce) * $product_unit->pivot->conversion_factor;
            $newQuantity = $producStock->pivot->quantity - $qty;
            $product->stocks()->syncWithoutDetaching([
                $purchase_invoice->stock_id => ['quantity' => $newQuantity],
            ]);
        }

        $purchase_invoice->delete();
        return back()->with('success_deleted', 'تم الحذف بنجاح ');
    }

    private function restriction($tree_id, $tree4_code, $madin, $dain, $constant_number, $purchase_invoice, $node, $date, $transactionNo = null): Restriction
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
                'type' => 2,
                'purchase_invoice_id' => $purchase_invoice->id,
                'user_id' => Auth::id(),
                'branch_id' => session('branch_id'),
            ],
        );
    }

    public function externalPurchase_create(PurchaseInvoice $purchase_invoice)
    {
        $operations = Restriction::where('purchase_invoice_id', $purchase_invoice->id)->where('branch_id', session('branch_id'))->where('type', 10)->whereYear('created_at', $purchase_invoice->created_at->year)->get();
        // dd($operations);
        $madin_trees = Tree4::where('tree3_code', 5301)->get();
        $dain_trees = Tree4::where('tree3_code', 1203)->orWhere('tree3_code', 1202)->get();
        return view('purchase.external', compact('purchase_invoice', 'dain_trees', 'madin_trees', 'operations'));
    }

    public function externalPurchase_store(Request $request, PurchaseInvoice $purchase_invoice): RedirectResponse
    {
        try {
            DB::beginTransaction();
            Restriction::where(['purchase_invoice_id' => $purchase_invoice->id, 'branch_id' => session('branch_id'), 'type' => 10])
                ->whereYear('created_at', $purchase_invoice->created_at->year)
                ->delete();
            $constant_number = Restriction::where('branch_id', session('branch_id'))->max('constant_number') + 1;
            foreach ($request->madin as $index => $madin) {
                $madin = Tree4::find($madin);

                Restriction::create([
                    'tree4_id' => $madin->id,
                    'tree4_code' => $madin->tree4_code,
                    'madin' => $request->price_madin[$index],
                    'dain' => 0,
                    'date' => now(),
                    'node' => 'عبارة عن منصرفات مشتريات خارجية',
                    'constant_number' => $constant_number,
                    'type' => 10,
                    'branch_id' => session('branch_id'),
                    'user_id' => Auth::id(),
                    'purchase_invoice_id' => $purchase_invoice->id,
                ]);
            }

            foreach ($request->dain as $index => $dain) {
                $dain = Tree4::find($dain);

                Restriction::create([
                    'tree4_id' => $dain->id,
                    'tree4_code' => $dain->tree4_code,
                    'madin' => 0,
                    'dain' => $request->price_dain[$index],
                    'date' => now(),
                    'node' => 'مصرفات مشتريات خارجية',
                    'constant_number' => $constant_number,
                    'type' => 10,
                    'branch_id' => session('branch_id'),
                    'user_id' => Auth::id(),
                    'purchase_invoice_id' => $purchase_invoice->id,
                ]);
            }
            DB::commit();
            return redirect()->route('daily.show', $constant_number)->with('success_created', 'successfully created');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('purchase_invoice.index')->withErrors($e->getMessage());
        }
    }
}
