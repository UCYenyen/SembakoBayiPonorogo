@extends('layouts.app')
@section('title', 'Complete Your Profile')

@section('content')
    <div class="relative min-h-[80vh] w-screen overflow-hidden flex flex-col justify-center items-center">
        <div class="bg-white rounded-lg shadow-lg p-8 md:p-12 max-w-md w-full mx-4">
            <div class="text-center mb-6">
                <svg class="w-20 h-20 text-[#3F3142] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <h2 class="text-2xl font-bold text-[#3F3142] mb-2">Complete Your Profile</h2>
                <p class="text-sm text-gray-600">Please add your phone number to continue</p>
            </div>

            <form method="POST" action="{{ route('profile.complete.store') }}" class="space-y-4">
                @csrf

                <div>
                    <x-input-label for="phone_number" :value="__('Phone Number')" />
                    <div class="relative flex items-center">
                        <span class="absolute left-3 text-gray-700 font-medium select-none pointer-events-none z-10">+62</span>
                        <input 
                            id="phone_number" 
                            name="phone_number" 
                            type="tel" 
                            value="{{ old('phone_number') }}" 
                            required 
                            placeholder="812 3456 7890" 
                            maxlength="15"
                            inputmode="numeric"
                            class="block w-full pl-14 pr-4 py-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                </div>

                <button type="submit" class="w-full bg-[#3F3142] text-white px-6 py-3 rounded-lg hover:bg-[#5C4B5E] transition-colors font-semibold">
                    Complete Profile
                </button>
            </form>
        </div>
    </div>

    <script>
        const phoneInput = document.getElementById('phone_number');

        phoneInput.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            value = value.replace(/^0+/, '');
            value = value.replace(/^62/, '');
            value = value.substring(0, 12);
            
            let formatted = '';
            if (value.length > 0) {
                formatted = value.substring(0, 3);
                if (value.length > 3) {
                    formatted += ' ' + value.substring(3, 7);
                }
                if (value.length > 7) {
                    formatted += ' ' + value.substring(7);
                }
            }
            this.value = formatted;
        });
    </script>
@endsection