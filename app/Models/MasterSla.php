<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSla extends Model
{
    use HasFactory;

    protected $table = 'master_sla';

    protected $fillable = [
        'backbone_segment',
        'sla_target_minutes',
    ];

    protected function casts(): array
    {
        return [
            'sla_target_minutes' => 'integer',
        ];
    }
}
