@extends('layouts.app')
@section('title', 'My Addresses')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[90%] lg:w-[80%] mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-4xl font-bold">Alamatku</h1>
                <a href="{{ route('user.addresses.create') }}" 
                   class="bg-[#3F3142] text-white px-6 py-3 rounded-lg hover:bg-[#5C4B5E] transition-colors font-semibold">
                    + Tambah Alamat Baru
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
            @endif

            @if($addresses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($addresses as $address)
                        <div class="bg-white rounded-lg shadow-md p-6 {{ $address->is_default ? 'border-2 border-[#3F3142]' : '' }}">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    @if($address->is_default)
                                        <span class="inline-block px-3 py-1 bg-[#3F3142] text-white text-xs font-semibold rounded-full mb-2">
                                            Alamat Default
                                        </span>
                                    @endif
                                    <h3 class="text-lg font-bold mb-2">Alamat #{{ $address->id }}</h3>
                                    <p class="text-gray-700 leading-relaxed">{{ $address->detail }}</p>
                                    <p class="text-sm text-gray-600 mt-2">
                                        📍 {{ $address->city_name }}, {{ $address->province }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-2 flex-wrap mt-4 pt-4 border-t">
                                @if(!$address->is_default)
                                    <form action="{{ route('user.addresses.set-default', $address) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm font-semibold">
                                           Jadikan Default
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('user.addresses.edit', $address) }}" 
                                   class="px-4 py-2 border-2 border-[#3F3142] text-[#3F3142] rounded-lg hover:bg-[#3F3142] hover:text-white transition-colors text-sm font-semibold">
                                    Ubah
                                </a>

                                @if($addresses->count() > 1)
                                    <form action="{{ route('user.addresses.destroy', $address) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this address?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="px-4 py-2 border-2 border-red-500 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-colors text-sm font-semibold">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <svg class="w-24 h-24 mx-auto mb-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-700 mb-4">Masih belum ada alamat</h2>
                    <p class="text-gray-500 mb-6">Tambah alamatmu agar produk dapat dikirim ke tujuan!</p>
                    <a href="{{ route('user.addresses.create') }}" 
                       class="inline-block px-8 py-4 bg-[#3F3142] text-white rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors">
                        Tambah Alamat
                    </a>
                </div>
            @endif
        </div>
    </main>
@endsection