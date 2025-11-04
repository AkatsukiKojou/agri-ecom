<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Mail\VerificationCodeMail;
use Illuminate\Support\Facades\Mail;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
public function store(Request $request): JsonResponse
{

    $request->validate([
        'first_name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'username' => ['required', 'string', 'max:30', 'unique:users,username'],
        'phone' => ['required', 'string', 'max:15', 'regex:/^[0-9]{11}$/'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'region' => ['required', 'string', 'max:255'],
        'province' => ['required', 'string', 'max:255'],
        'city' => ['required', 'string', 'max:255'],
        'barangay' => ['required', 'string', 'max:255'],
        'address' => ['required', 'string', 'max:255'], // street
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);


    // Generate 6-digit verification code
    $verification_code = random_int(100000, 999999);

    $user = User::create([
        'name' => $request->first_name . ' ' . $request->last_name,
        'username' => $request->username,
        'phone' => $request->phone,
        'email' => $request->email,
        'region' => $request->region,
        'province' => $request->province,
        'city' => $request->city,
        'barangay' => $request->barangay,
        'address' => $request->address, // save street as address
        'password' => Hash::make($request->password),
        'photo' => null, // or default filename if meron kang default
        'verification_code' => $verification_code,
    ]);


    // Send verification code email
    Mail::to($user->email)->send(new VerificationCodeMail($verification_code));

    // Do not login or fire Registered event to avoid redirect issues with AJAX
    return response()->json([
        'success' => true,
        'message' => 'Registration successful. Please check your email for the verification code.',
        'email' => $user->email
    ]);
}
}
