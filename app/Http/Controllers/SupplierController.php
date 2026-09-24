<?php

namespace App\Http\Controllers;

use App\Models\Tree4;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;

class SupplierController extends Controller
{
    public function index(): View
    {
        $suppliers = Tree4::with('restrictions')->where('tree3_code', 2301)->withSum('restrictions as total_madin', 'madin')->withSum('restrictions as total_dain', 'dain')->orderByDesc('id')->get();
        return view('supplier.index', compact('suppliers'));
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $tree4 = Tree4::whereTree3_code(2301)->orderBy('id', 'DESC')->first();
            $tree4_code = $tree4 ? $tree4->tree4_code + 1 : '230100001';

            $tree = Tree4::create(
                attributes: [
                    'tree4_name' => $request->name,
                    'tree4_code' => $tree4_code,
                    'tree3_id' => 11,
                    'tree3_code' => 2301,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'email' => $request->email,
                    'user_id' => Auth::id(),
                ],
            );

            DB::commit();
            return redirect()->route('supplier.index')->with('success_created', 'تمت اضافة البيانات بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ اثناء العملية');
        }
    }

    public function update(UpdateSupplierRequest $request, Tree4 $supplier): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $supplier->update([
                'tree4_name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'email' => $request->email,
            ]);
            Db::commit();

            return back()->with('success_updated', 'تم تحديث البيانات بنجاج');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ اثناء العملية');
        }
    }

    public function destroy(Tree4 $supplier): RedirectResponse
    {
        try {
            DB::beginTransaction();
           /* if ($supplier->restrictions()->count() > 0) {
                DB::rollBack();
                return back()->withErrors('لا يمكن حذف هذا المورد لانه مرتبط ببعض الحسابات ');
            } */
            $supplier->delete();
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

        $tree4 = $query->whereTree3_code(2301)->get();

        $formattedResults = $tree4->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->tree4_name, // الحقل الذي سيظهر في Select2
            ];
        });
        return response()->json($formattedResults);
    }

    public function addSupplier(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $tree4 = Tree4::whereTree3_code(2301)->orderBy('id', 'DESC')->first();
            $tree4_code = $tree4 ? $tree4->tree4_code + 1 : '230100001';

            $supplier = Tree4::create([
                'tree4_name' => $request->name,
                'tree4_code' => $tree4_code,
                'tree3_id' => 12,
                'tree3_code' => 2301,
                'phone' => $request->phone,
                'address' => $request->address,
                'email' => $request->email,
                'user_id' => Auth::id(),
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'newSupplier' => [
                    'id' => $supplier->id,
                    'name' => $supplier->tree4_name,
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
