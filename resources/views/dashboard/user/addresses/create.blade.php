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

            <div class="bg-white rounded-lg shadow-lg p-6">
                <form action="{{ route('user.addresses.store') }}" method="POST" class="space-y-6" x-data="addressForm()">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Alamat</label>
                        <input type="text" name="name" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow"
                            placeholder="Contoh: Rumah" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                        <select name="province" x-model="selectedProvince" @change="fetchCities()" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow">
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach ($provinces as $province)
                                <option value="{{ $province['id'] }}|{{ $province['name'] }}">{{ $province['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                        <select name="city" x-model="selectedCity" @change="fetchDistricts()" :disabled="!cities.length"
                            required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow disabled:bg-gray-100">
                            <option value="">-- Pilih Kota --</option>
                            <template x-for="city in cities" :key="city.id">
                                <option :value="city.id + '|' + city.name" x-text="city.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                        <select name="district" x-model="selectedDistrict" @change="fetchSubDistricts()"
                            :disabled="!districts.length" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow disabled:bg-gray-100">
                            <option value="">Pilih Kecamatan</option>
                            <template x-for="district in districts" :key="district.id">
                                <option :value="district.id + '|' + district.name" x-text="district.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kelurahan</label>
                        <select name="subdistrict" x-model="selectedSubDistrict" :disabled="!subDistricts.length" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow disabled:bg-gray-100">
                            <option value="">Pilih Kelurahan</option>
                            <template x-for="sub in subDistricts" :key="sub.id">
                                <option :value="sub.id + '|' + sub.name" x-text="sub.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                        <input type="text" name="postal_code" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow"
                            placeholder="Masukkan kodepos" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jalan</label>
                        <input type="text" name="extra_detail" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow"
                            placeholder="Nama, Nomor Jalan. Contoh: Jl. Merdeka No. 10" />
                    </div>

                    <div class="flex gap-4">
                        <button type="submit"
                            class="flex-1 bg-[#3F3142] text-white py-3 rounded-lg font-semibold hover:bg-[#5C4B5E]">Simpan</button>
                        <a href="{{ route('user.addresses.index') }}"
                            class="flex-1 text-center border-2 py-3 rounded-lg font-semibold hover:bg-gray-50">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

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

                async fetchCities() {
                    if (!this.selectedProvince) return;
                    const provinceId = this.selectedProvince.split('|')[0];
                    const response = await fetch(`/dashboard/user/addresses/cities/${provinceId}`);
                    this.cities = await response.json();
                    this.districts = [];
                    this.subDistricts = [];
                },

                async fetchDistricts() {
                    if (!this.selectedCity) return;
                    const cityId = this.selectedCity.split('|')[0];
                    const response = await fetch(`/dashboard/user/addresses/districts/${cityId}`);
                    this.districts = await response.json();
                    this.subDistricts = [];
                },

                async fetchSubDistricts() {
                    if (!this.selectedDistrict) return;
                    const districtId = this.selectedDistrict.split('|')[0];
                    const response = await fetch(`/dashboard/user/addresses/sub-districts/${districtId}`);
                    this.subDistricts = await response.json();
                }
            }
        }
    </script>
@endsection
