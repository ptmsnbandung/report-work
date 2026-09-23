<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotifikasiLog extends Model
{
    use HasFactory;

    protected $table = 'notifikasi_log';

    protected $fillable = [
        'id_tiket',
        'tipe',
        'penerima',
        'pesan',
        'status',
    ];

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'id_tiket');
    }
}
