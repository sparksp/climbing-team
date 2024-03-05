<?php

namespace App\Listeners;

use App\Enums\AttendeeStatus;
use App\Events\BookingChanged;
use App\Mail\BookingChanged as MailBookingChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendBookingChangedEmail
{
    /**
     * Handle the event.
     */
    public function handle(BookingChanged $event): void
    {
        foreach ($event->booking->attendees as $attendee) {
            if ($attendee->attendance->status !== AttendeeStatus::Declined) {
                Mail::to($attendee->email)
                    ->send(new MailBookingChanged(
                        $event->booking,
                        $attendee,
                        $event->changes,
                    ));
            }
        }
    }
}
