<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Tree4;
use App\Models\Product;
use Illuminate\View\View;
use App\Models\Restriction;
use App\Models\SaleInvoice;
use App\Models\StockDetail;
use App\Models\PreliminaryInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\SaleInvoiceReturn\StoreSaleInvoiceReturnRequest;
use App\Http\Requests\PreliminaryInvoice\StorePreliminaryInvoiceRequest;

class PreliminaryInvoiceController extends Controller
{
    public function index(): View
    {
        $preliminaryInvoices = PreliminaryInvoice::where('branch_id', session('branch_id'))->orderByDesc('invoice_number')->get();
        return view('preliminaryInvoice.index', compact('preliminaryInvoices'));
    }

    public function create(): View
    {
        $invoice_number = PreliminaryInvoice::where('branch_id', session('branch_id'))->max('invoice_number') + 1;
        $customers = Tree4::whereTree3_code(1205)->orWhere('tree3_code','2301')->get();
        $representatives = Tree4::whereTree3_code(1206)->get();
        $products = Product::with('stocks', 'units')->take(7)->inRandomOrder()->get();
        $stocks = Stock::get();
        return view('preliminaryInvoice.create', compact('customers', 'stocks', 'invoice_number', 'representatives', 'products'));
    }

    public function store(StorePreliminaryInvoiceRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $preliminary_invoice = PreliminaryInvoice::create([
                'customer_id' => $request->customer_id,
                'representative_id' => $request->representative_id,
                'date' => $request->date,
                'invoice_number' => $request->invoice_number,
                'sub_total_amount' => $request->sub_total,
                'discount_amount' => $request->discount,
                'tax_amount' => $request->tax,
                'tax_status' => $request->get('tax_status') ? 1 : 0,
                'total_amount' => $request->total,
                'branch_id' => session('branch_id'),
            ]);

            foreach ($request->product_id as $index => $product_id) {
                $preliminary_invoice->details()->create(
                    attributes: [
                        'product_id' => $product_id,
                        'stock_id' => $request->stock_id[$index],
                        'qty' => $request->qty[$index],
                        'bounce' => $request->bounce[$index],
                        'expire_date' => $request->expire_date[$index],
                        'price' => $request->price[$index],
                        'unit_id' => $request->unit_id[$index],
                        'row_sub_total' => $request->row_sub_total[$index],
                    ],
                );
            }

            DB::commit();

            return redirect()->route('PreliminaryInvoice.print', $preliminary_invoice);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('PreliminaryInvoice.index')->withErrors($e->getMessage());
        }
    }
    public function print(PreliminaryInvoice $PreliminaryInvoice)
    {
        $PreliminaryInvoice = $PreliminaryInvoice->load(['details','details','details.product','details.unit']);
        $bank = Tree4::where('tree3_code',1203)->select('no_bank')->first();
        return view('preliminaryInvoice.print', compact('PreliminaryInvoice','bank'));
    }
    public function transform(PreliminaryInvoice $preliminaryInvoice): View
    {
        $customers = Tree4::whereTree3_code(1205)->orWhere('tree3_code','2301')->get();
        $products = Product::with('stocks', 'units')->take(7)->inRandomOrder()->get();
        $representatives = Tree4::where('tree3_code', '1206')->get();
        $safes = Tree4::where('tree3_code', '1202')->get();
        $banks = Tree4::where('tree3_code', '1203')->get();
        $stocks = Stock::get();
        $productIds = $preliminaryInvoice->details()->pluck('product_id')->toArray();
        return view('preliminaryInvoice.transform', compact('preliminaryInvoice', 'representatives', 'products', 'customers', 'safes', 'banks', 'stocks', 'productIds'));
    }

    public function transform_store(PreliminaryInvoice $preliminaryInvoice, StoreSaleInvoiceReturnRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $preliminaryInvoice->delete();

            $invoice_status = $request->deferend_amount != '0.00' ? 0 : 1;
            $transaction_no = (float) str_replace(',', '', $request->bank_amount_total ?? '') > 0 ? $request->transaction_no : null;
            $sale_invoice = SaleInvoice::create(
                attributes: [
                    'customer_id' => $request->get('customer_id'),
                    'representative_id' => $request->get('representative_id'),
                    'invoice_number' => SaleInvoice::where('branch_id',session('branch_id'))->max('invoice_number') + 1,
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
                    'branch_id' => session('branch_id'),
                    'user_id' => Auth::id(),
                ],
            );

            foreach ($request->product_id as $index => $product_id) {
                $sale_invoice->details()->create(
                    attributes: [
                        'product_id' => $product_id,
                        'stock_id' => $request->stock_id[$index],
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
                $productStock = $product->stocks()->where('stock_id', $request->stock_id[$index])->first();
                if ($qty > $productStock->pivot->quantity && !auth()->user()->can('بيع منتجات غير متوفرة')) {
                    DB::rollBack();
                    return redirect()
                        ->route('sale_invoice.create')
                        ->withErrors(['error' => 'الكمية باحد المنتجات أكبر من الكمية الموجودة في المخزن.']);
                }

                StockDetail::create([
                    'product_id' => $product_id,
                    'unit_id' => $request->unit_id[$index],
                    'stock_id' => $request->stock_id[$index],
                    'sader' => $qty,
                    'user_id' => Auth::id(),
                    'type' => 1,
                    'branch_id' => session('branch_id'),
                    'sale_invoice_id' => $sale_invoice->id,
                ]);

                if ($productStock) {
                    $newQuantity = $productStock->pivot->quantity - $qty;
                    $product->stocks()->syncWithoutDetaching([
                        $request->stock_id[$index] => ['quantity' => $newQuantity],
                    ]);
                }
            }

            $last_constant_number = Restriction::where('branch_id', session('branch_id'))->max('constant_number');

            $constant_number = $last_constant_number ? $last_constant_number + 1 : 1;

            if (empty($request->customer_id)) {
                if (!empty($request->cash_amount_total)) {
                    $cash = Tree4::find($request->cash_name);

                    $this->restriction($cash->id, $cash->tree4_code, $request->cash_amount_total, 0, $constant_number, $sale_invoice, 'فاتورة مبيعات نقدي', $sale_invoice->date);

                    $this->restriction(3, 420100001, 0, $request->cash_amount_total, $constant_number, $sale_invoice, 'فاتورة مبيعات نقدي', $sale_invoice->date);
                }

                if (!empty($request->bank_amount_total)) {
                    $bank = Tree4::find($request->bank_name);

                    $this->restriction($bank->id, $bank->tree4_code, $request->bank_amount_total, 0, $constant_number, $sale_invoice, 'فاتورة مبيعات بنك', $sale_invoice->date, $transaction_no);

                    $this->restriction(3, 420100001, 0, $request->bank_amount_total, $constant_number, $sale_invoice, 'فاتورة مبيعات بنك', $sale_invoice->date, $transaction_no);
                }
            } else {
                $customer = Tree4::find($request->customer_id);

                $this->restriction($customer->id, $customer->tree4_code, $request->total, 0, $constant_number, $sale_invoice, 'فاتورة مبيعات', $sale_invoice->date);

                $this->restriction(3, 420100001, 0, $request->total, $constant_number, $sale_invoice, 'فاتورة مبيعات', $sale_invoice->date);

                if (!empty($request->cash_amount_total)) {
                    $cash = Tree4::find($request->cash_name);

                    $this->restriction($cash->id, $cash->tree4_code, $request->cash_amount_total, 0, $constant_number, $sale_invoice, 'سداد كاش', $sale_invoice->date);

                    $this->restriction($customer->id, $customer->tree4_code, 0, $request->cash_amount_total, $constant_number, $sale_invoice, 'سداد كاش', $sale_invoice->date);
                }

                if (!empty($request->bank_amount_total)) {
                    $bank = Tree4::find($request->bank_name);

                    $this->restriction($bank->id, $bank->tree4_code, $request->bank_amount_total, 0, $constant_number, $sale_invoice, 'سداد بنك', $sale_invoice->date, $transaction_no);

                    $this->restriction($customer->id, $customer->tree4_code, 0, $request->bank_amount_total, $constant_number, $sale_invoice, 'سداد بنك', $sale_invoice->date, $transaction_no);
                }
            }

            DB::commit();

            return redirect()->route('sale_invoice.print',$sale_invoice);
        } catch (\Exception $exception) {
            DB::rollback();
            return redirect()
                ->route('sale_invoice.index')
                ->withErrors(['error' => $exception->getMessage()])
                ->withInput();
        }
    }

    public function destroy(PreliminaryInvoice $preliminaryInvoice): RedirectResponse
    {
        $preliminaryInvoice->delete();

        return back()->with('success_deleted', 'Product deleted successfully.');
    }

    private function restriction($tree_id, $tree4_code, $madin, $dain, $constant_number, $sale_invoice, $node, $date, $transactionNo = null): Restriction
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
                'type' => 1,
                'sale_invoice_id' => $sale_invoice->id,
                'user_id' => Auth::id(),
                'branch_id' => session('branch_id'),
            ],
        );
    }
}
