@extends('layouts.app')
@section('title', 'Shop')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[90%] lg:w-[80%] mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-lg p-6 sticky top-24">
                        <h2 class="text-2xl font-bold mb-6">Filters</h2>

                        <form method="GET" action="/shop" id="filterForm">
                            <div class="mb-6 pb-6 border-b">
                                <h3 class="font-semibold text-lg mb-4">Categories</h3>
                                <div class="space-y-2 max-h-64 overflow-y-auto">
                                    @foreach ($categories as $category)
                                        <div>
                                            <label
                                                class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                                                <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                                    {{ in_array($category->id, request('categories', [])) ? 'checked' : '' }}
                                                    onchange="document.getElementById('filterForm').submit()"
                                                    class="w-4 h-4 text-[#3F3142] border-gray-300 rounded focus:ring-[#3F3142]">
                                                <span class="text-sm">{{ $category->name }}</span>
                                            </label>

                                            @if ($category->children->count() > 0)
                                                <div class="ml-6 mt-1 space-y-1">
                                                    @foreach ($category->children as $subCategory)
                                                        <label
                                                            class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                                                            <input type="checkbox" name="categories[]"
                                                                value="{{ $subCategory->id }}"
                                                                {{ in_array($subCategory->id, request('categories', [])) ? 'checked' : '' }}
                                                                onchange="document.getElementById('filterForm').submit()"
                                                                class="w-3 h-3 text-[#3F3142] border-gray-300 rounded focus:ring-[#3F3142]">
                                                            <span
                                                                class="text-xs text-gray-600">{{ $subCategory->name }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-6 pb-6 border-b">
                                <h3 class="font-semibold text-lg mb-4">Brands</h3>
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    @foreach ($brands as $brand)
                                        <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                                            <input type="checkbox" name="brands[]" value="{{ $brand->id }}"
                                                {{ in_array($brand->id, request('brands', [])) ? 'checked' : '' }}
                                                onchange="document.getElementById('filterForm').submit()"
                                                class="w-4 h-4 text-[#3F3142] border-gray-300 rounded focus:ring-[#3F3142]">
                                            <span class="text-sm">{{ $brand->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-6 pb-6 border-b">
                                <h3 class="font-semibold text-lg mb-4">Price Range</h3>
                                <div class="flex items-center gap-2 w-full">
                                    <select name="sort" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142]" onchange="updateSort(this.value)">
                                        <option value="" {{ request('sort') == null ? 'selected' : '' }}>None</option>
                                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Low to High</option>
                                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>High to Low</option>
                                    </select>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-sm text-gray-600">Min Price</label>
                                        <input type="number" name="min_price" value="{{ request('min_price') }}"
                                            placeholder="0"
                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142]">
                                    </div>
                                    <div>
                                        <label class="text-sm text-gray-600">Max Price</label>
                                        <input type="number" name="max_price" value="{{ request('max_price') }}"
                                            placeholder="1000000"
                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142]">
                                    </div>
                                    <button type="submit"
                                        class="w-full bg-[#3F3142] text-white py-2 rounded-lg hover:bg-[#5C4B5E] transition-colors">
                                        Apply Price
                                    </button>
                                </div>
                            </div>

                            <a href="/shop"
                                class="block w-full text-center py-2 border-2 border-[#3F3142] text-[#3F3142] rounded-lg hover:bg-[#3F3142] hover:text-white transition-colors">
                                Clear All Filters
                            </a>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-3">
                    @if ($products->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                            @foreach ($products as $product)
                                <x-pages.product-card :product="$product" productImage="{{ $product->image_url }}"
                                    productName="{{ $product->name }}" rating="4.5"
                                    price="{{ number_format($product->price, 0, ',', '.') }}" />
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="bg-white rounded-lg shadow-md p-12 text-center">
                            <svg class="w-24 h-24 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                </path>
                            </svg>
                            <h3 class="text-2xl font-semibold text-gray-700 mb-2">No Products Found</h3>
                            <p class="text-gray-500 mb-4">Try adjusting your filters</p>
                            <a href="/shop"
                                class="inline-block px-6 py-3 bg-[#3F3142] text-white rounded-lg hover:bg-[#5C4B5E] transition-colors">
                                Clear Filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <script>
        function updateSort(value) {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', value);
            window.location.href = url.toString();
        }
    </script>
@endsection
