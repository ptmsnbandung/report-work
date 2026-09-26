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
            'foto'      => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,mp4,webm,mov,m4v,3gp,avi', 'max:51200'], // max 50MB
            'video'     => ['nullable', 'file', 'mimes:mp4,webm,mov,m4v,3gp,avi', 'max:51200'],
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
            'foto.file'         => 'File lampiran tidak valid.',
            'foto.mimes'        => 'Format lampiran harus berupa foto (JPEG, PNG, JPG, WEBP) atau video (MP4, WEBM, MOV, 3GP).',
            'foto.max'          => 'Ukuran lampiran maksimal 50 MB.',
            'video.file'        => 'File video tidak valid.',
            'video.mimes'       => 'Format video harus berupa MP4, WEBM, MOV, atau 3GP.',
            'video.max'         => 'Ukuran video maksimal 50 MB.',
            'latitude.numeric'  => 'Format latitude harus berupa angka koordinat.',
            'longitude.numeric' => 'Format longitude harus berupa angka koordinat.',
        ];
    }
}
