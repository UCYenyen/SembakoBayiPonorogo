@extends('layouts.app')
@section('title', 'Home')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] flex items-center lg:justify-center min-h-screen flex-col">
        <x-pages.shop.promo-products :products="$promoProducts" />
        <x-pages.shop.top-products :products="$topProducts"/>
    </main>
@endsection
