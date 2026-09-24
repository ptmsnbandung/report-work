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
        'tiket_id',
        'nama_closure',
        'tipe_closure',
        'jenis_closure',
        'status_aset',
        'is_aset_baru',
        'kapasitas_kabel_asal',
        'jumlah_tube_asal',
        'kapasitas_kabel_jumper',
        'jumlah_tube_jumper',
        'jenis_sambungan',
        'lokasi_penempatan',
        'lokasi_fisik',
        'latitude',
        'longitude',
        'catatan',
        'keterangan',
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

    public function setTiketIdAttribute($value): void
    {
        $this->attributes['id_tiket'] = $value;
    }

    public function getTiketIdAttribute(): ?int
    {
        return $this->id_tiket ?? null;
    }

    public function setJenisClosureAttribute($value): void
    {
        $this->attributes['tipe_closure'] = $value;
    }

    public function getJenisClosureAttribute(): ?string
    {
        return $this->tipe_closure ?? 'DOME';
    }

    public function setIsAsetBaruAttribute($value): void
    {
        $this->attributes['status_aset'] = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'ASET_BARU' : 'EKSISTING';
    }

    public function getIsAsetBaruAttribute(): bool
    {
        return ($this->status_aset ?? 'EKSISTING') === 'ASET_BARU';
    }

    public function setLokasiFisikAttribute($value): void
    {
        $this->attributes['lokasi_penempatan'] = $value;
    }

    public function getLokasiFisikAttribute(): ?string
    {
        return $this->lokasi_penempatan ?? 'POLE';
    }

    public function setKeteranganAttribute($value): void
    {
        $this->attributes['catatan'] = $value;
    }

    public function getKeteranganAttribute(): ?string
    {
        return $this->catatan ?? null;
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
            'terhubung' => $cores->where('status', 'TERHUBUNG')->count(),
            'spare' => $cores->where('status', 'SPARE')->count(),
            'loss' => $cores->where('status', 'LOSS_PUTUS')->count(),
            'manuver' => $cores->where('status', 'MANUVER')->count(),
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
