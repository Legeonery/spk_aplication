<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LicenseCategory;
class LicenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            ['code' => 'B', 'description' => 'Легковые автомобили'],
            ['code' => 'C', 'description' => 'Грузовые автомобили'],
            ['code' => 'E', 'description' => 'Прицепы'],
            ['code' => 'D', 'description' => 'Автобусы'],
        ];

        LicenseCategory::insert($categories);
    }
}
