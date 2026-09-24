<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index(): View
    {
        $info = Setting::first();
        return view('setting.index', compact('info'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string',
        ]);

        $info = Setting::first();
        $info->name = $request->name;
        $info->email = $request->email;
        $info->location = $request->location;
        $info->phone = $request->phone;
        $info->save();
        return back()->with('success_created', 'تم التحديث بنجاج');
    }
}
