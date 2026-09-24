<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TiketHandoverShift extends Model
{
    use HasFactory;

    protected $table = 'tiket_handover_shifts';

    protected $fillable = [
        'id_tiket',
        'shift_from',
        'shift_to',
        'user_from_id',
        'user_to_id',
        'catatan_handover',
    ];

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'id_tiket');
    }

    public function userFrom(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_from_id');
    }

    public function userTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_to_id');
    }
}
