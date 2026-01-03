@extends('layouts.app')
@section('title', 'Edit Testimony')
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
                    <h1 class="text-4xl font-bold">Edit Ulasan</h1>
                </div>
            </div>

            {{-- Product Info --}}
            <div class="bg-white shadow-lg rounded-lg px-6 py-4 mb-6">
                <div class="flex gap-4">
                    <img src="{{ $testimony->transactionItem->product->image_path }}"
                        alt="{{ $testimony->transactionItem->product->name }}"
                        class="w-20 h-20 object-cover rounded-lg">
                    <div>
                        <h2 class="text-xl font-bold">{{ $testimony->transactionItem->product->name }}</h2>
                        <p class="text-gray-600">Transaksi #{{ $testimony->transactionItem->transaction_id }}</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('user.testimonies.update', $testimony) }}" 
                method="POST" 
                class="bg-white shadow-lg rounded-lg px-6 py-4 mb-8" 
                enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                
                {{-- Rating --}}
                <div class="mb-4">
                    <label class="block text-lg font-medium mb-2">Rating</label>
                    <div class="flex items-center gap-1" id="star-rating">
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button" class="star focus:outline-none" data-value="{{ $i }}">
                                <svg class="w-8 h-8 {{ $i <= $testimony->rating_star ? 'text-yellow-400' : 'text-gray-300' }}" 
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966c.3.922-.755 1.688-1.54 1.118l-3.38-2.454a1 1 0 00-1.175 0l-3.38 2.454c-.784.57-1.838-.196-1.54-1.118l1.287-3.966a1 1 0 00-.364-1.118L2.05 9.394c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69l1.286-3.967z" />
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" id="rating_star" name="rating_star" value="{{ $testimony->rating_star }}">
                </div>

                {{-- Review Content --}}
                <div class="mb-4">
                    <label for="description" class="block text-lg font-medium mb-2">Ulasan Anda</label>
                    <textarea id="description" name="description" rows="5" required 
                        class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-[#3F3142]">{{ $testimony->description }}</textarea>
                </div>

                {{-- Existing Media --}}
                @if($testimony->images->count() > 0)
                    <div class="mb-4">
                        <label class="block text-lg font-medium mb-2">Media Yang Ada</label>
                        <div class="flex flex-wrap gap-3 mb-2" id="existing-media">
                            @foreach($testimony->images as $image)
                                @php
                                    $extension = pathinfo($image->image_url, PATHINFO_EXTENSION);
                                    $isVideo = in_array(strtolower($extension), ['mp4', 'avi', 'mov', 'webm']);
                                @endphp
                                
                                <div class="relative" data-image-id="{{ $image->id }}">
                                    @if($isVideo)
                                        <video class="w-24 h-24 object-cover rounded-lg border">
                                            <source src="{{ asset('storage/' . $image->image_url) }}" type="video/{{ $extension }}">
                                        </video>
                                    @else
                                        <img src="{{ asset('storage/' . $image->image_url) }}" 
                                            alt="Testimony image"
                                            class="w-24 h-24 object-cover rounded-lg border">
                                    @endif
                                    <button type="button" 
                                        onclick="deleteExistingMedia({{ $image->id }})"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                        &times;
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- New Media Upload --}}
                <div class="mb-4">
                    <label class="block text-lg font-medium mb-2">Tambah Foto/Video Baru</label>
                    <div id="media-preview" class="flex flex-wrap gap-3 mb-3"></div>
                    
                    <label for="media-input" class="cursor-pointer inline-flex items-center justify-center gap-2 px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="text-gray-600">Tambah File</span>
                    </label>
                    <input id="media-input" type="file" class="hidden" multiple accept="image/jpeg,image/png,image/jpg,image/gif,image/webp,video/mp4,video/avi,video/mov,video/webm">
                    
                    <p class="text-sm text-gray-500 mt-2">Format: JPG, PNG, GIF, WEBP, MP4, AVI, MOV (Max 50MB per file)</p>
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-4">
                    <button type="submit" 
                        class="px-6 py-2 bg-[#3F3142] text-white rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors">
                        Update Ulasan
                    </button>
                    <a href="{{ route('dashboard') }}"
                        class="px-6 py-2 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Star Rating
            const stars = document.querySelectorAll('#star-rating .star');
            const ratingInput = document.getElementById('rating_star');
            let currentRating = parseInt(ratingInput.value);

            function setRating(rating) {
                stars.forEach((star, idx) => {
                    star.querySelector('svg').classList.toggle('text-yellow-400', idx < rating);
                    star.querySelector('svg').classList.toggle('text-gray-300', idx >= rating);
                });
                ratingInput.value = rating;
                currentRating = rating;
            }

            stars.forEach((star, idx) => {
                star.addEventListener('click', (e) => { 
                    e.preventDefault(); 
                    setRating(idx + 1); 
                });
                star.addEventListener('mouseover', () => setRating(idx + 1));
                star.addEventListener('mouseout', () => setRating(currentRating));
            });

            // Multiple Files Upload System
            const mediaInput = document.getElementById('media-input');
            const previewContainer = document.getElementById('media-preview');
            const form = document.querySelector('form');
            let filesArray = [];

            mediaInput.addEventListener('change', function(e) {
                const newFiles = Array.from(e.target.files);
                
                // Add new files to array
                newFiles.forEach(file => {
                    // Check file size (50MB)
                    if (file.size > 50 * 1024 * 1024) {
                        alert(`File ${file.name} terlalu besar (max 50MB)`);
                        return;
                    }
                    
                    filesArray.push(file);
                });
                
                // Clear input
                mediaInput.value = '';
                
                // Update preview
                updatePreview();
            });

            function updatePreview() {
                previewContainer.innerHTML = '';
                
                filesArray.forEach((file, index) => {
                    const url = URL.createObjectURL(file);
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative group';
                    
                    // Create media element
                    let mediaElement;
                    if (file.type.startsWith('image/')) {
                        mediaElement = document.createElement('img');
                        mediaElement.src = url;
                        mediaElement.className = 'w-32 h-32 object-cover rounded-lg border-2 border-gray-200';
                    } else if (file.type.startsWith('video/')) {
                        mediaElement = document.createElement('video');
                        mediaElement.src = url;
                        mediaElement.className = 'w-32 h-32 object-cover rounded-lg border-2 border-gray-200';
                        mediaElement.muted = true;
                    }
                    
                    // Delete button
                    const deleteBtn = document.createElement('button');
                    deleteBtn.type = 'button';
                    deleteBtn.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    `;
                    deleteBtn.className = 'absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity';
                    deleteBtn.onclick = function() {
                        filesArray.splice(index, 1);
                        updatePreview();
                    };
                    
                    // File info
                    const fileInfo = document.createElement('div');
                    fileInfo.className = 'absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 rounded-b-lg truncate';
                    fileInfo.textContent = file.name;
                    
                    wrapper.appendChild(mediaElement);
                    wrapper.appendChild(deleteBtn);
                    wrapper.appendChild(fileInfo);
                    previewContainer.appendChild(wrapper);
                });
                
                if (filesArray.length > 0) {
                    const countDisplay = document.createElement('div');
                    countDisplay.className = 'w-full text-sm text-gray-600 mt-2';
                    countDisplay.textContent = `${filesArray.length} file baru akan diupload`;
                    previewContainer.appendChild(countDisplay);
                }
            }

            // Form submission
            form.addEventListener('submit', function(e) {
                const formData = new FormData(this);
                
                // Remove any existing media[] fields and add from our array
                formData.delete('media[]');
                filesArray.forEach(file => { 
                    formData.append('media[]', file); 
                });
            });
        });

        // Delete existing media
        function deleteExistingMedia(imageId) {
            if (confirm('Hapus media ini?')) {
                const element = document.querySelector(`[data-image-id="${imageId}"]`);
                if (element) {
                    element.remove();
                }
                
                // Add hidden input for deleted image IDs
                const form = document.querySelector('form');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'deleted_media[]';
                input.value = imageId;
                form.appendChild(input);
            }
        }
    </script>
@endsection