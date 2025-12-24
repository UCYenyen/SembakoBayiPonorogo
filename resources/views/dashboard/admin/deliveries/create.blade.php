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

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <p class="font-semibold mb-2">⚠️ Validation Error</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-lg p-6">
                <form action="{{ route('user.addresses.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Map -->
                    <div>
                        <label class="block text-sm font-medium mb-2">📍 Select Location on Map</label>
                        <div class="mb-4">
                            <input 
                                type="text" 
                                id="search-input" 
                                placeholder="Search for a place..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142]"
                            >
                        </div>
                        <div id="map" class="w-full h-[400px] rounded-lg border border-gray-300"></div>
                    </div>

                    <!-- Address Detail -->
                    <div>
                        <label for="detail" class="block text-sm font-medium mb-2">
                            Full Address *
                        </label>
                        <textarea 
                            name="detail" 
                            id="detail" 
                            rows="4" 
                            required
                            placeholder="Address will appear here after selecting location on map..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142]"
                        >{{ old('detail') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">You can edit the address if needed</p>
                    </div>

                    <!-- Hidden Coordinates -->
                    <input type="hidden" name="latitude" id="latitude" required>
                    <input type="hidden" name="longitude" id="longitude" required>

                    <!-- Buttons -->
                    <div class="flex gap-4">
                        <button 
                            type="submit" 
                            id="submit-btn"
                            disabled
                            class="flex-1 bg-[#3F3142] text-white py-3 rounded-lg font-semibold hover:bg-[#5C4B5E] disabled:bg-gray-300 disabled:cursor-not-allowed">
                            Save Address
                        </button>
                        <a href="{{ route('user.addresses.index') }}" 
                           class="flex-1 text-center border-2 border-gray-300 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-50">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection
