@extends('layouts.app')
@section('title', 'Home')
@section('content')
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <div class="relative min-h-screen w-screen overflow-hidden flex flex-col justify-center items-center">
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
                {{-- @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif --}}
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center flex-col justify-end mt-4 gap-4">
                <Button type="submit"
                    class="w-full bg-interactible-primary-active text-white hover:text-interactible-primary-active font-semibold py-2 rounded-lg hover:bg-zinc-50 border border-interactible-primary-active">Login
                </Button>
                 <p class="text-center text-sm text-gray-600">
                       Don't have an account?
                        <a href="/register"
                            class="font-semibold text-interactible-primary-active hover:underline">
                           Register here
                        </a>
                    </p>
            </div>
        </form>
    </div>
@endsection
