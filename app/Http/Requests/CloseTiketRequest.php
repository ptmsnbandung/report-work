<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CloseTiketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole(['admin', 'helpdesk']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'tanggal_close'   => ['nullable', 'date'],
            'catatan_closing' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tanggal_close.date' => 'Format waktu penutupan tiket tidak valid.',
        ];
    }
}
