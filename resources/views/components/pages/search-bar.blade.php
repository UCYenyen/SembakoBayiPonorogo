<div x-data="searchComponent()" @click.away="open = false" class="relative w-full {{ $extraClasses ?? '' }}">
    <form action="{{ url($route) }}" method="GET" class="flex gap-2 w-full">
        <div class="relative flex-1">
            <input type="text" name="search" x-model="query" @input.debounce.300ms="search()" @focus="open = true"
                value="{{ request('search') }}" placeholder="{{ $placeholder }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent text-sm md:text-base"
                autocomplete="off">

            {{-- Loading Spinner --}}
            <div x-show="loading" class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin h-5 w-5 text-[#3F3142]" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </div>

            {{-- Search Results Dropdown --}}
            <div x-show="open && results.length > 0" x-transition
                class="absolute z-50 w-full mt-2 bg-white rounded-lg shadow-xl border border-gray-200 max-h-[500px] overflow-y-auto">

                <template x-for="product in results" :key="product.id">
                    <a :href="`/shop/products/${product.id}`"
                        class="flex items-center gap-3 p-3 hover:bg-gray-50 transition-colors border-b last:border-b-0">
                        {{-- Product Image --}}
                        <img :src="`/storage/${product.image_url}`" :alt="product.name"
                            class="w-16 h-16 object-cover rounded-lg flex-shrink-0" loading="lazy">

                        {{-- Product Info --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-sm text-gray-900 truncate" x-text="product.name"></h3>
                            <p class="text-xs text-gray-500 truncate" x-text="product.category?.name"></p>
                            <p class="text-sm font-bold text-[#3F3142] mt-1">
                                Rp<span x-text="formatPrice(product.price)"></span>
                            </p>
                        </div>

                        {{-- Stock Badge --}}
                        <div class="flex-shrink-0">
                            <span class="px-2 py-1 rounded-full text-xs"
                                :class="product.stocks > 10 ? 'bg-green-100 text-green-800' : (product.stocks > 0 ?
                                    'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')"
                                x-text="`Stock: ${product.stocks}`">
                            </span>
                        </div>
                    </a>
                </template>

                {{-- View All Results --}}
                <button type="submit" @click="open = false"
                    class="w-full p-3 text-center text-sm font-semibold text-[#3F3142] hover:bg-gray-50 transition-colors">
                    Lihat Semua →
                </button>
            </div>

            {{-- No Results Message --}}
            <div x-show="open && query.length > 0 && results.length === 0 && !loading" x-transition
                class="absolute z-50 w-full mt-2 bg-white rounded-lg shadow-xl border border-gray-200 p-4 text-center">
                <p class="text-gray-500 text-sm">Tidak ada produk ditemukan untuk "<span x-text="query"></span>"</p>
            </div>
        </div>

        <button type="submit" hidden @click="handleSubmit($event)"
            class="px-4 py-2 bg-[#3F3142] hover:bg-[#5C4B5E] text-white rounded-lg transition-colors duration-200 text-sm md:text-base whitespace-nowrap">
            <x-heroicon-o-magnifying-glass class="w-5 h-5 inline-block" />
        </button>
    </form>
</div>

<script>
    function searchComponent() {
        return {
            query: '{{ request('search') ?? '' }}',
            results: [],
            open: false,
            loading: false,
            abortController: null,

            search() {
                // Cancel previous request if exists
                if (this.abortController) {
                    this.abortController.abort();
                }

                // Don't search if query is too short or empty
                if (this.query.trim().length < 2) {
                    this.results = [];
                    this.open = false;
                    return;
                }

                this.loading = true;
                this.abortController = new AbortController();

                fetch(`/api/products/search?q=${encodeURIComponent(this.query)}`, {
                        signal: this.abortController.signal
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.results = data;
                        this.open = true;
                        this.loading = false;
                    })
                    .catch(error => {
                        if (error.name !== 'AbortError') {
                            console.error('Search error:', error);
                            this.loading = false;
                        }
                    });
            },

            handleSubmit(event) {
                // Prevent form submission if query is empty
                if (this.query.trim().length === 0) {
                    event.preventDefault();
                    return false;
                }
            },

            formatPrice(price) {
                return new Intl.NumberFormat('id-ID').format(price);
            }
        }
    }
</script>
