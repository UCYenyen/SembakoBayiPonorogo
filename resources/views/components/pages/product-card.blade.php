<form method="POST" class="flex flex-col items-center justify-center bg-[#F4F5FF] rounded-xl shadow-lg p-3 sm:p-4 md:p-6 gap-3 sm:gap-4 md:gap-5 w-full max-w-sm mx-auto">
    <img src="{{ asset($productImage) }}" alt="productImage" class="w-full h-auto max-w-[200px] sm:max-w-[250px] md:max-w-full object-contain">
    <div class="flex flex-col justify-center items-center gap-2 sm:gap-3 w-full">
        <h3 class="font-bold text-lg sm:text-xl md:text-2xl text-center">{{ $productName }}</h3>
        <div class="flex justify-center items-center gap-1">
            <img src="/images/misc/star.svg" alt="star" class="w-4 h-4 sm:w-5 sm:h-5">
            <p class="text-sm sm:text-base">{{ $rating }}</p>
        </div>
        <div class="flex flex-col justify-center items-center gap-2 sm:gap-3 w-full">
            <p class="text-[#856C8A] text-base sm:text-lg md:text-xl font-semibold">Rp{{ $price }}</p>
            <button type="submit" class="mt-2 sm:mt-3 md:mt-4 bg-white text-[#3F3142] font-bold text-base sm:text-lg md:text-xl py-2 sm:py-2.5 md:py-3 px-6 sm:px-7 md:px-8 rounded-full border-2 border-[#C4B5FD] hover:bg-[#C4B5FD] hover:text-white transition-colors w-full sm:w-auto">
                Add to cart
            </button>
        </div>
    </div>
</form>