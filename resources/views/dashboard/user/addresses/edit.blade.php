@extends('layouts.app')
@section('title', 'Edit Address')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[90%] lg:w-[70%] mx-auto">
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('user.addresses.index') }}" class="text-[#3F3142] hover:text-[#5C4B5E]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-4xl font-bold">Edit Alamat</h1>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-lg p-6">
                <form action="{{ route('user.addresses.update', $address) }}" method="POST" class="space-y-6"
                    x-data="addressEditForm({
                        oldProvince: '{{ $address->province_id }}',
                        oldProvinceName: '{{ $address->province_name }}',
                        oldCity: '{{ $address->city_id }}',
                        oldCityName: '{{ $address->city_name }}',
                        oldDistrict: '{{ $address->district_id }}',
                        oldDistrictName: '{{ $address->district_name }}',
                        oldSubDistrict: '{{ $address->subdistrict_id }}',
                        oldSubDistrictName: '{{ $address->subdistrict_name }}'
                    })"
                    x-init="initEdit()">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Alamat</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $address->name) }}" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label for="province" class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                        <select id="province" name="province" x-model="selectedProvince" @change="fetchCities()" required
                            class="mt-1 block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach ($provinces as $province)
                                <option value="{{ $province['id'] }}|{{ $province['name'] }}">{{ $province['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                        <select id="city" name="city" x-model="selectedCity" @change="fetchDistricts()" :disabled="!cities.length" required
                            class="mt-1 block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md shadow-sm disabled:bg-gray-100">
                            <option value="">-- Pilih Kota --</option>
                            <template x-for="city in cities" :key="city.id">
                                <option :value="city.id + '|' + city.name" x-text="city.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label for="district" class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                        <select id="district" name="district" x-model="selectedDistrict" @change="fetchSubDistricts()" :disabled="!districts.length" required
                            class="mt-1 block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md shadow-sm disabled:bg-gray-100">
                            <option value="">-- Pilih Kecamatan --</option>
                            <template x-for="district in districts" :key="district.id">
                                <option :value="district.id + '|' + district.name" x-text="district.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label for="subdistrict" class="block text-sm font-medium text-gray-700 mb-1">Kelurahan</label>
                        <select id="subdistrict" name="subdistrict" x-model="selectedSubDistrict" :disabled="!subDistricts.length" required
                            class="mt-1 block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md shadow-sm disabled:bg-gray-100">
                            <option value="">-- Pilih Kelurahan --</option>
                            <template x-for="sd in subDistricts" :key="sd.id">
                                <option :value="sd.id + '|' + sd.name" x-text="sd.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $address->postal_code) }}" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label for="extra_detail" class="block text-sm font-medium text-gray-700 mb-1">Jalan</label>
                        <input type="text" id="extra_detail" name="extra_detail" value="{{ old('extra_detail', $address->extra_detail) }}" required
                            placeholder="Nama, Nomor Jalan. Contoh: Jl. Merdeka No. 10"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 bg-[#3F3142] text-white py-3 rounded-lg font-semibold hover:bg-[#5C4B5E]">
                            Ubah Alamat
                        </button>
                        <a href="{{ route('user.addresses.index') }}" class="flex-1 text-center border-2 py-3 rounded-lg font-semibold hover:bg-gray-50">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        function addressEditForm(initialData) {
            return {
                selectedProvince: '',
                selectedCity: '',
                selectedDistrict: '',
                selectedSubDistrict: '',
                cities: [],
                districts: [],
                subDistricts: [],

                async initEdit() {
                    // Set province dengan format id|name
                    if (initialData.oldProvince && initialData.oldProvinceName) {
                        this.selectedProvince = initialData.oldProvince + '|' + initialData.oldProvinceName;
                        await this.fetchCities(true);
                        
                        // Set city dengan format id|name setelah cities loaded
                        if (initialData.oldCity && initialData.oldCityName) {
                            this.selectedCity = initialData.oldCity + '|' + initialData.oldCityName;
                            await this.fetchDistricts(true);
                            
                            // Set district dengan format id|name setelah districts loaded
                            if (initialData.oldDistrict && initialData.oldDistrictName) {
                                this.selectedDistrict = initialData.oldDistrict + '|' + initialData.oldDistrictName;
                                await this.fetchSubDistricts(true);
                                
                                // Set subdistrict dengan format id|name setelah subdistricts loaded
                                if (initialData.oldSubDistrict && initialData.oldSubDistrictName) {
                                    this.selectedSubDistrict = initialData.oldSubDistrict + '|' + initialData.oldSubDistrictName;
                                }
                            }
                        }
                    }
                },

                async fetchCities(isInit = false) {
                    if (!this.selectedProvince) return;
                    const provinceId = this.selectedProvince.split('|')[0];
                    try {
                        const res = await fetch(`/dashboard/user/addresses/cities/${provinceId}`);
                        this.cities = await res.json();
                        
                        if (!isInit) {
                            this.selectedCity = '';
                            this.districts = [];
                            this.subDistricts = [];
                        }
                    } catch (e) { 
                        console.error(e); 
                    }
                },

                async fetchDistricts(isInit = false) {
                    if (!this.selectedCity) return;
                    const cityId = this.selectedCity.split('|')[0];
                    try {
                        const res = await fetch(`/dashboard/user/addresses/districts/${cityId}`);
                        this.districts = await res.json();
                        
                        if (!isInit) {
                            this.selectedDistrict = '';
                            this.subDistricts = [];
                        }
                    } catch (e) { 
                        console.error(e); 
                    }
                },

                async fetchSubDistricts(isInit = false) {
                    if (!this.selectedDistrict) return;
                    const districtId = this.selectedDistrict.split('|')[0];
                    try {
                        const res = await fetch(`/dashboard/user/addresses/sub-districts/${districtId}`);
                        this.subDistricts = await res.json();
                        
                        if (!isInit) {
                            this.selectedSubDistrict = '';
                        }
                    } catch (e) { 
                        console.error(e); 
                    }
                }
            }
        }
    </script>
@endsection