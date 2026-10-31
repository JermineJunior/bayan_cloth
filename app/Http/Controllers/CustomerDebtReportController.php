<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tree4;
use App\Models\Curencey;
use App\Models\Restriction;
use App\Models\SaleInvoice;
use Illuminate\View\View;
use Illuminate\Http\Request;

class CustomerDebtReportController extends Controller
{
    public function index(): View
    {
        $customers = Tree4::where('tree3_code', 1205)->orderBy('tree4_name')->get();
        $curenceies = Curencey::get();

        return view('report.customer_debts.index', compact('customers', 'curenceies'));
    }

    public function show(Request $request): View
    {
        $asOf = $request->filled('as_of')
            ? Carbon::parse($request->as_of)->toDateString()
            : today()->toDateString();

        $customerId = $request->filled('customer_id') ? (int) $request->customer_id : null;
        $branch = $request->input('branch', 'current');
        $showDetails = $request->boolean('show_invoices', true);

        $curencey = Curencey::find($request->curencey_id)
            ?? Curencey::where('is_base', true)->first()
            ?? Curencey::first();

        $branchScope = fn($query) => $branch === 'all'
            ? $query
            : $query->where('branch_id', session('branch_id'));

        $balanceQuery = Restriction::query()->whereDate('date', '<=', $asOf);

        if ($customerId) {
            $balanceQuery->where('tree4_id', $customerId);
        } else {
            $balanceQuery->where('tree4_code', 'like', '1205%');
        }

        $branchScope($balanceQuery);

        $balances = $balanceQuery
            ->groupBy('tree4_id')
            ->selectRaw('tree4_id, SUM(madin) - SUM(dain) AS balance')
            ->get()
            ->mapWithKeys(fn($row) => [(int) $row->tree4_id => (float) $row->balance]);

        $invoiceQuery = SaleInvoice::with('branch')
            ->where('deferend_amount', '>', 0)
            ->whereDate('date', '<=', $asOf)
            ->when($customerId, fn($query) => $query->where('customer_id', $customerId));

        $branchScope($invoiceQuery);

        $unpaidInvoices = $invoiceQuery
            ->orderBy('date')
            ->orderBy('id')
            ->get()
            ->groupBy(fn($invoice) => (int) $invoice->customer_id);

        $customerIds = $balances->keys()
            ->merge($unpaidInvoices->keys())
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        $customers = Tree4::whereIn('id', $customerIds)->get()->keyBy('id');

        $rows = $customerIds
            ->map(function ($id) use ($balances, $unpaidInvoices, $customers) {
                $customer = $customers->get($id);

                if (!$customer) {
                    return null;
                }

                $balance = (float) $balances->get($id, 0);
                $invoices = $unpaidInvoices->get($id, collect());
                $invoicesRemaining = (float) $invoices->sum(fn($invoice) => (float) $invoice->deferend_amount);

                if (abs($balance) <= 0.005 && $invoicesRemaining <= 0.005) {
                    return null;
                }

                return [
                    'customer' => $customer,
                    'balance' => $balance,
                    'invoices' => $invoices,
                    'invoices_remaining' => $invoicesRemaining,
                    'unmatched' => $invoicesRemaining - $balance,
                ];
            })
            ->filter()
            ->sortByDesc('balance')
            ->values();

        $totals = [
            'balance' => (float) $rows->sum(fn($row) => $row['balance']),
            'debit' => (float) $rows->filter(fn($row) => $row['balance'] > 0.005)->sum(fn($row) => $row['balance']),
            'credit' => (float) $rows->filter(fn($row) => $row['balance'] < -0.005)->sum(fn($row) => abs($row['balance'])),
            'invoices_remaining' => (float) $rows->sum(fn($row) => $row['invoices_remaining']),
            'unmatched' => (float) $rows->sum(fn($row) => $row['unmatched']),
        ];

        $customer = $customerId ? Tree4::find($customerId) : null;

        return view('report.customer_debts.show', compact(
            'rows',
            'totals',
            'curencey',
            'asOf',
            'branch',
            'customer',
            'showDetails',
        ));
    }
}
