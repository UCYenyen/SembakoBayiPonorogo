<form action="{{ url($route) }}" method="GET" class="md:flex gap-2 w-full {{ $extraClass ?? '' }}">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $placeholder }}"
        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent text-sm md:text-base">
    <button type="submit"
        class="mt-2 md:mt-0 w-full md:w-auto px-4 md:px-6 py-2 bg-[#3F3142] hover:bg-[#5C4B5E] text-white rounded-lg transition-colors duration-200 text-sm md:text-base whitespace-nowrap">
        <x-heroicon-o-magnifying-glass class="w-5 h-5 inline-block mr-2" />
        Search
    </button>
</form>
