<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Stock;
use App\Models\Tree3;
use App\Models\Tree4;
use App\Models\Product;
use App\Models\Category;
use App\Models\Curencey;
use Illuminate\View\View;
use App\Models\Restriction;
use App\Models\SaleInvoice;
use App\Models\StockDetail;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\PurchaseInvoice;
use App\Models\SaleInvoiceDetail;
use App\Models\SaleInvoiceReturn;
use App\Models\PurchaseInvoiceReturn;

class ReportController extends Controller
{
    public function create_statement(): View
    {
        $trees = Tree4::get();
        $curenceies = Curencey::get();
        return view('report.account_statement.index', compact('trees', 'curenceies'));
    }

    public function show_statement(Request $request): View
    {
        $tree4Id = $request->tree4 == 'عام' ? null : $request->tree4;
        $start = $request->start;
        $end = $request->end;
        $tree4 = Tree4::find($tree4Id) ?? null;
        $curencey = Curencey::find($request->curencey_id);
        $current_balance = 0;

        // القيود
        $tree4Constants = Restriction::when($tree4Id, fn($q) => $q->where('tree4_id', $tree4Id))->where('branch_id', session('branch_id'))->when($start, fn($q) => $q->whereDate('created_at', '>=', $start))->when($end, fn($q) => $q->whereDate('created_at', '<=', $end))->pluck('constant_number')->unique()->toArray();

        $operations = Restriction::with('tree4')->whereIn('constant_number', $tree4Constants)->where('branch_id', session('branch_id'))->when($tree4Id, fn($q) => $q->where('tree4_id', $tree4Id))->when($start, fn($q) => $q->whereDate('created_at', '>=', $start))->when($end, fn($q) => $q->whereDate('created_at', '<=', $end))->orderBy('created_at', 'asc')->orderBy('id', 'asc')->get();

        // الرصيد الافتتاحي
        $madin_opening = Restriction::when($tree4Id, fn($q) => $q->where('tree4_id', $tree4Id))->where('branch_id', session('branch_id'))->when($start, fn($q) => $q->whereDate('created_at', '<', $start))->sum('madin');

        $dain_opening = Restriction::when($tree4Id, fn($q) => $q->where('tree4_id', $tree4Id))->where('branch_id', session('branch_id'))->when($start, fn($q) => $q->whereDate('created_at', '<', $start))->sum('dain');

        $blance = $madin_opening - $dain_opening;
        foreach ($operations as $operation) {
            // قيمة المدين والدائن
            $madin_value = $operation->dain == 0 ? $operation->madin : 0;
            $dain_value = $operation->madin == 0 ? $operation->dain : 0;

            // فرق العملية وتحديث الرصيد
            $difference = $madin_value - $dain_value;
            $current_balance += $difference;

            // إضافة الحقول للحلقة
            $operation->madin_value = $madin_value;
            $operation->dain_value = $dain_value;
            $operation->balance = $current_balance;
            // $operation->tree4_id = $
        }

        return view('report.account_statement.show', compact('operations', 'start', 'end', 'blance', 'tree4', 'curencey'));
    }

    public function journal(): View
    {
        $curenceies = Curencey::get();
        return view('report.journal.index', compact('curenceies'));
    }

    public function show_journal(Request $request): View
    {
        $start = $request->start;
        $end = $request->end;
        $curencey = Curencey::find($request->curencey_id);
        $restrictions = Restriction::with(['tree4', 'user'])
            ->where('branch_id', session('branch_id'))
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->orderByDesc('id')
            ->get();

        // نجمع القيود بحسب رقم المعاملة
        $grouped = $restrictions->groupBy('constant_number');

        $results = $grouped
            ->map(function ($group) use ($curencey) {
                // كل عناصر هذا القيد
                $madinAccounts = [];
                $dainAccounts = [];
                $id = null;
                $date = null;
                $user = null;
                $type = null;
                $node = null;
                $constant_number = null;

                foreach ($group as $entry) {
                    $name = $entry->tree4->tree4_name ?? '';
                    if ($entry->dain > 0) {
                        $madinAccounts[] = $name . ' (' . number_format($entry->dain / $curencey->amount, 2) . ' ' . $curencey->symbol . ')';
                    }
                    if ($entry->madin > 0) {
                        $dainAccounts[] = $name . ' (' . number_format($entry->madin / $curencey->amount, 2) . ' ' . $curencey->symbol . ')';
                    }
                    $id = $entry->id;
                    $date = $entry->date;
                    $user = $entry->user->name ?? '';
                    $type = $entry->type;
                    $node = $entry->node;
                    $constant_number = $entry->constant_number;
                }

                return (object) [
                    'id' => $id,
                    'constant_number' => $constant_number,
                    'date' => $date,
                    'madin_name' => implode(' + ', $madinAccounts),
                    'dain_name' => implode(' + ', $dainAccounts),
                    'user' => $user,
                    'type' => $type,
                    'node' => $node,
                    'price' => $group->sum('madin') ?? $group->sum('dain'),
                ];
            })
            ->values(); // ترتيب النتائج

        return view('report.journal.show', ['operations' => $results, 'start' => $start, 'end' => $end, 'curencey' => $curencey]);
    }

    public function journalEntry(): View
    {
        $restrictions = Restriction::with(['tree4', 'user'])
            ->where('branch_id', session('branch_id'))
            ->whereIn('type', [5, 6])
            ->orderByDesc('id')
            ->get();

        // نجمع القيود بحسب رقم المعاملة
        $grouped = $restrictions->groupBy('constant_number');

        $results = $grouped
            ->map(function ($group) {
                // كل عناصر هذا القيد
                $madinAccounts = [];
                $dainAccounts = [];
                $id = null;
                $date = null;
                $user = null;
                $type = null;
                $node = null;
                $constant_number = null;

                foreach ($group as $entry) {
                    $name = $entry->tree4->tree4_name ?? '';
                    if ($entry->madin > 0) {
                        $madinAccounts[] = $name . ' (' . number_format($entry->madin, 2) . ')';
                    }
                    if ($entry->dain > 0) {
                        $dainAccounts[] = $name . ' (' . number_format($entry->dain, 2) . ')';
                    }
                    $id = $entry->id;
                    $date = $entry->date;
                    $user = $entry->user->name ?? '';
                    $type = $entry->type;
                    $node = $entry->node;
                    $constant_number = $entry->constant_number;
                }

                return (object) [
                    'id' => $id,
                    'constant_number' => $constant_number,
                    'date' => $date,
                    'madin_name' => implode(' + ', $madinAccounts),
                    'dain_name' => implode(' + ', $dainAccounts),
                    'user' => $user,
                    'type' => $type,
                    'node' => $node,
                    'price' => $group->sum('madin') ?? $group->sum('dain'),
                ];
            })
            ->values();

        return view('report.journalEntry.index', ['operations' => $results]);
    }

    public function show_journalEntry($id): View
    {
        $operation = Restriction::with('tree4')->find($id);
        $restrictions = Restriction::where('constant_number', $operation->constant_number)->where('branch_id', session('branch_id'))->get();
        $grouped = $restrictions->groupBy('constant_number');

        $results = $grouped
            ->map(function ($group) {
                // كل عناصر هذا القيد
                $madinAccounts = [];
                $dainAccounts = [];
                $id = null;
                $date = null;
                $user = null;
                $type = null;
                $node = null;
                $constant_number = null;

                foreach ($group as $entry) {
                    $name = $entry->tree4->tree4_name ?? '';
                    if ($entry->madin > 0) {
                        $madinAccounts[] = $name . ' (' . number_format($entry->madin, 2) . ')';
                    }
                    if ($entry->dain > 0) {
                        $dainAccounts[] = $name . ' (' . number_format($entry->dain, 2) . ')';
                    }
                    $id = $entry->id;
                    $date = $entry->d;
                    $user = $entry->user->name ?? '';
                    $type = $entry->type;
                    $node = $entry->node;
                    $constant_number = $entry->constant_number;
                }
                return (object) [
                    'id' => $id,
                    'constant_number' => $constant_number,
                    'date' => $date,
                    'madin_name' => implode(' + ', $madinAccounts),
                    'dain_name' => implode(' + ', $dainAccounts),
                    'user' => $user,
                    'type' => $type,
                    'node' => $node,
                    'price' => $group->sum('madin') ?? $group->sum('dain'),
                ];
            })
            ->values();
        return view('report.journalEntry.show', ['operation' => $results[0]]);
    }

    public function accountingBooks(): View
    {
        $trees = Tree3::get();
        $curenceies = Curencey::get();
        return view('report.book.index', compact('trees', 'curenceies'));
    }

    public function show_accountingBooks(Request $request): View
    {
        $curencey = Curencey::find($request->curencey_id);
        $tree3 = Tree3::where('id', $request->tree3)->first();
        $one = Restriction::where('tree4_code', 'like', $tree3->tree3_code . '%')
            ->where('branch_id', session('branch_id'))
            ->whereDate('created_at', '>=', $request->start)
            ->whereDate('created_at', '<=', $request->end)
            ->get();
        $Madin = Restriction::where('tree4_code', 'like', $tree3->tree3_code . '%')
            ->where('branch_id', session('branch_id'))
            ->whereDate('created_at', '>=', $request->start)
            ->whereDate('created_at', '<=', $request->end)
            ->sum('madin');
        $Dain = Restriction::where('tree4_code', 'like', $tree3->tree3_code . '%')
            ->where('branch_id', session('branch_id'))
            ->whereDate('created_at', '>=', $request->start)
            ->whereDate('created_at', '<=', $request->end)
            ->sum('dain');
        $start = $request->start;
        $end = $request->end;
        return view('report.book.show', compact('one', 'start', 'end', 'Madin', 'Dain', 'curencey'));
    }

    public function topProducts(): View
    {
        // جلب المنتجات الأكثر مبيعًا بناءً على عدد مرات الطلب
        $topProducts = SaleInvoiceDetail::whereHas('sale_invoice', function ($query) {
            $query->where('branch_id', session('branch_id'));
        })
            ->select('product_id')
            ->selectRaw('sum(qty) as total_qty')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product') // جلب بيانات المنتج
            ->get();

        return view('report.product.top_products', compact('topProducts'));
    }

    public function downProducts(): View
    {
        // جلب المنتجات الأكثر مبيعًا بناءً على عدد مرات الطلب
        $topProducts = SaleInvoiceDetail::whereHas('sale_invoice', function ($query) {
            $query->where('branch_id', session('branch_id'));
        })
            ->select('product_id')
            ->selectRaw('sum(qty) as total_qty')
            ->groupBy('product_id')
            ->orderBy('total_qty', 'asc')
            ->with('product') // جلب بيانات المنتج
            ->get();

        return view('report.product.down_products', compact('topProducts'));
    }

    public function customerSales(): View
    {
        return view('report.customer.index');
    }

    public function show_customerSales(Request $request): View
    {
        $start = $request->start;
        $end = $request->end;
        $sales = SaleInvoice::where('branch_id', session('branch_id'))->whereDate('created_at', '>=', $request->start)->whereDate('created_at', '<=', $request->end)->whereCustomer_id($request->tree4)->orderByDesc('id')->get();
        return view('report.customer.show', compact('sales', 'start', 'end'));
    }

    public function supplierPurchases(): View
    {
        return view('report.supplier.index');
    }

    public function show_supplierPurchases(Request $request): View
    {
        $start = $request->start;
        $end = $request->end;
        $purchases = PurchaseInvoice::where('branch_id', session('branch_id'))->whereDate('created_at', '>=', $request->start)->whereDate('created_at', '<=', $request->end)->whereSupplier_id($request->tree4)->orderByDesc('id')->get();
        return view('report.supplier.show', compact('purchases', 'start', 'end'));
    }

    public function expiringProducts(): View
    {
        $today = Carbon::today();
        $nextWeek = $today->copy()->addWeek(); // تاريخ بعد 7 أيام

        // جلب المنتجات التي ستنتهي صلاحيتها خلال أسبوع، مع استبعاد المنتجات المنتهية الصلاحية
        $products = Product::whereBetween('expire_date', [$today, $nextWeek])->get();

        return view('report.product.expiring-products', compact('products'));
    }

    public function expiredProducts(): View
    {
        $today = Carbon::today();

        // جلب المنتجات التي انتهت صلاحيتها
        $products = Product::where('expire_date', '<=', $today)->get();

        return view('report.product.expired_products', compact('products'));
    }

    public function lowStockProducts(): View
    {
        $products = Product::with('stocks')
            ->get()
            ->filter(function ($product) {
                $totalQuantity = $product->stocks->sum('pivot.quantity'); // جمع كميات المنتج عبر جميع المخازن
                return $totalQuantity < $product->reorder_level;
            });

        return view('report.product.low_stock_products', compact('products'));
    }

    public function sale(): View
    {
        return view('report.sale.index');
    }

    public function show_sale(Request $request): View
    {
        $start = $request->start;
        $end = $request->end;
        $sales = SaleInvoice::where('branch_id', session('branch_id'))->whereDate('created_at', '>=', $request->start)->whereDate('created_at', '<=', $request->end)->get();
        return view('report.sale.show', compact('sales', 'start', 'end'));
    }

    public function purchase(): View
    {
        return view('report.purchase.index');
    }

    public function show_purchase(Request $request): View
    {
        $start = $request->start;
        $end = $request->end;
        $purchase_returns = PurchaseInvoice::where('branch_id', session('branch_id'))->whereDate('created_at', '>=', $request->start)->whereDate('created_at', '<=', $request->end)->get();
        return view('report.purchase.show', compact('purchase_returns', 'start', 'end'));
    }

    public function saleReturn(): View
    {
        return view('report.sale_return.index');
    }

    public function showSaleReturn(Request $request): View
    {
        $start = $request->start;
        $end = $request->end;
        $sale_returns = SaleInvoiceReturn::where('branch_id', session('branch_id'))->whereDate('created_at', '>=', $request->start)->whereDate('created_at', '<=', $request->end)->get();
        return view('report.sale_return.show', compact('sale_returns', 'start', 'end'));
    }

    public function purchaseReturn(): View
    {
        return view('report.purchase_return.index');
    }

    public function showPurchaseReturn(Request $request): View
    {
        $start = $request->start;
        $end = $request->end;
        $pruchase_returns = PurchaseInvoiceReturn::where('branch_id', session('branch_id'))->whereDate('created_at', '>=', $request->start)->whereDate('created_at', '<=', $request->end)->get();
        return view('report.purchase_return.show', compact('pruchase_returns', 'start', 'end'));
    }

    public function customerAccount(): View
    {
        $customers = Tree4::where('tree3_code', 1205)->get();
        $curenceies = Curencey::get();
        return view('report.customer.account_index', compact('customers', 'curenceies'));
    }

    public function customerAccountShow(Request $request)
    {
        $curencey = Curencey::find($request->curencey_id);
        $customerId = $request->customer_id;
        $start = $request->start;
        $end = $request->end;
        $current_balance = 0;
        $customer = Tree4::find($customerId) ?? null;

        // الخطوة 1: جيب كل أرقام العمليات (constant_number) للعميل فقط
        $customerConstants = Restriction::where('branch_id', session('branch_id'))->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end);
        if ($customerId) {
            $customerConstants = $customerConstants->where('tree4_id', $customerId)->pluck('constant_number')->unique()->toArray();
        } else {
            $customerConstants = $customerConstants->where('tree4_code', 'like', '1205%')->pluck('constant_number')->unique()->toArray();
        }
        // الخطوة 2: جيب العمليات التي لها نفس constant_number، لكن ليست للعميل
        $operations = Restriction::with('tree4')->whereIn('constant_number', $customerConstants)->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)->orderBy('created_at', 'asc')->orderBy('id', 'asc');
        if ($customerId) {
            $operations = $operations->where('tree4_id', $customerId)->get();
        } else {
            $operations = $operations->where('tree4_code', 'like', '1205%')->get();
        }
        $madin_opening = Restriction::where('branch_id', session('branch_id'))->whereDate('created_at', '<', $start);
        $dain_opening = Restriction::where('branch_id', session('branch_id'))->whereDate('created_at', '<', $start);
        if ($request->customer_id) {
            $madin_opening = $madin_opening->where('tree4_id', $request->customer_id)->sum('madin');
            $dain_opening = $dain_opening->where('tree4_id', $request->customer_id)->sum('dain');
        } else {
            $madin_opening = $madin_opening->where('tree4_code', 'like', '1205%')->sum('madin');
            $dain_opening = $dain_opening->where('tree4_code', 'like', '1205%')->sum('dain');
        }
        $blance = $madin_opening - $dain_opening;
        foreach ($operations as $operation) {
            // قيمة المدين والدائن
            $madin_value = $operation->dain == 0 ? $operation->madin : 0;
            $dain_value = $operation->madin == 0 ? $operation->dain : 0;

            // فرق العملية وتحديث الرصيد
            $difference = $madin_value - $dain_value;
            $current_balance += $difference;

            // إضافة الحقول للحلقة
            $operation->madin_value = $madin_value;
            $operation->dain_value = $dain_value;
            $operation->balance = $current_balance;
        }

        return view('report.customer.account_show', [
            'operations' => $operations,
            'start' => $start,
            'end' => $end,
            'blance' => $blance,
            'customer' => $customer,
            'curencey' => $curencey,
        ]);
    }

    public function supplierAccount(): View
    {
        $suppliers = Tree4::where('tree3_code', 2301)->get();
        $curenceies = Curencey::get();
        return view('report.supplier.account_index', compact('suppliers', 'curenceies'));
    }

    public function supplierAccountShow(Request $request)
    {
        $supplierId = $request->supplier_id;
        $start = $request->start;
        $end = $request->end;
        $supplier = Tree4::find($supplierId) ?? null;
        $current_balance = 0;
        $curencey = Curencey::find($request->curencey_id);

        $supplierConstants = Restriction::where('branch_id', session('branch_id'))->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end);
        if ($supplierId) {
            $supplierConstants = $supplierConstants->where('tree4_id', $supplierId)->pluck('constant_number')->unique()->toArray();
        } else {
            $supplierConstants = $supplierConstants->where('tree4_code', 'like', '2301%')->pluck('constant_number')->unique()->toArray();
        }
        $operations = Restriction::with('tree4')->whereIn('constant_number', $supplierConstants)->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)->orderBy('created_at', 'asc')->orderBy('id', 'asc');
        if ($supplierId) {
            $operations = $operations->where('tree4_id', $supplierId)->get();
        } else {
            $operations = $operations->where('tree4_code', 'like', '2301%')->get();
        }
        $madin_opening = Restriction::where('branch_id', session('branch_id'))->whereDate('created_at', '<', $start);
        $dain_opening = Restriction::where('branch_id', session('branch_id'))->whereDate('created_at', '<', $start);
        if ($request->supplier_id) {
            $madin_opening = $madin_opening->where('tree4_id', $request->supplier_id)->sum('madin');
            $dain_opening = $dain_opening->where('tree4_id', $request->supplier_id)->sum('dain');
        } else {
            $madin_opening = $madin_opening->where('tree4_code', 'like', '2301%')->sum('madin');
            $dain_opening = $dain_opening->where('tree4_code', 'like', '2301%')->sum('dain');
        }
        $blance = $madin_opening - $dain_opening;
        foreach ($operations as $operation) {
            // قيمة المدين والدائن
            $madin_value = $operation->dain == 0 ? $operation->madin : 0;
            $dain_value = $operation->madin == 0 ? $operation->dain : 0;

            // فرق العملية وتحديث الرصيد
            $difference = $madin_value - $dain_value;
            $current_balance += $difference;

            // إضافة الحقول للحلقة
            $operation->madin_value = $madin_value;
            $operation->dain_value = $dain_value;
            $operation->balance = $current_balance;
        }

        return view('report.supplier.account_show', [
            'operations' => $operations,
            'start' => $start,
            'end' => $end,
            'blance' => $blance,
            'supplier' => $supplier,
            'curencey' => $curencey,
        ]);
    }

    public function safeAccount(): View
    {
        $safes = Tree4::where('tree3_code', 1202)->get();
        $curenceies = Curencey::get();
        return view('report.safe.index', compact('safes', 'curenceies'));
    }

    public function safeAccountShow(Request $request)
    {
        $safeId = $request->safe_id;
        $start = $request->start;
        $end = $request->end;
        $curencey = Curencey::find($request->curencey_id);

        $safe = Tree4::find($safeId);
        $current_balance = 0;

        // نحسب الرصيد الافتتاحي من القيود الحقيقية
        $madin_opening = Restriction::where('branch_id', session('branch_id'))->where('tree4_id', $safeId)->whereDate('created_at', '<', $start)->sum('madin');

        $dain_opening = Restriction::where('branch_id', session('branch_id'))->where('tree4_id', $safeId)->whereDate('created_at', '<', $start)->sum('dain');

        $opening_balance = $madin_opening - $dain_opening;
        $current_balance = $opening_balance;

        // القيود داخل الفترة فقط
        $safeConstants = Restriction::where('branch_id', session('branch_id'))->where('tree4_id', $safeId)->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)->pluck('constant_number')->unique()->toArray();

        $operations = Restriction::with('tree4')->whereIn('constant_number', $safeConstants)->where('tree4_id', $safeId)->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)->orderBy('created_at', 'asc')->orderBy('id', 'asc')->get();

        foreach ($operations as $operation) {
            $madin_value = $operation->madin;
            $dain_value = $operation->dain;

            $others = Restriction::with('tree4')
                ->where('constant_number', $operation->constant_number)
                ->where('tree4_id', '!=', $safeId)
                ->where(function ($q) use ($madin_value, $dain_value) {
                    if ($madin_value > 0) {
                        $q->where('dain', '>', 0);
                    } else {
                        $q->where('madin', '>', 0);
                    }
                })
                ->get();

            $names = $others->pluck('tree4.tree4_name')->filter()->unique()->toArray();
            $operation->name = count($names) > 0 ? implode(' + ', $names) : 'غير محدد';

            $operation->madin_value = $madin_value > 0 ? $madin_value : 0;
            $operation->dain_value = $dain_value > 0 ? $dain_value : 0;

            $current_balance += $madin_value - $dain_value;
            $operation->balance = $current_balance;
        }

        return view('report.safe.show', [
            'operations' => $operations,
            'start' => $start,
            'end' => $end,
            'blance' => $opening_balance,
            'safe' => $safe,
            'curencey' => $curencey,
        ]);
    }

    public function bankAccount(): View
    {
        $banks = Tree4::where('tree3_code', 1203)->get();
        $curenceies = Curencey::get();
        return view('report.bank.index', compact('banks', 'curenceies'));
    }

    public function bankAccountShow(Request $request)
    {
        $bankId = $request->bank_id;
        $start = $request->start;
        $end = $request->end;
        $current_balance = 0;
        $bank = Tree4::find($bankId);
        $curencey = Curencey::find($request->curencey_id);

        $madin_opening = Restriction::where('branch_id', session('branch_id'))->where('tree4_id', $bankId)->whereDate('created_at', '<', $start)->sum('madin');

        $dain_opening = Restriction::where('branch_id', session('branch_id'))->where('tree4_id', $bankId)->whereDate('created_at', '<', $start)->sum('dain');

        $opening_balance = $madin_opening - $dain_opening;
        $current_balance = $opening_balance;

        // القيود داخل الفترة فقط
        $bankConstants = Restriction::where('branch_id', session('branch_id'))->where('tree4_id', $bankId)->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)->pluck('constant_number')->unique()->toArray();

        $operations = Restriction::with('tree4')->whereIn('constant_number', $bankConstants)->where('tree4_id', $bankId)->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)->orderBy('created_at', 'asc')->orderBy('id', 'asc')->get();

        foreach ($operations as $operation) {
            $madin_value = $operation->madin;
            $dain_value = $operation->dain;

            $others = Restriction::with('tree4')
                ->where('constant_number', $operation->constant_number)
                ->where('tree4_id', '!=', $bankId)
                ->where(function ($q) use ($madin_value, $dain_value) {
                    if ($madin_value > 0) {
                        $q->where('dain', '>', 0);
                    } else {
                        $q->where('madin', '>', 0);
                    }
                })
                ->get();

            $names = $others->pluck('tree4.tree4_name')->filter()->unique()->toArray();
            $operation->name = count($names) > 0 ? implode(' + ', $names) : 'غير محدد';

            $operation->madin_value = $madin_value > 0 ? $madin_value : 0;
            $operation->dain_value = $dain_value > 0 ? $dain_value : 0;

            $current_balance += $madin_value - $dain_value;
            $operation->balance = $current_balance;
        }
        return view('report.bank.show', [
            'operations' => $operations,
            'start' => $start,
            'end' => $end,
            'blance' => $opening_balance,
            'bank' => $bank,
            'curencey' => $curencey,
        ]);
    }

    public function representativeAccount(): View
    {
        $representatives = Tree4::where('tree3_code', 1206)->get();
        $curenceies = Curencey::get();
        return view('report.representative.index', compact('representatives', 'curenceies'));
    }

    public function representativeAccountShow(Request $request)
    {
        $name = $request->representative_id;
        $start = $request->start;
        $end = $request->end;
        $curencey = Curencey::find($request->curencey_id);

        $representative = Tree4::find($request->representative_id);
        $sale_invoices = SaleInvoice::whereRepresentative_id($request->representative_id)->where('branch_id', session('branch_id'))->whereDate('created_at', '>=', $request->start)->whereDate('created_at', '<=', $request->end)->latest()->get();
        $sale_invoices_return = SaleInvoiceReturn::whereRepresentative_id($request->representative_id)->where('branch_id', session('branch_id'))->whereDate('created_at', '>=', $request->start)->whereDate('created_at', '<=', $request->end)->latest()->get();
        $purchase_invoices = PurchaseInvoice::whereRepresentative_id($request->representative_id)->where('branch_id', session('branch_id'))->whereDate('created_at', '>=', $request->start)->whereDate('created_at', '<=', $request->end)->latest()->get();
        $purchase_invoices_return = PurchaseInvoiceReturn::whereRepresentative_id($request->representative_id)->where('branch_id', session('branch_id'))->whereDate('created_at', '>=', $request->start)->whereDate('created_at', '<=', $request->end)->latest()->get();
        $opening_amount = SaleInvoice::whereRepresentative_id($request->representative_id)->where('branch_id', session('branch_id'))->whereDate('created_at', '<', $start)->sum('total_price');
        $opening_amount += SaleInvoiceReturn::whereRepresentative_id($request->representative_id)->where('branch_id', session('branch_id'))->whereDate('created_at', '<', $start)->sum('total_price');
        $opening_amount -= PurchaseInvoice::whereRepresentative_id($request->representative_id)->where('branch_id', session('branch_id'))->whereDate('created_at', '<', $start)->sum('total_price');
        $opening_amount -= PurchaseInvoiceReturn::whereRepresentative_id($request->representative_id)->where('branch_id', session('branch_id'))->whereDate('created_at', '<', $start)->sum('total_price');
        foreach ($sale_invoices as $sale_invoice) {
            $sale_invoice->type = 1;
        }
        foreach ($sale_invoices_return as $sale_invoice_return) {
            $sale_invoice_return->type = 2;
        }
        foreach ($purchase_invoices as $purchase_invoice) {
            $purchase_invoice->type = 3;
        }
        foreach ($purchase_invoices_return as $purchase_invoice_return) {
            $purchase_invoice_return->type = 4;
        }
        $representatives = $purchase_invoices->concat($purchase_invoices_return)->concat($sale_invoices)->concat($sale_invoices_return)->sortByDesc('id')->values();
        return view('report.representative.invoice', compact('representatives', 'start', 'end', 'representative', 'curencey'));
    }

    public function select_representative()
    {
        $representatives = Tree4::where('tree3_code', 1206)->get();
        $categories = Category::orderBy('order', 'asc')->get();
        return view('report.product.select_representative', compact('representatives', 'categories'));
    }

    public function price(Request $request): View|RedirectResponse
    {
        $show_expiry = $request->boolean('show_expiry');
        $show_company = $request->boolean('show_company');

        $selectedCategory = null;
        if ($request->mode === 'category') {
            if (! $request->filled('category_id')) {
                return back()->with('error', 'يجب اختيار القسم لعرض سعر القسم المحدد');
            }
            $selectedCategory = Category::find($request->category_id);
        }

        $categoriesQuery = Category::whereHas('products', function ($q) {
            $q->whereHas('stocks', function ($q2) {
                $q2->where('product_stocks.quantity', '>', 0);
            });
        })
            ->with([
                'products' => function ($q) {
                    $q->whereHas('stocks', function ($q2) {
                        $q2->where('product_stocks.quantity', '>', 0);
                    });
                },
                'products.units',
            ])
            ->orderBy('order', 'ASC');

        if ($selectedCategory) {
            $categoriesQuery->where('id', $selectedCategory->id);
        }

        $categories = $categoriesQuery->get();
        $representative = Tree4::find($request->representative_id);

        return view('report.product.price', compact('categories', 'representative', 'show_expiry', 'show_company', 'selectedCategory'));
    }

    public function revenueAccount()
    {
        $curenceies = Curencey::get();
        return view('report.revenue.index', compact('curenceies'));
    }

    public function revenueAccountShow(Request $request)
    {
        $start = $request->start;
        $end = $request->end;
        $curencey = Curencey::find($request->curencey_id);
        $operations = Restriction::with('tree4')->where('branch_id', session('branch_id'))->where('type', 8)->where('dain', '!=', 0)->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)->get();
        return view('report.revenue.show', compact('start', 'end', 'operations', 'curencey'));
    }

    public function expenseAccount()
    {
        $curenceies = Curencey::get();
        return view('report.expense.index', compact('curenceies'));
    }

    public function expenseAccountShow(Request $request)
    {
        $start = $request->start;
        $end = $request->end;
        $curencey = Curencey::find($request->curencey_id);
        $operations = Restriction::with('tree4')->where('branch_id', session('branch_id'))->where('type', operator: 9)->where('madin', '!=', 0)->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)->get();
        return view('report.expense.show', compact('start', 'end', 'operations', 'curencey'));
    }

    public function stock_detail_index()
    {
        $stocks = Stock::get();
        return view('report.stock.index', compact('stocks'));
    }

    public function stock_detail_show(Request $request)
    {
        $start = $request->start;
        $end = $request->end;
        $grouped = StockDetail::with('product', 'unit', 'stock')->where('branch_id', session('branch_id'))->where('stock_id', $request->stock_id)->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)->get()->groupBy('product_id');
        $operations = [];
        foreach ($grouped as $item) {
            $operations[] = [
                'product_name' => $item->first()->product->name,
                'wared_total' => $item->first()->product->convertQuantity($item->sum('wared')),
                'sader_total' => $item->first()->product->convertQuantity($item->sum('sader')),
                'balance' => $item->first()->product->convertQuantity($item->first()->product->stocks()->where('stock_id', $request->stock_id)->first()->pivot->quantity),
                'stock_name' => Stock::find($request->stock_id)->name,
            ];
        }
        return view('report.stock.show', compact('start', 'end', 'operations'));
    }

    public function burrenSaleIndex()
    {
        $stocks = Stock::get();
        $curenceies = Curencey::get();
        return view('report.burren.index_sale', compact('stocks', 'curenceies'));
    }

    public function burrenSaleShow(Request $request)
    {
        $stock_id = $request->stock_id ?? null;
        $stock = Stock::find($stock_id);
        $curencey = Curencey::find($request->curencey_id);

        $burrens = StockDetail::with('product', 'product.units')
            ->selectRaw(
                '
                product_id,
                IFNULL(SUM(sader), 0) as sader,
                IFNULL(SUM(CASE WHEN type != 7 THEN wared ELSE 0 END), 0) as wared,
                IFNULL(SUM(CASE WHEN type = 7 THEN wared ELSE 0 END), 0) as opening_balance,
                (
                    IFNULL(SUM(CASE WHEN type != 7 THEN wared ELSE 0 END), 0) +
                    IFNULL(SUM(CASE WHEN type = 7 THEN wared ELSE 0 END), 0) -
                    IFNULL(SUM(sader), 0)
                ) as available_balance
            ',
            )
            ->when($stock_id, fn($q) => $q->where('stock_id', $stock_id))
            ->where('branch_id', session('branch_id'))
            ->groupBy('product_id')
            ->get();

        return view('report.burren.show_sale', compact('burrens', 'stock', 'curencey'));
    }

    public function burrenPurchaseIndex()
    {
        $stocks = Stock::get();
        $curenceies = Curencey::get();
        return view('report.burren.index_purchase', compact('stocks', 'curenceies'));
    }

    public function burrenPurchaseShow(Request $request)
    {
        $stock_id = $request->stock_id ?? null;
        $stock = Stock::find($stock_id);
        $curencey = Curencey::find($request->curencey_id);

        $burrens = StockDetail::with('product', 'product.units')
            ->selectRaw(
                '
                product_id,
                IFNULL(SUM(sader), 0) as sader,
                IFNULL(SUM(CASE WHEN type != 7 THEN wared ELSE 0 END), 0) as wared,
                IFNULL(SUM(CASE WHEN type = 7 THEN wared ELSE 0 END), 0) as opening_balance,
                (
                    IFNULL(SUM(CASE WHEN type != 7 THEN wared ELSE 0 END), 0) +
                    IFNULL(SUM(CASE WHEN type = 7 THEN wared ELSE 0 END), 0) -
                    IFNULL(SUM(sader), 0)
                ) as available_balance
            ',
            )
            ->when($stock_id, fn($q) => $q->where('stock_id', $stock_id))
            ->where('branch_id', session('branch_id'))
            ->groupBy('product_id')
            ->get();

        return view('report.burren.show_purchase', compact('burrens', 'stock', 'curencey'));
    }

    public function burrenSaleSearch(Request $request)
    {
        $stock_id = $request->stock_id ?? null;
        $stock = Stock::find($stock_id);
        $curencey = Curencey::find($request->curencey_id);

        $burrens = StockDetail::with('product', 'product.units')
            ->selectRaw(
                '
                product_id,
                IFNULL(SUM(sader), 0) as sader,
                IFNULL(SUM(CASE WHEN type != 7 THEN wared ELSE 0 END), 0) as wared,
                IFNULL(SUM(CASE WHEN type = 7 THEN wared ELSE 0 END), 0) as opening_balance,
                (
                    IFNULL(SUM(CASE WHEN type != 7 THEN wared ELSE 0 END), 0) +
                    IFNULL(SUM(CASE WHEN type = 7 THEN wared ELSE 0 END), 0) -
                    IFNULL(SUM(sader), 0)
                ) as available_balance
            ',
            )
            ->when($stock_id, fn($q) => $q->where('stock_id', $stock_id))
            ->where('branch_id', session('branch_id'))
            ->groupBy('product_id')
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('product', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%');
                });
            })
            ->get();

        return view('report.burren.show_sale', compact('burrens', 'stock', 'curencey'));
    }

    public function burrenPurchaseSearch(Request $request)
    {
        $stock_id = $request->stock_id ?? null;
        $stock = Stock::find($stock_id);
        $curencey = Curencey::find($request->curencey_id);

        $burrens = StockDetail::with('product', 'product.units')
            ->selectRaw(
                '
                product_id,
                IFNULL(SUM(sader), 0) as sader,
                IFNULL(SUM(CASE WHEN type != 7 THEN wared ELSE 0 END), 0) as wared,
                IFNULL(SUM(CASE WHEN type = 7 THEN wared ELSE 0 END), 0) as opening_balance,
                (
                    IFNULL(SUM(CASE WHEN type != 7 THEN wared ELSE 0 END), 0) +
                    IFNULL(SUM(CASE WHEN type = 7 THEN wared ELSE 0 END), 0) -
                    IFNULL(SUM(sader), 0)
                ) as available_balance
            ',
            )
            ->when($stock_id, fn($q) => $q->where('stock_id', $stock_id))
            ->where('branch_id', session('branch_id'))
            ->groupBy('product_id')
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('product', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%');
                });
            })
            ->get();

        return view('report.burren.show_purchase', compact('burrens', 'stock', 'curencey'));
    }

    public function customer_accouht_index()
    {
        $customers = Tree4::whereTree3_code('1205')->get();
        $curenceies = Curencey::get();
        return view('report.customer.customer_index', compact('customers', 'curenceies'));
    }

    public function customer_accouht_show(Request $request)
    {
        $curencey = Curencey::find($request->curencey_id);

        $results = [];

        // ✅ مجاميع عامة
        $grand_total_madin = 0;
        $grand_total_dain = 0;

        // لو في عميل محدد
        if ($request->filled('customer_id')) {
            $treeIds = [$request->customer_id];
        } else {
            // لو ما في عميل → كل العملاء
            $treeIds = Tree4::where('tree3_code', 1205)->pluck('id');
        }

        foreach ($treeIds as $treeId) {
            $restrictions = Restriction::where('tree4_id', $treeId);

            $total_madin = $restrictions->sum('madin');
            $total_dain = $restrictions->sum('dain');

            // تجاهل الرصيد الصفري
            if ($total_madin == $total_dain) {
                continue;
            }

            $tree = Tree4::find($treeId);
            if (!$tree) {
                continue;
            }

            $balance = $total_dain - $total_madin;

            // ✅ تجميع الإجمالي
            $grand_total_madin += $balance > 0 ? $balance : 0;
            $grand_total_dain += $balance < 0 ? abs($balance) : 0;

            $results[] = [
                'customer_name' => $tree->tree4_name,
                'customer_phone' => $tree->phone,
                'customer_address' => $tree->address,
                'madin' => $total_madin,
                'dain' => $total_dain,
                'to' => $balance > 0 ? abs($balance) : null,
                'from' => $balance < 0 ? abs($balance) : null,
            ];
        }

        return view('report.customer.customer_show', compact('results', 'curencey', 'grand_total_madin', 'grand_total_dain'));
    }
}
