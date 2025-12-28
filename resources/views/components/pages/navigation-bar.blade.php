<header class="fixed z-[100] w-screen rounded-lg flex justify-center items-center min-h-[12vh]">
    <nav class="bg-white shadow-sm rounded-lg w-full max-w-[80%] gap-8 flex justify-between p-4 items-center">
        <a href="/" class="flex">
            <h1 class="text-2xl font-bold">SBP</h1>
        </a>
        
        <x-pages.search-bar route="/shop" placeholder="Search products..." extraClasses="hidden md:block flex-1"/>
        
        <div class="flex gap-4 justify-center items-center text-xl">
            <a href="/shop"><x-heroicon-o-shopping-bag class="w-6 h-6 text-[#3F3142]"/></a>
            <a href="/cart"><x-heroicon-o-shopping-cart class="w-6 h-6 text-[#3F3142]" /></a>
            
            @if (Auth::check())
                <div x-data="{ open: false }" @click.away="open = false" class="relative">
                    <button @click="open = !open"
                        class="flex items-center justify-center p-1 rounded-full hover:bg-gray-100 transition-colors">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" 
                                 alt="{{ Auth::user()->name }}" 
                                 class="w-10 h-10 min-w-[40px] min-h-[40px] rounded-full object-cover border-2 border-[#3F3142]"
                                 referrerpolicy="no-referrer"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-10 h-10 min-w-[40px] min-h-[40px] bg-[#3F3142] rounded-full items-center justify-center text-white text-sm font-semibold hidden">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @else
                            <div class="w-10 h-10 min-w-[40px] min-h-[40px] bg-[#3F3142] rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </button>

                    <div x-show="open" x-transition
                        class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl py-2 z-50">
                        
                        <div class="px-4 py-3 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" 
                                         alt="{{ Auth::user()->name }}" 
                                         class="w-12 h-12 min-w-[48px] min-h-[48px] rounded-full object-cover border-2 border-[#3F3142] flex-shrink-0"
                                         referrerpolicy="no-referrer"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-12 h-12 min-w-[48px] min-h-[48px] bg-[#3F3142] rounded-full items-center justify-center text-white font-bold flex-shrink-0 hidden">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @else
                                    <div class="w-12 h-12 min-w-[48px] min-h-[48px] bg-[#3F3142] rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                    @if(Auth::user()->phone_number)
                                        <p class="text-xs text-gray-500 truncate mt-0.5">
                                            📱 {{ Auth::user()->phone_number }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if (Auth::user()->role === 'admin')
                            <a href="/dashboard/user" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Dasbor Pengguna
                            </a>
                            <a href="/dashboard/admin" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Dasbor Admin
                            </a>
                        @else
                            <a href="/dashboard/user" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Dasbor
                            </a>
                        @endif

                        <a href="/cart" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Keranjang Saya
                        </a>

                        <a href="/dashboard/user/addresses" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Alamat Saya
                        </a>

                        <form method="POST" action="/logout" class="block">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{route("login")}}" class="flex items-center gap-2 hover:bg-[#3F3142]/80 border-2 border-[#3F3142] shadow-lg rounded-lg text-sm bg-[#3F3142] text-white px-4 py-2 transition-all duration-200 group font-bold">Masuk</a>
            @endif
        </div>
    </nav>
</header>
