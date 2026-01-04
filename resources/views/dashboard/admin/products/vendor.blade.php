@extends('layouts.app')
@section('title', 'Manage Vendors - ' . $product->name)
@section('content')
    <x-pages.section title="" extraClasses="min-h-[80vh]">
        <div class="w-[80%] mx-auto mb-[5%]">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex justify-between items-center flex-col gap-4 sm:flex-row">
                    <div>
                        <a href="{{ route('admin.products') }}" class="text-[#3F3142] hover:underline text-sm mb-2 inline-block">
                            ← Kembali ke Product Management
                        </a>
                        <h1 class="text-3xl font-bold">Manage Vendors</h1>
                        <p class="text-gray-600 mt-1">Produk: <span class="font-semibold">{{ $product->name }}</span></p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Add Vendor Form -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-4 text-[#3F3142]">Tambah Vendor</h2>
                    
                    @if($availableVendors->count() > 0)
                        <form action="{{ route('admin.products.vendors.attach', $product) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Pilih Vendor</label>
                                <select name="vendor_id" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3F3142]" required>
                                    <option value="">-- Pilih Vendor --</option>
                                    @foreach($availableVendors as $vendor)
                                        <option value="{{ $vendor->id }}">
                                            {{ $vendor->name }} ({{ ucfirst($vendor->type) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full bg-[#3F3142] text-white px-6 py-3 rounded-lg hover:bg-[#5C4B5E] transition-colors font-semibold">
                                + Tambah Vendor
                            </button>
                        </form>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 mb-4">Semua vendor sudah ditambahkan ke produk ini</p>
                            <a href="{{ route('admin.vendors.create') }}" class="text-[#3F3142] hover:underline font-semibold">
                                Buat vendor baru
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Product Info -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-4 text-[#3F3142]">Informasi Produk</h2>
                    <div class="flex items-start gap-4">
                        <img src="{{ $product->image_path }}" alt="{{ $product->name }}" class="w-24 h-24 object-cover rounded-lg border">
                        <div class="flex-1">
                            <h3 class="font-bold text-lg">{{ $product->name }}</h3>
                            <p class="text-gray-600 text-sm mt-1">{{ $product->category->name ?? '-' }} | {{ $product->brand->name ?? '-' }}</p>
                            <p class="text-[#3F3142] font-bold mt-2">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                            <p class="text-sm text-gray-500 mt-1">Stok: {{ $product->stocks }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Vendors List -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mt-6">
                <div class="p-6 border-b bg-gray-50">
                    <h2 class="text-xl font-bold text-[#3F3142]">Vendor Terhubung ({{ $product->vendors->count() }})</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead class="bg-[#3F3142] text-white">
                            <tr>
                                <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">ID</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Nama Vendor</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Tipe</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Kontak</th>
                                <th class="px-4 py-4 text-center text-sm font-semibold uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($product->vendors as $vendor)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4 align-middle text-sm text-gray-600">#{{ $vendor->id }}</td>
                                    <td class="px-4 py-4 align-middle">
                                        <div class="font-bold text-gray-900">{{ $vendor->name }}</div>
                                    </td>
                                    <td class="px-4 py-4 align-middle">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase
                                            {{ $vendor->type === 'online' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ ucfirst($vendor->type) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 align-middle text-sm">
                                        @if($vendor->type === 'online' && $vendor->link)
                                            <a href="{{ $vendor->link }}" target="_blank" class="text-blue-600 hover:underline">
                                                {{ Str::limit($vendor->link, 30) }}
                                            </a>
                                        @elseif($vendor->type === 'offline' && $vendor->phone_number)
                                            <span class="text-gray-700">{{ $vendor->phone_number }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 align-middle text-center">
                                        <form id="detach-form-{{ $vendor->id }}"
                                            action="{{ route('admin.products.vendors.detach', [$product, $vendor]) }}" 
                                            method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDetach({{ $vendor->id }}, '{{ $vendor->name }}')"
                                                class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-md transition-all">
                                                <x-heroicon-o-trash class="w-5 h-5" />
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <span>Belum ada vendor yang terhubung dengan produk ini.</span>
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
                title: 'Hapus Vendor?',
                text: `Vendor "${name}" akan dihapus dari produk ini.`,
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