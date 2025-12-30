@extends('layouts.app')
@section('title', 'My Orders')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[90%] lg:w-[80%] mx-auto">
            <h1 class="text-4xl font-bold mb-8">My Orders</h1>

            <!-- Status Tabs -->
            <div class="bg-white rounded-lg shadow-md mb-6 overflow-x-auto">
                <div class="flex border-b min-w-max">
                    <a href="{{ route('dashboard', ['status' => 'all']) }}" 
                       class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'all' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                        All Orders ({{ $statusCounts['all'] }})
                    </a>
                    <a href="{{ route('dashboard', ['status' => 'pending_payment']) }}" 
                       class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'pending_payment' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                        Pending Payment ({{ $statusCounts['pending_payment'] }})
                    </a>
                    <a href="{{ route('dashboard', ['status' => 'paid']) }}" 
                       class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'paid' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                        Paid ({{ $statusCounts['paid'] }})
                    </a>
                    <a href="{{ route('dashboard', ['status' => 'processing']) }}" 
                       class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'processing' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                        Processing ({{ $statusCounts['processing'] }})
                    </a>
                    <a href="{{ route('dashboard', ['status' => 'shipped']) }}" 
                       class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'shipped' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                        Shipped ({{ $statusCounts['shipped'] }})
                    </a>
                    <a href="{{ route('dashboard', ['status' => 'completed']) }}" 
                       class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ in_array($currentStatus, ['delivered', 'completed']) ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                        Completed ({{ $statusCounts['completed'] }})
                    </a>
                </div>
            </div>

            <!-- Orders List -->
            @if($transactions->count() > 0)
                <div class="space-y-4">
                    @foreach($transactions as $transaction)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <!-- Order Header -->
                            <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-b">
                                <div class="flex items-center gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Order ID</p>
                                        <p class="font-semibold">#{{ $transaction->id }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Date</p>
                                        <p class="font-semibold">{{ $transaction->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <span class="px-4 py-2 rounded-full text-sm font-semibold bg-[#e8bec4] text-black">
                                    {{ $transaction->getStatusLabel() }}
                                </span>
                            </div>

                            <!-- Order Items -->
                            <div class="px-6 py-4">
                                @foreach($transaction->transaction_items as $item)
                                    <div class="flex gap-4 mb-4 last:mb-0">
                                        <img src="{{ asset('storage/' . $item->product->image_url) }}" 
                                             alt="{{ $item->product->name }}"
                                             class="w-20 h-20 object-cover rounded-lg">
                                        <div class="flex-1">
                                            <h4 class="font-semibold mb-1">{{ $item->product->name }}</h4>
                                            <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                                            <p class="text-[#3F3142] font-bold mt-1">
                                                Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Order Footer -->
                            <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-t">
                                <div>
                                    <p class="text-sm text-gray-600">Total Payment</p>
                                    <p class="text-2xl font-bold text-[#3F3142]">
                                        Rp{{ number_format($transaction->total_price + $transaction->delivery_price, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('user.transaction.show', $transaction) }}" 
                                       class="px-6 py-2 border-2 border-[#3F3142] text-[#3F3142] rounded-lg font-semibold hover:bg-[#3F3142] hover:text-white transition-colors">
                                        View Detail
                                    </a>
                                    @if($transaction->isPendingPayment())
                                        <button class="px-6 py-2 bg-[#3F3142] text-white rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors">
                                            Pay Now
                                        </button>
                                    @elseif($transaction->isShipped())
                                        <button class="px-6 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors">
                                            Track Order
                                        </button>
                                    @elseif($transaction->isDelivered())
                                        <button class="px-6 py-2 bg-[#3F3142] text-white rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors">
                                            Order Received
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $transactions->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <svg class="w-32 h-32 mx-auto mb-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <h2 class="text-3xl font-bold text-gray-700 mb-4">No Orders Yet</h2>
                    <p class="text-gray-500 mb-6">Start shopping and your orders will appear here!</p>
                    <a href="/shop" 
                       class="inline-block px-8 py-4 bg-[#3F3142] text-white rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors">
                        Start Shopping
                    </a>
                </div>
            @endif

            <!-- Shipping Address -->
            <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
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
                                    <p class="text-xs text-gray-500 mt-2">📍 {{ number_format($address->latitude, 4) }}, {{ number_format($address->longitude, 4) }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No address found. Please add an address first.</p>
                    <a href="{{ route('user.addresses.create') }}" 
                       class="inline-block mt-4 px-6 py-2 bg-[#3F3142] text-white rounded-lg hover:bg-[#5C4B5E]">
                        Add Address
                    </a>
                @endif
            </div>

            <!-- Delivery Method -->
            <div class="bg-white rounded-lg shadow-lg p-6 mt-4">
                <h2 class="text-2xl font-bold mb-4">Delivery Method</h2>
                
                <div id="shipping-options-container">
                    <p class="text-gray-500 text-center py-4">
                        <svg class="animate-spin h-5 w-5 mx-auto mb-2 text-[#3F3142]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading shipping options...
                    </p>
                </div>
                
                <input type="hidden" name="shipping_cost" id="shipping_cost" value="0">
                <input type="hidden" name="courier_service" id="courier_service">
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-24 mt-4">
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

                <div id="payment-status" class="mt-4"></div>
            </div>
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
                                   name="shipping_option" 
                                   value="${option.cost}"
                                   data-service="${option.display_name}"
                                   ${index === 0 ? 'checked' : ''}
                                   onchange="updateShippingCost(${option.cost}, '${option.display_name}')"
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
                    updateShippingCost(data.shipping_options[0].cost, data.shipping_options[0].display_name);
                }
            } else {
                container.innerHTML = '<p class="text-red-500 text-center py-4">No shipping options available</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = '<p class="text-red-500 text-center py-4">Failed to load shipping options</p>';
        });
    }

    function updateShippingCost(cost, service) {
        selectedShippingCost = cost;
        
        document.getElementById('shipping_cost').value = cost;
        document.getElementById('courier_service').value = service;
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
    
    </script>
@endsection
