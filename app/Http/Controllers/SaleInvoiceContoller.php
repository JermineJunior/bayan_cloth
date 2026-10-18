<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Stock;
use App\Models\Tree4;
use App\Models\Product;
use Illuminate\View\View;
use App\Models\Restriction;
use App\Models\SaleInvoice;
use App\Models\StockDetail;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use App\Http\Requests\SaleInvoice\StoreSaleInvoiceRequest;
use App\Http\Requests\SaleInvoice\UpdateSaleInvoiceRequest;

class SaleInvoiceContoller extends Controller
{
    public function index(): View
    {
        $sale_invoices = SaleInvoice::withCount('allocations')->where('branch_id', session('branch_id'))->orderBy('invoice_number', 'DESC')->get();
        return view('sale.index', compact('sale_invoices'));
    }

    public function create(): View
    {
        $results = [];
        $invoice_number = SaleInvoice::where('branch_id', session('branch_id'))->max('invoice_number') + 1;
        $customers = Tree4::whereTree3_code(1205)->orWhere('tree3_code', '2301')->get();
        $representatives = Tree4::where('tree3_code', '1206')->get();
        $stocks = Stock::get();
        $safes = Tree4::where('tree3_code', '1202')->get();
        $banks = Tree4::where('tree3_code', '1203')->get();
        $products = Product::with('stocks', 'units')->inRandomOrder()->get();

        return view('sale.create', compact('customers', 'representatives', 'invoice_number', 'stocks', 'safes', 'banks', 'products'));
    }

    public function store(StoreSaleInvoiceRequest $request): View|RedirectResponse
    {
        try {
            DB::beginTransaction();
            $invoice_status = $request->deferend_amount != '0.00' ? 0 : 1;
            $transaction_no = (float) str_replace(',', '', $request->bank_amount_total ?? '') > 0 ? $request->transaction_no : null;

            $sale_invoice = SaleInvoice::create(
                attributes: [
                    'customer_id' => $request->get('customer_id'),
                    'representative_id' => $request->get('representative_id'),
                    'invoice_number' => $request->get('invoice_number'),
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
                $availableQty = $productStock ? $productStock->pivot->quantity : 0;
                if ($qty > $availableQty) {
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

            return redirect()->route('sale_invoice.print', $sale_invoice);
        } catch (\Exception $exception) {
            DB::rollback();
            return redirect()
                ->route('sale_invoice.create')
                ->withErrors(['error' => $exception->getMessage()])
                ->withInput();
        }
    }

    public function print(SaleInvoice $sale_invoice)
    {
        $madinTotal = $dainTotal = $allDainTotal = $allMadinTotal = 0;
        $sale_invoice = $sale_invoice->load(['tree4', 'details', 'details.product', 'details.unit']);
        if ($sale_invoice->customer_id) {
            $restriction = Restriction::where('sale_invoice_id', $sale_invoice->id)->latest()->first();
            $madinTotal = Restriction::where('tree4_id', $sale_invoice->customer_id)->where('constant_number', '<', $restriction->constant_number)->sum('madin');
            $dainTotal = Restriction::where('tree4_id', $sale_invoice->customer_id)->where('constant_number', '<', $restriction->constant_number)->sum('dain');
            $allMadinTotal = Restriction::where('tree4_id', $sale_invoice->customer_id)->sum('madin');

            $allDainTotal = Restriction::where('tree4_id', $sale_invoice->customer_id)->sum('dain');
        }

        $bank = Tree4::where('tree3_code', 1203)->select('no_bank')->first();

        return view('sale.print', compact('sale_invoice', 'madinTotal', 'dainTotal', 'allMadinTotal', 'allDainTotal', 'bank'));
    }

    public function edit(SaleInvoice $sale_invoice): View|RedirectResponse
    {
        if ($sale_invoice->allocations()->exists()) {
            return redirect()->route('sale_invoice.index')->withErrors(['error' => 'لا يمكن تعديل هذه الفاتورة لانها مرتبطه بتحصيل فواتير العملاء']);
        }
        $customers = Tree4::whereTree3_code(1205)->orWhere('tree3_code', '2301')->get();
        $stocks = Stock::get();
        $representatives = Tree4::where('tree3_code', '1206')->get();
        $safes = Tree4::where('tree3_code', '1202')->get();
        $banks = Tree4::where('tree3_code', '1203')->get();
        $products = Product::with('stocks', 'units')->inRandomOrder()->get();
        $productIds = $sale_invoice->details()->pluck('product_id')->toArray();

        return view('sale.edit', compact('sale_invoice', 'customers', 'representatives', 'stocks', 'safes', 'banks', 'products', 'productIds'));
    }

    public function update(UpdateSaleInvoiceRequest $request, SaleInvoice $sale_invoice): RedirectResponse
    {
        if ($sale_invoice->allocations()->exists()) {
            return redirect()->route('sale_invoice.index')->withErrors(['error' => 'لا يمكن تعديل هذه الفاتورة لانها مرتبطه بتحصيل فواتير العملاء']);
        }
        try {
            DB::beginTransaction();

            $invoice_status = $request->deferend_amount != '0.00' ? 0 : 1;
            $transaction_no = (float) str_replace(',', '', $request->bank_amount_total ?? '') > 0 ? $request->transaction_no : null;

            $oldDetails = $sale_invoice->details;
            foreach ($oldDetails as $item) {
                $product = Product::with(['stocks', 'units'])->find($item->product_id);
                if (!$product) {
                    continue;
                }

                $unit = $product->units()->where('unit_id', $item->unit_id)->first();
                $stock = $product->stocks()->where('stock_id', $item->stock_id)->first();

                if (!$unit || !$stock) {
                    continue;
                }

                $qtyBack = ($item->qty + ($item->bounce ?? 0)) * $unit->pivot->conversion_factor;

                $product->stocks()->updateExistingPivot($item->stock_id, ['quantity' => $stock->pivot->quantity + $qtyBack]);
            }

            $sale_invoice->delete();

            $sale_invoice = SaleInvoice::create([
                'customer_id' => $request->customer_id,
                'representative_id' => $request->representative_id,
                'invoice_number' => $request->invoice_number,
                'date' => $request->date,
                'sub_total_amount' => $request->sub_total,
                'discount_amount' => $request->discount,
                'tax_amount' => $request->tax,
                'tax_status' => $request->tax_status ? 1 : 0,
                'total_amount' => $request->total,
                'status' => 1,
                'cash_name' => $request->cash_name,
                'bank_name' => $request->bank_name,
                'cash_amount_total' => $request->cash_amount_total,
                'bank_amount_total' => $request->bank_amount_total,
                'transaction_no' => $transaction_no,
                'deferend_amount' => $request->deferend_amount,
                'process_no' => $request->process_no,
                'invoice_status' => $invoice_status,
                'note' => $request->note,
                'branch_id' => session('branch_id'),
                'user_id' => Auth::id(),
            ]);

            foreach ($request->product_id as $index => $product_id) {
                $sale_invoice->details()->create([
                    'product_id' => $product_id,
                    'stock_id' => $request->stock_id[$index],
                    'qty' => $request->qty[$index],
                    'bounce' => $request->bounce[$index] ?? 0,
                    'expire_date' => $request->expire_date[$index],
                    'price' => $request->price[$index],
                    'unit_id' => $request->unit_id[$index],
                    'row_sub_total' => $request->row_sub_total[$index],
                ]);

                $product = Product::with(['stocks', 'units'])->find($product_id);
                $unit = $product->units()->where('unit_id', $request->unit_id[$index])->first();
                $stock = $product->stocks()->where('stock_id', $request->stock_id[$index])->first();

                $qty = ($request->qty[$index] + $request->bounce[$index]) * $unit->pivot->conversion_factor;

                $availableQty = $stock ? $stock->pivot->quantity : 0;

                if ($qty > $availableQty) {
                    DB::rollBack();
                    return back()->withErrors([
                        'error' => 'الكمية باحد المنتجات أكبر من الكمية الموجودة في المخزن.',
                    ]);
                }

                $product->stocks()->updateExistingPivot($request->stock_id[$index], ['quantity' => $availableQty - $qty]);

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
        if ($sale_invoice->allocations()->exists()) {
            return back()->withErrors(['error' => 'لا يمكن حذف هذه الفاتورة لانها مرتبطه بتحصيل فواتير العملاء']);
        }
        try {
            DB::beginTransaction();
            foreach ($sale_invoice->details as $item) {
                $product = Product::with('stocks', 'units')->find($item->product_id);
                $producStock = $product->stocks()->where('stock_id', $item->stock_id)->first();
                $conversion_factor = $product->units()->firstWhere('unit_id', $item->unit_id)->pivot->conversion_factor;
                $qty = ($item->qty + $item->bounce) * $conversion_factor;
                $newQuantity = $producStock->pivot->quantity + $qty;
                $product->stocks()->syncWithoutDetaching([
                    $item->stock_id => ['quantity' => $newQuantity],
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

    public function getProduct(Request $request): JsonResponse
    {
        $product = Product::with(['units:id,name', 'stocks:id,name']) // تحميل علاقة الوحدات
            ->where('id', $request->product_id)
            ->first(); // جلب أول منتج مطابق

        // إذا لم يتم العثور على المنتج
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'هذا المنتج غير متوفر .',
            ]);
        }
        // إذا كان تاريخ الصلاحية قد انتهى
        if ($product->expire_date && $product->expire_date < now()) {
            return response()->json([
                'success' => false,
                'message' => 'هذا المنتج منتهي الصلاحية .',
            ]);
        }

        $requireStock = $request->boolean('require_stock');
        $availableStocks = $product->stocks()->wherePivot('quantity', '>', 0)->get(['stocks.id', 'stocks.name']);

        if ($requireStock && $availableStocks->isEmpty()) {
            return response()->json([
                'success' => false,
                'no_stock' => true,
                'message' => 'المنتج "' . $product->name . '" ليس له كمية في المخازن، لا يمكن اضافته للفاتورة',
            ]);
        }

        // إذا كان المنتج موجودًا ومفعلًا ولم تنتهِ صلاحيته
        return response()->json([
            'success' => true,
            'product' => $product,
            'units' => $product->units,
            'quantity' => $product->convertQuantity($product->stocks()->where('stock_id', $request->stock_id)->sum('quantity')),
            'qty_in_stock' => $availableStocks->first()?->pivot->quantity,
            'stock_from' => $product->stocks()->where('quantity', '>=', 0)->get(),
            'stocks' => $requireStock ? $availableStocks : Stock::get(),
        ]);
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

    public function search(Request $request): View|JsonResponse
    {
        $search = $request->input('search');
        $type = $request->type;
        $available = $request->boolean('available');

        $products = Product::query()
            ->select('id', 'name', 'barcode')
            ->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%")->orWhere('barcode', $search);
            })
            ->when($available, function ($query) {
                $query->whereHas('stocks', fn ($q) => $q->where('product_stocks.quantity', '>', 0));
            })
            ->with('units')
            ->when($search !== null && $search !== '', fn ($query) => $query->limit(20))
            ->get();

        if ($products->isEmpty()) {
            if ($type == 3) {
                return response()->json(['products' => []]);
            }

            return response()->json([
                'success' => false,
                'message' => 'هذا المنتج غير متوفر .',
            ]);
        }

        if ($type == 3) {
            return response()->json(['products' => $products]);
        }

        return view('sale.search', compact('products', 'type'));
    }

    public function generatePDF($id)
    {
        $sale_invoice = SaleInvoice::with(['tree4', 'details', 'details.product', 'details.unit', 'representative'])->findOrFail($id);
        $madinTotal = $dainTotal = $allDainTotal = $allMadinTotal = 0;
        if ($sale_invoice->customer_id) {
            $restriction = Restriction::where('sale_invoice_id', $sale_invoice->id)->latest()->first();
            $madinTotal = Restriction::where('tree4_id', $sale_invoice->customer_id)->where('constant_number', '<', $restriction->constant_number)->sum('madin');
            $dainTotal = Restriction::where('tree4_id', $sale_invoice->customer_id)->where('constant_number', '<', $restriction->constant_number)->sum('dain');
            $allMadinTotal = Restriction::where('tree4_id', $sale_invoice->customer_id)->sum('madin');

            $allDainTotal = Restriction::where('tree4_id', $sale_invoice->customer_id)->sum('dain');
        }

        $bank = Tree4::where('tree3_code', 1203)->select('no_bank')->first();

        $mpdf = new Mpdf([
            'default_font' => 'dejavusans',
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_top' => 8,
            'margin_bottom' => 8,
        ]);

        $html = view('sale.pdf', compact('sale_invoice', 'madinTotal', 'dainTotal', 'allMadinTotal', 'allDainTotal', 'bank'))->render();

        $mpdf->WriteHTML($html);
        $filename = 'invoice_' . $sale_invoice->invoice_number . '.pdf';
        return $mpdf->Output($filename, 'D');
    }

    public function pay_create(SaleInvoice $sale_invoice)
    {
        $safes = Tree4::where('tree3_code', '1202')->get();
        $banks = Tree4::where('tree3_code', '1203')->get();
        return view('sale.pay', compact('sale_invoice', 'safes', 'banks'));
    }

    public function pay_store(SaleInvoice $sale_invoice, Request $request)
    {
        if ($request->cash_name == null and $request->bank_name == null) {
            return back()->withErrors('لا يمكنك اتمام العميلة في حالة م ادخلت اسم خزنة او اسم بنك');
        }

        if ($request->cash_amount_total == null && $request->bank_amount_total == null) {
            return back()->withErrors('يجب ادخال المبالغ في الكاش او في البنك ');
        }

        $request->validate([
            'cash_amount_total' => 'nullable|numeric',
            'bank_amount_total' => 'nullable|numeric',
            'cash_name' => 'nullable|exists:tree4s,id',
            'bank_name' => 'nullable|exists:tree4s,id',
            'transaction_no' => ['nullable', 'string', 'max:100', Rule::requiredIf(fn () => (float) str_replace(',', '', $request->bank_amount_total ?? '') > 0)],
        ], [
            'transaction_no.required' => 'يرجى إدخال رقم عملية البنك.',
        ]);

        if ($request->cash_amount_total + $request->bank_amount_total > $sale_invoice->total_amount) {
            return back()->withErrors('يجب ان يكون مجموع المدفوع بالبنك او الكاش اصغر او يساوي اجمالي الفاتورة ');
        }

        $transaction_no = (float) str_replace(',', '', $request->bank_amount_total ?? '') > 0 ? $request->transaction_no : null;
        $total = $request->cash_amount_total + $request->bank_amount_total;
        $last_constant_number = Restriction::where('branch_id', session('branch_id'))->max('constant_number');
        $constant_number = $last_constant_number ? $last_constant_number + 1 : 1;
        $tree4_customer = Tree4::find($sale_invoice->customer_id);
        if ($request->cash_amount_total != '') {
            $tree4 = Tree4::find($request->cash_name);

            $this->restriction($tree4->id, $tree4->tree4_code, $request->cash_amount_total, 0, $constant_number, $sale_invoice, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل', $sale_invoice->date);
            $this->restriction($tree4_customer->id, $tree4_customer->tree4_code, 0, $request->cash_amount_total, $constant_number, $sale_invoice, 'فاتورة مبيعات تم الدفع بالكاش عن طريق عميل', $sale_invoice->date);
        }

        if ($request->bank_amount_total != '') {
            $tree4 = Tree4::find($request->bank_name);

            $this->restriction($tree4->id, $tree4->tree4_code, $request->bank_amount_total, 0, $constant_number, $sale_invoice, 'فاتورة مبيعات تم الدفع بالبنك عن طريق عميل', $sale_invoice->date, $transaction_no);
            $this->restriction($tree4_customer->id, $tree4_customer->tree4_code, 0, $request->bank_amount_total, $constant_number, $sale_invoice, 'فاتورة مبيعات تم الدفع بالبنك عن طريق عميل', $sale_invoice->date, $transaction_no);
        }

        $sale_invoice->update(['deferend_amount' => $sale_invoice->deferend_amount - $total]);

        return redirect()->route('sale_invoice.index')->with('success_created', true);
    }
}
