<?php

namespace App\Http\Controllers;

use App\Models\Tree4;
use App\Models\Customer;
use App\Models\Operation;
use Illuminate\View\View;
use App\Models\Restriction;
use App\Models\SaleInvoice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\SaleInvoice\StoreSaleInvoiceRequest;
use App\Http\Requests\SaleInvoice\UpdateSaleInvoiceRequest;
use App\Models\Product;
use App\Models\SaleInvoiceReturn;
use App\Models\Stock;

class SaleInvoiceContoller extends Controller
{
    public function index(): View
    {
        $sale_invoices = SaleInvoice::get();
        return view('sale.index', compact('sale_invoices'));
    }

    public function create(): View
    {
        $check = SaleInvoice::orderBy('id', 'desc')->first();
        if ($check) {
            $last_invoice_number = $check->invoice_number;
            $invoice_number = $last_invoice_number + 1;
        } else {
            $invoice_number = random_int(999, 9999);
        }
        $customers = Tree4::whereTree3_code(1205)->whereStatus(1)->get();
        $stocks = Stock::statusActive()->get();
        return view('sale.create', compact('customers', 'invoice_number', 'stocks'));
    }

    public function store(StoreSaleInvoiceRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $sale_invoice = SaleInvoice::create(
                attributes: [
                    'customer_id' => $request->get('customer_id'),
                    'invoice_number' => $request->get('invoice_number'),
                    'stock_id' => $request->get('stock_id'),
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

            foreach ($request->product_id as $index => $product_id) {
                $sale_invoice->details()->create(
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
                if ($qty < 0) {
                    return back()->withErrors(['error' => 'الكمية المدفوعة أكبر من الكمية الموجودة في المخزن.']);
                }
                $productStock = $product->stocks()->where('stock_id', $request->stock_id)->first();

                if ($productStock) {
                    $newQuantity = $productStock->pivot->quantity - $qty;
                    $product->stocks()->syncWithoutDetaching([
                        $request->stock_id => ['quantity' => $newQuantity],
                    ]);
                }
            }

            $last_constant_number = Operation::max('constant_number');
            $constant_number = $last_constant_number ? $last_constant_number + 1 : 1;

            if ($request->payment_status == 0 && $request->customer_id == null) {
                $id = $request->cash_id != null ? $request->cash_id : ($request->bank_id != null ? $request->bank_id : '');
                if ($request->payment_method == 1 || $request->payment_method == 2) {
                    $tree4 = Tree4::find($id);

                    $operation = $this->operation($tree4->id, 3, $tree4->tree4_code, 420100001, $request->total, $constant_number, $sale_invoice, 'فاتورة مبيعات تم الدفع عن طريق الكاش');

                    $this->restriction(3, 420100001, 0, $request->total, $constant_number, $sale_invoice, $operation, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                    $this->restriction($tree4->id, $tree4->tree4_code, $request->total, 0, $constant_number, $sale_invoice, $operation, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                } elseif ($request->payment_method == 3) {
                    $tree4_cash = Tree4::find($request->cash_id);
                    $tree4_bank = Tree4::find($request->bank_id);

                    $operation_cash = $this->operation($tree4_cash->id, 3, $tree4_cash->tree4_code, 420100001, $request->cash_amount, $constant_number, $sale_invoice, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                    $operation_bank = $this->operation($tree4_bank->id, 3, $tree4_bank->tree4_code, 420100001, $request->bank_amount, $constant_number, $sale_invoice, 'فاتورة مبيعات تم الدفع عن طريق الكاش');

                    $this->restriction(3, 420100001, 0, $request->total, $constant_number, $sale_invoice, $operation_bank, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                    $this->restriction($tree4_cash->id, $tree4_cash->tree4_code, $request->cash_amount, 0, $constant_number, $sale_invoice, $operation_cash, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                    $this->restriction($tree4_bank->id, $tree4_bank->tree4_code, $request->bank_amount, 0, $constant_number, $sale_invoice, $operation_bank, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                }
            } elseif ($request->payment_status == 0 && $request->customer_id != null) {
                $tree4_customer = Tree4::find($request->customer_id);
                $id = $request->cash_id != null ? $request->cash_id : ($request->bank_id != null ? $request->bank_id : '');
                if ($request->payment_method == 1 || $request->payment_method == 2) {
                    $tree4 = Tree4::find($id);

                    $operation1 = $this->operation($tree4_customer->id, 3, $tree4_customer->tree4_code, 420100001, $request->total, $constant_number, $sale_invoice, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $operation2 = $this->operation($tree4->id, $tree4_customer->id, $tree4->tree4_code, $tree4_customer->tree4_code, $request->total, $constant_number, $sale_invoice, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');

                    $this->restriction($tree4_customer->id, $tree4_customer->tree4_code, $request->total, 0, $constant_number, $sale_invoice, $operation1, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction(3, 420100001, 0, $request->total, $constant_number, $sale_invoice, $operation1, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4->id, $tree4->tree4_code, $request->total, 0, $constant_number, $sale_invoice, $operation2, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_customer->id, $tree4_customer->tree4_code, 0, $request->total, $constant_number, $sale_invoice, $operation2, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                } elseif ($request->payment_method == 3) {
                    $tree4_cash = Tree4::find($request->cash_id);
                    $tree4_bank = Tree4::find($request->bank_id);

                    $operation1 = $this->operation($tree4_customer->id, 3, $tree4_customer->tree4_code, 420100001, $request->total, $constant_number, $sale_invoice, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $operation2 = $this->operation($tree4_cash->id, $tree4_customer->id, $tree4_cash->tree4_code, $tree4_customer->tree4_code, $request->cash_amount, $constant_number, $sale_invoice, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $operation3 = $this->operation($tree4_bank->id, $tree4_customer->id, $tree4_bank->tree4_code, $tree4_customer->tree4_code, $request->bank_amount, $constant_number, $sale_invoice, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');

                    $this->restriction($tree4_customer->id, $tree4_customer->tree4_code, $request->total, 0, $constant_number, $sale_invoice, $operation1, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction(3, 420100001, 0, $request->total, $constant_number, $sale_invoice, $operation1, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_cash->id, $tree4_cash->tree4_code, $request->cash_amount, 0, $constant_number, $sale_invoice, $operation2, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_customer->id, $tree4_customer->tree4_code, 0, $request->cash_amount, $constant_number, $sale_invoice, $operation2, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_bank->id, $tree4_bank->tree4_code, $request->bank_amount, 0, $constant_number, $sale_invoice, $operation3, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_customer->id, $tree4_customer->tree4_code, 0, $request->bank_amount, $constant_number, $sale_invoice, $operation3, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                }
            } elseif ($request->payment_status == 1 && $request->customer_id != null) {
                $tree4_customer = Tree4::find($request->customer_id);

                $operation = $this->operation($tree4_customer->id, 3, $tree4_customer->tree4_code, 420100001, $request->total, $constant_number, $sale_invoice, 'فاتورة مبيعات عن طريق الاجل ');

                $this->restriction($tree4_customer->id, $tree4_customer->tree4_code, $request->total, 0, $constant_number, $sale_invoice, $operation, 'فاتورة مبيعات عن طريق الاجل ');
                $this->restriction(3, 420100001, 0, $request->total, $constant_number, $sale_invoice, $operation, 'فاتورة مبيعات عن طريق الاجل ');
            }

            DB::commit();

            return back()->with('success_created', 'Product created successfully.');
        } catch (\Exception $exception) {
            DB::rollback();
            return redirect()
                ->route('sale_invoice.create')
                ->withErrors(['error' => $exception->getMessage()])
                ->withInput();
        }
    }

    public function edit(SaleInvoice $sale_invoice): View
    {
        $customers = Tree4::whereTree3_code(1205)->whereStatus(1)->get();
        $stocks = Stock::statusActive()->get();
        return view('sale.edit', compact('sale_invoice', 'customers', 'stocks'));
    }

    public function update(UpdateSaleInvoiceRequest $request, SaleInvoice $sale_invoice): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $sale = $sale_invoice->details;

            $sale_invoice->delete();

            $sale_invoice = SaleInvoice::create(
                attributes: [
                    'customer_id' => $request->get('customer_id'),
                    'invoice_number' => $request->get('invoice_number'),
                    'stock_id' => $request->get('stock_id'),
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

            foreach ($request->product_id as $index => $product_id) {
                foreach ($sale as $item) {
                    if ($item->product_id == $product_id) {
                        $product = Product::with('stocks', 'units')->find($item->product_id);
                        $product_unit = $product->units()->where('unit_id', $item->unit_id)->first();
                        $producStock = $product->stocks()->where('stock_id', $sale_invoice->stock_id)->first();
                        $qty = $item->qty * $product_unit->pivot->conversion_factor;
                        $newQuantity = $producStock->pivot->quantity + $qty;
                        $product->stocks()->syncWithoutDetaching([
                            $sale_invoice->stock_id => ['quantity' => $newQuantity],
                        ]);
                    }
                }

                $sale_invoice->details()->create(
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
                if ($qty < 0) {
                    return back()->withErrors(['error' => 'الكمية المدفوعة أكبر من الكمية الموجودة في المخزن.']);
                }
                $productStock = $product->stocks()->where('stock_id', $request->stock_id)->first();

                if ($productStock) {
                    $newQuantity = $productStock->pivot->quantity - $qty;
                    $product->stocks()->syncWithoutDetaching([
                        $request->stock_id => ['quantity' => $newQuantity],
                    ]);
                }
            }

            $last_constant_number = Operation::max('constant_number');
            $constant_number = $last_constant_number ? $last_constant_number + 1 : 1;

            if ($request->payment_status == 0 && $request->customer_id == null) {
                $id = $request->cash_id != null ? $request->cash_id : ($request->bank_id != null ? $request->bank_id : '');
                if ($request->payment_method == 1 || $request->payment_method == 2) {
                    $tree4 = Tree4::find($id);

                    $operation = $this->operation($tree4->id, 3, $tree4->tree4_code, 420100001, $request->total, $constant_number, $sale_invoice, 'فاتورة مبيعات تم الدفع عن طريق الكاش');

                    $this->restriction(3, 420100001, 0, $request->total, $constant_number, $sale_invoice, $operation, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                    $this->restriction($tree4->id, $tree4->tree4_code, $request->total, 0, $constant_number, $sale_invoice, $operation, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                } elseif ($request->payment_method == 3) {
                    $tree4_cash = Tree4::find($request->cash_id);
                    $tree4_bank = Tree4::find($request->bank_id);

                    $operation_cash = $this->operation($tree4_cash->id, 3, $tree4_cash->tree4_code, 420100001, $request->cash_amount, $constant_number, $sale_invoice, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                    $operation_bank = $this->operation($tree4_bank->id, 3, $tree4_bank->tree4_code, 420100001, $request->bank_amount, $constant_number, $sale_invoice, 'فاتورة مبيعات تم الدفع عن طريق الكاش');

                    $this->restriction(3, 420100001, 0, $request->total, $constant_number, $sale_invoice, $operation_bank, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                    $this->restriction($tree4_cash->id, $tree4_cash->tree4_code, $request->cash_amount, 0, $constant_number, $sale_invoice, $operation_cash, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                    $this->restriction($tree4_bank->id, $tree4_bank->tree4_code, $request->bank_amount, 0, $constant_number, $sale_invoice, $operation_bank, 'فاتورة مبيعات تم الدفع عن طريق الكاش');
                }
            } elseif ($request->payment_status == 0 && $request->customer_id != null) {
                $tree4_customer = Tree4::find($request->customer_id);
                $id = $request->cash_id != null ? $request->cash_id : ($request->bank_id != null ? $request->bank_id : '');
                if ($request->payment_method == 1 || $request->payment_method == 2) {
                    $tree4 = Tree4::find($id);

                    $operation1 = $this->operation($tree4_customer->id, 3, $tree4_customer->tree4_code, 420100001, $request->total, $constant_number, $sale_invoice, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $operation2 = $this->operation($tree4->id, $tree4_customer->id, $tree4->tree4_code, $tree4_customer->tree4_code, $request->total, $constant_number, $sale_invoice, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');

                    $this->restriction($tree4_customer->id, $tree4_customer->tree4_code, $request->total, 0, $constant_number, $sale_invoice, $operation1, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction(3, 420100001, 0, $request->total, $constant_number, $sale_invoice, $operation1, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4->id, $tree4->tree4_code, $request->total, 0, $constant_number, $sale_invoice, $operation2, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_customer->id, $tree4_customer->tree4_code, 0, $request->total, $constant_number, $sale_invoice, $operation2, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                } elseif ($request->payment_method == 3) {
                    $tree4_cash = Tree4::find($request->cash_id);
                    $tree4_bank = Tree4::find($request->bank_id);

                    $operation1 = $this->operation($tree4_customer->id, 3, $tree4_customer->tree4_code, 420100001, $request->total, $constant_number, $sale_invoice, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $operation2 = $this->operation($tree4_cash->id, $tree4_customer->id, $tree4_cash->tree4_code, $tree4_customer->tree4_code, $request->cash_amount, $constant_number, $sale_invoice, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $operation3 = $this->operation($tree4_bank->id, $tree4_customer->id, $tree4_bank->tree4_code, $tree4_customer->tree4_code, $request->bank_amount, $constant_number, $sale_invoice, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');

                    $this->restriction($tree4_customer->id, $tree4_customer->tree4_code, $request->total, 0, $constant_number, $sale_invoice, $operation1, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction(3, 420100001, 0, $request->total, $constant_number, $sale_invoice, $operation1, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_cash->id, $tree4_cash->tree4_code, $request->cash_amount, 0, $constant_number, $sale_invoice, $operation2, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_customer->id, $tree4_customer->tree4_code, 0, $request->cash_amount, $constant_number, $sale_invoice, $operation2, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_bank->id, $tree4_bank->tree4_code, $request->bank_amount, 0, $constant_number, $sale_invoice, $operation3, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                    $this->restriction($tree4_customer->id, $tree4_customer->tree4_code, 0, $request->bank_amount, $constant_number, $sale_invoice, $operation3, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل');
                }
            } elseif ($request->payment_status == 1 && $request->customer_id != null) {
                $tree4_customer = Tree4::find($request->customer_id);

                $operation = $this->operation($tree4_customer->id, 3, $tree4_customer->tree4_code, 420100001, $request->total, $constant_number, $sale_invoice, 'فاتورة مبيعات عن طريق الاجل ');

                $this->restriction($tree4_customer->id, $tree4_customer->tree4_code, $request->total, 0, $constant_number, $sale_invoice, $operation, 'فاتورة مبيعات عن طريق الاجل ');
                $this->restriction(3, 420100001, 0, $request->total, $constant_number, $sale_invoice, $operation, 'فاتورة مبيعات عن طريق الاجل ');
            }

            DB::commit();

            return redirect()->route('sale_invoice.index')->with('success_updated', 'تم تحديث البيانات بنجاج');
        } catch (\Exception $exception) {
            DB::rollback();
            return back()
                ->withErrors(['error' => $exception->getMessage()])
                ->withInput();
        }
    }

    public function destroy(SaleInvoice $sale_invoice, Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            foreach ($sale_invoice->details as $item) {
                $product = Product::with('stocks', 'units')->find($item->product_id);
                $producStock = $product->stocks()->where('stock_id', $sale_invoice->stock_id)->first();
                $conversion_factor = $product->units()->firstWhere('unit_id', $item->unit_id)->pivot->conversion_factor;
                $qty = $item->qty * $conversion_factor;
                $newQuantity = $producStock->pivot->quantity + $qty;
                $product->stocks()->syncWithoutDetaching([
                    $sale_invoice->stock_id => ['quantity' => $newQuantity],
                ]);
            }
            $sale_invoice->delete();
            DB::commit();
            return back()->with('success_deleted', 'تم الحذف بنجاح ');
        } catch (\Exception $exception) {
            DB::rollback();
            return back()->with('error', 'حدث خطا ما يرحى المحاولة مرةاخرى');
        }
    }

    public function toggleStatus(SaleInvoice $sale_invoice): JsonResponse
    {
        $sale_invoice->update(['status' => !$sale_invoice->status]);
        return response()->json(['status' => $sale_invoice->status]);
    }

    public function select2(Request $request): JsonResponse
    {
        $search = $request->get(key: 'q');
        $select_method = $request->get('selectMethod');
        $query = Tree4::query()
            ->where('status', 1)
            ->when($search, function ($query) use ($search) {
                $query->where('tree4_name', 'like', '%' . $search . '%');
            });

        if ($select_method == 1) {
            $tree4 = $query->whereTree3_code(1202)->get();
        } elseif ($select_method == 2) {
            $tree4 = $query->whereTree3_code(1203)->get();
        }

        $formattedResults = $tree4->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->tree4_name, // الحقل الذي سيظهر في Select2
            ];
        });
        return response()->json($formattedResults);
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
            $newQuantity = $productStock->pivot->quantity + $qty;
            $productqty->stocks()->syncWithoutDetaching([
                $request->stock_id => ['quantity' => $newQuantity],
            ]);
        }

        return response()->json(['success' => true, 'message' => 'تم تحديث الكمية بنجاح']);
    }

    private function operation($madin_id, $dain_id, $madin, $dain, $total, $constant_number, $sale_invoice, $node): Operation
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
                'sale_invoice_id' => $sale_invoice->id,
                'type' => 1,
                'user_id' => Auth::id(),
            ],
        );
    }

    private function restriction($tree_id, $tree4_code, $madin, $dain, $constant_number, $sale_invoice, $operation, $node): Restriction
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
                'type' => 1,
                'sale_invoice_id' => $sale_invoice->id,
                'user_id' => Auth::id(),
                'operation_id' => $operation->id,
            ],
        );
    }
}
