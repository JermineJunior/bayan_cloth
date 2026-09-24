<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Unit\StoreUnitRequest;
use App\Http\Requests\Unit\UpdateUnitRequest;

class UnitController extends Controller
{
    public function index(): View
    {
        $units = Unit::get();
        return view('unit.index', compact('units'));
    }

    public function store(StoreUnitRequest $request): RedirectResponse
    {
        Unit::create(attributes: ['name' => $request->name]);
        return back()->with('success_created', 'تمت اضافة البيانات بنجاح');
    }

    public function update(UpdateUnitRequest $request, Unit $unit): RedirectResponse
    {
        $unit->update(['name' => $request->name, 'branch_id' => session('branch_id')]);
        return back()->with('success_updated', 'تم تحديث البيانات بنجاج');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        if ($unit->products()->count() > 0) {
            return back()->withErrors('لا يمكن حذف هذه الوحدة لانها مرتبطة ببعض المنتجات');
        }
        $unit->delete();
        return back()->with('success_deleted', 'تم الحذف بنجاح ');
    }

    public function checkName(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $exists = Unit::where('name', $request->name)->where('id', '!=', $id)->exists(); // تحقق من وجود الباركود في قاعدة البيانات
        return response()->json(['isUnique' => !$exists]);
    }
}
