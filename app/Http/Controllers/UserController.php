<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Branch;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::whereHas('branches',function($query){
            $query->where('branch_id',session('branch_id'));
        })->where('username', '!=', 'bayan')->get(); //TODO::not all users appear
        return view('user.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::select('name', 'id')->get();
        $branches = Branch::get();
        return view('user.create', compact('roles', 'branches'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'password' => bcrypt($request->password),
                
            ]);
            foreach ($request->branch as $branch) {
                $user->branches()->syncWithoutDetaching([$branch]);
            }

            $role = Role::find($request->roles_name);
            $user->assignRole($role);

            DB::commit();
            return redirect()->route('user.index')->with('success_created', 'تم اضافة المستخدم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('user.index')->withErrors('حدث خطأ اثناء الاضافة');
        }
    }

    public function edit(User $user): View
    {
        $roles = Role::select('id', 'name')->get();
        $branches = Branch::get();
        return view('user.edit', compact('user', 'roles', 'branches'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // تحديث بيانات المستخدم
            $user->update([
                'name' => $request->name,
                'username' => $request->username,
            ]);

            if ($request->filled('password')) {
                // التحقق مما إذا كانت كلمة المرور مملوءة فقط
                $user->update(['password' => Hash::make($request->password)]);
            }

            // تحديث الصلاحية
            $role = Role::find($request->roles_name); // الحصول على اسم الدور
            $user->syncRoles($role); // حذف الأدوار القديمة وتعيين الدور الجديد

            foreach ($request->branch as $branch) {
                $user->branches()->syncWithoutDetaching([$branch]);
            }
            DB::commit();

            return redirect()->route('user.index')->with('success_updated', 'تم تعديل البيانات بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('user.index')->withErrors('حدث خطأ أثناء التعديل');
        }
    }

    public function destroy(User $user): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $user->delete();
            DB::commit();
            return redirect()->route('user.index')->with('success_deleted', 'تم حذف المستخدم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('user.index')->withErrors('حدث خطأ اثناء الحذف');
        }
    }

    public function password_reset(Request $request)
    {
        // التحقق من وجود user_id
        $this->validate(
            request: $request,
            rules: [
                'user_id' => 'required|exists:users,id',
            ],
        );

        $id = $request->user_id;
        $user = User::find($id);

        if ($user) {
            // تعيين كلمة المرور الجديدة
            $user->password = Hash::make(123456789);
            $user->save();

            // يمكنك إضافة إرسال بريد إلكتروني للمستخدم في حال أردت إبلاغه
            // Mail::to($user->email)->send(new PasswordResetMail($newPassword));

            return response()->json(
                [
                    'message' => 'تم إعادة تعيين كلمة المرور بنجاح.',
                ],
                200,
            );
        }

        return response()->json(
            [
                'message' => 'المستخدم غير موجود.',
            ],
            404,
        );
    }

    public function trashed_users(): View
    {
        $users = User::onlyTrashed()->get();
        return view('user.trashed', compact('users'));
    }

    public function restore(Request $request): RedirectResponse
    {
        $this->validate(
            request: $request,
            rules: [
                'restore_id' => 'required|exists:users,id',
            ],
        );

        $id = $request->restore_id;
        User::withTrashed()->find($id)->restore();
        return redirect()->route('user.index')->with('success', 'تم استعادة المستخدم من القائمة المهملة');
    }

    public function force_delete(Request $request): RedirectResponse
    {
        $this->validate(
            request: $request,
            rules: [
                'force_delete_id' => 'required|exists:users,id',
            ],
        );

        $id = $request->force_delete_id;
        User::withTrashed()->find($id)->forceDelete();
        return redirect()->route('user.trashed_users')->with('success_deleted', 'تم حذف المستخدم نهائيا ');
    }

    public function profile(): View
    {
        $user = User::whereId(Auth::id())->first();
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = User::find(Auth::id());

        $this->validate($request, [
            'name' => 'required',
            'username' => ['required', Rule::unique('users')->ignore($user)],
            'password' => 'same:password_confirmation',
        ]);

        $user->name = $request->name;
        $user->username = $request->username;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        Session()->flash('success');
        return back();
    }

    public function checkUserName(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $exists = User::whereHas('branches',function($query){
            $query->where('branch_id',session('branch_id'));
        })->where('username', $request->username)->where('id', '!=', $id)->exists(); // تحقق من وجود الباركود في قاعدة البيانات
        return response()->json(['isUnique' => !$exists]);
    }
}
