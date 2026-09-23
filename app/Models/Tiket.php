<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tiket extends Model
{
    use HasFactory;

    protected $table = 'tiket';

    protected $fillable = [
        'no_tiket',
        'status_link_impact',
        'backbone_segment',
        'deskripsi',
        'tanggal_open',
        'tanggal_close',
        'status',
        'mttr_minutes',
        'sla_target_minutes',
        'sla_status',
        'created_by',
        'closed_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_open' => 'datetime',
            'tanggal_close' => 'datetime',
            'mttr_minutes' => 'integer',
            'sla_target_minutes' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function kronologis(): HasMany
    {
        return $this->hasMany(Kronologis::class, 'id_tiket')->orderBy('timestamp', 'asc');
    }

    public function resume(): HasOne
    {
        return $this->hasOne(ResumePekerjaan::class, 'id_tiket');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class, 'id_tiket');
    }

    public function titikPerbaikans(): HasMany
    {
        return $this->hasMany(TitikPerbaikan::class, 'id_tiket');
    }

    public function manuverCores(): HasMany
    {
        return $this->hasMany(ManuverCore::class, 'id_tiket');
    }

    public function dokumentasis(): HasMany
    {
        return $this->hasMany(Dokumentasi::class, 'id_tiket');
    }

    public function notifikasiLogs(): HasMany
    {
        return $this->hasMany(NotifikasiLog::class, 'id_tiket');
    }

    /**
     * Format durasi MTTR dalam jam dan menit (human readable)
     */
    public function getFormattedMttrAttribute(): string
    {
        if ($this->mttr_minutes === null) {
            return '-';
        }

        $jam = floor($this->mttr_minutes / 60);
        $menit = $this->mttr_minutes % 60;

        if ($jam > 0) {
            return "{$jam} jam {$menit} mnt";
        }

        return "{$menit} mnt";
    }

    /**
     * Format target SLA dalam jam
     */
    public function getFormattedSlaTargetAttribute(): string
    {
        if ($this->sla_target_minutes === null) {
            return '-';
        }

        $jam = round($this->sla_target_minutes / 60, 1);
        return "{$jam} Jam ({$this->sla_target_minutes} mnt)";
    }

    /**
     * Badge class bootstrap untuk status tiket
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'OPEN' => 'bg-danger text-white',
            'PROSES' => 'bg-warning text-dark',
            'CLOSE' => 'bg-success text-white',
            default => 'bg-secondary text-white',
        };
    }

    /**
     * Badge class bootstrap untuk status SLA
     */
    public function getSlaBadgeClassAttribute(): string
    {
        return match ($this->sla_status) {
            'TEPAT' => 'bg-success text-white',
            'LEBIH' => 'bg-danger text-white',
            default => 'bg-secondary text-white',
        };
    }

    /**
     * Label teks SLA yang deskriptif
     */
    public function getSlaStatusLabelAttribute(): string
    {
        return match ($this->sla_status) {
            'TEPAT' => 'TEPAT SLA',
            'LEBIH' => 'MELEBIHI SLA',
            default => 'BELUM CLOSE',
        };
    }

    public function getIsOpenAttribute(): bool
    {
        return $this->status === 'OPEN';
    }

    public function getIsProsesAttribute(): bool
    {
        return $this->status === 'PROSES';
    }

    public function getIsCloseAttribute(): bool
    {
        return $this->status === 'CLOSE';
    }
}

