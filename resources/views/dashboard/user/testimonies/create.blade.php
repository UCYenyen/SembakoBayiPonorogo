@extends('layouts.app')
@section('title', 'Create Testimony')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[80%] mx-auto">
            <div class="flex justify-between items-center mb-6 bg-white shadow-lg rounded-lg px-6 py-4">
                <div class="flex gap-4 justify-center items-center">
                    <a href="{{ route('dashboard') }}" class="text-white bg-[#3F3142] hover:bg-[#5C4B5E] transition-colors rounded-full p-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-4xl font-bold"> {{ $transactionItem->product->name }}</h1>
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- SIMPLE FORM - No Ajax, direct POST -->
            <form action="{{ route('user.testimonies.store') }}" method="POST" class="bg-white shadow-lg rounded-lg px-6 py-4 mb-8" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="transaction_item_id" value="{{ $transactionItem->id }}">
                
                <div class="mb-4">
                    <label class="block text-lg font-medium mb-2">Rating <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-1" id="star-rating">
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button" class="star focus:outline-none" data-value="{{ $i }}">
                                <svg class="w-8 h-8 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966c.3.922-.755 1.688-1.54 1.118l-3.38-2.454a1 1 0 00-1.175 0l-3.38 2.454c-.784.57-1.838-.196-1.54-1.118l1.287-3.966a1 1 0 00-.364-1.118L2.05 9.394c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69l1.286-3.967z" />
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" id="rating_star" name="rating_star" value="0" required>
                </div>

                <div class="mb-4">
                    <label for="content" class="block text-lg font-medium mb-2">Ulasan Anda <span class="text-red-500">*</span></label>
                    <textarea id="content" name="content" rows="5" required class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-[#3F3142]" placeholder="Ceritakan pengalaman Anda dengan produk ini..."></textarea>
                </div>

                <div class="mb-4">
                    <label for="media-upload" class="block text-lg font-medium mb-2">Upload Foto/Video (Opsional)</label>
                    <input id="media-upload" name="media[]" type="file" class="w-full border border-gray-300 rounded-lg p-3" multiple accept="image/jpeg,image/png,image/jpg,image/gif,image/webp,video/mp4,video/avi,video/mov,video/webm">
                    <p class="text-sm text-gray-500 mt-2">Format: JPG, PNG, GIF, WEBP, MP4, AVI, MOV (Max 50MB per file)</p>
                    
                    <!-- Preview -->
                    <div id="media-preview" class="flex flex-wrap gap-3 mt-3"></div>
                </div>

                <button type="submit" id="submit-btn" class="bg-[#3F3142] text-white px-6 py-2 rounded-lg hover:bg-[#5C4B5E] font-semibold">
                    Submit Ulasan
                </button>
            </form>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Star Rating
            const stars = document.querySelectorAll('#star-rating .star');
            const ratingInput = document.getElementById('rating_star');
            let currentRating = 0;

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

            // Preview uploaded files
            const mediaInput = document.getElementById('media-upload');
            const previewContainer = document.getElementById('media-preview');

            mediaInput.addEventListener('change', function () {
                previewContainer.innerHTML = '';
                
                Array.from(this.files).forEach((file) => {
                    const url = URL.createObjectURL(file);
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative';
                    
                    if (file.type.startsWith('image/')) {
                        const img = document.createElement('img');
                        img.src = url;
                        img.className = 'w-24 h-24 object-cover rounded-lg border';
                        wrapper.appendChild(img);
                    } else if (file.type.startsWith('video/')) {
                        const video = document.createElement('video');
                        video.src = url;
                        video.controls = true;
                        video.className = 'w-24 h-24 object-cover rounded-lg border';
                        wrapper.appendChild(video);
                    }

                    previewContainer.appendChild(wrapper);
                });
            });

            // Form validation
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                if (ratingInput.value == 0) {
                    e.preventDefault();
                    alert('Mohon berikan rating bintang');
                    return false;
                }
                
                // Show loading
                const submitBtn = document.getElementById('submit-btn');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Mengirim...';
            });
        });
    </script>
@endsection