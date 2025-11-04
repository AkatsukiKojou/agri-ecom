<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Mail\VerificationCodeMail;

class VerifyController extends Controller
{
    // Registration with code sending
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'region' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'region' => $request->region,
            'province' => $request->province,
            'city' => $request->city,
            'barangay' => $request->barangay,
            'password' => bcrypt($request->password),
            // 'photo' => null,
        ]);

        $code = rand(100000, 999999);
        $user->verification_code = $code;
        $user->save();

        Mail::to($user->email)->send(new VerificationCodeMail($code));

        Auth::login($user);

        return response()->json([
            'success' => true,
            'email' => $user->email,
            'message' => 'Verification code sent to your email.'
        ]);
    }

    // Code verification
    public function verifyEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|numeric'
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user && $user->verification_code == $request->code) {
            $user->email_verified_at = now();
            $user->verification_code = null;
            $user->save();
            Auth::login($user);
            return response()->json([
                'verified' => true,
                'redirect' => route('user.dashboard')
            ]);
        }
        return response()->json([
            'verified' => false,
            'message' => 'Incorrect verification code.'
        ], 422);
    }

    // Resend code
    public function resend(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $code = rand(100000, 999999);
            $user->verification_code = $code;
            $user->save();
            Mail::to($user->email)->send(new VerificationCodeMail($code));
            return response()->json([
                'success' => true,
                'message' => 'Verification code resent.'
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'User not found.'
        ], 404);
    }
}