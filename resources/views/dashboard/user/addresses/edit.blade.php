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

            <div class="bg-white rounded-lg shadow-lg p-6">
                <form action="{{ route('user.addresses.update', $address) }}" method="POST" class="space-y-6" 
                      x-data="addressEditForm({{ $address->subdistrict_id }}, '{{ $address->subdistrict_name }}')">
                    @csrf
                    @method('PUT')
                    <!-- Buttons -->
                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 bg-[#3F3142] text-white py-3 rounded-lg font-semibold">
                            Ubah Alamat
                        </button>
                        <a href="{{ route('user.addresses.index') }}" class="flex-1 text-center border-2 py-3 rounded-lg font-semibold">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        function addressEditForm(initialId, initialName) {
            return {
                komerceQuery: initialName || '',
                komerceResults: [],
                komerceOpen: false,
                selectedSubdistrictId: initialId || null,
                selectedLocationName: initialName || '',
                komerceSelected: !!initialId,

                async searchKomerce() {
                    if (this.komerceQuery.length < 2) return;

                    try {
                        const response = await fetch(`/api/cities/search?q=${encodeURIComponent(this.komerceQuery)}`);
                        const data = await response.json();
                        this.komerceResults = data.slice(0, 10);
                        this.komerceOpen = true;
                    } catch (error) {
                        console.error(error);
                    }
                },

                selectKomerce(location) {
                    this.selectedSubdistrictId = location.subdistrict_id;
                    this.selectedLocationName = `${location.subdistrict_name}, ${location.city}, ${location.province}`;
                    this.komerceSelected = true;
                    this.komerceOpen = false;
                }
            }
        }
    </script>
@endsection