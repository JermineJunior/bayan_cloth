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
use App\Http\Requests\Opening\StoreOpeningRequest;

class OpeningContoller extends Controller
{
    public function index(): View
    {
        $operations = Operation::with('restrictions')->whereType(7)->get();
        return view('opening.index', compact('operations'));
    }

    public function create(): View
    {
        $constant_number = Restriction::max('constant_number') + 1;
        return view('opening.create', compact('constant_number'));
    }

    public function store(StoreOpeningRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            if ($request->accountType == 1) {
                $dain = Tree4::find($request->accountType);
            } elseif ($request->accountType == 2) {
                $madin = Tree4::find($request->accountType);
            }
            $operation = Operation::create([
                'madin_id' => $madin->id ?? null,
                'dain_id' => $dain->id ?? null,
                'madin' => $madin->tree4_code ?? null,
                'dain' => $dain->tree4_code ?? null,
                'price' => $request->price,
                'constant_number' => $request->constant_number,
                'node' => $request->note,
                'type' => 7,
                'user_id' => Auth::id(),
            ]);

            if ($request->accountType == 2) {
                Restriction::create([
                    'tree4_id' => $madin->id,
                    'tree4_code' => $madin->tree4_code,
                    'madin' => $request->price,
                    'dain' => 0,
                    'date' => $request->date,
                    'node' => $request->note,
                    'constant_number' => $request->constant_number,
                    'type' => 7,
                    'user_id' => Auth::id(),
                    'operation_id' => $operation->id,
                ]);
            }

            if ($request->accountType == 1) {
                Restriction::create([
                    'tree4_id' => $dain->id,
                    'tree4_code' => $dain->tree4_code,
                    'madin' => 0,
                    'dain' => $request->price,
                    'date' => $request->date,
                    'node' => $request->note,
                    'constant_number' => $request->constant_number,
                    'type' => 7,
                    'user_id' => Auth::id(),
                    'operation_id' => $operation->id,
                ]);
            }

            DB::commit();
            return redirect()->route('opening.index')->with('success_created', 'successfully created');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('opening.index')->withErrors($e->getMessage());
        }
    }
}
