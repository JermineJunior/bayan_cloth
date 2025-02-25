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
        $customers = Customer::get();
        return view('customer.index', compact('customers'));
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            Customer::create(
                attributes: [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address' => $request->address,
                ],
            );

            $tree4 = Tree4::whereTree3_code(1205)->orderBy('id', 'DESC')->first();
            $tree4_code = $tree4 ? $tree4->tree4_code + 1 : '120500001';

            Tree4::create(
                attributes: [
                    'tree4_name' => $request->name,
                    'tree4_code' => $tree4_code,
                    'tree3_id' => 12,
                    'tree3_code' => 1205,
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

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $tree4 = Tree4::whereTree4_name($customer->name);
            $customer->update(
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

    public function destroy(Customer $customer): RedirectResponse
    {
        try {
            DB::beginTransaction();
            Tree4::whereTree4_name($customer->name)->delete();
            $customer->delete();
            DB::commit();
            return back()->with('success_deleted', 'تم الحذف بنجاح ');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ اثناء العملية');
        }
    }

    public function toggleStatus(Customer $customer): JsonResponse
    {
        Tree4::whereTree4_name($customer->name)->update([
            'status' => !$customer->status,
        ]);
        $customer->update(['status' => !$customer->status]);
        return response()->json(['status' => $customer->status]);
    }

    public function select2(Request $request): JsonResponse
    {
        $search = $request->get(key: 'q');
        $query = Tree4::query()
            ->where('status', 1)
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
}
