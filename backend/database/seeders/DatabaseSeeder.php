<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $this->call([
            GrainTypesSeeder::class,
            DriversSeeder::class,
            VehiclesSeeder::class,
            LicenseCategorySeeder::class,
        ]);

        DB::table('vehicle_kinds')->insert([
            ['name' => 'трактор'],
            ['name' => 'камаз'],
            ['name' => 'комбайн'],
            ['name' => 'погрузчик'],
            ['name' => 'гусеничная техника'],
        ]);
        DB::table('roles')->insert([
            ['name' => 'admin'],
            ['name' => 'warehouse_manager'],
            ['name' => 'driver'],
        ]);
    }
}
