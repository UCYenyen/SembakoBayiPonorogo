@extends('layouts.app')
@section('title', 'Home')
@section('content')
    <x-pages.section title="" extraClasses="">
        <div class="w-[80%] mx-auto flex flex-col gap-4">
            <!-- Header -->
            <a href="/dashboard/admin/products/index"
                class="bg-[#3F3142] w-full sm:w-fit text-white px-6 py-3 rounded-lg hover:bg-[#5C4B5E] transition-colors font-semibold">
                Back
            </a>
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex justify-center items-start flex-col gap-4">
                    <h1 class="text-3xl font-bold">Product Management</h1>
                </div>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Products Table -->
            <form action="/dashboard/admin/products/{{ $product->id }}" method="POST" class="space-y-6 bg-white/80 p-4 rounded-lg"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Product Name -->
                <div>
                    <label for="name" class="block text-sm font-medium mb-2">Product Name *</label>
                    <input type="text" name="name" id="name" placeholder="{{$product->name}}" old="{{ old('name', $product->name) }}" value="{{$product->name }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium mb-2">Description *</label>
                    <textarea name="description" placeholder="{{$product->description}}" id="description" rows="4" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">{{ old('description', $product->description) }}</textarea>
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium mb-2">Price (Rp) *</label>
                    <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" min="0" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                </div>

                <!-- Stocks -->
                <div>
                    <label for="stocks" class="block text-sm font-medium mb-2">Stock Quantity *</label>
                    <input type="number" name="stocks" id="stocks" value="{{ old('stocks', $product->stocks) }}" min="0"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium mb-2">Category *</label>
                    <select name="category_id" id="category_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Brand -->
                <div>
                    <label for="brand_id" class="block text-sm font-medium mb-2">Brand *</label>
                    <select name="brand_id" id="brand_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                        <option value="">Select Brand</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Image URL -->
                <div>
                    <label for="image_url" class="block text-sm font-medium mb-2">Image URL *</label>
                    {{-- <input type="text" name="image_url" id="image_url" value="{{ old('image_url') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent"
                            placeholder="https://example.com/image.jpg"> --}}
                    <input type="file" name="image_file" accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent"
                        required>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-4">
                    <button type="submit"
                        class="flex-1 bg-[#3F3142] text-white px-6 py-3 rounded-lg hover:bg-[#5C4B5E] transition-colors font-semibold">
                        Update Product
                    </button>
                    <a href="/dashboard/admin/products"
                        class="flex-1 bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors font-semibold text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </x-pages.section>
@endsection
