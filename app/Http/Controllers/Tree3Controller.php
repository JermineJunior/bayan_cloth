<?php

namespace App\Http\Controllers;

use App\Models\Tree2;
use App\Models\Tree3;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class Tree3Controller extends Controller
{
    public function index(): View
    {
        $tree2s = Tree2::get();
        $tree3s = Tree3::orderBy('tree3_code')->get();
        return view('tree3.index', compact('tree2s', 'tree3s'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tree2_id' => 'required|exists:tree2s,id',
            'tree3_name' => 'required|string|max:255|unique:tree3s,tree3_name',
        ]);

        $tree2 = Tree2::with('tree3')->whereId($request->tree2_id)->orderByDesc('id')->first();
        Tree3::create([
            'tree2_id' => $tree2->id,
            'tree3_name' => $request->tree3_name,
            'tree3_code' => $tree2->tree3()->count() > 0 ? $tree2->tree3()->orderByDesc('id')->first()->tree3_code + 1 : $tree2->tree2_code * 100 + 1,
            'tree2_code' => $tree2->tree2_code,
        ]);

        return back()->with('success_created', 'success created');
    }

    public function update(Tree3 $tree3, Request $request): RedirectResponse
    {
        $request->validate([
            'tree3_name' => 'required|string|max:255|unique:tree3s,tree3_name,' . $tree3->id,
        ]);

        $tree3->update([
            'tree3_name' => $request->get('tree3_name'),
        ]);

        return back()->with('success_updated', 'success updated');
    }
}
