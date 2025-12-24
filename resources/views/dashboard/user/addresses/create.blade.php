@extends('layouts.app')
@section('title', 'Add New Address')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[90%] lg:w-[70%] mx-auto">
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('user.addresses.index') }}" class="text-[#3F3142] hover:text-[#5C4B5E]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-4xl font-bold">Add New Address</h1>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <form action="{{ route('user.addresses.store') }}" method="POST" class="space-y-6" x-data="addressForm()">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium mb-2">
                            📍 Step 1: Select Location on Map
                        </label>
                        <input 
                            type="text" 
                            id="search-input" 
                            placeholder="Search for a place..."
                            class="w-full px-4 py-2 mb-2 border rounded-lg"
                        >
                        <div id="map" class="w-full h-[400px] rounded-lg border"></div>
                        <p class="text-xs text-gray-500 mt-2">Click on map or search to select your location</p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4">
                        <button 
                            type="submit" 
                            class="flex-1 bg-[#3F3142] text-white py-3 rounded-lg font-semibold hover:bg-[#5C4B5E] disabled:bg-gray-300 disabled:cursor-not-allowed">
                            Save Address
                        </button>
                        <a href="{{ route('user.addresses.index') }}" 
                           class="flex-1 text-center border-2 py-3 rounded-lg font-semibold hover:bg-gray-50">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection