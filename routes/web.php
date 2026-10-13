<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Profile routes - accessible by all roles
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Redirect setelah login berdasarkan role
Route::get('/dashboard', function () {
    $role = auth()->user()->getRoleNames()->first();
    return match ($role) {
        'mahasiswa'  => redirect('/mahasiswa/dashboard'),
        'reviewer'   => redirect('/reviewer/dashboard'),
        'panitia'    => redirect('/panitia/dashboard'),
        'admin'      => redirect('/admin/dashboard'),
        'management' => redirect('/management/dashboard'),
        default      => redirect('/'),
    };
})->middleware(['auth'])->name('dashboard');

// ===== MAHASISWA =====
Route::prefix('mahasiswa')->name('mahasiswa.')->middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Mahasiswa\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('pendaftaran', \App\Http\Controllers\Mahasiswa\PendaftaranController::class);
    Route::post('pendaftaran/{pendaftaran}/submit', [\App\Http\Controllers\Mahasiswa\PendaftaranController::class, 'submit'])->name('pendaftaran.submit');
    Route::get('pendaftaran/{pendaftaran}/data-ekonomi/create', [\App\Http\Controllers\Mahasiswa\DataEkonomiController::class, 'create'])->name('data-ekonomi.create');
    Route::post('pendaftaran/{pendaftaran}/data-ekonomi', [\App\Http\Controllers\Mahasiswa\DataEkonomiController::class, 'store'])->name('data-ekonomi.store');
    Route::get('pendaftaran/{pendaftaran}/data-ekonomi/edit', [\App\Http\Controllers\Mahasiswa\DataEkonomiController::class, 'edit'])->name('data-ekonomi.edit');
    Route::put('pendaftaran/{pendaftaran}/data-ekonomi', [\App\Http\Controllers\Mahasiswa\DataEkonomiController::class, 'update'])->name('data-ekonomi.update');
    Route::post('pendaftaran/{pendaftaran}/dokumen', [\App\Http\Controllers\Mahasiswa\DokumenController::class, 'store'])->name('dokumen.store');
    Route::delete('dokumen/{dokumen}', [\App\Http\Controllers\Mahasiswa\DokumenController::class, 'destroy'])->name('dokumen.destroy');
    Route::get('hasil-ukt/{pendaftaran}', [\App\Http\Controllers\Mahasiswa\HasilUktController::class, 'show'])->name('hasil-ukt.show');
    Route::post('pendaftaran/{pendaftaran}/banding', [\App\Http\Controllers\Mahasiswa\BandingController::class, 'store'])->name('banding.store');
    Route::post('hasil-ukt/{pendaftaran}/terima', [\App\Http\Controllers\Mahasiswa\HasilUktController::class, 'terimaKeputusan'])->name('hasil-ukt.terima');
});

// ===== REVIEWER =====
Route::prefix('reviewer')->name('reviewer.')->middleware(['auth', 'role:reviewer'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Reviewer\DashboardController::class, 'index'])->name('dashboard');
    Route::get('verifikasi', [\App\Http\Controllers\Reviewer\VerifikasiController::class, 'index'])->name('verifikasi.index');
    Route::get('verifikasi/{pendaftaran}', [\App\Http\Controllers\Reviewer\VerifikasiController::class, 'show'])->name('verifikasi.show');
    Route::post('verifikasi/{pendaftaran}/approve', [\App\Http\Controllers\Reviewer\VerifikasiController::class, 'approve'])->name('verifikasi.approve');
    Route::post('verifikasi/{pendaftaran}/reject', [\App\Http\Controllers\Reviewer\VerifikasiController::class, 'reject'])->name('verifikasi.reject');
    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Reviewer\ExportController::class, 'index'])->name('index');
        Route::get('preview', [\App\Http\Controllers\Reviewer\ExportController::class, 'preview'])->name('preview');
        Route::get('excel', [\App\Http\Controllers\Reviewer\ExportController::class, 'excel'])->name('excel');
        Route::get('pdf', [\App\Http\Controllers\Reviewer\ExportController::class, 'pdf'])->name('pdf');
        Route::get('csv', [\App\Http\Controllers\Reviewer\ExportController::class, 'csv'])->name('csv');
    });
});

// ===== PANITIA =====
Route::prefix('panitia')->name('panitia.')->middleware(['auth', 'role:panitia'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Panitia\DashboardController::class, 'index'])->name('dashboard');
    Route::get('hasil-saw', [\App\Http\Controllers\Panitia\HasilSawController::class, 'index'])->name('hasil-saw.index');
    Route::get('hasil-saw/{pendaftaran}', [\App\Http\Controllers\Panitia\HasilSawController::class, 'show'])->name('hasil-saw.show');
    Route::post('finalisasi/{pendaftaran}', [\App\Http\Controllers\Panitia\FinalisasiController::class, 'finalisasi'])->name('finalisasi');
    Route::post('finalisasi-massal', [\App\Http\Controllers\Panitia\FinalisasiController::class, 'finalisasiMassal'])->name('finalisasi-massal');
    Route::post('override/{pendaftaran}', [\App\Http\Controllers\Panitia\OverrideController::class, 'override'])->name('override');
    Route::get('banding', [\App\Http\Controllers\Panitia\BandingController::class, 'index'])->name('banding.index');
    Route::get('banding/{banding}', [\App\Http\Controllers\Panitia\BandingController::class, 'show'])->name('banding.show');
    Route::post('banding/{banding}/proses', [\App\Http\Controllers\Panitia\BandingController::class, 'proses'])->name('banding.proses');
    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Panitia\ExportController::class, 'index'])->name('index');
        Route::get('preview', [\App\Http\Controllers\Panitia\ExportController::class, 'preview'])->name('preview');
        Route::get('excel', [\App\Http\Controllers\Panitia\ExportController::class, 'excel'])->name('excel');
        Route::get('pdf', [\App\Http\Controllers\Panitia\ExportController::class, 'pdf'])->name('pdf');
        Route::get('csv', [\App\Http\Controllers\Panitia\ExportController::class, 'csv'])->name('csv');
    });
});

// ===== ADMIN =====
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::post('users/{user}/assign-role', [\App\Http\Controllers\Admin\UserController::class, 'assignRole'])->name('users.assign-role');

    // ===== KRITERIA - route statis WAJIB sebelum resource =====
    Route::get('kriteria/riwayat', [\App\Http\Controllers\Admin\KriteriaController::class, 'riwayat'])->name('kriteria.riwayat');
    Route::post('kriteria/reorder', [\App\Http\Controllers\Admin\KriteriaController::class, 'reorder'])->name('kriteria.reorder');
    Route::resource('kriteria', \App\Http\Controllers\Admin\KriteriaController::class)->parameters(['kriteria' => 'kriteria']);

    // ===== SUB KRITERIA - route statis WAJIB sebelum resource =====
    Route::get('sub-kriteria/riwayat', [\App\Http\Controllers\Admin\SubKriteriaController::class, 'riwayat'])->name('sub-kriteria.riwayat');
    Route::resource('sub-kriteria', \App\Http\Controllers\Admin\SubKriteriaController::class)->parameters(['sub-kriteria' => 'subKriteria']);

    // Periode
    Route::prefix('periode')->name('periode.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PeriodeController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\PeriodeController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\PeriodeController::class, 'store'])->name('store');
        Route::get('/riwayat', [\App\Http\Controllers\Admin\PeriodeController::class, 'riwayat'])->name('riwayat');
        Route::get('/bandingkan', [\App\Http\Controllers\Admin\PeriodeController::class, 'bandingkan'])->name('bandingkan');
        Route::get('/{periode}', [\App\Http\Controllers\Admin\PeriodeController::class, 'show'])->name('show');
        Route::get('/{periode}/edit', [\App\Http\Controllers\Admin\PeriodeController::class, 'edit'])->name('edit');
        Route::put('/{periode}', [\App\Http\Controllers\Admin\PeriodeController::class, 'update'])->name('update');
        Route::delete('/{periode}', [\App\Http\Controllers\Admin\PeriodeController::class, 'destroy'])->name('destroy');
    });

    // Golongan UKT
    Route::resource('golongan-ukt', \App\Http\Controllers\Admin\GolonganUktController::class);

    // Program Studi
    Route::resource('program-studi', \App\Http\Controllers\Admin\ProgramStudiController::class);

    // Jenis Dokumen
    Route::get('jenis-dokumen/next-urutan', function () {
        return response()->json(['next_urutan' => \App\Models\JenisDokumen::max('urutan') + 1]);
    })->name('jenis-dokumen.next-urutan');
    Route::resource('jenis-dokumen', \App\Http\Controllers\Admin\JenisDokumenController::class)->parameters(['jenis-dokumen' => 'jenisDokumen']);

    // Akreditasi
    Route::resource('akreditasi', \App\Http\Controllers\Admin\AkreditasiController::class);

    // Audit Log & Export
    Route::get('audit-log', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-log.index');
    Route::get('export', [\App\Http\Controllers\Admin\ExportController::class, 'index'])->name('export.index');
    Route::get('export/preview', [\App\Http\Controllers\Admin\ExportController::class, 'preview'])->name('export.preview');
    Route::get('export/excel', [\App\Http\Controllers\Admin\ExportController::class, 'excel'])->name('export.excel');
    Route::get('export/pdf', [\App\Http\Controllers\Admin\ExportController::class, 'pdf'])->name('export.pdf');
    Route::get('export/csv', [\App\Http\Controllers\Admin\ExportController::class, 'csv'])->name('export.csv');

    // Recalculate SAW
    Route::post('recalculate-saw', [\App\Http\Controllers\Admin\RecalculateSawController::class, 'recalculate'])->name('recalculate-saw');

    // Import ETL
    Route::prefix('import-etl')->name('import-etl.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ImportEtlController::class, 'index'])->name('index');
        Route::get('/download-template', [\App\Http\Controllers\Admin\ImportEtlController::class, 'downloadTemplate'])->name('download-template');
        Route::match(['GET', 'POST'], '/preview', [\App\Http\Controllers\Admin\ImportEtlController::class, 'preview'])->name('preview');
        Route::post('/proses', [\App\Http\Controllers\Admin\ImportEtlController::class, 'proses'])->name('proses');
        Route::get('/show/{importLog}', [\App\Http\Controllers\Admin\ImportEtlController::class, 'show'])->name('show');
        Route::delete('/{importLog}', [\App\Http\Controllers\Admin\ImportEtlController::class, 'destroy'])->name('destroy');
        Route::post('/error-report', [\App\Http\Controllers\Admin\ImportEtlController::class, 'downloadErrorReport'])->name('error-report');
    });
});

// ===== MANAGEMENT =====
Route::prefix('management')->name('management.')->middleware(['auth', 'role:management'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Management\DashboardController::class, 'index'])->name('dashboard');
    Route::get('analitik', [\App\Http\Controllers\Management\AnalitikController::class, 'index'])->name('analitik.index');
    Route::get('distribusi-ukt', [\App\Http\Controllers\Management\DistribusiUktController::class, 'index'])->name('distribusi-ukt.index');
    Route::get('statistik-prodi', [\App\Http\Controllers\Management\StatistikProdiController::class, 'index'])->name('statistik-prodi.index');
    Route::get('trend-tahunan', [\App\Http\Controllers\Management\TrendTahunanController::class, 'index'])->name('trend-tahunan.index');
    Route::get('proyeksi-pendapatan', [\App\Http\Controllers\Management\ProyeksiPendapatanController::class, 'index'])->name('proyeksi-pendapatan.index');
    Route::get('analisis-keadilan', [\App\Http\Controllers\Management\AnalisisKeadilanController::class, 'index'])->name('analisis-keadilan.index');
    Route::get('sebaran-geografis', [\App\Http\Controllers\Management\SebaranGeografisController::class, 'index'])->name('sebaran-geografis.index');
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Management\ExportController::class, 'index'])->name('index');
        Route::get('preview', [\App\Http\Controllers\Management\ExportController::class, 'preview'])->name('preview');
        Route::get('excel', [\App\Http\Controllers\Management\ExportController::class, 'excel'])->name('excel');
        Route::get('pdf', [\App\Http\Controllers\Management\ExportController::class, 'pdf'])->name('pdf');
        Route::get('csv', [\App\Http\Controllers\Management\ExportController::class, 'csv'])->name('csv');
    });
});

Route::prefix('api')->name('api.')
    ->middleware(['auth', 'role:admin|management|panitia'])
    ->group(function () {
        Route::get('statistik/pendaftar', [\App\Http\Controllers\Api\StatistikController::class, 'pendaftar'])->name('statistik.pendaftar');
        Route::get('statistik/distribusi-ukt', [\App\Http\Controllers\Api\StatistikController::class, 'distribusiUkt'])->name('statistik.distribusi-ukt');
        Route::get('statistik/per-prodi', [\App\Http\Controllers\Api\StatistikController::class, 'perProdi'])->name('statistik.per-prodi');
        Route::get('statistik/ekonomi', [\App\Http\Controllers\Api\StatistikController::class, 'ekonomi'])->name('statistik.ekonomi');
        Route::get('statistik/kip-per-prodi', function (\Illuminate\Http\Request $request) {
            // Filter periode dari query (?periode=<id> | all). Default semua periode.
            $__p = $request->query('periode');
            $periodeId = (is_numeric($__p) && \App\Models\PeriodeSeleksi::whereKey((int) $__p)->exists())
                ? (int) $__p
                : null;

            $data = \App\Models\ProgramStudi::with(['pendaftaran' => function ($q) use ($periodeId) {
                $q->whereIn('status', ['hasil_awal', 'final_ukt', 'selesai']);
                if ($periodeId) {
                    $q->where('periode_id', $periodeId);
                }
            }])
                ->aktif()
                ->get()
                ->map(function ($prodi) {
                    $kip    = $prodi->pendaftaran->filter(fn($p) => $p->is_kip)->count();
                    $nonKip = $prodi->pendaftaran->filter(fn($p) => !$p->is_kip)->count();
                    return [
                        'prodi'   => $prodi->nama,
                        'kip'     => $kip,
                        'non_kip' => $nonKip,
                        'total'   => $kip + $nonKip,
                    ];
                })
                ->filter(fn($d) => $d['total'] > 0)
                ->values();

            return response()->json($data);
        })->name('statistik.kip-per-prodi');
        Route::get('/statistik/wilayah-detail', [App\Http\Controllers\Management\AnalitikController::class, 'wilayahDetail'])
            ->name('statistik.wilayah-detail');
    });

// ===== API (AJAX) =====
Route::prefix('api')->name('api.')->middleware('auth')->group(function () {
    Route::get('notifikasi', [\App\Http\Controllers\Api\NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('notifikasi/{id}/baca', [\App\Http\Controllers\Api\NotifikasiController::class, 'baca'])->name('notifikasi.baca');
    Route::post('notifikasi/baca-semua', [\App\Http\Controllers\Api\NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');
});

Route::middleware(['auth', 'throttle:20,1'])->group(function () {
    Route::get('check/nik', function (\Illuminate\Http\Request $r) {
        $exists = \App\Models\Pendaftaran::where('nik', $r->nik)
            ->when($r->ignore_id, fn($q) => $q->where('id', '!=', $r->ignore_id))
            ->exists();
        return response()->json(['exists' => $exists]);
    })->name('check.nik');

    Route::get('check/no-hp', function (\Illuminate\Http\Request $r) {
        $exists = \App\Models\Pendaftaran::where('no_hp', $r->no_hp)
            ->when($r->ignore_id, fn($q) => $q->where('id', '!=', $r->ignore_id))
            ->exists();
        return response()->json(['exists' => $exists]);
    })->name('check.no-hp');
});

// Preview dokumen - bisa diakses semua role
Route::get('dokumen/{dokumen}/preview', [\App\Http\Controllers\Mahasiswa\DokumenController::class, 'preview'])
    ->name('dokumen.preview')
    ->middleware('auth');

require __DIR__ . '/auth.php';
