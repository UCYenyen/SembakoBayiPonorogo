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
                                        <input type="radio" name="address_id" value="{{ $address->id }}" 
                                               class="mt-1" {{ $address->is_default ? 'checked' : '' }} required>
                                        <div class="flex-1">
                                            @if($address->is_default)
                                                <span class="inline-block px-2 py-1 bg-[#3F3142] text-white text-xs font-semibold rounded mb-2">
                                                    Default
                                                </span>
                                            @endif
                                            <p class="text-gray-700 leading-relaxed">{{ $address->detail }}</p>
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

                    <!-- Payment Method -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-4">Payment Method</h2>
                        <div class="space-y-3">
                            @foreach($payments as $payment)
                                <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                    <input type="radio" name="payment_id" value="{{ $payment->id }}" 
                                           {{ $loop->first ? 'checked' : '' }} required>
                                    <span class="font-semibold">{{ $payment->method }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Delivery Method -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-4">Delivery Method</h2>
                        <div class="space-y-3">
                            @foreach($deliveries as $delivery)
                                <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                    <input type="radio" name="delivery_id" value="{{ $delivery->id }}" 
                                           {{ $loop->first ? 'checked' : '' }} required>
                                    <span class="font-semibold">{{ $delivery->name }}</span>
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

                        <button type="button" id="pay-button"
                                class="w-full bg-[#3F3142] text-white py-4 rounded-lg font-bold text-lg hover:bg-[#5C4B5E] transition-colors mb-4">
                            Proceed to Payment
                        </button>

                        <a href="{{ route('cart.index') }}" 
                           class="block w-full text-center py-3 border-2 border-[#3F3142] text-[#3F3142] rounded-lg font-semibold hover:bg-[#3F3142] hover:text-white transition-colors">
                            Back to Cart
                        </a>

                        <div id="payment-status" class="mt-4 text-center text-sm"></div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function(){
            const statusDiv = document.getElementById('payment-status');
            statusDiv.innerHTML = '<p class="text-blue-600">Processing...</p>';
            
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
            .then(response => response.json())
            .then(data => {
                if (data.snap_token) {
                    statusDiv.innerHTML = '<p class="text-green-600">Opening payment window...</p>';
                    
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result){
                            statusDiv.innerHTML = '<p class="text-green-600">Payment successful!</p>';
                            setTimeout(() => {
                                window.location.href = '/payment/finish/' + data.transaction_id;
                            }, 1000);
                        },
                        onPending: function(result){
                            statusDiv.innerHTML = '<p class="text-yellow-600">Waiting for payment...</p>';
                            setTimeout(() => {
                                window.location.href = '/dashboard/user';
                            }, 2000);
                        },
                        onError: function(result){
                            statusDiv.innerHTML = '<p class="text-red-600">Payment failed!</p>';
                        },
                        onClose: function(){
                            statusDiv.innerHTML = '<p class="text-gray-600">Payment window closed.</p>';
                        }
                    });
                } else {
                    statusDiv.innerHTML = '<p class="text-red-600">' + (data.error || 'Failed to get payment token') + '</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                statusDiv.innerHTML = '<p class="text-red-600">System error occurred.</p>';
            });
        };
    </script>
@endsection
