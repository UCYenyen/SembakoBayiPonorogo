@extends('layouts.app')
@section('title', 'Checkout')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[90%] lg:w-[80%] mx-auto">
            <h1 class="text-4xl font-bold mb-8">Bayar</h1>

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <form id="checkoutForm" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                @csrf
                <input type="hidden" name="delivery_id" id="delivery_id" required>
                <input type="hidden" name="delivery_price" id="delivery_price" value="0" required>

                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-4">Alamat Pengiriman</h2>

                        @if ($addresses->count() > 0)
                            <div class="space-y-3">
                                @foreach ($addresses as $address)
                                    <label
                                        class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors {{ $address->is_default ? 'border-[#3F3142] bg-[#FFF3F3]' : '' }}">
                                        <input type="radio" name="address_id" value="{{ $address->id }}"
                                            class="address-radio" {{ $address->is_default ? 'checked' : '' }} required
                                            onchange="loadShippingOptions()">
                                        <div class="flex-1">
                                            <div class="flex justify-start items-center gap-2">
                                                <h3 class="font-semibold">{{ $address->name }}</h3>
                                                @if ($address->is_default)
                                                    <span
                                                        class="inline-block px-3 py-1 bg-[#3F3142] text-white text-xs font-semibold rounded-full">
                                                        Alamat Default
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-600">
                                                {{ $address->extra_detail }},
                                                {{ $address->subdistrict_name }},
                                                {{ $address->district_name }},
                                                {{ $address->city_name }},
                                                {{ $address->province_name }},
                                                {{ $address->postal_code }}
                                            </p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <a href="{{ route('user.addresses.create') }}"
                                class="inline-block mt-4 text-[#3F3142] font-semibold hover:underline">
                                + Tambah Alamat Baru
                            </a>
                        @else
                            <p class="text-gray-500">Anda belum mengisi alamat anda, mohon diisi terlebih dahulu!</p>
                            <a href="{{ route('user.addresses.create') }}"
                                class="inline-block mt-4 px-6 py-2 bg-[#3F3142] text-white rounded-lg hover:bg-[#5C4B5E]">
                                Tambah Alamat
                            </a>
                        @endif
                    </div>

                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-4">Metode Pengiriman</h2>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kurir</label>
                            <div class="flex gap-4">
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="courier_brand" value="jne" class="h-4 w-4 text-[#3F3142]"
                                        onchange="loadShippingOptions()">
                                    <span class="ml-2 text-sm font-medium text-gray-900">JNE</span>
                                </label>

                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="courier_brand" value="jnt" class="h-4 w-4 text-[#3F3142]"
                                        onchange="loadShippingOptions()">
                                    <span class="ml-2 text-sm font-medium text-gray-900">J&T</span>
                                </label>
                            </div>
                        </div>

                        <div id="shipping-options-container" class="space-y-3">
                            <p class="text-sm text-gray-500 italic text-center py-2">Silahkan pilih kurir terlebih dahulu
                            </p>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-4">Metode Pembayaran</h2>
                        <div class="space-y-3">
                            @foreach ($payments as $payment)
                                <label
                                    class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                    <input type="radio" name="payment_id" value="{{ $payment->id }}" required>
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold">{{ $payment->method }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-4">Item yang dipesan</h2>
                        <div class="space-y-4">
                            @foreach ($cart->items as $item)
                                <div class="flex gap-4">
                                    <img src="{{ asset('storage/' . $item->product->image_url) }}"
                                        alt="{{ $item->product->name }}" class="w-20 h-20 object-cover rounded-lg">
                                    <div class="flex-1">
                                        <h4 class="font-semibold">{{ $item->product->name }}</h4>
                                        <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                                        <p class="text-[#3F3142] font-bold">
                                            Rp{{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-lg p-6 sticky top-24">
                        <h2 class="text-2xl font-bold mb-6">Ringkasan Pesanan</h2>
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-semibold">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Pajak (11%)</span>
                                <span class="font-semibold">Rp{{ number_format($tax, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Pengiriman</span>
                                <span class="font-semibold" id="shipping-cost-display">Rp0</span>
                            </div>
                        </div>
                        <div class="border-t pt-4 mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold">Total</span>
                                <span class="text-2xl font-bold text-[#3F3142]" id="total-display">
                                    Rp{{ number_format($total, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                        <button type="button" id="pay-button"
                            class="w-full bg-[#3F3142] text-white py-4 rounded-lg font-bold text-lg hover:bg-[#5C4B5E] transition-colors">
                            Bayar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script>
        const subtotal = {{ $subtotal }};
        const tax = {{ $tax }};
        // Pastikan berat minimal 1000gr jika data di database kosong
        const totalWeight = {{ $cart->items->sum(fn($item) => ($item->product->weight ?? 1000) * $item->quantity) }} ||
            1000;
        let selectedShippingCost = 0;

        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function loadShippingOptions() {
            const addressRadio = document.querySelector('.address-radio:checked');
            const courierRadio = document.querySelector('input[name="courier_brand"]:checked');
            const container = document.getElementById('shipping-options-container');

            if (!addressRadio || !courierRadio) return;

            const addressId = addressRadio.value;
            const courier = courierRadio.value;

            container.innerHTML = '<div class="text-center p-4">Menghitung...</div>';

            fetch(`/check-ongkir/${addressId}?weight=${totalWeight}&courier=${courier}`)
                .then(response => response.json())
                .then(result => {
                    // DEBUG: Lihat di F12 Console untuk memastikan harga ada
                    console.log("Data API:", result.data);

                    if (result.success && result.data.length > 0) {
                        let html = '';
                        result.data.forEach(option => {

                            // CARA AMBIL HARGA (Mencoba semua kemungkinan field)
                            let costValue = 0;
                            if (option.price) {
                                costValue = parseInt(option.price);
                            } else if (option.costs && option.costs[0] && option.costs[0].value) {
                                costValue = parseInt(option.costs[0].value);
                            } else if (option.cost) {
                                costValue = parseInt(option.cost);
                            }

                            const serviceName = option.service || 'Reguler';

                            html += `
                            <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 mb-2">
                                <input type="radio" name="shipping_option" value="${costValue}" 
                                    onchange="updateShippingCost(${costValue}, '${serviceName}')">
                                <div class="flex-1">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="font-bold">${courier.toUpperCase()} ${serviceName}</p>
                                            <p class="text-xs text-gray-400">Berat: ${totalWeight}gr</p>
                                        </div>
                                        <span class="font-bold text-lg">Rp${formatNumber(costValue)}</span>
                                    </div>
                                </div>
                            </label>`;
                        });
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = '<p class="text-red-500 p-4">Tidak ada layanan kurir tersedia.</p>';
                    }
                })
                .catch(err => {
                    console.error("Fetch Error:", err);
                    container.innerHTML = '<p class="text-red-500 p-4">Gagal memuat ongkir.</p>';
                });
        }

        function updateShippingCost(cost, service) {
            selectedShippingCost = parseInt(cost);
            document.getElementById('delivery_price').value = selectedShippingCost;
            document.getElementById('delivery_id').value = 1;

            document.getElementById('shipping-cost-display').textContent = 'Rp' + formatNumber(selectedShippingCost);

            const grandTotal = subtotal + tax + selectedShippingCost;
            document.getElementById('total-display').textContent = 'Rp' + formatNumber(grandTotal);
        }

        document.getElementById('pay-button').addEventListener('click', function() {
            const selectedAddress = document.querySelector('.address-radio:checked');
            const selectedPayment = document.querySelector('input[name="payment_id"]:checked');
            const selectedShipping = document.querySelector('input[name="shipping_option"]:checked');

            if (!selectedAddress || !selectedPayment || !selectedShipping) {
                alert('Mohon lengkapi Alamat, Kurir, dan Metode Pembayaran!');
                return;
            }

            this.disabled = true;

            fetch('{{ route('payment.process') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        address_id: selectedAddress.value,
                        payment_id: selectedPayment.value,
                        delivery_price: selectedShippingCost,
                        courier: document.querySelector('input[name="courier_brand"]:checked').value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.snap_token) {
                        window.snap.pay(data.snap_token, {
                            onSuccess: (result) => window.location.href = '/payment/finish?order_id=' +
                                result.order_id,
                            onPending: (result) => window.location.href =
                                '/payment/unfinish?order_id=' + result.order_id,
                            onError: () => {
                                alert('Pembayaran gagal');
                                this.disabled = false;
                            },
                            onClose: () => {
                                this.disabled = false;
                            }
                        });
                    } else {
                        alert(data.error || 'Terjadi kesalahan');
                        this.disabled = false;
                    }
                })
                .catch(() => {
                    alert('Kesalahan sistem');
                    this.disabled = false;
                });
        });
    </script>
@endsection
