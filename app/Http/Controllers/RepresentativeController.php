<?php

namespace App\Http\Controllers;

use App\Models\Tree4;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Representative;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Representative\StoreRepresentativeRequest;
use App\Http\Requests\Representative\UpdateRepresentativeRequest;

class RepresentativeController extends Controller
{
    public function index(): View
    {
        $representatives = Tree4::where('tree3_code', 1206)->orderByDesc('id')->get();
        return view('representative.index', compact('representatives'));
    }

    public function store(StoreRepresentativeRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $tree4 = Tree4::whereTree3_code(1206)->orderBy('id', 'DESC')->first();
            $tree4_code = $tree4 ? $tree4->tree4_code + 1 : '120600001';

            Tree4::create(
                attributes: [
                    'tree4_name' => $request->name,
                    'tree4_code' => $tree4_code,
                    'tree3_id' => 13,
                    'tree3_code' => 1206,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'rep_percentage' => $request->rep_percentage ?? 0,
                    'user_id' => Auth::id(),
                ],
            );

            DB::commit();
            return back()->with('success_created', 'تمت اضافة البيانات بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ اثناء العملية');
        }
    }

    public function update(UpdateRepresentativeRequest $request, Tree4 $representative): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $representative->update([
                'tree4_name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'rep_percentage' => $request->rep_percentage ?? 0,
            ]);

            Db::commit();

            return back()->with('success_updated', 'تم تحديث البيانات بنجاج');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ اثناء العملية');
        }
    }

    public function destroy(Tree4 $representative): RedirectResponse
    {
        try {
            DB::beginTransaction();
            if($representative->saleRepresentatives()->count() > 0 || $representative->saleReturnRepresentatives()->count() > 0){
                DB::rollBack();
                return back()->withErrors('يلا يمكن حذف هذا المندوب لانه مرتبط ببعض الحسابات ');
            }
            $representative->delete();
            DB::commit();
            return back()->with('success_deleted', 'تم الحذف بنجاح ');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ اثناء العملية');
        }
    }

    public function select2(Request $request): JsonResponse
    {
        $search = $request->get(key: 'q');
        $query = Tree4::query()->when($search, function ($query) use ($search) {
            $query->where('tree4_name', 'like', '%' . $search . '%');
        });

        $tree4 = $query->whereTree3_code(2302)->get();

        $formattedResults = $tree4->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->tree4_name, // الحقل الذي سيظهر في Select2
            ];
        });
        return response()->json($formattedResults);
    }

    public function addRepresentative(Request $request): JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'rep_percentage' => 'nullable|numeric|between:0,100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'نسبة المندوب غير صالحة.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $tree4 = Tree4::whereTree3_code(1206)->orderBy('id', 'DESC')->first();
            $tree4_code = $tree4 ? $tree4->tree4_code + 1 : '120600001';

            $representative = Tree4::create(
                attributes: [
                    'tree4_name' => $request->name,
                    'tree4_code' => $tree4_code,
                    'tree3_id' => 13,
                    'tree3_code' => 1206,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'rep_percentage' => $request->rep_percentage ?? 0,
                    'user_id' => Auth::id(),
                ],
            );

            DB::commit();
            return response()->json([
                'success' => true,
                'newRepresentative' => [
                    'id' => $representative->id,
                    'name' => $representative->tree4_name,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function checkEmail(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $exists = Tree4::where('email', $request->email)->where('id', '!=', $id)->exists(); // تحقق من وجود الباركود في قاعدة البيانات
        return response()->json(['isUnique' => !$exists]);
    }

    public function checkName(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $exists = Tree4::where('tree4_name', $request->name)->where('id', '!=', $id)->exists(); // تحقق من وجود الباركود في قاعدة البيانات
        return response()->json(['isUnique' => !$exists]);
    }

    public function checkPhone(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $exists = Tree4::where('phone', $request->phone)->where('id', '!=', $id)->exists(); // تحقق من وجود الباركود في قاعدة البيانات
        return response()->json(['isUnique' => !$exists]);
    }
}
