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
use App\Models\PurchaseInvoiceReturn;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\PurchaseReturn\StorePurchaseReturnRequest;

class PurchaseInvoiceReturnController extends Controller
{
    public function create(Request $request): View
    {
        $check = PurchaseInvoiceReturn::orderBy('id', 'desc')->first();
        if ($check) {
            $last_invoice_number = $check->invoice_number;
            $invoice_number = $last_invoice_number + 1;
        } else {
            $invoice_number = random_int(999, 9999);
        }
        $suppliers = Tree4::whereTree3_code(2301)->whereStatus(1)->get();
        $id = $request->purchase_invoice;
        $purchase_invoice = PurchaseInvoice::find($id) ?? null;
        $stocks = Stock::statusActive()->get();
        return view('purchase_return.create', compact('suppliers', 'purchase_invoice', 'invoice_number', 'stocks'));
    }

    public function store(StorePurchaseReturnRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $check = PurchaseInvoiceReturn::orderBy('id', 'desc')->first();
            if ($check) {
                $last_invoice_number = $check->invoice_number;
                $invoice_number = $last_invoice_number + 1;
            } else {
                $invoice_number = random_int(999, 9999);
            }

            $purchase_invoice_return = PurchaseInvoiceReturn::create(
                attributes: [
                    'supplier_id' => $request->get('supplier_id'),
                    'invoice_number' => $invoice_number,
                    'purchase_invoice_id' => $request->get('purchase_invoice_id'),
                    'stock_id' => $request->get('stock_id'),
                    'purchase_invoice_number' => $request->get('purchase_invoice_number'),
                    'invoice_date' => $request->get('invoice_date'),
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

            $purchase_invoice = PurchaseInvoice::with('details')->find($request->get('purchase_invoice_id'));

            $check_invoice_return = PurchaseInvoiceReturn::wherePurchase_invoice_id($purchase_invoice->id)->first();

            foreach ($request->product_id as $index => $product_id) {
                foreach ($check_invoice_return->details as $item) {
                    if ($item->product_id == $product_id) {
                        $product = Product::with('stocks', 'units')->find($item->product_id);
                        $product_unit = $product->units()->where('unit_id', $item->unit_id)->first();
                        $producStock = $product->stocks()->where('stock_id', $check_invoice_return->stock_id)->first();
                        $qty = $item->qty * $product_unit->pivot->conversion_factor;
                        $newQuantity = $producStock->pivot->quantity + $qty;
                        $product->stocks()->syncWithoutDetaching([
                            $check_invoice_return->stock_id => ['quantity' => $newQuantity],
                        ]);
                    }
                }

                $purchase_invoice_return->details()->create(
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
                $newQuantity = $productStock->pivot->quantity - $qty;
                $product->stocks()->syncWithoutDetaching([
                    $request->stock_id => ['quantity' => $newQuantity],
                ]);
            }

            $last_constant_number = Operation::max('constant_number');
            $constant_number = $last_constant_number ? $last_constant_number + 1 : 1;

            if ($request->payment_status == 0 && $request->supplier_id == null) {
                $id = $request->cash_id != null ? $request->cash_id : ($request->bank_id != null ? $request->bank_id : '');
                if ($request->payment_method == 1 || $request->payment_method == 2) {
                    $tree4 = Tree4::find($id);

                    $operation = $this->operation($tree4->id, 3, $tree4->tree4_code, 420100001, $request->total, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات تم الدفع عن طريق الكاش');

                    $this->restriction(3, 420100001, 0, $request->total, $constant_number, $purchase_invoice_return, $operation, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                    $this->restriction($tree4->id, $tree4->tree4_code, $request->total, 0, $constant_number, $purchase_invoice_return, $operation, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                } elseif ($request->payment_method == 3) {
                    $tree4_cash = Tree4::find($request->cash_id);
                    $tree4_bank = Tree4::find($request->bank_id);

                    $operation_cash = $this->operation($tree4_cash->id, 3, $tree4_cash->tree4_code, 420100001, $request->cash_amount, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                    $operation_bank = $this->operation($tree4_bank->id, 3, $tree4_bank->tree4_code, 420100001, $request->bank_amount, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات تم الدفع عن طريق الكاش');

                    $this->restriction(3, 420100001, 0, $request->total, $constant_number, $purchase_invoice_return, $operation_bank, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                    $this->restriction($tree4_cash->id, $tree4_cash->tree4_code, $request->cash_amount, 0, $constant_number, $purchase_invoice_return, $operation_cash, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                    $this->restriction($tree4_bank->id, $tree4_bank->tree4_code, $request->bank_amount, 0, $constant_number, $purchase_invoice_return, $operation_bank, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                }
            } elseif ($request->payment_status == 0 && $request->supplier_id != null) {
                $tree4_supplier = Tree4::find($request->supplier_id);
                $id = $request->cash_id != null ? $request->cash_id : ($request->bank_id != null ? $request->bank_id : '');
                if ($request->payment_method == 1 || $request->payment_method == 2) {
                    $tree4 = Tree4::find($id);

                    $operation1 = $this->operation($tree4_supplier->id, 3, $tree4_supplier->tree4_code, 420100001, $request->total, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $operation2 = $this->operation($tree4->id, $tree4_supplier->id, $tree4->tree4_code, $tree4_supplier->tree4_code, $request->total, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');

                    $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, $request->total, 0, $constant_number, $purchase_invoice_return, $operation1, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction(3, 420100001, 0, $request->total, $constant_number, $purchase_invoice_return, $operation1, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4->id, $tree4->tree4_code, $request->total, 0, $constant_number, $purchase_invoice_return, $operation2, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, 0, $request->total, $constant_number, $purchase_invoice_return, $operation2, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                } elseif ($request->payment_method == 3) {
                    $tree4_cash = Tree4::find($request->cash_id);
                    $tree4_bank = Tree4::find($request->bank_id);

                    $operation1 = $this->operation($tree4_supplier->id, 3, $tree4_supplier->tree4_code, 420100001, $request->total, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $operation2 = $this->operation($tree4_cash->id, $tree4_supplier->id, $tree4_cash->tree4_code, $tree4_supplier->tree4_code, $request->cash_amount, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $operation3 = $this->operation($tree4_bank->id, $tree4_supplier->id, $tree4_bank->tree4_code, $tree4_supplier->tree4_code, $request->bank_amount, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');

                    $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, $request->total, 0, $constant_number, $purchase_invoice_return, $operation1, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction(3, 420100001, 0, $request->total, $constant_number, $purchase_invoice_return, $operation1, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_cash->id, $tree4_cash->tree4_code, $request->cash_amount, 0, $constant_number, $purchase_invoice_return, $operation2, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, 0, $request->cash_amount, $constant_number, $purchase_invoice_return, $operation2, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_bank->id, $tree4_bank->tree4_code, $request->bank_amount, 0, $constant_number, $purchase_invoice_return, $operation3, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, 0, $request->bank_amount, $constant_number, $purchase_invoice_return, $operation3, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                }
            } elseif ($request->payment_status == 1 && $request->supplier_id != null) {
                $tree4_supplier = Tree4::find($request->supplier_id);

                $operation = $this->operation($tree4_supplier->id, 3, $tree4_supplier->tree4_code, 420100001, $request->total, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات عن طريق الاجل ');

                $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, $request->total, 0, $constant_number, $purchase_invoice_return, $operation, 'فاتورة مبيعات عن طريق الاجل ');
                $this->restriction(3, 420100001, 0, $request->total, $constant_number, $purchase_invoice_return, $operation, 'فاتورة مبيعات عن طريق الاجل ');
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

    // public function edit(PurchaseInvoiceReturn $purchase_invoice_return): View
    // {
    //     $suppliers = Tree4::whereTree3_code(2301)->whereStatus(1)->get();
    //     return view('purchase_return.edit', compact('purchase_invoice_return', 'suppliers'));
    // }

    // public function update(UpdatePurchaseReturnRequest $request, PurchaseInvoiceReturn $purchase_invoice_return): RedirectResponse
    // {
    //     try {
    //         DB::beginTransaction();
    //         $purchase_invoice_return->update(
    //             attributes: [
    //                 'supplier_id' => $request->get('supplier_id'),
    //                 'purchase_invoice_id' => $request->get('purchase_invoice_id'),
    //                 'purchase_invoice_number' => $request->get('purchase_invoice_number'),
    //                 'invoice_date' => $request->get('invoice_date'),
    //                 'sub_total_amount' => $request->get('sub_total'),
    //                 'discount_amount' => $request->get('discount'),
    //                 'tax_amount' => $request->get('tax'),
    //                 'total_amount' => $request->get('total'),
    //                 'payment_status' => $request->get('payment_status'),
    //                 'status' => 1,
    //                 'payment_method' => $request->get('payment_method'),
    //                 'cash_id' => $request->get('cash_id'),
    //                 'bank_id' => $request->get('bank_id'),
    //                 'cash_amount' => $request->get('cash_amount'),
    //                 'bank_amount' => $request->get('bank_amount'),
    //             ],
    //         );
    //         $purchase_invoice_return->details()->delete();
    //         $purchase_invoice_return->operations()->delete();
    //         $purchase_invoice_return->restrictions()->delete();

    //         foreach ($request->product_id as $index => $product_id) {
    //             $purchase_invoice_return->details()->create(
    //                 attributes: [
    //                     'product_id' => $product_id,
    //                     'qty' => $request->qty[$index],
    //                     'price' => $request->price[$index],
    //                     'unit_id' => $request->unit_id[$index],
    //                     'row_sub_total' => $request->row_sub_total[$index],
    //                 ],
    //             );

    //             $product = Product::whereId($product_id)->first();
    //             $product_unit = $product->units()->where('unit_id', $request->unit_id[$index])->first();
    //             $qty = $request->qty[$index] * $product_unit->pivot->conversion_factor;
    //             if ($qty < 0) {
    //                 return back()->withErrors(['error' => 'الكمية المدفوعة أكبر من الكمية الموجودة في المخزن.']);
    //             }
    //             $product->update([
    //                 'qty' => $product->qty - $qty,
    //             ]);
    //         }

    //         $last_constant_number = Operation::max('constant_number');
    //         $constant_number = $last_constant_number ? $last_constant_number + 1 : 1;

    //         if ($request->payment_status == 0 && $request->supplier_id == null) {
    //             $id = $request->cash_id != null ? $request->cash_id : ($request->bank_id != null ? $request->bank_id : '');
    //             if ($request->payment_method == 1 || $request->payment_method == 2) {
    //                 $tree4 = Tree4::find($id);

    //                 $operation = $this->operation($tree4->id, 3, $tree4->tree4_code, 420100001, $request->total, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات تم الدفع عن طريق الكاش');

    //                 $this->restriction(3, 420100001, 0, $request->total, $constant_number, $purchase_invoice_return, $operation, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
    //                 $this->restriction($tree4->id, $tree4->tree4_code, $request->total, 0, $constant_number, $purchase_invoice_return, $operation, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
    //             } elseif ($request->payment_method == 3) {
    //                 $tree4_cash = Tree4::find($request->cash_id);
    //                 $tree4_bank = Tree4::find($request->bank_id);

    //                 $operation_cash = $this->operation($tree4_cash->id, 3, $tree4_cash->tree4_code, 420100001, $request->cash_amount, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
    //                 $operation_bank = $this->operation($tree4_bank->id, 3, $tree4_bank->tree4_code, 420100001, $request->bank_amount, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات تم الدفع عن طريق الكاش');

    //                 $this->restriction(3, 420100001, 0, $request->total, $constant_number, $purchase_invoice_return, $operation_bank, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
    //                 $this->restriction($tree4_cash->id, $tree4_cash->tree4_code, $request->cash_amount, 0, $constant_number, $purchase_invoice_return, $operation_cash, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
    //                 $this->restriction($tree4_bank->id, $tree4_bank->tree4_code, $request->bank_amount, 0, $constant_number, $purchase_invoice_return, $operation_bank, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
    //             }
    //         } elseif ($request->payment_status == 0 && $request->supplier_id != null) {
    //             $tree4_supplier = Tree4::find($request->supplier_id);
    //             $id = $request->cash_id != null ? $request->cash_id : ($request->bank_id != null ? $request->bank_id : '');
    //             if ($request->payment_method == 1 || $request->payment_method == 2) {
    //                 $tree4 = Tree4::find($id);

    //                 $operation1 = $this->operation($tree4_supplier->id, 3, $tree4_supplier->tree4_code, 420100001, $request->total, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
    //                 $operation2 = $this->operation($tree4->id, $tree4_supplier->id, $tree4->tree4_code, $tree4_supplier->tree4_code, $request->total, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');

    //                 $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, $request->total, 0, $constant_number, $purchase_invoice_return, $operation1, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
    //                 $this->restriction(3, 420100001, 0, $request->total, $constant_number, $purchase_invoice_return, $operation1, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
    //                 $this->restriction($tree4->id, $tree4->tree4_code, $request->total, 0, $constant_number, $purchase_invoice_return, $operation2, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
    //                 $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, 0, $request->total, $constant_number, $purchase_invoice_return, $operation2, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
    //             } elseif ($request->payment_method == 3) {
    //                 $tree4_cash = Tree4::find($request->cash_id);
    //                 $tree4_bank = Tree4::find($request->bank_id);

    //                 $operation1 = $this->operation($tree4_supplier->id, 3, $tree4_supplier->tree4_code, 420100001, $request->total, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
    //                 $operation2 = $this->operation($tree4_cash->id, $tree4_supplier->id, $tree4_cash->tree4_code, $tree4_supplier->tree4_code, $request->cash_amount, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
    //                 $operation3 = $this->operation($tree4_bank->id, $tree4_supplier->id, $tree4_bank->tree4_code, $tree4_supplier->tree4_code, $request->bank_amount, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');

    //                 $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, $request->total, 0, $constant_number, $purchase_invoice_return, $operation1, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
    //                 $this->restriction(3, 420100001, 0, $request->total, $constant_number, $purchase_invoice_return, $operation1, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
    //                 $this->restriction($tree4_cash->id, $tree4_cash->tree4_code, $request->cash_amount, 0, $constant_number, $purchase_invoice_return, $operation2, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
    //                 $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, 0, $request->cash_amount, $constant_number, $purchase_invoice_return, $operation2, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
    //                 $this->restriction($tree4_bank->id, $tree4_bank->tree4_code, $request->bank_amount, 0, $constant_number, $purchase_invoice_return, $operation3, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
    //                 $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, 0, $request->bank_amount, $constant_number, $purchase_invoice_return, $operation3, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
    //             }
    //         } elseif ($request->payment_status == 1 && $request->supplier_id != null) {
    //             $tree4_supplier = Tree4::find($request->supplier_id);

    //             $operation = $this->operation($tree4_supplier->id, 3, $tree4_supplier->tree4_code, 420100001, $request->total, $constant_number, $purchase_invoice_return, 'فاتورة مبيعات عن طريق الاجل ');

    //             $this->restriction($tree4_supplier->id, $tree4_supplier->tree4_code, $request->total, 0, $constant_number, $purchase_invoice_return, $operation, 'فاتورة مبيعات عن طريق الاجل ');
    //             $this->restriction(3, 420100001, 0, $request->total, $constant_number, $purchase_invoice_return, $operation, 'فاتورة مبيعات عن طريق الاجل ');
    //         }

    //         DB::commit();

    //         return redirect()->route('purchase_invoice_return.index')->with('success_updated', 'تم تحديث البيانات بنجاج');
    //     } catch (\Exception $exception) {
    //         DB::rollback();
    //         return back()
    //             ->withErrors(['error' => $exception->getMessage()])
    //             ->withInput();
    //     }
    // }

    // public function destroy(PurchaseInvoiceReturn $purchase_invoice_return): RedirectResponse
    // {
    //     $purchase_invoice_return->delete();
    //     return back()->with('success_deleted', 'تم الحذف بنجاح ');
    // }

    // public function toggleStatus(PurchaseInvoiceReturn $purchase_invoice_return): JsonResponse
    // {
    //     $purchase_invoice_return->update(['status' => !$purchase_invoice_return->status]);
    //     return response()->json(['status' => $purchase_invoice_return->status]);
    // }

    public function updateQtyProduct(Request $request): JsonResponse
    {
        $request->validate([
            'deleted_products' => 'required|array',
            'deleted_products.*.id' => 'exists:products,id',
            'deleted_products.*.qty' => 'required|integer|min:1',
        ]);

        foreach ($request->deleted_products as $product) {
            $productqty = DB::table('products')->where('id', $product['id']);
            $productqty->decrement('qty', $product['qty']);
        }

        return response()->json(['success' => true, 'message' => 'تم تحديث الكمية بنجاح']);
    }

    private function operation($madin_id, $dain_id, $madin, $dain, $total, $constant_number, $purchase_invoice_return, $node): Operation
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
                'purchase_invoice_return_id' => $purchase_invoice_return->id,
                'type' => 3,
                'user_id' => Auth::id(),
            ],
        );
    }

    private function restriction($tree_id, $tree4_code, $madin, $dain, $constant_number, $purchase_invoice_return, $operation, $node): Restriction
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
                'type' => 3,
                'purchase_invoice_return_id' => $purchase_invoice_return->id,
                'user_id' => Auth::id(),
                'operation_id' => $operation->id,
            ],
        );
    }
}
