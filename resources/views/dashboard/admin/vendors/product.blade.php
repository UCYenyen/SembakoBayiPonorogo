@extends('layouts.app')
@section('title', 'Manage Products - ' . $vendor->name)
@section('content')
    <x-pages.section title="" extraClasses="min-h-[80vh]">
        <div class="w-[80%] mx-auto mb-[5%]">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex justify-between items-center flex-col gap-4 sm:flex-row">
                    <div>
                        <a href="{{ route('admin.vendors.index') }}" class="text-[#3F3142] hover:underline text-sm mb-2 inline-block">
                            ← Kembali ke Vendor Management
                        </a>
                        <h1 class="text-3xl font-bold">Manage Products</h1>
                        <p class="text-gray-600 mt-1">Vendor: <span class="font-semibold">{{ $vendor->name }}</span></p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Add Product Form -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-4 text-[#3F3142]">Tambah Produk</h2>
                    
                    @if($availableProducts->count() > 0)
                        <form action="{{ route('admin.vendors.products.attach', $vendor) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Pilih Produk</label>
                                <select name="product_id" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3F3142]" required>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($availableProducts as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->name }} - Rp{{ number_format($product->price, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full bg-[#3F3142] text-white px-6 py-3 rounded-lg hover:bg-[#5C4B5E] transition-colors font-semibold">
                                + Tambah Produk
                            </button>
                        </form>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 mb-4">Semua produk sudah ditambahkan ke vendor ini</p>
                            <a href="{{ route('admin.products.create') }}" class="text-[#3F3142] hover:underline font-semibold">
                                Buat produk baru
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Vendor Info -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-4 text-[#3F3142]">Informasi Vendor</h2>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm text-gray-600">Nama Vendor</label>
                            <p class="font-bold text-lg">{{ $vendor->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Tipe</label>
                            <p>
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase inline-block
                                    {{ $vendor->type === 'online' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($vendor->type) }}
                                </span>
                            </p>
                        </div>
                        @if($vendor->type === 'online' && $vendor->link)
                            <div>
                                <label class="text-sm text-gray-600">Link</label>
                                <p>
                                    <a href="{{ $vendor->link }}" target="_blank" class="text-blue-600 hover:underline break-all">
                                        {{ $vendor->link }}
                                    </a>
                                </p>
                            </div>
                        @endif
                        @if($vendor->type === 'offline' && $vendor->phone_number)
                            <div>
                                <label class="text-sm text-gray-600">Nomor Telepon</label>
                                <p class="font-semibold">{{ $vendor->phone_number }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Current Products List -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mt-6">
                <div class="p-6 border-b bg-gray-50">
                    <h2 class="text-xl font-bold text-[#3F3142]">Produk Terhubung ({{ $vendor->products->count() }})</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead class="bg-[#3F3142] text-white">
                            <tr>
                                <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">ID</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Gambar</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Nama Produk</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Kategori</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Merek</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Harga</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Stok</th>
                                <th class="px-4 py-4 text-center text-sm font-semibold uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($vendor->products as $product)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4 align-middle text-sm text-gray-600">#{{ $product->id }}</td>
                                    <td class="px-4 py-4 align-middle">
                                        <div class="w-16 h-16 overflow-hidden rounded-lg border">
                                            <img src="{{ $product->image_path }}" alt="{{ $product->name }}"
                                                class="w-full h-full object-cover">
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 align-middle">
                                        <div class="font-bold text-gray-900 leading-tight">{{ $product->name }}</div>
                                    </td>
                                    <td class="px-4 py-4 align-middle text-sm text-gray-700">
                                        {{ $product->category->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-4 align-middle text-sm text-gray-700">
                                        {{ $product->brand->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-4 align-middle text-sm font-medium whitespace-nowrap">
                                        Rp{{ number_format($product->price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 align-middle whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                            {{ $product->stocks > 10 ? 'bg-gray-100 text-gray-800' : ($product->stocks > 0 ? 'bg-yellow-100 text-gray-800' : 'bg-red-100 text-gray-800') }}">
                                            {{ $product->stocks }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 align-middle text-center">
                                        <form id="detach-form-{{ $product->id }}"
                                            action="{{ route('admin.vendors.products.detach', [$vendor, $product]) }}" 
                                            method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDetach({{ $product->id }}, '{{ $product->name }}')"
                                                class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-md transition-all">
                                                <x-heroicon-o-trash class="w-5 h-5" />
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <span>Belum ada produk yang terhubung dengan vendor ini.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </x-pages.section>

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#3F3142',
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{{ session('error') }}",
                confirmButtonColor: '#3F3142',
            });
        @endif

        function confirmDetach(id, name) {
            Swal.fire({
                title: 'Hapus Produk?',
                text: `Produk "${name}" akan dihapus dari vendor ini.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3F3142',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('detach-form-' + id).submit();
                }
            })
        }
    </script>
@endsection