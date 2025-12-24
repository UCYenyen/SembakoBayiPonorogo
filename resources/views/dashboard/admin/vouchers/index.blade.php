@extends('layouts.app')
@section('title', 'Voucher Management')
@section('content')
    <x-pages.section title="" extraClasses="">
        <div class="w-[80%] mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex justify-between items-center flex-col gap-4 sm:flex-row">
                    <h1 class="text-3xl font-bold">Voucher Management</h1>
                    <a href="{{ route('admin.vouchers.create') }}"
                        class="bg-[#3F3142] w-full sm:w-fit text-white px-6 py-3 rounded-lg hover:bg-[#5C4B5E] transition-colors font-semibold">
                        + Add New Voucher
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

            <!-- Vouchers Table -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead class="bg-[#3F3142] text-white">
                            <tr>
                                <th class="px-4 py-3 text-left">ID</th>
                                <th class="px-4 py-3 text-left">Voucher Name</th>
                                <th class="px-4 py-3 text-left">Discount Amount</th>
                                <th class="px-4 py-3 text-left">Points Required</th>
                                <th class="px-4 py-3 text-left">Created At</th>
                                <th class="px-4 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vouchers as $voucher)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $voucher->id }}</td>
                                    <td class="px-4 py-3 font-semibold">{{ $voucher->name }}</td>
                                    <td class="px-4 py-3">
                                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                                            Rp{{ number_format($voucher->discount_amount, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                            {{ number_format($voucher->points_required) }} pts
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $voucher->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-center items-center gap-2">
                                            <!-- Edit -->
                                            <a href="{{ route('admin.vouchers.edit', $voucher) }}"
                                                class="p-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-lg transition-colors"
                                                title="Edit Voucher">
                                                <x-heroicon-o-pencil class="w-5 h-5" />
                                            </a>

                                            <!-- Delete -->
                                            <form action="{{ route('admin.vouchers.delete', $voucher) }}" method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Are you sure you want to delete this voucher?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors"
                                                    title="Delete Voucher">
                                                    <x-heroicon-o-trash class="w-5 h-5" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        No vouchers found. <a href="{{ route('admin.vouchers.create') }}"
                                            class="text-[#3F3142] hover:underline font-semibold">Add your first voucher</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($vouchers->hasPages())
                    <div class="px-6 py-4 border-t">
                        {{ $vouchers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </x-pages.section>
@endsection
