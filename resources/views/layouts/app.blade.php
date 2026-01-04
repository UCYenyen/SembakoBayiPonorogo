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
    <!-- Untuk Sandbox -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-[#FFF3F3] min-h-screen overflow-x-hidden">
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
    <script>
        function openTrackingModal(transactionId) {
            Swal.fire({
                title: 'Sedang melacak...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            axios.post(`/track-delivery/${transactionId}`)
                .then(response => {
                    const data = response.data.data;
                    const manifest = data.manifest;

                    let manifestHtml = `
                <div class="text-left border-b pb-3 mb-4">
                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Informasi Pengiriman</p>
                    <p class="text-sm"><strong>Status:</strong> <span class="text-green-600 font-bold">${data.summary.status}</span></p>
                    <p class="text-sm"><strong>Kurir:</strong> ${data.summary.courier_name}</p>
                </div>
                <div class="max-h-96 overflow-y-auto px-1">`;

                    manifest.forEach((item, index) => {
                        const isLast = index === manifest.length - 1;

                        manifestHtml += `
                    <div class="relative pb-6 pl-8 ${isLast ? '' : 'border-l-2 border-[#3F3142]'}">
                        <div class="absolute -left-[6px] top-0 w-4 h-4 rounded-full border-2 border-white bg-[#3F3142] ${isLast ? 'ring-4 ring-[#3F3142]/20' : ''}"></div>
                        
                        <div class="${isLast ? 'opacity-100' : 'opacity-50'} text-left">
                            <p class="text-[10px] font-mono font-bold text-gray-500">${item.manifest_date} ${item.manifest_time}</p>
                            <p class="text-sm font-bold text-[#3F3142] leading-tight">${item.manifest_description}</p>
                            <p class="text-xs text-gray-400 italic mt-1">${item.city_name}</p>
                        </div>
                    </div>`;
                    });

                    manifestHtml += `</div>`;

                    Swal.fire({
                        title: `<span class="text-lg font-bold text-[#3F3142]">Resi: ${data.summary.waybill_number}</span>`,
                        html: manifestHtml,
                        confirmButtonColor: '#3F3142',
                        confirmButtonText: 'Tutup',
                        width: '500px'
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: err.response?.data?.message || 'Gagal melacak pesanan.',
                        confirmButtonColor: '#3F3142'
                    });
                });
        }
    </script>
</body>

</html>
