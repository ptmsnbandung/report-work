<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKronologisRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole(['admin', 'helpdesk', 'teknis']);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (empty($this->kategori)) {
            $this->merge([
                'kategori' => 'LAIN',
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'kategori'  => ['nullable', 'string', 'in:IZIN,OTDR,TRACING,MATERIAL,JOINTING,LINK_UP,SELESAI,LAIN'],
            'informasi' => ['required', 'string', 'max:5000'],
            'timestamp' => ['nullable', 'date'],
            'foto'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:25600'], // max 25MB (client compresses automatically)
            'latitude'  => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'kategori.required' => 'Kategori kronologis wajib dipilih.',
            'kategori.in'       => 'Kategori kronologis tidak valid.',
            'informasi.required'=> 'Informasi update lapangan wajib diisi.',
            'informasi.max'     => 'Informasi maksimal 5000 karakter.',
            'foto.image'        => 'File yang diupload harus berupa gambar.',
            'foto.mimes'        => 'Format foto harus berupa JPEG, PNG, JPG, atau WEBP.',
            'foto.max'          => 'Ukuran foto maksimal 25 MB.',
            'latitude.numeric'  => 'Format latitude harus berupa angka koordinat.',
            'longitude.numeric' => 'Format longitude harus berupa angka koordinat.',
        ];
    }
}
