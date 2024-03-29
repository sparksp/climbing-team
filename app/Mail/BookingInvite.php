<?php

namespace App\Mail;

use App\iCal\Domain\Enum\CalendarMethod;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

class BookingInvite extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Booking $booking,
        public User $attendee,
        public array $changes = [],
    ) {
        $this->attendee = $booking->attendees()->find($attendee);
    }

    /**
     * The subject line for the email.
     *
     * Will be translated with `:activity` and `:start` passed in.
     */
    public function getSubject(): string
    {
        return 'Invitation: :activity @ :start';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __(
                $this->getSubject(),
                [
                    'activity' => $this->booking->activity,
                    'start' => localDate($this->booking->start_at)->toFormattedDayDateString(),
                ]
            ),
            replyTo: [
                new Address($this->booking->uid)
            ],
            tags: ['invite'],
            metadata: [
                'booking_id' => $this->booking->id,
            ],
            using: [
                fn (Email $message) => $this->attachCalendarData($message),
            ],
        );
    }

    /**
     * The title line in the body of the email
     */
    public function getTitle(): string
    {
        return 'Invitation';
    }

    /**
     * The label for the call to action button.
     */
    public function getButtonLabel(): string
    {
        return 'Respond';
    }

    /**
     * The URL for the call to action button.
     */
    public function getButtonUrl(): string
    {
        return URL::route('respond', [
            $this->booking, $this->attendee,
            'invite' => $this->attendee->attendance->token,
        ]);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.booking.invite',
            with: array_merge([
                'title' => __($this->getTitle()),
                'changed_summary' => $this->buildChangedSummary(),
                'when' => $this->buildDateString(),
                'button_label' => __($this->getButtonLabel()),
                'button_url' => $this->getButtonUrl(),
            ], $this->buildChangedList())
        );
    }

    protected function buildChangedList(): array
    {
        $label = ' (' . __('changed') . ')';

        $fields = [
            'status',
            'start_at' => 'when',
            'end_at' => 'when',
            'location',
            'activity',
            'lead_instructor_id' => 'lead_instructor',
            'group_name' => 'group',
            'notes',
        ];

        $changed_list = [];

        foreach ($fields as $value) {
            $changed_key = $value . '_changed';
            $changed_list[$changed_key] = '';
        }

        foreach ($fields as $key => $value) {
            $changed_key = $value . '_changed';
            if (array_key_exists($key, $this->changes) || array_key_exists($value, $this->changes)) {
                $changed_list[$changed_key] = $label;
            }
        }

        return $changed_list;
    }

    protected function buildChangedSummary(): string
    {
        $labels = [
            'status' => __('Status'),
            'start_at' => __('When'),
            'end_at' => __('When'),
            'location' => __('Location'),
            'activity' => __('Activity'),
            'lead_instructor_id' => __('Lead Instructor'),
            'group_name' => __('Group'),
            'notes' => __('Notes'),
        ];

        return collect($this->changes)
            ->map(function ($value, $key) use ($labels) {
                if (array_key_exists($key, $labels)) {
                    return $labels[$key];
                }
                return null;
            })
            ->filter()
            ->unique()
            ->join(', ', ' and ');
    }

    protected function buildDateString(): string
    {
        if (localDate($this->booking->start_at)->isSameDay(localDate($this->booking->end_at))) {
            return __(':start_date from :start_time to :end_time', [
                'start_time' => localDate($this->booking->start_at)->format('H:i'),
                'start_date' => localDate($this->booking->start_at)->toFormattedDayDateString(),
                'end_time' => localDate($this->booking->end_at)->format('H:i'),
            ]);
        } else {
            return __(':start to :end', [
                'start' => localDate($this->booking->start_at)->toDayDateTimeString(),
                'end' => localDate($this->booking->end_at)->toDayDateTimeString(),
            ]);
        }
    }

    /**
     * The method for the calendar attachment.
     */
    public function getCalendarMethod(): CalendarMethod
    {
        return CalendarMethod::Request;
    }

    protected function getCalendarMethodAsString(): string
    {
        return match ($this->getCalendarMethod()) {
            CalendarMethod::Add => 'ADD',
            CalendarMethod::Cancel => 'CANCEL',
            CalendarMethod::Counter => 'COUNTER',
            CalendarMethod::DeclineCounter => 'DECLINECOUNTER',
            CalendarMethod::Publish => 'PUBLISH',
            CalendarMethod::Refresh => 'REFRESH',
            CalendarMethod::Reply => 'REPLY',
            CalendarMethod::Request => 'REQUEST',
        };
    }

    protected function attachCalendarData(Email $message): void
    {
        // We need to include the calendar data in two different ways to
        //  satisfy different email & calendar clients. Some expect the data
        //  as an inline 'alternative' part of the email, and others expect it
        //  as an 'attachment'.
        $icsData = $this->buildCalendarData();
        $icsMethod = $this->getCalendarMethodAsString();

        // Ideally the inline data would be an 'alternative' part, before the
        //  HTML, but that requires manually handling all the email parts.
        $icsInline = new DataPart($icsData, filename: 'invite', contentType: 'text/calendar');
        $icsInline->asInline();
        $icsInline->getHeaders()->addParameterizedHeader(
            'Content-Type',
            'text/calendar',
            [
                'method' => $icsMethod,
                'charset' => 'utf-8',
                'component' => 'vevent',
            ]
        );
        $message->addPart($icsInline);

        // The attachment has filenames and is not 'inline'. This could be
        //  handled by the Laravel `attachments` function, but is included here
        //  to keep both representations together in a similar format.
        $icsDownload = new DataPart($icsData, filename: 'invite.ics', contentType: 'text/calendar');
        $icsDownload->getHeaders()->addParameterizedHeader(
            'Content-Type',
            'text/calendar',
            [
                'method' => $icsMethod,
                'charset' => 'utf-8',
                'component' => 'vevent',
                'name' => 'invite.ics',
            ]
        );
        $message->addPart($icsDownload);
    }

    protected function buildCalendarData(): string
    {
        return view('booking.ics', [
            'bookings' => [$this->booking],
            'user' => $this->attendee,
            'method' => $this->getCalendarMethod(),
        ])->render();
    }
}
