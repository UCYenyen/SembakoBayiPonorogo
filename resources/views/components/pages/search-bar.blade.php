<form action="{{ url($route) }}" method="GET" class="flex gap-2 w-full">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{$placeholder}}"
        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent text-sm md:text-base">
    <button type="submit"
        class="px-4 md:px-6 py-2 bg-[#3F3142] hover:bg-[#5C4B5E] text-white rounded-lg transition-colors duration-200 text-sm md:text-base whitespace-nowrap">
        <x-heroicon-o-magnifying-glass class="w-5 h-5 inline-block mr-2" />
        Search
    </button>
    @if (request('search'))
        <a href="{{ url($route) }}"
            class="px-4 md:px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-200 text-sm md:text-base whitespace-nowrap">
            Clear
            <x-heroicon-o-x-mark class="w-5 h-5" />
        </a>
    @endif
</form>
