@extends('layouts.app')
@section('title', 'Dashboard')
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
                                <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $transaction->getStatusBadgeClass() }}">
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
        </div>
    </main>
@endsection
