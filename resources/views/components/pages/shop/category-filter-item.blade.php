<div>
    <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-{{ $level > 0 ? '1' : '2' }} rounded">
        <input type="checkbox" 
               name="categories[]" 
               value="{{ $category->id }}"
               {{ in_array($category->id, request('categories', [])) ? 'checked' : '' }}
               onchange="document.getElementById('filterForm').submit()"
               class="w-{{ $level > 0 ? '3' : '4' }} h-{{ $level > 0 ? '3' : '4' }} text-[#3F3142] border-gray-300 rounded focus:ring-[#3F3142]">
        <span class="text-{{ $level > 0 ? 'xs' : 'sm' }} {{ $level > 1 ? 'text-gray-500' : ($level > 0 ? 'text-gray-600' : '') }}">
            {{ $category->name }}
        </span>
    </label>

    @if ($category->children->count() > 0)
        <div class="ml-6 mt-1 space-y-1">
            @foreach ($category->children as $child)
                <x-pages.shop.category-filter-item :category="$child" :level="$level + 1" />
            @endforeach
        </div>
    @endif
</div>