@extends('layouts.app')
@section('title', 'Voucher Management')
@section('content')
    <x-pages.section title="" extraClasses="min-h-[80vh]">
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
            <!-- Vouchers Table -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead class="bg-[#3F3142] text-white">
                            <tr>
                                <th class="px-4 py-3 text-left">ID</th>
                                <th class="px-4 py-3 text-left">Nama Voucher</th>
                                <th class="px-4 py-3 text-left">Jumlah Diskon</th>
                                <th class="px-4 py-3 text-left">Points Yang Dibutuhkan</th>
                                <th class="px-4 py-3 text-left">Dibuat Pada</th>
                                <th class="px-4 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vouchers as $voucher)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $voucher->id }}</td>
                                    <td class="px-4 py-3 font-semibold">{{ $voucher->name }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="bg-gray-100 text-black px-3 py-1 rounded-full text-sm font-semibold">
                                            Rp{{ number_format($voucher->discount_amount, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="bg-gray-100 text-black px-3 py-1 rounded-full text-sm font-semibold">
                                            {{ number_format($voucher->points_required) }} points
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $voucher->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex justify-center items-center gap-2">
                                            <a href="{{ route('admin.vouchers.edit', $voucher) }}"
                                                class="flex items-center justify-center p-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-lg transition-colors h-9 w-9"
                                                title="Edit Voucher">
                                                <x-heroicon-o-pencil class="w-5 h-5" />
                                            </a>

                                            <form action="{{ route('admin.vouchers.delete', $voucher) }}" method="POST"
                                                class="delete-form m-0 inline-flex">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="flex items-center justify-center p-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors btn-delete h-9 w-9"
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
                                        Tidak ada voucher ditemukan.
                                        <br>
                                        <a href="{{ route('admin.vouchers.create') }}"
                                            class="text-[#3F3142] hover:underline font-semibold">Tambahkan voucher pertama Anda
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($vouchers->hasPages())
                    <div class="px-6 py-4 border-t">
                        {{ $vouchers->links('vendor.pagination.simple') }}
                    </div>
                @endif
            </div>
        </div>
    </x-pages.section>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
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

        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const form = this.closest('.delete-form');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data voucher yang dihapus tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3F3142',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
