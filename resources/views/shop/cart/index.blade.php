@extends('layouts.app')
@section('title', 'Shopping Cart')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-[80vh] py-8">
        <div class="w-[80%] min-h-[60vh] mx-auto">
            @if ($cartItems->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-4">
                        @foreach ($cartItems as $item)
                            <x-pages.shop.cart-item :item="$item" />
                        @endforeach

                        <form action="{{ route('cart.clear') }}" method="POST" class="mt-4">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Clear all items from cart?')"
                                class="text-red-500 hover:text-red-700 font-semibold">
                                <x-heroicon-o-trash class="w-5 h-5 inline mr-2" />
                                Bersihkan Keranjang
                            </button>
                        </form>
                    </div>

                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-lg p-6 sticky top-24">
                            <h2 class="text-2xl font-bold mb-6">Ringkasan Pesanan</h2>

                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal ({{ $cartItems->count() }} items)</span>
                                    <span class="font-semibold">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>

                                @foreach ($cartItems as $item)
                                    @if ($item->product->discount_amount > 0)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 w-[80%]">Diskon {{ $item->product->name }}</span>
                                            <span class="font-semibold text-gray-600">
                                                -Rp{{ number_format($item->product->discount_amount * $item->quantity, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endif
                                @endforeach

                                @if ($voucherDiscount > 0)
                                    <div class="flex justify-between">
                                        <span>Diskon Voucher</span>
                                        <span class="font-semibold text-gray-600">
                                            -Rp{{ number_format($voucherDiscount, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endif

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
                                <h3 class="font-semibold mb-4 text-lg">Voucher Tersedia</h3>

                                @if ($availableVouchers->count() > 0)
                                    <div class="space-y-3 mb-4">
                                        @foreach ($availableVouchers as $voucher)
                                            <div
                                                class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                                                <div class="flex-1">
                                                    <p class="font-semibold text-sm">{{ $voucher->base_voucher->name }}</p>
                                                    <p class="text-xs">Hemat
                                                        Rp{{ number_format($voucher->base_voucher->disc_amt, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                                <form action="{{ route('cart.voucher.apply') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="voucher_id" value="{{ $voucher->id }}">
                                                    <button type="submit"
                                                        class="px-4 py-2 bg-[#3F3142] text-white rounded-lg hover:bg-[#5C4B5E] transition-colors text-sm font-semibold whitespace-nowrap">
                                                        Gunakan
                                                    </button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if ($availableVouchers->hasPages())
                                        <div class="border-t pt-3 mt-3">
                                            {{ $availableVouchers->links('vendor.pagination.simple') }}
                                        </div>
                                    @endif
                                @else
                                    <p class="text-sm text-gray-500 mb-4">Tidak ada voucher tersedia.</p>
                                @endif

                                @if ($appliedVouchers->count() > 0)
                                    <div class="mt-4 pt-4 border-t">
                                        <h4 class="font-semibold mb-3 text-sm text-gray-700">Voucher Digunakan:</h4>
                                        <div class="space-y-2">
                                            @foreach ($appliedVouchers as $voucher)
                                                <div
                                                    class="flex items-center justify-between p-3 bg-[#dbdeff]/20 text-black border rounded-lg">
                                                    <div class="flex-1">
                                                        <p class="font-semibold text-sm">{{ $voucher->base_voucher->name }}
                                                        </p>
                                                        <p class="text-xs">
                                                            -Rp{{ number_format($voucher->base_voucher->disc_amt, 0, ',', '.') }}
                                                        </p>
                                                    </div>
                                                    <form action="{{ route('cart.voucher.remove') }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="voucher_id"
                                                            value="{{ $voucher->id }}">
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-800 text-sm font-semibold">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div
                    class="bg-white rounded-lg shadow-lg p-12 text-center w-full min-h-[60vh] justify-center items-center flex flex-col">
                    <svg class="w-32 h-32 mx-auto mb-6 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
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

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#3F3142',
                timer: 2000
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{{ session('error') }}",
                confirmButtonColor: '#3F3142',
            });
        @endif
    </script>
@endsection
