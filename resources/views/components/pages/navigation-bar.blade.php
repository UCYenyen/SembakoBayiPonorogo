<header class="fixed z-[100] w-screen overflow-hidden rounded-lg flex justify-center items-center h-[12vh]">
    <nav class="bg-white shadow-sm rounded-lg w-full max-w-[80%] gap-8 flex justify-between p-4 items-center">
        <a href="/">
            <h1 class="text-2xl font-bold">SBP</h1>
        </a>
        <x-pages.search-bar />
        <div class="flex gap-4 justify-center items-center text-xl">
            <a href="/shop">Shop</a>
            @if (Auth::check())
                @if (Auth::user()->role === 'admin')
                    <a href="/dashboard">Dashboard</a>
                    <a href="/dashboard/admin">Admin Dashboard</a>
                @else
                    <a href="/dashboard">Dashboard</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="hover:underline">Logout</button>
                </form>
            @else
                <x-pages.solid-button link="/login"
                    extraClass="bg-[#3F3142] shadow-lg rounded-lg text-xl text-white hover:bg-[#5C4B5E]">
                    Login
                </x-pages.solid-button>
            @endif
        </div>
    </nav>
</header>
