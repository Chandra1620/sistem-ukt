@extends('layouts.app')
@section('title', 'Detail Periode Seleksi')
@section('page-title', 'Detail Periode Seleksi')

@section('content')
    <div class="row g-4">

        {{-- Info Periode --}}
        <div class="col-lg-4 col-md-12">
            <div class="card premium-card">
                <div class="card-header premium-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon primary-icon">
                            <i class="bi bi-calendar-event-fill"></i>
                        </div>
                        <h5 class="fw-semibold mb-0">Info Periode</h5>
                    </div>
                    <a href="{{ route('admin.periode.edit', $periode) }}" class="btn-edit">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="info-list">
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-tag"></i></div>
                            <div class="info-content">
                                <div class="info-label">Nama Periode</div>
                                <div class="info-value">{{ $periode->nama }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-calendar-year"></i></div>
                            <div class="info-content">
                                <div class="info-label">Tahun Akademik</div>
                                <div class="info-value">{{ $periode->tahun }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-calendar-plus"></i></div>
                            <div class="info-content">
                                <div class="info-label">Tanggal Mulai</div>
                                <div class="info-value">{{ $periode->tanggal_mulai->format('d F Y') }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-calendar-minus"></i></div>
                            <div class="info-content">
                                <div class="info-label">Tanggal Selesai</div>
                                <div class="info-value">{{ $periode->tanggal_selesai->format('d F Y') }}</div>
                                @if (\Carbon\Carbon::now()->gt($periode->tanggal_selesai))
                                    <div class="small text-danger mt-2">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Periode sudah berakhir
                                    </div>
                                @elseif(\Carbon\Carbon::now()->lt($periode->tanggal_mulai))
                                    <div class="small text-info mt-2">
                                        <i class="bi bi-hourglass-split"></i>
                                        Mulai dalam {{ \Carbon\Carbon::now()->diffInDays($periode->tanggal_mulai) }} hari
                                    </div>
                                @else
                                    <div class="small text-success mt-2">
                                        <i class="bi bi-clock-history"></i>
                                        Sisa {{ $periode->sisa_hari }} hari
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-people-fill"></i></div>
                            <div class="info-content">
                                <div class="info-label">Total Pendaftar</div>
                                <div class="info-value">
                                    <span class="fw-bold text-primary fs-3">{{ $periode->pendaftaran()->count() }}</span>
                                    <span class="text-muted small ms-1">mahasiswa</span>
                                </div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-circle-fill"></i></div>
                            <div class="info-content">
                                <div class="info-label">Status Periode</div>
                                <div class="info-value">
                                    @php $statusReal = $periode->status_real; @endphp
                                    @if ($statusReal === 'aktif')
                                        <span class="badge-status badge-aktif">
                                            <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                        </span>
                                    @elseif($statusReal === 'selesai')
                                        <span class="badge-status badge-selesai">
                                            <i class="bi bi-flag-checkered me-1"></i> Selesai
                                        </span>
                                    @elseif($statusReal === 'belum_mulai')
                                        <span class="badge-status badge-belum-mulai">
                                            <i class="bi bi-clock me-1"></i> Belum Mulai
                                        </span>
                                    @else
                                        <span class="badge-status badge-nonaktif">
                                            <i class="bi bi-pause-circle-fill me-1"></i> Nonaktif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent pb-4 px-4">
                    <a href="{{ route('admin.periode.index') }}" class="btn-back">
                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Periode
                    </a>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-8 col-md-12">
            {{-- Kriteria SAW --}}
            <div class="card premium-card mb-4">
                <div class="card-header premium-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon success-icon">
                            <i class="bi bi-sliders2"></i>
                        </div>
                        <h5 class="fw-semibold mb-0">Kriteria SAW</h5>
                    </div>
                    <a href="{{ route('admin.kriteria.create') }}" class="btn-add">
                        <i class="bi bi-plus-circle"></i> Tambah Kriteria
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($kriteria->count() > 0)
                        <div class="table-responsive">
                            <table class="table premium-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Kriteria</th>
                                        <th>Bobot</th>
                                        <th>Tipe</th>
                                        <th>Sub Kriteria</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalBobot = 0; @endphp
                                    @foreach ($kriteria as $k)
                                        @php $totalBobot += $k->bobot; @endphp
                                        <tr>
                                            <td><span class="kode-badge">{{ $k->kode }}</span></td>
                                            <td class="fw-semibold">{{ $k->nama }}</td>
                                            <td>
                                                <div class="bobot-wrapper">
                                                    <div class="progress bobot-progress">
                                                        <div class="progress-bar bg-success"
                                                            style="width: {{ $k->bobot }}%"></div>
                                                    </div>
                                                    <span class="bobot-value">{{ $k->bobot }}%</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($k->tipe === 'cost')
                                                    <span class="badge-tipe badge-cost">Cost</span>
                                                @else
                                                    <span class="badge-tipe badge-benefit">Benefit</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.sub-kriteria.index', $k) }}" class="sub-count">
                                                    <i class="bi bi-folder2-open"></i>
                                                    {{ $k->subKriteria->count() }} sub kriteria
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="fw-bold">Total Bobot</td>
                                        <td colspan="3">
                                            @if ($totalBobot == 100)
                                                <div class="total-bobot-success">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                    <span>{{ $totalBobot }}% - Lengkap</span>
                                                </div>
                                            @else
                                                <div class="total-bobot-warning">
                                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                                    <span>{{ $totalBobot }}% - Harus 100%</span>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="bi bi-sliders2"></i>
                            </div>
                            <h6>Belum Ada Kriteria SAW</h6>
                            <p class="text-muted">Tambahkan kriteria untuk melakukan penilaian UKT</p>
                            <a href="{{ route('admin.kriteria.create') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Kriteria
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Golongan UKT --}}
            <div class="card premium-card mb-4">
                <div class="card-header premium-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon warning-icon">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <h5 class="fw-semibold mb-0">Golongan UKT</h5>
                    </div>
                    <a href="{{ route('admin.golongan-ukt.index') }}" class="btn-add">
                        <i class="bi bi-gear"></i> Kelola Golongan
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($golonganUkt->count() > 0)
                        <div class="table-responsive">
                            <table class="table premium-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Golongan</th>
                                        <th>Nominal UKT</th>
                                        <th>Skor Minimal</th>
                                        <th>Skor Maksimal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($golonganUkt as $g)
                                        <tr>
                                            <td>
                                                <div class="golongan-badge golongan-{{ $g->golongan }}">
                                                    Golongan {{ $g->golongan }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="nominal-ukt">
                                                    <i class="bi bi-cash-stack"></i>
                                                    Rp {{ number_format($g->nominal, 0, ',', '.') }}
                                                </div>
                                            </td>
                                            <td class="text-muted">{{ $g->skor_min ?? 'Tidak ditentukan' }}</td>
                                            <td class="text-muted">{{ $g->skor_max ?? 'Tidak ditentukan' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="bi bi-cash-coin"></i>
                            </div>
                            <h6>Belum Ada Golongan UKT</h6>
                            <p class="text-muted">Tentukan golongan UKT untuk seleksi</p>
                            <a href="{{ route('admin.golongan-ukt.index') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Golongan
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Program Studi --}}
            <div class="card premium-card mb-4">
                <div class="card-header premium-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon info-icon">
                            <i class="bi bi-mortarboard"></i>
                        </div>
                        <h5 class="fw-semibold mb-0">Program Studi</h5>
                    </div>
                    <a href="{{ route('admin.program-studi.index') }}" class="btn-add">
                        <i class="bi bi-gear"></i> Kelola Prodi
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($programStudi->count() > 0)
                        <div class="table-responsive">
                            <table class="table premium-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Program Studi</th>
                                        <th>Jenjang</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($programStudi as $p)
                                        <tr>
                                            <td><span class="kode-prodi">{{ $p->kode }}</span></td>
                                            <td class="fw-semibold">{{ $p->nama }}</td>
                                            <td><span class="jenjang-badge">{{ $p->jenjang }}</span></td>
                                            <td>
                                                @if ($p->aktif)
                                                    <span class="status-badge status-aktif">
                                                        <i class="bi bi-check-circle-fill"></i> Aktif
                                                    </span>
                                                @else
                                                    <span class="status-badge status-nonaktif">
                                                        <i class="bi bi-x-circle-fill"></i> Nonaktif
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="bi bi-mortarboard"></i>
                            </div>
                            <h6>Belum Ada Program Studi</h6>
                            <p class="text-muted">Tambahkan program studi yang tersedia</p>
                            <a href="{{ route('admin.program-studi.index') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Prodi
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Jenis Dokumen --}}
            <div class="card premium-card">
                <div class="card-header premium-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon danger-icon">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <h5 class="fw-semibold mb-0">Persyaratan Dokumen</h5>
                    </div>
                    <a href="{{ route('admin.jenis-dokumen.index') }}" class="btn-add">
                        <i class="bi bi-gear"></i> Kelola Dokumen
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($jenisDokumen->count() > 0)
                        <div class="table-responsive">
                            <table class="table premium-table mb-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Dokumen</th>
                                        <th>Sifat</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jenisDokumen as $j)
                                        <tr>
                                            <td class="text-muted">{{ $j->urutan }}</td>
                                            <td class="fw-semibold">{{ $j->nama }}</td>
                                            <td>
                                                @if ($j->wajib)
                                                    <span class="sifat-badge sifat-wajib">
                                                        <i class="bi bi-star-fill"></i> Wajib
                                                    </span>
                                                @else
                                                    <span class="sifat-badge sifat-opsional">
                                                        <i class="bi bi-hand-index-thumb"></i> Opsional
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($j->aktif)
                                                    <span class="status-badge status-aktif">
                                                        <i class="bi bi-check-circle-fill"></i> Aktif
                                                    </span>
                                                @else
                                                    <span class="status-badge status-nonaktif">
                                                        <i class="bi bi-x-circle-fill"></i> Nonaktif
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <h6>Belum Ada Jenis Dokumen</h6>
                            <p class="text-muted">Tentukan dokumen yang diperlukan untuk pendaftaran</p>
                            <a href="{{ route('admin.jenis-dokumen.index') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Dokumen
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
            --dark: #1e293b;
            --light: #f8fafc;
            --border: #e2e8f0;
        }

        /* Premium Card */
        .premium-card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
        }

        .premium-card:hover {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        /* Card Header */
        .premium-card-header {
            background: linear-gradient(135deg, #ffffff 0%, #fafcff 100%);
            border-bottom: 2px solid var(--border);
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .header-icon {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            transition: all 0.3s ease;
        }

        .primary-icon {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: var(--primary);
        }

        .success-icon {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: var(--success);
        }

        .warning-icon {
            background: linear-gradient(135deg, #fed7aa, #fde68a);
            color: var(--warning);
        }

        .info-icon {
            background: linear-gradient(135deg, #cffafe, #a5f3fc);
            color: var(--info);
        }

        .danger-icon {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: var(--danger);
        }

        /* Buttons */
        .btn-edit,
        .btn-add {
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            padding: 6px 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
        }

        .btn-edit {
            background: white;
            border: 1.5px solid var(--warning);
            color: var(--warning);
        }

        .btn-edit:hover {
            background: var(--warning);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .btn-add {
            background: white;
            border: 1.5px solid var(--success);
            color: var(--success);
        }

        .btn-add:hover {
            background: var(--success);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--light);
            color: var(--dark);
            padding: 10px 24px;
            border-radius: 40px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: var(--border);
            transform: translateX(-5px);
            color: var(--primary);
        }

        /* Info List */
        .info-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }

        .info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--light), #ffffff);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.2rem;
            flex-shrink: 0;
            border: 1px solid var(--border);
        }

        .info-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 700;
            color: var(--dark);
        }

        /* Badge Status */
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-aktif {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-selesai {
            background: #e2e8f0;
            color: #475569;
        }

        .badge-belum-mulai {
            background: #cffafe;
            color: #0e7490;
        }

        .badge-nonaktif {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Tables */
        .premium-table {
            margin-bottom: 0;
        }

        .premium-table thead th {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 14px 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            border-bottom: 2px solid var(--border);
        }

        .premium-table tbody td {
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            font-size: 0.85rem;
        }

        .premium-table tfoot td {
            padding: 14px 20px;
            background: #f8fafc;
            font-weight: 600;
        }

        /* Kode Badge */
        .kode-badge {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 700;
            font-family: monospace;
            display: inline-block;
        }

        /* Bobot Progress */
        .bobot-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .bobot-progress {
            flex: 1;
            height: 8px;
            border-radius: 10px;
            background: #e2e8f0;
            overflow: hidden;
        }

        .bobot-value {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--success);
            min-width: 45px;
        }

        /* Badge Tipe */
        .badge-tipe {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .badge-cost {
            background: #fee2e2;
            color: #dc2626;
        }

        .badge-benefit {
            background: #d1fae5;
            color: #059669;
        }

        /* Sub Count */
        .sub-count {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            background: #e0e7ff;
            color: var(--primary);
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .sub-count:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-1px);
        }

        /* Total Bobot */
        .total-bobot-success,
        .total-bobot-warning {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .total-bobot-success {
            background: #d1fae5;
            color: #065f46;
        }

        .total-bobot-warning {
            background: #fed7aa;
            color: #92400e;
        }

        /* Golongan Badge */
        .golongan-badge {
            display: inline-flex;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .golongan-1 {
            background: #fef3c7;
            color: #92400e;
        }

        .golongan-2 {
            background: #dbeafe;
            color: #1e40af;
        }

        .golongan-3 {
            background: #d1fae5;
            color: #065f46;
        }

        .golongan-4 {
            background: #e0e7ff;
            color: #3730a3;
        }

        .golongan-5 {
            background: #fce7f3;
            color: #9d174d;
        }

        /* Nominal UKT */
        .nominal-ukt {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 700;
            color: var(--warning);
        }

        .nominal-ukt i {
            font-size: 0.9rem;
        }

        /* Kode Prodi */
        .kode-prodi {
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--dark);
        }

        /* Jenjang Badge */
        .jenjang-badge {
            display: inline-block;
            padding: 4px 10px;
            background: linear-gradient(135deg, #c7d2fe, #a5b4fc);
            color: #3730a3;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .status-aktif {
            background: #d1fae5;
            color: #065f46;
        }

        .status-nonaktif {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Sifat Badge */
        .sifat-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .sifat-wajib {
            background: #fee2e2;
            color: #dc2626;
        }

        .sifat-opsional {
            background: #e2e8f0;
            color: #475569;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 48px 24px;
        }

        .empty-state-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--light), #ffffff);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2.5rem;
            color: #94a3b8;
            border: 1px solid var(--border);
        }

        .empty-state h6 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--dark);
        }

        .empty-state p {
            font-size: 0.85rem;
            margin-bottom: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .premium-card-header {
                flex-direction: column;
                text-align: center;
            }

            .info-item {
                flex-direction: column;
                text-align: center;
            }

            .info-icon {
                margin: 0 auto;
            }

            .bobot-wrapper {
                flex-direction: column;
                align-items: flex-start;
            }

            .premium-table thead th,
            .premium-table tbody td {
                padding: 10px 12px;
                font-size: 0.75rem;
            }

            .empty-state {
                padding: 32px 20px;
            }

            .empty-state-icon {
                width: 60px;
                height: 60px;
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .premium-table thead {
                display: none;
            }

            .premium-table tbody tr {
                display: block;
                margin-bottom: 16px;
                border: 1px solid var(--border);
                border-radius: 12px;
                padding: 12px;
            }

            .premium-table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
                border: none;
                border-bottom: 1px solid var(--border);
            }

            .premium-table tbody td:last-child {
                border-bottom: none;
            }

            .premium-table tbody td::before {
                content: attr(data-label);
                font-weight: 700;
                font-size: 0.7rem;
                color: #64748b;
            }
        }
    </style>
@endsection
