@extends('layouts.app')
@section('title', 'Home')
@section('content')
    <x-pages.section title="Product Manager" extraClasses="bg-white">
        <div class="w-[90%] mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-3xl font-bold">Product Management</h1>
                    <a href="/dashboard/admin/products/create"
                        class="bg-[#3F3142] text-white px-6 py-3 rounded-lg hover:bg-[#5C4B5E] transition-colors font-semibold">
                        + Add New Product
                    </a>
                </div>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Products Table -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead class="bg-[#3F3142] text-white">
                            <tr>
                                <th class="px-4 py-3 text-left">ID</th>
                                <th class="px-4 py-3 text-left">Image</th>
                                <th class="px-4 py-3 text-left">Name</th>
                                <th class="px-4 py-3 text-left">Category</th>
                                <th class="px-4 py-3 text-left">Brand</th>
                                <th class="px-4 py-3 text-left">Price</th>
                                <th class="px-4 py-3 text-left">Stock</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $product->id }}</td>
                                    <td class="px-4 py-3">
                                        <img src="{{ asset('storage/' . $product->image_url) }}"
                                            alt="{{ $product->name }}"
                                            class="w-16 h-16 object-cover rounded-lg">
                                    </td>
                                    <td class="px-4 py-3 font-semibold">{{ $product->name }}</td>
                                    <td class="px-4 py-3">{{ $product->category->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ $product->brand->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">Rp{{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="px-2 py-1 rounded-full text-sm {{ $product->stocks > 10 ? 'bg-green-100 text-green-800' : ($product->stocks > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $product->stocks }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($product->is_hidden)
                                            <span
                                                class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">Hidden</span>
                                        @else
                                            <span
                                                class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">Visible</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-center items-center gap-2">
                                            <!-- Toggle Visibility -->
                                            <form action="/dashboard/admin/products/{{ $product->id }}/toggle-visibility"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="p-2 rounded-lg transition-colors {{ $product->is_hidden ? 'bg-gray-200 hover:bg-gray-300 text-gray-700' : 'bg-blue-100 hover:bg-blue-200 text-blue-700' }}"
                                                    title="{{ $product->is_hidden ? 'Show Product' : 'Hide Product' }}">
                                                    @if ($product->is_hidden)
                                                        <x-heroicon-o-eye-slash class="w-5 h-5" />
                                                    @else
                                                        <x-heroicon-o-eye class="w-5 h-5" />
                                                    @endif
                                                </button>
                                            </form>

                                            <!-- Edit -->
                                            <a href="/dashboard/admin/products/{{ $product->id }}/edit"
                                                class="p-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-lg transition-colors"
                                                title="Edit Product">
                                                <x-heroicon-o-pencil class="w-5 h-5" />
                                            </a>

                                            <!-- Delete -->
                                            <form action="/dashboard/admin/products/{{ $product->id }}" method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Are you sure you want to delete this product?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors"
                                                    title="Delete Product">
                                                    <x-heroicon-o-trash class="w-5 h-5" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                        No products found. <a href="/dashboard/admin/products/create"
                                            class="text-[#3F3142] hover:underline font-semibold">Add your first
                                            product</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($products->hasPages())
                    <div class="px-6 py-4 border-t">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </x-pages.section>
@endsection
