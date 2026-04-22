<?php

namespace App\Http\Requests;

use App\Rules\GoogleMapsLinkOrText;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitPublicBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:120'],
            'service_id' => ['required', 'integer'],
            'service_location' => ['required', Rule::in(['home_service', 'studio'])],
            'people_count' => ['required', 'integer', 'min:1', 'max:20'],
            'booking_date' => ['required', 'date'],
            'booking_time' => ['required', 'date_format:H:i'],
            'location' => ['nullable', 'required_if:service_location,home_service', 'string', 'max:500', new GoogleMapsLinkOrText()],
            'notes' => ['nullable', 'string'],
        ];
    }
}
