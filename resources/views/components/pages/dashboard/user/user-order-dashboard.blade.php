<div class="bg-white rounded-lg">
    <!-- Status Tabs -->
    <div class="bg-white rounded-lg overflow-x-auto">
        <div class="flex border-b min-w-max">
            <a href="{{ route('dashboard', ['status' => 'all']) }}"
                class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'all' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                Semua Pesanan ({{ $statusCounts['all'] }})
            </a>
            <a href="{{ route('dashboard', ['status' => 'pending_payment']) }}"
                class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'pending_payment' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                Menunggu Pembayaran ({{ $statusCounts['pending_payment'] }})
            </a>
            <a href="{{ route('dashboard', ['status' => 'paid']) }}"
                class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'paid' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                Dibayar ({{ $statusCounts['paid'] }})
            </a>
            <a href="{{ route('dashboard', ['status' => 'processing']) }}"
                class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'processing' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                Diproses ({{ $statusCounts['processing'] }})
            </a>
            <a href="{{ route('dashboard', ['status' => 'shipped']) }}"
                class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'shipped' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                Dikirim ({{ $statusCounts['shipped'] }})
            </a>
            <a href="{{ route('dashboard', ['status' => 'delivered']) }}"
                class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ $currentStatus === 'delivered' ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                Diterima ({{ $statusCounts['delivered'] }})
            </a>
            <a href="{{ route('dashboard', ['status' => 'completed']) }}"
                class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ in_array($currentStatus, ['delivered', 'completed']) ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                Selesai ({{ $statusCounts['completed'] }})
            </a>
        </div>
    </div>

    <!-- Orders List -->
    @if ($transactions->count() > 0)
        <div class="space-y-4">
            @foreach ($transactions as $transaction)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gray-50 border-b overflow-x-auto no-scrollbar touch-pan-x">
                        <div class="px-6 py-4 flex items-center justify-start gap-12 w-max min-w-full">
                            <div class="flex-shrink-0">
                                <p class="text-sm text-gray-600">Nomor Transaksi</p>
                                <p class="font-semibold">#{{ $transaction->id }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <p class="text-sm text-gray-600">Tanggal</p>
                                <p class="font-semibold">{{ $transaction->created_at->format('d M Y') }}</p>
                            </div>

                            @if ($transaction->no_resi)
                                <div class="flex-shrink-0">
                                    <p class="text-sm text-gray-600">No. Resi</p>
                                    <div class="flex items-center gap-2">
                                        <p class="font-bold text-[#3F3142] font-mono">
                                            {{ $transaction->no_resi }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <div class="ml-auto flex-shrink-0 pr-6">
                                <span class="px-4 py-2 rounded-full text-sm font-semibold bg-[#dbdeff] text-black">
                                    {{ $transaction->getStatusLabel() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4">
                        @foreach ($transaction->transaction_items as $item)
                            <div class="flex gap-4 mb-4 last:mb-0">
                                <img src="{{ asset('storage/' . $item->product->image_url) }}"
                                    alt="{{ $item->product->name }}"
                                    class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                                <div class="flex-1">
                                    <h4 class="font-semibold mb-1">{{ $item->product->name }}</h4>
                                    <p class="text-sm text-gray-600">Jumlah: {{ $item->quantity }}</p>
                                    <p class="text-[#3F3142] font-bold mt-1">
                                        Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="bg-gray-50 border-t overflow-x-auto no-scrollbar touch-pan-x">
                        <div class="px-6 py-4 flex items-center justify-start gap-10 w-max min-w-full">
                            <div class="flex-shrink-0">
                                <p class="text-sm text-gray-600">Total Pembayaran</p>
                                <p class="text-2xl font-bold text-[#3F3142]">
                                    Rp{{ number_format($transaction->total_price + $transaction->delivery_price, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="ml-auto flex gap-2 flex-shrink-0 pr-6">
                                <a href="{{ route('user.transaction.show', $transaction) }}"
                                    class="px-6 py-2 border-2 border-[#3F3142] text-[#3F3142] rounded-lg font-semibold hover:bg-[#3F3142] hover:text-white transition-colors whitespace-nowrap">
                                    Lihat Detail
                                </a>
                                @if ($transaction->isPendingPayment())
                                    <button
                                        class="pay-btn px-6 py-2 bg-[#3F3142] text-white rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors whitespace-nowrap"
                                        data-transaction="{{ $transaction->id }}">
                                        Bayar Sekarang
                                    </button>
                                @elseif($transaction->isShipped())
                                    <button onclick="openTrackingModal('{{ $transaction->id }}')"
                                        class="px-6 py-2 bg-[#3F3142] text-white rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors whitespace-nowrap">
                                        Lacak Pesanan
                                    </button>
                                @elseif($transaction->isDelivered())
                                    <button
                                        class="px-6 py-2 bg-[#3F3142] text-white rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors whitespace-nowrap">
                                        Selesai
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $transactions->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="w-32 h-32 mx-auto mb-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <h2 class="text-3xl font-bold text-gray-700 mb-4">Belum ada pesanan</h2>
            <p class="text-gray-500 mb-6">Mulailah berbelanja dan pesanan Anda akan muncul di sini!</p>
            <a href="/shop"
                class="inline-block px-8 py-4 bg-[#3F3142] text-white rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors">
                Mulai Berbelanja
            </a>
        </div>
    @endif
</div>

<script>
    document.querySelectorAll('.pay-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const transactionId = this.dataset.transaction;
            const originalText = this.textContent;
            this.disabled = true;
            this.textContent = 'Loading...';

            fetch(`/payment/retry/${transactionId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            ?.content || '',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.snap_token) {
                        window.snap.pay(data.snap_token, {
                            onSuccess: (result) => window.location.href =
                                `/payment/finish/${transactionId}`,
                            onPending: (result) => window.location.href =
                                `/payment/unfinish/${transactionId}`,
                            onError: () => {
                                alert('Pembayaran gagal');
                                this.disabled = false;
                                this.textContent = originalText;
                            },
                            onClose: () => {
                                this.disabled = false;
                                this.textContent = originalText;
                            }
                        });
                    } else {
                        alert(data.error || 'Terjadi kesalahan');
                        this.disabled = false;
                        this.textContent = originalText;
                    }
                })
                .catch(() => {
                    alert('Kesalahan sistem');
                    this.disabled = false;
                    this.textContent = originalText;
                });
        });
    });
</script>
