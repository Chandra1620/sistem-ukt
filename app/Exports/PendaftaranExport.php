<?php

namespace App\Exports;

use App\Models\Pendaftaran;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PendaftaranExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithColumnWidths,
    WithColumnFormatting
{
    protected array $filters;
    protected array $baseStatuses = [];
    protected int $no = 0;

    public function __construct(array $filters = [], array $baseStatuses = [])
    {
        $this->filters = $filters;
        $this->baseStatuses = $baseStatuses;
    }

    public function query()
    {
        $query = Pendaftaran::with([
            'programStudi.akreditasiData',
            'periode',
            'hasilUkt',
            'hasilSaw',
        ]);

        // Batasan status dasar sesuai wewenang role pemanggil (mis. panitia).
        if (!empty($this->baseStatuses)) {
            $query->whereIn('status', $this->baseStatuses);
        }

        if (!empty($this->filters['periode_id'])) {
            $query->where('periode_id', $this->filters['periode_id']);
        }

        if (!empty($this->filters['program_studi_id'])) {
            $query->where('program_studi_id', $this->filters['program_studi_id']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['finalisasi'])) {
            if ($this->filters['finalisasi'] === 'sudah') {
                $query->whereIn('status', ['final_ukt', 'selesai']);
            } else {
                $query->where('status', 'hasil_awal');
            }
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

        $nominal = $row->hasilUkt ? (float) $row->hasilUkt->nominal_final : 0;
        $skor    = $row->hasilSaw ? (float) $row->hasilSaw->skor_akhir : 0;

        $statusLabel = $this->getStatusLabel($row->status);

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
            optional($row->created_at)->format('d/m/Y H:i') ?? '-',
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
        $highestRow    = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Header row (satu kali)
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
                'name' => 'Arial',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A5F'],
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
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Seluruh area data dalam SATU pemanggilan (hemat memori)
        if ($highestRow > 1) {
            $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        return [];
    }

    public function title(): string
    {
        return 'Data UKT ' . date('Y');
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
            'G' => 10,
            'H' => 15,
            'I' => 15,
            'J' => 15,
            'K' => 12,
            'L' => 15,
            'M' => 20,
            'N' => 18,
            'O' => 15,
            'P' => 15,
            'Q' => 20,
            'R' => 12,
            'S' => 10,
            'T' => 10,
            'U' => 18,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'P' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'R' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }
}
