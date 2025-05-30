<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Driver;

class DriversSeeder extends Seeder
{
    public function run(): void
    {
        Driver::insert([
            ['name' => 'Дмитрий Иванов', 'license_number' => 'A123456'],
            ['name' => 'Алексей Смирнов', 'license_number' => 'B654321'],
        ]);
    }
}
