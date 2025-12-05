@extends('layouts.app')
@section('title', 'Payment Success')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[90%] lg:w-[80%] mx-auto">
            <div class="max-w-2xl mx-auto">
                <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                    <svg class="w-24 h-24 mx-auto mb-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    
                    <h1 class="text-3xl font-bold mb-4">Payment Received!</h1>
                    <p class="text-gray-600 mb-6">Thank you for your order. We're processing it now.</p>

                    <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Order ID:</span>
                            <span class="font-semibold">#{{ $transaction->id }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Total Payment:</span>
                            <span class="font-semibold text-[#3F3142]">
                                Rp{{ number_format($transaction->total_price + $transaction->delivery_price, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $transaction->getStatusBadgeClass() }}">
                                {{ $transaction->getStatusLabel() }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('user.transaction.show', $transaction) }}" 
                           class="flex-1 bg-[#3F3142] text-white py-3 rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors">
                            View Order Detail
                        </a>
                        <a href="/shop" 
                           class="flex-1 border-2 border-[#3F3142] text-[#3F3142] py-3 rounded-lg font-semibold hover:bg-[#3F3142] hover:text-white transition-colors">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection