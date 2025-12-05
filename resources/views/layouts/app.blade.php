<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-[#FFF3F3] min-h-screen">
    <x-pages.navigation-bar />
    <div class="h-[8vh] sm:h-[12vh]"></div>
    @yield('content')
    <x-pages.footer />
    <script>
        const hamburgerBtn = document.getElementById('hamburgerButton');
        const mobilePanel = document.getElementById('panelMenuMobile');

        if (hamburgerBtn && mobilePanel) {
            hamburgerBtn.addEventListener('click', () => {
                mobilePanel.classList.toggle('show');
                // Optional: Toggle hamburger icon animation
                hamburgerBtn.classList.toggle('active');
            });

            // Close menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!hamburgerBtn.contains(e.target) && !mobilePanel.contains(e.target)) {
                    mobilePanel.classList.remove('show');
                    hamburgerBtn.classList.remove('active');
                }
            });
        }
    </script>
</body>

</html>
