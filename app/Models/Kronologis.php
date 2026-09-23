<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kronologis extends Model
{
    use HasFactory;

    protected $table = 'kronologis';

    protected $fillable = [
        'id_tiket',
        'timestamp',
        'user_id',
        'kategori',
        'informasi',
        'foto_url',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'timestamp' => 'datetime',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'id_tiket');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Label teks kategori
     */
    public function getKategoriLabelAttribute(): string
    {
        return match ($this->kategori) {
            'IZIN' => 'Izin Masuk Ruangan / Lokasi',
            'OTDR' => 'Hasil Pengukuran OTDR',
            'TRACING' => 'Tracing Jalur Kabel',
            'MATERIAL' => 'Material Menuju / Sampai',
            'JOINTING' => 'Proses Jointing Splicing',
            'LINK_UP' => 'Link UP / Normalisasi',
            'SELESAI' => 'Pekerjaan Selesai',
            'LAIN' => 'Update Lainnya',
            default => $this->kategori,
        };
    }

    /**
     * Bootstrap icon untuk kategori
     */
    public function getKategoriIconAttribute(): string
    {
        return match ($this->kategori) {
            'IZIN' => 'bi-key-fill',
            'OTDR' => 'bi-activity',
            'TRACING' => 'bi-search',
            'MATERIAL' => 'bi-box-seam-fill',
            'JOINTING' => 'bi-tools',
            'LINK_UP' => 'bi-wifi',
            'SELESAI' => 'bi-check-circle-fill',
            default => 'bi-chat-dots-fill',
        };
    }

    /**
     * Label warna badge kategori
     */
    public function getKategoriBadgeAttribute(): string
    {
        return match ($this->kategori) {
            'IZIN' => 'bg-secondary text-white',
            'OTDR' => 'bg-info text-dark',
            'TRACING' => 'bg-primary text-white',
            'MATERIAL' => 'bg-warning text-dark',
            'JOINTING' => 'bg-indigo text-white',
            'LINK_UP' => 'bg-success text-white',
            'SELESAI' => 'bg-dark text-white',
            'LAIN' => 'bg-light text-dark border',
            default => 'bg-secondary text-white',
        };
    }

    /**
     * Cek apakah memiliki koordinat GPS
     */
    public function getHasCoordinatesAttribute(): bool
    {
        return !empty($this->latitude) && !empty($this->longitude);
    }

    /**
     * Link Google Maps jika ada koordinat
     */
    public function getGoogleMapsUrlAttribute(): ?string
    {
        if (!$this->has_coordinates) {
            return null;
        }

        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }
}
