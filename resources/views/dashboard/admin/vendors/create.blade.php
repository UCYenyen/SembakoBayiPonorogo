@extends('layouts.app')
@section('title', 'Create Vendor')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] flex items-center justify-center min-h-screen py-12">
        <div class="w-full max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl font-bold mb-6">Create New Vendor</h1>
                
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.vendors.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Vendor Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium mb-2">Vendor Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            placeholder="e.g., PT. Sembako Jaya"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                        @error('name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone_number" class="block text-sm font-medium mb-2">Phone Number *</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3 text-gray-700 font-medium select-none pointer-events-none z-10">+62</span>
                            <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" 
                                required placeholder="812 3456 7890" maxlength="15" inputmode="numeric"
                                class="w-full pl-14 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                        </div>
                        @error('phone_number')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4 pt-4">
                        <button type="submit"
                            class="flex-1 bg-[#3F3142] text-white px-6 py-3 rounded-lg hover:bg-[#5C4B5E] transition-colors font-semibold">
                            Create Vendor
                        </button>
                        <a href="{{ route('admin.vendors.index') }}"
                            class="flex-1 bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors font-semibold text-center">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        const phoneInput = document.getElementById('phone_number');

        phoneInput.addEventListener('input', function(e) {
            let cursorPosition = this.selectionStart;
            
            // Hapus semua karakter non-digit
            let value = this.value.replace(/\D/g, '');
            
            // Hapus leading 0 dan 62
            value = value.replace(/^0+/, '');
            value = value.replace(/^62/, '');
            
            // Batasi maksimal 12 digit
            value = value.substring(0, 12);

            // Format: XXX XXXX XXXX
            let formatted = '';
            if (value.length > 0) {
                formatted = value.substring(0, 3);
                
                if (value.length > 3) {
                    formatted += ' ' + value.substring(3, 7);
                }
                
                if (value.length > 7) {
                    formatted += ' ' + value.substring(7);
                }
            }

            // Hitung posisi cursor yang benar
            let oldValue = this.value;
            this.value = formatted;

            // Jika user mengetik (bukan menghapus)
            if (formatted.length >= oldValue.length) {
                // Tambahkan offset untuk spasi yang ditambahkan
                if (cursorPosition === 4 || cursorPosition === 9) {
                    cursorPosition++;
                }
            }

            this.setSelectionRange(cursorPosition, cursorPosition);
        });

        // Handle paste
        phoneInput.addEventListener('paste', function(e) {
            e.preventDefault();
            let pastedText = (e.clipboardData || window.clipboardData).getData('text');
            
            pastedText = pastedText.replace(/\D/g, '');
            pastedText = pastedText.replace(/^0+/, '');
            pastedText = pastedText.replace(/^62/, '');
            pastedText = pastedText.substring(0, 12);

            let formatted = '';
            if (pastedText.length > 0) {
                formatted = pastedText.substring(0, 3);
                if (pastedText.length > 3) {
                    formatted += ' ' + pastedText.substring(3, 7);
                }
                if (pastedText.length > 7) {
                    formatted += ' ' + pastedText.substring(7);
                }
            }

            this.value = formatted;
            this.dispatchEvent(new Event('input', { bubbles: true }));
        });

        // Prevent cursor issues
        phoneInput.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft' && this.selectionStart === 0) {
                e.preventDefault();
            }
            if (e.key === 'Home') {
                e.preventDefault();
                this.setSelectionRange(0, 0);
            }
        });

        phoneInput.addEventListener('mousedown', function(e) {
            if (this.selectionStart < 0) {
                setTimeout(() => {
                    this.setSelectionRange(0, 0);
                }, 0);
            }
        });

        phoneInput.addEventListener('input', function() {
            this.setCustomValidity('');
        });
    </script>
@endsection