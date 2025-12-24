<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            Log::info('Google OAuth Callback', [
                'google_id' => $googleUser->id,
                'email' => $googleUser->email,
                'name' => $googleUser->name
            ]);

            $existingUser = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if ($existingUser) {
                $existingUser->update([
                    'google_id' => $googleUser->id,
                    'name' => $googleUser->name,
                    'avatar' => $googleUser->avatar,
                    'email_verified_at' => $existingUser->email_verified_at ?? now(),
                ]);
                
                $user = $existingUser;
                Log::info('Existing user logged in', ['user_id' => $user->id]);
            } else {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'email_verified_at' => now(),
                    'role' => 'guest', 
                ]);
                
                Log::info('New user created', ['user_id' => $user->id]);
            }

            Auth::login($user, true);
            
            request()->session()->regenerate();
            
            Log::info('User authenticated', [
                'user_id' => $user->id,
                'session_id' => session()->getId()
            ]);

            if (!$user->hasCompletedProfile()) {
                Log::info('Redirecting to complete profile', ['user_id' => $user->id]);
                return redirect()->route('profile.complete');
            }
            
            Log::info('Redirecting to dashboard', ['user_id' => $user->id]);
            return redirect()->route('dashboard');
            
        } catch (\Exception $e) {
            Log::error('Google OAuth Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect('/')
                ->with('error', 'Google authentication failed. Please try again.');
        }
    }
}