<?php

namespace App\Http\Requests;

use App\Rules\GoogleMapsLinkOrText;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'notify_tomorrow_booking' => $this->boolean('notify_tomorrow_booking'),
            'remove_logo' => $this->boolean('remove_logo'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'studio_name' => ['nullable', 'string', 'max:120'],
            'studio_location' => ['nullable', 'string', 'max:255'],
            'studio_maps_link' => ['nullable', 'url', 'max:500', new GoogleMapsLinkOrText()],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_logo' => ['required', 'boolean'],
            'payment_bank_name' => ['nullable', 'string', 'max:120'],
            'payment_account_name' => ['nullable', 'string', 'max:120'],
            'payment_account_number' => ['nullable', 'string', 'max:80'],
            'payment_contact' => ['nullable', 'string', 'max:120'],
            'payment_instructions' => ['nullable', 'string', 'max:2000'],
            'payment_accounts' => ['nullable', 'array', 'max:5'],
            'payment_accounts.*.bank_name' => ['nullable', 'string', 'max:120'],
            'payment_accounts.*.account_number' => ['nullable', 'string', 'max:80'],
            'payment_accounts.*.account_name' => ['nullable', 'string', 'max:120'],
            'payment_accounts.*.contact' => ['nullable', 'string', 'max:120'],
            'payment_accounts.*.notes' => ['nullable', 'string', 'max:255'],
            'primary_account_index' => ['nullable', 'integer', 'min:0', 'max:4'],
            'notify_tomorrow_booking' => ['required', 'boolean'],
        ];
    }
}
