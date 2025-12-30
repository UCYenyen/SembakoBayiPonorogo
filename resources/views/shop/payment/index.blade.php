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
                                {{-- <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="courier_brand" value="gosend" class="h-4 w-4 text-[#3F3142]"
                                        onchange="loadShippingOptions()">
                                    <span class="ml-2 text-sm font-medium text-gray-900">GOSEND</span>
                                </label> --}}
                            </div>
                        </div>

                        <div id="shipping-options-container" class="space-y-3">
                            <p class="text-sm text-gray-500 italic text-center py-2">Silahkan pilih kurir terlebih dahulu
                            </p>
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
        const totalWeight = {{ $cart->items->sum(fn($item) => ($item->product->weight ?? 0) * $item->quantity) }};
        let selectedShippingCost = 0;

        // Mapping courier ke delivery_id sesuai database
        const courierToDeliveryId = {
            'jne': 1,
            'jnt': 2,
            'gosend': 3
        };

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
            const itemValue = typeof subtotal !== 'undefined' ? subtotal : 50000;

            container.innerHTML = '<div class="text-center p-4 text-gray-500 italic">Mengecek tarif...</div>';

            let url = `/check-ongkir/${addressId}?weight=${totalWeight}&courier=${courier}&item_value=${itemValue}`;

            fetch(url)
                .then(response => response.json())
                .then(result => {
                    if (result.success && result.data.length > 0) {
                        let html = '';
                        result.data.forEach(option => {
                            let costValue = parseInt(option.cost);
                            html += `
                        <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 mb-3 border-gray-200">
                            <input type="radio" name="shipping_option" value="${costValue}" 
                                data-courier="${courier}"
                                onchange="updateShippingCost(${costValue}, '${option.service}', '${courier}')" class="w-4 h-4">
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm uppercase">${option.courier} ${option.service}</p>
                                        <p class="text-xs text-gray-500">${option.description}</p>
                                        <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-1 rounded">Estimasi: ${option.etd}</span>
                                    </div>
                                    <span class="font-bold text-base text-[#3F3142]">Rp${formatNumber(costValue)}</span>
                                </div>
                            </div>
                        </label>`;
                        });
                        container.innerHTML = html;
                    } else {
                        container.innerHTML =
                            `<div class="p-4 text-center text-red-500">${result.message || 'Layanan tidak tersedia.'}</div>`;
                    }
                })
                .catch(() => {
                    container.innerHTML = '<div class="p-4 text-center text-red-500">Gagal memuat data.</div>';
                });
        }

        function updateShippingCost(cost, service, courier) {
            selectedShippingCost = parseInt(cost);

            // Set delivery_price
            document.getElementById('delivery_price').value = selectedShippingCost;

            // Set delivery_id berdasarkan courier yang dipilih
            const deliveryId = courierToDeliveryId[courier] || 1; // default ke 1 jika tidak ada mapping
            document.getElementById('delivery_id').value = deliveryId;

            // Update tampilan
            document.getElementById('shipping-cost-display').textContent = 'Rp' + formatNumber(selectedShippingCost);

            const grandTotal = subtotal + selectedShippingCost;
            document.getElementById('total-display').textContent = 'Rp' + formatNumber(grandTotal);
        }

        document.getElementById('pay-button').addEventListener('click', function() {
            const selectedAddress = document.querySelector('.address-radio:checked');
            const selectedShipping = document.querySelector('input[name="shipping_option"]:checked');
            const selectedCourier = document.querySelector('input[name="courier_brand"]:checked');

            if (!selectedAddress || !selectedShipping || !selectedCourier) {
                alert('Mohon lengkapi Alamat dan Kurir terlebih dahulu!');
                return;
            }

            // Ambil delivery_id dari hidden input yang sudah di-set oleh updateShippingCost
            const deliveryId = document.getElementById('delivery_id').value;

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
                        delivery_id: deliveryId, // Kirim delivery_id yang sudah sesuai
                        delivery_price: selectedShippingCost,
                        courier: selectedCourier.value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.snap_token && data.transaction_id) {
                        window.snap.pay(data.snap_token, {
                            onSuccess: (result) => window.location.href =
                                `/payment/finish/${data.transaction_id}`,
                            onPending: (result) => window.location.href =
                                `/payment/unfinish/${data.transaction_id}`,
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
