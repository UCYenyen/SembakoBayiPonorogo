@extends('layouts.app')
@section('title', 'Transaction Management')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[90%] lg:w-[80%] mx-auto">
            <h1 class="text-4xl font-bold mb-8">Transaction Management</h1>
            <x-admin-order-dashboard :transactions="$transactions" :statusCounts="$statusCounts" :currentStatus="request('status', 'all')" />
        </div>
    </main>
@endsection
