@extends('layouts.app')
@section('title', 'Order Detail')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[90%] lg:w-[80%] mx-auto">
            <!-- Back Button -->
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 mb-6 text-[#3F3142] hover:underline">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke pesanan
            </a>

            <h1 class="text-4xl font-bold mb-8">Detail Transaksi</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Order Items & Status -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Order Status Card -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h2 class="text-2xl font-bold">Transaksi #{{ $transaction->id }}</h2>
                                <p class="text-gray-600">{{ $transaction->created_at->format('d F Y, H:i') }}</p>
                            </div>
                            <span
                                class="px-4 py-2 rounded-full text-sm font-semibold {{ $transaction->getStatusBadgeClass() }}">
                                {{ $transaction->getStatusLabel() }}
                            </span>
                        </div>

                        <!-- Order Timeline -->
                        <div class="space-y-4">
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-10 h-10 rounded-full {{ $transaction->isPendingPayment() || $transaction->isPaid() || $transaction->isProcessing() || $transaction->isShipped() || $transaction->isDelivered() || $transaction->isCompleted() ? 'bg-[#3F3142]' : 'bg-gray-300' }} flex items-center justify-center text-white">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div
                                        class="w-0.5 h-12 {{ $transaction->isPaid() || $transaction->isProcessing() || $transaction->isShipped() || $transaction->isDelivered() || $transaction->isCompleted() ? 'bg-[#3F3142]' : 'bg-gray-300' }}">
                                    </div>
                                </div>
                                <div class="flex-1 pb-4">
                                    <h3 class="font-semibold">Memesan</h3>
                                    <p class="text-sm text-gray-600">{{ $transaction->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-10 h-10 rounded-full {{ $transaction->isPaid() || $transaction->isProcessing() || $transaction->isShipped() || $transaction->isDelivered() || $transaction->isCompleted() ? 'bg-[#3F3142]' : 'bg-gray-300' }} flex items-center justify-center text-white">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z">
                                            </path>
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div
                                        class="w-0.5 h-12 {{ $transaction->isProcessing() || $transaction->isShipped() || $transaction->isDelivered() || $transaction->isCompleted() ? 'bg-[#3F3142]' : 'bg-gray-300' }}">
                                    </div>
                                </div>
                                <div class="flex-1 pb-4">
                                    <h3 class="font-semibold">Pesanan Dibayar</h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $transaction->isPaid() || $transaction->isProcessing() || $transaction->isShipped() || $transaction->isDelivered() || $transaction->isCompleted() ? $transaction->updated_at->format('d M Y, H:i') : '-' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-10 h-10 rounded-full {{ $transaction->isProcessing() || $transaction->isShipped() || $transaction->isDelivered() || $transaction->isCompleted() ? 'bg-[#3F3142]' : 'bg-gray-300' }} flex items-center justify-center text-white">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z">
                                            </path>
                                            <path
                                                d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div
                                        class="w-0.5 h-12 {{ $transaction->isShipped() || $transaction->isDelivered() || $transaction->isCompleted() ? 'bg-[#3F3142]' : 'bg-gray-300' }}">
                                    </div>
                                </div>
                                <div class="flex-1 pb-4">
                                    <h3 class="font-semibold">Pesanan Dikirim</h3>
                                    <p class="text-sm text-gray-600">
                                        @if ($transaction->isShipped() || $transaction->isDelivered() || $transaction->isCompleted())
                                            {{ $transaction->updated_at->format('d M Y, H:i') }}
                                            @if ($transaction->no_resi)
                                                <br>Resi: <span class="font-mono">{{ $transaction->no_resi }}</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-10 h-10 rounded-full {{ $transaction->isDelivered() || $transaction->isCompleted() ? 'bg-[#3F3142]' : 'bg-gray-300' }} flex items-center justify-center text-white">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold">Pesanan Selesai</h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $transaction->isDelivered() || $transaction->isCompleted() ? $transaction->updated_at->format('d M Y, H:i') : '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-6">Item yang dibeli</h2>
                        <div class="space-y-4">
                            @foreach ($transaction->transaction_items as $item)
                                <div class="flex gap-4 pb-4 border-b last:border-b-0">
                                    <img src="{{ asset('storage/' . $item->product->image_url) }}"
                                        alt="{{ $item->product->name }}" class="w-24 h-24 object-cover rounded-lg">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-lg mb-1">{{ $item->product->name }}</h4>
                                        <p class="text-sm text-gray-600 mb-2">{{ $item->product->category->name }}</p>
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
                </div>

                <!-- Right Column: Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-lg p-6 sticky top-24">
                        <h2 class="text-2xl font-bold mb-6">Ringkasan Pesanan</h2>

                        <div class="mb-6 pb-6 border-b">
                            <h3 class="font-semibold mb-3 text-sm uppercase text-gray-500">Daftar Produk</h3>
                            <div class="space-y-3">
                                @foreach ($transaction->transaction_items as $item)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-700 flex-1">
                                            {{ $item->product->name }}
                                            <span class="text-gray-400">x{{ $item->quantity }}</span>
                                        </span>
                                        <span class="font-medium text-[#3F3142]">
                                            Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-6 pb-6 border-b">
                            <h3 class="font-semibold mb-2 text-sm uppercase text-gray-500">Alamat Pengiriman</h3>
                            <p class="text-sm text-gray-700 leading-relaxed">
                                <span class="font-bold">{{ $transaction->address->name }}</span><br>
                                {{ $transaction->address->extra_detail }}<br>
                                {{ $transaction->address->subdistrict_name }}, {{ $transaction->address->city_name }}<br>
                                {{ $transaction->address->province_name }}, {{ $transaction->address->postal_code }}
                            </p>
                        </div>

                        <div class="mb-6 pb-6 border-b">
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">Metode Pembayaran</span>
                                <span class="font-semibold">{{ $transaction->payment_method ?? 'Midtrans' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kurir</span>
                                <span class="font-semibold">{{ $transaction->delivery->name }}</span>
                            </div>
                        </div>

                        <div class="space-y-3 mb-6">
                            @php
                                $subtotal = $transaction->transaction_items->sum(
                                    fn($item) => $item->price * $item->quantity,
                                );
                                $tax = $subtotal * 0.11;
                            @endphp
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-semibold">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Pajak (11%)</span>
                                <span class="font-semibold">Rp{{ number_format($tax, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Ongkos Kirim</span>
                                <span
                                    class="font-semibold">Rp{{ number_format($transaction->delivery_price, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="border-t pt-4 mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold">Total Tagihan</span>
                                <span class="text-2xl font-bold text-[#3F3142]">
                                    Rp{{ number_format($subtotal + $tax + $transaction->delivery_price, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
    <script>
        document.getElementById('pay-now-btn')?.addEventListener('click', function() {
            this.disabled = true;
            const btn = this;

            fetch('{{ route('payment.retry', $transaction) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.snap_token) {
                        window.snap.pay(data.snap_token, {
                            onSuccess: (result) => window.location.href =
                                `/payment/finish/{{ $transaction->id }}`,
                            onPending: (result) => window.location.href =
                                `/payment/unfinish/{{ $transaction->id }}`,
                            onError: () => {
                                alert('Pembayaran gagal');
                                btn.disabled = false;
                            },
                            onClose: () => {
                                btn.disabled = false;
                            }
                        });
                    } else {
                        alert(data.error || 'Terjadi kesalahan');
                        btn.disabled = false;
                    }
                })
                .catch(() => {
                    alert('Kesalahan sistem');
                    btn.disabled = false;
                });
        });
    </script>
@endsection
