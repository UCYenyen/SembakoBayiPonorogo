<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::updateOrCreate(
                ['google_id' => $googleUser->id],
                [
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'email_verified_at' => now(),
                ]
            );

            Auth::login($user, true);
            
            request()->session()->regenerate();
            
            request()->session()->save();

          
            
            if (!$user->hasCompletedProfile()) {
                return redirect()->route('profile.complete');
            }
            return redirect()->route('dashboard');
            
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Google authentication failed: ' . $e->getMessage());
        }
    }
}