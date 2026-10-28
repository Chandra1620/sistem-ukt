<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\PendaftaranExport;
use App\Models\Pendaftaran;
use App\Models\PeriodeSeleksi;
use App\Models\ProgramStudi;
use App\Models\GolonganUkt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function index()
    {
        $periode      = PeriodeSeleksi::orderByDesc('tahun')->get();
        $programStudi = ProgramStudi::aktif()->orderBy('jenjang')->orderBy('nama')->get();
        $golongan     = GolonganUkt::orderBy('golongan')->get();
        $statusList   = [
            'draft'                => 'Draft',
            'menunggu_verifikasi'  => 'Menunggu Verifikasi',
            'diverifikasi'         => 'Diverifikasi',
            'sedang_dihitung'      => 'Sedang Dihitung',
            'hasil_awal'           => 'Hasil Awal',
            'final_ukt'            => 'Final UKT',
            'ditolak_reviewer'     => 'Ditolak Reviewer',
            'selesai'              => 'Selesai',
        ];

        return view('admin.export.index', compact(
            'periode',
            'programStudi',
            'golongan',
            'statusList'
        ));
    }

    public function preview(Request $request)
    {
        $query = $this->buildQuery($request);
        $total = $query->count();

        return response()->json([
            'total'   => $total,
            'message' => "Ditemukan {$total} data sesuai filter.",
        ]);
    }

    public function excel(Request $request)
    {
        $filters  = $request->only([
            'periode_id',
            'program_studi_id',
            'golongan',
            'status',
            'tanggal_mulai',
            'tanggal_selesai'
        ]);
        $filename = 'data-ukt-' . date('Y-m-d-His') . '.xlsx';

        return Excel::download(new PendaftaranExport($filters), $filename);
    }

    private const MAX_PDF_ROWS = 300;

    public function pdf(Request $request)
    {
        $query = $this->buildQuery($request);
        $total = (clone $query)->count();

        if ($total === 0) {
            return back()->with('error', 'Tidak ada data yang sesuai dengan filter yang dipilih.');
        }

        if ($total > self::MAX_PDF_ROWS) {
            return back()->with(
                'error',
                "Jumlah data ({$total} baris) melebihi batas maksimum " . self::MAX_PDF_ROWS . " baris untuk export PDF. " .
                "Silakan persempit filter (misal pilih Program Studi atau Periode tertentu) atau gunakan Export Excel / CSV untuk seluruh data."
            );
        }

        $pendaftaran = $query->select([
            'id',
            'user_id',
            'periode_id',
            'program_studi_id',
            'no_pendaftaran',
            'nama_lengkap',
            'created_at',
        ])->with([
            'programStudi:id,nama',
            'periode:id,nama',
            'hasilUkt:id,pendaftaran_id,golongan_rekomendasi,golongan_final,nominal_final,is_override',
            'hasilSaw:id,pendaftaran_id,skor_akhir',
            'user:id,name',
        ])
        ->limit(self::MAX_PDF_ROWS)
        ->get();

        $filterInfo = $this->getFilterInfo($request);
        $periode    = $request->periode_id
            ? PeriodeSeleksi::select('id', 'nama', 'tahun')->find($request->periode_id)
            : null;

        $pdf = Pdf::loadView('admin.export.pdf', compact(
            'pendaftaran',
            'periode',
            'filterInfo'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('laporan-ukt-' . date('Y-m-d') . '.pdf');
    }

    public function csv(Request $request)
    {
        $query       = $this->buildQuery($request);
        $pendaftaran = $query->with([
            'programStudi',
            'periode',
            'hasilUkt',
            'hasilSaw',
        ])->get();

        $filename = 'data-ukt-' . date('Y-m-d-His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($pendaftaran) {
            $file = fopen('php://output', 'w');

            // Header CSV
            fputcsv($file, [
                'No',
                'No Pendaftaran',
                'Nama Lengkap',
                'NIK',
                'No HP',
                'Program Studi',
                'Jenjang',
                'Asal Daerah',
                'Provinsi',
                'Periode',
                'Status',
                'Golongan Rekomendasi',
                'Golongan Final',
                'Nominal UKT',
                'Skor SAW',
                'Ranking',
                'Override',
                'Tanggal Daftar',
            ]);

            foreach ($pendaftaran as $i => $p) {
                fputcsv($file, [
                    $i + 1,
                    $p->no_pendaftaran,
                    $p->nama_lengkap,
                    $p->nik,
                    $p->no_hp,
                    $p->programStudi->nama ?? '-',
                    $p->programStudi->jenjang ?? '-',
                    $p->asal_daerah,
                    $p->provinsi,
                    $p->periode->nama ?? '-',
                    $p->label_status ?? $p->status,
                    $p->hasilUkt ? 'Golongan ' . $p->hasilUkt->golongan_rekomendasi : '-',
                    $p->hasilUkt ? 'Golongan ' . $p->hasilUkt->golongan_final : '-',
                    $p->hasilUkt ? $p->hasilUkt->nominal_final : '-',
                    $p->hasilSaw ? number_format($p->hasilSaw->skor_akhir, 6) : '-',
                    $p->hasilSaw->ranking ?? '-',
                    $p->hasilUkt && $p->hasilUkt->is_override ? 'Ya' : 'Tidak',
                    optional($p->created_at)->format('d/m/Y H:i') ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function buildQuery(Request $request)
    {
        $query = Pendaftaran::query();

        if ($request->filled('periode_id')) {
            $query->where('periode_id', $request->periode_id);
        }

        if ($request->filled('program_studi_id')) {
            $query->where('program_studi_id', $request->program_studi_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('golongan')) {
            $query->whereHas(
                'hasilUkt',
                fn($q) =>
                $q->where('golongan_final', $request->golongan)
            );
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        return $query->orderBy('created_at');
    }

    private function getFilterInfo(Request $request): array
    {
        $info = [];

        if ($request->filled('periode_id')) {
            $p = PeriodeSeleksi::find($request->periode_id);
            $info[] = 'Periode: ' . ($p->nama ?? '-');
        }

        if ($request->filled('program_studi_id')) {
            $p = ProgramStudi::find($request->program_studi_id);
            $info[] = 'Program Studi: ' . ($p->nama ?? '-');
        }

        if ($request->filled('status')) {
            $info[] = 'Status: ' . $request->status;
        }

        if ($request->filled('golongan')) {
            $info[] = 'Golongan: ' . $request->golongan;
        }

        if ($request->filled('tanggal_mulai')) {
            $info[] = 'Dari: ' . $request->tanggal_mulai;
        }

        if ($request->filled('tanggal_selesai')) {
            $info[] = 'Sampai: ' . $request->tanggal_selesai;
        }

        return $info;
    }
}
