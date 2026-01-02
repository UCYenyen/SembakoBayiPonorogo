<a href="{{ route('product.show', $product ?? 0) }}"
    class="flex flex-col items-center justify-center bg-[#F4F5FF] rounded-xl shadow-lg p-3 sm:p-4 md:p-6 gap-3 sm:gap-4 md:gap-5 w-full hover:shadow-xl transition-shadow">
    <div class="w-full aspect-square overflow-hidden flex items-center justify-center bg-white rounded-lg">
        <img src="{{ $product->image_path }}" alt="{{ $productName }}" loading="lazy" class="w-full h-full object-cover">
    </div>
    <h3 class="font-bold text-lg sm:text-xl md:text-2xl text-center line-clamp-2">{{ $productName }}</h3>
    <div class="flex justify-center items-center gap-1">
        <img src="/images/misc/star.svg" alt="star" class="w-4 h-4 sm:w-5 sm:h-5" loading="lazy">
        <p class="text-sm sm:text-base">{{ $rating }}</p>
    </div>
    <div class="flex flex-col justify-center items-center gap-2 sm:gap-3 w-full">
        <p class="text-[#856C8A] text-base sm:text-lg md:text-xl font-semibold">Rp{{ $price }}</p>
    </div>
</a>
