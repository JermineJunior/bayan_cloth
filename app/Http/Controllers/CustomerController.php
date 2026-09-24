<?php

namespace App\Http\Controllers;

use App\Models\Tree4;
use App\Models\Customer;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Tree4::with('restrictions')->where('tree3_code', 1205)->withSum('restrictions as total_madin', 'madin')->withSum('restrictions as total_dain', 'dain')->orderBy('id', 'desc')->get();
        $representatives = Tree4::where('tree3_code', 1206)->get();
        return view('customer.index', compact('customers', 'representatives'));
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $tree4 = Tree4::whereTree3_code(1205)->orderBy('id', 'DESC')->first();
            $tree4_code = $tree4 ? $tree4->tree4_code + 1 : '120500001';

            Tree4::create(
                attributes: [
                    'tree4_name' => $request->name,
                    'tree4_code' => $tree4_code,
                    'tree3_id' => 12,
                    'tree3_code' => 1205,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'representative_id' => $request->representative_id,
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

    public function update(UpdateCustomerRequest $request, Tree4 $customer): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $customer->update([
                'tree4_name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'representative_id' => $request->representative_id,
            ]);

            Db::commit();

            return back()->with('success_updated', 'تم تحديث البيانات بنجاج');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ اثناء العملية');
        }
    }

    public function destroy(Tree4 $customer): RedirectResponse
    {
        try {
            DB::beginTransaction();
           /* if ($customer->restrictions()->count() > 0) {  // تعديل 11/08 
                DB::rollBack();
                return back()->withErrors('لا يمكن حذف هذا العميل لانه مرتبط ببعض الحسابات '); // العميل يريد حذف العميل بكل ملفاته
            } */
            $customer->delete();
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
        $query = Tree4::query()
            ->when($search, function ($query) use ($search) {
                $query->where('tree4_name', 'like', '%' . $search . '%');
            });

        $tree4 = $query->whereTree3_code(1205)->get();

        $formattedResults = $tree4->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->tree4_name, // الحقل الذي سيظهر في Select2
            ];
        });
        return response()->json($formattedResults);
    }

    public function addCustomer(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'unique:tree4s,tree4_name'
            ]);

            $tree4 = Tree4::whereTree3_code(1205)->orderBy('id', 'DESC')->first();
            $tree4_code = $tree4 ? $tree4->tree4_code + 1 : '120500001';

            $customer = Tree4::create([
                'tree4_name' => $request->name,
                'tree4_code' => $tree4_code,
                'tree3_id' => 12,
                'tree3_code' => 1205,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'representative_id' => $request->representative_id,
                'user_id' => Auth::id(),
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'newCustomer' => [
                    'id' => $customer->id,
                    'name' => $customer->tree4_name,
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
        $exists = Tree4::where('email', $request->email)->where('id', '!=', $id)->exists();
        return response()->json(['isUnique' => !$exists]);
    }

    public function checkName(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $exists = Tree4::where(['tree4_name' => $request->name])
            ->where('id', '!=', $id)
            ->exists();
        return response()->json(['isUnique' => !$exists]);
    }

    public function checkPhone(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $exists = Tree4::where(['phone' => $request->phone])
            ->where('id', '!=', $id)
            ->exists();
        return response()->json(['isUnique' => !$exists]);
    }

    public function getRepresentative($id)
    {
        $customer_tree = Tree4::find($id);

        return response()->json([
            'success' => true,
            'representative' => [
                'id' => $customer_tree->representative_id,
                'name' => $customer_tree->representative->tree4_name,
            ],
        ]);
    }
}
