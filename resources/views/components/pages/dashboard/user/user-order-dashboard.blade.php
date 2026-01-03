<div class="bg-white rounded-lg flex flex-col gap-4">
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
                class="px-6 py-4 font-semibold border-b-2 whitespace-nowrap {{ in_array($currentStatus, ['completed']) ? 'border-[#3F3142] text-[#3F3142]' : 'border-transparent text-gray-500 hover:text-[#3F3142]' }}">
                Selesai ({{ $statusCounts['completed'] }})
            </a>
        </div>
    </div>

    @if ($transactions->count() > 0)
        <div class="space-y-4">
            @foreach ($transactions as $transaction)
                <div class="bg-white shadow-md overflow-hidden">
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

                            <div class="ml-auto flex-shrink-0">
                                <span class="px-4 py-2 rounded-full text-sm font-semibold bg-[#dbdeff] text-black">
                                    {{ $transaction->getStatusLabel() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach ($transaction->transaction_items as $item)
                                <div class="flex flex-col bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
                                    <div class="relative w-full aspect-square mb-3">
                                        <img src="{{ $item->product->image_path }}" alt="{{ $item->product->name }}"
                                            class="w-full h-full object-cover rounded-lg">

                                        @if ($transaction->isCompleted() && $item->testimony)
                                            <div
                                                class="absolute top-2 left-2 px-2 py-1 bg-white/90 backdrop-blur-sm shadow-md flex items-center gap-1 rounded-lg">
                                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966c.3.922-.755 1.688-1.54 1.118l-3.38-2.454a1 1 0 00-1.175 0l-3.38 2.454c-.784.57-1.838-.196-1.54-1.118l1.287-3.966a1 1 0 00-.364-1.118L2.05 9.394c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69l1.286-3.967z" />
                                                </svg>
                                                <span
                                                    class="font-bold text-xs text-yellow-600">{{ $item->testimony->rating_star }}/5</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex flex-col flex-1">
                                        <h4 class="font-semibold text-gray-800 line-clamp-2 min-h-[2.5rem] mb-1">
                                            {{ $item->product->name }}
                                        </h4>
                                        <p class="text-sm text-gray-500">Jumlah: {{ $item->quantity }}</p>
                                        <p class="text-[#3F3142] font-extrabold mt-1">
                                            Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                        </p>
                                    </div>

                                    @if ($transaction->isCompleted())
                                        <div class="mt-4 pt-3 border-t border-gray-50 flex flex-wrap gap-2">
                                            @if ($item->testimony)
                                                <a href="{{ route('user.testimonies.show', $item->testimony) }}"
                                                    class="flex-1 text-center px-3 py-2 bg-transparent border-[#3F3142] border-2 text-[#3F3142] hover:text-white rounded-lg font-semibold hover:bg-[#3F3142] transition-colors text-xs whitespace-nowrap">
                                                    Lihat Ulasan
                                                </a>
                                                <a href="{{ route('user.testimonies.edit', $item->testimony) }}"
                                                    class="flex-1 text-center px-3 py-2 bg-[#3F3142] text-white rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors text-xs whitespace-nowrap">
                                                    Edit
                                                </a>
                                            @else
                                                <a href="{{ route('user.testimonies.create', $item) }}"
                                                    class="w-full text-center px-4 py-2 bg-[#3F3142] text-white rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors text-sm">
                                                    Ulas Produk
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-gray-50 border-t overflow-x-auto no-scrollbar touch-pan-x">
                        <div class="px-6 py-4 flex items-center justify-start gap-10 w-max min-w-full">
                            <div class="flex-shrink-0">
                                <p class="text-sm text-gray-600">Total Pembayaran</p>
                                <p class="text-2xl font-bold text-[#3F3142]">
                                    Rp{{ number_format($transaction->total_price + $transaction->delivery_price, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="ml-auto flex gap-2 flex-shrink-0">
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
                                    <form action="{{ route('user.transaction.complete', $transaction) }}"
                                        method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="px-6 py-2 bg-[#3F3142] text-white rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors whitespace-nowrap">
                                            Selesai
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
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
