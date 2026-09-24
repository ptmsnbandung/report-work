<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TiketJointClosure extends Model
{
    use HasFactory;

    protected $table = 'tiket_joint_closures';

    protected $fillable = [
        'id_tiket',
        'nama_closure',
        'tipe_closure',
        'status_aset',
        'kapasitas_kabel_asal',
        'jumlah_tube_asal',
        'kapasitas_kabel_jumper',
        'jumlah_tube_jumper',
        'jenis_sambungan',
        'lokasi_penempatan',
        'latitude',
        'longitude',
        'catatan',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'kapasitas_kabel_asal' => 'integer',
            'jumlah_tube_asal' => 'integer',
            'kapasitas_kabel_jumper' => 'integer',
            'jumlah_tube_jumper' => 'integer',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'id_tiket');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cores(): HasMany
    {
        return $this->hasMany(TiketJointClosureCore::class, 'id_joint_closure');
    }

    /**
     * Hitung ringkasan status core dalam closure ini
     */
    public function getCoreSummaryAttribute(): array
    {
        $cores = $this->cores;
        return [
            'total' => $cores->count(),
            'terhubung' => $cores->where('status_core', 'TERHUBUNG')->count(),
            'spare' => $cores->where('status_core', 'SPARE')->count(),
            'loss' => $cores->where('status_core', 'LOSS_PUTUS')->count(),
            'manuver' => $cores->where('status_core', 'MANUVER')->count(),
        ];
    }

    /**
     * Link Google Maps
     */
    public function getGoogleMapsUrlAttribute(): ?string
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }
        return null;
    }
}
