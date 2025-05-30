<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;

class VehiclesSeeder extends Seeder
{
    public function run(): void
    {
        Vehicle::insert([
            [
                'number' => 'AB123CD',
                'type' => 'привоз',
                'max_weight' => 10000,
                'status' => 'на ходу',
                'driver_id' => 1,
                'is_available' => true,
                'notes' => null,
            ],
            [
                'number' => 'EF456GH',
                'type' => 'отгрузка',
                'max_weight' => 8000,
                'status' => 'на ходу',
                'driver_id' => 2,
                'is_available' => true,
                'notes' => 'Новое авто',
            ],
        ]);
    }
}
