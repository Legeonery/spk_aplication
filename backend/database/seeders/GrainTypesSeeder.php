<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GrainTypes;

class GrainTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['Пшеница', 'Кукуруза', 'Ячмень', 'Овёс', 'Рожь'];

        foreach ($types as $type) {
            GrainTypes::create(['name' => $type]);
        }
    }
}