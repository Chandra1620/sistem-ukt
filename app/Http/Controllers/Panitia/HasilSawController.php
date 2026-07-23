<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\HasilSaw;
use App\Models\PeriodeSeleksi;
use Illuminate\Http\Request;

class HasilSawController extends Controller
{
    public function index(Request $request)
    {
        $periode = PeriodeSeleksi::where('status', 'aktif')->first();

        $query = HasilSaw::with([
            'pendaftaran.user',
            'pendaftaran.programStudi',
            'pendaftaran.hasilUkt',
        ]);

        if ($periode) {
            $query->where('periode_id', $periode->id);
        }

        if ($request->prodi) {
            $query->whereHas('pendaftaran', function ($q) use ($request) {
                $q->where('program_studi_id', $request->prodi);
            });
        }

        if ($request->golongan) {
            $query->where('rekomendasi_golongan', $request->golongan);
        }

        if ($request->search) {
            $query->whereHas('pendaftaran', function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                    ->orWhere('no_pendaftaran', 'like', '%' . $request->search . '%');
            });
        }

        $hasilSaw     = $query->orderBy('ranking')->paginate(20);
        $programStudi = \App\Models\ProgramStudi::aktif()->get();
        return view('panitia.hasil-saw.index', compact(
            'hasilSaw',
            'periode',
            'programStudi'
        ));
    }

    public function show(Pendaftaran $pendaftaran)
    {
        $pendaftaran->load([
            'user',
            'programStudi',
            'periode',
            'dataEkonomi',
            'dokumen.jenisDokumen',
            'hasilSaw.details.kriteria',
            'hasilUkt',
            'verifikasi.reviewer',
            'riwayatStatus',
        ]);

        return view('panitia.hasil-saw.show', compact('pendaftaran'));
    }
}
