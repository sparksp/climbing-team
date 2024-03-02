@use('App\Enums\AttendeeStatus')
<x-layout.app :title="__('Add Attendee')">
    <section class="p-4 sm:p-8">
        @include('booking.partials.header')

        <div class="md:flex md:space-x-4">
            @include('booking.partials.details')

            <div class="flex-grow my-2">
                @if ($users->isNotEmpty())
                    <form method="post" action="{{ route('booking.attendee.store', $booking) }}">
                        @csrf
                        <h3 class="text-xl font-semibold border-b border-gray-800 dark:border-gray-200 w-full">
                            {{ __('Attendance') }}</h3>

                        <div class="space-y-1 my-1">
                            <div>
                                <x-input-label for="user_id" :value="__('Attendee')" />
                                <x-select-input id="user_id" name="user_id" class="mt-1 block" required
                                    :value="old('user_id')">
                                    <option value="" disabled selected>{{ __('-- Select User --') }}</option>
                                    <x-select-input.collection :options="$users" label_key="name" />
                                </x-select-input>
                                <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
                            </div>

                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <x-select-input id="status" name="status" class="mt-1 block" required
                                    :value="old('status', AttendeeStatus::Accepted)">
                                    <x-select-input.enum :options="AttendeeStatus::class" :except="AttendeeStatus::NeedsAction"
                                        lang="app.attendee.status.:value" />
                                </x-select-input>
                                <x-input-error class="mt-2" :messages="$errors->get('status')" />
                            </div>
                        </div>

                        <footer class="flex items-center gap-4 mt-4">
                            <x-button.primary>
                                {{ __('Add Attendee') }}
                            </x-button.primary>

                            <x-button.secondary :href="route('booking.show', $booking)">
                                {{ __('Back') }}
                            </x-button.secondary>
                        </footer>
                    </form>
                @else
                    <h3 class="text-xl font-semibold border-b border-gray-800 dark:border-gray-200 w-full">
                        {{ __('Attendance') }}</h3>
                    <p class="my-1">
                        {{ __('All users have already been invited to this booking. You may change their response on the guest list.') }}
                    </p>
                    <footer class="flex items-center gap-4 pt-2">
                        <x-button.secondary :href="route('booking.show', $booking)">
                            {{ __('Back') }}
                        </x-button.secondary>
                    </footer>
                @endif
            </div>
        </div>
    </section>
</x-layout.app>
