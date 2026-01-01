<div class="bg-white rounded-lg shadow-lg p-6">
    <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
        <svg class="w-6 h-6 text-[#3F3142]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Voucher Saya
    </h3>

    @if(auth()->user()->vouchers->count() > 0)
        <div class="space-y-3">
            @foreach(auth()->user()->vouchers as $voucher)
                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-[#3F3142] cursor-pointer transition-colors">
                    <input type="radio" name="voucher_id" value="{{ $voucher->id }}" class="w-4 h-4 text-[#3F3142]">
                    <div class="ml-4 flex-1">
                        <p class="font-semibold text-[#3F3142]">{{ $voucher->base_voucher->name }}</p>
                        <p class="text-sm text-gray-600">Diskon: Rp{{ number_format($voucher->base_voucher->disc_amt, 0, ',', '.') }}</p>
                    </div>
                </label>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 text-center py-4">Tidak ada voucher tersedia</p>
    @endif
</div>