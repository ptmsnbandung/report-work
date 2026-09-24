<?php

namespace App\Exports;

use App\Models\TiketHandoverShift;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HandoverShiftExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        $query = TiketHandoverShift::with(['tiket', 'userFrom', 'userTo'])
            ->orderBy('created_at', 'desc');

        if (!empty($this->filters['shift_from'])) {
            $query->where('shift_from', $this->filters['shift_from']);
        }

        if (!empty($this->filters['shift_to'])) {
            $query->where('shift_to', $this->filters['shift_to']);
        }

        if (!empty($this->filters['start_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['start_date']);
        }

        if (!empty($this->filters['end_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['end_date']);
        }

        if (!empty($this->filters['user_id'])) {
            $userId = $this->filters['user_id'];
            $query->where(function ($q) use ($userId) {
                $q->where('user_from_id', $userId)
                  ->orWhere('user_to_id', $userId);
            });
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->whereHas('tiket', function ($q) use ($search) {
                $q->where('no_tiket', 'like', "%{$search}%")
                  ->orWhere('backbone_segment', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID Handover',
            'Waktu Handover (WIB)',
            'No Tiket',
            'Segment Backbone',
            'Status Tiket',
            'Shift Asal',
            'Shift Tujuan',
            'Petugas Asal (From)',
            'Petugas Penerima (To)',
            'Catatan Serah Terima / Kondisi Lapangan',
        ];
    }

    /**
     * @param TiketHandoverShift $row
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->created_at->format('d/m/Y H:i:s'),
            $row->tiket?->no_tiket ?? '-',
            $row->tiket?->backbone_segment ?? '-',
            $row->tiket?->status ?? '-',
            $row->shift_from,
            $row->shift_to,
            $row->userFrom?->name ?? 'Sistem',
            $row->userTo?->name ?? 'PIC Shift Baru',
            $row->catatan_handover,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E40AF'], // Blue Navy
                ],
            ],
        ];
    }
}
