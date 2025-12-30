@extends('layouts.app')
@section('title', 'Vendor Management')
@section('content')
    <x-pages.section title="" extraClasses="extraClasses="min-h-[80vh]">
        <div class="w-[80%] mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex justify-between items-center flex-col gap-4 sm:flex-row">
                    <h1 class="text-3xl font-bold">Vendor Management</h1>
                    <a href="{{ route('admin.vendors.create') }}"
                        class="bg-[#3F3142] w-full sm:w-fit text-white px-6 py-3 rounded-lg hover:bg-[#5C4B5E] transition-colors font-semibold">
                        + Tambah Vendor Baru
                    </a>
                </div>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Error Message -->
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Vendors Table -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead class="bg-[#3F3142] text-white">
                            <tr>
                                <th class="px-4 py-3 text-left">ID</th>
                                <th class="px-2 py-3 text-left">Nama Vendor</th>
                                <th class="px-2 py-3 text-left">Nomor Telepon</th>
                                <th class="px-4 py-3 text-left">Tipe</th>
                                <th class="px-8 py-3 text-left">Link</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendors as $vendor)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $vendor->id }}</td>
                                    <td class="px-2 py-3 font-semibold">{{ $vendor->name }}</td>
                                    <td class="px-2 py-3">
                                        {{ $vendor->phone_number ? '+62' . $vendor->phone_number : '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($vendor->type)
                                            <span class="px-3 py-1 rounded-full text-sm font-semibold 
                                                {{ $vendor->type === 'online' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                {{ ucfirst($vendor->type) }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-3">
                                        @if($vendor->link)
                                            <a href="{{ $vendor->link }}" target="_blank" 
                                               class="text-blue-600 hover:text-blue-800 hover:underline truncate block max-w-lg"
                                               title="{{ $vendor->link }}">
                                                {{ Str::limit($vendor->link, 50) }}
                                                <svg class="w-3 h-3 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                            </a>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-center items-center gap-2">
                                            <!-- Edit -->
                                            <a href="{{ route('admin.vendors.edit', $vendor) }}"
                                                class="p-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-lg transition-colors"
                                                title="Edit Vendor">
                                                <x-heroicon-o-pencil class="w-5 h-5" />
                                            </a>

                                            <!-- Delete -->
                                            <form action="{{ route('admin.vendors.delete', $vendor) }}" method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus vendor ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors"
                                                    title="Delete Vendor">
                                                    <x-heroicon-o-trash class="w-5 h-5" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        Masih belum ada vendor.
                                        <br>
                                        <a href="{{ route('admin.vendors.create') }}"
                                            class="text-[#3F3142] hover:underline font-semibold">
                                            Tambah vendor pertama Anda
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($vendors->hasPages())
                    <div class="px-6 py-4 border-t">
                        {{ $vendors->links('vendor.pagination.simple') }}
                    </div>
                @endif
            </div>
        </div>
    </x-pages.section>
@endsection
