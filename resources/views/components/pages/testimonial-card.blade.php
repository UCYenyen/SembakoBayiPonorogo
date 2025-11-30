<div class="flex flex-col gap-4 bg-white shadow-lg w-full h-full rounded-lg p-4 text-[#3F3142]">
    <h3 class="text-2xl w-full text-start font-bold">{{ $userName }}</h3>
    <div class="flex gap-2 justify-start items-center">
        <img src="/images/misc/star.svg" alt="sembako bayi ponorogo rating star" class="w-5 h-5">
        <p class="text-lg text-justify">{{ $rating }}</p>
    </div>
    <p class="text-lg text-justify">{{ $testimonialText }}</p>
</div>
