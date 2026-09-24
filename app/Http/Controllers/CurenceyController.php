<?php

namespace App\Http\Controllers;

use App\Models\Curencey;
use Illuminate\View\View;
use Illuminate\Http\Request;

class CurenceyController extends Controller
{
    public function index(): View
    {
        $curenceies = Curencey::whereNot('id', 1)->get();
        return view('curencey.index', compact('curenceies'));
    }

    public function store(Request $request)
    {
        Curencey::create([
            'name' => $request->name,
            'amount' => $request->amount,
            'symbol' => $request->symbol,
        ]);

        return back()->with('success_created', true);
    }

    public function update(Request $request, Curencey $curencey)
    {
        $curencey->update([
            'name' => $request->name,
            'amount' => $request->amount,
            'symbol' => $request->symbol,
        ]);
        return back()->with('success_created', true);
    }

    public function destroy(Curencey $curencey)
    {
        $curencey->delete();
        return back()->with('success_deleted', 'ok');
    }

    public function checkName(Request $request)
    {
        $id = $request->input('id');
        $exists = Curencey::where('name', $request->name)->where('id', '!=', $id)->exists(); // تحقق من وجود الباركود في قاعدة البيانات
        return response()->json(['isUnique' => !$exists]);
    }
}
