<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManuverCore extends Model
{
    use HasFactory;

    protected $table = 'manuver_core';

    protected $fillable = [
        'id_tiket',
        'lokasi_tipe',
        'titik',
        'core_asal',
        'core_tujuan',
        'core_dialihkan',
        'titik_kembali',
        'tipe',
        'status_manuver',
        'status_core_aset',
        'keterangan',
    ];

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'id_tiket');
    }

    /**
     * Badge CSS class untuk tipe manuver (SEBELUM / SESUDAH)
     */
    public function getTipeBadgeClassAttribute(): string
    {
        return match (strtoupper($this->tipe ?? '')) {
            'SEBELUM' => 'bg-secondary text-white',
            'SESUDAH' => 'bg-success text-white',
            default => 'bg-light text-dark',
        };
    }

    /**
     * Badge CSS class untuk status manuver (TEMPORARY / PERMANENT / RESTORED)
     */
    public function getStatusManuverBadgeClassAttribute(): string
    {
        return match (strtoupper($this->status_manuver ?? 'TEMPORARY')) {
            'TEMPORARY' => 'bg-warning text-dark',
            'PERMANENT' => 'bg-primary text-white',
            'RESTORED' => 'bg-success text-white',
            default => 'bg-secondary text-white',
        };
    }

    /**
     * Badge CSS class untuk status core aset
     */
    public function getStatusCoreAsetBadgeClassAttribute(): string
    {
        return match (strtoupper($this->status_core_aset ?? 'OCCUPIED_MANUVER')) {
            'OCCUPIED_MANUVER' => 'bg-danger text-white',
            'BROKEN_LOSS' => 'bg-dark text-white',
            'SPARE_AVAILABLE' => 'bg-success text-white',
            default => 'bg-secondary text-white',
        };
    }
}
