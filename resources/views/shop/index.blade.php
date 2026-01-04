@extends('layouts.app')
@section('title', 'Shop')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[80%] mx-auto">
            <div class="lg:hidden mb-6">
                <div class="mb-4">
                    <x-pages.search-bar route="/shop" placeholder="Search products..." extraClasses="w-full" />
                </div>
                <button onclick="toggleMobileFilter()"
                    class="w-full flex items-center justify-between bg-white px-4 py-3 rounded-lg shadow-md border border-gray-200">
                    <span class="font-bold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter dan Urutkan
                    </span>
                    <svg id="filterArrow" class="w-5 h-5 transition-transform duration-300" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <div id="filterContainer" class="hidden lg:block lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-lg p-6 sticky top-24">
                        <h2 class="text-2xl font-bold mb-6">Filter</h2>

                        <form method="GET" action="/shop" id="filterForm">
                            <div class="mb-6 pb-6 border-b">
                                <h3 class="font-semibold text-lg mb-4">Kategori</h3>
                                <div class="space-y-2 max-h-64 overflow-y-auto">
                                    @foreach ($categories as $category)
                                        <x-category-filter-item :category="$category" />
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-6 pb-6 border-b">
                                <h3 class="font-semibold text-lg mb-4">Brand</h3>
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
                                <h3 class="font-semibold text-lg mb-4">Jangkauan Harga</h3>
                                <div class="mb-4">
                                    <select name="sort"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142]"
                                        onchange="updateSort(this.value)">
                                        <option value="" {{ request('sort') == '' ? 'selected' : '' }}>Urutkan
                                        </option>
                                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                                            Harga: Rendah ke Tinggi</option>
                                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                                            Harga: Tinggi ke Rendah</option>
                                    </select>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-sm text-gray-600">Harga Minimum</label>
                                        <input type="number" name="min_price" value="{{ request('min_price') }}"
                                            placeholder="0"
                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142]">
                                    </div>
                                    <div>
                                        <label class="text-sm text-gray-600">Harga Maximum</label>
                                        <input type="number" name="max_price" value="{{ request('max_price') }}"
                                            placeholder="1000000"
                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142]">
                                    </div>
                                    <button type="submit"
                                        class="w-full bg-[#3F3142] text-white py-2 rounded-lg hover:bg-[#5C4B5E] transition-colors">
                                        Terapkan
                                    </button>
                                </div>
                            </div>

                            <a href="/shop"
                                class="block w-full text-center py-2 border-2 border-[#3F3142] text-[#3F3142] rounded-lg hover:bg-[#3F3142] hover:text-white transition-colors">
                                Hapus Semua Filter
                            </a>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-3">
                    @if (isset($searchQuery) && $searchQuery)
                        <h2 class="text-2xl font-bold mb-6">Hasil Pencarian untuk "{{ $searchQuery }}"</h2>
                    @endif

                    @if ($products->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 lg:gap-6">
                            @foreach ($products as $product)
                                <x-pages.product-card :product="$product" productImage="{{ $product->image_url }}"
                                    productName="{{ $product->name }}" rating="{{ $product->average_rating }}"
                                    price="{{ number_format($product->price, 0, ',', '.') }}" />
                            @endforeach
                        </div>

                        <div class="w-full flex justify-end items-center mt-8">
                            {{ $products->links('vendor.pagination.simple') }}
                        </div>
                    @else
                        <div class="bg-white rounded-lg shadow-md p-12 text-center">
                            <svg class="w-24 h-24 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                </path>
                            </svg>
                            <h3 class="text-2xl font-semibold text-gray-700 mb-2">Tidak Ada Produk Ditemukan</h3>
                            <p class="text-gray-500 mb-4">
                                {{ isset($searchQuery) ? 'Tidak ada produk yang cocok dengan "' . $searchQuery . '"' : 'Coba sesuaikan filter Anda' }}
                            </p>
                            <a href="/shop"
                                class="inline-block px-6 py-3 bg-[#3F3142] text-white rounded-lg hover:bg-[#5C4B5E] transition-colors">
                                Hapus Semua Filter
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <script>
        function toggleMobileFilter() {
            const container = document.getElementById('filterContainer');
            const arrow = document.getElementById('filterArrow');
            if (container.classList.contains('hidden')) {
                container.classList.remove('hidden');
                arrow.style.transform = 'rotate(180deg)';
            } else {
                container.classList.add('hidden');
                arrow.style.transform = 'rotate(0deg)';
            }
        }

        function updateSort(value) {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', value);
            window.location.href = url.toString();
        }
    </script>
@endsection
