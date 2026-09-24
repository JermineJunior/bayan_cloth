<?php

namespace Database\Seeders;

use App\Models\Tree3;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class Tree3TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tree3 = new Tree3();
        $tree3->tree3_code = 1201;
        $tree3->tree3_name = 'الوكلاء';
        $tree3->tree2_id = 2;
        $tree3->tree2_code = 12;
        $tree3->save();

        $tree3 = new Tree3();
        $tree3->tree3_code = 1202;
        $tree3->tree3_name = 'نقدية بالخزينة';
        $tree3->tree2_id = 2;
        $tree3->tree2_code = 12;
        $tree3->save();

        $tree3 = new Tree3();
        $tree3->tree3_code = 1203;
        $tree3->tree3_name = 'نقدية بالبنوك';
        $tree3->tree2_id = 2;
        $tree3->tree2_code = 12;
        $tree3->save();

        // //////////////////////////////////

        $tree3 = new Tree3();
        $tree3->tree3_code = 1204;
        $tree3->tree3_name = 'سلفيات الموظفين';
        $tree3->tree2_id = 2;
        $tree3->tree2_code = 12;
        $tree3->save();

        $tree3 = new Tree3();
        $tree3->tree3_code = 2101;
        $tree3->tree3_name = 'الموظفين';
        $tree3->tree2_id = 3;
        $tree3->tree2_code = 21;
        $tree3->save();

        $tree3 = new Tree3();
        $tree3->tree3_code = 2201;
        $tree3->tree3_name = 'دائنون حكوميون';
        $tree3->tree2_id = 4;
        $tree3->tree2_code = 22;
        $tree3->save();

        $tree3 = new Tree3();
        $tree3->tree3_code = 4101;
        $tree3->tree3_name = 'ايرادات اخري';
        $tree3->tree2_id = 5;
        $tree3->tree2_code = 41;
        $tree3->save();

        $tree3 = new Tree3();
        $tree3->tree3_code = 5101;
        $tree3->tree3_name = 'منصرفات اخري';
        $tree3->tree2_id = 6;
        $tree3->tree2_code = 51;
        $tree3->save();

        $tree3 = new Tree3();
        $tree3->tree3_code = 4201;
        $tree3->tree3_name = 'المبيعات';
        $tree3->tree2_id = 7;
        $tree3->tree2_code = 42;
        $tree3->save();

        $tree3 = new Tree3();
        $tree3->tree3_code = 5201;
        $tree3->tree3_name = 'المشتريات';
        $tree3->tree2_id = 8;
        $tree3->tree2_code = 52;
        $tree3->save();

        $tree3 = new Tree3();
        $tree3->tree3_code = 2301;
        $tree3->tree3_name = 'الموردين';
        $tree3->tree2_id = 9;
        $tree3->tree2_code = 23;
        $tree3->save();

        $tree3 = new Tree3();
        $tree3->tree3_code = 1205;
        $tree3->tree3_name = 'المدينون العملاء';
        $tree3->tree2_id = 2;
        $tree3->tree2_code = 12;
        $tree3->save();

        $tree3 = new Tree3();
        $tree3->tree3_code = 1206;
        $tree3->tree3_name = 'مدينون المناديب ';
        $tree3->tree2_id = 2;
        $tree3->tree2_code = 12;
        $tree3->save();

        $tree3 = new Tree3();
        $tree3->tree3_code = 3101;
        $tree3->tree3_name = 'راس المال';
        $tree3->tree2_id = 10;
        $tree3->tree2_code = 31;
        $tree3->save();

        $tree3 = new Tree3();
        $tree3->tree3_code = 5301;
        $tree3->tree3_name = 'منصرفات مشتريات خارجية';
        $tree3->tree2_id = 11;
        $tree3->tree2_code = 53;
        $tree3->save();
    }
}
