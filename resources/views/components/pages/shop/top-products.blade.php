<x-pages.section title="Top" extraClasses="">
    <div class="relative w-full flex flex-col gap-24 justify-center items-center">
        <div class="w-[80%] grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($products as $product)
                <x-pages.product-card productImage="{{ $product->image_url }}" productName="{{ $product->name }}"
                    rating="{{ $product->average_rating }}" price="{{ number_format($product->price, 0, ',', '.') }}" />
            @endforeach
        </div>
        <img src="/images/misc/pink-waves.svg" draggable="false" alt="background waves" class="h-auto z-1 left-0 w-full">
    </div>
</x-pages.section>
