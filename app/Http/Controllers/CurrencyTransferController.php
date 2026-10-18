<?php

namespace App\Http\Controllers;

use App\Models\Curencey;
use App\Models\CurrencyTransfer;
use App\Models\Restriction;
use App\Models\Tree4;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CurrencyTransferController extends Controller
{
    public function index(): View
    {
        $transfers = CurrencyTransfer::where('branch_id', session('branch_id'))
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('restrictions')
                    ->whereColumn('restrictions.constant_number', 'currency_transfers.constant_number')
                    ->where('restrictions.branch_id', session('branch_id'));
            })
            ->orderByDesc('id')
            ->get();
        return view('currency-transfer.index', compact('transfers'));
    }

    public function create(): View
    {
        $accounts = Tree4::whereIn('tree3_code', [1202, 1203])->get();
        $currencies = Curencey::get();
        return view('currency-transfer.create', compact('accounts', 'currencies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['from_amount' => str_replace(',', '', $request->from_amount)]);

        $data = $request->validate([
            'date' => 'required',
            'from_account_id' => 'required|exists:tree4s,id|different:to_account_id',
            'to_account_id' => 'required|exists:tree4s,id',
            'from_currency_id' => 'required|exists:curenceys,id',
            'to_currency_id' => 'required|exists:curenceys,id',
            'from_amount' => 'required|numeric|gt:0',
            'note' => 'nullable|max:255',
        ]);

        $fromCurrency = Curencey::find($data['from_currency_id']);
        $toCurrency = Curencey::find($data['to_currency_id']);

        if ($fromCurrency->amount <= 0 || $toCurrency->amount <= 0) {
            return back()->withErrors(['from_currency_id' => 'سعر صرف العملة يجب أن يكون أكبر من صفر'])->withInput();
        }

        $fromAmount = (float) str_replace(',', '', $data['from_amount']);
        $baseAmount = round($fromAmount * $fromCurrency->amount, 2);
        $toAmount = round($baseAmount / $toCurrency->amount, 2);

        try {
            DB::beginTransaction();

            $constant_number = Restriction::where('branch_id', session('branch_id'))->max('constant_number') + 1;

            $node = "تحويل عملة: {$fromAmount} {$fromCurrency->symbol} إلى {$toAmount} {$toCurrency->symbol}"
                . ($data['note'] ?? '' ? " - {$data['note']}" : '');

            $fromAccount = Tree4::find($data['from_account_id']);
            $toAccount = Tree4::find($data['to_account_id']);

            $transfer = CurrencyTransfer::create([
                'date' => $data['date'],
                'from_account_id' => $fromAccount->id,
                'to_account_id' => $toAccount->id,
                'from_currency_id' => $fromCurrency->id,
                'from_amount' => $fromAmount,
                'to_currency_id' => $toCurrency->id,
                'to_amount' => $toAmount,
                'base_amount' => $baseAmount,
                'constant_number' => $constant_number,
                'note' => $data['note'] ?? null,
                'branch_id' => session('branch_id'),
                'user_id' => Auth::id(),
            ]);

            Restriction::create([
                'tree4_id' => $toAccount->id,
                'tree4_code' => $toAccount->tree4_code,
                'madin' => $baseAmount,
                'dain' => 0,
                'date' => $data['date'],
                'node' => $node,
                'constant_number' => $constant_number,
                'type' => 6,
                'branch_id' => session('branch_id'),
                'user_id' => Auth::id(),
            ]);

            Restriction::create([
                'tree4_id' => $fromAccount->id,
                'tree4_code' => $fromAccount->tree4_code,
                'madin' => 0,
                'dain' => $baseAmount,
                'date' => $data['date'],
                'node' => $node,
                'constant_number' => $constant_number,
                'type' => 6,
                'branch_id' => session('branch_id'),
                'user_id' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('currency-transfer.show', $transfer->id)->with('success_created', 'successfully created');
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->route('currency-transfer.create')->withErrors($exception->getMessage())->withInput();
        }
    }

    public function show($id): View
    {
        $transfer = CurrencyTransfer::with(['fromAccount', 'toAccount', 'fromCurrency', 'toCurrency'])->findOrFail($id);
        return view('currency-transfer.show', compact('transfer'));
    }
}
