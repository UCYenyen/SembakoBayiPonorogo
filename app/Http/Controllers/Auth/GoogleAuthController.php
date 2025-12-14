<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
            // ✅ Stateless() prevents session issues
            $googleUser = Socialite::driver('google')->user();
            
            Log::info('Google OAuth Callback', [
                'google_id' => $googleUser->id,
                'email' => $googleUser->email,
                'name' => $googleUser->name
            ]);

            // ✅ Check if user exists first
            $existingUser = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if ($existingUser) {
                // ✅ Update existing user
                $existingUser->update([
                    'google_id' => $googleUser->id,
                    'name' => $googleUser->name,
                    'avatar' => $googleUser->avatar,
                    'email_verified_at' => $existingUser->email_verified_at ?? now(),
                ]);
                
                $user = $existingUser;
                Log::info('Existing user logged in', ['user_id' => $user->id]);
            } else {
                // ✅ Create new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'email_verified_at' => now(),
                    'role' => 'guest', // ✅ Set default role
                ]);
                
                Log::info('New user created', ['user_id' => $user->id]);
            }

            // ✅ Login user with remember token
            Auth::login($user, true);
            
            // ✅ Regenerate session to prevent fixation
            request()->session()->regenerate();
            
            Log::info('User authenticated', [
                'user_id' => $user->id,
                'session_id' => session()->getId()
            ]);

            // ✅ Check profile completion
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