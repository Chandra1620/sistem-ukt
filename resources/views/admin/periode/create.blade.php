@extends('layouts.app')
@section('title', 'Tambah Periode')
@section('page-title', 'Tambah Periode Seleksi')

@section('content')
    <div class="page-header-wrapper">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="bi bi-calendar-plus"></i>
                </div>
                <div class="header-text">
                    <h1 class="header-title">Tambah Periode Seleksi</h1>
                    <p class="header-subtitle">Buat periode baru untuk penerimaan mahasiswa baru dan seleksi UKT</p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.periode.index') }}" class="btn btn-outline-back">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    {{-- @if (session('error'))
        <div class="alert-premium alert-premium-danger">
            <div class="alert-icon">
                <i class="bi bi-exclamation-circle-fill"></i>
            </div>
            <div class="alert-content">
                <span class="alert-title">Terjadi Kesalahan</span>
                <span class="alert-message">{{ session('error') }}</span>
            </div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif --}}

    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-header-left">
                <i class="bi bi-calendar3"></i>
                <span>Form Periode Seleksi</span>
            </div>
            <span class="form-card-badge">Baru</span>
        </div>

        <div class="form-card-body">
            <div class="info-alert">
                <div class="info-alert-icon">
                    <i class="bi bi-info-circle-fill"></i>
                </div>
                <div class="info-alert-content">
                    <span class="info-alert-title">Informasi Periode Baru</span>
                    <span class="info-alert-text">
                        Periode baru akan dibuat dengan status <strong>Nonaktif</strong> secara otomatis.
                        Kriteria dan sub kriteria akan disalin dari periode terakhir.
                        Aktifkan melalui menu <strong>Edit</strong> setelah periode siap digunakan.
                    </span>
                </div>
            </div>

            <form action="{{ route('admin.periode.store') }}" method="POST" id="periodeForm">
                @csrf

                <div class="form-row">
                    <div class="form-group form-group-half">
                        <label class="form-label">
                            <span class="label-text">Nama Periode</span>
                            <span class="label-required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="bi bi-tag-fill input-icon"></i>
                            <input type="text" name="nama" class="form-input @error('nama') is-error @enderror"
                                value="{{ old('nama') }}" placeholder="Contoh: Penerimaan Mahasiswa Baru 2026"
                                id="namaPeriode" autofocus>
                            @error('nama')
                                <div class="input-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-hint">
                            <i class="bi bi-lightbulb-fill"></i>
                            Gunakan nama yang jelas dan mudah diingat
                        </div>
                    </div>

                    <div class="form-group form-group-half">
                        <label class="form-label">
                            <span class="label-text">Tahun</span>
                            <span class="label-required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="bi bi-calendar-year input-icon"></i>
                            <input type="number" name="tahun" class="form-input @error('tahun') is-error @enderror"
                                value="{{ old('tahun', date('Y')) }}" min="2020" max="2100" id="tahunPeriode">
                            @error('tahun')
                                <div class="input-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-group-half">
                        <label class="form-label">
                            <span class="label-text">Tanggal Mulai</span>
                            <span class="label-required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="bi bi-calendar-plus input-icon"></i>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                class="form-input @error('tanggal_mulai') is-error @enderror"
                                value="{{ old('tanggal_mulai') }}" min="{{ date('Y-m-d') }}">
                            @error('tanggal_mulai')
                                <div class="input-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-hint">
                            <i class="bi bi-clock-fill"></i>
                            Minimal hari ini ({{ \Carbon\Carbon::today()->translatedFormat('d F Y') }})
                        </div>
                    </div>

                    <div class="form-group form-group-half">
                        <label class="form-label">
                            <span class="label-text">Tanggal Selesai</span>
                            <span class="label-required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="bi bi-calendar-minus input-icon"></i>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                                class="form-input @error('tanggal_selesai') is-error @enderror"
                                value="{{ old('tanggal_selesai') }}">
                            @error('tanggal_selesai')
                                <div class="input-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-hint" id="dateHint">
                            <i class="bi bi-info-circle-fill"></i>
                            Harus setelah tanggal mulai
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-group-half">
                        <label class="form-label">
                            <span class="label-text">Status</span>
                        </label>
                        <div class="status-display">
                            <span class="status-badge status-inactive">
                                <span class="status-dot"></span>
                                Nonaktif
                            </span>
                            <span class="status-hint">(akan diaktifkan nanti)</span>
                        </div>
                    </div>
                </div>

                {{-- Pilihan mode kriteria --}}
                <div class="config-section">
                    <div class="config-header">
                        <i class="bi bi-gear"></i>
                        <span>Konfigurasi Kriteria SAW</span>
                    </div>

                    <div class="config-options">
                        <div class="config-option">
                            <input class="config-radio" type="radio" name="salin_mode" id="mode_salin" value="salin"
                                checked onchange="toggleSalinDari(this.value)">
                            <label class="config-label" for="mode_salin">
                                <div class="config-label-icon">
                                    <i class="bi bi-copy"></i>
                                </div>
                                <div class="config-label-content">
                                    <span class="config-label-title">Salin dari periode sebelumnya</span>
                                    <span class="config-label-desc">Menyalin seluruh kriteria, bobot, dan sub kriteria dari
                                        periode terakhir</span>
                                </div>
                            </label>
                        </div>

                        <div class="config-option">
                            <input class="config-radio" type="radio" name="salin_mode" id="mode_default"
                                value="default" onchange="toggleSalinDari(this.value)">
                            <label class="config-label" for="mode_default">
                                <div class="config-label-icon">
                                    <i class="bi bi-grid-3x3-gap-fill"></i>
                                </div>
                                <div class="config-label-content">
                                    <span class="config-label-title">Gunakan 12 kriteria default</span>
                                    <span class="config-label-desc">Membuat 12 kriteria standar tanpa sub kriteria</span>
                                </div>
                            </label>
                        </div>

                        <div class="config-option">
                            <input class="config-radio" type="radio" name="salin_mode" id="mode_kosong"
                                value="kosong" onchange="toggleSalinDari(this.value)">
                            <label class="config-label" for="mode_kosong">
                                <div class="config-label-icon">
                                    <i class="bi bi-file-earmark-plus"></i>
                                </div>
                                <div class="config-label-content">
                                    <span class="config-label-title">Mulai dari kosong</span>
                                    <span class="config-label-desc">Membuat periode tanpa kriteria (isi manual
                                        nanti)</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Dropdown pilih periode sumber --}}
                    <div id="salin_dari_wrapper" class="salin-dari-wrapper">
                        <div class="salin-dari-header">
                            <i class="bi bi-arrow-down-circle"></i>
                            <span>Pilih Periode Sumber</span>
                        </div>
                        <div class="select-wrapper">
                            <i class="bi bi-chevron-down select-icon"></i>
                            <select name="salin_dari" class="form-input form-select" id="salinDariSelect">
                                <option value="">-- Pilih Periode Sumber --</option>
                                @foreach (\App\Models\PeriodeSeleksi::orderByDesc('tahun')->get() as $p)
                                    <option value="{{ $p->id }}">
                                        {{ $p->nama }} ({{ $p->tahun }})
                                        @if ($p->status === 'aktif')
                                            ★ Aktif
                                        @endif
                                        - {{ $p->kriteria->count() }} kriteria
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="salin-dari-hint">
                            <i class="bi bi-info-circle-fill"></i>
                            Seluruh kriteria, bobot, dan sub kriteria akan disalin sebagai data baru.
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-submit" id="submitBtn">
                        <i class="bi bi-save-fill"></i>
                        <span>Simpan Periode</span>
                    </button>
                    <a href="{{ route('admin.periode.index') }}" class="btn btn-outline-cancel">
                        <i class="bi bi-x-lg"></i>
                        <span>Batal</span>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* PAGE HEADER */
        /* ============================================ */
        .page-header-wrapper {
            margin-bottom: 32px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            flex-shrink: 0;
        }

        .header-text {
            display: flex;
            flex-direction: column;
        }

        .header-title {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        .header-subtitle {
            font-size: 14px;
            color: #64748b;
            margin: 4px 0 0 0;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-outline-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 40px;
            border: 1.5px solid #e2e8f0;
            background: white;
            color: #475569;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-outline-back:hover {
            border-color: #3b82f6;
            color: #3b82f6;
            background: #f8faff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
        }

        /* ============================================ */
        /* ALERT PREMIUM */
        /* ============================================ */
        .alert-premium {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 16px 20px;
            border-radius: 16px;
            margin-bottom: 24px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            position: relative;
        }

        .alert-premium-danger {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .alert-premium .alert-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #fee2e2;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #dc2626;
            font-size: 18px;
            flex-shrink: 0;
        }

        .alert-premium .alert-content {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .alert-premium .alert-title {
            font-weight: 600;
            color: #991b1b;
            font-size: 14px;
        }

        .alert-premium .alert-message {
            color: #7f1d1d;
            font-size: 13px;
            margin-top: 2px;
        }

        .alert-premium .alert-close {
            background: none;
            border: none;
            color: #991b1b;
            cursor: pointer;
            padding: 4px;
            font-size: 14px;
            opacity: 0.6;
            transition: opacity 0.2s ease;
            flex-shrink: 0;
            margin-top: 4px;
        }

        .alert-premium .alert-close:hover {
            opacity: 1;
        }

        /* ============================================ */
        /* FORM CARD - FULL WIDTH */
        /* ============================================ */
        .form-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            border: 1px solid #f1f5f9;
            overflow: hidden;
            width: 100%;
        }

        .form-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 28px;
            border-bottom: 1px solid #f1f5f9;
            background: #fafcff;
        }

        .form-card-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: #0f172a;
            font-size: 15px;
        }

        .form-card-header-left i {
            color: #3b82f6;
            font-size: 18px;
        }

        .form-card-badge {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            padding: 4px 14px;
            border-radius: 40px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .form-card-body {
            padding: 32px 28px 36px;
        }

        /* ============================================ */
        /* INFO ALERT */
        /* ============================================ */
        .info-alert {
            display: flex;
            gap: 14px;
            padding: 16px 20px;
            background: #eff6ff;
            border-radius: 14px;
            border: 1px solid #bfdbfe;
            margin-bottom: 32px;
        }

        .info-alert-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #bfdbfe;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563eb;
            font-size: 18px;
            flex-shrink: 0;
        }

        .info-alert-content {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .info-alert-title {
            font-weight: 600;
            color: #1e3a8a;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .info-alert-text {
            font-size: 13px;
            color: #1e40af;
            line-height: 1.5;
        }

        .info-alert-text strong {
            color: #1e3a8a;
        }

        /* ============================================ */
        /* FORM GROUP */
        /* ============================================ */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 4px;
            font-weight: 600;
            font-size: 14px;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .label-required {
            color: #ef4444;
            font-weight: 700;
        }

        .label-text {
            color: #0f172a;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
            pointer-events: none;
            transition: color 0.3s ease;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px 12px 44px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            color: #0f172a;
            background: #fafcff;
            transition: all 0.3s ease;
            outline: none;
            font-family: inherit;
        }

        .form-input:focus {
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.08);
        }

        .form-input:focus+.input-icon,
        .form-input:focus~.input-icon {
            color: #3b82f6;
        }

        .form-input.is-error {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .form-input.is-error:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.08);
        }

        .form-input::placeholder {
            color: #94a3b8;
        }

        .form-select {
            appearance: none;
            padding-right: 40px;
            cursor: pointer;
        }

        .input-error {
            color: #dc2626;
            font-size: 13px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .input-error::before {
            content: "⚠";
        }

        .form-hint {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #94a3b8;
            margin-top: 6px;
        }

        .form-hint i {
            font-size: 13px;
        }

        /* ============================================ */
        /* FORM ROW - GRID 2 KOLOM */
        /* ============================================ */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .form-group-half {
            margin-bottom: 0;
        }

        /* ============================================ */
        /* STATUS DISPLAY */
        /* ============================================ */
        .status-display {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            min-height: 50px;
            margin-top: 2px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 40px;
            font-size: 13px;
            font-weight: 600;
        }

        .status-inactive {
            background: #f1f5f9;
            color: #475569;
        }

        .status-inactive .status-dot {
            background: #94a3b8;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-hint {
            font-size: 12px;
            color: #94a3b8;
        }

        /* ============================================ */
        /* CONFIG SECTION */
        /* ============================================ */
        .config-section {
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid #f1f5f9;
        }

        .config-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 14px;
            color: #0f172a;
            margin-bottom: 16px;
        }

        .config-header i {
            color: #3b82f6;
            font-size: 16px;
        }

        .config-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 16px;
        }

        .config-option {
            position: relative;
        }

        .config-radio {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .config-radio:checked+.config-label {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .config-radio:checked+.config-label .config-label-icon {
            background: #3b82f6;
            color: white;
        }

        .config-label {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #fafcff;
        }

        .config-label:hover {
            border-color: #c4b5fd;
            background: #f5f3ff;
        }

        .config-label-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 18px;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .config-label-content {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .config-label-title {
            font-weight: 600;
            font-size: 14px;
            color: #0f172a;
        }

        .config-label-desc {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 1px;
        }

        /* ============================================ */
        /* SALIN DARI WRAPPER */
        /* ============================================ */
        .salin-dari-wrapper {
            margin-top: 16px;
            padding: 16px 20px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            display: block;
        }

        .salin-dari-wrapper.hidden {
            display: none;
        }

        .salin-dari-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 13px;
            color: #0f172a;
            margin-bottom: 10px;
        }

        .salin-dari-header i {
            color: #3b82f6;
            font-size: 16px;
        }

        .select-wrapper {
            position: relative;
        }

        .select-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 12px;
            pointer-events: none;
        }

        .salin-dari-wrapper .form-select {
            padding: 10px 14px;
            padding-right: 40px;
            background: white;
        }

        .salin-dari-hint {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #94a3b8;
            margin-top: 8px;
        }

        .salin-dari-hint i {
            font-size: 13px;
            color: #3b82f6;
        }

        /* ============================================ */
        /* FORM ACTIONS */
        /* ============================================ */
        .form-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid #f1f5f9;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 32px;
            border-radius: 40px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-family: inherit;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary i {
            font-size: 16px;
        }

        .btn-outline-cancel {
            background: transparent;
            color: #64748b;
            border: 1.5px solid #e2e8f0;
        }

        .btn-outline-cancel:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        /* ============================================ */
        /* RESPONSIVE */
        /* ============================================ */

        /* Tablet */
        @media (max-width: 992px) {
            .header-title {
                font-size: 20px;
            }

            .header-icon {
                width: 48px;
                height: 48px;
                font-size: 20px;
            }

            .form-card-body {
                padding: 24px 20px 28px;
            }

            .form-card-header {
                padding: 16px 20px;
            }

            .form-row {
                gap: 18px;
            }
        }

        /* Mobile */
        @media (max-width: 768px) {
            .page-header-wrapper {
                margin-bottom: 24px;
            }

            .header-content {
                flex-direction: column;
                align-items: stretch;
            }

            .header-left {
                gap: 12px;
            }

            .header-icon {
                width: 44px;
                height: 44px;
                font-size: 18px;
                border-radius: 12px;
            }

            .header-title {
                font-size: 18px;
            }

            .header-subtitle {
                font-size: 13px;
            }

            .header-actions {
                width: 100%;
            }

            .btn-outline-back {
                width: 100%;
                justify-content: center;
                padding: 10px 16px;
                font-size: 13px;
            }

            .form-card-header {
                padding: 14px 16px;
            }

            .form-card-header-left {
                font-size: 14px;
            }

            .form-card-body {
                padding: 20px 16px 24px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .form-group-half {
                margin-bottom: 20px;
            }

            .form-group-half:last-child {
                margin-bottom: 0;
            }

            .form-input {
                padding: 11px 12px 11px 40px;
                font-size: 14px;
            }

            .input-icon {
                left: 12px;
                font-size: 14px;
            }

            .info-alert {
                flex-direction: column;
                gap: 10px;
                padding: 14px 16px;
                margin-bottom: 24px;
            }

            .info-alert-icon {
                width: 32px;
                height: 32px;
                font-size: 16px;
            }

            .info-alert-title {
                font-size: 13px;
            }

            .info-alert-text {
                font-size: 12px;
            }

            .status-display {
                padding: 8px 12px;
                min-height: 44px;
                flex-wrap: wrap;
            }

            .config-options {
                gap: 8px;
            }

            .config-label {
                padding: 10px 14px;
            }

            .config-label-icon {
                width: 36px;
                height: 36px;
                font-size: 16px;
            }

            .config-label-title {
                font-size: 13px;
            }

            .config-label-desc {
                font-size: 11px;
            }

            .salin-dari-wrapper {
                padding: 12px 16px;
            }

            .form-actions {
                flex-direction: column-reverse;
                gap: 10px;
                padding-top: 20px;
                margin-top: 20px;
            }

            .form-actions .btn {
                width: 100%;
                justify-content: center;
                padding: 12px 20px;
                font-size: 14px;
            }

            .alert-premium {
                padding: 14px 16px;
                gap: 12px;
                flex-wrap: wrap;
            }

            .alert-premium .alert-icon {
                width: 32px;
                height: 32px;
                font-size: 16px;
            }
        }

        /* Small Mobile */
        @media (max-width: 480px) {
            .header-icon {
                width: 40px;
                height: 40px;
                font-size: 16px;
                border-radius: 10px;
            }

            .header-title {
                font-size: 16px;
            }

            .header-subtitle {
                font-size: 12px;
            }

            .form-card-header {
                padding: 12px 14px;
            }

            .form-card-header-left {
                font-size: 13px;
                gap: 8px;
            }

            .form-card-header-left i {
                font-size: 16px;
            }

            .form-card-badge {
                font-size: 10px;
                padding: 3px 10px;
            }

            .form-card-body {
                padding: 16px 14px 20px;
            }

            .form-group {
                margin-bottom: 16px;
            }

            .form-group-half {
                margin-bottom: 16px;
            }

            .form-label {
                font-size: 13px;
            }

            .form-input {
                padding: 10px 10px 10px 38px;
                font-size: 13px;
                border-radius: 10px;
            }

            .input-icon {
                left: 10px;
                font-size: 13px;
            }

            .form-hint {
                font-size: 11px;
            }

            .status-badge {
                font-size: 12px;
                padding: 4px 12px;
            }

            .status-hint {
                font-size: 11px;
            }

            .config-label {
                padding: 8px 12px;
                gap: 10px;
            }

            .config-label-icon {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }

            .btn {
                font-size: 13px;
                padding: 10px 16px;
            }

            .form-actions .btn {
                padding: 10px 16px;
                font-size: 13px;
            }
        }

        /* Very Small */
        @media (max-width: 360px) {
            .form-card-body {
                padding: 12px 10px 16px;
            }

            .form-input {
                padding: 8px 8px 8px 34px;
                font-size: 12px;
            }

            .input-icon {
                left: 8px;
                font-size: 12px;
            }

            .status-display {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .config-label {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
            }

            .config-label-icon {
                width: 28px;
                height: 28px;
                font-size: 12px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mulaiInput = document.getElementById('tanggal_mulai');
            const selesaiInput = document.getElementById('tanggal_selesai');
            const dateHint = document.getElementById('dateHint');
            const submitBtn = document.getElementById('submitBtn');
            const form = document.getElementById('periodeForm');
            const salinDariWrapper = document.getElementById('salin_dari_wrapper');

            // Validasi frontend: tanggal selesai harus setelah tanggal mulai
            function validateDates() {
                const mulai = mulaiInput.value;
                const selesai = selesaiInput.value;

                if (mulai && selesai) {
                    if (selesai <= mulai) {
                        selesaiInput.classList.add('is-error');
                        dateHint.innerHTML =
                            '<i class="bi bi-exclamation-circle-fill"></i> Tanggal selesai harus setelah tanggal mulai';
                        dateHint.style.color = '#dc2626';
                        return false;
                    } else {
                        selesaiInput.classList.remove('is-error');
                        dateHint.innerHTML = '<i class="bi bi-check-circle-fill"></i> Tanggal valid';
                        dateHint.style.color = '#059669';
                        return true;
                    }
                }

                if (selesai && !mulai) {
                    dateHint.innerHTML =
                        '<i class="bi bi-info-circle-fill"></i> Pilih tanggal mulai terlebih dahulu';
                    dateHint.style.color = '#94a3b8';
                } else {
                    dateHint.innerHTML = '<i class="bi bi-info-circle-fill"></i> Harus setelah tanggal mulai';
                    dateHint.style.color = '#94a3b8';
                }

                return true;
            }

            mulaiInput.addEventListener('change', function() {
                const mulai = this.value;
                if (mulai) {
                    selesaiInput.min = mulai;
                    if (selesaiInput.value && selesaiInput.value <= mulai) {
                        selesaiInput.value = '';
                    }
                }
                validateDates();
            });

            selesaiInput.addEventListener('change', validateDates);
            selesaiInput.addEventListener('input', validateDates);

            // Auto-capitalize nama periode
            const namaInput = document.getElementById('namaPeriode');
            namaInput.addEventListener('blur', function() {
                if (this.value) {
                    this.value = this.value
                        .split(' ')
                        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
                        .join(' ');
                }
            });

            // Toggle salin dari wrapper
            window.toggleSalinDari = function(mode) {
                if (salinDariWrapper) {
                    salinDariWrapper.style.display = mode === 'salin' ? 'block' : 'none';
                }
            };

            // Initial state
            const checkedRadio = document.querySelector('input[name="salin_mode"]:checked');
            if (checkedRadio) {
                toggleSalinDari(checkedRadio.value);
            }

            // Prevent double submit
            form.addEventListener('submit', function(e) {
                if (submitBtn.disabled) {
                    e.preventDefault();
                    return;
                }

                // Check if salin mode is selected but no source period chosen
                const salinMode = document.querySelector('input[name="salin_mode"]:checked');
                if (salinMode && salinMode.value === 'salin') {
                    const salinDari = document.querySelector('select[name="salin_dari"]');
                    if (!salinDari.value) {
                        e.preventDefault();
                        salinDari.focus();
                        salinDari.style.borderColor = '#ef4444';
                        alert('Silakan pilih periode sumber untuk menyalin kriteria.');
                        return;
                    }
                }

                if (!validateDates()) {
                    e.preventDefault();
                    selesaiInput.focus();
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-spinner bi-spin"></i> Menyimpan...';
            });

            // Real-time character counter for nama
            namaInput.addEventListener('input', function() {
                const maxLength = 100;
                if (this.value.length > maxLength) {
                    this.value = this.value.substring(0, maxLength);
                }
            });

            // Reset border color on focus
            const salinDariSelect = document.querySelector('select[name="salin_dari"]');
            if (salinDariSelect) {
                salinDariSelect.addEventListener('focus', function() {
                    this.style.borderColor = '';
                });
            }
        });
    </script>
@endsection
