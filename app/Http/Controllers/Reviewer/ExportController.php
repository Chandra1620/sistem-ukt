<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\PeriodeSeleksi;
use App\Models\ProgramStudi;
use App\Models\GolonganUkt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VerifikasiReviewerExport;

class ExportController extends Controller
{
    public function index()
    {
        $periode      = PeriodeSeleksi::orderByDesc('tahun')->get();
        $programStudi = ProgramStudi::aktif()->orderBy('jenjang')->orderBy('nama')->get();
        $golongan     = GolonganUkt::orderBy('golongan')->get();
        $statusList   = [
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'diverifikasi'        => 'Diverifikasi',
            'ditolak_reviewer'    => 'Ditolak Reviewer',
        ];

        return view('reviewer.export.index', compact(
            'periode',
            'programStudi',
            'golongan',
            'statusList'
        ));
    }

    public function preview(Request $request)
    {
        $total = $this->buildQuery($request)->count();
        return response()->json(['total' => $total]);
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
        ])
        ->withCount('dokumen')
        ->with([
            'programStudi:id,nama',
            'periode:id,nama',
            'verifikasi' => fn($q) => $q->select('id', 'pendaftaran_id', 'reviewer_id', 'komentar', 'created_at')
                ->with('reviewer:id,name'),
        ])
        ->limit(self::MAX_PDF_ROWS)
        ->get();

        $filterInfo = $this->getFilterInfo($request);
        $periode    = $request->filled('periode_id')
            ? PeriodeSeleksi::select('id', 'nama', 'tahun')->find($request->periode_id)
            : null;

        $pdf = Pdf::loadView('reviewer.export.pdf', compact(
            'pendaftaran',
            'periode',
            'filterInfo'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('data-verifikasi-reviewer-' . date('Y-m-d') . '.pdf');
    }

    public function csv(Request $request)
    {
        $pendaftaran = $this->buildQuery($request)
            ->with(['programStudi', 'periode', 'verifikasi.reviewer'])
            ->get();

        $filename = 'data-verifikasi-reviewer-' . date('Y-m-d-His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($pendaftaran) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'No',
                'No Pendaftaran',
                'Nama Lengkap',
                'NIK',
                'No HP',
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
            ]);

            foreach ($pendaftaran as $i => $p) {
                $verifikasi = $p->verifikasi->last();
                fputcsv($file, [
                    $i + 1,
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
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function excel(Request $request)
    {
        $pendaftaran = $this->buildQuery($request)
            ->with(['programStudi', 'periode', 'verifikasi.reviewer', 'dokumen'])
            ->get();

        $filename = 'data-verifikasi-reviewer-' . date('Y-m-d-His') . '.xlsx';

        return Excel::download(new VerifikasiReviewerExport($pendaftaran), $filename);
    }

    private function buildQuery(Request $request)
    {
        // Reviewer hanya lihat data yang masuk ke proses verifikasi
        $query = Pendaftaran::whereIn('status', [
            'menunggu_verifikasi',
            'diverifikasi',
            'ditolak_reviewer',
            'sedang_dihitung',
            'hasil_awal',
            'final_ukt',
            'selesai',
        ]);

        if ($request->filled('periode_id')) {
            $query->where('periode_id', $request->periode_id);
        }
        if ($request->filled('program_studi_id')) {
            $query->where('program_studi_id', $request->program_studi_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('is_kip')) {
            $isKip = $request->is_kip == '1' ? 'true' : 'false';
            $query->whereRaw("\"is_kip\" IS {$isKip}");
        }
        if ($request->filled('hasil_verifikasi')) {
            if ($request->hasil_verifikasi === 'disetujui') {
                $query->where('status', '!=', 'ditolak_reviewer');
            } else {
                $query->where('status', 'ditolak_reviewer');
            }
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
        if ($request->filled('is_kip')) {
            $info[] = 'KIP: ' . ($request->is_kip == '1' ? 'KIP' : 'Non-KIP');
        }
        if ($request->filled('hasil_verifikasi')) {
            $info[] = 'Hasil Verifikasi: ' . ucfirst($request->hasil_verifikasi);
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
