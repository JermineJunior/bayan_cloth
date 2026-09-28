<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\View\View;

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
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $info = Setting::first();
        $info->name = $request->name;
        $info->email = $request->email;
        $info->location = $request->location;
        $info->phone = $request->phone;

        if ($request->hasFile('logo')) {
            $this->deleteOldLogo($info->logo);
            $info->logo = $this->storeLogo($request->file('logo'));
        }

        $info->save();

        return back()->with('success_created', 'تم التحديث بنجاح');
    }

    private function storeLogo(UploadedFile $file): string
    {
        $fileName = 'logo_'.time().'_'.Str::random(10).'.'.$file->extension();

        $file->move(public_path('image/logo'), $fileName);

        return $fileName;
    }

    // نحذف المرفوع سابقاً فقط، لأن هناك ملفات ثابتة مثل bayan.jpg
    // تشير إليها صفحات الطباعة و PDF بمسار مباشر
    private function deleteOldLogo(?string $fileName): void
    {
        if ($fileName && str_starts_with($fileName, 'logo_')) {
            $path = public_path('image/logo/'.$fileName);

            if (is_file($path)) {
                unlink($path);
            }
        }
    }
}
