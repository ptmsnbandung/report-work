<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Dokumentasi extends Model
{
    use HasFactory;

    protected $table = 'dokumentasi';

    protected $fillable = [
        'id_tiket',
        'kategori',
        'file_path',
        'timestamp',
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

    /**
     * URL file foto dari storage
     */
    public function getUrlAttribute(): string
    {
        if (!$this->file_path) {
            return asset('images/placeholder.jpg');
        }

        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }

        return Storage::disk('public')->url($this->file_path);
    }

    /**
     * Link Google Maps jika koordinat tersedia
     */
    public function getGoogleMapsUrlAttribute(): ?string
    {
        if ($this->latitude && $this->longitude) {
            $lat = (float) $this->latitude;
            $lng = (float) $this->longitude;
            return "https://www.google.com/maps?q={$lat},{$lng}";
        }

        return null;
    }

    /**
     * Format timestamp Indonesia
     */
    public function getFormattedTimestampAttribute(): string
    {
        if (!$this->timestamp) {
            return '-';
        }

        return Carbon::parse($this->timestamp)->isoFormat('D MMM YYYY, HH:mm') . ' WIB';
    }

    /**
     * Badge CSS class untuk kategori foto
     */
    public function getKategoriBadgeClassAttribute(): string
    {
        return match (strtolower($this->kategori)) {
            'hasil jointing', 'jointing' => 'bg-success text-white',
            'closure terpasang', 'closure' => 'bg-info text-dark',
            'kondisi lokasi', 'lokasi' => 'bg-warning text-dark',
            'hasil otdr', 'otdr' => 'bg-primary text-white',
            default => 'bg-secondary text-white',
        };
    }
}
