@use('App\Models\Attendance')
@use('Illuminate\Contracts\Auth\Access\Gate')
@props(['booking', 'attendees' => collect([]), 'attendance' => null, 'showTools' => true])
<aside class="my-2 flex-grow flex-shrink basis-80 max-w-xl">
    <h2 class="text-xl font-semibold border-b border-gray-800 dark:border-gray-200">
        @lang('Guest list')
    </h2>

    @if ($attendee = $booking->attendees()->find($booking->lead_instructor))
        <h3 class="text-lg">@lang('Lead Instructor')</h3>
        <ul class="mb-3 space-y-1">
            <li class="flex gap-1 items-center">
                @include('booking.partials.guest-list.item')
            </li>
        </ul>
    @endif

    @foreach ($attendees->groupBy('attendance.status') as $status => $attendees)
        <h3 class="text-lg">@lang("app.attendee.status.$status")
            <span class="bg-gray-200 dark:bg-gray-600 dark:text-white px-2 rounded-xl">{{ count($attendees) }}</span>
        </h3>
        <ul class="mb-3 space-y-1">
            @foreach ($attendees as $attendee)
                <li class="flex gap-1 items-center">
                    @include('booking.partials.guest-list.item')
                </li>
            @endforeach
        </ul>
    @endforeach

    @if (!$attendee)
        <p class="my-1">@lang('No one has responded to this booking yet.')</p>
    @endempty

    @if ($showTools)
        <footer class="flex items-start gap-4 mt-4">
            @if (
                $booking->isFuture() &&
                    !$booking->isCancelled() &&
                    app(Gate::class)->check('create', [Attendance::class, $booking]))
                <x-button.primary :href="route('booking.attendee.invite', $booking)">
                    @lang('Invite')
                </x-button.primary>
                <x-button.primary :href="route('booking.attendee.create', $booking)">
                    @lang('Add')
                </x-button.primary>
            @endif
            @include('booking.partials.respond-button')
        </footer>
    @endif
</aside>
