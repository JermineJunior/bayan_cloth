<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::all();

        return view('role.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Permission::get();
        return view('role.create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $this->validate($request, [
                'name' => 'required|unique:roles,name',
                'permission' => 'required',
            ]);
            $role = Role::create(['name' => $request->input('name')]);
            $role->syncPermissions($request->input('permission'));

            DB::commit();
            return redirect()->route('role.index')->with('success_created', 'Role created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('role.index')
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show($id): View
    {
        $role = Role::with('permissions')->findOrFail($id); // جلب الدور مع الأذونات مباشرةً
        return view('role.show', compact('role'));
    }

    public function edit($id): View
    {
        $role = Role::with('permissions')->find($id);
        $permissions = Permission::get();
        return view('role.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);
        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();
        $role->syncPermissions($request->input('permission'));
        return redirect()->route('role.index')->with('success_updated', 'Role updated successfully');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();
        return redirect()->route('role.index')->with('success_deleted', 'Role deleted successfully');
    }
}
