@extends('layouts.app')
@section('title', 'Edit Address')
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
                <h1 class="text-3xl font-bold mb-6">Edit Address</h1>

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('user.addresses.update', $address) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

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
                    </div>

                    <!-- Map -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Select Location on Map</label>
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
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent"
                        >{{ old('detail', $address->detail) }}</textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4 pt-4">
                        <button 
                            type="submit" 
                            class="flex-1 bg-[#3F3142] text-white px-6 py-3 rounded-lg hover:bg-[#5C4B5E] transition-colors font-semibold">
                            Update Address
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
            const defaultLocation = { lat: -6.2088, lng: 106.8456 };

            map = new google.maps.Map(document.getElementById('map'), {
                center: defaultLocation,
                zoom: 15,
                mapTypeControl: false,
                streetViewControl: false,
            });

            geocoder = new google.maps.Geocoder();

            marker = new google.maps.Marker({
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP,
            });

            const input = document.getElementById('search-input');
            autocomplete = new google.maps.places.Autocomplete(input, {
                componentRestrictions: { country: 'id' },
                fields: ['geometry', 'formatted_address', 'name']
            });

            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();

                if (!place.geometry) {
                    alert('No details available for: ' + place.name);
                    return;
                }

                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }

                marker.setPosition(place.geometry.location);
                marker.setVisible(true);
                updateAddress(place.geometry.location);
            });

            map.addListener('click', function(event) {
                marker.setPosition(event.latLng);
                marker.setVisible(true);
                updateAddress(event.latLng);
            });

            marker.addListener('dragend', function(event) {
                updateAddress(event.latLng);
            });

            // Try to geocode existing address to show on map
            const existingAddress = document.getElementById('detail').value;
            if (existingAddress) {
                geocoder.geocode({ address: existingAddress }, function(results, status) {
                    if (status === 'OK' && results[0]) {
                        const location = results[0].geometry.location;
                        map.setCenter(location);
                        marker.setPosition(location);
                        marker.setVisible(true);
                    }
                });
            }
        }

        function updateAddress(location) {
            geocoder.geocode({ location: location }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    document.getElementById('detail').value = results[0].formatted_address;
                } else {
                    alert('Cannot get address: ' + status);
                }
            });
        }
    </script>
@endsection