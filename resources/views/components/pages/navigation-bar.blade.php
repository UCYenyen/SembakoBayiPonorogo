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
                <div x-data="{ open: false }" @click.away="open = false" class="relative">
                    <button @click="open = !open"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                        {{-- ✅ Avatar di Button (7x7 = 28px) --}}
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" 
                                 alt="{{ Auth::user()->name }}" 
                                 class="w-7 h-7 rounded-full object-cover border-2 border-[#3F3142]"
                                 referrerpolicy="no-referrer"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-7 h-7 bg-[#3F3142] rounded-full items-center justify-center text-white text-xs font-semibold hidden">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @else
                            <div class="w-7 h-7 bg-[#3F3142] rounded-full flex items-center justify-center text-white text-xs font-semibold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <span class="hidden md:inline text-sm font-medium">{{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div x-show="open" x-transition
                        class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl py-2 z-50">
                        
                        <div class="px-4 py-3 border-b border-gray-200">
                            {{-- ✅ Avatar di Dropdown Header (10x10 = 40px) --}}
                            <div class="flex items-center gap-3">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" 
                                         alt="{{ Auth::user()->name }}" 
                                         class="w-10 h-10 rounded-full object-cover border-2 border-[#3F3142] flex-shrink-0"
                                         referrerpolicy="no-referrer"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-10 h-10 bg-[#3F3142] rounded-full items-center justify-center text-white font-bold flex-shrink-0 hidden">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @else
                                    <div class="w-10 h-10 bg-[#3F3142] rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
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
                {{-- ✅ Google Login Button --}}
                <a href="{{ route('auth.google') }}"
                    class="flex items-center gap-2 bg-white border-2 border-[#3F3142] shadow-lg rounded-lg text-sm text-[#3F3142] hover:bg-[#3F3142] hover:text-white px-4 py-2 transition-all duration-200">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span class="hidden md:inline font-semibold">Login</span>
                </a>
            @endif
        </div>
    </nav>
</header>
