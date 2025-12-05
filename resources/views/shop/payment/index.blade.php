@extends('layouts.app')
@section('title', 'Checkout')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[90%] lg:w-[80%] mx-auto">
            <h1 class="text-4xl font-bold mb-8">Checkout</h1>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <form id="checkoutForm" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                @csrf
                
                <!-- Left Column: Shipping & Payment Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Shipping Address -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-4">Shipping Address</h2>
                        
                        @if($addresses->count() > 0)
                            <div class="space-y-3">
                                @foreach($addresses as $address)
                                    <label class="flex items-start gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors {{ $address->is_default ? 'border-[#3F3142] bg-[#FFF3F3]' : '' }}">
                                        <input type="radio" 
                                               name="address_id" 
                                               value="{{ $address->id }}" 
                                               data-city-id="{{ $address->city_id }}"
                                               class="mt-1 address-radio" 
                                               {{ $address->is_default ? 'checked' : '' }} 
                                               required
                                               onchange="loadShippingOptions(this.value)">
                                        <div class="flex-1">
                                            @if($address->is_default)
                                                <span class="inline-block px-2 py-1 bg-[#3F3142] text-white text-xs font-semibold rounded mb-2">
                                                    Default
                                                </span>
                                            @endif
                                            <p class="text-gray-700 leading-relaxed">{{ $address->detail }}</p>
                                            @if($address->city_name)
                                                <p class="text-xs text-gray-500 mt-1">📍 {{ $address->city_name }}</p>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <a href="{{ route('user.addresses.create') }}" 
                               class="inline-block mt-4 text-[#3F3142] font-semibold hover:underline">
                                + Add New Address
                            </a>
                        @else
                            <p class="text-gray-500">No address found. Please add an address first.</p>
                            <a href="{{ route('user.addresses.create') }}" 
                               class="inline-block mt-4 px-6 py-2 bg-[#3F3142] text-white rounded-lg hover:bg-[#5C4B5E]">
                                Add Address
                            </a>
                        @endif
                    </div>

                    <!-- ✅ Delivery Method - Dynamic from RajaOngkir -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-4">Delivery Method</h2>
                        
                        <div id="shipping-options-container">
                            <p class="text-gray-500 text-center py-4">
                                <svg class="animate-spin h-5 w-5 mx-auto mb-2 text-[#3F3142]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Select an address to see shipping options
                            </p>
                        </div>
                        
                        <input type="hidden" name="shipping_cost" id="shipping_cost" value="0" required>
                        <input type="hidden" name="shipping_service" id="shipping_service" required>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-4">Payment Method</h2>
                        <div class="space-y-3">
                            @foreach($payments as $payment)
                                <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors {{ $loop->first ? 'border-[#3F3142] bg-[#FFF3F3]' : '' }}">
                                    <input type="radio" name="payment_id" value="{{ $payment->id }}" 
                                           {{ $loop->first ? 'checked' : '' }} required>
                                    <div class="flex items-center gap-2">
                                        @if($payment->getPaymentType())
                                            <svg class="w-6 h-6 {{ $payment->getIconColor() }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                            </svg>
                                        @endif
                                        <span class="font-semibold">{{ $payment->method }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-4">Order Items</h2>
                        <div class="space-y-4">
                            @foreach($cart->items as $item)
                                <div class="flex gap-4">
                                    <img src="{{ asset('storage/' . $item->product->image_url) }}" 
                                         alt="{{ $item->product->name }}"
                                         class="w-20 h-20 object-cover rounded-lg">
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

                <!-- Right Column: Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-lg p-6 sticky top-24">
                        <h2 class="text-2xl font-bold mb-6">Order Summary</h2>

                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-semibold">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax (11%)</span>
                                <span class="font-semibold">Rp{{ number_format($tax, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping</span>
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

                        <button type="button"
                                id="pay-button"
                                class="w-full bg-[#3F3142] text-white py-4 rounded-lg font-bold text-lg hover:bg-[#5C4B5E] transition-colors">
                            Proceed to Payment
                        </button>

                        <a href="{{ route('cart.index') }}" 
                           class="block w-full text-center py-3 border-2 border-[#3F3142] text-[#3F3142] rounded-lg font-semibold hover:bg-[#3F3142] hover:text-white transition-colors mt-4">
                            Back to Cart
                        </a>

                        <div id="payment-status" class="mt-4 text-center text-sm"></div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script>
    const subtotal = {{ $subtotal }};
    const tax = {{ $tax }};
    let selectedShippingCost = 0;

    // Load shipping options when address changes
    function loadShippingOptions(addressId) {
        const container = document.getElementById('shipping-options-container');
        container.innerHTML = `
            <p class="text-gray-500 text-center py-4">
                <svg class="animate-spin h-5 w-5 mx-auto mb-2 text-[#3F3142]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Loading shipping options...
            </p>
        `;

        fetch('/api/shipping-options', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ address_id: addressId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.shipping_options.length > 0) {
                let html = '<div class="space-y-3">';
                
                data.shipping_options.forEach((option, index) => {
                    html += `
                        <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors ${index === 0 ? 'border-[#3F3142] bg-[#FFF3F3]' : ''}">
                            <input type="radio" 
                                   name="delivery_option" 
                                   value="${option.delivery_id}"
                                   data-cost="${option.cost}"
                                   data-service="${option.display_name}"
                                   ${index === 0 ? 'checked' : ''}
                                   onchange="updateShippingCost(${option.delivery_id}, ${option.cost}, '${option.display_name}')"
                                   required>
                            <div class="flex-1">
                                <p class="font-semibold">${option.display_name}</p>
                                <p class="text-xs text-gray-500">Est: ${option.etd} days</p>
                                <p class="font-bold text-[#3F3142] mt-1">Rp${formatNumber(option.cost)}</p>
                            </div>
                        </label>
                    `;
                });
                
                html += '</div>';
                container.innerHTML = html;
                
                // Auto-select first option
                if (data.shipping_options[0]) {
                    updateShippingCost(
                        data.shipping_options[0].delivery_id, 
                        data.shipping_options[0].cost, 
                        data.shipping_options[0].display_name
                    );
                }
            } else {
                container.innerHTML = '<p class="text-red-500 text-center py-4">No shipping options available for this address</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = '<p class="text-red-500 text-center py-4">Failed to load shipping options. Please try again.</p>';
        });
    }

    function updateShippingCost(deliveryId, cost, service) {
        selectedShippingCost = cost;
        
        // ✅ Update hidden fields dengan delivery_id
        document.getElementById('delivery_id').value = deliveryId;
        document.getElementById('shipping_cost').value = cost;
        document.getElementById('shipping_service').value = service;
        
        document.getElementById('shipping-cost-display').textContent = 'Rp' + formatNumber(cost);
        
        const total = subtotal + tax + cost;
        document.getElementById('total-display').textContent = 'Rp' + formatNumber(total);
    }

    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Load default shipping options on page load
    document.addEventListener('DOMContentLoaded', function() {
        const defaultAddress = document.querySelector('.address-radio:checked');
        if (defaultAddress) {
            loadShippingOptions(defaultAddress.value);
        }
    });

    // Payment button handler
    document.getElementById('pay-button').onclick = function(){
        const statusDiv = document.getElementById('payment-status');
        const submitBtn = document.getElementById('pay-button');
        
        // ✅ Validate delivery selection
        const deliveryId = document.getElementById('delivery_id').value;
        const shippingCost = document.getElementById('shipping_cost').value;
        const shippingService = document.getElementById('shipping_service').value;
        
        if (!deliveryId || !shippingCost || shippingCost === '0' || !shippingService) {
            statusDiv.innerHTML = '<p class="text-red-600">Please select a shipping method first!</p>';
            return;
        }
        
        statusDiv.innerHTML = '<p class="text-blue-600">Processing...</p>';
        submitBtn.disabled = true;
        
        const form = document.getElementById('checkoutForm');
        const formData = new FormData(form);

        fetch('{{ route('payment.create') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            console.log('Response:', data);
            
            if (data.success && data.snap_token) {
                statusDiv.innerHTML = '<p class="text-green-600">Opening payment window...</p>';
                
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result){
                        console.log('Payment success:', result);
                        statusDiv.innerHTML = '<p class="text-green-600">Payment successful!</p>';
                        setTimeout(() => {
                            window.location.href = '/payment/finish/' + data.transaction_id;
                        }, 1000);
                    },
                    onPending: function(result){
                        console.log('Payment pending:', result);
                        statusDiv.innerHTML = '<p class="text-yellow-600">Waiting for payment...</p>';
                        setTimeout(() => {
                            window.location.href = '/dashboard/user';
                        }, 2000);
                    },
                    onError: function(result){
                        console.error('Payment error:', result);
                        statusDiv.innerHTML = '<p class="text-red-600">Payment failed!</p>';
                        submitBtn.disabled = false;
                    },
                    onClose: function(){
                        console.log('Payment popup closed');
                        statusDiv.innerHTML = '<p class="text-gray-600">Payment window closed.</p>';
                        submitBtn.disabled = false;
                    }
                });
            } else {
                throw new Error(data.error || 'Failed to get payment token');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            let errorMessage = 'System error occurred.';
            if (error.message) {
                errorMessage = error.message;
            }
            if (error.details) {
                errorMessage += '<br><small>' + JSON.stringify(error.details) + '</small>';
            }
            
            statusDiv.innerHTML = '<p class="text-red-600">' + errorMessage + '</p>';
            submitBtn.disabled = false;
        });
    };
    </script>

    <!-- Hidden fields -->
    <input type="hidden" name="delivery_id" id="delivery_id" required>
    <input type="hidden" name="shipping_cost" id="shipping_cost" value="0" required>
    <input type="hidden" name="shipping_service" id="shipping_service" required>
@endsection
