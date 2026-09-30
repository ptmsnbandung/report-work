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
        $isBroadband = $this->input('kategori_tiket') === 'BROADBAND';

        return [
            'kategori_tiket'          => ['nullable', 'string', 'in:BACKBONE,BROADBAND'],
            'wilayah'                 => [$isBroadband ? 'required' : 'nullable', 'string', 'in:Bandung,Soreang,BANDUNG,SOREANG'],
            'status_link_impact'      => [$isBroadband ? 'nullable' : 'required', 'string', 'max:150'],
            'backbone_segment'        => [$isBroadband ? 'nullable' : 'required', 'string', 'max:100'],
            'tanggal_open'            => ['required', 'date'],
            'sla_target_minutes'      => ['nullable', 'integer', 'min:1'],
            'deskripsi'               => ['nullable', 'string', 'max:5000'],

            // Broadband specific fields
            'id_pelanggan'            => [$isBroadband ? 'required' : 'nullable', 'string', 'max:50'],
            'nama_pelanggan'          => [$isBroadband ? 'required' : 'nullable', 'string', 'max:200'],
            'no_kontak_pelanggan'     => ['nullable', 'string', 'max:50'],
            'alamat_pelanggan'        => ['nullable', 'string', 'max:1000'],
            'titik_odp'               => ['nullable', 'string', 'max:100'],
            'kode_bandwith'           => ['nullable', 'string', 'max:50'],
            'kode_pop'                => ['nullable', 'string', 'max:50'],
            'lon_lat_pelanggan'       => ['nullable', 'string', 'max:200'],
            'sn_ont'                  => ['nullable', 'string', 'max:100'],
            'jenis_kendala_broadband' => ['nullable', 'string', 'max:100'],
            'redaman_sebelum'         => ['nullable', 'string', 'max:20'],
            'redaman_sesudah'         => ['nullable', 'string', 'max:20'],
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
            'id_pelanggan.required'       => 'ID Pelanggan (CID) wajib diisi untuk tiket Broadband.',
            'nama_pelanggan.required'     => 'Nama pelanggan wajib diisi untuk tiket Broadband.',
            'tanggal_open.required'       => 'Waktu open tiket wajib diisi.',
            'tanggal_open.date'           => 'Format waktu open tiket tidak valid.',
            'sla_target_minutes.integer'  => 'Target SLA harus berupa angka menit.',
            'sla_target_minutes.min'      => 'Target SLA minimal 1 menit.',
        ];
    }
}
