<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'endpoint',
        'public_key',
        'auth_token',
        'content_encoding',
        'device_type',
        'is_mobile',
        'user_agent',
        'last_active_at',
    ];

    protected $casts = [
        'is_mobile' => 'boolean',
        'last_active_at' => 'datetime',
    ];

    /**
     * Relasi ke User pemilik subscription
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
