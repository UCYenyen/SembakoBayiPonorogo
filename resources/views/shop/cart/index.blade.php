@extends('layouts.app')
@section('title', 'Shopping Cart')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-[80vh] py-8">
        <div class="w-[80%] min-h-[60vh] mx-auto">
            @if($cartItems->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-4">
                        @foreach($cartItems as $item)
                            <x-pages.shop.cart-item :item="$item" />
                        @endforeach

                        <form action="{{ route('cart.clear') }}" method="POST" class="mt-4">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('Clear all items from cart?')"
                                    class="text-red-500 hover:text-red-700 font-semibold">
                                <x-heroicon-o-trash class="w-5 h-5 inline mr-2" />
                                Bersihkan Keranjang
                            </button>
                        </form>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-lg p-6 sticky top-24">
                            <h2 class="text-2xl font-bold mb-6">Ringkasan Pesanan</h2>

                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal ({{ $cartItems->count() }} items)</span>
                                    <span class="font-semibold">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Pengiriman</span>
                                    <span class="font-semibold">Rp{{ number_format($shippingCost, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="border-t pt-4 mb-6">
                                <div class="flex justify-between items-center">
                                    <span class="text-xl font-bold">Total</span>
                                    <span class="text-2xl font-bold text-[#3F3142]">
                                        Rp{{ number_format($total, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <a href="/payment" 
                               class="block w-full bg-[#3F3142] text-white text-center py-4 rounded-lg font-bold text-lg hover:bg-[#5C4B5E] transition-colors mb-4">
                                Bayar
                            </a>

                            <a href="/shop" 
                               class="block w-full text-center py-3 border-2 border-[#3F3142] text-[#3F3142] rounded-lg font-semibold hover:bg-[#3F3142] hover:text-white transition-colors">
                                Lanjut Belanja
                            </a>

                            <div class="mt-6 pt-6 border-t">
                                <h3 class="font-semibold mb-3">Ada voucher?</h3>
                                <form class="flex gap-2">
                                    <input type="text" 
                                           placeholder="Enter code"
                                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142]">
                                    <button type="submit" 
                                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-semibold">
                                        Gunakan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-lg p-12 text-center w-full min-h-[60vh] justify-center items-center flex flex-col">
                    <svg class="w-32 h-32 mx-auto mb-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <h2 class="text-3xl font-bold text-gray-700 mb-4">Keranjang Belanjamu Kosong!</h2>
                    <p class="text-gray-500 mb-6">Mulai belanja sekarang!</p>
                    <a href="/shop" 
                       class="inline-block px-8 py-4 bg-[#3F3142] text-white rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors">
                        Mulai Belanja
                    </a>
                </div>
            @endif
        </div>
    </main>

    <script>
        function incrementQuantity(button) {
            const input = button.previousElementSibling;
            const max = parseInt(input.max);
            const current = parseInt(input.value);
            
            if (current < max) {
                input.value = current + 1;
                input.form.submit();
            }
        }

        function decrementQuantity(button) {
            const input = button.nextElementSibling;
            const current = parseInt(input.value);
            
            if (current > 1) {
                input.value = current - 1;
                input.form.submit();
            }
        }
    </script>
@endsection
