@extends('layouts.app')
@section('title', 'Testimony Detail')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[80%] mx-auto">
            <div class="flex justify-between items-center mb-6 bg-white shadow-lg rounded-lg px-6 py-4">
                <div class="flex gap-4 justify-center items-center">
                    <a href="{{ route('dashboard') }}" 
                        class="text-white bg-[#3F3142] hover:bg-[#5C4B5E] transition-colors rounded-full p-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-4xl font-bold">Detail Ulasan</h1>
                </div>
            </div>

            <div class="bg-white shadow-lg rounded-lg px-6 py-6 mb-8">
                {{-- Product Info --}}
                <div class="flex gap-4 mb-6 pb-6 border-b">
                    <img src="{{ $testimony->transactionItem->product->image_path }}"
                        alt="{{ $testimony->transactionItem->product->name }}"
                        class="w-24 h-24 object-cover rounded-lg">
                    <div>
                        <h2 class="text-2xl font-bold mb-2">{{ $testimony->transactionItem->product->name }}</h2>
                        <p class="text-gray-600">Transaksi #{{ $testimony->transactionItem->transaction_id }}</p>
                    </div>
                </div>

                {{-- Rating --}}
                <div class="mb-6">
                    <label class="block text-lg font-medium mb-2">Rating</label>
                    <div class="flex items-center gap-1">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="w-8 h-8 {{ $i <= $testimony->rating_star ? 'text-yellow-400' : 'text-gray-300' }}" 
                                fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966c.3.922-.755 1.688-1.54 1.118l-3.38-2.454a1 1 0 00-1.175 0l-3.38 2.454c-.784.57-1.838-.196-1.54-1.118l1.287-3.966a1 1 0 00-.364-1.118L2.05 9.394c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69l1.286-3.967z" />
                            </svg>
                        @endfor
                        <span class="ml-2 text-xl font-bold">{{ $testimony->rating_star }}/5</span>
                    </div>
                </div>

                {{-- Review Content --}}
                <div class="mb-6">
                    <label class="block text-lg font-medium mb-2">Ulasan</label>
                    <p class="text-gray-700 whitespace-pre-wrap bg-gray-50 p-4 rounded-lg">{{ $testimony->description }}</p>
                </div>

                {{-- Media --}}
                @if($testimony->images->count() > 0)
                    <div class="mb-6">
                        <label class="block text-lg font-medium mb-2">Foto/Video</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($testimony->images as $image)
                                @php
                                    $extension = pathinfo($image->image_url, PATHINFO_EXTENSION);
                                    $isVideo = in_array(strtolower($extension), ['mp4', 'avi', 'mov', 'webm']);
                                @endphp
                                
                                @if($isVideo)
                                    <video controls class="w-full h-48 object-cover rounded-lg border">
                                        <source src="{{ asset('storage/' . $image->image_url) }}" type="video/{{ $extension }}">
                                    </video>
                                @else
                                    <img src="{{ asset('storage/' . $image->image_url) }}" 
                                        alt="Testimony image"
                                        class="w-full h-48 object-cover rounded-lg border cursor-pointer hover:opacity-90"
                                        onclick="openImageModal(this.src)">
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="flex gap-4 pt-6 border-t">
                    <a href="{{ route('user.testimonies.edit', $testimony) }}"
                        class="px-6 py-2 bg-[#3F3142] text-white rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors">
                        Edit Ulasan
                    </a>
                    
                    <form action="{{ route('user.testimonies.destroy', $testimony) }}" 
                        method="POST"
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus ulasan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-6 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition-colors">
                            Hapus Ulasan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    {{-- Image Modal --}}
    <div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" onclick="closeImageModal()">
        <img id="modalImage" src="" alt="Full size" class="max-w-full max-h-full object-contain">
    </div>

    <script>
        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }
    </script>
@endsection