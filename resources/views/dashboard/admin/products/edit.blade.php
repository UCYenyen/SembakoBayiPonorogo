@extends('layouts.app')
@section('title', 'Edit Produk')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] flex items-center justify-center min-h-[80vh] py-12">
        <div class="w-full max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl font-bold mb-6">Edit Produk</h1>

                <form action="{{ route('admin.products.update', $product->id) }}" method="POST"
                    class="space-y-6 bg-white/80 p-4 rounded-lg" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-sm font-medium mb-2">Nama Produk *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium mb-2">Deskripsi *</label>
                        <textarea name="description" id="description" rows="4" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="price" class="block text-sm font-medium mb-2">Harga (Rp) *</label>
                            <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}"
                                min="0" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                        </div>

                        <div>
                            <label for="stocks" class="block text-sm font-medium mb-2">Stok *</label>
                            <input type="number" name="stocks" id="stocks"
                                value="{{ old('stocks', $product->stocks) }}" min="0" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="category_id" class="block text-sm font-medium mb-2">Kategori *</label>
                            <select name="category_id" id="category_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="brand_id" class="block text-sm font-medium mb-2">Brand *</label>
                            <select name="brand_id" id="brand_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                                <option value="">Select Brand</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}"
                                        {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="image_file" class="block text-sm font-medium mb-2">Gambar Produk</label>
                        @if ($product->image_url)
                            <div class="mb-2">
                                <p class="text-xs text-gray-500 mb-1">Gambar saat ini:</p>
                                <img src="{{ asset('storage/' . $product->image_url) }}"
                                    class="w-full h-auto object-cover rounded border" alt="">
                            </div>
                        @endif
                        <input type="file" name="image_file" id="image_file" accept="image/*"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#3F3142] file:text-white hover:file:bg-[#5C4B5E]">
                        <p class="text-xs text-gray-500 mt-1">*Kosongkan jika tidak ingin mengubah gambar.</p>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit"
                            class="flex-1 bg-[#3F3142] text-white px-6 py-3 rounded-lg hover:bg-[#5C4B5E] transition-colors font-semibold">
                            Update Product
                        </button>
                        <a href="{{ route('admin.products') }}"
                            class="flex-1 bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors font-semibold text-center">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // 1. Alert Sukses
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#3F3142',
            });
        @endif

        // 2. Alert Error Validasi (Laravel Validation Errors)
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                html: `
                    <ul style="text-align: left;">
                        @foreach ($errors->all() as $error)
                            <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                `,
                confirmButtonColor: '#3F3142',
            });
        @endif
    </script>
@endsection
