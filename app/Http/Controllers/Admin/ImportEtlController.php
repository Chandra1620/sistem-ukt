<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProsesImportEtl;
use App\Models\ImportLog;
use App\Models\PeriodeSeleksi;
use Illuminate\Support\Facades\Storage;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Services\ImportEtlValidator;
use App\Support\NormalisasiKolom;

class ImportEtlController extends Controller
{
    use NormalisasiKolom;
    public function index()
    {
        $logs   = ImportLog::with('user')
            ->orderByDesc('created_at')
            ->paginate(10);
        $periode = PeriodeSeleksi::orderByDesc('tahun')->get();

        return view('admin.import-etl.index', compact('logs', 'periode'));
    }

    public function preview(Request $request)
    {
        if ($request->isMethod('GET')) {
            return redirect()->route('admin.import-etl.index');
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'file'       => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'periode_id' => 'required|exists:periode_seleksi,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.import-etl.index')
                ->with('error', $validator->errors()->first());
        }

        try {
            $__mulaiBaca = microtime(true); // [TEST CASE] mulai ukur pembacaan & validasi
            $file        = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray();

            if (count($rows) < 3) {
                return redirect()->route('admin.import-etl.index')
                    ->with('error', 'File kosong atau tidak sesuai template.');
            }

            $headerRow = $this->detectHeaderRow($rows);

            if ($headerRow === null) {
                return redirect()->route('admin.import-etl.index')
                    ->with('error', 'Header kolom tidak ditemukan. Pastikan menggunakan template yang benar.');
            }

            $headers = array_map(function ($h) {
                return strtolower(trim(str_replace('*', '', $h ?? '')));
            }, $rows[$headerRow]);

            $dataStartRow = $headerRow + 2;
            $dataRows     = array_slice($rows, $dataStartRow);
            $dataRows     = array_filter($dataRows, fn($r) => !empty(array_filter($r)));

            $required = ['nama_lengkap', 'nik', 'program_studi'];
            $missing  = array_diff($required, $headers);

            if (!empty($missing)) {
                return redirect()->route('admin.import-etl.index')
                    ->with('error', 'Kolom wajib tidak ditemukan: ' . implode(', ', $missing));
            }

            // Mapping baris ke array asosiatif berdasarkan header
            $mappedRows = [];
            foreach (array_values($dataRows) as $row) {
                $mapped = [];
                foreach ($headers as $i => $header) {
                    $mapped[$header] = $row[$i] ?? null;
                }
                $mappedRows[] = $mapped;
            }

            if (empty($mappedRows)) {
                return redirect()->route('admin.import-etl.index')
                    ->with('error', 'Tidak ada data yang bisa diproses.');
            }

            // ===== JALANKAN VALIDASI LENGKAP =====
            $importValidator = new ImportEtlValidator((int) $request->periode_id);
            $result          = $importValidator->validateAll($mappedRows);

            // [TEST CASE] Waktu pembacaan & validasi
            $__detikBacaValidasi = round(microtime(true) - $__mulaiBaca, 2);
            \Illuminate\Support\Facades\Log::info('[TEST CASE] Pembacaan & validasi ' . count($mappedRows) . ' baris: ' . $__detikBacaValidasi . ' detik');

            $path    = $file->store('imports', 'local');
            $periode = PeriodeSeleksi::find($request->periode_id);

            session(['import_header_row' => $headerRow]);

            return view('admin.import-etl.validasi', [
                'headers'    => $headers,
                'preview'    => array_slice($mappedRows, 0, 5),
                'total'      => count($mappedRows),
                'path'       => $path,
                'periode'    => $periode,
                'errors'     => $result['errors'],
                'summary'    => $result['summary'],
                'mappedRows' => $mappedRows,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.import-etl.index')
                ->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }
    }

    public function downloadErrorReport(Request $request)
    {
        $request->validate([
            'path'       => 'required|string',
            'periode_id' => 'required|exists:periode_seleksi,id',
        ]);

        if (!Storage::disk('local')->exists($request->path)) {
            return redirect()->route('admin.import-etl.index')
                ->with('error', 'File tidak ditemukan. Silakan upload ulang.');
        }

        $fullPath = Storage::disk('local')->path($request->path);

        $spreadsheet = IOFactory::load($fullPath);
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray();

        $headerRow = $this->detectHeaderRow($rows);
        $headers   = array_map(function ($h) {
            return strtolower(trim(str_replace('*', '', $h ?? '')));
        }, $rows[$headerRow]);

        $dataStartRow = $headerRow + 2;
        $dataRows     = array_slice($rows, $dataStartRow);
        $dataRows     = array_filter($dataRows, fn($r) => !empty(array_filter($r)));

        $mappedRows = [];
        foreach (array_values($dataRows) as $row) {
            $mapped = [];
            foreach ($headers as $i => $header) {
                $mapped[$header] = $row[$i] ?? null;
            }
            $mappedRows[] = $mapped;
        }

        $importValidator = new ImportEtlValidator((int) $request->periode_id);
        $result          = $importValidator->validateAll($mappedRows);

        // Generate Excel laporan error
        $spreadsheetOut = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheetOut = $spreadsheetOut->getActiveSheet();
        $sheetOut->setTitle('Laporan Error');

        // 1. HEADER UTAMA
        $sheetOut->mergeCells('A1:E1');
        $sheetOut->setCellValue('A1', 'LAPORAN ERROR VALIDASI IMPORT ETL');
        $sheetOut->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF'],
                'name' => 'Arial'
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'c0392b']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
        ]);
        $sheetOut->getRowDimension(1)->setRowHeight(40);
        // 2. SUB JUDUL INFO
        $sheetOut->mergeCells('A2:E2');
        $sheetOut->setCellValue('A2', 'Total ' . count($result['errors']) . ' kesalahan ditemukan | Dihasilkan: ' . now()->translatedFormat('d F Y H:i'));
        $sheetOut->getStyle('A2')->applyFromArray([
            'font' => [
                'italic' => true,
                'size' => 10,
                'color' => ['rgb' => '7f8c8d'],
                'name' => 'Arial'
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'f8f9fa']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => 'c0392b']
                ]
            ]
        ]);
        $sheetOut->getRowDimension(2)->setRowHeight(22);
        // 3. HEADER TABEL
        $headersOut = ['Baris', 'Kolom', 'Nilai Diinput', 'Kesalahan', 'Saran Perbaikan'];
        $lebarKolom = [10, 25, 30, 45, 50];
        $warnaHeader = '2c3e50';

        foreach ($headersOut as $i => $h) {
            $col = chr(65 + $i);
            $cell = $col . '3';
            $sheetOut->setCellValue($cell, $h);
            $sheetOut->getStyle($cell)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 11,
                    'color' => ['rgb' => 'FFFFFF'],
                    'name' => 'Arial'
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $warnaHeader]
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'FFFFFF']
                    ]
                ],
            ]);
            $sheetOut->getColumnDimension($col)->setWidth($lebarKolom[$i]);
        }
        $sheetOut->getRowDimension(3)->setRowHeight(24);
        // 4. ISI DATA
        $row = 4;
        $totalErrors = count($result['errors']);

        foreach ($result['errors'] as $index => $e) {
            $sheetOut->setCellValueExplicit('A' . $row, $e['baris'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $sheetOut->setCellValueExplicit('B' . $row, $e['kolom'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $nilai = $e['nilai'] !== '' ? $e['nilai'] : '(kosong)';
            $sheetOut->setCellValueExplicit('C' . $row, $nilai, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheetOut->setCellValueExplicit('D' . $row, $e['kesalahan'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheetOut->setCellValueExplicit('E' . $row, $e['saran'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $styleData = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'd0d7de']
                    ]
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                    'wrapText' => true
                ],
            ];

            if ($index % 2 == 0) {
                $styleData['fill'] = [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'fff5f5']
                ];
            }

            $sheetOut->getStyle('A' . $row . ':E' . $row)->applyFromArray($styleData);
            $sheetOut->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheetOut->getStyle('D' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('c0392b'));
            $sheetOut->getStyle('E' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('27ae60'));
            $sheetOut->getRowDimension($row)->setRowHeight(-1);

            $row++;
        }

        // 5. FOOTER (Total Error)
        $footerRow = $row + 1;
        $sheetOut->mergeCells('A' . $footerRow . ':E' . $footerRow);
        $sheetOut->setCellValue('A' . $footerRow, 'Total Kesalahan: ' . $totalErrors . ' error');
        $sheetOut->getStyle('A' . $footerRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
                'name' => 'Arial'
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'c0392b']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
        ]);
        $sheetOut->getRowDimension($footerRow)->setRowHeight(30);

        // 6. FREEZE PANE & AUTOFILTER
        $sheetOut->freezePane('A4');
        $sheetOut->setAutoFilter('A3:E3');

        // 7. STYLING TAMBAHAN
        foreach (range('A', 'E') as $col) {
            $sheetOut->getStyle($col . '4:' . $col . ($row - 1))->getFont()->setSize(10);
        }

        // 8. PROTECTION (Opsional - untuk mencegah edit)
        // $sheetOut->getProtection()->setSheet(true);

        // 9. OUTPUT
        $filename = 'Laporan_Error_Import_' . now()->format('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheetOut);
        $writer->save('php://output');
        exit;
    }

    public function proses(Request $request)
    {
        $request->validate([
            'path'       => 'required|string',
            'periode_id' => 'required|exists:periode_seleksi,id',
        ]);

        try {
            if (!Storage::disk('local')->exists($request->path)) {
                return redirect()->route('admin.import-etl.index')
                    ->with('error', 'File tidak ditemukan. Silakan upload ulang.');
            }

            $fullPath = Storage::disk('local')->path($request->path);

            $spreadsheet = IOFactory::load($fullPath);
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray();

            $headerRow = $this->detectHeaderRow($rows);

            if ($headerRow === null) {
                return redirect()->route('admin.import-etl.index')
                    ->with('error', 'Header tidak ditemukan dalam file.');
            }

            $headers = array_map(function ($h) {
                return strtolower(trim(str_replace('*', '', $h ?? '')));
            }, $rows[$headerRow]);

            $dataStartRow = $headerRow + 2;
            $dataRows     = array_slice($rows, $dataStartRow);
            $dataRows     = array_filter($dataRows, fn($r) => !empty(array_filter($r)));

            $mappedRows = [];
            foreach (array_values($dataRows) as $row) {
                $mapped = [];
                foreach ($headers as $i => $header) {
                    $mapped[$header] = $row[$i] ?? null;
                }
                $mappedRows[] = $mapped;
            }

            if (empty($mappedRows)) {
                return redirect()->route('admin.import-etl.index')
                    ->with('error', 'Tidak ada data yang bisa diproses.');
            }

            // ===== VALIDASI ULANG SEBELUM PROSES (mencegah bypass) =====
            $importValidator = new ImportEtlValidator((int) $request->periode_id);
            $result          = $importValidator->validateAll($mappedRows);

            if ($result['summary']['total_error'] > 0) {
                return redirect()->route('admin.import-etl.index')
                    ->with('error', 'Data masih mengandung ' . $result['summary']['total_error'] . ' error. Proses import dibatalkan. Silakan validasi ulang dan perbaiki data.');
            }

            $periode = PeriodeSeleksi::find($request->periode_id);

            $log = ImportLog::create([
                'user_id'      => Auth::id(),
                'nama_file'    => basename($request->path),
                'periode_nama' => $periode->nama,
                'total_baris'  => count($mappedRows),
                'status'       => 'pending',
            ]);

            ProsesImportEtl::dispatch($mappedRows, $request->periode_id, $log);

            $log->refresh();

            return redirect()->route('admin.import-etl.index')
                ->with('success', "Import dijadwalkan untuk {$log->total_baris} baris data. Jalankan queue worker untuk memproses.");
        } catch (\Exception $e) {
            return redirect()->route('admin.import-etl.index')
                ->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    private function detectHeaderRow(array $rows): ?int
    {
        $required = ['nama_lengkap', 'nik', 'program_studi'];

        foreach ($rows as $index => $row) {
            $normalized = array_map(function ($cell) {
                return strtolower(trim(str_replace('*', '', $cell ?? '')));
            }, $row);

            $found = 0;
            foreach ($required as $col) {
                if (in_array($col, $normalized)) {
                    $found++;
                }
            }

            if ($found >= 2) {
                return $index;
            }
        }

        return null;
    }

    public function show(ImportLog $importLog)
    {
        return view('admin.import-etl.show', compact('importLog'));
    }

    public function destroy(ImportLog $importLog)
    {
        $importLog->delete();
        return redirect()->route('admin.import-etl.index')
            ->with('success', 'Log import berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // ==========================================
        // SHEET 1: TEMPLATE DATA
        // ==========================================
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import');

        // Ambil data dinamis dari database
        $programStudiList = \App\Models\ProgramStudi::with('akreditasiData')
            ->aktif()->orderBy('jenjang')->orderBy('nama')->get();
        $kriteria = \App\Models\Kriteria::with('subKriteria')
            ->whereHas('periode', fn($q) => $q->where('status', 'aktif'))
            ->orderByRaw("CAST(SUBSTRING(kode, 2) AS INTEGER)")
            ->get();

        // ---- JUDUL ----
        $sheet->mergeCells('A1:U1');
        $sheet->setCellValue('A1', 'TEMPLATE IMPORT DATA MAHASISWA - SISTEM UKT');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(35);

        // ---- SUBJUDUL INFO ----
        $sheet->mergeCells('A2:U2');
        $sheet->setCellValue('A2', 'Isi data mulai dari baris ke-4. Jangan mengubah header atau struktur kolom. Kolom bertanda (*) wajib diisi.');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '7f8c8d']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f8f9fa']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // ---- HEADER KOLOM ----
        $headers = [
            // [Kolom, Nama, Wajib, Warna, Lebar, Keterangan]
            ['A', 'nama_lengkap',    true,  '2980b9', 25, 'Nama lengkap sesuai KTP'],
            ['B', 'nik',             true,  '2980b9', 20, '16 digit angka'],
            ['C', 'no_hp',           true,  '2980b9', 18, 'Format: 08xxxxxxxxxx'],
            ['D', 'email',           false, '2980b9', 25, 'Email aktif (opsional)'],
            ['E', 'program_studi',   true,  '2980b9', 30, 'Nama program studi sesuai sistem'],
            ['F', 'asal_daerah',     true,  '2980b9', 20, 'Kota/Kabupaten asal'],
            ['G', 'provinsi',        true,  '2980b9', 20, 'Provinsi asal'],
            ['H', 'is_kip',          false, '8e44ad', 12, 'Isi: 1 jika KIP, 0 jika bukan'],
            ['I', 'no_kip',          false, '8e44ad', 20, 'Nomor KIP (jika is_kip = 1)'],
        ];

        // Tambah kolom kriteria dinamis
        $kolomKriteria = ['J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
        $kriteriaHeaders = [];
        foreach ($kriteria as $i => $k) {
            $col = $kolomKriteria[$i] ?? chr(65 + 9 + $i);
            $namaKolom = strtolower(str_replace([' ', '/', '(', ')', '&'], ['_', '_', '', '', ''], $k->nama));
            $namaKolom = preg_replace('/_+/', '_', $namaKolom);
            $namaKolom = trim($namaKolom, '_');

            $kriteriaHeaders[] = [
                $col,
                $namaKolom,
                true,
                '27ae60',
                22,
                $k->nama . ' | Nilai: ' . $k->subKriteria->pluck('label')->join(', '),
            ];
        }

        $allHeaders = array_merge($headers, $kriteriaHeaders);

        // Row 3: Nama kolom
        foreach ($allHeaders as [$col, $nama, $wajib, $warna, $lebar]) {
            $cell = $col . '3';
            $sheet->setCellValue($cell, ($wajib ? '*' : '') . $nama);
            $sheet->getStyle($cell)->applyFromArray([
                'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $warna]],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
            ]);
            $sheet->getColumnDimension($col)->setWidth($lebar);
        }
        $sheet->getRowDimension(3)->setRowHeight(30);

        // Row 4: Keterangan kolom
        foreach ($allHeaders as [$col, $nama, $wajib, $warna, $lebar, $keterangan]) {
            $cell = $col . '4';
            $sheet->setCellValue($cell, $keterangan);
            $sheet->getStyle($cell)->applyFromArray([
                'font'      => ['italic' => true, 'size' => 8, 'color' => ['rgb' => '555555']],
                'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ecf0f1']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT, 'wrapText' => true],
                'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'bdc3c7']]],
            ]);
        }
        $sheet->getRowDimension(4)->setRowHeight(40);

        // Row 5: Contoh data
        $contohData = [
            'A5' => 'Budi Santoso',
            'B5' => '3201234567890001',
            'C5' => '081234567890',
            'D5' => 'budi@email.com',
            'E5' => $programStudiList->first()?->nama ?? 'D4 Teknik Informatika',
            'F5' => 'Bandung',
            'G5' => 'Jawa Barat',
            'H5' => '0',
            'I5' => '',
        ];

        // Contoh data kriteria
        foreach ($kriteria as $i => $k) {
            $col = $kolomKriteria[$i] ?? chr(65 + 9 + $i);
            $contoh = $k->subKriteria->first()?->label ?? '-';
            $contohData[$col . '5'] = $contoh;
        }

        foreach ($contohData as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $sheet->getStyle('A5:' . (end($allHeaders)[0]) . '5')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '7f8c8d']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fdfefe']],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'bdc3c7']]],
        ]);

        // Label baris contoh
        $lastCol = end($allHeaders)[0];
        $sheet->mergeCells('A4:A4');
        $sheet->setCellValue('A4', 'Keterangan Kolom ↓');

        // Area input data (baris 6 ke bawah)
        $sheet->getStyle('A6:' . $lastCol . '106')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'e0e0e0']]],
        ]);

        // Freeze header
        $sheet->freezePane('A6');

        // ==========================================
        // SHEET 2: REFERENSI PROGRAM STUDI
        // ==========================================
        $sheetRef = $spreadsheet->createSheet();
        $sheetRef->setTitle('Referensi Program Studi');

        $sheetRef->setCellValue('A1', 'DAFTAR PROGRAM STUDI YANG TERSEDIA');
        $sheetRef->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
        ]);
        $sheetRef->mergeCells('A1:D1');
        $sheetRef->getRowDimension(1)->setRowHeight(25);

        $sheetRef->setCellValue('A2', 'Nama Program Studi (salin persis ke kolom program_studi)');
        $sheetRef->setCellValue('B2', 'Jenjang');
        $sheetRef->setCellValue('C2', 'Akreditasi');
        $sheetRef->setCellValue('D2', 'Nominal KIP');
        $sheetRef->getStyle('A2:D2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2980b9']],
        ]);

        $row = 3;
        foreach ($programStudiList as $p) {
            $sheetRef->setCellValue('A' . $row, $p->nama);
            $sheetRef->setCellValue('B' . $row, $p->jenjang);
            $sheetRef->setCellValue('C' . $row, $p->akreditasiData?->nama ?? '-');
            $sheetRef->setCellValue('D' . $row, $p->akreditasiData?->nominal_kip_format ?? '-');
            $sheetRef->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'e0e0e0']]],
            ]);
            $row++;
        }

        $sheetRef->getColumnDimension('A')->setWidth(45);
        $sheetRef->getColumnDimension('B')->setWidth(12);
        $sheetRef->getColumnDimension('C')->setWidth(15);
        $sheetRef->getColumnDimension('D')->setWidth(20);

        // ==========================================
        // SHEET 3: REFERENSI SUB KRITERIA
        // ==========================================
        $sheetSub = $spreadsheet->createSheet();
        $sheetSub->setTitle('Referensi Sub Kriteria');

        $sheetSub->setCellValue('A1', 'DAFTAR NILAI SUB KRITERIA YANG VALID');
        $sheetSub->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
        ]);
        $sheetSub->mergeCells('A1:D1');
        $sheetSub->getRowDimension(1)->setRowHeight(25);

        $sheetSub->setCellValue('A2', 'Kriteria');
        $sheetSub->setCellValue('B2', 'Nama Kolom di Template');
        $sheetSub->setCellValue('C2', 'Label (Isi di template)');
        $sheetSub->setCellValue('D2', 'Nilai Bobot');
        $sheetSub->getStyle('A2:D2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '27ae60']],
        ]);

        $row = 3;
        foreach ($kriteria as $k) {
            $namaKolom = strtolower(str_replace([' ', '/', '(', ')'], ['_', '_', '', ''], $k->nama));
            foreach ($k->subKriteria->sortBy('nilai') as $sub) {
                $sheetSub->setCellValue('A' . $row, $k->kode . ' - ' . $k->nama);
                $sheetSub->setCellValue('B' . $row, $namaKolom);
                $sheetSub->setCellValue('C' . $row, $sub->label);
                $sheetSub->setCellValue('D' . $row, $sub->nilai);
                $sheetSub->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'e0e0e0']]],
                ]);
                // Warna alternating
                if ($row % 2 == 0) {
                    $sheetSub->getStyle('A' . $row . ':D' . $row)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('f8f9fa');
                }
                $row++;
            }
            // Separator antar kriteria
            $sheetSub->getStyle('A' . $row . ':D' . $row)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('27ae6020');
            $row++;
        }

        $sheetSub->getColumnDimension('A')->setWidth(35);
        $sheetSub->getColumnDimension('B')->setWidth(30);
        $sheetSub->getColumnDimension('C')->setWidth(35);
        $sheetSub->getColumnDimension('D')->setWidth(15);

        // Aktifkan sheet pertama
        $spreadsheet->setActiveSheetIndex(0);

        // Generate file
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'template-import-ukt-' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
