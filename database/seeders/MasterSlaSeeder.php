<?php

namespace Database\Seeders;

use App\Models\MasterSla;
use Illuminate\Database\Seeder;

class MasterSlaSeeder extends Seeder
{
    public function run(): void
    {
        $slas = [
            [
                'backbone_segment' => 'SW BBLU - SW Reog',
                'sla_target_minutes' => 360, // 6 jam
            ],
            [
                'backbone_segment' => 'SW Cikutra - SW Dago',
                'sla_target_minutes' => 360,
            ],
            [
                'backbone_segment' => 'SW Soekarno Hatta - SW Buah Batu',
                'sla_target_minutes' => 360,
            ],
            [
                'backbone_segment' => 'SW Pasteur - SW Sukajadi',
                'sla_target_minutes' => 240, // 4 jam
            ],
            [
                'backbone_segment' => 'SW Kopo - SW Cibaduyut',
                'sla_target_minutes' => 180, // 3 jam
            ],
            [
                'backbone_segment' => 'SW Ujungberung - SW Cibiru',
                'sla_target_minutes' => 180,
            ],
            [
                'backbone_segment' => 'SW Cimahi - SW Padalarang',
                'sla_target_minutes' => 240,
            ],
            [
                'backbone_segment' => 'SW Setiabudi - SW Lembang',
                'sla_target_minutes' => 300, // 5 jam
            ],
        ];

        foreach ($slas as $sla) {
            MasterSla::updateOrCreate(
                ['backbone_segment' => $sla['backbone_segment']],
                $sla
            );
        }
    }
}
