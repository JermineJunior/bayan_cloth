<?php

namespace App\Http\Controllers;

use App\Models\Tree4;
use App\Models\Product;
use App\Models\Operation;
use Illuminate\View\View;
use App\Models\Restriction;
use App\Models\SaleInvoice;
use Illuminate\Http\Request;
use App\Models\SaleInvoiceReturn;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\SaleInvoiceReturn\StoreSaleInvoiceReturnRequest;
use App\Models\Stock;

class SaleInvoiceReturnController extends Controller
{
    public function create(Request $request): View
    {
        $check = SaleInvoiceReturn::orderBy('id', 'desc')->first();
        if ($check) {
            $last_invoice_number = $check->invoice_number;
            $invoice_number = $last_invoice_number + 1;
        } else {
            $invoice_number = random_int(999, 9999);
        }
        $customers = Tree4::whereTree3_code(1205)->whereStatus(1)->get();
        $id = $request->sale_invoice;
        $sale_invoice = SaleInvoice::find($id) ?? null;
        $stocks = Stock::statusActive()->get();
        return view('sale_return.create', compact('customers', 'sale_invoice', 'invoice_number', 'stocks'));
    }

    public function store(StoreSaleInvoiceReturnRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $check = SaleInvoiceReturn::orderBy('id', 'desc')->first();
            if ($check) {
                $last_invoice_number = $check->invoice_number;
                $invoice_number = $last_invoice_number + 1;
            } else {
                $invoice_number = random_int(999, 9999);
            }

            $sale_invoice_return = SaleInvoiceReturn::create(
                attributes: [
                    'customer_id' => $request->get('customer_id'),
                    'invoice_number' => $invoice_number,
                    'sale_invoice_id' => $request->get('sale_invoice_id'),
                    'stock_id' => $request->get('stock_id'),
                    'sale_invoice_number' => $request->get('sale_invoice_number'),
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

            $sale_invoice = SaleInvoice::with('details')->find($request->get('sale_invoice_id'));

            // this code if user enter sale invoice return again that update quantity of stock before save the new quantity
            $check_invoice_return = SaleInvoiceReturn::whereSale_invoice_id($sale_invoice->id)->first();

            foreach ($request->product_id as $index => $product_id) {
                if ($check_invoice_return->details->count() > 0) {
                    foreach ($check_invoice_return->details as $item) {
                        if ($item->product_id == $product_id) {
                            $product = Product::with('stocks', 'units')->find($item->product_id);
                            $product_unit = $product->units()->where('unit_id', $item->unit_id)->first();
                            $producStock = $product->stocks()->where('stock_id', $check_invoice_return->stock_id)->first();
                            $qty = $item->qty * $product_unit->pivot->conversion_factor;
                            $newQuantity = $producStock->pivot->quantity - $qty;
                            $product->stocks()->syncWithoutDetaching([
                                $check_invoice_return->stock_id => ['quantity' => $newQuantity],
                            ]);
                        }
                    }
                }

                $sale_invoice_return->details()->create(
                    attributes: [
                        'product_id' => $product_id,
                        'qty' => $request->qty[$index],
                        'price' => $request->price[$index],
                        'unit_id' => $request->unit_id[$index],
                        'row_sub_total' => $request->row_sub_total[$index],
                    ],
                );

                $product = Product::with('stocks', 'units')->whereId($product_id)->first();
                $product_unit = $product->units()->where('unit_id', $request->unit_id[$index])->first();
                $qty = $request->qty[$index] * $product_unit->pivot->conversion_factor;
                $productStock = $product->stocks()->where('stock_id', $request->stock_id)->first();
                $newQuantity = $productStock->pivot->quantity + $qty;
                $product->stocks()->syncWithoutDetaching([
                    $request->stock_id => ['quantity' => $newQuantity],
                ]);
            }

            $last_constant_number = Operation::max('constant_number');
            $constant_number = $last_constant_number ? $last_constant_number + 1 : 1;

            if ($request->payment_status == 0 && $request->customer_id == null) {
                $id = $request->cash_id != null ? $request->cash_id : ($request->bank_id != null ? $request->bank_id : '');
                if ($request->payment_method == 1 || $request->payment_method == 2) {
                    $tree4 = Tree4::find($id);

                    $operation = $this->operation($tree4->id, 3, $tree4->tree4_code, 420100001, $request->total, $constant_number, $sale_invoice_return, 'فاتورة مبيعات تم الدفع عن طريق الكاش');

                    $this->restriction(3, 420100001, 0, $request->total, $constant_number, $sale_invoice_return, $operation, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                    $this->restriction($tree4->id, $tree4->tree4_code, $request->total, 0, $constant_number, $sale_invoice_return, $operation, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                } elseif ($request->payment_method == 3) {
                    $tree4_cash = Tree4::find($request->cash_id);
                    $tree4_bank = Tree4::find($request->bank_id);

                    $operation_cash = $this->operation($tree4_cash->id, 3, $tree4_cash->tree4_code, 420100001, $request->cash_amount, $constant_number, $sale_invoice_return, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                    $operation_bank = $this->operation($tree4_bank->id, 3, $tree4_bank->tree4_code, 420100001, $request->bank_amount, $constant_number, $sale_invoice_return, 'فاتورة مبيعات تم الدفع عن طريق الكاش');

                    $this->restriction(3, 420100001, 0, $request->total, $constant_number, $sale_invoice_return, $operation_bank, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                    $this->restriction($tree4_cash->id, $tree4_cash->tree4_code, $request->cash_amount, 0, $constant_number, $sale_invoice_return, $operation_cash, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                    $this->restriction($tree4_bank->id, $tree4_bank->tree4_code, $request->bank_amount, 0, $constant_number, $sale_invoice_return, $operation_bank, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                }
            } elseif ($request->payment_status == 0 && $request->customer_id != null) {
                $tree4_customer = Tree4::find($request->customer_id);
                $id = $request->cash_id != null ? $request->cash_id : ($request->bank_id != null ? $request->bank_id : '');
                if ($request->payment_method == 1 || $request->payment_method == 2) {
                    $tree4 = Tree4::find($id);

                    $operation1 = $this->operation($tree4_customer->id, 3, $tree4_customer->tree4_code, 420100001, $request->total, $constant_number, $sale_invoice_return, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $operation2 = $this->operation($tree4->id, $tree4_customer->id, $tree4->tree4_code, $tree4_customer->tree4_code, $request->total, $constant_number, $sale_invoice_return, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');

                    $this->restriction($tree4_customer->id, $tree4_customer->tree4_code, $request->total, 0, $constant_number, $sale_invoice_return, $operation1, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction(3, 420100001, 0, $request->total, $constant_number, $sale_invoice_return, $operation1, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4->id, $tree4->tree4_code, $request->total, 0, $constant_number, $sale_invoice_return, $operation2, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_customer->id, $tree4_customer->tree4_code, 0, $request->total, $constant_number, $sale_invoice_return, $operation2, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                } elseif ($request->payment_method == 3) {
                    $tree4_cash = Tree4::find($request->cash_id);
                    $tree4_bank = Tree4::find($request->bank_id);

                    $operation1 = $this->operation($tree4_customer->id, 3, $tree4_customer->tree4_code, 420100001, $request->total, $constant_number, $sale_invoice_return, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $operation2 = $this->operation($tree4_cash->id, $tree4_customer->id, $tree4_cash->tree4_code, $tree4_customer->tree4_code, $request->cash_amount, $constant_number, $sale_invoice_return, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $operation3 = $this->operation($tree4_bank->id, $tree4_customer->id, $tree4_bank->tree4_code, $tree4_customer->tree4_code, $request->bank_amount, $constant_number, $sale_invoice_return, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');

                    $this->restriction($tree4_customer->id, $tree4_customer->tree4_code, $request->total, 0, $constant_number, $sale_invoice_return, $operation1, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction(3, 420100001, 0, $request->total, $constant_number, $sale_invoice_return, $operation1, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_cash->id, $tree4_cash->tree4_code, $request->cash_amount, 0, $constant_number, $sale_invoice_return, $operation2, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_customer->id, $tree4_customer->tree4_code, 0, $request->cash_amount, $constant_number, $sale_invoice_return, $operation2, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_bank->id, $tree4_bank->tree4_code, $request->bank_amount, 0, $constant_number, $sale_invoice_return, $operation3, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_customer->id, $tree4_customer->tree4_code, 0, $request->bank_amount, $constant_number, $sale_invoice_return, $operation3, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                }
            } elseif ($request->payment_status == 1 && $request->customer_id != null) {
                $tree4_customer = Tree4::find($request->customer_id);

                $operation = $this->operation($tree4_customer->id, 3, $tree4_customer->tree4_code, 420100001, $request->total, $constant_number, $sale_invoice_return, 'فاتورة مبيعات عن طريق الاجل ');

                $this->restriction($tree4_customer->id, $tree4_customer->tree4_code, $request->total, 0, $constant_number, $sale_invoice_return, $operation, 'فاتورة مبيعات عن طريق الاجل ');
                $this->restriction(3, 420100001, 0, $request->total, $constant_number, $sale_invoice_return, $operation, 'فاتورة مبيعات عن طريق الاجل ');
            }

            DB::commit();

            return redirect()->route('sale_invoice.index')->with('success_created', 'Product created successfully.');
        } catch (\Exception $exception) {
            DB::rollback();
            return back()
                ->withErrors(['error' => $exception->getMessage()])
                ->withInput();
        }
    }

    private function operation($madin_id, $dain_id, $madin, $dain, $total, $constant_number, $sale_invoice_return, $node): Operation
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
                'sale_invoice_return_id' => $sale_invoice_return->id,
                'type' => 4,
                'user_id' => Auth::id(),
            ],
        );
    }

    private function restriction($tree_id, $tree4_code, $madin, $dain, $constant_number, $sale_invoice_return, $operation, $node): Restriction
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
                'type' => 4,
                'sale_invoice_return_id' => $sale_invoice_return->id,
                'user_id' => Auth::id(),
                'operation_id' => $operation->id,
            ],
        );
    }
}
