@extends('layouts.app')
@section('title', 'Home')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] flex items-center justify-center pt-[10%] md:pt-0 md:min-h-[85vh] flex-col">
        <x-section title="Admin Dashboard" extraClasses="items-center justify-center">
            <div class="grid grid-cols-1 md:grid-cols-2 w-[80%] h-full gap-8">
                <x-admin-menu-chooser
                    extraClasses="flex flex-col justify-center items-center w-full h-full p-4 gap-4 bg-white shadow-lg rounded-lg"
                    link="/" title="Atur Event" description="Tambah atau ubah event seperti broadcast atau promo">
                    <x-gmdi-event class="text-xl max-w-[84px]" />
                </x-admin-menu-chooser>
                <x-admin-menu-chooser
                    extraClasses="flex flex-col justify-center items-center w-full h-full p-4 gap-4 bg-white shadow-lg rounded-lg"
                    link="/" title="Atur Layanan" description="Tambah atau ubah layanan pengiriman">
                    <x-eos-service class="text-xl max-w-[84px]" />
                </x-admin-menu-chooser>
                <x-admin-menu-chooser
                    extraClasses="flex flex-col justify-center items-center w-full h-full p-4 gap-4 bg-white shadow-lg rounded-lg"
                    link="/" title="Atur Produk" description="Tambah, ubah, atau hapus produk dari gudang.">
                    <x-gmdi-inventory-2-o class="text-xl max-w-[84px]"/>
                </x-admin-menu-chooser>
                <x-admin-menu-chooser
                    extraClasses="flex flex-col justify-center items-center w-full h-full p-4 gap-4 bg-white shadow-lg rounded-lg"
                    link="/" title="Lihat Penjualan"
                    description="Pantau dan proses pesanan pelanggan dengan efisien.">
                    <x-ri-money-dollar-circle-fill class="text-xl max-w-[84px]" />
                </x-admin-menu-chooser>
            </div>
        </x-section>
    </main>
@endsection
