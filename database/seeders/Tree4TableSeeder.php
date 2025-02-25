<?php

namespace Database\Seeders;

use App\Models\Tree4;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class Tree4TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tree4  = new Tree4();
        $tree4->tree4_code = 410100001;
        $tree4->tree4_name = 'العمولات';
        $tree4->tree3_id = 7;
        $tree4->tree3_code = 4101;
        $tree4->user_id = 1;
        $tree4->save();


        $tree4  = new Tree4();
        $tree4->tree4_code = 510100001;
        $tree4->tree4_name = 'منصرفات اليوم';
        $tree4->tree3_id = 8;
        $tree4->tree3_code = 5101;
        $tree4->user_id = 1;
        $tree4->save();

        $tree4  = new Tree4();
        $tree4->tree4_code = 420100001;
        $tree4->tree4_name = 'المبيعات';
        $tree4->tree3_id = 9;
        $tree4->tree3_code = 4201;
        $tree4->user_id = 1;
        $tree4->save();

        $tree4  = new Tree4();
        $tree4->tree4_code = 520100001;
        $tree4->tree4_name = 'المشتريات';
        $tree4->tree3_id = 10;
        $tree4->tree3_code = 5201;
        $tree4->user_id = 1;
        $tree4->save();

        $tree4  = new Tree4();
        $tree4->tree4_code = 410100002;
        $tree4->tree4_name = 'إيرادات النقل';
        $tree4->tree3_id = 7;
        $tree4->tree3_code = 4101;
        $tree4->user_id = 1;
        $tree4->save();
    }
}
