@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] py-8">
        <div class="w-[80%] mx-auto flex flex-col gap-8">
            <x-pages.dashboard.user.profile-card />
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
