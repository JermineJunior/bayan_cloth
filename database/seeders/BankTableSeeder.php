<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\Tree4;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BankTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bank = Bank::create([
            'name' => 'بنك امدرمان',
            'no_bank' => 545527,
        ]);

        Tree4::create(['tree4_name' => $bank->name, 'tree4_code' => 120300001, 'tree3_id' => 3, 'tree3_code' => 1203, 'user_id' => 1]);

        $bank = Bank::create([
            'name' => 'بنك الخرطوم',
            'no_bank' => 545527,
        ]);

        Tree4::create(['tree4_name' => $bank->name, 'tree4_code' => 120300002, 'tree3_id' => 3, 'tree3_code' => 1203, 'user_id' => 1]);
    }
}
