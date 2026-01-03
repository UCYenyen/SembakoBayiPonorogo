@extends('layouts.app')
@section('title', 'Create Testimony')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[80%] mx-auto">
            <div class="flex justify-between items-center mb-6 bg-white shadow-lg rounded-lg px-6 py-4">
                <div class="flex gap-4 justify-center items-center">
                    <a href="{{ route('dashboard') }}"
                        class="text-white bg-[#3F3142] hover:bg-[#5C4B5E] transition-colors rounded-full p-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                    </a>
                    <h1 class="text-4xl font-bold">{{ $transactionItem->product->name }}</h1>
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

            <form action="{{ route('user.testimonies.store') }}" method="POST"
                class="bg-white shadow-lg rounded-lg px-6 py-4 mb-8" enctype="multipart/form-data" id="testimony-form">
                @csrf
                <input type="hidden" name="transaction_item_id" value="{{ $transactionItem->id }}">

                <div class="mb-4">
                    <label class="block text-lg font-medium mb-2">Rating <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-1" id="star-rating">
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button" class="star focus:outline-none" data-value="{{ $i }}">
                                <svg class="w-8 h-8 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966c.3.922-.755 1.688-1.54 1.118l-3.38-2.454a1 1 0 00-1.175 0l-3.38 2.454c-.784.57-1.838-.196-1.54-1.118l1.287-3.966a1 1 0 00-.364-1.118L2.05 9.394c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69l1.286-3.967z" />
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" id="rating_star" name="rating_star" value="0" required>
                </div>

                <div class="mb-4">
                    <label for="content" class="block text-lg font-medium mb-2">Ulasan Anda <span
                            class="text-red-500">*</span></label>
                    <textarea id="content" name="content" rows="5" required
                        class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-[#3F3142]"
                        placeholder="Ceritakan pengalaman Anda dengan produk ini..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-lg font-medium mb-2">Upload Foto/Video (Opsional)</label>

                    <!-- Preview Area -->
                    <div id="media-preview" class="flex flex-wrap gap-3 mb-3"></div>

                    <!-- Upload Button -->
                    <label for="media-input"
                        class="cursor-pointer inline-flex items-center justify-center gap-2 px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="text-gray-600">Tambah File</span>
                    </label>
                    <input id="media-input" type="file" class="hidden" multiple
                        accept="image/jpeg,image/png,image/jpg,image/gif,image/webp,video/mp4,video/avi,video/mov,video/webm">

                    <p class="text-sm text-gray-500 mt-2">
                        Format: JPG, PNG, GIF, WEBP, MP4, AVI, MOV (Max 50MB per file, max 10 files)
                    </p>
                </div>

                <button type="submit" id="submit-btn"
                    class="bg-[#3F3142] text-white px-6 py-2 rounded-lg hover:bg-[#5C4B5E] font-semibold transition-colors">
                    Submit Ulasan
                </button>
            </form>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Star Rating System
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

            // Multiple Files Upload System
            const mediaInput = document.getElementById('media-input');
            const previewContainer = document.getElementById('media-preview');
            const form = document.getElementById('testimony-form');
            let filesArray = []; // Store files here
            const maxFiles = 10;

            mediaInput.addEventListener('change', function(e) {
                const newFiles = Array.from(e.target.files);

                // Check max files limit
                if (filesArray.length + newFiles.length > maxFiles) {
                    alert(`Maksimal ${maxFiles} file. Anda sudah memiliki ${filesArray.length} file.`);
                    mediaInput.value = '';
                    return;
                }

                // Add new files to array
                newFiles.forEach(file => {
                    // Check file size (50MB)
                    if (file.size > 50 * 1024 * 1024) {
                        alert(`File ${file.name} terlalu besar (max 50MB)`);
                        return;
                    }

                    filesArray.push(file);
                });

                // Clear input for next selection
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
                        mediaElement.className =
                            'w-32 h-32 object-cover rounded-lg border-2 border-gray-200';
                    } else if (file.type.startsWith('video/')) {
                        mediaElement = document.createElement('video');
                        mediaElement.src = url;
                        mediaElement.className =
                            'w-32 h-32 object-cover rounded-lg border-2 border-gray-200';
                        mediaElement.muted = true;
                    }

                    // Create delete button
                    const deleteBtn = document.createElement('button');
                    deleteBtn.type = 'button';
                    deleteBtn.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    `;
                    deleteBtn.className =
                        'absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors opacity-0 group-hover:opacity-100';
                    deleteBtn.onclick = function() {
                        removeFile(index);
                    };

                    // File info
                    const fileInfo = document.createElement('div');
                    fileInfo.className =
                        'absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 rounded-b-lg truncate';
                    fileInfo.textContent = file.name;

                    wrapper.appendChild(mediaElement);
                    wrapper.appendChild(deleteBtn);
                    wrapper.appendChild(fileInfo);
                    previewContainer.appendChild(wrapper);
                });

                // Update file count display
                if (filesArray.length > 0) {
                    const countDisplay = document.createElement('div');
                    countDisplay.className = 'w-full text-sm text-gray-600 mt-2';
                    countDisplay.textContent = `${filesArray.length} file dipilih (max ${maxFiles})`;
                    previewContainer.appendChild(countDisplay);
                }
            }

            function removeFile(index) {
                filesArray.splice(index, 1);
                updatePreview();
            }

            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validate rating
                if (ratingInput.value == 0) {
                    alert('Mohon berikan rating bintang');
                    return;
                }

                const submitBtn = document.getElementById('submit-btn');
                const originalText = submitBtn.textContent;
                submitBtn.disabled = true;
                submitBtn.textContent = 'Mengirim...';

                // Create FormData and append all files
                const formData = new FormData(form);

                // Remove any existing media[] fields
                formData.delete('media[]');

                // Append all files from our array
                filesArray.forEach((file, index) => {
                    formData.append('media[]', file);
                });

                console.log('Submitting with files:', filesArray.length);

                // Submit form
                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert('Ulasan berhasil dikirim!');
                        window.location.href = "{{ route('dashboard') }}";
                    })
                    .catch(error => {
                        console.error('Error:', error);
                         window.location.href = "{{ route('dashboard') }}";
                    })
            });
        });
    </script>
@endsection
