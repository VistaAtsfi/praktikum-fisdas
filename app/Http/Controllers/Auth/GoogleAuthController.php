<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect user ke halaman login Google.
     */
    public function redirect(): RedirectResponse
    {
        /** @var \Laravel\Socialite\Two\AbstractProvider $provider */
        $provider = Socialite::driver('google');

        return $provider
            ->with([
                'prompt' => 'select_account',
            ])
            ->redirect();
    }

    /**
     * Callback setelah user login Google.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                $user->update([
                    'google_id' => $user->google_id ?? $googleUser->getId(),
                    'auth_provider' => $user->auth_provider === 'email' ? 'email_google' : 'google',
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            } else {
                $user = User::create([
                    'name' => 'Pengguna Baru',
                    'email' => $googleUser->getEmail(),
                    'email_verified_at' => now(),
                    'password' => null,
                    'role' => 'praktikan',
                    'google_id' => $googleUser->getId(),
                    'auth_provider' => 'google',
                    'profile_completed' => false,
                    'remember_token' => Str::random(60),
                ]);
            }

            Auth::login($user, true);

            return redirect()->route('dashboard');
        } catch (\Throwable $e) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Login Google gagal. Silakan coba lagi. Detail: ' . $e->getMessage(),
                ]);
        }
    }
}