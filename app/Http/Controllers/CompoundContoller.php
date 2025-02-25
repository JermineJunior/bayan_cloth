<?php

namespace App\Http\Controllers;

use App\Models\Tree4;
use App\Models\Operation;
use Illuminate\View\View;
use App\Models\Restriction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Compound\StoreCompoundRequest;

class CompoundContoller extends Controller
{
    public function index(): View
    {
        return view('compound.index');
    }

    public function create(): View
    {
        $constant_number = Restriction::max('constant_number') + 1;
        return view('compound.create', compact('constant_number'));
    }

    public function store(StoreCompoundRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            foreach ($request->madin as $index => $madin) {
                $madin = Tree4::find($madin);
                $dain = Tree4::find($request->dain);
                $operation = Operation::create([
                    'madin_id' => $madin->id,
                    'dain_id' => $request->dain,
                    'madin' => $madin->tree4_code,
                    'dain' => $dain->tree4_code,
                    'price' => $request->price_madin[$index],
                    'constant_number' => $request->constant_number,
                    'node' => $request->note,
                    'type' => 6,
                    'user_id' => Auth::id(),
                ]);

                Restriction::create([
                    'tree4_id' => $madin->id,
                    'tree4_code' => $madin->tree4_code,
                    'madin' => $request->price_madin[$index],
                    'dain' => 0,
                    'date' => $request->date,
                    'node' => $request->note,
                    'constant_number' => $request->constant_number,
                    'type' => 6,
                    'user_id' => Auth::id(),
                    'operation_id' => $operation->id,
                ]);

                Restriction::create([
                    'tree4_id' => $dain->id,
                    'tree4_code' => $dain->tree4_code,
                    'madin' => 0,
                    'dain' => $request->price_dain,
                    'date' => $request->date,
                    'node' => $request->note,
                    'constant_number' => $request->constant_number,
                    'type' => 6,
                    'user_id' => Auth::id(),
                    'operation_id' => $operation->id,
                ]);
            }
            DB::commit();
            return redirect()->route('daily.index')->with('success_created', 'successfully created');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('daily.index')->withErrors($e->getMessage());
        }
    }

    public function destroy(Request $request):RedirectResponse 
    {
        $operation = Operation::find($request->id);
        $operation = $operation->where('constant_number', $operation->constant_number)->delete();
        return back()->with('success_deleted', 'successfully deleted');
    }
}
