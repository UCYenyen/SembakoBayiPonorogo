@extends('layouts.app')
@section('title', 'Transaction Detail')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[90%] lg:w-[80%] mx-auto">
            <div class="flex justify-between items-center mb-6">
                <a href="{{ route('admin.transactions.index') }}" 
                   class="inline-flex items-center gap-2 text-[#3F3142] hover:underline">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Transactions
                </a>
            
                <a href="{{ route('admin.transactions.edit', $transaction) }}" 
                   class="bg-[#3F3142] text-white px-6 py-2 rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors">
                    Edit
                </a>
            </div>

            <h1 class="text-4xl font-bold mb-8">Transaction Detail #{{ $transaction->id }}</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-4">Informasi Pelanggan</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nama</span>
                                <span class="font-semibold">{{ $transaction->user->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Email</span>
                                <span class="font-semibold">{{ $transaction->user->email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Telepon</span>
                                <span class="font-semibold">{{ $transaction->user->phone_number ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Order Items --}}
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-6">Pesanan</h2>
                        <div class="space-y-4">
                            @foreach($transaction->transaction_items as $item)
                                <div class="flex gap-4 pb-4 border-b last:border-b-0">
                                    <img src="{{ asset('storage/' . $item->product->image_url) }}" 
                                         alt="{{ $item->product->name }}"
                                         class="w-24 h-24 object-cover rounded-lg">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-lg mb-1">{{ $item->product->name }}</h4>
                                        <div class="flex justify-between items-center">
                                            <p class="text-gray-600">Qty: {{ $item->quantity }}</p>
                                            <p class="text-xl font-bold text-[#3F3142]">
                                                Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Shipping Address --}}
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-4">Alamat Pengiriman</h2>
                        <p class="text-gray-700 leading-relaxed">
                            <span class="font-bold">{{ $transaction->address->name }}</span><br>
                            {{ $transaction->address->extra_detail }}<br>
                            {{ $transaction->address->subdistrict_name }}, {{ $transaction->address->city_name }}<br>
                            {{ $transaction->address->province_name }}, {{ $transaction->address->postal_code }}
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-lg p-6 sticky top-24">
                        <h2 class="text-2xl font-bold mb-6">Ringkasan</h2>

                        <div class="mb-6 pb-6 border-b">
                            <h3 class="font-semibold mb-2 text-gray-500 uppercase text-xs">Status Saat Ini</h3>
                            <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold bg-[#dbdeff] text-black">
                                {{ $transaction->getStatusLabel() }}
                            </span>
                        </div>

                        <div class="space-y-3">
                            <h3 class="font-semibold mb-4 text-xs uppercase text-gray-500">Informasi Pesanan</h3>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Metode</span>
                                <span class="font-semibold">{{ $transaction->payment_method ?? 'Midtrans' }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Kurir</span>
                                <span class="font-semibold uppercase">{{ $transaction->delivery->courier_code }} - {{ $transaction->delivery->name }}</span>
                            </div>
                            @if($transaction->no_resi)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tracking #</span>
                                <span class="font-mono font-bold text-blue-600">{{ $transaction->no_resi }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="mt-6 pt-6 border-t space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-semibold">Rp{{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Ongkos Kirim</span>
                                <span class="font-semibold">Rp{{ number_format($transaction->delivery_price, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t">
                            <div class="flex justify-between items-center">
                                <span class="font-bold">Total Tagihan</span>
                                <span class="text-xl font-bold text-[#3F3142]">
                                    Rp{{ number_format($transaction->total_price + $transaction->delivery_price, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection