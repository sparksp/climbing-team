@use('App\Enums\BookingStatus')
<x-layout.app :title="__('Edit Booking')">
    <section class="p-4 sm:p-8"
        x-data='{
        booking: {
            cancelled: {{ $booking->isCancelled() ? 'true' : 'false' }},
        },
        updateCancelled(ev) {
            this.booking.cancelled = !ev.target.checked;
            if (this.booking.cancelled) $refs.form.reset();
        }
    }'>
        <header>
            <h2 class="text-3xl font-medium text-gray-900 dark:text-gray-100 flex flex-wrap gap-2">
                <span x-text="booking.activity || 'Booking'"></span>
                -
                <span x-text="dateString(booking.start_date)"></span>
            </h2>
        </header>

        <div class="md:flex md:space-x-4">
            <div class="space-y-1 max-w-xl flex-grow">
                <p
                    class="text-lg text-gray-800 dark:text-gray-200 border-b border-gray-800 dark:border-gray-200 my-2 flex items-center justify-between max-w-xl">
                    <span class="flex items-center">
                        <x-icon.location class="h-5 w-5 fill-current mr-1" />
                        <span x-text="booking.location"></span>
                    </span>
                    <x-badge.booking-status :status="$booking->status" class="text-sm" />
                </p>

                <form method="post" action="{{ route('booking.update', $booking) }}" id="update-booking" x-ref="form"
                    class="space-y-6 max-w-xl mb-6">
                    @csrf
                    @method('PATCH')

                    @if ($booking->isCancelled())
                        <div class="space-y-1">
                            <span class="font-bold after:content-[':']">{{ __('Restore Booking') }}</span>
                            <p>
                                {{ __('This booking has been cancelled. If you restore this booking you will need to find instructors and confirm the booking again. If you do not want to invite any of the previous attendees you should remove them from the booking first.') }}
                            </p>
                            <label class="mt-1 w-full flex space-x-1 items-center">
                                <input type="checkbox" id="status" name="status"
                                    value="{{ BookingStatus::Tentative }}" x-model.fill="booking.status"
                                    @change="updateCancelled" />
                                <span>{{ __('Restore booking') }}</span>
                            </label>
                            <x-input-error :messages="$errors->get('status')" />
                        </div>
                    @elseif ($booking->isTentative())
                        <div class="space-y-1">
                            <span class="font-bold after:content-[':']">{{ __('Confirm Booking') }}</span>
                            @if ($instructors_attending->isEmpty() && !auth()->user()->isTeamLeader())
                                <p>
                                    {{ __('You can only confirm a booking when there is an instructor attending.') }}
                                </p>
                            @else
                                <p>
                                    {{ __('Before you confirm this booking you should ensure that there are enough instructors attending. It is also recommended that you have chosen a ') }}
                                    <a href="#lead_instructor_id">{{ __('Lead Instructor') }}</a>.
                                </p>
                                <label class="mt-1 w-full flex space-x-1 items-center">
                                    <input type="checkbox" id="status" name="status"
                                        value="{{ BookingStatus::Confirmed }}" />
                                    <span>{{ __('Confirm booking') }}</span>
                                </label>
                            @endif
                        </div>
                    @else
                        {{-- Booking is confirmed --}}
                        <div class="space-y-1">
                            <span class="font-bold after:content-[':']">{{ __('Confirm Booking') }}</span>
                            <label class="mt-1 w-full flex space-x-1 items-center">
                                <input type="checkbox" name="_status" checked disabled required />
                                <span>{{ __('This booking has been confirmed.') }}</span>
                            </label>
                        </div>
                    @endif
                    <div class="flex gap-6" x-data="{
                        start_time: '',
                        end_time: '',
                        duration: 0,
                    
                        timeToMinutes(timeString) {
                            var time = timeString.match(/^([012]\d)[:](\d\d)/i);
                            if (time == null) return 0;
                    
                            return ((parseInt(time[1], 10) || 0) * 60) +
                                (parseInt(time[2], 10) || 0);
                        },
                        minutesToTime(minutes) {
                            return (new String(Math.floor(minutes / 60))).padStart(2, '0') + ':' +
                                (new String(minutes % 60)).padStart(2, '0');
                        },
                        syncEndTime() {
                            var endMinutes = this.timeToMinutes(this.start_time) + this.duration;
                            if (endMinutes > 1440) {
                                this.end_time = '23:59';
                            } else {
                                this.end_time = this.minutesToTime(endMinutes);
                            }
                        },
                        syncDuration() {
                            this.duration = this.timeToMinutes(this.end_time) - this.timeToMinutes(this.start_time);
                            if (this.duration < 0) {
                                this.end_time = this.start_time;
                                this.duration = 0;
                            }
                        },
                    
                        init() {
                            $nextTick(() => { this.syncDuration() });
                        }
                    }">
                        <div class="space-y-1">
                            <x-input-label for="start_date" :value="__('Date')" />
                            <x-text-input id="start_date" name="start_date" type="date" :value="old('start_date', $booking->start_date)"
                                placeholder="yyyy-mm-dd" required x-model.fill="booking.start_date"
                                x-bind:disabled="booking.cancelled" />
                            <x-input-error :messages="$errors->get('start_date')" />
                        </div>

                        <div class="space-y-1">
                            <x-input-label for="start_time" :value="__('Start')" />
                            <x-text-input id="start_time" name="start_time" type="time" step="60"
                                :value="old('start_time', $booking->start_time)" placeholder="hh:mm" required x-model.fill="start_time"
                                @change="syncEndTime" x-bind:disabled="booking.cancelled" />
                            <x-input-error :messages="$errors->get('start_time')" />
                        </div>

                        <div class="space-y-1">
                            <x-input-label for="end_time" :value="__('End')" />
                            <x-text-input id="end_time" name="end_time" type="time" step="60"
                                :value="old('end_time', $booking->end_time)" placeholder="hh:mm" required x-model.fill="end_time"
                                @blur="syncDuration" x-bind:disabled="booking.cancelled" />
                            <x-input-error :messages="$errors->get('end_time')" />
                        </div>
                    </div>

                    <div class="space-y-1">
                        <x-input-label for="location" :value="__('Location')" />
                        <x-text-input id="location" name="location" type="text" class="block w-full"
                            :value="old('location', $booking->location)" maxlength="255" required x-model.fill='booking.location'
                            x-bind:disabled="booking.cancelled" />
                        <x-input-error :messages="$errors->get('location')" />
                    </div>

                    <div class="space-y-1">
                        @if ($instructors_attending->isEmpty())
                            <p class="block not-italic font-bold after:content-[':'] text-gray-900 dark:text-gray-100">
                                {{ __('Lead Instructor') }}</p>
                            <p>{{ __('No instructors are going to this booking yet.') }}</p>
                        @elseif ($booking->isCancelled())
                            <p class="block not-italic font-bold after:content-[':'] text-gray-900 dark:text-gray-100">
                                {{ __('Lead Instructor') }}</p>
                            <div
                                class="form-input border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                {{ __('No lead instructor') }}
                            </div>
                        @else
                            <x-input-label for="lead_instructor_id" :value="__('Lead Instructor')" />
                            <x-select-input id="lead_instructor_id" name="lead_instructor_id" class="mt-1 block"
                                x-model.fill="booking.lead_instructor_id" :value="$booking->lead_instructor_id">
                                @if (is_null($booking->lead_instructor) || $booking->isTentative())
                                    <option value="" @selected(is_null($booking->lead_instructor))>{{ __('No lead instructor') }}
                                    </option>
                                @endif
                                <optgroup label="{{ __('Permit Holders') }}">
                                    <x-select-input.collection :options="$instructors_attending" label_key="name" />
                                </optgroup>
                            </x-select-input>
                            <p class="text-sm">
                                {{ __('Someone missing? Only instructors who are going to this booking will appear here.') }}
                            </p>
                        @endif
                        <x-input-error :messages="$errors->get('location')" />
                    </div>

                    <div class="space-y-1">
                        <datalist id="activity-suggestions">
                            @foreach ($activity_suggestions as $activity)
                                <option>{{ $activity }}</option>
                            @endforeach
                        </datalist>
                        <x-input-label for="activity" :value="__('Activity')" />
                        <x-text-input id="activity" name="activity" type="text" class="block w-full"
                            :value="old('activity', $booking->activity)" maxlength="255" required autocomplete="on" list="activity-suggestions"
                            x-model.fill="booking.activity" x-bind:disabled="booking.cancelled" />
                        <x-input-error :messages="$errors->get('activity')" />
                    </div>

                    <div class="space-y-1">
                        <x-input-label for="group_name" :value="__('Group Name')" />
                        <x-text-input id="group_name" name="group_name" type="text" class="block w-full"
                            :value="old('group_name', $booking->group_name)" maxlength="255" required x-bind:disabled="booking.cancelled" />
                        <x-input-error :messages="$errors->get('group_name')" />
                    </div>

                    <div class="space-y-1">
                        <x-input-label for="notes" :value="__('Notes')" />
                        <x-textarea id="notes" name="notes" class="block w-full" :value="old('notes', $booking->notes)"
                            x-bind:disabled="booking.cancelled" />
                        <x-input-error :messages="$errors->get('notes')" />
                    </div>
                </form>
            </div>

            @include('booking.partials.guest-list', ['showTools' => false])
        </div>

        <footer class="flex items-center gap-4 mt-6">
            <x-button.primary form="update-booking" x-bind:disabled="booking.cancelled">
                {{ __('Update') }}
            </x-button.primary>
            @include('booking.partials.delete-button')
            <x-button.secondary :href="route('booking.show', $booking)">
                {{ __('Back') }}
            </x-button.secondary>
        </footer>
    </section>
</x-layout.app>
