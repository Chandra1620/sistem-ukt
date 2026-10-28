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
        $periode = null;
        if ($request->filled('periode_id')) {
            $periode = PeriodeSeleksi::find($request->periode_id);
        }
        if (!$periode) {
            $periode = PeriodeSeleksi::where('status', 'aktif')->first();
        }

        // Query dasar dari Pendaftaran dengan status ['hasil_awal', 'final_ukt', 'selesai']
        $query = Pendaftaran::whereIn('pendaftaran.status', ['hasil_awal', 'final_ukt', 'selesai'])
            ->with([
                'hasilSaw',
                'hasilUkt',
                'user',
                'programStudi',
            ]);

        if ($periode) {
            $query->where('pendaftaran.periode_id', $periode->id);
        }

        if ($request->filled('prodi')) {
            $query->where('pendaftaran.program_studi_id', $request->prodi);
        }

        if ($request->filled('golongan')) {
            $golonganVal = $request->golongan;
            if ($golonganVal === 'kip' || $golonganVal === '0') {
                $query->where(function ($q) {
                    $q->whereRaw('"pendaftaran"."is_kip" IS TRUE')
                      ->orWhereHas('hasilUkt', fn($hu) => $hu->where('golongan_final', 0));
                });
            } else {
                $query->where(function ($q) use ($golonganVal) {
                    $q->whereHas('hasilUkt', fn($hu) => $hu->where('golongan_final', $golonganVal))
                      ->orWhereHas('hasilSaw', fn($hs) => $hs->where('rekomendasi_golongan', $golonganVal));
                });
            }
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('pendaftaran.nama_lengkap', 'ilike', '%' . $search . '%')
                    ->orWhere('pendaftaran.no_pendaftaran', 'ilike', '%' . $search . '%');
            });
        }

        // Sorting: ranking ASC NULLS LAST agar pendaftar Non-KIP terurut berdasarkan ranking SAW,
        // sedangkan mahasiswa KIP (yang tidak memiliki ranking/skor SAW) ditempatkan di bawah (NULLS LAST).
        $query->leftJoin('hasil_saw', 'pendaftaran.id', '=', 'hasil_saw.pendaftaran_id')
            ->select('pendaftaran.*')
            ->orderByRaw('hasil_saw.ranking ASC NULLS LAST')
            ->orderBy('pendaftaran.nama_lengkap', 'asc');

        $pendaftarans = $query->paginate(20);
        $hasilSaw     = $pendaftarans; // Alias untuk backward compatibility view

        // Hitung total mahasiswa pada periode ini yang masih menunggu finalisasi (status: hasil_awal)
        $totalMenungguFinalisasi = Pendaftaran::where('status', 'hasil_awal')
            ->when($periode, fn($q) => $q->where('periode_id', $periode->id))
            ->count();

        $programStudi = \App\Models\ProgramStudi::aktif()->get();

        return view('panitia.hasil-saw.index', compact(
            'pendaftarans',
            'hasilSaw',
            'periode',
            'programStudi',
            'totalMenungguFinalisasi'
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
