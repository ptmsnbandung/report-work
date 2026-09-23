<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManuverCoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->role, ['admin', 'helpdesk', 'teknis']);
    }

    public function rules(): array
    {
        return [
            'titik' => ['required', 'string', 'max:50'],
            'core_asal' => ['required', 'string', 'max:50'],
            'core_tujuan' => ['required', 'string', 'max:50'],
            'tipe' => ['required', 'string', 'in:SEBELUM,SESUDAH'],
        ];
    }

    public function messages(): array
    {
        return [
            'titik.required' => 'Nama titik (contoh: JC1, JC2) wajib diisi.',
            'core_asal.required' => 'Core asal (contoh: Tube 2 Core 1) wajib diisi.',
            'core_tujuan.required' => 'Core tujuan (contoh: Tube 2 Core 1) wajib diisi.',
            'tipe.required' => 'Tipe manuver (SEBELUM / SESUDAH) wajib dipilih.',
            'tipe.in' => 'Tipe manuver hanya boleh SEBELUM atau SESUDAH.',
        ];
    }
}
