<?php
namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\Municipality;
use App\Models\Barangay;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    // Fetch provinces for the first dropdown
    public function getProvinces()
    {
        $provinces = Province::all();
        return response()->json($provinces);
    }

    // Fetch municipalities based on selected province
    public function getMunicipalities($province_id)
    {
        $municipalities = Municipality::where('province_id', $province_id)->get();
        return response()->json($municipalities);
    }

    // Fetch barangays based on selected municipality
    public function getBarangays($municipality_id)
    {
        $barangays = Barangay::where('municipality_id', $municipality_id)->get();
        return response()->json($barangays);
    }
    
}

