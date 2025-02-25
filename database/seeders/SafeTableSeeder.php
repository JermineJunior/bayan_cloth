<?php

namespace Database\Seeders;

use App\Models\Safe;
use App\Models\Tree4;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SafeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
     {
        $safe = Safe::create([
            'name' => 'الخزنة الرئيسية',
        ]);

        Tree4::create(['tree4_name' => $safe->name, 'tree4_code' => 120200001, 'tree3_id' => 2, 'tree3_code' => 1202, 'user_id' => 1]);

        $safe = safe::create([
            'name' => 'الخزنة الثانوية',
        ]);

        Tree4::create(['tree4_name' => $safe->name, 'tree4_code' => 120200002, 'tree3_id' => 2, 'tree3_code' => 1202, 'user_id' => 1]);
    }
}
