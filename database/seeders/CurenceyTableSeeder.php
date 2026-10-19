<?php

namespace Database\Seeders;

use App\Models\Curencey;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurenceyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Curencey::create([
            'name'=>'الجنيه السوداني',
            'amount'=>1,
            'symbol' => 'ج.س'
        ]);
    }
}
