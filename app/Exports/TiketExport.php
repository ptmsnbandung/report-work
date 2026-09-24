<?php

namespace App\Exports;

use App\Models\Tiket;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TiketExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Ambil data tiket sesuai filter
     */
    public function collection(): Collection
    {
        $query = Tiket::with(['creator', 'closer', 'resume', 'materials', 'titikPerbaikans', 'jointClosures.cores'])
            ->orderBy('tanggal_open', 'desc');

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['sla_status'])) {
            $query->where('sla_status', $this->filters['sla_status']);
        }

        if (!empty($this->filters['segment'])) {
            $query->where('backbone_segment', $this->filters['segment']);
        }

        if (!empty($this->filters['start_date'])) {
            $query->whereDate('tanggal_open', '>=', $this->filters['start_date']);
        }

        if (!empty($this->filters['end_date'])) {
            $query->whereDate('tanggal_open', '<=', $this->filters['end_date']);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('no_tiket', 'like', "%{$search}%")
                  ->orWhere('status_link_impact', 'like', "%{$search}%")
                  ->orWhere('backbone_segment', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    /**
     * Header baris Excel
     */
    public function headings(): array
    {
        return [
            'No',
            'Nomor Tiket',
            'Status Link Impact',
            'Segment Backbone',
            'Status',
            'Tanggal Open',
            'Tanggal Close',
            'MTTR (Menit)',
            'Durasi MTTR',
            'Target SLA (Menit)',
            'Status SLA',
            'Joint Closure (JC)',
            'Aset Fisik Baru',
            'Problem / Temuan',
            'Tindakan Perbaikan',
            'Team OM Teknis',
            'Dibuat Oleh',
            'Ditutup Oleh',
        ];
    }

    /**
     * Map setiap baris Tiket ke kolom Excel
     *
     * @param Tiket $tiket
     */
    public function map($tiket): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $teamOmStr = '-';
        if ($tiket->resume && is_array($tiket->resume->team_om)) {
            $teamOmStr = implode(', ', $tiket->resume->team_om);
        }

        $jcSummary = '-';
        $newAssetSummary = 'Tidak';
        if ($tiket->jointClosures && $tiket->jointClosures->count() > 0) {
            $jcNames = $tiket->jointClosures->map(function ($jc) {
                return "{$jc->nama_closure} ({$jc->kapasitas_kabel_asal}C -> {$jc->kapasitas_kabel_jumper}C)";
            })->implode('; ');
            $jcSummary = $jcNames;

            $newAssetCount = $tiket->jointClosures->where('status_aset', 'ASET_BARU')->count();
            if ($newAssetCount > 0) {
                $newAssetSummary = "Ya ({$newAssetCount} Aset Baru)";
            }
        }

        return [
            $rowNumber,
            $tiket->no_tiket,
            $tiket->status_link_impact,
            $tiket->backbone_segment,
            $tiket->status,
            $tiket->tanggal_open ? $tiket->tanggal_open->format('d/m/Y H:i') : '-',
            $tiket->tanggal_close ? $tiket->tanggal_close->format('d/m/Y H:i') : '-',
            $tiket->mttr_minutes ?? '-',
            $tiket->formatted_mttr,
            $tiket->sla_target_minutes ?? '-',
            $tiket->sla_status,
            $jcSummary,
            $newAssetSummary,
            $tiket->resume?->problem_temuan ?? '-',
            $tiket->resume?->action ?? '-',
            $teamOmStr,
            $tiket->creator?->name ?? '-',
            $tiket->closer?->name ?? '-',
        ];
    }

    /**
     * Style worksheet Excel
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF071525']
                ]
            ],
        ];
    }
}
