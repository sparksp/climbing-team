<x-mail::message>

# {{ $title }}

@if (!empty($changed_summary))
<x-mail::panel>
**@lang('This booking has been updated')**<br>
**@lang('Changes'):** {{ $changed_summary }}.
</x-mail::panel>
@endif

@if ($status_changed != '')
**@lang('Status')**{{ $status_changed }}<br>
@lang("app.booking.status.{$booking->status->value}")
@endif
{{-- new line --}}

**@lang('When')**{{ $when_changed }}<br>
{{ $when }}

**@lang('Location')**{{ $location_changed }}<br>
{{ $booking->location }}

**@lang('Activity')**{{ $activity_changed }}<br>
{{ $booking->activity }}

@isset($booking->lead_instructor)
**@lang('Lead Instructor')**{{ $lead_instructor_changed }}<br>
{{ $booking->lead_instructor->name }}
@endisset
{{-- new line --}}

**@lang('Group')**{{ $group_changed }}<br>
{{ $booking->group_name }}

@if ($booking->notes)
<x-markdown>
**@lang('Notes')**{{ $notes_changed }}<br>
{{ $booking->notes }}
</x-markdown>
@endif

<x-mail::panel>
<x-mail::center>
## @lang('Can you attend this event?')
</x-mail::center>

<x-mail::button-group>
<x-mail::button-group.button :url="$accept_url" color="success">
@lang('Yes')
</x-mail::button-group.button>
<x-mail::button-group.button :url="$decline_url" color="error">
@lang('No')
</x-mail::button-group.button>
<x-mail::button-group.button :url="$tentative_url">
@lang('Maybe')
</x-mail::button-group.button>
</x-mail::button-group>
</x-mail::panel>

@lang('Thanks,')<br>
{{ config('app.name') }}
</x-mail::message>
