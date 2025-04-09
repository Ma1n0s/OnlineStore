<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): Response
    {
        $this->ensureIsNotRateLimited();

        $credentials = $request->only('email', 'password');
        $verificationCode = $request->input('verification_code');

        // Try to authenticate
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            // If authentication fails, check if user exists
            $user = \App\Models\User::where('email', $credentials['email'])->first();
            
            if (!$user) {
                // User doesn't exist, create new user
                $user = \App\Models\User::create([
                    'email' => $credentials['email'],
                    'password' => bcrypt($credentials['password']),
                    'name' => explode('@', $credentials['email'])[0], // Default name from email
                ]);
            }

            // If verification code is provided, verify the user
            if ($verificationCode) {
                if ($user->verification_code === $verificationCode) {
                    $user->email_verified_at = now();
                    $user->verification_code = null;
                    $user->save();
                } else {
                    throw ValidationException::withMessages([
                        'verification_code' => __('Invalid verification code'),
                    ]);
                }
            }

            // Log in the user
            Auth::login($user, $request->boolean('remember'));
        }

        $request->session()->regenerate();
        RateLimiter::clear($this->throttleKey());

        return response()->noContent();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
