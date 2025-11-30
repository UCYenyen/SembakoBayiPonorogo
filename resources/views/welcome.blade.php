@extends('layouts.app')
@section('title', 'Home')
@section('content')
    <link href="/css/home.css" rel="stylesheet">
    {{-- hero mobile --}}

    <main class="bg-[#FFF3F3] text-[#3F3142] flex items-center lg:justify-center min-h-screen flex-col">
        <x-pages.home.hero-section />
        <x-pages.home.product-section />
    </main>
@endsection
