@extends('layouts.app')
@section('title', 'Home')
@section('content')
    <link href="/css/home.css" rel="stylesheet">

    <main class="bg-[#FFF3F3] text-[#3F3142] flex items-center lg:justify-center min-h-screen flex-col">
        <x-pages.home.hero-section />
        
        @if(isset($searchQuery) && $searchQuery)
            <!-- Search Results Section -->
            <section class="w-full py-12">
                <div class="w-[80%] mx-auto">
                    <h2 class="text-3xl font-bold mb-6">Search Results for "{{ $searchQuery }}"</h2>
                    
                    @if($products->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            @foreach($products as $product)
                                <x-pages.product-card 
                                    productImage="{{ $product->image_url }}" 
                                    productName="{{ $product->name }}"
                                    rating="4.5" 
                                    price="{{ number_format($product->price, 0, ',', '.') }}" />
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white rounded-lg shadow-md p-8 text-center">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Products Found</h3>
                            <p class="text-gray-500">No products matching the name "{{ $searchQuery }}"</p>
                            <a href="/" class="inline-block mt-4 px-6 py-2 bg-[#3F3142] text-white rounded-lg hover:bg-[#5C4B5E] transition-colors">
                                Clear Search
                            </a>
                        </div>
                    @endif
                </div>
            </section>
        @else
            <x-pages.home.product-section />
            <x-pages.home.testimonials-section />
        @endif
    </main>
@endsection
