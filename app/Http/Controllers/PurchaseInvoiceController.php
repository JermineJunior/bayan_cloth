<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Tree4;
use App\Models\Product;
use App\Models\Operation;
use Illuminate\View\View;
use App\Models\Restriction;
use Illuminate\Http\Request;
use App\Models\PurchaseInvoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Purchase\StorePurchaseRequest;
use App\Http\Requests\Purchase\UpdatePurchaseRequest;

class PurchaseInvoiceController extends Controller
{
    public function index(): View
    {
        $purchase_invoices = PurchaseInvoice::get();
        return view('purchase.index', compact('purchase_invoices'));
    }

    public function create(): View
    {
        $check = PurchaseInvoice::orderBy('id', 'desc')->first();
        if ($check) {
            $last_invoice_number = $check->invoice_number;
            $invoice_number = $last_invoice_number + 1;
        } else {
            $invoice_number = random_int(999, 9999);
        }
        $suppliers = Tree4::whereTree3_code(2301)->whereStatus(1)->get();
        $stocks = Stock::statusActive()->get();
        return view('purchase.create', compact('suppliers', 'invoice_number', 'stocks'));
    }

    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $purchase_invoice = PurchaseInvoice::create(
                attributes: [
                    'supplier_id' => $request->get('supplier_id'),
                    'invoice_number' => $request->get('invoice_number'),
                    'invoice_date' => $request->get('invoice_date'),
                    'stock_id' => $request->get('stock_id'),
                    'sub_total_amount' => $request->get('sub_total'),
                    'discount_amount' => $request->get('discount'),
                    'tax_amount' => $request->get('tax'),
                    'total_amount' => $request->get('total'),
                    'payment_status' => $request->get('payment_status'),
                    'status' => 1,
                    'payment_method' => $request->get('payment_method'),
                    'cash_id' => $request->get('cash_id'),
                    'bank_id' => $request->get('bank_id'),
                    'cash_amount' => $request->get('cash_amount'),
                    'bank_amount' => $request->get('bank_amount'),
                ],
            );

            foreach ($request->product_id as $index => $product_id) {
                $purchase_invoice->details()->create(
                    attributes: [
                        'product_id' => $product_id,
                        'qty' => $request->qty[$index],
                        'price' => $request->price[$index],
                        'unit_id' => $request->unit_id[$index],
                        'row_sub_total' => $request->row_sub_total[$index],
                    ],
                );

                $product = Product::whereId($product_id)->first();
                $product_unit = $product->units()->where('unit_id', $request->unit_id[$index])->first();
                $qty = $request->qty[$index] * $product_unit->pivot->conversion_factor;

                $productStock = $product->stocks()->where('stock_id', $request->stock_id)->first();

                if ($productStock) {
                    $newQuantity = $productStock->pivot->quantity + $qty;
                    $product->stocks()->syncWithoutDetaching([
                        $request->stock_id => ['quantity' => $newQuantity],
                    ]);
                }
            }

            $last_constant_number = Operation::max('constant_number');
            $constant_number = $last_constant_number ? $last_constant_number + 1 : 1;

            if ($request->payment_status == 0 && $request->supplier_id == null) {
                $id = $request->cash_id != null ? $request->cash_id : ($request->bank_id != null ? $request->bank_id : '');
                if ($request->payment_method == 1 || $request->payment_method == 2) {
                    $tree4 = Tree4::find($id);

                    $operation = $this->operation(4, $tree4->id, 520100001, $tree4->tree4_code, $request->total, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع عن طريق الكاش');

                    $this->restriction(4, 520100001, $request->total, 0, $constant_number, $purchase_invoice, $operation, 'فاتورة مشتريات تم الدفع عن طريق الكاش');
                    $this->restriction($tree4->id, $tree4->tree4_code, 0, $request->total, $constant_number, $purchase_invoice, $operation, 'فاتورة مشتريات تم الدفع عن طريق الكاش');
                } elseif ($request->payment_method == 3) {
                    $tree4_cash = Tree4::find($request->cash_id);
                    $tree4_bank = Tree4::find($request->bank_id);

                    $operation_cash = $this->operation(4, $tree4_cash->id, 520100001, $tree4_cash->tree4_code, $request->cash_amount, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع عن طريق الكاش');
                    $operation_bank = $this->operation(4, $tree4_bank->id, 520100001, $tree4_bank->tree4_code, $request->bank_amount, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع عن طريق الكاش');

                    $this->restriction(4, 520100001, $request->total, 0, $constant_number, $purchase_invoice, $operation_bank, 'فاتورة مشتريات تم الدفع عن طريق الكاش');
                    $this->restriction($tree4_cash->id, $tree4_cash->tree4_code, 0, $request->cash_amount, $constant_number, $purchase_invoice, $operation_cash, 'فاتورة مشتريات تم الدفع عن طريق الكاش');
                    $this->restriction($tree4_bank->id, $tree4_bank->tree4_code, 0, $request->bank_amount, $constant_number, $purchase_invoice, $operation_bank, 'فاتورة مشتريات تم الدفع عن طريق الكاش');
                }
            } elseif ($request->payment_status == 0 && $request->supplier_id != null) {
                $tree4_supplier = Tree4::find($request->supplier_id);
                $id = $request->cash_id != null ? $request->cash_id : ($request->bank_id != null ? $request->bank_id : '');
                if ($request->payment_method == 1 || $request->payment_method == 2) {
                    $tree4 = Tree4::find($id);

                    $operation1 = $this->operation(4, $tree4_supplier->id, 520100001, $tree4_supplier->tree4_code, $request->total, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $operation2 = $this->operation($tree4_supplier->id, $tree4->id, $tree4_supplier->tree4_code, $tree4->tree4_code, $request->total, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');

                    $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, 0, $request->total, $constant_number, $purchase_invoice, $operation1, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction(4, 520100001, $request->total, 0, $constant_number, $purchase_invoice, $operation1, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4->id, $tree4->tree4_code, 0, $request->total, $constant_number, $purchase_invoice, $operation2, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, $request->total, 0, $constant_number, $purchase_invoice, $operation2, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                } elseif ($request->payment_method == 3) {
                    $tree4_cash = Tree4::find($request->cash_id);
                    $tree4_bank = Tree4::find($request->bank_id);

                    $operation1 = $this->operation(4, $tree4_supplier->id, 520100001, $tree4_supplier->tree4_code, $request->total, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $operation2 = $this->operation($tree4_supplier->id, $tree4_cash->id, $tree4_supplier->tree4_code, $tree4_cash->tree4_code, $request->cash_amount, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $operation3 = $this->operation($tree4_supplier->id, $tree4_bank->id, $tree4_supplier->tree4_code, $tree4_bank->tree4_code, $request->bank_amount, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');

                    $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, 0, $request->total, $constant_number, $purchase_invoice, $operation1, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction(4, 520100001, $request->total, 0, $constant_number, $purchase_invoice, $operation1, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_cash->id, $tree4_cash->tree4_code, 0, $request->cash_amount, $constant_number, $purchase_invoice, $operation2, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, $request->cash_amount, 0, $constant_number, $purchase_invoice, $operation2, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_bank->id, $tree4_bank->tree4_code, 0, $request->bank_amount, $constant_number, $purchase_invoice, $operation3, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, $request->bank_amount, 0, $constant_number, $purchase_invoice, $operation3, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                }
            } elseif ($request->payment_status == 1 && $request->supplier_id != null) {
                $tree4_supplier = Tree4::find($request->supplier_id);

                $operation = $this->operation(4, $tree4_supplier->id, 520100001, $tree4_supplier->tree4_code, $request->total, $constant_number, $purchase_invoice, 'فاتورة مشتريات عن طريق الاجل ');

                $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, 0, $request->total, $constant_number, $purchase_invoice, $operation, 'فاتورة مشتريات عن طريق الاجل ');
                $this->restriction(4, 520100001, $request->total, 0, $constant_number, $purchase_invoice, $operation, 'فاتورة مشتريات عن طريق الاجل ');
            }

            DB::commit();

            return back()->with('success_created', 'Product created successfully.');
        } catch (\Exception $exception) {
            DB::rollback();
            return redirect()
                ->route('purchase_invoice.create')
                ->withErrors(['error' => $exception->getMessage()])
                ->withInput();
        }
    }

    public function edit(PurchaseInvoice $purchase_invoice): View
    {
        $suppliers = Tree4::whereTree3_code(2301)->whereStatus(1)->get();
        $stocks = Stock::statusActive()->get();
        return view('purchase.edit', compact('purchase_invoice', 'suppliers', 'stocks'));
    }

    public function update(UpdatePurchaseRequest $request, PurchaseInvoice $purchase_invoice): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $purchase = $purchase_invoice->details;
            $purchase_invoice->delete();
            $purchase_invoice = PurchaseInvoice::create(
                attributes: [
                    'supplier_id' => $request->get('supplier_id'),
                    'invoice_number' => $request->get('invoice_number'),
                    'invoice_date' => $request->get('invoice_date'),
                    'stock_id' => $request->get('stock_id'),
                    'sub_total_amount' => $request->get('sub_total'),
                    'discount_amount' => $request->get('discount'),
                    'tax_amount' => $request->get('tax'),
                    'total_amount' => $request->get('total'),
                    'payment_status' => $request->get('payment_status'),
                    'status' => 1,
                    'payment_method' => $request->get('payment_method'),
                    'cash_id' => $request->get('cash_id'),
                    'bank_id' => $request->get('bank_id'),
                    'cash_amount' => $request->get('cash_amount'),
                    'bank_amount' => $request->get('bank_amount'),
                ],
            );

            foreach ($request->product_id as $index => $product_id) {
                foreach ($purchase as $item) {
                    if ($item->product_id == $product_id) {
                        $product = Product::with('stocks', 'units')->find($item->product_id);
                        $product_unit = $product->units()->where('unit_id', $item->unit_id)->first();
                        $producStock = $product->stocks()->where('stock_id', $purchase_invoice->stock_id)->first();
                        $qty = $item->qty * $product_unit->pivot->conversion_factor;
                        $newQuantity = $producStock->pivot->quantity - $qty;
                        $product->stocks()->syncWithoutDetaching([
                            $purchase_invoice->stock_id => ['quantity' => $newQuantity],
                        ]);
                    }
                }

                $purchase_invoice->details()->create(
                    attributes: [
                        'product_id' => $product_id,
                        'qty' => $request->qty[$index],
                        'price' => $request->price[$index],
                        'unit_id' => $request->unit_id[$index],
                        'row_sub_total' => $request->row_sub_total[$index],
                    ],
                );

                $product = Product::whereId($product_id)->first();
                $product_unit = $product->units()->where('unit_id', $request->unit_id[$index])->first();
                $qty = $request->qty[$index] * $product_unit->pivot->conversion_factor;

                $productStock = $product->stocks()->where('stock_id', $request->stock_id)->first();

                if ($productStock) {
                    $newQuantity = $productStock->pivot->quantity + $qty;
                    $product->stocks()->syncWithoutDetaching([
                        $request->stock_id => ['quantity' => $newQuantity],
                    ]);
                }
            }

            $last_constant_number = Operation::max('constant_number');
            $constant_number = $last_constant_number ? $last_constant_number + 1 : 1;

            if ($request->payment_status == 0 && $request->supplier_id == null) {
                $id = $request->cash_id != null ? $request->cash_id : ($request->bank_id != null ? $request->bank_id : '');
                if ($request->payment_method == 1 || $request->payment_method == 2) {
                    $tree4 = Tree4::find($id);

                    $operation = $this->operation(4, $tree4->id, 520100001, $tree4->tree4_code, $request->total, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع عن طريق الكاش');

                    $this->restriction(4, 520100001, $request->total, 0, $constant_number, $purchase_invoice, $operation, 'فاتورة مشتريات تم الدفع عن طريق الكاش');
                    $this->restriction($tree4->id, $tree4->tree4_code, 0, $request->total, $constant_number, $purchase_invoice, $operation, 'فاتورة مشتريات تم الدفع عن طريق الكاش');
                } elseif ($request->payment_method == 3) {
                    $tree4_cash = Tree4::find($request->cash_id);
                    $tree4_bank = Tree4::find($request->bank_id);

                    $operation_cash = $this->operation(4, $tree4_cash->id, 520100001, $tree4_cash->tree4_code, $request->cash_amount, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع عن طريق الكاش');
                    $operation_bank = $this->operation(4, $tree4_bank->id, 520100001, $tree4_bank->tree4_code, $request->bank_amount, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع عن طريق الكاش');

                    $this->restriction(4, 520100001, $request->total, 0, $constant_number, $purchase_invoice, $operation_bank, 'فاتورة مشتريات تم الدفع عن طريق الكاش');
                    $this->restriction($tree4_cash->id, $tree4_cash->tree4_code, 0, $request->cash_amount, $constant_number, $purchase_invoice, $operation_cash, 'فاتورة مشتريات تم الدفع عن طريق الكاش');
                    $this->restriction($tree4_bank->id, $tree4_bank->tree4_code, 0, $request->bank_amount, $constant_number, $purchase_invoice, $operation_bank, 'فاتورة مشتريات تم الدفع عن طريق الكاش');
                }
            } elseif ($request->payment_status == 0 && $request->supplier_id != null) {
                $tree4_supplier = Tree4::find($request->supplier_id);
                $id = $request->cash_id != null ? $request->cash_id : ($request->bank_id != null ? $request->bank_id : '');
                if ($request->payment_method == 1 || $request->payment_method == 2) {
                    $tree4 = Tree4::find($id);

                    $operation1 = $this->operation(4, $tree4_supplier->id, 520100001, $tree4_supplier->tree4_code, $request->total, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $operation2 = $this->operation($tree4_supplier->id, $tree4->id, $tree4_supplier->tree4_code, $tree4->tree4_code, $request->total, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');

                    $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, 0, $request->total, $constant_number, $purchase_invoice, $operation1, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction(4, 520100001, $request->total, 0, $constant_number, $purchase_invoice, $operation1, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4->id, $tree4->tree4_code, 0, $request->total, $constant_number, $purchase_invoice, $operation2, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, $request->total, 0, $constant_number, $purchase_invoice, $operation2, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                } elseif ($request->payment_method == 3) {
                    $tree4_cash = Tree4::find($request->cash_id);
                    $tree4_bank = Tree4::find($request->bank_id);

                    $operation1 = $this->operation(4, $tree4_supplier->id, 520100001, $tree4_supplier->tree4_code, $request->total, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $operation2 = $this->operation($tree4_supplier->id, $tree4_cash->id, $tree4_supplier->tree4_code, $tree4_cash->tree4_code, $request->cash_amount, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $operation3 = $this->operation($tree4_supplier->id, $tree4_bank->id, $tree4_supplier->tree4_code, $tree4_bank->tree4_code, $request->bank_amount, $constant_number, $purchase_invoice, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');

                    $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, 0, $request->total, $constant_number, $purchase_invoice, $operation1, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction(4, 520100001, $request->total, 0, $constant_number, $purchase_invoice, $operation1, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_cash->id, $tree4_cash->tree4_code, 0, $request->cash_amount, $constant_number, $purchase_invoice, $operation2, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, $request->cash_amount, 0, $constant_number, $purchase_invoice, $operation2, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_bank->id, $tree4_bank->tree4_code, 0, $request->bank_amount, $constant_number, $purchase_invoice, $operation3, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, $request->bank_amount, 0, $constant_number, $purchase_invoice, $operation3, 'فاتورة مشتريات تم الدفع بالكاش عن طريق عميل');
                }
            } elseif ($request->payment_status == 1 && $request->supplier_id != null) {
                $tree4_supplier = Tree4::find($request->supplier_id);

                $operation = $this->operation(4, $tree4_supplier->id, 520100001, $tree4_supplier->tree4_code, $request->total, $constant_number, $purchase_invoice, 'فاتورة مشتريات عن طريق الاجل ');

                $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, 0, $request->total, $constant_number, $purchase_invoice, $operation, 'فاتورة مشتريات عن طريق الاجل ');
                $this->restriction(4, 520100001, $request->total, 0, $constant_number, $purchase_invoice, $operation, 'فاتورة مشتريات عن طريق الاجل ');
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
            $qty = $item->qty * $product_unit->pivot->conversion_factor;
            $newQuantity = $producStock->pivot->quantity - $qty;
            $product->stocks()->syncWithoutDetaching([
                $purchase_invoice->stock_id => ['quantity' => $newQuantity],
            ]);
        }

        $purchase_invoice->delete();
        return back()->with('success_deleted', 'تم الحذف بنجاح ');
    }

    public function toggleStatus(PurchaseInvoice $purchase_invoice): JsonResponse
    {
        $purchase_invoice->update(['status' => !$purchase_invoice->status]);
        return response()->json(['status' => $purchase_invoice->status]);
    }

    public function updateQtyProduct(Request $request): JsonResponse
    {
        $request->validate([
            'deleted_products' => 'required|array',
            'deleted_products.*.id' => 'exists:products,id',
            'deleted_products.*.unitId' => 'exists:units,id',
            'deleted_products.*.qty' => 'required|integer|min:1',
        ]);

        foreach ($request->deleted_products as $product) {
            $productqty = Product::with('units', 'stocks')->firstWhere('id', $product['id']);
            $conversion_factor = $productqty->units()->firstWhere('unit_id', $product['unitId'])->pivot->conversion_factor;
            $qty = $product['qty'] * $conversion_factor;
            $productStock = $productqty->stocks->where('id', $request->stock_id)->first();
            $newQuantity = $productStock->pivot->quantity - $qty;
            $productqty->stocks()->syncWithoutDetaching([
                $request->stock_id => ['quantity' => $newQuantity],
            ]);
        }

        return response()->json(['success' => true, 'message' => 'تم تحديث الكمية بنجاح']);
    }

    private function operation($madin_id, $dain_id, $madin, $dain, $total, $constant_number, $purchase_invoice, $node): Operation
    {
        return Operation::create(
            attributes: [
                'madin_id' => $madin_id,
                'dain_id' => $dain_id,
                'madin' => $madin,
                'dain' => $dain,
                'price' => $total,
                'constant_number' => $constant_number,
                'node' => $node,
                'purchase_invoice_id' => $purchase_invoice->id,
                'type' => 2,
                'user_id' => Auth::id(),
            ],
        );
    }

    private function restriction($tree_id, $tree4_code, $madin, $dain, $constant_number, $purchase_invoice, $operation, $node): Restriction
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
                'type' => 2,
                'purchase_invoice_id' => $purchase_invoice->id,
                'user_id' => Auth::id(),
                'operation_id' => $operation->id,
            ],
        );
    }
}
