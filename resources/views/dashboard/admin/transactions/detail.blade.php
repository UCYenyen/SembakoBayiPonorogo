@extends('layouts.app')
@section('title', 'Transaction Detail')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[80%] mx-auto">
            <div class="flex justify-between items-center mb-6 bg-white shadow-lg rounded-lg px-6 py-4">
                <div class="flex gap-4 justify-center items-center">
                    <a href="{{ route('admin.transactions.index') }}"
                        class="text-white bg-[#3F3142] hover:bg-[#5C4B5E] transition-colors rounded-full p-2 hover:underline">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                    </a>
                    <h1 class="text-4xl font-bold">Transaction Detail #{{ $transaction->id }}</h1>
                </div>

                <a href="{{ route('admin.transactions.edit', $transaction) }}"
                    class="bg-[#3F3142] text-white px-6 py-2 rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors">
                    Edit
                </a>
            </div>



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
                                <span class="font-semibold">+{{ $transaction->user->phone_number ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Alamat</span>
                                <span class="font-semibold">
                                    {{ $transaction->address->subdistrict_name }}, {{ $transaction->address->city_name }}
                                    {{ $transaction->address->province_name }},
                                    {{ $transaction->address->postal_code }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Detail Alamat</span>
                                <span class="font-semibold">
                                    {{ $transaction->address->extra_detail }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Order Items --}}
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-6">Pesanan</h2>
                        <div class="space-y-4">
                            @foreach ($transaction->transaction_items as $item)
                                <div class="flex gap-4 pb-4 border-b last:border-b-0">
                                    <div class="relative">
                                        <img src="{{ $item->product->image_path }}" alt="{{ $item->product->name }}"
                                            class="w-24 h-24 object-cover rounded-lg">
                                        @if ($transaction->isCompleted() && $item->testimony)
                                            <div
                                                class="absolute top-2 left-2 px-2 py-1 bg-white/90 backdrop-blur-sm shadow-md flex items-center gap-1 rounded-lg">
                                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966c.3.922-.755 1.688-1.54 1.118l-3.38-2.454a1 1 0 00-1.175 0l-3.38 2.454c-.784.57-1.838-.196-1.54-1.118l1.287-3.966a1 1 0 00-.364-1.118L2.05 9.394c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69l1.286-3.967z" />
                                                </svg>
                                                <span
                                                    class="font-bold text-xs text-yellow-600">{{ $item->testimony->rating_star }}/5</span>
                                            </div>
                                        @endif
                                    </div>
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
                                @if ($transaction->isCompleted())
                                    <div class="mt-4 pt-3 border-t border-gray-50 flex flex-wrap gap-2">
                                        @if ($item->testimony)
                                            <a href="{{ route('user.testimonies.show', $item->testimony) }}"
                                                class="flex-1 text-center px-3 py-2 bg-transparent border-[#3F3142] border-2 text-[#3F3142] hover:text-white rounded-lg font-semibold hover:bg-[#3F3142] transition-colors text-xs whitespace-nowrap">
                                                Lihat Ulasan
                                            </a>
                                            <a href="{{ route('user.testimonies.edit', $item->testimony) }}"
                                                class="flex-1 text-center px-3 py-2 bg-[#3F3142] text-white rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors text-xs whitespace-nowrap">
                                                Edit
                                            </a>
                                        @else
                                            <a href="{{ route('user.testimonies.create', $item) }}"
                                                class="w-full text-center px-4 py-2 bg-[#3F3142] text-white rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors text-sm">
                                                Ulas Produk
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
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
                                <span class="font-semibold uppercase">{{ $transaction->delivery->courier_code }} -
                                    {{ $transaction->delivery->name }}</span>
                            </div>
                            @if ($transaction->no_resi)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Tracking #</span>
                                    <span class="font-mono font-bold text-blue-600">{{ $transaction->no_resi }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="mt-6 pt-6 border-t space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span
                                    class="font-semibold">Rp{{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Ongkos Kirim</span>
                                <span
                                    class="font-semibold">Rp{{ number_format($transaction->delivery_price, 0, ',', '.') }}</span>
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
