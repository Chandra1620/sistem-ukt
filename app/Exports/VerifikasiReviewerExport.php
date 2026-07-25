<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class VerifikasiReviewerExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithColumnWidths
{
    protected Collection $data;
    protected int $no = 0;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'No',
            'No. Pendaftaran',
            'Nama Lengkap',
            'NIK',
            'No. HP',
            'Program Studi',
            'Status KIP',
            'Asal Daerah',
            'Provinsi',
            'Periode',
            'Status Verifikasi',
            'Reviewer',
            'Komentar Verifikasi',
            'Jumlah Dokumen',
            'Tanggal Daftar',
        ];
    }

    public function map($p): array
    {
        $this->no++;
        $verifikasi = $p->verifikasi->last();

        return [
            $this->no,
            $p->no_pendaftaran,
            $p->nama_lengkap,
            $p->nik,
            $p->no_hp,
            $p->programStudi->nama ?? '-',
            $p->is_kip ? 'KIP' : 'Non-KIP',
            $p->asal_daerah,
            $p->provinsi,
            $p->periode->nama ?? '-',
            $p->label_status ?? $p->status,
            $verifikasi?->reviewer?->name ?? '-',
            $verifikasi?->komentar ?? '-',
            $p->dokumen->count(),
            optional($p->created_at)->format('d/m/Y H:i') ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('1:1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
                'name' => 'Arial',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1e3a5f'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        return [];
    }

    public function title(): string
    {
        return 'Data Verifikasi ' . date('Y');
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 18,
            'C' => 25,
            'D' => 18,
            'E' => 15,
            'F' => 30,
            'G' => 12,
            'H' => 15,
            'I' => 15,
            'J' => 15,
            'K' => 20,
            'L' => 20,
            'M' => 35,
            'N' => 15,
            'O' => 18,
        ];
    }
}
