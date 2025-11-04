<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class VerificationCodeController extends Controller
{
    public function showForm()
    {
        return view('auth.verify_code');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|digits:6',
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user && $user->verification_code === $request->verification_code) {
            $user->email_verified_at = Carbon::now();
            $user->verification_code = null;
            $user->save();
            Auth::login($user);
            return view('auth.thank_you');
        }
        return back()->withErrors(['verification_code' => 'Invalid verification code.']);
    }
}
