@extends('layouts.app')
@section('title', 'Add New Address')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[80%] mx-auto">
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('user.addresses.index') }}" class="text-[#3F3142] hover:text-[#5C4B5E]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-4xl font-bold">Tambah Alamat Baru</h1>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <p class="font-semibold mb-2">⚠️ Kesalahan Validasi</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-lg p-6">
                <form action="{{ route('user.addresses.store') }}" method="POST" class="space-y-6" x-data="addressForm()">
                    @csrf
                    <div>
                        <label for="province" class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                        <select id="province" name="province_id" x-model="selectedProvince" @change="fetchCities()"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base bg-white border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow">
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach ($provinces as $province)
                                <option value="{{ $province['id'] }}">{{ $province['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                        <select id="city" name="city_id" x-model="selectedCity" @change="fetchDistricts()"
                            {{-- TAMBAHKAN INI --}} :disabled="!cities.length"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base bg-white border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow disabled:bg-gray-100">
                            <option value="">-- Pilih Kota --</option>
                            <template x-for="city in cities" :key="city.id">
                                <option :value="city.id" x-text="city.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label for="district" class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                        <select id="district" name="district_id" x-model="selectedDistrict" @change="fetchSubDistricts()"
                            {{-- TAMBAHKAN INI --}} :disabled="!districts.length"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base bg-white border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow disabled:bg-gray-100">
                            <option value="">-- Pilih Kecamatan --</option>
                            <template x-for="district in districts" :key="district.id">
                                <option :value="district.id" x-text="district.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label for="sub-district" class="block text-sm font-medium text-gray-700 mb-1">Kelurahan</label>
                        <select id="sub-district" name="sub_district_id" x-model="selectedSubDistrict"
                            :disabled="!subDistricts.length"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base bg-white border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow disabled:bg-gray-100">
                            <option value="">-- Pilih Kelurahan --</option>
                            <template x-for="subDistrict in subDistricts" :key="subDistrict.id">
                                <option :value="subDistrict.id" x-text="subDistrict.name"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4">
                        <button type="submit" :disabled="!komerceSelected"
                            class="flex-1 bg-[#3F3142] text-white py-3 rounded-lg font-semibold hover:bg-[#5C4B5E] disabled:bg-gray-300 disabled:cursor-not-allowed">
                            Simpan Alamat
                        </button>
                        <a href="{{ route('user.addresses.index') }}"
                            class="flex-1 text-center border-2 py-3 rounded-lg font-semibold hover:bg-gray-50">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection
<script>
    function addressForm() {
        return {
            selectedProvince: '',
            selectedCity: '',
            selectedDistrict: '',
            selectedSubDistrict: '',
            cities: [],
            districts: [],
            subDistricts: [],
            komerceSelected: true,

            async fetchCities() {
                if (!this.selectedProvince) {
                    this.cities = [];
                    this.districts = [];
                    this.subDistricts = [];
                    return;
                }
                try {
                    const response = await fetch(`/dashboard/user/addresses/cities/${this.selectedProvince}`);
                    this.cities = await response.json();
                    this.selectedCity = '';
                    this.districts = [];
                    this.subDistricts = [];
                } catch (error) {
                    console.error('Error fetching cities:', error);
                }
            }, // <-- WAJIB ADA KOMA DI SINI

            async fetchDistricts() {
                if (!this.selectedCity) {
                    this.districts = [];
                    this.subDistricts = [];
                    return;
                }
                try {
                    const response = await fetch(`/dashboard/user/addresses/districts/${this.selectedCity}`);
                    this.districts = await response.json();
                    this.selectedDistrict = '';
                    this.subDistricts = [];
                } catch (error) {
                    console.error('Error fetching districts:', error);
                }
            }, // <-- WAJIB ADA KOMA DI SINI

            async fetchSubDistricts() {
                if (!this.selectedDistrict) {
                    this.subDistricts = [];
                    return;
                }
                try {
                    const response = await fetch(`/dashboard/user/addresses/sub-districts/${this.selectedDistrict}`);
                    this.subDistricts = await response.json();
                    this.selectedSubDistrict = '';
                } catch (error) {
                    console.error('Error fetching sub-districts:', error);
                }
            }
        }
    }
</script>