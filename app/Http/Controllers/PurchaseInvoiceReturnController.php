<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Tree4;
use App\Models\Product;
use Illuminate\View\View;
use App\Models\Restriction;
use App\Models\StockDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseInvoiceReturn;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Purchase\UpdatePurchaseRequest;
use App\Http\Requests\PurchaseReturn\StorePurchaseReturnRequest;

class PurchaseInvoiceReturnController extends Controller
{
    public function index(): View
    {
        $purchase_invoice_returns = PurchaseInvoiceReturn::where('branch_id', session('branch_id'))->orderByDesc('invoice_number')->get();
        return view('purchase_return.index', compact('purchase_invoice_returns'));
    }

    public function create(): View
    {
        $invoice_number = PurchaseInvoiceReturn::where('branch_id', session('branch_id'))->max('invoice_number') + 1;
        $suppliers = Tree4::whereTree3_code(1205)->orWhere('tree3_code','2301')->get();
        $representatives = Tree4::where('tree3_code', '1206')->get();
        $stocks = Stock::get();
        $safes = Tree4::where('tree3_code', '1202')->get();
        $banks = Tree4::where('tree3_code', '1203')->get();
        $products = Product::with('stocks', 'units')->take(7)->inRandomOrder()->get();
        return view('purchase_return.create', compact('suppliers', 'representatives', 'invoice_number', 'stocks', 'safes', 'banks', 'products'));
    }

    public function store(StorePurchaseReturnRequest $request): View|RedirectResponse
    {
        try {
            DB::beginTransaction();
            $invoice_status = $request->deferend_amount != '' ? 0 : 1;
            $transaction_no = (float) str_replace(',', '', $request->bank_amount_total ?? '') > 0 ? $request->transaction_no : null;
            $purchase_invoice_return = PurchaseInvoiceReturn::create(
                attributes: [
                    'supplier_id' => $request->get('supplier_id'),
                    'representative_id' => $request->get('representative_id'),
                    'invoice_number' => $request->get('invoice_number'),
                    'stock_id' => $request->get('stock_id'),
                    'date' => $request->get('date'),
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
                    'deferend_amount' => $request->get('deferend_amount'),
                    'process_no' => $request->get('process_no'),
                    'invoice_status' => $invoice_status,
                    'note' => $request->note,
                    'branch_id' => session('branch_id'),
                    'user_id' => Auth::id(),
                ],
            );

            foreach ($request->product_id as $index => $product_id) {
                $purchase_invoice_return->details()->create(
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
                $qty = ($request->qty[$index] + $request->bounce[$index]) * $unit->pivot->conversion_factor;
                $stock = $product->stocks()->where('stock_id', $request->stock_id)->first();
                $availableQty = $stock ? $stock->pivot->quantity : 0;
                if ($qty > $availableQty) {
                    DB::rollBack();
                    return back()->withErrors(['error' => 'الكمية باحد المنتجات أكبر من الكمية الموجودة في المخزن.']);
                }

                StockDetail::create([
                    'product_id' => $product_id,
                    'unit_id' => $request->unit_id[$index],
                    'stock_id' => $request->stock_id,
                    'sader' => $qty,
                    'user_id' => Auth::id(),
                    'type' => 4,
                    'branch_id' => session('branch_id'),
                    'purchase_invoice_return_id' => $purchase_invoice_return->id,
                ]);

                $newQuantity = $availableQty - $qty;
                    $product->stocks()->syncWithoutDetaching([
                        $request->stock_id => ['quantity' => $newQuantity],
                    ]);
            }

            $last_constant_number = Restriction::where('branch_id', session('branch_id'))->max('constant_number');

            $constant_number = $last_constant_number ? $last_constant_number + 1 : 1;

            if (empty($request->customer_id)) {
                if (!empty($request->cash_amount_total)) {
                    $cash = Tree4::find($request->cash_name);

                    $this->restriction($cash->id, $cash->tree4_code, $request->cash_amount_total, 0, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات نقدي', $purchase_invoice_return->date);

                    $this->restriction(3, 420100001, 0, $request->cash_amount_total, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات نقدي', $purchase_invoice_return->date);
                }

                if (!empty($request->bank_amount_total)) {
                    $bank = Tree4::find($request->bank_name);

                    $this->restriction($bank->id, $bank->tree4_code, $request->bank_amount_total, 0, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات بنك', $purchase_invoice_return->date, $transaction_no);

                    $this->restriction(3, 420100001, 0, $request->bank_amount_total, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات بنك', $purchase_invoice_return->date, $transaction_no);
                }
            } else {
                $customer = Tree4::find($request->customer_id);

                $this->restriction($customer->id, $customer->tree4_code, $request->total, 0, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات', $purchase_invoice_return->date);

                $this->restriction(3, 420100001, 0, $request->total, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات', $purchase_invoice_return->date);

                if (!empty($request->cash_amount_total)) {
                    $cash = Tree4::find($request->cash_name);

                    $this->restriction($cash->id, $cash->tree4_code, $request->cash_amount_total, 0, $constant_number, $purchase_invoice_return, 'سداد كاش', $purchase_invoice_return->date);

                    $this->restriction($customer->id, $customer->tree4_code, 0, $request->cash_amount_total, $constant_number, $purchase_invoice_return, 'سداد كاش', $purchase_invoice_return->date);
                }

                if (!empty($request->bank_amount_total)) {
                    $bank = Tree4::find($request->bank_name);

                    $this->restriction($bank->id, $bank->tree4_code, $request->bank_amount_total, 0, $constant_number, $purchase_invoice_return, 'سداد بنك', $purchase_invoice_return->date, $transaction_no);

                    $this->restriction($customer->id, $customer->tree4_code, 0, $request->bank_amount_total, $constant_number, $purchase_invoice_return, 'سداد بنك', $purchase_invoice_return->date, $transaction_no);
                }
            }

            DB::commit();

            return redirect()->route('purchase_invoice_return.show',$purchase_invoice_return->id);
        } catch (\Exception $exception) {
            DB::rollback();
            return redirect()
                ->route('purchase_invoice_return.create')
                ->withErrors(['error' => $exception->getMessage()])
                ->withInput();
        }
    }

    public function show(PurchaseInvoiceReturn $purchase_invoice_return)
    {
        $purchase_invoice_return = $purchase_invoice_return->load('details');
        return view('purchase_return.print', compact('purchase_invoice_return'));
    }

    public function edit(PurchaseInvoiceReturn $purchase_invoice_return): View
    {
        $suppliers = Tree4::whereTree3_code(1205)->orWhere('tree3_code','2301')->get();
        $representatives = Tree4::where('tree3_code', '1206')->get();
        $stocks = Stock::get();
        $safes = Tree4::where('tree3_code', '1202')->get();
        $banks = Tree4::where('tree3_code', '1203')->get();
        $products = Product::with('stocks', 'units')->take(7)->inRandomOrder()->get();
        $productIds = $purchase_invoice_return->details()->pluck('product_id')->toArray();
        return view('purchase_return.edit', compact('purchase_invoice_return', 'suppliers', 'representatives', 'stocks', 'safes', 'banks', 'products', 'productIds'));
    }

    public function update(UpdatePurchaseRequest $request, PurchaseInvoiceReturn $purchase_invoice_return): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $invoice_status = $request->deferend_amount != '' ? 0 : 1;
            $transaction_no = (float) str_replace(',', '', $request->bank_amount_total ?? '') > 0 ? $request->transaction_no : null;

            $oldDetails = $purchase_invoice_return->details;

            foreach ($oldDetails as $item) {
                $product = Product::with(['stocks', 'units'])->find($item->product_id);
                if (!$product) {
                    continue;
                }

                $unit = $product->units()->where('unit_id', $item->unit_id)->first();
                $stock = $product->stocks()->where('stock_id', $purchase_invoice_return->stock_id)->first();

                if (!$unit || !$stock) {
                    continue;
                }

                $qtyBack = ($item->qty + ($item->bounce ?? 0)) * $unit->pivot->conversion_factor;

                $product->stocks()->updateExistingPivot($purchase_invoice_return->stock_id, ['quantity' => $stock->pivot->quantity + $qtyBack]);
            }

            $purchase_invoice_return->delete();

            $purchase_invoice_return = PurchaseInvoiceReturn::create(
                attributes: [
                    'supplier_id' => $request->get('supplier_id'),
                    'representative_id' => $request->get('representative_id'),
                    'invoice_number' => $request->get('invoice_number'),
                    'stock_id' => $request->get('stock_id'),
                    'date' => $request->get('date'),
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
                    'deferend_amount' => $request->get('deferend_amount'),
                    'process_no' => $request->get('process_no'),
                    'invoice_status' => $invoice_status,
                    'note' => $request->note,
                    'branch_id' => session('branch_id'),
                    'user_id' => Auth::id(),
                ],
            );

            foreach ($request->product_id as $index => $product_id) {
                $purchase_invoice_return->details()->create(
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
                $qty = ($request->qty[$index] + $request->bounce[$index]) * $unit->pivot->conversion_factor;
                $stock = $product->stocks()->where('stock_id', $request->stock_id)->first();
                $availableQty = $stock ? $stock->pivot->quantity : 0;
                if ($qty > $availableQty) {
                    DB::rollBack();
                    return back()->withErrors(['error' => 'الكمية باحد المنتجات أكبر من الكمية الموجودة في المخزن.']);
                }

                StockDetail::create([
                    'product_id' => $product_id,
                    'unit_id' => $request->unit_id[$index],
                    'stock_id' => $request->stock_id,
                    'sader' => $qty,
                    'user_id' => Auth::id(),
                    'type' => 4,
                    'branch_id' => session('branch_id'),
                    'purchase_invoice_return_id' => $purchase_invoice_return->id,
                ]);

                $product->stocks()->updateExistingPivot($request->stock_id, ['quantity' => $availableQty - $qty]);
            }

            $last_constant_number = Restriction::where('branch_id', session('branch_id'))->max('constant_number');

            $constant_number = $last_constant_number ? $last_constant_number + 1 : 1;

            if (empty($request->customer_id)) {
                if (!empty($request->cash_amount_total)) {
                    $cash = Tree4::find($request->cash_name);

                    $this->restriction($cash->id, $cash->tree4_code, $request->cash_amount_total, 0, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات نقدي', $purchase_invoice_return->date);

                    $this->restriction(3, 420100001, 0, $request->cash_amount_total, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات نقدي', $purchase_invoice_return->date);
                }

                if (!empty($request->bank_amount_total)) {
                    $bank = Tree4::find($request->bank_name);

                    $this->restriction($bank->id, $bank->tree4_code, $request->bank_amount_total, 0, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات بنك', $purchase_invoice_return->date, $transaction_no);

                    $this->restriction(3, 420100001, 0, $request->bank_amount_total, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات بنك', $purchase_invoice_return->date, $transaction_no);
                }
            } else {
                $customer = Tree4::find($request->customer_id);

                $this->restriction($customer->id, $customer->tree4_code, $request->total, 0, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات', $purchase_invoice_return->date);

                $this->restriction(3, 420100001, 0, $request->total, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات', $purchase_invoice_return->date);

                if (!empty($request->cash_amount_total)) {
                    $cash = Tree4::find($request->cash_name);

                    $this->restriction($cash->id, $cash->tree4_code, $request->cash_amount_total, 0, $constant_number, $purchase_invoice_return, 'سداد كاش', $purchase_invoice_return->date);

                    $this->restriction($customer->id, $customer->tree4_code, 0, $request->cash_amount_total, $constant_number, $purchase_invoice_return, 'سداد كاش', $purchase_invoice_return->date);
                }

                if (!empty($request->bank_amount_total)) {
                    $bank = Tree4::find($request->bank_name);

                    $this->restriction($bank->id, $bank->tree4_code, $request->bank_amount_total, 0, $constant_number, $purchase_invoice_return, 'سداد بنك', $purchase_invoice_return->date, $transaction_no);

                    $this->restriction($customer->id, $customer->tree4_code, 0, $request->bank_amount_total, $constant_number, $purchase_invoice_return, 'سداد بنك', $purchase_invoice_return->date, $transaction_no);
                }
            }

            DB::commit();

            return redirect()->route('purchase_invoice_return.index')->with('success_updated', 'تم تحديث البيانات بنجاج');
        } catch (\Exception $exception) {
            DB::rollback();
            return back()
                ->withErrors(['error' => $exception->getMessage()])
                ->withInput();
        }
    }

    public function destroy(PurchaseInvoiceReturn $purchase_invoice_return, Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            foreach ($purchase_invoice_return->details as $item) {
                $product = Product::with('stocks', 'units')->find($item->product_id);
                $producStock = $product->stocks()->where('stock_id', $purchase_invoice_return->stock_id)->first();
                $conversion_factor = $product->units()->firstWhere('unit_id', $item->unit_id)->pivot->conversion_factor;
                $qty = ($item->qty + $item->bounce) * $conversion_factor;
                $newQuantity = $producStock->pivot->quantity + $qty;
                $product->stocks()->syncWithoutDetaching([
                    $purchase_invoice_return->stock_id => ['quantity' => $newQuantity],
                ]);
            }
            $purchase_invoice_return->delete();
            DB::commit();
            return back()->with('success_deleted', 'تم الحذف بنجاح ');
        } catch (\Exception $exception) {
            DB::rollback();
            return back()->with('error', 'حدث خطا ما يرحى المحاولة مرةاخرى');
        }
    }

    private function restriction($tree_id, $tree4_code, $madin, $dain, $constant_number, $purchase_invoice_return, $node,$date, $transactionNo = null): Restriction
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
                'type' => 4,
                'purchase_invoice_return_id' => $purchase_invoice_return->id,
                'branch_id' => session('branch_id'),
                'user_id' => Auth::id(),
            ],
        );
    }
}
