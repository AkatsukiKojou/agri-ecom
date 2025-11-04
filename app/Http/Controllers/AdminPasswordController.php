<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminPasswordController extends Controller
{
    /**
     * Show the admin password edit form.
     */
    public function edit()
    {
        return view('admin.profile.password');
    }

    /**
     * Update the admin password.
     */
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return back()->with('success', 'Password updated successfully!');
    }
}
