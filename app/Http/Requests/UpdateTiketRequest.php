<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTiketRequest extends FormRequest
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
            'status_link_impact' => ['required', 'string', 'max:150'],
            'backbone_segment'   => ['required', 'string', 'max:100'],
            'tanggal_open'       => ['required', 'date'],
            'sla_target_minutes' => ['nullable', 'integer', 'min:1'],
            'deskripsi'          => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * Custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status_link_impact.required' => 'Status link impact wajib diisi.',
            'status_link_impact.max'      => 'Status link impact maksimal 150 karakter.',
            'backbone_segment.required'   => 'Segment backbone wajib dipilih.',
            'tanggal_open.required'       => 'Waktu open tiket wajib diisi.',
            'tanggal_open.date'           => 'Format waktu open tiket tidak valid.',
            'sla_target_minutes.integer'  => 'Target SLA harus berupa angka menit.',
            'sla_target_minutes.min'      => 'Target SLA minimal 1 menit.',
        ];
    }
}
