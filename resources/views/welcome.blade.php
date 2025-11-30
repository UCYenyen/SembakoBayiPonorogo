@extends('layouts.app')
@section('title', 'Home')
@section('content')
    <link href="/css/home.css" rel="stylesheet">
    {{-- hero mobile --}}

    <main class="bg-[#FFF3F3] text-[#3F3142] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        <section class="w-[80%] min-h-screen">
            <div class="flex justify-center items-center">
                <div class="flex flex-col gap-4 basis-[40%]">
                    <h1 class="text-7xl font-bold text-[#3F3142]">
                        Get the best
                        for your baby
                    </h1>
                    <h2 class="text-5xl">
                        The cheapest and most complete baby shop in Ponorogo
                    </h2>
                </div>

                <!-- wrapper dengan basis 2/5 (40%) -->
                <div class="basis-[60%] flex justify-center">
                    <img src="/images/home/baby-image.svg"
                         alt="sembako bayi ponorogo baby image"
                         class="w-full max-w-full h-auto object-contain">
                </div>
            </div>
        </section>
        <x-pages.home.product-section />
    </main>
@endsection
