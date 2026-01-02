@extends('layouts.app')
@section('title', $product->name)
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[80%] mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div
                                class="aspect-square bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                                <img src="{{ asset('storage/' . $product->image_url) }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover">
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <h1 class="text-3xl font-bold mb-2">{{ $product->name }}</h1>
                                    <p class="text-gray-600">Brand:
                                        <span class="font-semibold text-[#3F3142]">{{ $product->brand->name }}</span>
                                    </p>
                                    <p class="text-gray-600">Kategori:
                                        <span class="font-semibold text-[#3F3142]">{{ $product->category->name }}</span>
                                    </p>
                                    <p class="text-gray-600">Berat:
                                        <span class="font-semibold text-[#3F3142]">{{ $product->weight }}g</span>
                                    </p>
                                </div>

                                <div class="flex items-center gap-2">
                                    <div class="flex">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <img src="/images/misc/star.svg" alt="star" class="w-5 h-5">
                                        @endfor
                                    </div>
                                    <span class="text-lg font-semibold">4.5</span>
                                    <span class="text-gray-600">(127 reviews)</span>
                                </div>

                                <div class="border-t border-b py-4">
                                    <p class="text-4xl font-bold text-[#3F3142]">
                                        Rp{{ number_format($product->price, 0, ',', '.') }}
                                    </p>
                                    @if ($product->is_on_sale && $product->discount_amount > 0)
                                        <p class="text-gray-500 line-through text-xl">
                                            Rp{{ number_format($product->price + $product->discount_amount, 0, ',', '.') }}
                                        </p>
                                        <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                            Hemat Rp{{ number_format($product->discount_amount, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>

                                <div>
                                    <p class="text-gray-600">Ketersediaan:</p>
                                    @if ($product->stocks > 10)
                                        <span class="text-green-600 font-semibold">({{ $product->stocks }}
                                            items)</span>
                                    @elseif($product->stocks > 0)
                                        <span class="text-yellow-600 font-semibold">({{ $product->stocks }}
                                            items)</span>
                                    @else
                                        <span class="text-red-600 font-semibold">Habis</span>
                                    @endif
                                </div>

                                <form method="POST" action="{{ route('cart.add', $product) }}" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                                    <div class="flex items-center gap-4">
                                        <label class="text-gray-700 font-semibold">Jumlah:</label>
                                        <input type="number" name="quantity" value="1" min="1"
                                            max="{{ $product->stocks }}"
                                            class="w-20 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142]"
                                            {{ $product->stocks == 0 ? 'disabled' : '' }}>
                                    </div>

                                    <button type="submit"
                                        class="w-full bg-[#3F3142] text-white py-3 rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors {{ $product->stocks == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ $product->stocks == 0 ? 'disabled' : '' }}>
                                        <x-heroicon-o-shopping-cart class="w-5 h-5 inline-block mr-2" />
                                        Tambah ke Keranjang
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-4">Deskripsi Produk</h2>
                        {{-- filepath: resources/views/shop/product-detail.blade.php --}}
<p class="text-gray-700 leading-relaxed whitespace-pre-line">
    {!! nl2br(e($product->description)) !!}
</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-6">Ulasan Pelanggan</h2>

                        <div class="flex items-center gap-8 mb-8 pb-6 border-b">
                            <div class="text-center">
                                <p class="text-5xl font-bold text-[#3F3142]">4.5</p>
                                <div class="flex mt-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <img src="/images/misc/star.svg" alt="star" class="w-5 h-5">
                                    @endfor
                                </div>
                                <p class="text-gray-600 mt-2">127 reviews</p>
                            </div>

                            <div class="flex-1">
                                @foreach ([5, 4, 3, 2, 1] as $star)
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm w-8">{{ $star }} ⭐</span>
                                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                                            <div class="bg-[#3F3142] h-2 rounded-full" style="width: {{ rand(20, 90) }}%">
                                            </div>
                                        </div>
                                        <span class="text-sm w-12 text-gray-600">{{ rand(10, 80) }}%</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-6">
                            @for ($i = 1; $i <= 3; $i++)
                                <div class="border-b pb-6 last:border-b-0">
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="w-12 h-12 bg-[#3F3142] rounded-full flex items-center justify-center text-white font-bold">
                                            {{ chr(64 + $i) }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="font-semibold">User {{ chr(64 + $i) }}</h4>
                                                <span class="text-sm text-gray-500">{{ rand(1, 30) }} hari yang
                                                    lalu</span>
                                            </div>
                                            <div class="flex mb-2">
                                                @for ($j = 1; $j <= 5; $j++)
                                                    <img src="/images/misc/star.svg" alt="star" class="w-4 h-4">
                                                @endfor
                                            </div>
                                            <p class="text-gray-700">
                                                Produk yang bagus! Bayi saya menyukainya. Kualitasnya sangat baik dan
                                                pengirimannya cepat. Sangat direkomendasikan!
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <button
                            class="w-full mt-6 py-3 border-2 border-[#3F3142] text-[#3F3142] rounded-lg font-semibold hover:bg-[#3F3142] hover:text-white transition-colors">
                            Lihat lebih banyak ulasan
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-lg p-6 sticky top-24">
                        <h2 class="text-2xl font-bold mb-6">Produk yang mirip</h2>
                        <div class="space-y-4">
                            @foreach ($similarProducts as $similar)
                                <a href="{{ route('product.show', $similar) }}"
                                    class="flex gap-4 p-3 rounded-lg hover:bg-gray-50 transition-colors border border-gray-200">
                                    <img src="{{ asset('storage/' . $similar->image_url) }}" alt="{{ $similar->name }}"
                                        class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-sm mb-1 truncate">{{ $similar->name }}</h4>
                                        <div class="flex items-center gap-1 mb-1">
                                            <img src="/images/misc/star.svg" alt="star" class="w-3 h-3">
                                            <span class="text-xs text-gray-600">4.5</span>
                                        </div>
                                        <p class="text-[#3F3142] font-bold text-sm">
                                            Rp{{ number_format($similar->price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        @if ($similarProducts->isEmpty())
                            <p class="text-gray-500 text-center py-8">Tidak ada produk serupa ditemukan</p>
                        @endif

                        <a href="/shop?category={{ $product->category_id }}"
                            class="block w-full mt-6 py-3 bg-[#3F3142] text-white text-center rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors">
                            Lihat Semua di {{ $product->category->name }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
