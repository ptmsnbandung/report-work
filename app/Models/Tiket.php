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

    /**
     * Update kronologis terakhir dari lapangan
     */
    public function getLastKronologisAttribute()
    {
        return $this->hasMany(Kronologis::class, 'id_tiket')->reorder()->orderByDesc('timestamp')->orderByDesc('id')->first();
    }

    /**
     * Hitung berapa menit sejak update kronologis terakhir
     */
    public function getMinutesSinceLastUpdateAttribute(): int
    {
        $last = $this->last_kronologis;
        $referenceTime = $last ? ($last->timestamp ?? $last->created_at) : $this->tanggal_open;

        if (!$referenceTime) {
            return 0;
        }

        return max(0, (int) Carbon::parse($referenceTime)->diffInMinutes(Carbon::now()));
    }

    /**
     * Status kepatuhan update laporan berkala 30 menit
     * Return: 'CLOSED' | 'STOP_CLOCK' | 'NORMAL' (<=20m) | 'WARNING' (21-30m) | 'OVERDUE' (>30m)
     */
    public function getFieldUpdateStatusAttribute(): string
    {
        if ($this->status === 'CLOSE') {
            return 'CLOSED';
        }

        if ($this->is_stop_clock) {
            return 'STOP_CLOCK';
        }

        $minutes = $this->minutes_since_last_update;

        if ($minutes <= 20) {
            return 'NORMAL';
        }

        if ($minutes <= 30) {
            return 'WARNING';
        }

        return 'OVERDUE';
    }

    /**
     * Rata-rata interval antar laporan lapangan pada tiket ini (dalam menit)
     */
    public function getAverageReportIntervalMinutesAttribute(): int
    {
        $kronologisList = $this->kronologis()->orderBy('timestamp', 'asc')->get();
        if ($kronologisList->count() < 2) {
            return $this->minutes_since_last_update;
        }

        $intervals = [];
        for ($i = 1; $i < $kronologisList->count(); $i++) {
            $prev = Carbon::parse($kronologisList[$i - 1]->timestamp);
            $curr = Carbon::parse($kronologisList[$i]->timestamp);
            $intervals[] = max(0, $prev->diffInMinutes($curr));
        }

        return count($intervals) > 0 ? (int) round(array_sum($intervals) / count($intervals)) : 0;
    }

    /**
     * Hitung durasi verifikasi HelpDesk NOC (dari closing awal ke closing akhir)
     */
    public function getVerificationDurationMinutesAttribute(): ?int
    {
        if (!$this->resolved_at || !$this->tanggal_close) {
            return null;
        }

        $resolved = Carbon::parse($this->resolved_at);
        $closed = Carbon::parse($this->tanggal_close);

        return max(0, (int) $resolved->diffInMinutes($closed));
    }

    /**
     * Breakdown Tahapan Garis Waktu SLA (Visual Stepper Stages)
     */
    public function getSlaTimelineStagesAttribute(): array
    {
        $isOpenDone = $this->tanggal_open !== null;
        $isResponseDone = $this->first_response_at !== null || $this->status !== 'OPEN';
        $isFieldDone = $this->resolved_at !== null || $this->status === 'CLOSE';
        $isCloseDone = $this->status === 'CLOSE';

        return [
            [
                'key' => 'OPEN',
                'title' => 'Open Tiket',
                'description' => $this->creator?->name ? 'Oleh ' . $this->creator->name : 'Tiket diterbitkan',
                'timestamp' => $this->tanggal_open ? $this->tanggal_open->format('d/m/Y H:i') : null,
                'is_completed' => $isOpenDone,
                'is_current' => $this->status === 'OPEN',
                'badge' => 'Stage 1',
                'icon' => 'bi-ticket-detailed-fill',
            ],
            [
                'key' => 'FIRST_RESPONSE',
                'title' => 'Respon Pertama',
                'description' => $this->first_response_at
                    ? 'Respon dalam ' . ($this->response_time_minutes ?? $this->tanggal_open->diffInMinutes($this->first_response_at)) . ' mnt'
                    : ($this->status === 'OPEN' ? 'Menunggu respon teknisi' : 'Ditangani'),
                'timestamp' => $this->first_response_at ? Carbon::parse($this->first_response_at)->format('d/m/Y H:i') : null,
                'is_completed' => $isResponseDone,
                'is_current' => $this->status === 'PROSES' && !$this->resolved_at,
                'badge' => $this->response_time_minutes ? $this->response_time_minutes . ' mnt' : null,
                'icon' => 'bi-lightning-charge-fill',
            ],
            [
                'key' => 'STOP_CLOCK',
                'title' => 'Jeda SLA (Stop Clock)',
                'description' => $this->total_stop_clock_minutes > 0
                    ? $this->total_stop_clock_minutes . ' mnt jeda tercatat'
                    : ($this->is_stop_clock ? 'Sedang dijeda' : 'Tidak ada jeda'),
                'timestamp' => $this->activeStopClock ? $this->activeStopClock->start_time->format('H:i') : null,
                'is_completed' => $this->total_stop_clock_minutes > 0,
                'is_current' => (bool) $this->is_stop_clock,
                'badge' => $this->total_stop_clock_minutes > 0 ? $this->total_stop_clock_minutes . ' mnt' : null,
                'icon' => 'bi-pause-circle-fill',
            ],
            [
                'key' => 'CLOSING_AWAL',
                'title' => 'Closing Awal Lapangan',
                'description' => $this->resolver?->name ? 'Selesai oleh ' . $this->resolver->name : 'Pekerjaan fisik selesai',
                'timestamp' => $this->resolved_at ? Carbon::parse($this->resolved_at)->format('d/m/Y H:i') : null,
                'is_completed' => $isFieldDone,
                'is_current' => $this->status === 'PENDING_VERIFIKASI',
                'badge' => $this->tipe_penanganan ? str_replace('_', ' ', $this->tipe_penanganan) : null,
                'icon' => 'bi-check2-all',
            ],
            [
                'key' => 'CLOSING_AKHIR',
                'title' => 'Closing Akhir (NOC)',
                'description' => $this->closed_by ? 'Verifikasi NOC: ' . ($this->closer?->name ?? 'Helpdesk') : 'Verifikasi link UP & Closing',
                'timestamp' => $this->tanggal_close ? $this->tanggal_close->format('d/m/Y H:i') : null,
                'is_completed' => $isCloseDone,
                'is_current' => false,
                'badge' => $this->status === 'CLOSE' ? $this->formatted_mttr : null,
                'icon' => 'bi-shield-fill-check',
            ],
        ];
    }
}
