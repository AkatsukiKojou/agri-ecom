<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{

 public function update1(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'name' => 'nullable|string|max:255',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
        
    ]);



    return redirect()->route('checkout.review')->with('success', 'Profile updated successfully!');
}
    public function edit()
    {
        return view('user.setting.edit', ['user' => Auth::user()]);
    }

 public function update(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'farm_owner' => 'nullable|string|max:255',
        'email' => 'nullable|email|unique:users,email,' . $user->id,
        'photo' => 'nullable|image|max:2048',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
    ]);

    if ($request->hasFile('photo')) {
        $path = $request->file('photo')->store('profile_photos', 'public');
        $user->photo = $path;
    }

    // Update user fields (email, phone, address)
    $user->fill($request->only('email', 'phone', 'address'));
    $user->save();

    // If farm_owner is provided, persist it to the user's profile (create profile if needed)
    if ($request->filled('farm_owner')) {
        $profile = $user->profile;
        if (!$profile) {
            $profile = new \App\Models\Profile();
            $profile->admin_id = $user->id;
        }
        $profile->farm_owner = $request->input('farm_owner');
        // Persist profile photo if a dedicated field is used
        if ($request->hasFile('profile_photo')) {
            $profile->profile_photo = $request->file('profile_photo')->store('profiles', 'public');
        }
        $profile->save();
    }

    return redirect()->route('settings.edit')->with('success', 'Profile updated successfully.');
}

public function editPassword()
{
    return view('user.setting.password');
}

public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:8|confirmed',
    ]);

    if (!Hash::check($request->current_password, Auth::user()->password)) {
        return back()->withErrors(['current_password' => 'Current password is incorrect.']);
    }

    $user = Auth::user();
    $user->password = Hash::make($request->new_password);
    $user->save();

    return back()->with('success', 'Password updated successfully!');
}
}
