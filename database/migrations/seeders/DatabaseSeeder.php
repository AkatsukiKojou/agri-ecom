<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\Barangay;
use Database\Seeders\ProvinceSeeder;
use Database\Seeders\MunicipalitySeeder;
use Database\Seeders\BarangaySeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
      $this->call([
            ProvinceSeeder::class,
            MunicipalitySeeder::class,
            BarangaySeeder::class,
            UserTableSeeder::class,
            
        ]);
        User::factory(10)->create();
       
    }
}
