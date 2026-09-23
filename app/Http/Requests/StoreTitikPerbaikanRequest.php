<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTitikPerbaikanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole(['admin', 'helpdesk', 'teknis']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nama_titik' => ['required', 'string', 'max:50'],
            'latitude'   => ['required', 'numeric', 'between:-90,90'],
            'longitude'  => ['required', 'numeric', 'between:-180,180'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'nama_titik.required' => 'Nama titik penanganan wajib diisi (contoh: JC1, JC2, Span 12).',
            'latitude.required'   => 'Latitude wajib diisi.',
            'latitude.numeric'    => 'Latitude harus berupa angka desimal.',
            'longitude.required'  => 'Longitude wajib diisi.',
            'longitude.numeric'   => 'Longitude harus berupa angka desimal.',
        ];
    }
}
