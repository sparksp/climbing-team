@use('Illuminate\Contracts\Auth\Access\Gate')
@props(['booking', 'attendance'])
@if ($booking->isFuture() && !$booking->isCancelled() && app(Gate::class)->check('respond', [$booking, auth()->user()]))
    <div class="flex" x-data="{ open: false }">
        <div class="relative">
            <button
                class="flex gap-2 px-4 py-2 border rounded-t-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 disabled:opacity-25"
                :class="{ 'rounded-b-md': !open }" x-on:click="open = true">
                @include('booking.partials.attendance-icon')
                @if (is_null($attendance))
                    {{ __('Respond') }}
                @else
                    {{ __("app.attendee.status.{$attendance->status->value}") }}
                @endif

                <x-icon.cheveron-down class="w-4 h-4 fill-current" x-show="!open" />
                <x-icon.cheveron-up class="w-4 h-4 fill-current" x-show="open" x-cloak />
            </button>

            <div x-show="open" x-cloak x-on:click.outside="open = false"
                class="absolute left-0 -mt-px z-20 shadow-xl border border-gray-300 dark:border-gray-500 min-w-full">
                <form method="post" action="{{ route('booking.attendance.update', $booking) }}">
                    @csrf
                    @method('PUT')
                    @foreach (['accepted', 'tentative', 'declined'] as $status)
                        @if ($status != $attendance?->status->value)
                            <button name="status" value="{{ $status }}"
                                class="flex gap-2 flex-nowrap items-center min-w-full px-4 py-2 text-xs uppercase font-semibold tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 disabled:opacity-25">
                                <x-icon.empty-outline class="h-4 w-4 fill-current" />
                                <span class="flex-grow text-left">{{ __("app.attendee.status.$status") }}</span>
                            </button>
                        @endif
                    @endforeach
                </form>
            </div>
        </div>
    </div>
@elseif ($booking->isCancelled())
    <div
        class="flex gap-2 px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-300 shadow-sm disabled:opacity-25">
        <x-icon.close-outline class="h-4 w-4 fill-current" />
        {{ __('Cancelled') }}
    </div>
@elseif (!is_null($attendance))
    <div
        class="flex gap-2 px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-300 shadow-sm disabled:opacity-25">
        @include('booking.partials.attendance-icon')
        {{ __("app.attendee.status.{$attendance->status->value}") }}
    </div>
@endif
