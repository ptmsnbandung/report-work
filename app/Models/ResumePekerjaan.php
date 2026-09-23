<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumePekerjaan extends Model
{
    use HasFactory;

    protected $table = 'resume_pekerjaan';

    protected $fillable = [
        'id_tiket',
        'team_om',
        'problem_temuan',
        'action',
        'catatan_tambahan',
    ];

    protected function casts(): array
    {
        return [
            'team_om' => 'array',
        ];
    }

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'id_tiket');
    }

    /**
     * Dapatkan string daftar nama teknis
     */
    public function getTeamOmStringAttribute(): string
    {
        if (is_array($this->team_om)) {
            return implode(', ', $this->team_om);
        }

        return (string) ($this->team_om ?? '-');
    }
}
