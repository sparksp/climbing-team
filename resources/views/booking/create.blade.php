@use('App\Enums\BookingStatus')
<x-layout.app :title="__('Create Booking')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-3xl font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Create Booking') }}
                            </h2>
                        </header>

                        <form method="post" action="{{ route('booking.store') }}" class="mt-6 space-y-6">
                            @csrf

                            <div>
                                <x-input-label for="start_at" :value="__('Start')" />
                                <x-text-input id="start_at" name="start_at" type="datetime-local" class="mt-1 block"
                                    :value="old('start_at', $booking->start_at)" placeholder="yyyy-mm-dd hh:mm:ss" required />
                                <x-input-error class="mt-2" :messages="$errors->get('start_at')" />
                            </div>

                            <div>
                                <x-input-label for="end_at" :value="__('End')" />
                                <x-text-input id="end_at" name="end_at" type="datetime-local" class="mt-1 block"
                                    :value="old('end_at', $booking->end_at)" placeholder="yyyy-mm-dd hh:mm:ss" required />
                                <x-input-error class="mt-2" :messages="$errors->get('end_at')" />
                            </div>

                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <x-select-input id="status" name="status" class="mt-1 block" required
                                    :value="old('status', $booking->status)">
                                    <x-select-input.enum :options="BookingStatus::class" lang="booking.status.:value" />
                                </x-select-input>
                                <x-input-error class="mt-2" :messages="$errors->get('status')" />
                            </div>

                            <div>
                                <x-input-label for="location" :value="__('Location')" />
                                <x-text-input id="location" name="location" type="text" class="mt-1 block w-full"
                                    :value="old('location', $booking->location)" maxlength="255" required />
                                <x-input-error class="mt-2" :messages="$errors->get('location')" />
                            </div>

                            <div>
                                <datalist id="activity-suggestions">
                                    @foreach ($activity_suggestions as $activity)
                                        <option>{{ $activity }}</option>
                                    @endforeach
                                </datalist>
                                <x-input-label for="activity" :value="__('Activity')" />
                                <x-text-input id="activity" name="activity" type="text" class="mt-1 block w-full"
                                    :value="old('activity', $booking->activity)" maxlength="255" required autocomplete="on"
                                    list="activity-suggestions" />
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
                                <x-textarea id="notes" name="notes" class="mt-1 block w-full"
                                    :value="old('notes', $booking->notes)" />
                                <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-button.primary>
                                    {{ __('Create') }}
                                </x-button.primary>

                                <x-button.secondary :href="route('booking.index')">
                                    {{ __('Cancel') }}
                                </x-button.secondary>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
