<?php

namespace App\Exports;

use App\Models\Pendaftaran;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PendaftaranExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize,
    WithColumnWidths,
    WithColumnFormatting
{
    protected array $filters;
    protected int $no = 0;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Pendaftaran::with([
            'programStudi.akreditasiData',
            'periode',
            'hasilUkt',
            'hasilSaw',
        ]);

        if (!empty($this->filters['periode_id'])) {
            $query->where('periode_id', $this->filters['periode_id']);
        }

        if (!empty($this->filters['program_studi_id'])) {
            $query->where('program_studi_id', $this->filters['program_studi_id']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['golongan'])) {
            $query->whereHas(
                'hasilUkt',
                fn($q) =>
                $q->where('golongan_final', $this->filters['golongan'])
            );
        }

        if (isset($this->filters['is_kip']) && $this->filters['is_kip'] !== '') {
            $isKip = $this->filters['is_kip'] == '1' ? 'TRUE' : 'FALSE';
            $query->whereRaw("\"is_kip\" IS {$isKip}");
        }

        if (!empty($this->filters['tanggal_mulai'])) {
            $query->whereDate('created_at', '>=', $this->filters['tanggal_mulai']);
        }

        if (!empty($this->filters['tanggal_selesai'])) {
            $query->whereDate('created_at', '<=', $this->filters['tanggal_selesai']);
        }

        return $query->orderBy('created_at');
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
            'Jenjang',
            'Asal Daerah',
            'Provinsi',
            'Periode',
            'Status KIP',
            'No. KIP',
            'Status',
            'Gol. Rekomendasi',
            'Gol. Final',
            'Nominal UKT',
            'Sumber Pembiayaan',
            'Skor SAW',
            'Ranking',
            'Override',
            'Tanggal Daftar',
        ];
    }

    public function map($row): array
    {
        $this->no++;

        // Format nominal UKT
        $nominal = $row->hasilUkt ? (float) $row->hasilUkt->nominal_final : 0;

        // Format skor SAW
        $skor = $row->hasilSaw ? (float) $row->hasilSaw->skor_akhir : 0;

        // Determine status label
        $statusLabel = $this->getStatusLabel($row->status);

        // Determine golongan final
        $golonganFinal = '-';
        if ($row->hasilUkt) {
            if ($row->is_kip) {
                $golonganFinal = 'KIP';
            } else {
                $golonganFinal = 'Gol. ' . $row->hasilUkt->golongan_final;
            }
        }

        return [
            $this->no,
            $row->no_pendaftaran,
            $row->nama_lengkap,
            $row->nik,
            $row->no_hp,
            $row->programStudi->nama ?? '-',
            $row->programStudi->jenjang ?? '-',
            $row->asal_daerah,
            $row->provinsi,
            $row->periode->nama ?? '-',
            $row->is_kip ? 'KIP' : 'Non-KIP',
            $row->no_kip ?? '-',
            $statusLabel,
            $row->hasilUkt ? ($row->is_kip ? '-' : 'Gol. ' . $row->hasilUkt->golongan_rekomendasi) : '-',
            $golonganFinal,
            $nominal,
            $row->hasilUkt->sumber_pembiayaan ?? '-',
            $skor,
            $row->hasilSaw->ranking ?? '-',
            $row->hasilUkt && $row->hasilUkt->is_override ? 'Ya' : 'Tidak',
            $row->created_at->format('d/m/Y H:i'),
        ];
    }

    protected function getStatusLabel($status)
    {
        $labels = [
            'draft' => 'Draft',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'ditolak_reviewer' => 'Ditolak Reviewer',
            'hasil_awal' => 'Hasil Awal',
            'final_ukt' => 'Final UKT',
            'selesai' => 'Selesai',
        ];

        return $labels[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }

    public function styles(Worksheet $sheet): array
    {
        // Header style
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

        // Set header row height
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Alternate row colors (zebra styling)
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle($row . ':' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'f8f9fa'],
                    ],
                ]);
            }

            // Border for all cells
            $sheet->getStyle($row . ':' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'e2e8f0'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        // // Highlight KIP rows (light yellow)
        // $highestColumn = $sheet->getHighestColumn();
        // for ($row = 2; $row <= $highestRow; $row++) {
        //     $kipValue = $sheet->getCell('K' . $row)->getValue();
        //     if ($kipValue === 'KIP') {
        //         $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
        //             'fill' => [
        //                 'fillType' => Fill::FILL_SOLID,
        //                 'startColor' => ['rgb' => 'fef9e7'],
        //             ],
        //         ]);
        //     }
        // }

        // // Highlight Final UKT rows (light green)
        // for ($row = 2; $row <= $highestRow; $row++) {
        //     $statusValue = $sheet->getCell('M' . $row)->getValue();
        //     if (in_array($statusValue, ['Final UKT', 'Selesai'])) {
        //         $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
        //             'fill' => [
        //                 'fillType' => Fill::FILL_SOLID,
        //                 'startColor' => ['rgb' => 'e8f5e9'],
        //             ],
        //         ]);
        //     }
        // }

        return [];
    }

    public function title(): string
    {
        return 'Data UKT ' . date('Y');
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 18,  // No. Pendaftaran
            'C' => 25,  // Nama Lengkap
            'D' => 18,  // NIK
            'E' => 15,  // No. HP
            'F' => 30,  // Program Studi
            'G' => 10,  // Jenjang
            'H' => 15,  // Asal Daerah
            'I' => 15,  // Provinsi
            'J' => 15,  // Periode
            'K' => 12,  // Status KIP
            'L' => 15,  // No. KIP
            'M' => 20,  // Status
            'N' => 18,  // Gol. Rekomendasi
            'O' => 15,  // Gol. Final
            'P' => 15,  // Nominal UKT
            'Q' => 20,  // Sumber Pembiayaan
            'R' => 12,  // Skor SAW
            'S' => 10,  // Ranking
            'T' => 10,  // Override
            'U' => 18,  // Tanggal Daftar
        ];
    }

    public function columnFormats(): array
    {
        return [
            'P' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Nominal UKT
            'R' => NumberFormat::FORMAT_NUMBER_00, // Skor SAW (2 desimal)
        ];
    }
}
