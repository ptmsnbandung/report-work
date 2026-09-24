<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResumeRequest extends FormRequest
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
            'team_om'            => ['required'],
            'problem_temuan'     => ['required', 'string', 'max:5000'],
            'action'             => ['required', 'string', 'max:5000'],
            'tipe_penanganan'    => ['nullable', 'string', 'in:JOINTING_LURUS,MANUVER_CORE,LAINNYA'],
            'joint_closure_type' => ['nullable', 'string', 'max:255'],
            'core_count_jointed' => ['nullable', 'integer', 'min:0'],
            'catatan_tambahan'   => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'team_om.required'        => 'Team OM / Daftar teknisi pelaksana wajib diisi.',
            'problem_temuan.required' => 'Problem / Temuan masalah wajib diisi.',
            'problem_temuan.max'      => 'Problem / Temuan maksimal 5000 karakter.',
            'action.required'         => 'Action / Tindakan perbaikan wajib diisi.',
            'action.max'              => 'Action maksimal 5000 karakter.',
        ];
    }
}
