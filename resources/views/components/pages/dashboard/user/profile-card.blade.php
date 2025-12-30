<div
    class="bg-gradient-to-br from-gray-50 to-white rounded-2xl shadow-md overflow-hidden relative">
    <div class="absolute top-0 right-0 w-40 h-40 bg-[#dbdeff]/20 rounded-full -mr-16 -mt-16"></div>

    <div class="px-8 py-8 relative">
        <div class="flex justify-between items-start mb-8">
            <span
                class="bg-[#dbdeff] text-[#3F3142] text-[10px] uppercase font-bold px-3 py-1 rounded-md shadow-sm">
                {{ auth()->user()->role }}
            </span>
            <div class="text-right inline-block md:hidden">
                <p class="text-gray-400 text-[10px] uppercase tracking-[0.2em] mb-1">Total Point</p>
                <p class="text-2xl font-black text-[#3F3142] leading-none">
                    {{ number_format(auth()->user()->points) }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-6">
            <div class="flex flex-col">
                <h2 class="text-xl font-bold text-[#3F3142] leading-tight uppercase tracking-wide">
                    {{ auth()->user()->name }}
                </h2>
                <p class="text-gray-400 text-sm font-mono tracking-tighter">
                    ID: #{{ str_pad(auth()->user()->id, 20, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>

        <div class="mt-10 pt-6 border-t border-black flex justify-between items-end">
            <div class="space-y-1 text-[#3F3142]">
                <p class=" text-[10px] uppercase tracking-[0.2em]">Kontak</p>
                <p class=" text-xs">{{ auth()->user()->email }}</p>
                <p class=" text-xs">+{{ auth()->user()->phone_number }}</p>
            </div>

            <div class="hidden text-right md:inline-block text-[#3F3142]">
                <p class="text-[10px] uppercase tracking-[0.2em] mb-1">Total Point</p>
                <p class="text-2xl font-black text-[#3F3142] leading-none">
                    {{ number_format(auth()->user()->points) }}
                </p>
            </div>
        </div>
    </div>
</div>
