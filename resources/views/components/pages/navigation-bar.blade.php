<header class="fixed z-[100] w-screen rounded-lg flex justify-center items-center min-h-[12vh]">
    <nav class="bg-white shadow-sm rounded-lg w-full max-w-[80%] gap-8 flex justify-between p-4 items-center">
        <a href="/" class="flex">
            <h1 class="text-2xl font-bold">SBP</h1>
        </a>
        <x-pages.search-bar route="/" placeholder="Search..." extraClasses="hidden"/>
        <div class="flex gap-4 justify-center items-center text-xl">
            <a href="/shop"><x-heroicon-o-shopping-bag class="w-6 h-6 text-[#3F3142]"/></a>
            <a href="/cart"><x-heroicon-o-shopping-cart class="w-6 h-6 text-[#3F3142]" /></a>
            @if (Auth::check())
                {{-- <a href="/dashboard">Dashboard</a> --}}
                <div x-data="{ open: false }" @click.away="open = false" class="relative">
                    <button @click="open = !open"
                        class="relative w-10 h-10 rounded-full bg-[#3F3142] hover:bg-[#5C4B5E] text-white">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </button>

                    {{-- Dropdown Menu --}}
                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute -right-[1rem] mt-4 w-48 bg-white rounded-lg shadow-lg py-2 z-50"
                        style="display: none;">

                        <div class="px-4 py-2 border-b border-gray-200">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                            <p
                                class="text-xs text-gray-500 overflow-x-auto whitespace-nowrap scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-200">
                                {{ Auth::user()->email }}
                            </p>
                        </div>

                        @if (Auth::user()->role === 'admin')
                            <a href="/dashboard/user" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Dashboard
                            </a>
                            <a href="/dashboard/admin" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Admin Dashboard
                            </a>
                        @else
                            <a href="/dashboard/user" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Dashboard
                            </a>
                        @endif


                        <a href="/shop/cart" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            My Cart
                        </a>

                        <form method="POST" action="/logout" class="block">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <x-pages.solid-button link="/login"
                    extraClasses="bg-[#3F3142] shadow-lg rounded-lg text-lg text-white hover:bg-[#5C4B5E]">
                    Login
                </x-pages.solid-button>
            @endif
        </div>
    </nav>
</header>
