<div
    class="bg-gradient-to-br from-gray-50 to-white rounded-xl shadow-xl p-6 text-[#3F3142] overflow-hidden relative group transition-all duration-300">
    <div
        class="absolute top-0 right-0 w-32 h-32 bg-[#dbdeff]/20 rounded-full -mr-16 -mt-16 group-hover:bg-[#dbdeff]/30 transition-all">
    </div>
    <div
        class="absolute bottom-0 left-0 w-24 h-24 bg-[#dbdeff]/20 rounded-full -ml-12 -mb-12 group-hover:bg-[#dbdeff]/30 transition-all">
    </div>

    <div class="relative z-10 text-[#3F3142]">
        <h3 class="text-2xl font-bold truncate mb-3">{{ $voucher->name }}</h3>
        <div class="h-px bg-[#3F3142] mb-6"></div>

        <div class="mb-6">
            <p class="text-[#3F3142] text-xs uppercase tracking-wider mb-1">Nilai Diskon</p>
            <p class="text-4xl font-black">
                Rp{{ number_format($voucher->disc_amt, 0, ',', '.') }}</p>
        </div>
    </div>
</div>
