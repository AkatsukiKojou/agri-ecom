<?php

namespace App\Http\Controllers;


use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Products;
use App\Models\Profile;
use App\Models\UserProfile;
use Illuminate\Http\Request;
class UserController extends Controller
{
    public function index()
    {
        $admins = User::where('role', 'admin')->get();
        $products = Products::all();
        $randomProducts = Products::whereHas('admin', function($q) {
            $q->where('role', 'admin');
        })->inRandomOrder()->take(3)->get();
        $profiles = Profile::all();
        $services = Service::all();
        $randomServices = Service::inRandomOrder()->take(3)->get();

        // // Fetch messages from admin 'ghet' to the current user
        // $userId = auth()->id();
        // $messages = \App\Models\Message::where('sender_name', 'ghet')
        //     ->where('receiver_id', $userId)
        //     ->orderBy('created_at', 'desc')
        //     ->get();
        // $unreadMessages = $messages->where('is_read', false)->count();

        return view('user.dashboard', compact(
            'profiles', 'admins', 'products', 'randomProducts', 'randomServices', 'services'
        ));
    }
    public function UserLogout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
//Controller for User Services
    public function indexForUser(Request $request)
    {
        // Start with a query for available services
         $search = $request->input('search');

        // Query the services, filter based on the search term
        $services = Service::when($search, function($query, $search) {
            return $query->where('service_name', 'like', "%{$search}%")
                         ->orWhere('category', 'like', "%{$search}%")
                         ->orWhere('service_mode', 'like', "%{$search}%");
        })
        ->paginate(9);  // You can change the number of services per page here


        // For each service, compute nextAvailableDate (use first service for demo, or refactor for list view)
        $service = $services->first();
        $nextAvailableDate = null;
        if ($service) {
            $lastApprovedBooking = $service->bookings()
                ->where('status', 'ongoing')
                ->orderByDesc('booking_start')
                ->first();
            if ($lastApprovedBooking) {
                $duration = is_numeric($service->duration) ? intval($service->duration) : 1;
                $nextAvailable = \Carbon\Carbon::parse($lastApprovedBooking->booking_start)
                    ->addDays($duration)
                    ->addDays(3);
                if ($lastApprovedBooking->booking_end && $lastApprovedBooking->booking_end > $lastApprovedBooking->booking_start) {
                    $nextAvailable = \Carbon\Carbon::parse($lastApprovedBooking->booking_end)->addDays(3);
                }
                $nextAvailableDate = $nextAvailable->toDateString();
            } else {
                $nextAvailableDate = now()->addDays(2)->toDateString();
            }
        }
        return view('user.services.show', compact('services', 'nextAvailableDate'));
    
        // Filter by category if specified
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
    
        // Filter by service mode if specified
        if ($request->filled('service_mode')) {
            $query->where('service_mode', $request->service_mode);
        }
    
        // Paginate the services (show 10 per page)
        $services = $query->paginate(10);
    
        // Return the services index view with the services data
        return view('user.services.show', compact('services'));
    }
    public function showUser($id)
    {
        $service = Service::with('bookings')->findOrFail($id);
        $lastApprovedBooking = $service->bookings()
            ->where('status', 'ongoing')
            ->orderByDesc('booking_start')
            ->first();
        if ($lastApprovedBooking) {
            $duration = is_numeric($service->duration) ? intval($service->duration) : 1;
            $nextAvailable = \Carbon\Carbon::parse($lastApprovedBooking->booking_start)
                ->addDays($duration)
                ->addDays(3);
            if ($lastApprovedBooking->booking_end && $lastApprovedBooking->booking_end > $lastApprovedBooking->booking_start) {
                $nextAvailable = \Carbon\Carbon::parse($lastApprovedBooking->booking_end)->addDays(3);
            }
            $nextAvailableDate = $nextAvailable->toDateString();
        } else {
            $nextAvailableDate = now()->addDays(2)->toDateString();
        }
        return view('user.services.show', compact('service', 'nextAvailableDate'));
    }
    
     public function edit()
    {
        $user = Auth::user();
        return view('user.profiles.edit', compact('user'));
    }

//     public function update(Request $request)
// {
//     $request->validate([
//         'name' => 'required|string|max:255',
//         'phone' => 'nullable|string|max:20',
//         'address' => 'nullable|string|max:255',
//     ]);

//     $user =Auth::user();
//     $user->name = $request->name;
//     $user->phone = $request->phone;
//     $user->address = $request->address;
//     $user->save();

//     return redirect()->back()->with('success', 'Profile updated successfully!');
// }
public function profile()
{
    $user = Auth::user();
    return view('user.profiles.myprofile', compact('user'));
}
public function updateProfile(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'password' => 'nullable|string|min:6|confirmed',
        'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $user->name = $request->name;
    $user->email = $request->email;

    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    if ($request->hasFile('profile_image')) {
        $imagePath = $request->file('profile_image')->store('profile_images', 'public');
        $user->profile_image = $imagePath;
    }

    $user->save();

    return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
}
// public function getProfileImageUrlAttribute()
// {
//     return $this->profile_image
//         ? asset('storage/' . $this->profile_image)
//         : 'https://via.placeholder.com/150';
// }
// public function updateShipping(Request $request)
// {
//     $request->validate([
//         'name' => 'required|string|max:255',
//         'phone' => 'required|string|max:20',
//         'address' => 'required|string|max:500',
//     ]);

//     auth()->user()->update($request->only('name', 'phone', 'address'));

//     return redirect()->back()->with('success', 'Shipping info updated successfully.');
// }

public function updateShipping(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:500',
    ]);

    $user = Auth::user();

    $user->update([
        'name' => $request->name,
        'phone' => $request->phone,
        'address' => $request->address,
    ]);

    return back()->with('success', 'Shipping information updated successfully.');
}
// public function landing()
//     {
//         $products = Products::where('availability', true)->get();
//         $services = Service::where('is_available', true)->get();
//         $profiles = Profile::all();  // Show all LSA profiles, or filter as needed

//         return view('welcome', compact('products', 'services', 'profiles'));
//     }\\

// For user My Account
public function myProfile()
{
    $user = Auth::user();
    $profile = $user->user;
    return view('user.userprofile.show', compact('user', 'profile'));
}
public function uploadPhoto(Request $request)
{
    $request->validate([
        'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);
    $user = Auth::user();
    $path = $request->file('profile_photo')->store('profile_photos', 'public');
    $user->photo = $path;
    $user->save();
    return back()->with('success', 'Profile photo updated!');
}

public function update(Request $request)
{
     $user = Auth::user();
   $data = $request->validate([
        'username' => 'nullable|string|max:255',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        'phone' => 'nullable|string|max:20',
        'gender' => 'nullable|string|max:20',
        'date_of_birth' => 'nullable|date',
        'address' => 'nullable|string|max:255',
        'barangay' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'province' => 'nullable|string|max:255',
        'region' => 'nullable|string|max:255',
    ]);
   $user->update($data);
    return back()->with('success', 'Profile updated!');
}
}

