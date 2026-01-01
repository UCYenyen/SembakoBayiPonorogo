@extends('layouts.app')
@section('title', 'Beli Voucher')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] min-h-screen py-8">
        <div class="w-[80%] mx-auto flex flex-col gap-8">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="text-[#3F3142] hover:text-[#5C4B5E]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-4xl font-bold">Tukar Voucher</h1>
            </div>

            <x-pages.dashboard.user.profile-card />

            @if ($baseVouchers->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($baseVouchers as $baseVoucher)
                        <div
                            class="bg-gradient-to-br from-gray-50 to-white rounded-xl shadow-lg p-6 text-[#3F3142] overflow-hidden relative group transition-all duration-300">
                            <div
                                class="absolute top-0 right-0 w-32 h-32 bg-[#dbdeff]/20 rounded-full -mr-16 -mt-16 group-hover:bg-[#dbdeff]/30 transition-all">
                            </div>
                            <div
                                class="absolute bottom-0 left-0 w-24 h-24 bg-[#dbdeff]/20 rounded-full -ml-12 -mb-12 group-hover:bg-[#dbdeff]/30 transition-all">
                            </div>

                            <div class="relative z-10 text-[#3F3142]">
                                <h3 class="text-2xl font-bold truncate mb-3">{{ $baseVoucher->name }}</h3>
                                <div class="h-px bg-[#3F3142] mb-6"></div>

                                <div class="mb-6">
                                    <p class="text-[#3F3142] text-xs uppercase tracking-wider mb-1">Nilai Diskon</p>
                                    <p class="text-4xl font-black">
                                        Rp{{ number_format($baseVoucher->disc_amt, 0, ',', '.') }}</p>
                                </div>

                                <div
                                    class="bg-[#dbdeff]/30 backdrop-blur-sm rounded-lg p-4 mb-6 border border-[#dbdeff]/20">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-[#3F3142]/80">Poin Diperlukan</span>
                                        <span class="font-bold">{{ number_format($baseVoucher->points_required) }}
                                            pts</span>
                                    </div>
                                </div>
                                @if (auth()->user()->role != 'guest')
                                    @if (auth()->user()->points >= $baseVoucher->points_required)
                                        <form action="{{ route('user.vouchers.store') }}" method="POST"
                                            class="relative z-2">
                                            @csrf
                                            <input type="hidden" name="base_voucher_id" value="{{ $baseVoucher->id }}">
                                            <button type="submit"
                                                class="w-full bg-[#3F3142] text-white font-bold py-2 rounded-lg hover:bg-[#5C4B5E] transition-colors duration-200 flex items-center justify-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4"></path>
                                                </svg>
                                                Tukar Poin {{ auth()->user()->name }}
                                            </button>
                                        </form>
                                    @else
                                        <div
                                            class="w-full bg-gray-400 text-white font-bold py-2 rounded-lg text-center cursor-not-allowed relative z-2">
                                            Poin Tidak Cukup
                                        </div>
                                    @endif
                                @else
                                    <div
                                        class="w-full bg-gray-400 text-white font-bold py-2 rounded-lg text-center cursor-not-allowed relative z-2">
                                       Jadi Member Untuk Menukar
                                    </div>
                                @endif

                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                    <svg class="w-32 h-32 mx-auto mb-6 text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                        </path>
                    </svg>
                    <h2 class="text-3xl font-bold text-gray-700 mb-2">Tidak Ada Voucher Tersedia</h2>
                    <p class="text-gray-500 mb-6">Mohon maaf, saat ini tidak ada voucher yang dapat ditukar.</p>
                    <a href="{{ route('user.vouchers.index') }}"
                        class="inline-block px-8 py-3 bg-[#3F3142] text-white rounded-lg font-semibold hover:bg-[#5C4B5E] transition-colors">
                        Kembali ke Voucher Saya
                    </a>
                </div>
            @endif
        </div>
    </main>
@endsection
