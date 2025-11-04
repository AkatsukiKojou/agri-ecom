<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Profile;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request) //: RedirectResponse
    {
        try {
            $request->authenticate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMsg = 'Incorrect email and password.';
            if (method_exists($e, 'validator') && $e->validator) {
                $errorLogin = $e->validator->errors()->first('login');
                $errorPassword = $e->validator->errors()->first('password');
                if ($errorLogin) {
                    $errorMsg = $errorLogin;
                } elseif ($errorPassword) {
                    $errorMsg = $errorPassword;
                }
            }
            return back()->with('login_error', $errorMsg)->withInput();
        }

        $request->session()->regenerate();
        if($request->user()->role === 'super_admin'){
            return redirect()->route('superadmin.dashboard');
        }
        elseif ($request->user()->role === 'admin') {
            if (!$request->user()->profile()->exists()) {
                return redirect()->route('profiles.create');
            } else {
                return redirect()->route('admin.dashboard');
            }
        }
        elseif($request->user()->role === 'user'){
            return redirect()->intended('user/dashboard');
        }
        // return redirect()->intended(route('dashboard', absolute: false));
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
