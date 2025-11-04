        <!-- Region -->
        <div class="mt-4">
            <x-input-label for="region" :value="__('Region')" />
            <x-text-input id="region" class="block mt-1 w-full" type="text" name="region" :value="old('region')" required />
            <x-input-error :messages="$errors->get('region')" class="mt-2" />
        </div>

        <!-- Province -->
        <div class="mt-4">
            <x-input-label for="province" :value="__('Province')" />
            <x-text-input id="province" class="block mt-1 w-full" type="text" name="province" :value="old('province')" required />
            <x-input-error :messages="$errors->get('province')" class="mt-2" />
        </div>

        <!-- City -->
        <div class="mt-4">
            <x-input-label for="city" :value="__('City/Municipality')" />
            <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city')" required />
            <x-input-error :messages="$errors->get('city')" class="mt-2" />
        </div>

        <!-- Barangay -->
        <div class="mt-4">
            <x-input-label for="barangay" :value="__('Barangay')" />
            <x-text-input id="barangay" class="block mt-1 w-full" type="text" name="barangay" :value="old('barangay')" required />
            <x-input-error :messages="$errors->get('barangay')" class="mt-2" />
        </div>

        <!-- Address -->
        <div class="mt-4">
            <x-input-label for="address" :value="__('Address (Street)')" />
            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" required />
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>
<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf


        <!-- First Name -->
        <div>
            <x-input-label for="first_name" :value="__('First Name')" />
            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus autocomplete="given-name" />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>

        <!-- Last Name -->
        <div class="mt-4">
            <x-input-label for="last_name" :value="__('Last Name')" />
            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autocomplete="family-name" />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
