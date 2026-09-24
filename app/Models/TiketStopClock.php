<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TiketStopClock extends Model
{
    use HasFactory;

    protected $table = 'tiket_stop_clocks';

    protected $fillable = [
        'id_tiket',
        'start_time',
        'end_time',
        'duration_minutes',
        'alasan_kategori',
        'alasan_detail',
        'requested_by',
        'stopped_by',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'duration_minutes' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'id_tiket');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function stopper(): BelongsTo
    {
        return $this->belongsTo(User::class, 'stopped_by');
    }

    public function stoppedBy(): BelongsTo
    {
        return $this->stopper();
    }

    public function user(): BelongsTo
    {
        return $this->requester();
    }

    public function getStoppedAtAttribute()
    {
        return $this->start_time;
    }

    public function setStoppedAtAttribute($value)
    {
        $this->attributes['start_time'] = $value;
    }

    public function getResumedAtAttribute()
    {
        return $this->end_time;
    }

    public function setResumedAtAttribute($value)
    {
        $this->attributes['end_time'] = $value;
    }

    public function getReasonAttribute(): ?string
    {
        return $this->alasan_kategori;
    }

    public function getNotesAttribute(): ?string
    {
        return $this->alasan_detail;
    }

    public function getReasonLabelAttribute(): string
    {
        return $this->alasan_kategori_label;
    }

    public function getAlasanKategoriLabelAttribute(): string
    {
        return match ($this->alasan_kategori) {
            'IZIN_AKSES', 'MENUNGGU_AKSES_PELANGGAN' => 'Menunggu Izin / Akses Pelanggan',
            'PIHAK_KETIGA_PLN', 'KENDALA_PIHAK_KETIGA' => 'Kendala Pihak Ketiga (PLN/Bina Marga)',
            'CUACA_EKSTRIM', 'CUACA_BURUK' => 'Cuaca Buruk / Hujan Deras',
            'MENUNGGU_MATERIAL' => 'Menunggu Pengiriman Material',
            'PERMINTAAN_PELANGGAN' => 'Penundaan Permintaan Pelanggan',
            'FORCE_MAJEURE' => 'Force Majeure / Bencana Alam',
            default => 'Lainnya',
        };
    }
}
