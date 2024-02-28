<?php

namespace App\Http\Requests;

use App\Enums\BookingStatus;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'start_date' => ['sometimes', 'required', 'date'],
            'start_time' => ['sometimes', 'required', 'date_format:H:i'],
            'end_time' => ['sometimes', 'required', 'date_format:H:i', 'after:start_time'],
            'location' => ['sometimes', 'required', 'string', 'max:255'],
            'activity' => ['sometimes', 'required', 'string', 'max:255'],
            'group_name' => ['sometimes', 'required', 'string', 'max:255'],
            'notes' => ['sometimes', 'nullable', 'string'],

            // TODO: Restrict only to 'PermitHolder' attendees of the booking.
            'lead_instructor_id' => [
                'exclude_if:status,' . BookingStatus::Cancelled->value,
                'sometimes', 'nullable', 'ulid', Rule::exists(User::class, 'id')
            ],

            // Status is updated in secret ways.
            'status' => ['sometimes', 'required', Rule::enum(BookingStatus::class)],
        ];
    }

    public function attributes(): array
    {
        return [
            'start_date' => __('Date'),
            'start_time' => __('Start'),
            'end_time' => __('End'),
            'location' => __('Location'),
            'activity' => __('Activity'),
            'group_name' => __('Group Name'),
            'notes' => __('Notes'),
            'lead_instructor_id' => __('Lead Instructor'),
            'status' => __('Status'),
        ];
    }
}
