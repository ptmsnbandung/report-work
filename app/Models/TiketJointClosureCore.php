<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TiketJointClosureCore extends Model
{
    use HasFactory;

    protected $table = 'tiket_joint_closure_cores';

    protected $fillable = [
        'id_joint_closure',
        'tube_asal',
        'core_asal',
        'tube_tujuan',
        'core_tujuan',
        'status_core',
        'keterangan',
    ];

    public function jointClosure(): BelongsTo
    {
        return $this->belongsTo(TiketJointClosure::class, 'id_joint_closure');
    }

    /**
     * Badge CSS class untuk status core
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status_core) {
            'TERHUBUNG' => 'bg-success text-white',
            'SPARE' => 'bg-info bg-opacity-15 text-info border border-info border-opacity-25',
            'LOSS_PUTUS' => 'bg-danger text-white',
            'MANUVER' => 'bg-warning text-dark',
            default => 'bg-secondary text-white',
        };
    }
}
