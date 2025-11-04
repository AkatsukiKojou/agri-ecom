<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Province;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Province::insert([
            ['name' => 'Pangasinan'],
            ['name' => 'Ilocos Norte'],
            ['name' => 'Ilocos Sur'],
            ['name' => 'La Union'],
        ]);
        
    }
}
