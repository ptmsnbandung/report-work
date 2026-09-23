<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequest extends FormRequest
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
            'nama_material' => ['required', 'string', 'max:100'],
            'jumlah'        => ['required', 'integer', 'min:1'],
            'satuan'        => ['required', 'string', 'max:20'],
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'nama_material.required' => 'Nama material wajib diisi.',
            'nama_material.max'      => 'Nama material maksimal 100 karakter.',
            'jumlah.required'        => 'Jumlah material wajib diisi.',
            'jumlah.integer'         => 'Jumlah material harus berupa angka bulat.',
            'jumlah.min'             => 'Jumlah material minimal 1.',
            'satuan.required'        => 'Satuan material wajib diisi (contoh: pcs, meter, unit).',
        ];
    }
}
