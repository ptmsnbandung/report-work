<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Helper check user role.
     *
     * @param string|array $roles
     * @return bool
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles, true);
        }

        return $this->role === $roles;
    }

    /**
     * Get human-readable role name in Indonesian.
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Admin Sistem',
            'helpdesk' => 'HelpDesk NOC / SA & CS',
            'teknis' => 'Team Teknis Lapangan',
            'sa_cs' => 'HelpDesk NOC / SA & CS',
            default => ucfirst($this->role),
        };
    }

    /**
     * Label peran ringkas untuk tampilan chat / badge kecil.
     */
    public function getRoleShortAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Admin',
            'helpdesk' => 'Helpdesk',
            'teknis' => 'Teknis',
            'sa_cs' => 'Helpdesk',
            default => ucfirst($this->role),
        };
    }

    /**
     * Get avatar public URL.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar && file_exists(public_path('storage/' . $this->avatar))) {
            return asset('storage/' . $this->avatar);
        }
        return null;
    }

    /**
     * Tiket yang dibuat oleh user.
     */
    public function tiketsCreated(): HasMany
    {
        return $this->hasMany(Tiket::class, 'created_by');
    }

    /**
     * Tiket yang ditutup oleh user.
     */
    public function tiketsClosed(): HasMany
    {
        return $this->hasMany(Tiket::class, 'closed_by');
    }

    /**
     * Kronologis yang diinput oleh user.
     */
    public function kronologis(): HasMany
    {
        return $this->hasMany(Kronologis::class, 'user_id');
    }

    /**
     * Perangkat web push subscriptions milik user.
     */
    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class, 'user_id');
    }

    /**
     * Dapatkan mapping jumlah notifikasi belum dibaca per ID tiket / No Tiket
     * @return array<string|int, int>
     */
    public function getUnreadNotifCountsPerTiket(): array
    {
        $counts = [];
        foreach ($this->unreadNotifications as $notif) {
            $tId = $notif->data['id_tiket'] ?? null;
            $noT = $notif->data['no_tiket'] ?? null;
            if ($tId) {
                $counts[$tId] = ($counts[$tId] ?? 0) + 1;
            }
            if ($noT) {
                $counts[$noT] = ($counts[$noT] ?? 0) + 1;
            }
        }
        return $counts;
    }
}
