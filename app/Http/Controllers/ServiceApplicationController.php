<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceApplication;
use Illuminate\Support\Facades\Auth;

class ServiceApplicationController extends Controller
{
    public function apply($service_id)
    {
        $service = Service::findOrFail($service_id);
    
        $existing = ServiceApplication::where('user_id', auth()->id())
            ->where('service_id', $service_id)
            ->first();
    
        if ($existing) {
            return back()->with('error', 'You have already applied for this service.');
        }
    
        ServiceApplication::create([
            'user_id' => auth()->id(),
            'service_id' => $service_id,
            'status' => 'pending',
        ]);
    
        return back()->with('success', 'Service application submitted successfully.');
    }
    
}
