<div
    class="bg-gradient-to-br from-[#3F3142] to-[#1a141b] rounded-2xl shadow-2xl overflow-hidden relative border border-gray-700">
    <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16"></div>

    <div class="px-8 py-8 relative">
        <div class="flex justify-between items-start mb-8">
            <span
                class="bg-amber-400 text-[#3F3142] text-[10px] uppercase font-bold px-3 py-1 rounded-md shadow-sm">
                {{ auth()->user()->role }}
            </span>
            <div class="text-right inline-block md:hidden">
                <p class="text-gray-400 text-[10px] uppercase tracking-[0.2em] mb-1">Total Points</p>
                <p class="text-2xl font-black text-amber-400 leading-none">
                    {{ number_format(auth()->user()->points) }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-6">
            <div class="flex flex-col">
                <h2 class="text-xl font-bold text-white leading-tight uppercase tracking-wide">
                    {{ auth()->user()->name }}
                </h2>
                <p class="text-gray-400 text-sm font-mono tracking-tighter">
                    ID: #{{ str_pad(auth()->user()->id, 20, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>

        <div class="mt-10 pt-6 border-t border-white/10 flex justify-between items-end">
            <div class="space-y-1">
                <p class="text-gray-400 text-[10px] uppercase tracking-[0.2em]">Contact Information</p>
                <p class="text-gray-200 text-xs">{{ auth()->user()->email }}</p>
                <p class="text-gray-200 text-xs">+{{ auth()->user()->phone_number }}</p>
            </div>

            <div class="hidden text-right md:inline-block">
                <p class="text-gray-400 text-[10px] uppercase tracking-[0.2em] mb-1">Total Points</p>
                <p class="text-2xl font-black text-amber-400 leading-none">
                    {{ number_format(auth()->user()->points) }}
                </p>
            </div>
        </div>
    </div>
</div>
