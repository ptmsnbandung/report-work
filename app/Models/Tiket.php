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
        'closing_notes_teknisi',
        'tipe_penanganan',
        'tanggal_open',
        'tanggal_close',
        'resolved_at',
        'first_response_at',
        'status',
        'mttr_minutes',
        'total_stop_clock_minutes',
        'is_stop_clock',
        'response_time_minutes',
        'sla_target_minutes',
        'sla_status',
        'created_by',
        'resolved_by',
        'closed_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_open' => 'datetime',
            'tanggal_close' => 'datetime',
            'resolved_at' => 'datetime',
            'first_response_at' => 'datetime',
            'mttr_minutes' => 'integer',
            'total_stop_clock_minutes' => 'integer',
            'is_stop_clock' => 'boolean',
            'response_time_minutes' => 'integer',
            'sla_target_minutes' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
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

    public function stopClocks(): HasMany
    {
        return $this->hasMany(TiketStopClock::class, 'id_tiket')->orderBy('start_time', 'asc');
    }

    public function activeStopClock(): HasOne
    {
        return $this->hasOne(TiketStopClock::class, 'id_tiket')->where('is_active', true)->latestOfMany();
    }

    public function handoverShifts(): HasMany
    {
        return $this->hasMany(TiketHandoverShift::class, 'id_tiket')->latest();
    }

    /**
     * Cek apakah seluruh prasyarat mandatori untuk Closing Awal telah terpenuhi.
     */
    public function checkClosingPrerequisites(): array
    {
        $hasResume = $this->resume !== null && !empty($this->resume->problem_temuan) && !empty($this->resume->action);
        $hasDokumentasi = $this->dokumentasis()->count() > 0;
        $hasTitikPerbaikan = $this->titikPerbaikans()->count() > 0;
        $hasTipePenanganan = !empty($this->tipe_penanganan) || (!empty($this->resume?->tipe_penanganan));

        $missing = [];
        if (!$hasResume) {
            $missing[] = 'Resume pekerjaan wajib diisi (Problem temuan & Action perbaikan).';
        }
        if (!$hasDokumentasi) {
            $missing[] = 'Minimal lampirkan 1 foto dokumentasi hasil perbaikan lapangan / OTDR.';
        }
        if (!$hasTitikPerbaikan) {
            $missing[] = 'Minimal masukkan 1 titik koordinat perbaikan kabel / joint closure.';
        }
        if (!$hasTipePenanganan) {
            $missing[] = 'Pilih tipe penanganan (Jointing Lurus atau Manuver Core).';
        }

        return [
            'is_eligible' => empty($missing),
            'ready' => empty($missing),
            'items' => [
                'resume' => $hasResume,
                'resume_filled' => $hasResume,
                'dokumentasi' => $hasDokumentasi,
                'photo_uploaded' => $hasDokumentasi,
                'titik_perbaikan' => $hasTitikPerbaikan,
                'tipe_penanganan' => $hasTipePenanganan,
            ],
            'missing' => $missing,
            'missing_items' => $missing,
        ];
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
            'PENDING_VERIFIKASI' => 'bg-info text-dark',
            'CLOSE' => 'bg-success text-white',
            default => 'bg-secondary text-white',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'OPEN' => 'OPEN',
            'PROSES' => 'PROSES',
            'PENDING_VERIFIKASI' => 'CLOSING AWAL (PENDING VERIFIKASI)',
            'CLOSE' => 'CLOSE',
            default => $this->status,
        };
    }

    public function getTipePenangananLabelAttribute(): string
    {
        return match ($this->tipe_penanganan) {
            'JOINTING_LURUS' => 'Jointing Lurus (Straight Splice)',
            'MANUVER_CORE' => 'Manuver Core (Swapping Core)',
            default => 'Lainnya / Normalisasi',
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

    public function getIsPendingVerifikasiAttribute(): bool
    {
        return $this->status === 'PENDING_VERIFIKASI';
    }

    public function getIsCloseAttribute(): bool
    {
        return $this->status === 'CLOSE';
    }
}
