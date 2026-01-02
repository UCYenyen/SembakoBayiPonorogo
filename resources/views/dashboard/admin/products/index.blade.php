@extends('layouts.app')
@section('title', 'Product Management')
@section('content')
    <x-pages.section title="" extraClasses="min-h-[80vh]">
        <div class="w-[80%] mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex justify-between items-center flex-col gap-4 sm:flex-row">
                    <h1 class="text-3xl font-bold">Product Management</h1>
                    <a href="{{ route('admin.products.create') }}"
                        class="bg-[#3F3142] w-full sm:w-fit text-white px-6 py-3 rounded-lg hover:bg-[#5C4B5E] transition-colors font-semibold text-center">
                        + Add New Product
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead class="bg-[#3F3142] text-white">
                            <tr>
                                <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">ID</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Gambar</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Nama</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Kategori</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Merek</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Harga</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Stok</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Status</th>
                                <th class="px-4 py-4 text-center text-sm font-semibold uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($products as $product)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4 align-middle text-sm text-gray-600">#{{ $product->id }}</td>
                                    <td class="px-4 py-4 align-middle">
                                        <div class="w-16 h-16 overflow-hidden rounded-lg border">
                                            <img src="{{$product->image_path}}"
                                                alt="{{ $product->name }}" class="w-full h-full object-cover">
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
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-bold
                                            {{ $product->stocks > 10 ? 'bg-gray-100 text-gray-800' : ($product->stocks > 0 ? 'bg-yellow-100 text-gray-800' : 'bg-red-100 text-gray-800') }}">
                                            {{ $product->stocks }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 align-middle">
                                        @if ($product->is_hidden)
                                            <span
                                                class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-bold uppercase tracking-wider">Hidden</span>
                                        @else
                                            <span
                                                class="px-3 py-1 bg-[#dbdeff] text-gray-700 rounded-full text-xs font-bold uppercase tracking-wider">Active</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 align-middle text-center">
                                        <div class="flex justify-center items-center gap-2">
                                            <form action="{{ route('admin.products.toggle', $product) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="p-2 rounded-md transition-all {{ $product->is_hidden ? 'bg-gray-100 text-gray-500 hover:bg-gray-200' : 'bg-blue-50 text-blue-600 hover:bg-blue-100' }}">
                                                    @if ($product->is_hidden)
                                                        <x-heroicon-o-eye-slash class="w-5 h-5" />
                                                    @else
                                                        <x-heroicon-o-eye class="w-5 h-5" />
                                                    @endif
                                                </button>
                                            </form>

                                            <a href="{{ route('admin.products.edit', $product) }}"
                                                class="p-2 bg-yellow-50 text-yellow-600 hover:bg-yellow-100 rounded-md transition-all">
                                                <x-heroicon-o-pencil class="w-5 h-5" />
                                            </a>

                                            <form id="delete-form-{{ $product->id }}"
                                                action="{{ route('admin.products.delete', $product) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete({{ $product->id }})"
                                                    class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-md transition-all">
                                                    <x-heroicon-o-trash class="w-5 h-5" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <span>Tidak ada produk yang ditemukan.</span>
                                            <a href="{{ route('admin.products.create') }}"
                                                class="text-[#3F3142] font-bold hover:underline">Tambah produk pertama
                                                Anda</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($products->hasPages())
                    <div class="px-6 py-4 border-t bg-gray-50">
                        {{ $products->links('vendor.pagination.simple') }}
                    </div>
                @endif
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

        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Produk?',
                text: "Data ini akan dihapus permanen dari sistem.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3F3142',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
@endsection
