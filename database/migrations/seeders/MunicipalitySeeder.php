<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use Illuminate\Support\Facades\DB;

use App\Models\Municipality;

class MunicipalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pangasinan = Province::firstOrCreate(['name' => 'Pangasinan']);
        $ilocossur = Province::firstOrCreate(['name' => 'Ilocos Sur']);
        $ilocosnorte = Province::firstOrCreate(['name' => 'Ilocos Norte']);
        $launion = Province::firstOrCreate(['name' => 'La Union']);
    
        Municipality::create([
            // Pangasinan
            ['name' => 'Agno', 'province_id' => $pangasinan->id],
            ['name' => 'Aguilar', 'province_id' => $pangasinan->id],
            ['name' => 'Alcala', 'province_id' => $pangasinan->id],
            ['name' => 'Anda', 'province_id' => $pangasinan->id],
            ['name' => 'Asingan', 'province_id' => $pangasinan->id],
            ['name' => 'Balungao', 'province_id' => $pangasinan->id],
            ['name' => 'Bani', 'province_id' => $pangasinan->id],
            ['name' => 'Basista', 'province_id' => $pangasinan->id],
            ['name' => 'Bautista', 'province_id' => $pangasinan->id],
            ['name' => 'Bayambang', 'province_id' => $pangasinan->id],
            ['name' => 'Binalonan', 'province_id' => $pangasinan->id],
            ['name' => 'Binmaley', 'province_id' => $pangasinan->id],
            ['name' => 'Bolinao', 'province_id' => $pangasinan->id],
            ['name' => 'Bugallon', 'province_id' => $pangasinan->id],
            ['name' => 'Burgos', 'province_id' => $pangasinan->id],
            ['name' => 'Calasiao', 'province_id' => $pangasinan->id],
            ['name' => 'Dagupan City', 'province_id' => $pangasinan->id],
            ['name' => 'Dasol', 'province_id' => $pangasinan->id],
            ['name' => 'Infanta', 'province_id' => $pangasinan->id],
            ['name' => 'Labrador', 'province_id' => $pangasinan->id],
            ['name' => 'Laoac', 'province_id' => $pangasinan->id],
            ['name' => 'Lingayen', 'province_id' => $pangasinan->id],
            ['name' => 'Mabini', 'province_id' => $pangasinan->id],
            ['name' => 'Malasiqui', 'province_id' => $pangasinan->id],
            ['name' => 'Manaoag', 'province_id' => $pangasinan->id],
            ['name' => 'Mangaldan', 'province_id' => $pangasinan->id],
            ['name' => 'Mangatarem', 'province_id' => $pangasinan->id],
            ['name' => 'Mapandan', 'province_id' => $pangasinan->id],
            ['name' => 'Natividad', 'province_id' => $pangasinan->id],
            ['name' => 'Pozorrubio', 'province_id' => $pangasinan->id],
            ['name' => 'Rosales', 'province_id' => $pangasinan->id],
            ['name' => 'San Carlos City', 'province_id' => $pangasinan->id],
            ['name' => 'San Fabian', 'province_id' => $pangasinan->id],
            ['name' => 'San Jacinto', 'province_id' => $pangasinan->id],
            ['name' => 'San Manuel', 'province_id' => $pangasinan->id],
            ['name' => 'San Nicolas', 'province_id' => $pangasinan->id],
            ['name' => 'San Quintin', 'province_id' => $pangasinan->id],
            ['name' => 'Santa Barbara', 'province_id' => $pangasinan->id],
            ['name' => 'Santa Maria', 'province_id' => $pangasinan->id],
            ['name' => 'Santo Tomas', 'province_id' => $pangasinan->id],
            ['name' => 'Sison', 'province_id' => $pangasinan->id],
            ['name' => 'Sual', 'province_id' => $pangasinan->id],
            ['name' => 'Tayug', 'province_id' => $pangasinan->id],
            ['name' => 'Umingan', 'province_id' => $pangasinan->id],
            ['name' => 'Urbiztondo', 'province_id' => $pangasinan->id],
            ['name' => 'Urdaneta City', 'province_id' => $pangasinan->id],
            ['name' => 'Villasis', 'province_id' => $pangasinan->id],

            // Ilocos Sur
            ['name' => 'Vigan City', 'province_id' => $ilocossur->id],
            ['name' => 'Candon City', 'province_id' => $ilocossur->id],

            // Ilocos Norte
            ['name' => 'Laoag City', 'province_id' => $ilocosnorte->id],
            ['name' => 'Batac City', 'province_id' => $ilocosnorte->id],

            // La Union
            ['name' => 'San Fernando City', 'province_id' => $launion->id],
            ['name' => 'Bauang', 'province_id' => $launion->id],
        ]);
    }
}
