<form method="POST" class="flex flex-col items-center justify-center bg-[#F4F5FF] rounded-xl shadow-lg p-4 gap-5">
    <img src="{{ $productImage }}" alt="productImage">
    <div class="flex flex-col justify-center items-center gap-3">
        <h3 class="font-bold text-2xl">{{ $productName }}</h3>
        <div class="flex justify-center items-center">
            <img src="/images/misc/star.svg" alt="star">
            <p>{{ $rating }}</p>
        </div>
        <div class="flex flex-col justify-center items-center">
            <p class="text-[#856C8A]">Rp{{ $price }}</p>
            <button type="submit" class="mt-4 bg-white text-[#3F3142] font-bold text-xl py-3 px-8 rounded-full border-2 border-[#C4B5FD] hover:bg-[#C4B5FD] hover:text-white transition-colors">
                Add to cart
            </button>
        </div>
    </div>
</form>