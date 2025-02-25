<?php

namespace App\Http\Controllers;

use App\Http\Requests\Daily\StoreDailyRequest;
use App\Models\Tree4;
use App\Models\Operation;
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
        $operations = Operation::with('restrictions')->whereIn('type',[5,6])->orderByDesc('id')->get();
        return view('daily.index', compact('operations'));
    }
    public function create(): View
    {
        $maxInvoiceNumber = Restriction::max('constant_number');
        $constant_number = $maxInvoiceNumber + 1;
        return view('daily.create', compact('constant_number'));
    }

    public function store(StoreDailyRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $madin = Tree4::find($request->madin);
            $dain = Tree4::find($request->dain);
            $operation = Operation::create([
                'madin_id' => $request->madin,
                'dain_id' => $request->dain,
                'madin' => $madin->tree4_code,
                'dain' => $dain->tree4_code,
                'price' => $request->price,
                'constant_number' => $request->constant_number,
                'node' => $request->node,
                'type' => 5,
                'user_id' => Auth::id(),
            ]);

            Restriction::create([
                'tree4_id' => $madin->id,
                'tree4_code' => $madin->tree4_code,
                'madin' => $request->price,
                'dain' => 0,
                'date' => $request->date,
                'node' => $request->node,
                'constant_number' => $request->constant_number,
                'type' => 5,
                'user_id' => Auth::id(),
                'operation_id' => $operation->id,
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
                'user_id' => Auth::id(),
                'operation_id' => $operation->id,
            ]);

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
        if (!$operation){
            return back()->withErrors('حدث خطا ما يرجى المحاولة مرة اخرى');
        }
        $operation->delete();
        return back()->with('success_deleted', 'successfully deleted');
         
    }
}
