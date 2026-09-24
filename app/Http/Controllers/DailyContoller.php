<?php

namespace App\Http\Controllers;

use App\Http\Requests\Daily\StoreDailyRequest;
use App\Models\Tree4;
use Illuminate\View\View;
use App\Models\Restriction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class DailyContoller extends Controller
{
    public function index(): View
    {
        // فقط السطور المدين، لتجنب التكرار
        $restrictions = Restriction::with(['tree4', 'user'])
            ->where('branch_id', session('branch_id'))
            ->whereIn('type', [5, 6])
            ->orderByDesc('id')
            ->get();

        // نجمع القيود بحسب رقم المعاملة
        $grouped = $restrictions->where('branch_id',session('branch_id'))->groupBy('constant_number');

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
            ->values(); // ترتيب النتائج

        return view('daily.index', ['operations' => $results]);
    }

    public function create(): View
    {
        $maxInvoiceNumber = Restriction::where('branch_id', session('branch_id'))->max('constant_number');
        $constant_number = $maxInvoiceNumber + 1;
        $trees = Tree4::get();
        return view('daily.create', compact('constant_number', 'trees'));
    }

    public function store(StoreDailyRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $madin = Tree4::find($request->madin);
            $dain = Tree4::find($request->dain);

            Restriction::create([
                'tree4_id' => $madin->id,
                'tree4_code' => $madin->tree4_code,
                'madin' => $request->price,
                'dain' => 0,
                'date' => $request->date,
                'node' => $request->node,
                'constant_number' => $request->constant_number,
                'type' => 5,
                'branch_id' => session('branch_id'),
                'user_id' => Auth::id(),
            ]);

            Restriction::create([
                'tree4_id' => $dain->id,
                'tree4_code' => $dain->tree4_code,
                'madin' => 0,
                'dain' => $request->price,
                'date' => $request->date,
                'node' => $request->node,
                'constant_number' => $request->constant_number,
                'type' => 5,
                'branch_id' => session('branch_id'),
                'user_id' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('daily.show', $request->constant_number)->with('success_created', 'successfully created');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('daily.index')->withErrors($e->getMessage());
        }
    }

    public function show($constant_number)
    {
        $restrictions = Restriction::with('tree4')->where('branch_id', session('branch_id'))->where('constant_number', $constant_number)->get();
        return view('daily.print', compact('restrictions'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $operation = Restriction::find($request->id);
        if (!$operation) {
            return back()->withErrors('حدث خطا ما يرجى المحاولة مرة اخرى');
        }
        Restriction::where('constant_number', $operation->constant_number)->where('branch_id',session('branch_id'))->delete();
        return back()->with('success_deleted', 'successfully deleted');
    }
}
