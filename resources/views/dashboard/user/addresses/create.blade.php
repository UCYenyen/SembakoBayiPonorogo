@extends('layouts.app')
@section('title', 'Add New Address')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[90%] lg:w-[70%] mx-auto">
            <a href="{{ route('user.addresses.index') }}" 
               class="inline-flex items-center gap-2 mb-6 text-[#3F3142] hover:underline">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Addresses
            </a>

            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl font-bold mb-6">Add New Address</h1>

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('user.addresses.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Search Box -->
                    <div>
                        <label for="search-input" class="block text-sm font-medium mb-2">
                            Search Location
                        </label>
                        <input 
                            id="search-input" 
                            type="text" 
                            placeholder="Search for your address..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent"
                        />
                        <p class="text-xs text-gray-500 mt-1">Type to search or click on the map to select your location</p>
                    </div>

                    <!-- Map -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Select Location on Map</label>
                        <div id="map" class="w-full h-[400px] rounded-lg border border-gray-300"></div>
                    </div>

                    <!-- Address Detail (Auto-filled) -->
                    <div>
                        <label for="detail" class="block text-sm font-medium mb-2">
                            Full Address *
                        </label>
                        <textarea 
                            name="detail" 
                            id="detail" 
                            rows="4" 
                            required
                            readonly
                            placeholder="Address will appear here after selecting location on map..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent bg-gray-50"
                        >{{ old('detail') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">You can edit the address if needed</p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4 pt-4">
                        <button 
                            type="submit" 
                            id="submit-btn"
                            disabled
                            class="flex-1 bg-[#3F3142] text-white px-6 py-3 rounded-lg hover:bg-[#5C4B5E] transition-colors font-semibold disabled:bg-gray-300 disabled:cursor-not-allowed">
                            Save Address
                        </button>
                        <a href="{{ route('user.addresses.index') }}" 
                           class="flex-1 bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors font-semibold text-center">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Google Maps Script -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&libraries=places&callback=initMap" async defer></script>

    <script>
        let map;
        let marker;
        let geocoder;
        let autocomplete;

        function initMap() {
            // Default location (Indonesia)
            const defaultLocation = { lat: -6.2088, lng: 106.8456 };

            // Initialize map
            map = new google.maps.Map(document.getElementById('map'), {
                center: defaultLocation,
                zoom: 15,
                mapTypeControl: false,
                streetViewControl: false,
            });

            // Initialize geocoder
            geocoder = new google.maps.Geocoder();

            // Initialize marker
            marker = new google.maps.Marker({
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP,
            });

            // Initialize autocomplete
            const input = document.getElementById('search-input');
            autocomplete = new google.maps.places.Autocomplete(input, {
                componentRestrictions: { country: 'id' }, // Restrict to Indonesia
                fields: ['geometry', 'formatted_address', 'name']
            });

            // Handle autocomplete selection
            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();

                if (!place.geometry) {
                    alert('No details available for: ' + place.name);
                    return;
                }

                // Center map on selected place
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }

                // Update marker position
                marker.setPosition(place.geometry.location);
                marker.setVisible(true);

                // Update address field
                updateAddress(place.geometry.location);
            });

            // Click on map to set marker
            map.addListener('click', function(event) {
                marker.setPosition(event.latLng);
                marker.setVisible(true);
                updateAddress(event.latLng);
            });

            // Drag marker to update address
            marker.addListener('dragend', function(event) {
                updateAddress(event.latLng);
            });

            // Try to get user's current location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const userLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };

                        map.setCenter(userLocation);
                        marker.setPosition(userLocation);
                        marker.setVisible(true);
                        updateAddress(userLocation);
                    },
                    function() {
                        console.log('Geolocation permission denied');
                    }
                );
            }
        }

        function updateAddress(location) {
            geocoder.geocode({ location: location }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    const address = results[0].formatted_address;
                    document.getElementById('detail').value = address;
                    document.getElementById('detail').readOnly = false; // Allow manual edit
                    document.getElementById('submit-btn').disabled = false;
                } else {
                    alert('Cannot get address: ' + status);
                }
            });
        }
    </script>
@endsection