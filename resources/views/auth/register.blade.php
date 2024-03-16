<x-layout.guest :title="__('Register')">
    <form method="POST" action="{{ route('register') }}" x-data="{
        submitted: false,
        user: {},
        init() {
            $nextTick(() => {
                if (!this.user.timezone || this.user.timezone == 'UTC') {
                    this.user.timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                }
            });
        },
    }"
        x-on:submit="setTimeout(() => submitted = true, 0)">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" x-model.fill="user.name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autocomplete="username" x-model.fill="user.email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Phone Number -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Phone')" />
            <x-text-input id="phone" class="block mt-1 w-40" type="tel" name="phone" :value="old('phone')"
                autocomplete="tel" x-model.fill="user.phone" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-password-input id="password" name="password" class="block mt-1 w-full" required
                autocomplete="new-password" x-model.fill="user.password" />

            <x-password-rules :errors="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-password-input id="password_confirmation" name="password_confirmation" class="block mt-1 w-full" required
                autocomplete="new-password" x-model.fill="user.password_confirmation" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Emergency Contact -->
        <fieldset class="mt-4">
            <legend class="text-lg font-medium">@lang('Emergency Contact')</legend>
            <p class="mb-2 text-md text-blue-800 dark:text-blue-200">@lang('The lead instructor for a booking will be able to access these details should the need arise. If no details are provided then there may be a delay in contacting someone.')</p>
            <div class="mt-4">
                <x-input-label for="emergency_name" :value="__('Name')" />
                <x-text-input id="emergency_name" class="block mt-1 w-full" name="emergency_name" :value="old('emergency_name')"
                    x-bind:required="!!user.emergency_phone" x-model.fill="user.emergency_name" />
                <x-input-error :messages="$errors->get('emergency_name')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="emergency_phone" :value="__('Phone')" />
                <x-text-input id="emergency_phone" class="block mt-1 w-40" type="tel" name="emergency_phone"
                    :value="old('emergency_phone')" x-bind:required="!!user.emergency_name" x-model.fill="user.emergency_phone" />
                <x-input-error :messages="$errors->get('emergency_phone')" class="mt-2" />
            </div>
        </fieldset>

        <!-- Timezone -->
        <div class="mt-4 hidden">
            <x-input-label for="timezone" :value="__('Timezone')" />
            <x-select-input id="timezone" name="timezone" class="mt-1 block" required :value="old('timezone')"
                x-model.fill="user.timezone">
                <x-select-input.timezones />
            </x-select-input>
            <x-input-error class="mt-2" :messages="$errors->get('timezone')" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                href="{{ route('login') }}">
                @lang('Already registered?')
            </a>

            <x-button.primary class="ml-4" x-bind:disabled="submitted"
                x-text="submitted ? '{{ __('Please wait...') }}' : '{{ __('Register') }}'" />
        </div>
    </form>
</x-layout.guest>
