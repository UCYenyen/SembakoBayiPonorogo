@extends('layouts.app')
@section('title', 'Login')
@section('content')
    <x-auth-session-status class="mb-4" :status="session('status')" />
    
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="relative min-h-[80vh] w-screen overflow-hidden flex flex-col justify-center items-center">
        <form method="POST" action="{{ route('login') }}" class="bg-white rounded-lg shadow-lg p-12 max-w-[90%]">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autofocus autocomplete="username" placeholder="example@example.com"/>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="current-password" placeholder="**********"/>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex flex-col items-center gap-4 mt-6">
                <button type="submit"
                    class="w-full bg-[#3F3142] shadow-lg rounded-lg text-xl text-white hover:bg-[#5C4B5E] px-4 py-2">
                    Log in
                </button>

                {{-- ✅ Add Google Login Button --}}
                <div class="w-full text-center">
                    <p class="text-gray-500 mb-2">atau</p>
                    <a href="{{ route('auth.google') }}" 
                       class="w-full flex items-center justify-center gap-3 bg-white border-2 border-gray-300 shadow-lg rounded-lg text-lg text-gray-700 hover:bg-gray-50 px-4 py-2">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Login with Google
                    </a>
                </div>

                @if (Route::has('password.request'))
                    <a class="text-sm text-gray-600 hover:text-gray-900 hover:underline"
                        href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <p class="text-center text-sm text-gray-600">
                    Don't have an account?
                    <a href="/register" class="font-semibold text-[#3F3142] hover:underline">
                        Register here
                    </a>
                </p>
            </div>
        </form>
    </div>
@endsection
