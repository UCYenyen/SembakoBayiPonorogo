@extends('layouts.app')
@section('title', 'Verify Email')

@section('content')
    <div class="relative min-h-[80vh] w-screen overflow-hidden flex flex-col justify-center items-center">
        <div class="bg-white rounded-lg shadow-lg p-8 md:p-12 max-w-md w-full mx-4">
            <div class="text-center mb-6">
                <svg class="w-20 h-20 text-[#3F3142] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                    </path>
                </svg>
                <h2 class="text-2xl font-bold text-[#3F3142] mb-2">Verify Your Email</h2>
            </div>

            <div class="mb-4 text-sm text-gray-600 text-center">
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the 
                link we just emailed to you? If you didn't receive the email, we will gladly send you another.
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg text-center">
                    <p class="font-semibold">✓ Email Sent!</p>
                    <p class="text-sm">A new verification link has been sent to your email address.</p>
                </div>
            @endif

            <div class="mt-6 flex flex-col gap-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" 
                        class="w-full bg-[#3F3142] text-white px-6 py-3 rounded-lg hover:bg-[#5C4B5E] transition-colors font-semibold">
                        Resend Verification Email
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                        class="w-full bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
