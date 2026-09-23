<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterMaterial extends Model
{
    use HasFactory;

    protected $table = 'master_materials';

    protected $fillable = [
        'kode_material',
        'nama_material',
        'satuan',
        'stok_tersedia',
        'keterangan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'stok_tersedia' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
