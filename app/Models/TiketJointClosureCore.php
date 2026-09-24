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
        'joint_closure_id',
        'tube_asal',
        'core_asal',
        'tube_tujuan',
        'tube_jumper',
        'core_tujuan',
        'core_jumper',
        'status_core',
        'status',
        'loss_db',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'loss_db' => 'decimal:2',
        ];
    }

    public function setJointClosureIdAttribute($value): void
    {
        $this->attributes['id_joint_closure'] = $value;
    }

    public function getJointClosureIdAttribute(): ?int
    {
        return $this->id_joint_closure ?? null;
    }

    public function setTubeJumperAttribute($value): void
    {
        $this->attributes['tube_tujuan'] = $value;
    }

    public function getTubeJumperAttribute(): ?string
    {
        return $this->tube_tujuan ?? null;
    }

    public function setCoreJumperAttribute($value): void
    {
        $this->attributes['core_tujuan'] = $value;
    }

    public function getCoreJumperAttribute(): ?string
    {
        return $this->core_tujuan ?? null;
    }

    public function setStatusAttribute($value): void
    {
        $this->attributes['status_core'] = $value;
    }

    public function getStatusAttribute(): string
    {
        return $this->status_core ?? 'TERHUBUNG';
    }

    public function jointClosure(): BelongsTo
    {
        return $this->belongsTo(TiketJointClosure::class, 'id_joint_closure');
    }

    /**
     * Badge CSS class untuk status core
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'TERHUBUNG' => 'bg-success text-white',
            'SPARE' => 'bg-info bg-opacity-15 text-info border border-info border-opacity-25',
            'LOSS_PUTUS' => 'bg-danger text-white',
            'MANUVER' => 'bg-warning text-dark',
            default => 'bg-secondary text-white',
        };
    }
}
