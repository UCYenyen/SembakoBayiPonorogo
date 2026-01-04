@props(['item'])
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex gap-6">
        <!-- Product Image -->
        <div class="w-32 h-32 flex-shrink-0">
            <img src="{{ $item->product->image_path }}" alt="{{ $item->product->name }}"
                class="w-full h-full object-cover rounded-lg">
        </div>

        <!-- Product Info -->
        <div class="flex-1 min-w-0">
            <div class="flex justify-between items-start mb-2">
                <div class="flex-1">
                    <h3 class="text-xl font-bold mb-1">
                        <a href="{{ route('product.show', $item->product) }}" class="hover:text-[#5C4B5E]">
                            {{ $item->product->name }}
                        </a>
                    </h3>
                    <p class="text-sm text-gray-600">
                        {{ $item->product->category->name }} | {{ $item->product->brand->name }}
                    </p>
                </div>

                <!-- Remove Button -->
                <form action="{{ route('cart.remove', $item) }}" method="POST" class="ml-4">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Remove this item from cart?')"
                        class="text-red-500 hover:text-red-700">
                        <x-heroicon-o-trash class="w-5 h-5" />
                    </button>
                </form>
            </div>

            <!-- Price -->
            <div class="text-2xl font-bold text-[#3F3142] mb-4">
                @if ($item->product->is_on_sale)
                    <p class="text-[#856C8A]/30 text-base sm:text-lg md:text-xl font-semibold line-through">
                        Rp{{ number_format($item->product->price, 0, ',', '.') }}</p>
                    <p class="text-[#856C8A] text-base sm:text-lg md:text-xl font-bold">
                        Rp{{ number_format($item->product->price - $item->product->discount_amount, 0, ',', '.') }}</p>
                @else
                    Rp{{ number_format($item->product->price, 0, ',', '.') }}
                @endif
            </div>

            <!-- Quantity Controls -->
            <div class="flex items-center gap-4">
                <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    @method('PATCH')
                    <label class="text-sm font-semibold">Jumlah:</label>
                    <div class="flex items-center border border-gray-300 rounded-lg">
                        <button type="button" onclick="decrementQuantity(this)" class="px-3 py-2 hover:bg-gray-100">
                            -
                        </button>
                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                            max="{{ $item->product->stocks }}" class="w-16 text-center border-0 focus:ring-0"
                            onchange="this.form.submit()">
                        <button type="button" onclick="incrementQuantity(this)" class="px-3 py-2 hover:bg-gray-100">
                            +
                        </button>
                    </div>
                </form>

                <!-- Stock Info -->
                <span class="text-sm text-gray-600">
                    ({{ $item->product->stocks }} available)
                </span>
            </div>

            <!-- Subtotal -->
            <div class="mt-4 pt-4 border-t">
                <p class="text-sm text-gray-600">Subtotal:</p>
                <p class="text-xl font-bold text-[#3F3142]">
                    Rp{{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
</div>
