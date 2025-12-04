@extends('layouts.app')
@section('title', 'Unauthorized')

@section('content')
    <main class="min-h-screen w-screen flex flex-col items-center justify-center bg-gray-100 px-4">
        <div class="bg-white rounded-lg shadow-lg p-8 md:p-12 max-w-md w-full text-center">
            <svg class="w-24 h-24 text-red-500 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Access Denied</h1>
            
            <p class="text-gray-600 mb-6">
                You do not have permission to access this page.
            </p>
            
            <a href="/" class="inline-block bg-interactible-primary-active text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                Return to Home
            </a>
        </div>
    </main>
@endsection
