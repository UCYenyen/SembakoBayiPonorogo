@extends('layouts.app')
@section('title', 'Register')

@section('content')
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <div class="relative min-h-screen w-screen overflow-hidden flex flex-col justify-center items-center py-12">
        <div class="relative z-10 bg-white rounded-lg shadow-lg p-8 md:p-12 w-full max-w-[90%] sm:max-w-md mx-4">
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- Username -->
                <div>
                    <x-input-label for="name" :value="__('Username')" />
                    <x-text-input id="name" class="block mt-1 w-full" placeholder="name" type="text" name="name" :value="old('name')"
                        required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Phone Number -->
                <div>
                    <x-input-label for="phone_number" :value="__('Phone Number')" />
                    <x-text-input id="phone_number" class="block mt-1 w-full" type="tel" name="phone_number"
                        :value="old('phone_number')" required placeholder="+62 XXX XXXX XXXX" pattern="[0-9+\-\s]*"
                        inputmode="numeric" />
                    <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" placeholder="exampleuser@example.com" type="email" name="email" :value="old('email')"
                        required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full" placeholder="**********" type="password" name="password" required
                        autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation" placeholder="**********" class="block mt-1 w-full" type="password"
                        name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Submit Button -->
                <div class="flex flex-col items-center gap-4 mt-6">
                    <Button type="submit"
                        class="w-full bg-interactible-primary-active text-white hover:text-interactible-primary-active font-semibold py-2 rounded-lg hover:bg-zinc-50 border border-interactible-primary-active">Register
                    </Button>


                    <p class="text-center text-sm text-gray-600">
                       Already have an account?
                        <a href="/login"
                            class="font-semibold text-interactible-primary-active hover:underline">
                           Login here
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Prevent non-numeric input on phone number field
        document.getElementById('phone_number').addEventListener('input', function(e) {
            // Allow only numbers, +, -, and spaces
            this.value = this.value.replace(/[^0-9+\-\s]/g, '');
        });

        // Prevent paste of non-numeric content
        document.getElementById('phone_number').addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const cleanedText = pastedText.replace(/[^0-9+\-\s]/g, '');
            this.value = cleanedText;
        });
    </script>
@endsection
