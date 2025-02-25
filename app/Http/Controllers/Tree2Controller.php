<?php

namespace App\Http\Controllers;

use App\Models\Tree1;
use App\Models\Tree2;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class Tree2Controller extends Controller
{
    public function index():View
    {
        $tree1s = Tree1::get();
        $tree2s = Tree2::orderBy('tree2_code')->get();
        return view('tree2.index',compact('tree1s', 'tree2s'));
    }

    public function store(Request $request):RedirectResponse
    {
        $request->validate([
            'tree1_id' =>'required|exists:tree1s,id',
            'tree2_name' =>'required|string|max:255|unique:tree2s,tree2_name',
        ]);

        $tree1 = Tree1::with('tree2')->whereId($request->tree1_id)->orderByDesc('id')->first();
                Tree2::create([
                    'tree1_id' => $tree1->id,
                    'tree2_name' => $request->tree2_name,
                    'tree2_code' => $tree1->tree2()->count() > 0 ? $tree1->tree2()->orderByDesc('id')->first()->tree2_code + 1 : ( $tree1->tree1_code * 10 ) + 1,
                    'tree1_code' => $tree1->tree1_code,
                ]);

                return back()->with('success_created','success created');
    }

    public function update(Tree2 $tree2, Request $request):RedirectResponse
    {
        $request->validate([
            'tree2_name' => 'required|string|max:255|unique:tree2s,tree2_name,'.$tree2->id,
        ]);

        $tree2->update([
            'tree2_name' => $request->get('tree2_name')
        ]);

        return back()->with('success_updated','success updated');
    }
}
