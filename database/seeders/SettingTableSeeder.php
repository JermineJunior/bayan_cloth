<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create(attributes: [
            'name' => 'شركة بيان للبرمجيات',
            'location' => ' الخرطوم',
            'logo' => 'bayan.jpg',
            'phone' => implode(',',['0123113936','0123196595'])
        ]);
    }
}
