@extends('layouts.app')
@section('title', 'Edit Vendor')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] flex items-center justify-center min-h-screen py-12">
        <div class="w-full max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl font-bold mb-6">Edit Vendor</h1>
                
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.vendors.update', $vendor) }}" method="POST" class="space-y-6" x-data="vendorForm()">
                    @csrf
                    @method('PUT')
                    
                    <!-- Vendor Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium mb-2">Nama Vendor *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $vendor->name) }}" required
                            placeholder="e.g., PT. Sembako Jaya"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                        @error('name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Vendor Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium mb-2">Tipe Vendor *</label>
                        <select name="type" id="type" x-model="vendorType" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                            <option value="">Pilih Tipe Vendor</option>
                            <option value="online" {{ old('type', $vendor->type) == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="offline" {{ old('type', $vendor->type) == 'offline' ? 'selected' : '' }}>Offline</option>
                        </select>
                        @error('type')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Phone Number (Required for Offline) -->
                    <div x-show="vendorType === 'offline'" x-transition>
                        <label for="phone_number" class="block text-sm font-medium mb-2">
                            Nomor Telepon <span class="text-red-600">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3 text-gray-700 font-medium select-none pointer-events-none z-10">+62</span>
                            <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number', $vendor->phone_number) }}" 
                                placeholder="812 3456 7890" maxlength="15" inputmode="numeric"
                                :required="vendorType === 'offline'"
                                class="w-full pl-14 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                        </div>
                        @error('phone_number')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Link (Required for Online) -->
                    <div x-show="vendorType === 'online'" x-transition>
                        <label for="link" class="block text-sm font-medium mb-2">
                            Link Vendor <span class="text-red-600">*</span>
                        </label>
                        <input type="url" name="link" id="link" value="{{ old('link', $vendor->link) }}" 
                            placeholder="https://example.com/vendor"
                            :required="vendorType === 'online'"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Masukkan URL lengkap vendor online (e.g., Tokopedia, Shopee)</p>
                        @error('link')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4 pt-4">
                        <button type="submit"
                            class="flex-1 bg-[#3F3142] text-white px-6 py-3 rounded-lg hover:bg-[#5C4B5E] transition-colors font-semibold">
                            Update Vendor
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
        function vendorForm() {
            return {
                vendorType: '{{ old('type', $vendor->type) }}',
            }
        }

        const phoneInput = document.getElementById('phone_number');

        if (phoneInput) {
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
        }
    </script>
@endsection