<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'مهند عبد الوهاب',
            'username' => 'bayan',
            'password' => bcrypt('123'),
        ]);

        // إنشاء الأدوار
        $owner_role = Role::create(['name' => 'owner']);

        // جلب الصلاحيات
        $permissions_owner = Permission::pluck('id');

        // مزامنة الصلاحيات مع الأدوار
        $owner_role->syncPermissions($permissions_owner);

        // تعيين الدور للمستخدم
        $user->assignRole('owner');

        $user->branches()->syncWithoutDetaching([1]);
    }
}
