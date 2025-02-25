<?php

namespace App\Http\Controllers;

use App\Models\Tree4;
use App\Models\Supplier;
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
        $suppliers = Supplier::get();
        return view('supplier.index', compact('suppliers'));
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            Supplier::create(
                attributes: [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address' => $request->address,
                ],
            );

            $tree4 = Tree4::whereTree3_code(2301)->orderBy('id', 'DESC')->first();
            $tree4_code = $tree4 ? $tree4->tree4_code + 1 : '230100001';

            Tree4::create(
                attributes: [
                    'tree4_name' => $request->name,
                    'tree4_code' => $tree4_code,
                    'tree3_id' => 11,
                    'tree3_code' => 2301,
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

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $tree4 = Tree4::whereTree4_name($supplier->name);
            $supplier->update(
                attributes: [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address' => $request->address,
                ],
            );

            $tree4->update([
                'name' => $request->name,
            ]);
            Db::commit();

            return back()->with('success_updated', 'تم تحديث البيانات بنجاج');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ اثناء العملية');
        }
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        try {
            DB::beginTransaction();
            Tree4::whereTree4_name($supplier->name)->delete();
            $supplier->delete();
            DB::commit();
            return back()->with('success_deleted', 'تم الحذف بنجاح ');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ اثناء العملية');
        }
    }

    public function toggleStatus(Supplier $supplier): JsonResponse
    {
        Tree4::whereTree4_name($supplier->name)->update([
            'status' => !$supplier->status,
        ]);
        $supplier->update(['status' => !$supplier->status]);
        return response()->json(['status' => $supplier->status]);
    }

    public function select2(Request $request): JsonResponse
    {
        $search = $request->get(key: 'q');
        $query = Tree4::query()
            ->where('status', 1)
            ->when($search, function ($query) use ($search) {
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
}
