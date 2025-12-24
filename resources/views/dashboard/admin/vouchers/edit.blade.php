@extends('layouts.app')
@section('title', 'Edit Voucher')
@section('content')
    <main class="bg-[#FFF3F3] text-[#3F3142] flex items-center justify-center min-h-screen py-12">
        <div class="w-full max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl font-bold mb-6">Edit Voucher</h1>
                
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.vouchers.update', $voucher) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <!-- Voucher Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium mb-2">Voucher Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $voucher->name) }}" required
                            placeholder="e.g., Welcome Bonus, Birthday Discount"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                        @error('name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Discount Amount -->
                    <div>
                        <label for="disc_amt" class="block text-sm font-medium mb-2">Discount Amount (Rp) *</label>
                        <input type="number" name="disc_amt" id="disc_amt" value="{{ old('disc_amt', $voucher->disc_amt) }}" 
                            min="0" required
                            placeholder="e.g., 50000"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                        @error('disc_amt')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Points Required -->
                    <div>
                        <label for="points_required" class="block text-sm font-medium mb-2">Points Required *</label>
                        <input type="number" name="points_required" id="points_required" value="{{ old('points_required', $voucher->points_required) }}" 
                            min="0" required
                            placeholder="e.g., 100"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3F3142] focus:border-transparent">
                        @error('points_required')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800">
                            <strong>Created:</strong> {{ $voucher->created_at->format('d M Y, H:i') }}
                        </p>
                        <p class="text-sm text-blue-800 mt-2">
                            <strong>Last Updated:</strong> {{ $voucher->updated_at->format('d M Y, H:i') }}
                        </p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4 pt-4">
                        <button type="submit"
                            class="flex-1 bg-[#3F3142] text-white px-6 py-3 rounded-lg hover:bg-[#5C4B5E] transition-colors font-semibold">
                            Update Voucher
                        </button>
                        <a href="{{ route('admin.vouchers.index') }}"
                            class="flex-1 bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors font-semibold text-center">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection