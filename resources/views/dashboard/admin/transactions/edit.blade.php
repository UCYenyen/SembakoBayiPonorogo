@extends('layouts.app')
@section('title', 'Transaction Detail')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[90%] lg:w-[80%] mx-auto">
            <!-- Back Button -->
            <a href="{{ route('admin.transactions.index') }}" 
               class="inline-flex items-center gap-2 mb-6 text-[#3F3142] hover:underline">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Transactions
            </a>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <h1 class="text-4xl font-bold mb-8">Transaction Detail #{{ $transaction->id }}</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Order Items & Customer Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Customer Info -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-4">Customer Information</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Name</span>
                                <span class="font-semibold">{{ $transaction->user->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Email</span>
                                <span class="font-semibold">{{ $transaction->user->email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Phone</span>
                                <span class="font-semibold">{{ $transaction->user->phone_number ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-6">Order Items</h2>
                        <div class="space-y-4">
                            @foreach($transaction->transaction_items as $item)
                                <div class="flex gap-4 pb-4 border-b last:border-b-0">
                                    <img src="{{ asset('storage/' . $item->product->image_url) }}" 
                                         alt="{{ $item->product->name }}"
                                         class="w-24 h-24 object-cover rounded-lg">
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

                    <!-- Shipping Address -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-4">Shipping Address</h2>
                        <p class="text-gray-700">
                            {{ $transaction->address->detail }}<br>
                            {{ $transaction->address->city_name }}, {{ $transaction->address->province }}
                        </p>
                    </div>
                </div>

                <!-- Right Column: Status Management -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-lg p-6 sticky top-24">
                        <h2 class="text-2xl font-bold mb-6">Order Management</h2>

                        <!-- Current Status -->
                        <div class="mb-6 pb-6 border-b">
                            <h3 class="font-semibold mb-2">Current Status</h3>
                            <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold {{ $transaction->getStatusBadgeClass() }}">
                                {{ $transaction->getStatusLabel() }}
                            </span>
                        </div>

                        <!-- Update Status Form -->
                        <form action="{{ route('admin.transactions.update-status', $transaction) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="status" class="block text-sm font-medium mb-2">Update Status *</label>
                                <select name="status" id="status" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                                    <option value="pending_payment" {{ $transaction->status === 'pending_payment' ? 'selected' : '' }}>Pending Payment</option>
                                    <option value="paid" {{ $transaction->status === 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="processing" {{ $transaction->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ $transaction->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $transaction->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="completed" {{ $transaction->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $transaction->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="failed" {{ $transaction->status === 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                            </div>

                            <!-- Tracking Number (show when status is shipped) -->
                            <div id="resi-field" style="display: none;">
                                <label for="no_resi" class="block text-sm font-medium mb-2">Tracking Number</label>
                                <input type="text" name="no_resi" id="no_resi" 
                                       value="{{ old('no_resi', $transaction->no_resi) }}"
                                       placeholder="Enter tracking number"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                            </div>

                            <button type="submit" 
                                    class="w-full bg-[#3F3142] text-white py-3 rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors">
                                Update Status
                            </button>
                        </form>

                        <!-- Order Details -->
                        <div class="mt-6 pt-6 border-t space-y-3">
                            <h3 class="font-semibold mb-4">Order Details</h3>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Order Date</span>
                                <span class="font-semibold">{{ $transaction->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Payment Method</span>
                                <span class="font-semibold">{{ $transaction->payment->method }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Delivery</span>
                                <span class="font-semibold">{{ $transaction->delivery->name }}</span>
                            </div>
                            @if($transaction->no_resi)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tracking Number</span>
                                <span class="font-semibold font-mono text-sm">{{ $transaction->no_resi }}</span>
                            </div>
                            @endif
                        </div>

                        <!-- Price Details -->
                        <div class="mt-6 pt-6 border-t space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-semibold">Rp{{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping Cost</span>
                                <span class="font-semibold">Rp{{ number_format($transaction->delivery_price, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="mt-4 pt-4 border-t">
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold">Total</span>
                                <span class="text-2xl font-bold text-[#3F3142]">
                                    Rp{{ number_format($transaction->total_price + $transaction->delivery_price, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Show/hide tracking number field based on status
        const statusSelect = document.getElementById('status');
        const resiField = document.getElementById('resi-field');

        function toggleResiField() {
            if (statusSelect.value === 'shipped') {
                resiField.style.display = 'block';
            } else {
                resiField.style.display = 'none';
            }
        }

        statusSelect.addEventListener('change', toggleResiField);
        toggleResiField(); // Initial check
    </script>
@endsection
