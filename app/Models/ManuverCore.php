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
        'titik',
        'core_asal',
        'core_tujuan',
        'tipe',
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
        return match (strtoupper($this->tipe)) {
            'SEBELUM' => 'bg-secondary text-white',
            'SESUDAH' => 'bg-success text-white',
            default => 'bg-light text-dark',
        };
    }
}
