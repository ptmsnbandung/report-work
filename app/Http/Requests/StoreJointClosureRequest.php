<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJointClosureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->role, ['admin', 'helpdesk', 'teknis']);
    }

    public function rules(): array
    {
        return [
            'nama_closure' => 'required|string|max:100',
            'tipe_closure' => 'nullable|in:DOME,INLINE,BOX_FAT,OTB,CLOSURE_STANDAR',
            'jenis_closure' => 'nullable|in:DOME,INLINE,BOX_FAT,OTB,CLOSURE_STANDAR',
            'status_aset' => 'nullable|in:EKSISTING,ASET_BARU',
            'is_aset_baru' => 'nullable|boolean',
            'kapasitas_kabel_asal' => 'required|integer|in:2,4,6,8,12,24,48,96,144,288',
            'jumlah_tube_asal' => 'nullable|integer|min:1|max:48',
            'kapasitas_kabel_jumper' => 'required|integer|in:2,4,6,8,12,24,48,96,144,288',
            'jumlah_tube_jumper' => 'nullable|integer|min:1|max:48',
            'jenis_sambungan' => 'nullable|in:LURUS_STRAIGHT,PERCABANGAN_BRANCH,LOOP_MANUVER',
            'lokasi_penempatan' => 'nullable|string|max:150',
            'lokasi_fisik' => 'nullable|string|max:150',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'catatan' => 'nullable|string|max:1000',
            'keterangan' => 'nullable|string|max:1000',
            'cores' => 'nullable|array',
            'tube_asal' => 'nullable|array',
            'core_asal' => 'nullable|array',
            'tube_jumper' => 'nullable|array',
            'core_jumper' => 'nullable|array',
            'core_status' => 'nullable|array',
            'loss_db' => 'nullable|array',
            'core_keterangan' => 'nullable|array',
        ];
    }
}
