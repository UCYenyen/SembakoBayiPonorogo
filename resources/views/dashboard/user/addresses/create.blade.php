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
                <form action="{{ route('user.addresses.store') }}" method="POST" class="space-y-6" x-data="addressForm()">
                    @csrf

                    <!-- ✅ Step 1: Google Maps - Pick Location -->
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

                    <!-- ✅ Step 2: Komerce - Select Subdistrict -->
                    <div x-show="mapLocationSelected">
                        <label class="block text-sm font-medium mb-2">
                            🏙️ Step 2: Select Precise Subdistrict from Komerce
                        </label>
                        
                        <div class="bg-blue-50 border border-blue-200 p-3 rounded-lg mb-3">
                            <p class="text-sm text-blue-800">
                                📌 Location from map: <span class="font-semibold" x-text="mapLocationName"></span>
                            </p>
                        </div>

                        <div class="relative">
                            <input 
                                type="text" 
                                x-model="komerceQuery"
                                @input.debounce.300ms="searchKomerce()"
                                placeholder="Type to search subdistrict in Komerce..."
                                class="w-full px-4 py-2 border rounded-lg"
                                autocomplete="off"
                            >
                            
                            <!-- Komerce Results Dropdown -->
                            <div x-show="komerceOpen && komerceResults.length > 0" 
                                 @click.away="komerceOpen = false"
                                 class="absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                <template x-for="location in komerceResults" :key="location.subdistrict_id">
                                    <button type="button"
                                            @click="selectKomerce(location)"
                                            class="w-full text-left px-4 py-2 hover:bg-gray-100 border-b">
                                        <p class="font-semibold text-sm" x-text="location.subdistrict_name"></p>
                                        <p class="text-xs text-gray-500" x-text="`${location.district}, ${location.city}, ${location.province}`"></p>
                                    </button>
                                </template>
                            </div>

                            <!-- No Results -->
                            <div x-show="komerceOpen && komerceQuery.length >= 2 && komerceResults.length === 0"
                                 class="absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg p-4">
                                <p class="text-gray-500 text-center text-sm">No subdistricts found</p>
                            </div>
                        </div>

                        <!-- Selected Komerce Location -->
                        <div x-show="komerceSelected" class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-sm text-green-800">
                                ✅ Selected: <span class="font-semibold" x-text="selectedLocationName"></span>
                            </p>
                        </div>

                        <!-- Hidden Input -->
                        <input type="hidden" name="subdistrict_id" x-model="selectedSubdistrictId" required>
                    </div>

                    <!-- ✅ Step 3: Full Address Detail -->
                    <div>
                        <label for="detail" class="block text-sm font-medium mb-2">
                            📝 Step 3: Full Address Detail *
                        </label>
                        <textarea 
                            name="detail" 
                            id="detail" 
                            rows="4" 
                            required
                            x-model="addressDetail"
                            placeholder="Enter complete address (street, RT/RW, kelurahan, kecamatan)"
                            class="w-full px-4 py-2 border rounded-lg"
                        ></textarea>
                        <p class="text-xs text-gray-500 mt-1">This will be pre-filled from map, but you can edit it</p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4">
                        <button 
                            type="submit" 
                            :disabled="!komerceSelected"
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

    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initMap" async defer></script>

    <script>
        let map, marker, geocoder, autocomplete;

        function initMap() {
            const defaultLocation = { lat: -7.8753, lng: 111.4638 }; // Ponorogo

            map = new google.maps.Map(document.getElementById('map'), {
                center: defaultLocation,
                zoom: 13,
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
            });

            // Place changed from search
            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (!place.geometry) return;

                map.setCenter(place.geometry.location);
                map.setZoom(17);
                marker.setPosition(place.geometry.location);
                marker.setVisible(true);
                
                updateFromMap(place.geometry.location, place.formatted_address);
            });

            // Click on map
            map.addListener('click', function(event) {
                marker.setPosition(event.latLng);
                marker.setVisible(true);
                
                geocoder.geocode({ location: event.latLng }, function(results, status) {
                    if (status === 'OK' && results[0]) {
                        updateFromMap(event.latLng, results[0].formatted_address);
                    }
                });
            });

            // Drag marker
            marker.addListener('dragend', function(event) {
                geocoder.geocode({ location: event.latLng }, function(results, status) {
                    if (status === 'OK' && results[0]) {
                        updateFromMap(event.latLng, results[0].formatted_address);
                    }
                });
            });
        }

        function updateFromMap(location, formattedAddress) {
            // Extract city/subdistrict from Google Maps result
            geocoder.geocode({ location: location }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    const addressComponents = results[0].address_components;
                    let cityName = '';
                    let subdistrictName = '';

                    // Extract locality or administrative_area
                    addressComponents.forEach(component => {
                        if (component.types.includes('locality') || 
                            component.types.includes('administrative_area_level_2')) {
                            cityName = component.long_name;
                        }
                        if (component.types.includes('sublocality_level_1') || 
                            component.types.includes('administrative_area_level_3')) {
                            subdistrictName = component.long_name;
                        }
                    });

                    // Trigger Alpine.js to update
                    const event = new CustomEvent('map-location-selected', {
                        detail: {
                            cityName: cityName || subdistrictName,
                            formattedAddress: formattedAddress
                        }
                    });
                    window.dispatchEvent(event);
                }
            });
        }

        function addressForm() {
            return {
                // Map data
                mapLocationSelected: false,
                mapLocationName: '',
                addressDetail: '',

                // Komerce data
                komerceQuery: '',
                komerceResults: [],
                komerceOpen: false,
                komerceSelected: false,
                selectedSubdistrictId: null,
                selectedLocationName: '',

                init() {
                    // Listen to map selection
                    window.addEventListener('map-location-selected', (e) => {
                        this.mapLocationSelected = true;
                        this.mapLocationName = e.detail.cityName;
                        this.addressDetail = e.detail.formattedAddress;
                        
                        // Auto-search Komerce with city name
                        this.komerceQuery = e.detail.cityName;
                        this.searchKomerce();
                    });
                },

                async searchKomerce() {
                    if (this.komerceQuery.length < 2) {
                        this.komerceResults = [];
                        this.komerceOpen = false;
                        return;
                    }

                    try {
                        const response = await fetch(`/api/cities/search?q=${encodeURIComponent(this.komerceQuery)}`);
                        const data = await response.json();
                        this.komerceResults = data.slice(0, 10);
                        this.komerceOpen = true;
                    } catch (error) {
                        console.error('Komerce search error:', error);
                    }
                },

                selectKomerce(location) {
                    this.selectedSubdistrictId = location.subdistrict_id;
                    this.selectedLocationName = `${location.subdistrict_name}, ${location.district}, ${location.city}, ${location.province}`;
                    this.komerceSelected = true;
                    this.komerceOpen = false;
                }
            }
        }
    </script>
@endsection