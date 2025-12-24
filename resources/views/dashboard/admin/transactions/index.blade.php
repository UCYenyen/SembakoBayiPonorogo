@extends('layouts.app')
@section('title', 'Transaction Management')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[90%] lg:w-[80%] mx-auto">
            <h1 class="text-4xl font-bold mb-8">Transaction Management</h1>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Status Tabs -->
            <div class="bg-white rounded-lg shadow-md mb-6 overflow-x-auto">
                <div class="flex border-b min-w-max">
                    <a href="{{ route('admin.transactions.index', ['status' => 'all']) }}" 
                       class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'all' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                        All ({{ $statusCounts['all'] }})
                    </a>
                    <a href="{{ route('admin.transactions.index', ['status' => 'pending_payment']) }}" 
                       class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'pending_payment' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                        Pending Payment ({{ $statusCounts['pending_payment'] }})
                    </a>
                    <a href="{{ route('admin.transactions.index', ['status' => 'paid']) }}" 
                       class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'paid' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                        Paid ({{ $statusCounts['paid'] }})
                    </a>
                    <a href="{{ route('admin.transactions.index', ['status' => 'processing']) }}" 
                       class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'processing' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                        Processing ({{ $statusCounts['processing'] }})
                    </a>
                    <a href="{{ route('admin.transactions.index', ['status' => 'shipped']) }}" 
                       class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'shipped' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                        Shipped ({{ $statusCounts['shipped'] }})
                    </a>
                    <a href="{{ route('admin.transactions.index', ['status' => 'delivered']) }}" 
                       class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'delivered' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                        Delivered ({{ $statusCounts['delivered'] }})
                    </a>
                    <a href="{{ route('admin.transactions.index', ['status' => 'completed']) }}" 
                       class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'completed' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                        Completed ({{ $statusCounts['completed'] }})
                    </a>
                </div>
            </div>

            <!-- Transactions List -->
            @if($transactions->count() > 0)
                <div class="space-y-4">
                    @foreach($transactions as $transaction)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <!-- Transaction Header -->
                            <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-b">
                                <div class="flex items-center gap-6">
                                    <div>
                                        <p class="text-sm text-gray-600">Order ID</p>
                                        <p class="font-semibold">#{{ $transaction->id }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Customer</p>
                                        <p class="font-semibold">{{ $transaction->user->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Date</p>
                                        <p class="font-semibold">{{ $transaction->created_at->format('d M Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Total</p>
                                        <p class="font-semibold text-[#3F3142]">
                                            Rp{{ number_format($transaction->total_price + $transaction->delivery_price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                                <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $transaction->getStatusBadgeClass() }}">
                                    {{ $transaction->getStatusLabel() }}
                                </span>
                            </div>

                            <!-- Transaction Items -->
                            <div class="px-6 py-4">
                                @foreach($transaction->transaction_items->take(3) as $item)
                                    <div class="flex gap-4 mb-4 last:mb-0">
                                        <img src="{{ asset('storage/' . $item->product->image_url) }}" 
                                             alt="{{ $item->product->name }}"
                                             class="w-16 h-16 object-cover rounded-lg">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-sm">{{ $item->product->name }}</h4>
                                            <p class="text-xs text-gray-600">Qty: {{ $item->quantity }}</p>
                                        </div>
                                    </div>
                                @endforeach
                                @if($transaction->transaction_items->count() > 3)
                                    <p class="text-sm text-gray-500 mt-2">+{{ $transaction->transaction_items->count() - 3 }} more items</p>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-2 border-t">
                                <a href="{{ route('admin.transactions.show', $transaction) }}" 
                                   class="px-6 py-2 border-2 border-[#3F3142] text-[#3F3142] rounded-lg font-semibold hover:bg-[#3F3142] hover:text-white transition-colors">
                                    View Detail
                                </a>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h2 class="text-3xl font-bold text-gray-700 mb-4">No Transactions Yet</h2>
                    <p class="text-gray-500">Transactions will appear here when customers place orders.</p>
                </div>
            @endif
        </div>
    </main>
@endsection
