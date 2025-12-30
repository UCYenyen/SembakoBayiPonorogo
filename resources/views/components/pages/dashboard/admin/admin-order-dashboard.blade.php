<div class="bg-white rounded-lg flex flex-col gap-4">
    <div class="bg-white rounded-lg overflow-x-auto">
        <div class="flex border-b min-w-max">
            <a href="{{ route('admin.transactions.index', ['status' => 'all']) }}"
                class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'all' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                Semua Pesanan ({{ $statusCounts['all'] }})
            </a>
            <a href="{{ route('admin.transactions.index', ['status' => 'pending_payment']) }}"
                class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'pending_payment' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                Menunggu Pembayaran ({{ $statusCounts['pending_payment'] }})
            </a>
            <a href="{{ route('admin.transactions.index', ['status' => 'paid']) }}"
                class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'paid' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                Dibayar ({{ $statusCounts['paid'] }})
            </a>
            <a href="{{ route('admin.transactions.index', ['status' => 'processing']) }}"
                class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'processing' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                Diproses ({{ $statusCounts['processing'] }})
            </a>
            <a href="{{ route('admin.transactions.index', ['status' => 'shipped']) }}"
                class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'shipped' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                Dikirim ({{ $statusCounts['shipped'] }})
            </a>
            <a href="{{ route('admin.transactions.index', ['status' => 'delivered']) }}"
                class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'delivered' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                Diterima ({{ $statusCounts['delivered'] }})
            </a>
            <a href="{{ route('admin.transactions.index', ['status' => 'completed']) }}"
                class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'completed' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                Selesai ({{ $statusCounts['completed'] }})
            </a>
        </div>
    </div>

    @if ($transactions->count() > 0)
        <div class="space-y-4">
            @foreach ($transactions as $transaction)
                <div class="bg-white shadow-md overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-b">
                        <div class="flex items-center gap-6">
                            <div>
                                <p class="text-sm text-gray-600">Order ID</p>
                                <p class="font-semibold">#{{ $transaction->id }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Pelanggan</p>
                                <p class="font-semibold">{{ $transaction->user->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Tanggal</p>
                                <p class="font-semibold">{{ $transaction->created_at->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total</p>
                                <p class="font-semibold text-[#3F3142]">
                                    Rp{{ number_format($transaction->total_price + $transaction->delivery_price, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <span class="px-4 py-2 rounded-full text-sm font-semibold bg-[#dbdeff] text-black">
                            {{ $transaction->getStatusLabel() }}
                        </span>
                    </div>

                    <div class="px-6">
                        @foreach ($transaction->transaction_items->take(3) as $item)
                            <div class="flex gap-4 mb-4 last:mb-0">
                                <img src="{{ asset('storage/' . $item->product->image_url) }}"
                                    alt="{{ $item->product->name }}" class="w-16 h-16 object-cover rounded-lg">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-sm">{{ $item->product->name }}</h4>
                                    <p class="text-xs text-gray-600">Qty: {{ $item->quantity }}</p>
                                </div>
                            </div>
                        @endforeach
                        @if ($transaction->transaction_items->count() > 3)
                            <p class="text-sm text-gray-500 mt-2">+{{ $transaction->transaction_items->count() - 3 }}
                                more
                                items</p>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-2 border-t">
                        <a href="{{ route('admin.transactions.detail', $transaction) }}"
                            class="px-6 py-2 border-2 border-[#3F3142] text-[#3F3142] rounded-lg font-semibold hover:bg-[#3F3142] hover:text-white transition-colors">
                            View Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        @if ($transactions->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $transactions->links('vendor.pagination.simple') }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="w-32 h-32 mx-auto mb-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            <h2 class="text-3xl font-bold text-gray-700 mb-4">No Transactions Yet</h2>
            <p class="text-gray-500">Transactions will appear here when customers place orders.</p>
        </div>
    @endif
</div>
