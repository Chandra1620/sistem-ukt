@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')

@section('content')
    {{-- Banner Progres Recalculate SAW (Dinamis jika ada batch antrean aktif) --}}
    <div id="recalculateProgressAlert" class="alert alert-info border-0 shadow-sm rounded-4 mb-4 d-none"
        style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-left: 5px solid #3b82f6 !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center gap-2">
                <div class="spinner-border spinner-border-sm text-primary" role="status" id="recalcSpinner"></div>
                <strong class="text-primary fw-semibold" id="recalcAlertTitle">Perhitungan Ulang SAW Sedang Berjalan</strong>
            </div>
            <span class="badge bg-primary rounded-pill px-3 py-1" id="recalcAlertBadge">0% Selesai</span>
        </div>
        <div class="progress" style="height: 10px; border-radius: 6px; background-color: #cbd5e1;">
            <div id="recalcAlertProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                role="progressbar" style="width: 0%;"></div>
        </div>
        <div class="d-flex justify-content-between mt-2 small text-muted">
            <span id="recalcAlertDetail">Memproses antrean chunk...</span>
            <span id="recalcAlertWorkerHint"><i class="bi bi-cpu me-1"></i>Queue Worker</span>
        </div>
    </div>

    {{-- Stat Cards Premium --}}
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border" style="border-color: #e0e7ff !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Total User</span>
                        <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">{{ number_format($stats['total_user']) }}</h2>
                    </div>
                    <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                        style="background: #eef2ff; width: 48px; height: 48px;">
                        <i class="bi bi-people-fill fs-5 text-primary"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="small text-muted">Seluruh pengguna sistem</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border" style="border-color: #d1fae5 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Mahasiswa</span>
                        <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">{{ number_format($stats['total_mahasiswa']) }}
                        </h2>
                    </div>
                    <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                        style="background: #d1fae5; width: 48px; height: 48px;">
                        <i class="bi bi-mortarboard-fill fs-5 text-success"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="small text-muted">Mahasiswa terdaftar</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border" style="border-color: #ffe4e2 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Reviewer</span>
                        <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">{{ number_format($stats['total_reviewer']) }}
                        </h2>
                    </div>
                    <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                        style="background: #ffe4e2; width: 48px; height: 48px;">
                        <i class="bi bi-shield-check fs-5 text-danger"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="small text-muted">Tim verifikasi dokumen</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border" style="border-color: #fed7aa !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Panitia</span>
                        <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">{{ number_format($stats['total_panitia']) }}
                        </h2>
                    </div>
                    <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                        style="background: #fed7aa; width: 48px; height: 48px;">
                        <i class="bi bi-person-gear fs-5 text-warning"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="small text-muted">Admin & operator sistem</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 g-md-4 mb-4">
        {{-- Statistik Pendaftaran Premium --}}
        <div class="col-12 col-md-4">
            <div class="card stats-card h-100">
                <div class="card-header stats-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon purple-icon">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Statistik Pendaftaran</h6>
                    </div>
                    <div class="header-badge">
                        <i class="bi bi-database"></i> Real-time
                    </div>
                </div>
                <div class="card-body">
                    <div class="stats-list">
                        <div class="stats-item">
                            <div class="stats-item-label">
                                <i class="bi bi-people"></i> Total Pendaftar
                            </div>
                            <div class="stats-item-value">{{ $pendaftaranStats['total'] }}</div>
                        </div>
                        <div class="stats-item">
                            <div class="stats-item-label">
                                <i class="bi bi-hourglass-split"></i> Menunggu Verifikasi
                            </div>
                            <div class="stats-item-value warning-value">{{ $pendaftaranStats['menunggu_verifikasi'] }}</div>
                        </div>
                        <div class="stats-item">
                            <div class="stats-item-label">
                                <i class="bi bi-check-circle-fill"></i> Final UKT
                            </div>
                            <div class="stats-item-value success-value">{{ $pendaftaranStats['final_ukt'] }}</div>
                        </div>
                        <div class="stats-item">
                            <div class="stats-item-label">
                                <i class="bi bi-flag-checkered"></i> Selesai
                            </div>
                            <div class="stats-item-value primary-value">{{ $pendaftaranStats['selesai'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer stats-card-footer">
                    <div class="stats-summary">
                        <span class="summary-text">Tingkat penyelesaian:</span>
                        <span class="summary-value">
                            @php
                                $total = $pendaftaranStats['total'];
                                $selesai = $pendaftaranStats['selesai'];
                                $persen = $total > 0 ? round(($selesai / $total) * 100) : 0;
                            @endphp
                            {{ $persen }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Periode Aktif Premium --}}
        <div class="col-12 col-md-4">
            <div class="card period-card h-100">
                <div class="card-header period-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon green-icon">
                            <i class="bi bi-calendar-check-fill"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Periode Aktif</h6>
                    </div>
                    @if ($periodeAktif)
                        <div class="active-pulse"></div>
                    @endif
                </div>
                <div class="card-body">
                    @if ($periodeAktif)
                        <div class="period-detail">
                            <div class="period-field">
                                <div class="period-label">
                                    <i class="bi bi-tag"></i> Nama Periode
                                </div>
                                <div class="period-value fw-bold">{{ $periodeAktif->nama }}</div>
                            </div>
                            <div class="period-field">
                                <div class="period-label">
                                    <i class="bi bi-calendar-year"></i> Tahun
                                </div>
                                <div class="period-value">{{ $periodeAktif->tahun }}</div>
                            </div>
                            <div class="period-field">
                                <div class="period-label">
                                    <i class="bi bi-calendar-range"></i> Tanggal
                                </div>
                                <div class="period-value">
                                    <div class="date-range">
                                        <span>{{ $periodeAktif->tanggal_mulai->translatedFormat('d M Y') }}</span>
                                        <i class="bi bi-arrow-right-short"></i>
                                        <span>{{ $periodeAktif->tanggal_selesai->translatedFormat('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            @php
                                $tanggalMulai = $periodeAktif->tanggal_mulai->startOfDay();
                                $tanggalSelesai = $periodeAktif->tanggal_selesai->startOfDay();
                                $sekarang = now()->startOfDay();

                                if ($sekarang < $tanggalMulai) {
                                    $hariTersisa = $sekarang->diffInDays($tanggalMulai);
                                    $status = 'belum_mulai';
                                    $statusText = 'Mulai dalam';
                                    $statusIcon = 'bi-calendar-check';
                                    $statusColor = '#06b6d4';
                                } elseif ($sekarang <= $tanggalSelesai) {
                                    $hariTersisa = $sekarang->diffInDays($tanggalSelesai) + 1;
                                    $status = 'berlangsung';
                                    $statusText = 'Tersisa';
                                    $statusIcon = 'bi-hourglass-split';
                                    $statusColor = '#f59e0b';
                                } else {
                                    $hariTersisa = 0;
                                    $status = 'berakhir';
                                    $statusText = 'Telah berakhir';
                                    $statusIcon = 'bi-calendar-x';
                                    $statusColor = '#94a3b8';
                                }
                            @endphp

                            @if ($status === 'berlangsung')
                                <div class="countdown-timer" data-days="{{ $hariTersisa }}">
                                    <div class="timer-icon">
                                        <i class="bi {{ $statusIcon }}"></i>
                                    </div>
                                    <div class="timer-info">
                                        <div class="timer-label">{{ $statusText }}</div>
                                        <div class="timer-value">{{ $hariTersisa }} <span class="timer-unit">hari</span>
                                        </div>
                                    </div>
                                </div>
                            @elseif($status === 'belum_mulai')
                                <div class="info-timer info-upcoming">
                                    <i class="bi {{ $statusIcon }}"></i>
                                    <span>{{ $statusText }} <strong>{{ $hariTersisa }}</strong> hari lagi</span>
                                </div>
                            @elseif($status === 'berakhir')
                                <div class="info-timer info-ended">
                                    <i class="bi {{ $statusIcon }}"></i>
                                    <span>{{ $statusText }}</span>
                                </div>
                            @endif
                        </div>
                        <a href="{{ route('admin.periode.index') }}" class="btn-action-primary w-100 mt-3">
                            <i class="bi bi-gear"></i> Kelola Periode
                        </a>
                    @else
                        <div class="empty-period-enhanced">
                            <div class="empty-period-icon">
                                <i class="bi bi-calendar-x"></i>
                            </div>
                            <h6 class="empty-period-title">Tidak Ada Periode Aktif</h6>
                            <p class="empty-period-desc">
                                Saat ini tidak ada periode seleksi yang sedang berlangsung.
                                Silakan buat periode baru untuk memulai seleksi UKT.
                            </p>
                            <a href="{{ route('admin.periode.create') }}" class="btn-create-period-enhanced">
                                <i class="bi bi-plus-circle"></i> Buat Periode Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Quick Actions Premium --}}
        <div class="col-12 col-md-4">
            <div class="card quick-actions-card h-100">
                <div class="card-header quick-actions-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon orange-icon">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Aksi Cepat</h6>
                    </div>
                    <div class="header-badge">
                        <i class="bi bi-rocket"></i> Shortcut
                    </div>
                </div>
                <div class="card-body">
                    <div class="quick-actions-grid">
                        <a href="{{ route('admin.users.create') }}" class="quick-action-btn primary-btn">
                            <i class="bi bi-person-plus"></i>
                            <span>Tambah User</span>
                            <div class="btn-hint">User baru</div>
                        </a>
                        <a href="{{ route('admin.kriteria.index') }}" class="quick-action-btn success-btn">
                            <i class="bi bi-sliders2"></i>
                            <span>Kriteria SAW</span>
                            <div class="btn-hint">Kelola bobot</div>
                        </a>
                        <a href="{{ route('admin.periode.create') }}" class="quick-action-btn info-btn">
                            <i class="bi bi-calendar-plus"></i>
                            <span>Periode Baru</span>
                            <div class="btn-hint">Buka pendaftaran</div>
                        </a>
                        <a href="{{ route('admin.export.excel') }}" class="quick-action-btn excel-btn">
                            <i class="bi bi-file-earmark-excel-fill"></i>
                            <span>Export Excel</span>
                            <div class="btn-hint">.xlsx</div>
                        </a>
                        <a href="{{ route('admin.export.pdf') }}" class="quick-action-btn pdf-btn">
                            <i class="bi bi-file-earmark-pdf-fill"></i>
                            <span>Export PDF</span>
                            <div class="btn-hint">.pdf</div>
                        </a>
                        <a href="{{ route('admin.audit-log.index') }}" class="quick-action-btn secondary-btn">
                            <i class="bi bi-journal-text"></i>
                            <span>Audit Log</span>
                            <div class="btn-hint">Aktivitas</div>
                        </a>
                        <a href="#" class="quick-action-btn recalculate-btn" data-bs-toggle="modal"
                            data-bs-target="#recalculateModal">
                            <i class="bi bi-arrow-repeat"></i>
                            <span>Hitung Ulang SAW</span>
                            <div class="btn-hint">Reset perhitungan</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Audit Log Terbaru Premium --}}
    <div class="card audit-card">
        <div class="card-header audit-card-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="header-icon dark-icon">
                        <i class="bi bi-journal-bookmark-fill"></i>
                    </div>
                    <h6 class="fw-semibold mb-0">Aktivitas Terbaru</h6>
                    <span class="live-badge">
                        <i class="bi bi-circle-fill"></i> Live
                    </span>
                </div>
                <a href="{{ route('admin.audit-log.index') }}" class="btn-view-all-logs">
                    <i class="bi bi-arrow-right-circle"></i> Lihat Semua
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if ($auditTerbaru->count() > 0)
                <div class="table-responsive">
                    <table class="table audit-table">
                        <thead>
                            <tr>
                                <th><i class="bi bi-tag"></i> Aksi</th>
                                <th><i class="bi bi-person"></i> User</th>
                                <th class="d-none d-md-table-cell"><i class="bi bi-database"></i> Model</th>
                                <th class="d-none d-lg-table-cell"><i class="bi bi-wifi"></i> IP Address</th>
                                <th><i class="bi bi-clock"></i> Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($auditTerbaru as $log)
                                <tr class="audit-row">
                                    <td>
                                        <span class="action-badge action-{{ strtolower($log->label_aksi) }}">
                                            <i class="bi {{ $log->action_icon }}"></i>
                                            <span class="action-text">{{ $log->label_aksi }}</span>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                {{ substr($log->user->name ?? 'S', 0, 1) }}
                                            </div>
                                            <span class="fw-semibold user-name">{{ $log->user->name ?? 'System' }}</span>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <div class="model-info">
                                            <i class="bi bi-table"></i>
                                            <span class="model-name">{{ $log->model ?? '-' }}</span>
                                            @if ($log->model_id)
                                                <span class="model-id">#{{ $log->model_id }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <span class="ip-badge">
                                            <i class="bi bi-router"></i>
                                            {{ $log->ip_address ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="time-info">
                                            <i class="bi bi-calendar3 d-md-none"></i>
                                            <span class="time-relative">{{ $log->created_at?->diffForHumans() ?? '-' }}</span>
                                            <span
                                                class="time-full d-none d-sm-inline">{{ $log->created_at?->format('H:i, d M Y') ?? '-' }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-audit">
                    <div class="empty-icon">
                        <i class="bi bi-journal-x"></i>
                    </div>
                    <p class="mt-2 mb-0">Belum ada aktivitas</p>
                    <span class="text-muted small">Aktivitas sistem akan muncul di sini</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Recalculate SAW --}}
    <div class="modal fade" id="recalculateModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="modal-icon">
                            <i class="bi bi-arrow-repeat text-warning fs-4"></i>
                        </div>
                        <h6 class="modal-title fw-semibold">Hitung Ulang SAW</h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.recalculate-saw') }}" method="POST" id="recalculateForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning border-0">
                            <div class="d-flex gap-2">
                                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                                <div>
                                    <strong>Perhatian!</strong> Proses ini akan menghitung ulang skor SAW untuk pendaftaran berstatus <em>Diverifikasi</em>, <em>Sedang Dihitung</em>, atau <em>Hasil Awal</em>. Pendaftaran yang sudah <strong>Final UKT / Selesai</strong> serta baris penetapan dengan status <em>Override</em> tidak akan diubah dan dilewati.
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar-range me-1"></i>
                                Pilih Periode
                            </label>
                            <select name="periode_id" class="form-select" id="periodeSelect" required>
                                <option value="">-- Pilih Periode --</option>
                                @foreach (\App\Models\PeriodeSeleksi::orderByDesc('tahun')->get() as $p)
                                    <option value="{{ $p->id }}">
                                        {{ $p->nama }} ({{ ucfirst($p->status) }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle"></i>
                                Pilih periode yang akan dihitung ulang SAW-nya
                            </div>
                        </div>
                        <div class="info-box mt-3">
                            <div class="info-box-icon">
                                <i class="bi bi-database"></i>
                            </div>
                            <div class="info-box-content">
                                <div class="info-box-title">Dampak Proses</div>
                                <ul class="info-box-list">
                                    <li>Menghapus semua data Hasil SAW periode terpilih</li>
                                    <li>Meriset status pendaftaran ke "Diverifikasi"</li>
                                    <li>Menghitung ulang SAW untuk semua pendaftar</li>
                                    <li>Mengupdate ranking dan rekomendasi golongan</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Indikator Progres di dalam Modal --}}
                        <div id="modalRecalcProgressContainer" class="d-none mt-3 p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-semibold text-primary" id="modalRecalcText">
                                    <i class="spinner-border spinner-border-sm me-1"></i> Perhitungan sedang berlangsung di background...
                                </span>
                                <span class="badge bg-primary" id="modalRecalcBadge">0%</span>
                            </div>
                            <div class="progress" style="height: 8px; border-radius: 4px;">
                                <div id="modalRecalcBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 0%;"></div>
                            </div>
                            <div class="small text-muted mt-1" id="modalRecalcSubtext">Harap tunggu hingga seluruh chunk mahasiswa selesai diproses worker.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-cancel-modal" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="submit" class="btn-submit-modal" id="submitRecalculate"
                            onclick="return confirmRecalculate(event)">
                            <i class="bi bi-arrow-repeat"></i> Hitung Ulang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Elegant Stat Cards */
        .stat-card {
            transition: all 0.25s ease;
            cursor: default;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08) !important;
        }

        .stat-icon {
            transition: all 0.25s ease;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.05);
        }

        /* Stats Card */
        .stats-card,
        .period-card,
        .quick-actions-card,
        .audit-card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stats-card:hover,
        .period-card:hover,
        .quick-actions-card:hover,
        .audit-card:hover {
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.08);
        }

        .stats-card-header,
        .period-card-header,
        .quick-actions-header,
        .audit-card-header {
            background: white;
            border-bottom: 1px solid #f0f2f5;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .header-badge {
            background: #f1f5f9;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.65rem;
            color: #64748b;
        }

        .stats-card-footer {
            background: #f8fafc;
            padding: 12px 20px;
            border-top: 1px solid #f0f2f5;
        }

        .stats-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-text {
            font-size: 0.7rem;
            color: #64748b;
        }

        .summary-value {
            font-weight: 700;
            font-size: 0.9rem;
            color: #3b82f6;
        }

        .header-icon {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .purple-icon {
            background: linear-gradient(135deg, #ede9fe, #ddd6fe);
            color: #8b5cf6;
        }

        .green-icon {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #10b981;
        }

        .orange-icon {
            background: linear-gradient(135deg, #fed7aa, #fdba74);
            color: #f59e0b;
        }

        .dark-icon {
            background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
            color: #475569;
        }

        .stats-list {
            padding: 4px 0;
        }

        .stats-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .stats-item:last-child {
            border-bottom: none;
        }

        .stats-item-label {
            font-size: 0.85rem;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stats-item-value {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .warning-value {
            color: #f59e0b;
        }

        .success-value {
            color: #10b981;
        }

        .primary-value {
            color: #3b82f6;
        }

        /* Period Card */
        .period-card {
            position: relative;
        }

        .active-pulse {
            width: 10px;
            height: 10px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse-green 1.5s infinite;
        }

        @keyframes pulse-green {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
            }

            50% {
                box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
            }
        }

        .period-detail {
            padding: 4px 0;
        }

        .period-field {
            margin-bottom: 14px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .period-field:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .period-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: #94a3b8;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .period-value {
            font-size: 0.9rem;
            color: #1e293b;
        }

        .date-range {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        /* Countdown Timer */
        .countdown-timer {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-radius: 16px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            margin-top: 12px;
            border-left: 4px solid #f59e0b;
        }

        .timer-icon {
            width: 44px;
            height: 44px;
            background: rgba(245, 158, 11, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: #f59e0b;
        }

        .timer-info {
            flex: 1;
        }

        .timer-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: #92400e;
            letter-spacing: 0.5px;
        }

        .timer-value {
            font-size: 1.3rem;
            font-weight: 800;
            color: #92400e;
            line-height: 1.2;
        }

        .timer-unit {
            font-size: 0.7rem;
            font-weight: 500;
        }

        /* Info Timer */
        .info-timer {
            background: #f8fafc;
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 12px;
            font-size: 0.8rem;
        }

        .info-upcoming {
            background: #cffafe;
            color: #0e7490;
        }

        .info-upcoming i {
            color: #06b6d4;
            font-size: 1.1rem;
        }

        .info-ended {
            background: #e2e8f0;
            color: #475569;
        }

        .info-ended i {
            color: #64748b;
            font-size: 1.1rem;
        }

        /* Empty Period Enhanced */
        .empty-period-enhanced {
            text-align: center;
            padding: 32px 20px;
        }

        .empty-period-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 2rem;
            color: #94a3b8;
        }

        .empty-period-title {
            font-weight: 700;
            font-size: 1rem;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .empty-period-desc {
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 20px;
        }

        .btn-create-period-enhanced {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 10px 20px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-create-period-enhanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            color: white;
        }

        .btn-action-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            padding: 10px 16px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-action-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
            color: white;
        }

        /* Quick Actions */
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .quick-action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 14px 10px;
            border-radius: 16px;
            text-decoration: none;
            transition: all 0.2s;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .quick-action-btn i {
            font-size: 1.3rem;
        }

        .quick-action-btn span {
            font-size: 0.7rem;
            font-weight: 600;
        }

        .btn-hint {
            font-size: 0.55rem;
            opacity: 0.7;
            margin-top: 2px;
        }

        .primary-btn {
            background: #eff6ff;
            color: #3b82f6;
        }

        .primary-btn:hover {
            background: #3b82f6;
            color: white;
            transform: translateY(-3px);
        }

        .success-btn {
            background: #d1fae5;
            color: #10b981;
        }

        .success-btn:hover {
            background: #10b981;
            color: white;
            transform: translateY(-3px);
        }

        .info-btn {
            background: #cffafe;
            color: #06b6d4;
        }

        .info-btn:hover {
            background: #06b6d4;
            color: white;
            transform: translateY(-3px);
        }

        .excel-btn {
            background: #d1fae5;
            color: #059669;
        }

        .excel-btn:hover {
            background: #059669;
            color: white;
            transform: translateY(-3px);
        }

        .pdf-btn {
            background: #fee2e2;
            color: #dc2626;
        }

        .pdf-btn:hover {
            background: #dc2626;
            color: white;
            transform: translateY(-3px);
        }

        .secondary-btn {
            background: #f1f5f9;
            color: #475569;
        }

        .secondary-btn:hover {
            background: #475569;
            color: white;
            transform: translateY(-3px);
        }

        .recalculate-btn {
            background: #fef3c7;
            color: #d97706;
        }

        .recalculate-btn:hover {
            background: #f59e0b;
            color: white;
            transform: translateY(-3px);
        }

        /* Modal Premium */
        .modal-content {
            border-radius: 24px;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #ffffff, #f8fafc);
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 20px;
        }

        .modal-icon {
            width: 36px;
            height: 36px;
            background: #fef3c7;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .info-box {
            background: #f8fafc;
            border-radius: 16px;
            padding: 16px;
            display: flex;
            gap: 12px;
        }

        .info-box-icon {
            width: 40px;
            height: 40px;
            background: #eef2ff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3b82f6;
        }

        .info-box-content {
            flex: 1;
        }

        .info-box-title {
            font-weight: 700;
            font-size: 0.8rem;
            margin-bottom: 8px;
        }

        .info-box-list {
            margin: 0;
            padding-left: 18px;
            font-size: 0.7rem;
            color: #475569;
        }

        .info-box-list li {
            margin-bottom: 4px;
        }

        .btn-cancel-modal {
            background: #f1f5f9;
            border: none;
            padding: 8px 20px;
            border-radius: 40px;
            color: #475569;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-cancel-modal:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
        }

        .btn-submit-modal {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border: none;
            padding: 8px 20px;
            border-radius: 40px;
            color: white;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-submit-modal:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        /* Audit Table */
        .live-badge {
            background: #fee2e2;
            color: #dc2626;
            padding: 3px 10px;
            border-radius: 40px;
            font-size: 0.6rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .live-badge i {
            font-size: 0.4rem;
        }

        .btn-view-all-logs {
            background: transparent;
            color: #3b82f6;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-view-all-logs:hover {
            color: #2563eb;
            transform: translateX(4px);
        }

        .audit-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .audit-table thead th {
            background: #f8fafc;
            padding: 12px 16px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }

        .audit-table tbody tr {
            transition: all 0.2s;
        }

        .audit-table tbody tr:hover {
            background: #fafcff;
        }

        .audit-table tbody td {
            padding: 12px 16px;
            vertical-align: middle;
        }

        .action-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .action-create {
            background: #d1fae5;
            color: #065f46;
        }

        .action-update {
            background: #dbeafe;
            color: #1e40af;
        }

        .action-delete {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-login {
            background: #cffafe;
            color: #0e7490;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .user-avatar {
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .user-name {
            font-size: 0.85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 120px;
        }

        .model-info {
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .model-name {
            max-width: 100px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .model-id {
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 16px;
            font-size: 0.65rem;
            font-family: monospace;
        }

        .ip-badge {
            font-family: monospace;
            font-size: 0.75rem;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 16px;
        }

        .time-info {
            display: flex;
            flex-direction: column;
            font-size: 0.7rem;
            gap: 2px;
        }

        .time-relative {
            font-weight: 500;
            color: #1e293b;
        }

        .time-full {
            font-size: 0.6rem;
            color: #94a3b8;
        }

        .empty-audit {
            text-align: center;
            padding: 40px 20px;
        }

        /* Animations */
        .audit-row {
            animation: fadeInUp 0.3s ease forwards;
            opacity: 0;
            transform: translateY(10px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .audit-row:nth-child(1) {
            animation-delay: 0.05s;
        }

        .audit-row:nth-child(2) {
            animation-delay: 0.1s;
        }

        .audit-row:nth-child(3) {
            animation-delay: 0.15s;
        }

        .audit-row:nth-child(4) {
            animation-delay: 0.2s;
        }

        .audit-row:nth-child(5) {
            animation-delay: 0.25s;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .stat-value {
                font-size: 1.5rem;
            }

            .stat-icon {
                width: 42px;
                height: 42px;
            }
        }

        @media (max-width: 992px) {
            .stat-value {
                font-size: 1.3rem;
            }

            .stat-icon {
                width: 38px;
                height: 38px;
            }

            .stat-label {
                font-size: 0.6rem;
            }

            .quick-action-btn i {
                font-size: 1.1rem;
            }

            .quick-action-btn span {
                font-size: 0.65rem;
            }
        }

        @media (max-width: 768px) {
            .stat-card {
                padding: 14px;
            }

            .stat-value {
                font-size: 1.2rem;
            }

            .stat-icon {
                width: 34px;
                height: 34px;
            }

            .stat-icon i {
                font-size: 0.9rem;
            }

            .stat-label {
                font-size: 0.55rem;
            }

            .stats-item-label {
                font-size: 0.75rem;
            }

            .stats-item-value {
                font-size: 0.95rem;
            }

            .period-value {
                font-size: 0.8rem;
            }

            .period-label {
                font-size: 0.65rem;
            }

            .quick-action-btn {
                padding: 10px;
            }

            .quick-action-btn i {
                font-size: 1rem;
            }

            .quick-action-btn span {
                font-size: 0.6rem;
            }

            .audit-table thead th {
                font-size: 0.6rem;
                padding: 10px 12px;
            }

            .audit-table tbody td {
                padding: 10px 12px;
            }

            .action-badge {
                padding: 3px 10px;
                font-size: 0.65rem;
            }

            .action-text {
                display: none;
            }

            .action-badge i {
                margin-right: 0;
            }

            .user-name {
                max-width: 80px;
                font-size: 0.75rem;
            }

            .time-relative {
                font-size: 0.65rem;
            }
        }

        @media (max-width: 576px) {
            .stat-card {
                padding: 10px;
            }

            .stat-value {
                font-size: 1rem;
            }

            .stat-icon {
                width: 30px;
                height: 30px;
            }

            .stat-icon i {
                font-size: 0.8rem;
            }

            .stat-label {
                font-size: 0.5rem;
            }

            .stats-card-header,
            .period-card-header,
            .quick-actions-header,
            .audit-card-header {
                padding: 12px 16px;
            }

            .header-icon {
                width: 30px;
                height: 30px;
                font-size: 0.9rem;
            }

            .stats-card-header h6,
            .period-card-header h6,
            .quick-actions-header h6,
            .audit-card-header h6 {
                font-size: 0.8rem;
            }

            .stats-item {
                padding: 8px 0;
            }

            .stats-item-label {
                font-size: 0.7rem;
            }

            .stats-item-value {
                font-size: 0.85rem;
            }

            .period-field {
                margin-bottom: 10px;
                padding-bottom: 8px;
            }

            .period-value {
                font-size: 0.75rem;
            }

            .quick-actions-grid {
                gap: 8px;
            }

            .quick-action-btn {
                padding: 8px;
            }

            .quick-action-btn span {
                font-size: 0.55rem;
            }

            .quick-action-btn i {
                font-size: 0.9rem;
            }

            .btn-action-primary,
            .btn-create-period-enhanced {
                font-size: 0.7rem;
                padding: 6px 12px;
            }

            .audit-table thead th:nth-child(2),
            .audit-table tbody td:nth-child(2) {
                max-width: 80px;
            }

            .user-name {
                max-width: 60px;
                font-size: 0.7rem;
            }

            .user-avatar {
                width: 24px;
                height: 24px;
                font-size: 0.6rem;
            }

            .live-badge {
                font-size: 0.55rem;
                padding: 2px 8px;
            }

            .btn-view-all-logs {
                font-size: 0.7rem;
            }

            .empty-audit {
                padding: 30px 16px;
            }

            .empty-audit .empty-icon {
                width: 48px;
                height: 48px;
                font-size: 1.3rem;
            }

            .info-box {
                flex-direction: column;
                text-align: center;
            }

            .info-box-icon {
                margin: 0 auto;
            }

            .info-box-list {
                text-align: left;
            }

            .empty-period-icon {
                width: 55px;
                height: 55px;
                font-size: 1.5rem;
            }

            .empty-period-title {
                font-size: 0.9rem;
            }

            .empty-period-desc {
                font-size: 0.7rem;
            }
        }

        @media (max-width: 380px) {
            .stat-card {
                padding: 8px;
            }

            .stat-value {
                font-size: 0.9rem;
            }

            .stat-icon {
                width: 28px;
                height: 28px;
            }

            .stat-icon i {
                font-size: 0.75rem;
            }

            .quick-actions-grid {
                grid-template-columns: 1fr;
                gap: 6px;
            }

            .quick-action-btn {
                flex-direction: row;
                justify-content: center;
                gap: 8px;
                padding: 8px;
            }

            .quick-action-btn span {
                font-size: 0.6rem;
            }

            .btn-hint {
                display: none;
            }

            .date-range {
                flex-direction: column;
                gap: 2px;
                align-items: flex-start;
            }

            .date-range i {
                transform: rotate(90deg);
            }

            .user-name {
                display: none;
            }

            .user-info {
                justify-content: center;
            }

            .countdown-timer {
                flex-direction: column;
                text-align: center;
            }

            .timer-icon {
                margin: 0 auto;
            }
        }
    </style>

    <script>
        function confirmRecalculate(event) {
            event.preventDefault();
            const periodeSelect = document.getElementById('periodeSelect');
            const periodeName = periodeSelect.options[periodeSelect.selectedIndex]?.text || 'periode terpilih';

            if (!periodeSelect.value) {
                alert('⚠️ Silakan pilih periode terlebih dahulu!');
                return false;
            }

            swalConfirm(
                `⚠️ PERINGATAN!\n\nAnda akan menghitung ulang SAW untuk periode "${periodeName}".\n\nDampak:\n- Semua hasil SAW akan dihapus\n- Status pendaftaran akan direset\n- Perhitungan akan berjalan ulang\n\nYakin ingin melanjutkan?`
                ).then(function(ok) {
                if (!ok) return;
                document.getElementById('recalculateForm').submit();
            });
            return false;
        }

        // Real-time polling status Recalculate SAW
        let recalcPollInterval = null;
        let isRecalcActive = false;

        function checkRecalculateStatus() {
            if (document.hidden) return; // Menghemat request jika tab sedang tidak aktif

            fetch('{{ route('admin.recalculate-saw.status') }}')
                .then(res => res.json())
                .then(data => {
                    const alertEl = document.getElementById('recalculateProgressAlert');
                    const barEl = document.getElementById('recalcAlertProgressBar');
                    const badgeEl = document.getElementById('recalcAlertBadge');
                    const titleEl = document.getElementById('recalcAlertTitle');
                    const detailEl = document.getElementById('recalcAlertDetail');
                    const spinnerEl = document.getElementById('recalcSpinner');
                    const submitBtn = document.getElementById('submitRecalculate');
                    const modalContainer = document.getElementById('modalRecalcProgressContainer');
                    const modalBar = document.getElementById('modalRecalcBar');
                    const modalBadge = document.getElementById('modalRecalcBadge');

                    if (data.active) {
                        isRecalcActive = true;
                        if (alertEl) alertEl.classList.remove('d-none');

                        const progress = data.progress || 0;
                        if (barEl) barEl.style.width = progress + '%';
                        if (badgeEl) badgeEl.innerText = progress + '% Selesai';

                        if (data.has_batch && data.total_jobs > 0) {
                            if (detailEl) {
                                detailEl.innerText = `${data.processed_jobs} dari ${data.total_jobs} chunk selesai diproses.` +
                                    (data.failed_jobs > 0 ? ` (${data.failed_jobs} gagal)` : '');
                            }
                        } else {
                            if (detailEl) detailEl.innerText = `Menyiapkan antrean pendaftaran...`;
                        }

                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i> Sedang Diproses...';
                        }

                        if (modalContainer) {
                            modalContainer.classList.remove('d-none');
                            if (modalBar) modalBar.style.width = progress + '%';
                            if (modalBadge) modalBadge.innerText = progress + '%';
                        }
                    } else if (isRecalcActive && data.finished) {
                        // Selesai baru saja
                        isRecalcActive = false;
                        if (recalcPollInterval) clearInterval(recalcPollInterval);

                        if (alertEl) {
                            alertEl.classList.remove('d-none', 'alert-info');
                            alertEl.classList.add('alert-success');
                        }
                        if (spinnerEl) spinnerEl.classList.add('d-none');
                        if (barEl) {
                            barEl.classList.remove('bg-primary');
                            barEl.classList.add('bg-success');
                            barEl.style.width = '100%';
                        }
                        if (badgeEl) {
                            badgeEl.classList.remove('bg-primary');
                            badgeEl.classList.add('bg-success');
                            badgeEl.innerText = '100% Selesai';
                        }
                        if (titleEl) titleEl.innerText = 'Perhitungan Ulang SAW Selesai!';
                        if (detailEl) detailEl.innerText = 'Ranking telah diperbarui. Memperbarui halaman dalam 2 detik...';

                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        // Tidak ada recalculate aktif
                        if (alertEl && !isRecalcActive) alertEl.classList.add('d-none');
                        if (submitBtn && submitBtn.disabled) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Hitung Ulang';
                        }
                        if (modalContainer) modalContainer.classList.add('d-none');
                    }
                })
                .catch(err => {
                    console.error('Error saat memeriksa status recalculate SAW:', err);
                });
        }

        // Inisialisasi polling saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            checkRecalculateStatus();
            recalcPollInterval = setInterval(checkRecalculateStatus, 3000);
        });

        // Polling aktif kembali saat tab aktif
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                checkRecalculateStatus();
            }
        });
    </script>
@endsection
