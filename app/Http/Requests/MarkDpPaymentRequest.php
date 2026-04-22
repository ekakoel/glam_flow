<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkDpPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $raw = $this->input('dp_amount');
        if (is_string($raw)) {
            $normalized = preg_replace('/[^\d.,]/', '', $raw);
            $normalized = str_replace('.', '', (string) $normalized);
            $normalized = str_replace(',', '.', (string) $normalized);
            $this->merge([
                'dp_amount' => $normalized,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'dp_amount' => ['nullable', 'numeric', 'gt:0'],
        ];
    }
}

