@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] py-8">
        <div class="w-[80%] mx-auto">
            <div class="bg-white rounded-lg shadow-md mb-6 overflow-x-auto">
                <div class="px-6 py-4">
                    <h1 class="text-3xl font-bold">Dasbor</h1>
                    <div class="flex flex-col mt-4 gap-2">
                        <p class="text-gray-600">Nama: {{ auth()->user()->name }}</p>
                        <p class="text-gray-600">Email: {{ auth()->user()->email }}</p>
                        <p class="text-gray-600">Telepon: +{{ auth()->user()->phone_number }}</p>
                        <p class="text-gray-600">Alamat: {{ auth()->user()->address }}</p>
                    </div>
                </div>
            </div>
            <x-user-order-dashboard :transactions="$transactions" :statusCounts="$statusCounts" :currentStatus="request('status', 'all')" />
        </div>
    </main>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
        }
    </style>
@endsection
