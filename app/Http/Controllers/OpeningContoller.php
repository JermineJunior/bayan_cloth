<?php

namespace App\Http\Controllers;

use App\Models\Tree4;
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
        $operations = Restriction::with('tree4')->whereNot('tree4_id', 14)->where('branch_id', session('branch_id'))->whereType(7)->get();
        return view('opening.index', compact('operations'));
    }

    public function create(): View
    {
        $constant_number = Restriction::max('constant_number') + 1;
        $trees = Tree4::get();
        return view('opening.create', compact('constant_number', 'trees'));
    }

    public function store(StoreOpeningRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            if ($request->accountType == 2) {
                $madin = Tree4::find($request->account);
                Restriction::create([
                    'tree4_id' => $madin->id,
                    'tree4_code' => $madin->tree4_code,
                    'madin' => $request->price,
                    'dain' => 0,
                    'date' => $request->date,
                    'node' => $request->note,
                    'constant_number' => $request->constant_number,
                    'type' => 7,
                    'branch_id' => session('branch_id'),
                    'user_id' => Auth::id(),
                ]);

                Restriction::create([
                    'tree4_id' => 14,
                    'tree4_code' => 310100001,
                    'madin' => 0,
                    'dain' => $request->price,
                    'date' => $request->date,
                    'node' => $request->note,
                    'constant_number' => $request->constant_number,
                    'type' => 7,
                    'branch_id' => session('branch_id'),
                    'user_id' => Auth::id(),
                ]);
            }elseif ($request->accountType == 1) {
                $dain = Tree4::find($request->account);
                Restriction::create([
                    'tree4_id' => $dain->id,
                    'tree4_code' => $dain->tree4_code,
                    'madin' => 0,
                    'dain' => $request->price,
                    'date' => $request->date,
                    'node' => $request->note,
                    'constant_number' => $request->constant_number,
                    'type' => 7,
                    'branch_id' => session('branch_id'),
                    'user_id' => Auth::id(),
                ]);

                Restriction::create([
                    'tree4_id' => 14,
                    'tree4_code' => 31010001,
                    'madin' => $request->price,
                    'dain' => 0,
                    'date' => $request->date,
                    'node' => $request->note,
                    'constant_number' => $request->constant_number,
                    'type' => 7,
                    'branch_id' => session('branch_id'),
                    'user_id' => Auth::id(),
                ]);
            }

            DB::commit();
            return redirect()->route('opening.index')->with('success_created', 'successfully created');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('opening.index')->withErrors($e->getMessage());
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        $operation = Restriction::find($request->id);
        if (!$operation) {
            return back()->withErrors('حدث خطا ما يرجى المحاولة مرة اخرى');
        }
        Restriction::where('constant_number', $operation->constant_number)->delete();
        return back()->with('success_deleted', 'successfully deleted');
    }
}
