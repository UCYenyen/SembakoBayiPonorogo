@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] py-8">
        <div class="w-[80%] mx-auto flex flex-col gap-8">
            <x-pages.dashboard.user.profile-card />
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="flex border-b overflow-x-auto">
                    <button onclick="switchTab('transactions')" id="tab-transactions"
                        class="tab-button px-6 py-4 font-semibold border-b-2 whitespace-nowrap border-[#3F3142] text-[#3F3142]">
                        Transaksi Saya
                    </button>
                    <button onclick="switchTab('vouchers')" id="tab-vouchers"
                        class="tab-button px-6 py-4 font-semibold border-b-2 whitespace-nowrap border-transparent text-gray-500 hover:text-[#3F3142]">
                        Voucher Saya
                    </button>
                </div>

                <div id="content-transactions" class="tab-content">
                    <x-user-order-dashboard :transactions="$transactions" :statusCounts="$statusCounts" :currentStatus="request('status', 'all')" />
                </div>
                <div id="content-vouchers" class="tab-content hidden">
                    <div class="p-6">
                        @if ($voucherData['vouchers']->count() > 0)
                            <div class="mb-6 w-full flex justify-center md:justify-end">
                                <a href="{{ route('user.vouchers.create') }}"
                                    class="inline-block px-8 py-3 bg-[#3F3142] w-full md:w-fit text-center text-white rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors">
                                    + Beli Voucher Baru
                                </a>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach ($voucherData['vouchers'] as $voucher)
                                    <x-pages.user-voucher :voucher="$voucher->base_voucher" :userVoucherId="$voucher->id" />
                                @endforeach
                            </div>

                            @if ($voucherData['vouchers']->hasPages())
                                <div class="mt-8 flex justify-center">
                                    {{ $voucherData['vouchers']->appends(['tab' => 'vouchers'])->links('vendor.pagination.simple') }}
                                </div>
                            @endif
                        @else
                            <div class="bg-white rounded-lg border-gray-300 p-12 text-center">
                                <svg class="w-32 h-32 mx-auto mb-6 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                                <h2 class="text-3xl font-bold text-gray-700 mb-2">Belum Ada Voucher Aktif</h2>
                                <p class="text-gray-500 mb-6">Tukarkan poin Anda dengan voucher eksklusif!</p>
                                <a href="{{ route('user.vouchers.create') }}"
                                    class="inline-block px-8 py-3 bg-[#3F3142] text-white rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors">
                                    Lihat Voucher Tersedia
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
        }

        .tab-button.active {
            border-bottom-color: #3F3142;
            color: #3F3142;
        }
    </style>

    <script>
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-[#3F3142]', 'text-[#3F3142]');
                button.classList.add('border-transparent', 'text-gray-500');
            });

            document.getElementById('content-' + tabName).classList.remove('hidden');

            const activeButton = document.getElementById('tab-' + tabName);
            activeButton.classList.remove('border-transparent', 'text-gray-500');
            activeButton.classList.add('border-[#3F3142]', 'text-[#3F3142]');

            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.pushState({}, '', url);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');

            if (tab === 'vouchers') {
                switchTab('vouchers');
            }
        });
    </script>
@endsection
