<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDokumentasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->role, ['admin', 'helpdesk', 'teknis']);
    }

    public function rules(): array
    {
        return [
            'kategori' => ['required', 'string', 'max:100'],
            'timestamp' => ['nullable', 'date'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'source_photo_url' => ['nullable', 'string'],
            'foto' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:25600'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:25600'],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori.required' => 'Kategori dokumentasi wajib dipilih/diisi.',
            'foto.max' => 'Ukuran foto maksimal 25 MB.',
            'foto.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WebP.',
            'photos.*.max' => 'Ukuran file foto maksimal 25 MB per foto.',
            'photos.*.mimes' => 'Format file foto harus berupa JPG, JPEG, PNG, atau WebP.',
            'latitude.between' => 'Nilai latitude tidak valid (-90 s/d 90).',
            'longitude.between' => 'Nilai longitude tidak valid (-180 s/d 180).',
        ];
    }
}
