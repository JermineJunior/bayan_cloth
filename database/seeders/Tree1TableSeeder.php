<?php

namespace Database\Seeders;

use App\Models\Tree1;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Tree1TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trees = ['الاصول', 'الخصوم', 'حقوق الملكية', 'ايرادات', 'منصرفات'];

        foreach ($trees as $index => $tree) {
            Tree1::create([
                'tree1_name' => $tree,
                'tree1_code' => $index + 1,
            ]);
        }
    }
}
