@use('App\Enums\BookingStatus')
<x-layout.app :title="__('Edit Booking')">
    <section class="p-4 sm:p-8 max-w-xl">
        <header>
            <h2 class="text-3xl font-medium text-gray-900 dark:text-gray-100">
                {{ __('Edit Booking') }}
            </h2>
        </header>

        <form method="post" action="{{ route('booking.update', $booking) }}" class="mt-6 space-y-6">
            @csrf
            @method('PATCH')

            <div class="flex space-x-6">
                <div>
                    <x-input-label for="start_date" :value="__('Date')" />
                    <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block" :value="old('start_date', $booking->start_date)"
                        placeholder="yyyy-mm-dd" required />
                    <x-input-error class="mt-2" :messages="$errors->get('start_date')" />
                </div>

                <div>
                    <x-input-label for="start_time" :value="__('Start')" />
                    <x-text-input id="start_time" name="start_time" type="time" class="mt-1 block" step="60"
                        :value="old('start_time', $booking->start_time)" placeholder="hh:mm" required />
                    <x-input-error class="mt-2" :messages="$errors->get('start_time')" />
                </div>

                <div>
                    <x-input-label for="end_time" :value="__('End')" />
                    <x-text-input id="end_time" name="end_time" type="time" class="mt-1 block" step="60"
                        :value="old('end_time', $booking->end_time)" placeholder="hh:mm" required />
                    <x-input-error class="mt-2" :messages="$errors->get('end_time')" />
                </div>
            </div>

            <div>
                <x-input-label for="status" :value="__('Status')" />
                <x-select-input id="status" name="status" class="mt-1 block" required :value="old('status', $booking->status)">
                    <x-select-input.enum :options="BookingStatus::class" lang="booking.status.:value" />
                </x-select-input>
                <x-input-error class="mt-2" :messages="$errors->get('status')" />
            </div>

            <div>
                <x-input-label for="location" :value="__('Location')" />
                <x-text-input id="location" name="location" type="text" class="mt-1 block w-full" :value="old('location', $booking->location)"
                    maxlength="255" required />
                <x-input-error class="mt-2" :messages="$errors->get('location')" />
            </div>

            <div>
                <x-input-label for="activity" :value="__('Activity')" />
                <x-text-input id="activity" name="activity" type="text" class="mt-1 block w-full" :value="old('activity', $booking->activity)"
                    maxlength="255" required />
                <x-input-error class="mt-2" :messages="$errors->get('activity')" />
            </div>

            <div>
                <x-input-label for="group_name" :value="__('Group Name')" />
                <x-text-input id="group_name" name="group_name" type="text" class="mt-1 block w-full"
                    :value="old('group_name', $booking->group_name)" maxlength="255" required />
                <x-input-error class="mt-2" :messages="$errors->get('group_name')" />
            </div>

            <div>
                <x-input-label for="notes" :value="__('Notes')" />
                <x-textarea id="notes" name="notes" class="mt-1 block w-full" :value="old('notes', $booking->notes)" />
                <x-input-error class="mt-2" :messages="$errors->get('notes')" />
            </div>

            <div class="flex items-center gap-4">
                <x-button.primary>
                    {{ __('Update') }}
                </x-button.primary>

                <x-button.secondary :href="route('booking.show', $booking)">
                    {{ __('Back') }}
                </x-button.secondary>
            </div>
        </form>
    </section>
</x-layout.app>
