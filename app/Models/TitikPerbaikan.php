<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TitikPerbaikan extends Model
{
    use HasFactory;

    protected $table = 'titik_perbaikan';

    protected $fillable = [
        'id_tiket',
        'nama_titik',
        'latitude',
        'longitude',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'id_tiket');
    }

    /**
     * Link Google Maps
     */
    public function getGoogleMapsUrlAttribute(): string
    {
        $lat = (float) $this->latitude;
        $lng = (float) $this->longitude;
        return "https://www.google.com/maps?q={$lat},{$lng}";
    }
}
