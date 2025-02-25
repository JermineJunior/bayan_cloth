<?php

namespace App\Http\Controllers;

use App\Models\Tree1;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class Tree1Controller extends Controller
{
    public function index(): View
    {
        $tree1s = Tree1::get();
        return view('tree1.index', compact('tree1s'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tree1_name' => 'required|string|max:255|unique:tree1s,tree1_name',
        ]);

        $tree1_code = Tree1::max('tree1_code') + 1;

        Tree1::create([
            'tree1_code' => $tree1_code,
            'tree1_name' => $request->tree1_name,
        ]);
        return back()->with('success_created', 'Tree 1 created successfully');
    }

    public function update(Tree1 $tree1, Request $request):RedirectResponse
    {
        $request->validate([
            'tree1_name' =>'required|string|max:255|unique:tree1s,tree1_name,'. $tree1->id,
        ]);

        $tree1->tree1_name = $request->tree1_name;
        $tree1->save();

        return back()->with('success_updated', 'Tree 1 updated successfully');
    }
}
