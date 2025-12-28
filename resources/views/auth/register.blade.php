@extends('layouts.app')
@section('title', 'Register')

@section('content')
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <div
        class="relative min-h-[90vh] sm:min-h-[80vh] w-screen overflow-hidden flex flex-col justify-center items-center py-12">
        <div class="relative z-10 bg-white rounded-lg shadow-lg p-8 md:p-12 w-full max-w-[80%] sm:max-w-md mx-4">
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- Username -->
                <div>
                    <x-input-label for="name" :value="__('Username')" />
                    <x-text-input id="name" class="block mt-1 w-full" placeholder="name" type="text" name="name"
                        :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Phone Number -->
                <div>
                    <x-input-label for="phone_number" :value="__('Phone Number')" />
                    <div class="relative flex items-center">
                        <span
                            class="absolute left-3 text-gray-700 font-medium select-none pointer-events-none z-10">+62</span>

                        <input id="phone_number" name="phone_number" type="tel" value="{{ old('phone_number') }}"
                            required placeholder="812 3456 7890" maxlength="15" inputmode="numeric"
                            class="block w-full pl-14 pr-4 py-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                    </div>
                    <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" placeholder="exampleuser@example.com"
                        type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full" placeholder="**********" type="password"
                        name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation" placeholder="**********" class="block mt-1 w-full"
                        type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Submit Button -->
                <div class="flex flex-col items-center gap-4 mt-6">
                    <button type="submit"
                        class="w-full bg-[#3F3142] shadow-lg rounded-lg text-lg text-white hover:bg-[#5C4B5E] px-4 py-2">
                        Daftar
                    </button>

                    <p class="text-center text-sm text-gray-600">
                        Sudah punya akun?
                        <a href="/login" class="font-semibold text-[#3F3142] hover:underline">
                            Masuk disini
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
        const phoneInput = document.getElementById('phone_number');

        phoneInput.addEventListener('input', function(e) {
            let cursorPosition = this.selectionStart;
            let valueBeforeCursor = this.value.substring(0, cursorPosition);

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

            let spacesBeforeCursor = (valueBeforeCursor.match(/ /g) || []).length;
            let spacesInFormatted = (formatted.substring(0, cursorPosition).match(/ /g) || []).length;
            let newCursorPosition = cursorPosition + (spacesInFormatted - spacesBeforeCursor);

            this.setSelectionRange(newCursorPosition, newCursorPosition);
        });

        phoneInput.addEventListener('paste', function(e) {
            e.preventDefault();
            let pastedText = (e.clipboardData || window.clipboardData).getData('text');

            pastedText = pastedText.replace(/\D/g, '');
            pastedText = pastedText.replace(/^0+/, '');
            pastedText = pastedText.replace(/^62/, '');
            pastedText = pastedText.substring(0, 12);

            let formatted = '';
            if (pastedText.length > 0) {
                formatted = pastedText.substring(0, 3);
                if (pastedText.length > 3) {
                    formatted += ' ' + pastedText.substring(3, 7);
                }
                if (pastedText.length > 7) {
                    formatted += ' ' + pastedText.substring(7);
                }
            }

            this.value = formatted;

            this.dispatchEvent(new Event('input', {
                bubbles: true
            }));
        });

        phoneInput.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft' && this.selectionStart === 0) {
                e.preventDefault();
            }
            if (e.key === 'Home') {
                e.preventDefault();
                this.setSelectionRange(0, 0);
            }
        });

        phoneInput.addEventListener('mousedown', function(e) {
            if (this.selectionStart < 0) {
                setTimeout(() => {
                    this.setSelectionRange(0, 0);
                }, 0);
            }
        });

        phoneInput.addEventListener('input', function() {
            this.setCustomValidity('');
        });
    </script>
@endsection
