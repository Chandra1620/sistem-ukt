<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodeSeleksi;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodeController extends Controller
{
    public function index()
    {
        $periode = PeriodeSeleksi::withCount('pendaftaran')
            ->orderByDesc('tahun')
            ->paginate(10);
        return view('admin.periode.index', compact('periode'));
    }

    public function create()
    {
        return view('admin.periode.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'            => 'required|string|max:255',
            'tahun'           => 'required|integer|min:2020|max:2100',
            'tanggal_mulai'   => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'salin_dari'      => 'nullable|exists:periode_seleksi,id',
        ], [
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai periode tidak boleh lebih kecil dari tanggal hari ini.',
        ]);

        $sudahAda = PeriodeSeleksi::where('nama', $request->nama)
            ->where('tahun', $request->tahun)
            ->exists();

        if ($sudahAda) {
            return back()->withInput()
                ->with('error', 'Periode dengan nama dan tahun yang sama sudah ada.');
        }

        DB::transaction(function () use ($request) {
            $periode = PeriodeSeleksi::create([
                'nama'            => $request->nama,
                'tahun'           => $request->tahun,
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status'          => 'nonaktif',
            ]);

            if ($request->filled('salin_dari')) {
                // Salin dari periode yang dipilih admin
                $this->salinDariPeriode($periode->id, (int) $request->salin_dari);
            } elseif ($request->salin_mode === 'default') {
                // Buat 12 kriteria default tanpa sub kriteria
                $this->buatKriteriaDefault($periode->id);
            }
            // Jika salin_mode = 'kosong', tidak buat apa-apa

            AuditLogService::log('buat_periode', 'PeriodeSeleksi', $periode->id, null, $request->all());
        });

        return redirect()->route('admin.periode.index')
            ->with('success', 'Periode berhasil dibuat dengan status Nonaktif. Aktifkan melalui menu Edit jika sudah siap.');
    }

    public function show(PeriodeSeleksi $periode)
    {
        $periode->load(['pendaftaran']);

        $kriteria = Kriteria::where('periode_id', $periode->id)
            ->with('subKriteria')
            ->orderByRaw("CAST(SUBSTRING(kode, 2) AS INTEGER)")
            ->get();

        $golonganUkt  = \App\Models\GolonganUkt::orderBy('golongan')->get();
        $programStudi = \App\Models\ProgramStudi::orderBy('jenjang')->orderBy('nama')->get();
        $jenisDokumen = \App\Models\JenisDokumen::orderBy('urutan')->get();

        return view('admin.periode.show', compact(
            'periode',
            'kriteria',
            'golonganUkt',
            'programStudi',
            'jenisDokumen'
        ));
    }

    public function edit(PeriodeSeleksi $periode)
    {
        return view('admin.periode.edit', compact('periode'));
    }

    public function update(Request $request, PeriodeSeleksi $periode)
    {
        $request->validate([
            'nama'            => 'required|string|max:255',
            'tahun'           => 'required|integer|min:2020|max:2100',
            'tanggal_mulai'   => [
                'required',
                'date',
                // Edit: hanya paksa after_or_equal:today kalau tanggalnya BERUBAH
                function ($attribute, $value, $fail) use ($periode) {
                    if ($value !== $periode->tanggal_mulai->format('Y-m-d') && $value < now()->format('Y-m-d')) {
                        $fail('Tanggal mulai periode tidak boleh lebih kecil dari tanggal hari ini.');
                    }
                },
            ],
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status'          => 'required|in:aktif,nonaktif,selesai',
        ]);

        // Validasi: hanya 1 periode aktif
        if ($request->status === 'aktif') {
            $periodeAktifLain = PeriodeSeleksi::where('status', 'aktif')
                ->where('id', '!=', $periode->id)
                ->first();

            if ($periodeAktifLain && !$request->boolean('konfirmasi_nonaktif')) {
                return back()
                    ->withInput()
                    ->with('warning_aktif', [
                        'nama_aktif' => $periodeAktifLain->nama,
                        'id_aktif'   => $periodeAktifLain->id,
                        'periode_id' => $periode->id,
                        'data'       => $request->all(),
                    ]);
            }

            // Nonaktifkan periode lain
            PeriodeSeleksi::where('status', 'aktif')
                ->where('id', '!=', $periode->id)
                ->update(['status' => 'nonaktif']);
        }

        $dataLama = $periode->toArray();
        $periode->update($request->only([
            'nama',
            'tahun',
            'tanggal_mulai',
            'tanggal_selesai',
            'status'
        ]));

        AuditLogService::log('ubah_periode', 'PeriodeSeleksi', $periode->id, $dataLama, $request->all());

        return redirect()->route('admin.periode.index')
            ->with('success', 'Periode berhasil diperbarui.');
    }

    public function destroy(PeriodeSeleksi $periode)
    {
        if ($periode->pendaftaran()->count() > 0) {
            return back()->with('error', 'Periode tidak dapat dihapus karena sudah ada pendaftaran.');
        }

        AuditLogService::log('hapus_periode', 'PeriodeSeleksi', $periode->id, $periode->toArray(), null);
        $periode->delete(); // cascade akan hapus kriteria & sub_kriteria otomatis

        return redirect()->route('admin.periode.index')
            ->with('success', 'Periode berhasil dihapus.');
    }

    /**
     * Fitur Riwayat: tampilkan ringkasan kriteria per periode
     */
    public function riwayat()
    {
        $riwayat = PeriodeSeleksi::withCount('pendaftaran')
            ->with(['kriteria' => function ($q) {
                $q->withCount('subKriteria');
            }])
            ->orderByDesc('tahun')
            ->get()
            ->map(function ($p) {
                return [
                    'id'               => $p->id,
                    'nama'             => $p->nama,
                    'tahun'            => $p->tahun,
                    'status'           => $p->status,
                    'tanggal_mulai'    => $p->tanggal_mulai,
                    'tanggal_selesai'  => $p->tanggal_selesai,
                    'jumlah_kriteria'  => $p->kriteria->count(),
                    'jumlah_sub'       => $p->kriteria->sum('sub_kriteria_count'),
                    'total_bobot'      => $p->kriteria->sum('bobot'),
                    'jumlah_pendaftar' => $p->pendaftaran_count,
                    'created_at'       => $p->created_at,
                ];
            });

        return view('admin.periode.riwayat', compact('riwayat'));
    }

    /**
     * Fitur Bandingkan: tampilkan perbedaan kriteria antar dua periode
     */
    public function bandingkan(Request $request)
    {
        $semua = PeriodeSeleksi::orderByDesc('tahun')->get();

        $periodeA = null;
        $periodeB = null;
        $diff     = [];

        if ($request->filled('periode_a') && $request->filled('periode_b')) {
            $periodeA = PeriodeSeleksi::with(['kriteria.subKriteria'])
                ->find($request->periode_a);
            $periodeB = PeriodeSeleksi::with(['kriteria.subKriteria'])
                ->find($request->periode_b);

            if ($periodeA && $periodeB) {
                $kodeA = $periodeA->kriteria->keyBy('kode');
                $kodeB = $periodeB->kriteria->keyBy('kode');
                $semuaKode = $kodeA->keys()->merge($kodeB->keys())->unique()->sort();

                foreach ($semuaKode as $kode) {
                    $kA = $kodeA->get($kode);
                    $kB = $kodeB->get($kode);
                    $diff[$kode] = [
                        'kode'    => $kode,
                        'a_nama'  => $kA?->nama ?? '-',
                        'b_nama'  => $kB?->nama ?? '-',
                        'a_bobot' => $kA?->bobot ?? '-',
                        'b_bobot' => $kB?->bobot ?? '-',
                        'a_tipe'  => $kA?->tipe ?? '-',
                        'b_tipe'  => $kB?->tipe ?? '-',
                        'a_sub'   => $kA?->subKriteria->count() ?? 0,
                        'b_sub'   => $kB?->subKriteria->count() ?? 0,
                        'berubah' => (
                            $kA?->nama   !== $kB?->nama   ||
                            $kA?->bobot  != $kB?->bobot   ||
                            $kA?->tipe   !== $kB?->tipe
                        ),
                    ];
                }
            }
        }

        return view('admin.periode.bandingkan', compact('semua', 'periodeA', 'periodeB', 'diff'));
    }

    /**
     * Salin kriteria + sub_kriteria dari periode terakhir ke periode baru.
     * Jika tidak ada periode sebelumnya, buat 12 kriteria default.
     * Guard duplikasi: cek count > 0 sebelum insert.
     */
    private function salinAtauBuatKriteria(int $periodeId): void
    {
        // Guard utama — jika sudah ada data, batalkan (cegah double-run)
        if (Kriteria::where('periode_id', $periodeId)->count() > 0) {
            return;
        }

        // Cari periode terakhir yang punya kriteria (selain periode ini)
        $periodeSumber = PeriodeSeleksi::where('id', '!=', $periodeId)
            ->whereHas('kriteria')
            ->orderByDesc('id')
            ->first();

        if ($periodeSumber) {
            // Salin dari periode sumber
            $kriteriaLama = Kriteria::where('periode_id', $periodeSumber->id)
                ->with('subKriteria')
                ->get();

            foreach ($kriteriaLama as $k) {
                // Double-check per kode agar benar-benar tidak duplikat
                $sudahAda = Kriteria::where('periode_id', $periodeId)
                    ->where('kode', $k->kode)
                    ->exists();
                if ($sudahAda) continue;

                $kriteriasBaru = Kriteria::create([
                    'kode'       => $k->kode,
                    'nama'       => $k->nama,
                    'bobot'      => $k->bobot,
                    'tipe'       => $k->tipe,
                    'urutan'     => $k->urutan,
                    'periode_id' => $periodeId,
                ]);

                foreach ($k->subKriteria as $sub) {
                    SubKriteria::create([
                        'kriteria_id' => $kriteriasBaru->id,
                        'label'       => $sub->label,
                        'nilai'       => $sub->nilai,
                    ]);
                }
            }
        } else {
            // Tidak ada periode sumber — buat 12 kriteria default (tanpa sub kriteria)
            $defaultKriteria = [
                ['kode' => 'C1',  'nama' => 'Penghasilan Ayah',            'bobot' => 15, 'tipe' => 'cost',    'urutan' => 1],
                ['kode' => 'C2',  'nama' => 'Penghasilan Ibu',             'bobot' => 15, 'tipe' => 'cost',    'urutan' => 2],
                ['kode' => 'C3',  'nama' => 'Jumlah Tanggungan Keluarga',  'bobot' => 12, 'tipe' => 'benefit', 'urutan' => 3],
                ['kode' => 'C4',  'nama' => 'Tagihan Listrik',             'bobot' => 9,  'tipe' => 'cost',    'urutan' => 4],
                ['kode' => 'C5',  'nama' => 'Pekerjaan Ayah',              'bobot' => 6,  'tipe' => 'cost',    'urutan' => 5],
                ['kode' => 'C6',  'nama' => 'Pekerjaan Ibu',               'bobot' => 6,  'tipe' => 'cost',    'urutan' => 6],
                ['kode' => 'C7',  'nama' => 'Status Kepemilikan Rumah',    'bobot' => 8,  'tipe' => 'cost',    'urutan' => 7],
                ['kode' => 'C8',  'nama' => 'Kepemilikan Kendaraan',       'bobot' => 8,  'tipe' => 'cost',    'urutan' => 8],
                ['kode' => 'C9',  'nama' => 'Luas Tanah',                  'bobot' => 6,  'tipe' => 'cost',    'urutan' => 9],
                ['kode' => 'C10', 'nama' => 'PBB (Pajak Bumi & Bangunan)', 'bobot' => 6,  'tipe' => 'cost',    'urutan' => 10],
                ['kode' => 'C11', 'nama' => 'Hutang / Cicilan',            'bobot' => 5,  'tipe' => 'benefit', 'urutan' => 11],
                ['kode' => 'C12', 'nama' => 'Tagihan Air',                 'bobot' => 4,  'tipe' => 'cost',    'urutan' => 12],
            ];

            foreach ($defaultKriteria as $k) {
                Kriteria::create(array_merge($k, ['periode_id' => $periodeId]));
            }
        }
    }
    private function salinDariPeriode(int $periodeId, int $sumberId): void
    {
        if (Kriteria::where('periode_id', $periodeId)->count() > 0) return;

        $kriteriaLama = Kriteria::where('periode_id', $sumberId)
            ->with('subKriteria')
            ->get();

        foreach ($kriteriaLama as $k) {
            $baru = Kriteria::create([
                'kode'       => $k->kode,
                'nama'       => $k->nama,
                'bobot'      => $k->bobot,
                'tipe'       => $k->tipe,
                'urutan'     => $k->urutan,
                'periode_id' => $periodeId,
            ]);

            foreach ($k->subKriteria as $sub) {
                SubKriteria::create([
                    'kriteria_id' => $baru->id,
                    'label'       => $sub->label,
                    'nilai'       => $sub->nilai,
                    'range_min'   => $sub->range_min,
                    'range_max'   => $sub->range_max,
                ]);
            }
        }
    }

    private function buatKriteriaDefault(int $periodeId): void
    {
        if (Kriteria::where('periode_id', $periodeId)->count() > 0) return;

        $defaultKriteria = [
            ['kode' => 'C1',  'nama' => 'Penghasilan Ayah',            'bobot' => 15, 'tipe' => 'cost',    'urutan' => 1],
            ['kode' => 'C2',  'nama' => 'Penghasilan Ibu',             'bobot' => 15, 'tipe' => 'cost',    'urutan' => 2],
            ['kode' => 'C3',  'nama' => 'Jumlah Tanggungan Keluarga',  'bobot' => 12, 'tipe' => 'benefit', 'urutan' => 3],
            ['kode' => 'C4',  'nama' => 'Tagihan Listrik',             'bobot' => 9,  'tipe' => 'cost',    'urutan' => 4],
            ['kode' => 'C5',  'nama' => 'Pekerjaan Ayah',              'bobot' => 6,  'tipe' => 'cost',    'urutan' => 5],
            ['kode' => 'C6',  'nama' => 'Pekerjaan Ibu',               'bobot' => 6,  'tipe' => 'cost',    'urutan' => 6],
            ['kode' => 'C7',  'nama' => 'Status Kepemilikan Rumah',    'bobot' => 8,  'tipe' => 'cost',    'urutan' => 7],
            ['kode' => 'C8',  'nama' => 'Kepemilikan Kendaraan',       'bobot' => 8,  'tipe' => 'cost',    'urutan' => 8],
            ['kode' => 'C9',  'nama' => 'Luas Tanah',                  'bobot' => 6,  'tipe' => 'cost',    'urutan' => 9],
            ['kode' => 'C10', 'nama' => 'PBB (Pajak Bumi & Bangunan)', 'bobot' => 6,  'tipe' => 'cost',    'urutan' => 10],
            ['kode' => 'C11', 'nama' => 'Hutang / Cicilan',            'bobot' => 5,  'tipe' => 'benefit', 'urutan' => 11],
            ['kode' => 'C12', 'nama' => 'Tagihan Air',                 'bobot' => 4,  'tipe' => 'cost',    'urutan' => 12],
        ];

        foreach ($defaultKriteria as $k) {
            Kriteria::create(array_merge($k, ['periode_id' => $periodeId]));
        }
    }
}
