<?php

namespace Database\Seeders;

use App\Models\MasterMaterial;
use Illuminate\Database\Seeder;

class MasterMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            [
                'kode_material' => 'MAT-FO-01',
                'nama_material' => 'Kabel FO ADSS 24 Core',
                'satuan' => 'Meter',
                'stok_tersedia' => 2500,
                'keterangan' => 'Kabel FO Aerial ADSS 24 core G.652D',
                'is_active' => true,
            ],
            [
                'kode_material' => 'MAT-FO-02',
                'nama_material' => 'Kabel FO ADSS 48 Core',
                'satuan' => 'Meter',
                'stok_tersedia' => 1800,
                'keterangan' => 'Kabel FO Aerial ADSS 48 core G.652D',
                'is_active' => true,
            ],
            [
                'kode_material' => 'MAT-CLS-01',
                'nama_material' => 'Closure Dome 24 Core',
                'satuan' => 'Unit',
                'stok_tersedia' => 35,
                'keterangan' => 'Joint Closure Dome 24 core outdoor IP68',
                'is_active' => true,
            ],
            [
                'kode_material' => 'MAT-CLS-02',
                'nama_material' => 'Closure Inline 48 Core',
                'satuan' => 'Unit',
                'stok_tersedia' => 20,
                'keterangan' => 'Joint Closure Inline 48 core outdoor IP68',
                'is_active' => true,
            ],
            [
                'kode_material' => 'MAT-PROT-01',
                'nama_material' => 'Protection Sleeve 60mm',
                'satuan' => 'Pcs',
                'stok_tersedia' => 500,
                'keterangan' => 'Sleeve pelindung sambungan core fusion splicer',
                'is_active' => true,
            ],
            [
                'kode_material' => 'MAT-ACC-01',
                'nama_material' => 'Suspension Clamp FO',
                'satuan' => 'Set',
                'stok_tersedia' => 120,
                'keterangan' => 'Aksesoris gantungan kabel di tiang',
                'is_active' => true,
            ],
            [
                'kode_material' => 'MAT-ACC-02',
                'nama_material' => 'Dead End Clamp / Tension Clamp',
                'satuan' => 'Set',
                'stok_tersedia' => 95,
                'keterangan' => 'Klem penarik kabel tiang ujung/sudut',
                'is_active' => true,
            ],
            [
                'kode_material' => 'MAT-ACC-03',
                'nama_material' => 'Spiral Protection / Wrap Tube',
                'satuan' => 'Meter',
                'stok_tersedia' => 300,
                'keterangan' => 'Pelindung kabel dari gesekan ranting pohon',
                'is_active' => true,
            ],
        ];

        foreach ($materials as $m) {
            MasterMaterial::updateOrCreate(
                ['kode_material' => $m['kode_material']],
                $m
            );
        }
    }
}
