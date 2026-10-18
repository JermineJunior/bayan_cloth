<?php

namespace App\Http\Controllers;

use App\Models\Tree4;
use Illuminate\View\View;
use App\Models\Restriction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Compound\StoreCompoundRequest;

class CompoundContoller extends Controller
{
    public function create(): View
    {
        $constant_number = Restriction::where('branch_id', session('branch_id'))->max('constant_number') + 1;
        $trees = Tree4::get();
        return view('compound.create', compact('constant_number', 'trees'));
    }

    public function store(StoreCompoundRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            foreach ($request->madin as $index => $madin) {
                $madin = Tree4::find($madin);

                Restriction::create([
                    'tree4_id' => $madin->id,
                    'tree4_code' => $madin->tree4_code,
                    'madin' => $request->price_madin[$index],
                    'dain' => 0,
                    'date' => $request->date,
                    'node' => $request->note,
                    'line_note' => $request->note_madin[$index] ?? null,
                    'constant_number' => $request->constant_number,
                    'type' => 6,
                    'branch_id' => session('branch_id'),
                    'user_id' => Auth::id(),
                ]);
            }

            foreach ($request->dain as $index => $dain) {
                $dain = Tree4::find($dain);

                Restriction::create([
                    'tree4_id' => $dain->id,
                    'tree4_code' => $dain->tree4_code,
                    'madin' => 0,
                    'dain' => $request->price_dain[$index],
                    'date' => $request->date,
                    'node' => $request->note,
                    'line_note' => $request->note_dain[$index] ?? null,
                    'constant_number' => $request->constant_number,
                    'type' => 6,
                    'branch_id' => session('branch_id'),
                    'user_id' => Auth::id(),
                ]);
            }
            DB::commit();
            return redirect()->route('daily.show', $request->constant_number)->with('success_created', 'successfully created');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('daily.index')->withErrors($e->getMessage());
        }
    }

    public function edit($id)
    {
        $operation = Restriction::find($id);
        $constant_number = $operation->constant_number;
        $operations = Restriction::where('constant_number', $constant_number)->where('branch_id', session('branch_id'))->whereYear('created_at', $operation->created_at->year)->get();
        $trees = Tree4::get();
        return view('compound.edit', compact('operations', 'constant_number', 'trees'));
    }

    public function update(StoreCompoundRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $operation = Restriction::find($id);
            $constant_number = $operation->constant_number;
            Restriction::where(['constant_number' => $constant_number, 'branch_id' => session('branch_id'), 'type' => 6])
                ->whereYear('created_at', $operation->created_at->year)
                ->delete();
            foreach ($request->madin as $index => $madin) {
                $madin = Tree4::find($madin);

                Restriction::create([
                    'tree4_id' => $madin->id,
                    'tree4_code' => $madin->tree4_code,
                    'madin' => $request->price_madin[$index],
                    'dain' => 0,
                    'date' => $request->date,
                    'node' => $request->note,
                    'line_note' => $request->note_madin[$index] ?? null,
                    'constant_number' => $request->constant_number,
                    'type' => 6,
                    'branch_id' => session('branch_id'),
                    'user_id' => Auth::id(),
                ]);
            }

            foreach ($request->dain as $index => $dain) {
                $dain = Tree4::find($dain);

                Restriction::create([
                    'tree4_id' => $dain->id,
                    'tree4_code' => $dain->tree4_code,
                    'madin' => 0,
                    'dain' => $request->price_dain[$index],
                    'date' => $request->date,
                    'node' => $request->note,
                    'line_note' => $request->note_dain[$index] ?? null,
                    'constant_number' => $request->constant_number,
                    'type' => 6,
                    'branch_id' => session('branch_id'),
                    'user_id' => Auth::id(),
                ]);
            }
            DB::commit();
            return redirect()->route('daily.show', $request->constant_number)->with('success_created', 'successfully created');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('daily.index')->withErrors($e->getMessage());
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        $operation = Restriction::find($request->id);
        $operation = $operation->where('constant_number', $operation->constant_number)->where('branch_id', session('branch_id'))->delete();
        return back()->with('success_deleted', 'successfully deleted');
    }
}
