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

    <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&libraries=places&callback=initMap" async defer></script>

    <script>
        let map, marker, geocoder, autocomplete;

        function initMap() {
            const defaultLocation = { lat: -6.2088, lng: 106.8456 }; // Jakarta default

            map = new google.maps.Map(document.getElementById('map'), {
                center: defaultLocation,
                zoom: 13,
                mapTypeControl: false,
            });

            geocoder = new google.maps.Geocoder();
            
            marker = new google.maps.Marker({
                map: map,
                draggable: true,
                visible: false
            });

            const input = document.getElementById('search-input');
            autocomplete = new google.maps.places.Autocomplete(input, {
                componentRestrictions: { country: 'id' },
                fields: ['geometry', 'formatted_address', 'name']
            });

            // ✅ Place changed from search
            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (!place.geometry) {
                    alert('No details available for: ' + place.name);
                    return;
                }

                map.setCenter(place.geometry.location);
                map.setZoom(17);
                marker.setPosition(place.geometry.location);
                marker.setVisible(true);
                
                updateAddress(place.geometry.location);
            });

            // ✅ Click on map
            map.addListener('click', function(event) {
                marker.setPosition(event.latLng);
                marker.setVisible(true);
                updateAddress(event.latLng);
            });

            // ✅ Drag marker
            marker.addListener('dragend', function(event) {
                updateAddress(event.latLng);
            });

            // ✅ Get user location
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
                        console.log('Geolocation denied or failed');
                    }
                );
            }
        }

        function updateAddress(location) {
            const lat = typeof location.lat === 'function' ? location.lat() : location.lat;
            const lng = typeof location.lng === 'function' ? location.lng() : location.lng;

            console.log('📍 Updating address with coordinates:', { lat, lng });

            // ✅ Set coordinates immediately
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            // Reverse geocode to get address
            geocoder.geocode({ location: { lat, lng } }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    const address = results[0].formatted_address;
                    document.getElementById('detail').value = address;
                    document.getElementById('detail').readOnly = false;
                    
                    console.log('✅ Address updated:', {
                        lat: lat,
                        lng: lng,
                        address: address
                    });
                    
                    // ✅ Enable submit button
                    document.getElementById('submit-btn').disabled = false;
                    
                    // Show success indicator
                    const detailInput = document.getElementById('detail');
                    detailInput.style.borderColor = '#22c55e';
                    setTimeout(() => {
                        detailInput.style.borderColor = '';
                    }, 2000);
                } else {
                    console.error('❌ Geocoding failed:', status);
                    alert('Could not get address for this location. Status: ' + status);
                }
            });
        }

        // ✅ Validate before submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const lat = document.getElementById('latitude').value;
            const lng = document.getElementById('longitude').value;
            const detail = document.getElementById('detail').value;

            console.log('📝 Form submit validation:', { lat, lng, detail });

            if (!lat || !lng || !detail) {
                e.preventDefault();
                alert('Please select a location on the map first!');
                return false;
            }
        });
    </script>
@endsection
