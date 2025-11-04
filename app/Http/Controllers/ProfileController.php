<?php
namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Service;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\ProfileFollower;
use App\Models\ProfileLike;
use App\Notifications\UserFollowedNotification;
use App\Notifications\UserLikedNotification;
use App\Notifications\UserFollowedProfile;
use App\Notifications\UserLikedProfile;
use App\Models\User; // or kung saan naka-link ang admin
use Hash;

class ProfileController extends Controller
{
    public function create()
    {
        return view('admin.profile.create');
    }
public function store(Request $request)
{
    $validated = $request->validate([
        'farm_name' => 'required|string',
        'farm_owner' => 'required|string',
        'region' => 'required|string',
        'province' => 'required|string',
        'city' => 'required|string',
        'barangay' => 'required|string',
            'address' => 'nullable|string|max:255',
        'phone_number' => 'nullable|string',
        'email' => 'required|email',
        'description' => 'required|string', // <-- ADD THIS LINE
        'profile_photo' => 'nullable|image|max:2048',
        'certificate' => 'required|file',
        'farm_photos' => 'image|max:4096',          // each file must be image max 4MB
        'documentary' => 'nullable|file',
    ]);

    if ($request->hasFile('profile_photo')) {
        $validated['profile_photo'] = $request->file('profile_photo')->store('profiles', 'public');
    }
    if ($request->hasFile('certificate')) {
        $validated['certificate'] = $request->file('certificate')->store('certificates', 'public');
    }

    // Store multiple farm photos
    $farmPhotosPaths = [];
      if ($request->hasFile('farm_photo')) {
        $validated['farm_photo'] = $request->file('farm_photo')->store('farm_photos', 'public');
    }
    if ($request->hasFile('documentary')) {
        $validated['documentary'] = $request->file('documentary')->store('docs', 'public');
    }

    $validated['admin_id'] = Auth::id();

    Profile::create($validated);

       User::create([
        'name' => $request->farm_owner,
 'email' => $request->email, // dapat unique
    'password' => Hash::make('defaultpassword123'), // or generate random password
]);   

    return redirect()->route('profiles.show')->with('success', 'Profile created successfully.');
}


public function show()
{
    $profile = Auth::user()->profile;

    // Make sure $profile exists to avoid errors
    $products_count = 0;
    $training_services_count = 0;

if ($profile) {
    $products_count = \App\Models\Products::where('admin_id', $profile->admin_id)->count();
    $training_services_count = \App\Models\Service::where('admin_id', $profile->admin_id)->count();

    // Prepare gallery images from farm_gallery JSON column
    $galleryImages = [];
    if ($profile->farm_gallery) {
        $galleryImages = is_array($profile->farm_gallery)
            ? $profile->farm_gallery
            : json_decode($profile->farm_gallery, true);
        if (!is_array($galleryImages)) {
            $galleryImages = [];
        }
    }
} else {
    $galleryImages = [];
}

// Followers and Likes (if you want to show them)
// Followers and likes counts
$followers_count = \App\Models\ProfileFollower::where('profile_id', $profile->id)->count();
$likes_count = \App\Models\ProfileLike::where('profile_id', $profile->id)->count();

// Load follower list (with user relationship) for modal display
$followersList = \App\Models\ProfileFollower::with('user')
    ->where('profile_id', $profile->id)
    ->latest()
    ->get()
    ->map(function($f){
        // Prefer the user's `photo` column, then profile photo fields, then profile_image
        $photo = null;
        if ($f->user) {
            if (!empty($f->user->photo)) {
                $photo = $f->user->photo;
            } elseif (!empty($f->user->profile) && !empty($f->user->profile->profile_photo)) {
                $photo = $f->user->profile->profile_photo;
            } elseif (!empty($f->user->profile_image)) {
                $photo = $f->user->profile_image;
            }
        }

        return [
            'id' => $f->id,
            'user_id' => $f->user_id,
            'name' => $f->user ? $f->user->name : 'User',
            'photo' => $photo,
            'photo_url' => $photo ? asset('storage/' . $photo) : asset('/images/default-avatar.png'),
        ];
    });

// Load likers list (with user relationship) for modal display
$likesList = \App\Models\ProfileLike::with('user')
    ->where('profile_id', $profile->id)
    ->latest()
    ->get()
    ->map(function($l){
        // Prefer the user's `photo` column, then profile photo fields, then profile_image
        $photo = null;
        if ($l->user) {
            if (!empty($l->user->photo)) {
                $photo = $l->user->photo;
            } elseif (!empty($l->user->profile) && !empty($l->user->profile->profile_photo)) {
                $photo = $l->user->profile->profile_photo;
            } elseif (!empty($l->user->profile_image)) {
                $photo = $l->user->profile_image;
            }
        }

        return [
            'id' => $l->id,
            'user_id' => $l->user_id,
            'name' => $l->user ? $l->user->name : 'User',
            'photo' => $photo,
            'photo_url' => $photo ? asset('storage/' . $photo) : asset('/images/default-avatar.png'),
        ];
    });

// Pass all variables to the view (use *_count names used by the Blade)
return view('admin.profile.show', compact(
    'profile',
    'products_count',
    'training_services_count',
    'followers_count',
    'likes_count',
    'galleryImages',
    'followersList',
    'likesList'
));
}
public function edit()
{
    $profile = Auth::user()->profile;

    return view('admin.profile.edit', compact('profile'));
}
public function update(Request $request)
{
    $profile = Auth::user()->profile;

    $data = $request->validate([
        'farm_name' => 'required|string|max:255',
        'farm_owner' => 'required|string|max:255',
        'location' => 'required|string|max:255',
        'description' => 'required|string',
        'phone_number' => 'nullable|string',
        'email' => 'required|email',
        'Facebook' => 'nullable|string',
        'profile_photo' => 'nullable|image',
        'certificate' => 'nullable|file',
        'farm_photos' => 'nullable|array|max:6',
        'farm_photos.*' => 'image',
        'documentary' => 'nullable|file',
    ]);

    if ($request->hasFile('profile_photo')) {
        if ($profile->profile_photo) {
            Storage::delete('public/' . $profile->profile_photo);
        }
        $data['profile_photo'] = $request->file('profile_photo')->store('profiles', 'public');
    }

    if ($request->hasFile('certificate')) {
        if ($profile->certificate) {
            Storage::delete('public/' . $profile->certificate);
        }
        $data['certificate'] = $request->file('certificate')->store('certificates', 'public');
    }

    if ($request->hasFile('farm_photos')) {
        if ($profile->farm_photos) {
            $oldPhotos = json_decode($profile->farm_photos, true);
            foreach ($oldPhotos as $oldPhoto) {
                Storage::delete('public/' . $oldPhoto);
            }
        }
        $newFarmPhotos = [];
        foreach ($request->file('farm_photos') as $photo) {
            $newFarmPhotos[] = $photo->store('farm_photos', 'public');
        }
        $data['farm_photos'] = json_encode($newFarmPhotos);
    }

    if ($request->hasFile('documentary')) {
        if ($profile->documentary) {
            Storage::delete('public/' . $profile->documentary);
        }
        $data['documentary'] = $request->file('documentary')->store('docs', 'public');
    }

    $profile->update($data);

    return redirect()->route('admin.dashboard')->with('success', 'Profile updated successfully!');
}

public function addGalleryPhoto(Request $request, $profileId)
{
    $request->validate([
        'gallery_photo' => 'required|image|max:4096',
    ]);

    $profile = Profile::findOrFail($profileId);

    // Save the uploaded image
    $path = $request->file('gallery_photo')->store('gallery', 'public');

    // Assuming you have a JSON or array column named 'farm_gallery'
    $gallery = $profile->farm_gallery ?? [];
    if (!is_array($gallery)) {
        $gallery = json_decode($gallery, true) ?? [];
    }
    if (count($gallery) < 6) {
        $gallery[] = $path;
        $profile->farm_gallery = json_encode($gallery);
        $profile->save();
    }

    return back()->with('success', 'Gallery photo added!');
}
public function updatePhoto(Request $request, $profileId)
{
    $request->validate([
        'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $profile = \App\Models\Profile::findOrFail($profileId);

    // Delete old photo if exists
    if ($profile->profile_photo) {
        \Storage::delete('public/profiles/' . $profile->profile_photo);
    }

    // Store new photo
    $path = $request->file('profile_photo')->store('profile_photos', 'public');
    $profile->profile_photo = $path;
    $profile->save();

    return redirect()->back()->with('success', 'Profile photo updated successfully!');
}

public function updateAddress(Request $request, $id)
{
    $request->validate([
        'address' => 'required|string|max:255', // Street/House Number
        'barangay' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        'province' => 'required|string|max:255',
        'region' => 'required|string|max:255',
    ]);

    $profile = Profile::findOrFail($id);
    $profile->update([
        'address' => $request->address,
        'barangay' => $request->barangay,
        'city' => $request->city,
        'province' => $request->province,
        'region' => $request->region,
    ]);

    return redirect()->back()->with('success', 'Address updated successfully!');
}

//for user View

public function indexForUsers(Request $request)
{
    $profiles = Profile::all();
    $followed = [];
    $liked = [];
    if (Auth::check()) {
        $userId = Auth::id();
        $followed = ProfileFollower::where('user_id', $userId)->pluck('profile_id')->toArray();
        $liked = ProfileLike::where('user_id', $userId)->pluck('profile_id')->toArray();
    }

    // Top followers (paginate by 10)
    $topFollowers = Profile::withCount('followers')
        ->orderByDesc('followers_count')
        ->paginate(10, ['*'], 'followers_page');

    // Top likes (paginate by 10)
    $topLikes = Profile::withCount('likes')
        ->orderByDesc('likes_count')
        ->paginate(10, ['*'], 'likes_page');

    return view('user.profiles.index', compact(
        'profiles',
        'followed',
        'liked',
        'topFollowers',
        'topLikes'
    ));
}

public function show1(Request $request, $id)
{
    $profile = Profile::with('user')->findOrFail($id);

    $products = Products::where('admin_id', $profile->admin_id)
        ->latest()
        ->paginate(20);

    $services = Service::where('admin_id', $profile->admin_id)
        ->latest()
        ->paginate(20);

    $totalProducts = Products::where('admin_id', $profile->admin_id)->count();
    $totalServices = Service::where('admin_id', $profile->admin_id)->count();

    // Followers and Likes logic
    $followersCount = ProfileFollower::where('profile_id', $profile->id)->count();
    $likesCount = ProfileLike::where('profile_id', $profile->id)->count();

    $alreadyFollowed = false;
    $alreadyLiked = false;
    if (Auth::check()) {
        $alreadyFollowed = ProfileFollower::where('profile_id', $profile->id)
            ->where('user_id', Auth::id())
            ->exists();
        $alreadyLiked = ProfileLike::where('profile_id', $profile->id)
            ->where('user_id', Auth::id())
            ->exists();
    }

    if ($request->ajax()) {
        $query = $request->query('query');

        $filteredProducts = Products::where('admin_id', $profile->admin_id)
            ->where('name', 'like', '%' . $query . '%')
            ->get(['name', 'description', 'images']);

        $filteredServices = Service::where('admin_id', $profile->admin_id)
            ->where('service_name', 'like', '%' . $query . '%')
            ->get(['service_name', 'description', 'images']);

        return response()->json($filteredProducts->concat($filteredServices)->values());
    }

    return view('user.profiles.show', compact(
        'profile',
        'products',
        'services',
        'totalProducts',
        'totalServices',
        'followersCount',
        'likesCount',
        'alreadyFollowed',
        'alreadyLiked'
    ));
}
public function uploadGcashQr(Request $request, $profileId)
{
    $request->validate([
        'gcash_qr' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);
    $profile = Profile::findOrFail($profileId);
    if ($request->hasFile('gcash_qr')) {
        $path = $request->file('gcash_qr')->store('gcash_qr_codes', 'public');
        $profile->gcash_qr = $path;
        $profile->save();
    }
    return back()->with('success', 'GCash QR code uploaded successfully!');
}

    /**
     * Update phone number for a profile (from inline edit)
     */
    public function updatePhone(Request $request, $profileId)
    {
        $request->validate([
            'phone_number' => 'nullable|string|max:30',
        ]);

        $profile = Profile::findOrFail($profileId);
        $profile->phone_number = $request->input('phone_number');
        $profile->save();

        return redirect()->back()->with('success', 'Phone number updated successfully!');
    }

    /**
     * Update email for a profile (from inline edit). Also update linked user email when possible.
     */
    public function updateEmail(Request $request, $profileId)
    {
        $profile = Profile::findOrFail($profileId);

        $request->validate([
            'email' => 'required|email|max:255|unique:users,email,' . ($profile->admin_id ?? 'NULL'),
        ]);

        $email = $request->input('email');
        $profile->email = $email;
        $profile->save();

        // Also update the associated user record if admin_id exists
        if (!empty($profile->admin_id)) {
            $user = \App\Models\User::find($profile->admin_id);
            if ($user) {
                $user->email = $email;
                $user->save();
            }
        }

        return redirect()->back()->with('success', 'Email updated successfully!');
    }



}