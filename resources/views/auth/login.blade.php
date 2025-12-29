@extends('layouts.app')
@section('title', 'Login')
@section('content')
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div
        class="relative min-h-[90vh] sm:min-h-[80vh] w-screen overflow-hidden flex flex-col justify-center items-center py-12">
        <div class="relative z-10 bg-white rounded-lg shadow-lg p-8 md:p-12 w-full max-w-[80%] sm:max-w-md mx-4">
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                        required autofocus autocomplete="username" placeholder="contoh@contoh.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                        autocomplete="current-password" placeholder="**********" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                        <span class="ms-2 text-sm text-gray-600">{{ __('Ingat saya') }}</span>
                    </label>
                </div>

                <div class="flex flex-col items-center gap-4 mt-6">
                    <button type="submit"
                        class="w-full bg-[#3F3142] shadow-lg rounded-lg text-lg text-white hover:bg-[#5C4B5E] px-4 py-2">
                        Masuk
                    </button>

                    {{-- @if (Route::has('password.request'))
                        <a class="text-sm text-gray-600 hover:text-gray-900 hover:underline"
                            href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif --}}

                    <p class="text-center text-sm text-gray-600">
                       Tidak punya akun?
                        <a href="/register" class="font-semibold text-[#3F3142] hover:underline">
                            Daftar disini
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
@endsection
