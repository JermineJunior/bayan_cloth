<?php

namespace App\Http\Controllers;

use App\Models\Tree4;
use Illuminate\View\View;
use App\Models\Restriction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Revenue\StoreRevenueRequest;
use App\Http\Requests\Revenue\UpdateRevenueRequest;
use App\Models\Revenue;

class RevenueController extends Controller
{
    public function index(): View
    {
        $revenues = Revenue::where('branch_id', session('branch_id'))->orderByDesc('id')->get();
        $banks = Tree4::where('tree3_code', 1203)->get();
        $safes = Tree4::where('tree3_code', 1202)->get();
        $dains = Tree4::get();
        return view('revenue.index', compact('revenues', 'dains', 'banks', 'safes'));
    }

    public function store(StoreRevenueRequest $request): View|RedirectResponse
    {
        try {
            DB::beginTransaction();
            $sum = 0;
            $revenue = Revenue::create([
                'dain_id' => $request->dain,
                'bank_id' => $request->madin_id[1] ?? null,
                'cach_id' => $request->madin_id[0] ?? null,
                'cach_name' => $request->madin_name[0] ?? null,
                'bank_name' => $request->madin_name[1] ?? null,
                'branch_id' => session('branch_id'),
                'note' => $request->note,
            ]);

            $last_constant_number = Restriction::where('branch_id', session('branch_id'))->max('constant_number');
            $constant_number = $last_constant_number ? $last_constant_number + 1 : 1;

            foreach ($request->madin_id as $index => $madinId) {
                if (!$madinId || !isset($request->madin_name[$index]) || !$request->madin_name[$index]) {
                    continue;
                }
                $madin = Tree4::findOrFail($madinId);
                $this->restriction($madin->id, $madin->tree4_code, $request->madin_name[$index], 0, $constant_number, $revenue, $request->note);
                $sum += $request->madin_name[$index];
            }
            $dain = Tree4::findOrFail($request->dain);
            $this->restriction($dain->id, $dain->tree4_code, 0, $sum, $constant_number, $revenue, $request->note);
            DB::commit();
            // return view('revenue.print', ['revenue' => $operation]);
            return redirect()->route('revenue.show', $revenue->id);
        } catch (\Exception $exception) {
            DB::rollback();
            return back()->withErrors(['error' => $exception->getMessage()]);
        }
    }

    public function show(Revenue $revenue): View
    {
        return view('revenue.print', compact('revenue'));
    }

    public function update(UpdateRevenueRequest $request, Revenue $revenue): RedirectResponse|View
    {
        try {
            DB::beginTransaction();
            $revenue->delete();
            $sum = 0;
            $revenue = Revenue::create([
                'dain_id' => $request->dain,
                'bank_id' => $request->madin_id[1] ?? null,
                'cach_id' => $request->madin_id[0] ?? null,
                'cach_name' => $request->madin_name[0] ?? null,
                'bank_name' => $request->madin_name[1] ?? null,
                'branch_id' => session('branch_id'),
                'note' => $request->note,
            ]);

            $last_constant_number = Restriction::where('branch_id', session('branch_id'))->max('constant_number');
            $constant_number = $last_constant_number ? $last_constant_number + 1 : 1;

            foreach ($request->madin_id as $index => $madinId) {
                if (!$madinId || !isset($request->madin_name[$index]) || !$request->madin_name[$index]) {
                    continue;
                }
                $madin = Tree4::findOrFail($madinId);
                $this->restriction($madin->id, $madin->tree4_code, $request->madin_name[$index], 0, $constant_number, $revenue, $request->note);
                $sum += $request->madin_name[$index];
            }
            $dain = Tree4::findOrFail($request->dain);
            $this->restriction($dain->id, $dain->tree4_code, 0, $sum, $constant_number, $revenue, $request->note);
            DB::commit();
            return redirect()->route('revenue.show', $revenue->id);
        } catch (\Exception $exception) {
            DB::rollback();
            return back()->withErrors(['error' => $exception->getMessage()]);
        }
    }

    public function destroy(Revenue $revenue)
    {
        try {
            DB::beginTransaction();
            $revenue->delete();
            DB::commit();
            return back()->with('success_deleted', 'successfully deleted');
        } catch (\Exception $exception) {
            DB::rollback();
            return back()->withErrors(['error' => $exception->getMessage()]);
        }
    }

    private function restriction($tree_id, $tree4_code, $madin, $dain, $constant_number, $revenue, $node): Restriction
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
                'revenue_id' => $revenue->id,
                'type' => 8,
                'branch_id' => session('branch_id'),
                'user_id' => Auth::id(),
            ],
        );
    }
}
