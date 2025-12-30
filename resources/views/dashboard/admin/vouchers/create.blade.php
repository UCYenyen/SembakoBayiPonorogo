@extends('layouts.app')
@section('title', 'Create Voucher')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] flex items-center justify-center min-h-[80vh] py-12">
        <div class="w-full max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl font-bold mb-6">Buat Voucher Baru</h1>
                <form action="{{ route('admin.vouchers.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="name" class="block text-sm font-medium mb-2">Nama Voucher *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            placeholder="e.g., Welcome Bonus, Birthday Discount"
                            class="w-full px-4 py-2 border @error('name') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                    </div>

                    <div>
                        <label for="disc_amt" class="block text-sm font-medium mb-2">Jumlah Diskon (Rp) *</label>
                        <input type="number" name="disc_amt" id="disc_amt" value="{{ old('disc_amt') }}" 
                            min="0" required
                            placeholder="e.g., 50000"
                            class="w-full px-4 py-2 border @error('disc_amt') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                    </div>

                    <div>
                        <label for="points_required" class="block text-sm font-medium mb-2">Points Yang Dibutuhkan *</label>
                        <input type="number" name="points_required" id="points_required" value="{{ old('points_required') }}" 
                            min="0" required
                            placeholder="e.g., 100"
                            class="w-full px-4 py-2 border @error('points_required') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit"
                            class="flex-1 bg-[#3F3142] text-white px-6 py-3 rounded-lg hover:bg-[#5C4B5E] transition-colors font-semibold">
                            Buat Voucher
                        </button>
                        <a href="{{ route('admin.vouchers.index') }}"
                            class="flex-1 bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors font-semibold text-center">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection