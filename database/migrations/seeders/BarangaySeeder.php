<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Barangay;
use App\Models\Municipality;

class BarangaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $scc = Municipality::where('name', 'San Carlos City')->first();
    $dagupan = Municipality::where('name', 'Dagupan City')->first();
    $binmaley = Municipality::where('name', 'Binmaley')->first();

    $vigan = Municipality::where('name', 'Vigan City')->first();
    $candon = Municipality::where('name', 'Candon City')->first();

    $laoag = Municipality::where('name', 'Laoag City')->first();
    $batac = Municipality::where('name', 'Batac City')->first();

    $sanfernando = Municipality::where('name', 'San Fernando City')->first();
    $bauang = Municipality::where('name', 'Bauang')->first();

    Barangay::create([
        // San Carlos City
        ['name' => 'Pagal', 'municipality_id' => $scc->id],
        ['name' => 'Camanang', 'municipality_id' => $scc->id],

        // Dagupan City
        ['name' => 'Bonuan Gueset', 'municipality_id' => $dagupan->id],
        ['name' => 'Pantal', 'municipality_id' => $dagupan->id],

        // Binmaley
        ['name' => 'Poblacion', 'municipality_id' => $binmaley->id],
        ['name' => 'Nagpalangan', 'municipality_id' => $binmaley->id],

        // Vigan
        ['name' => 'Tamag', 'municipality_id' => $vigan->id],
        ['name' => 'Mindoro', 'municipality_id' => $vigan->id],

        // Candon
        ['name' => 'San Nicolas', 'municipality_id' => $candon->id],
        ['name' => 'Bagani Campo', 'municipality_id' => $candon->id],

        // Laoag
        ['name' => 'Barangay 1 San Joaquin', 'municipality_id' => $laoag->id],
        ['name' => 'Barangay 2 Ablan', 'municipality_id' => $laoag->id],

        // Batac
        ['name' => 'Quiaoit', 'municipality_id' => $batac->id],
        ['name' => 'Valle', 'municipality_id' => $batac->id],

        // San Fernando
        ['name' => 'Pagdaraoan', 'municipality_id' => $sanfernando->id],
        ['name' => 'Catbangen', 'municipality_id' => $sanfernando->id],

        // Bau  ang
        ['name' => 'Nagrebcan', 'municipality_id' => $bauang->id],
        ['name' => 'Parian Este', 'municipality_id' => $bauang->id],
    ]);
    }
}
