@extends('layouts.app')
@section('title', 'My Vouchers')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[90%] lg:w-[80%] mx-auto">
            <!-- Header -->
            <div class="mb-12">
                <h1 class="text-4xl font-bold mb-2">Voucher Saya</h1>
                <p class="text-gray-600">Voucher yang belum digunakan</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm mb-1">Total Voucher Aktif</p>
                            <p class="text-4xl font-bold text-[#3F3142]">{{ $activeVouchers->count() }}</p>
                        </div>
                        <div class="w-16 h-16 bg-[#dbdeff] rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-[#3F3142]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm mb-1">Poin Saya</p>
                            <p class="text-4xl font-bold text-[#3F3142]">{{ number_format(auth()->user()->points) }}</p>
                        </div>
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm mb-1">Total Diskon Tersedia</p>
                            <p class="text-4xl font-bold text-[#3F3142]">
                                Rp{{ number_format($totalDiscount, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Button Beli Voucher -->
            <div class="mb-8">
                <a href="{{ route('user.vouchers.create') }}"
                    class="inline-block px-8 py-3 bg-[#3F3142] text-white rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors">
                    + Beli Voucher Baru
                </a>
            </div>

            <!-- Vouchers Grid -->
            @if($activeVouchers->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($activeVouchers as $voucher)
                        <x-pages.user-voucher :voucher="$voucher->base_voucher" :userVoucherId="$voucher->id" />
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($activeVouchers->hasPages())
                    <div class="mt-8 flex justify-center">
                        {{ $activeVouchers->links('vendor.pagination.simple') }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                    <svg class="w-32 h-32 mx-auto mb-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="text-3xl font-bold text-gray-700 mb-2">Belum Ada Voucher Aktif</h2>
                    <p class="text-gray-500 mb-6">Tukarkan poin Anda dengan voucher eksklusif!</p>
                    <a href="{{ route('user.vouchers.create') }}" class="inline-block px-8 py-3 bg-[#3F3142] text-white rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors">
                        Lihat Voucher Tersedia
                    </a>
                </div>
            @endif
        </div>
    </main>

    <script>
        function copyVoucherCode(voucherId) {
            const code = `VOUCHER-${voucherId}`;
            navigator.clipboard.writeText(code).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Kode Disalin!',
                    text: `Kode ${code} telah disalin ke clipboard`,
                    confirmButtonColor: '#3F3142',
                    timer: 2000
                });
            });
        }
    </script>
@endsection
