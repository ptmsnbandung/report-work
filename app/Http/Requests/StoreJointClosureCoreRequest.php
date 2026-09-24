<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJointClosureCoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->role, ['admin', 'helpdesk', 'teknis']);
    }

    public function rules(): array
    {
        return [
            'tube_asal' => 'required|string|max:30',
            'core_asal' => 'required|string|max:30',
            'tube_tujuan' => 'nullable|string|max:30',
            'tube_jumper' => 'nullable|string|max:30',
            'core_tujuan' => 'nullable|string|max:30',
            'core_jumper' => 'nullable|string|max:30',
            'status_core' => 'nullable|in:TERHUBUNG,SPARE,LOSS_PUTUS,MANUVER',
            'status' => 'nullable|in:TERHUBUNG,SPARE,LOSS_PUTUS,MANUVER',
            'loss_db' => 'nullable|numeric|between:0,100',
            'keterangan' => 'nullable|string|max:255',
        ];
    }
}
