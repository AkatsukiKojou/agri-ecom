<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use App\Models\Service;


class UserProfileViewController extends Controller
{
    // public function index()
    // {
    //     $profiles = Profile::latest()->get();
    //     return view('user.profiles.index', compact('profiles'));
    // }

    // public function index1()
    // {
    //     // Get services that are available (is_available = true) and not deleted (soft delete)
    //     $services = Service::where('is_available', true)
    //                        ->whereNull('deleted_at')
    //                        ->get();

    //     // Return the services to the user view
    //     return view('user.services.index', compact('services'));
    // }
}
