<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminSettingsController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('admin.settings.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'password' => 'nullable|min:6|confirmed'
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('user_photos', 'public');
            $user->photo = $path;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->update(array_filter($validated)); // Avoid null overwrites

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
