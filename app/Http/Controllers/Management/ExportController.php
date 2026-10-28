<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\PeriodeSeleksi;
use App\Models\ProgramStudi;
use App\Models\GolonganUkt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PendaftaranExport;

class ExportController extends Controller
{
    public function index()
    {
        $periode      = PeriodeSeleksi::orderByDesc('tahun')->get();
        $programStudi = ProgramStudi::aktif()->orderBy('jenjang')->orderBy('nama')->get();
        $golongan     = GolonganUkt::orderBy('golongan')->get();
        $statusList   = [
            'draft'               => 'Draft',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'diverifikasi'        => 'Diverifikasi',
            'sedang_dihitung'     => 'Sedang Dihitung',
            'hasil_awal'          => 'Hasil Awal',
            'final_ukt'           => 'Final UKT',
            'ditolak_reviewer'    => 'Ditolak Reviewer',
            'selesai'             => 'Selesai',
        ];

        return view('management.export.index', compact(
            'periode', 'programStudi', 'golongan', 'statusList'
        ));
    }

    public function preview(Request $request)
    {
        $total = $this->buildQuery($request)->count();
        return response()->json(['total' => $total]);
    }

    public function excel(Request $request)
    {
        $filters  = $request->only([
            'periode_id', 'program_studi_id', 'golongan',
            'status', 'is_kip', 'tanggal_mulai', 'tanggal_selesai'
        ]);
        $filename = 'rekap-ukt-management-' . date('Y-m-d-His') . '.xlsx';
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
            'periode_id',
            'program_studi_id',
            'no_pendaftaran',
            'nama_lengkap',
            'is_kip',
            'status',
            'created_at',
        ])->with([
            'programStudi:id,nama',
            'periode:id,nama',
            'hasilUkt:id,pendaftaran_id,golongan_final,nominal_final',
        ])
        ->limit(self::MAX_PDF_ROWS)
        ->get();

        $filterInfo = $this->getFilterInfo($request);
        $periode    = $request->filled('periode_id')
            ? PeriodeSeleksi::select('id', 'nama', 'tahun')->find($request->periode_id)
            : null;

        $pdf = Pdf::loadView('management.export.pdf', compact(
            'pendaftaran', 'periode', 'filterInfo'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('rekap-ukt-management-' . date('Y-m-d') . '.pdf');
    }

    public function csv(Request $request)
    {
        $pendaftaran = $this->buildQuery($request)
            ->with(['programStudi', 'periode', 'hasilUkt', 'hasilSaw'])
            ->get();

        $filename = 'rekap-ukt-management-' . date('Y-m-d-His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($pendaftaran) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'No', 'No Pendaftaran', 'Nama Lengkap', 'NIK',
                'Program Studi', 'Jenjang', 'Asal Daerah', 'Provinsi',
                'Periode', 'Status KIP', 'No KIP', 'Status',
                'Golongan Final', 'Nominal UKT', 'Sumber Pembiayaan',
                'Dasar Penetapan', 'Skor SAW', 'Ranking',
                'Tanggal Daftar',
            ]);

            foreach ($pendaftaran as $i => $p) {
                fputcsv($file, [
                    $i + 1,
                    $p->no_pendaftaran,
                    $p->nama_lengkap,
                    $p->nik,
                    $p->programStudi->nama ?? '-',
                    $p->programStudi->jenjang ?? '-',
                    $p->asal_daerah,
                    $p->provinsi,
                    $p->periode->nama ?? '-',
                    $p->is_kip ? 'KIP' : 'Non-KIP',
                    $p->no_kip ?? '-',
                    $p->label_status ?? $p->status,
                    $p->hasilUkt ? ($p->is_kip ? 'KIP' : 'Golongan ' . $p->hasilUkt->golongan_final) : '-',
                    $p->hasilUkt ? 'Rp ' . number_format($p->hasilUkt->nominal_final, 0, ',', '.') : '-',
                    $p->hasilUkt->sumber_pembiayaan ?? '-',
                    $p->hasilUkt->dasar_penetapan ?? '-',
                    $p->hasilSaw ? number_format($p->hasilSaw->skor_akhir, 2) : '-',
                    $p->hasilSaw->ranking ?? '-',
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
            $query->whereHas('hasilUkt', fn($q) =>
                $q->where('golongan_final', $request->golongan)
            );
        }
        if ($request->filled('is_kip')) {
            $isKip = $request->is_kip == '1' ? 'true' : 'false';
            $query->whereRaw("\"is_kip\" IS {$isKip}");
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
            $p      = PeriodeSeleksi::find($request->periode_id);
            $info[] = 'Periode: ' . ($p->nama ?? '-');
        }
        if ($request->filled('program_studi_id')) {
            $p      = ProgramStudi::find($request->program_studi_id);
            $info[] = 'Program Studi: ' . ($p->nama ?? '-');
        }
        if ($request->filled('status')) {
            $info[] = 'Status: ' . $request->status;
        }
        if ($request->filled('golongan')) {
            $info[] = 'Golongan: ' . $request->golongan;
        }
        if ($request->filled('is_kip')) {
            $info[] = 'KIP: ' . ($request->is_kip == '1' ? 'KIP' : 'Non-KIP');
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