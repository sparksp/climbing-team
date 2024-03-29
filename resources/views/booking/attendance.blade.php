@use('App\Enums\AttendeeStatus')
@use('Illuminate\Contracts\Auth\Access\Gate')
<x-layout.app :title="__('Edit Attendance')">
    <section class="p-4 sm:px-8">
        @include('booking.partials.header')
        <div class="md:flex md:space-x-4">
            <div class="w-full max-w-xl">
                @include('booking.partials.details')

                @if ($booking->isFuture() && !$booking->isCancelled() && app(Gate::class)->check('respond', [$booking, auth()->user()]))
                    <form method="post" action="{{ route('booking.attendance.update', $booking) }}"
                        x-data="{ submitted: false }" x-on:submit="setTimeout(() => submitted = true, 0)">
                        @csrf
                        @method('PUT')
                        <div
                            class="my-4 space-y-4 p-4 border text-black bg-slate-100 border-slate-400 dark:text-white dark:bg-slate-900 dark:border-slate-600">
                            <p class="text-lg text-center">@lang('Can you attend this event?')</p>
                            <div class="flex justify-center gap-4">
                                <x-button.primary type="submit" name="status" x-bind:disabled="submitted"
                                    :value="AttendeeStatus::Accepted->value">@lang('Yes')</x-button.primary>
                                <x-button.secondary type="submit" name="status" x-bind:disabled="submitted"
                                    :value="AttendeeStatus::Declined->value">@lang('No')</x-button.secondary>
                                <x-button.secondary type="submit" name="status" x-bind:disabled="submitted"
                                    :value="AttendeeStatus::Tentative->value">@lang('Maybe')</x-button.secondary>
                            </div>
                            <p class="text-sm text-center">
                                @lang('Replying for :name.', ['name' => auth()->user()->name])
                            </p>
                        </div>
                    </form>
                @endif
            </div>

            <x-guest-list :$booking :$currentUser :showTools="false" />
        </div>
    </section>
</x-layout.app>
