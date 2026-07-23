@extends('layouts.app')
@section('title', 'Edit Periode')
@section('page-title', 'Edit Periode Seleksi')

@section('content')
    {{-- @if (session('error'))
        <div class="alert-premium alert-premium-danger">
            <div class="alert-icon">
                <i class="bi bi-exclamation-circle"></i>
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

    {{-- Konfirmasi nonaktifkan periode lain --}}
    @if (session('warning_aktif'))
        @php $wa = session('warning_aktif'); @endphp
        <div class="alert-premium alert-premium-warning">
            <div class="alert-icon warning">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="alert-content">
                <span class="alert-title">Perhatian!</span>
                <span class="alert-message">
                    Periode "<strong>{{ $wa['nama_aktif'] }}</strong>" saat ini sedang aktif.
                    Mengaktifkan periode ini akan <strong>menonaktifkan</strong> periode tersebut secara otomatis.
                    Apakah Anda yakin ingin melanjutkan?
                </span>
                <div class="alert-actions mt-2">
                    <form action="{{ route('admin.periode.update', $wa['periode_id']) }}" method="POST">
                        @csrf @method('PUT')
                        @foreach ($wa['data'] as $key => $val)
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endforeach
                        <input type="hidden" name="konfirmasi_nonaktif" value="1">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-warning btn-sm">
                                <i class="bi bi-check-circle"></i>
                                Ya, Aktifkan & Nonaktifkan yang Lain
                            </button>
                            <a href="{{ route('admin.periode.edit', $wa['periode_id']) }}"
                                class="btn btn-outline-secondary btn-sm">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-header-left">
                <i class="bi bi-pencil"></i>
                <span>Edit Periode: <strong>{{ $periode->nama }}</strong></span>
            </div>
            <span class="form-card-badge status-{{ $periode->status }}">
                {{ ucfirst($periode->status) }}
            </span>
        </div>

        <div class="form-card-body">
            <form action="{{ route('admin.periode.update', $periode) }}" method="POST" id="periodeForm">
                @csrf @method('PUT')

                <div class="form-row">
                    <div class="form-group form-group-half">
                        <label class="form-label">
                            <span class="label-text">Nama Periode</span>
                            <span class="label-required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="bi bi-tag input-icon"></i>
                            <input type="text" name="nama" class="form-input @error('nama') is-error @enderror"
                                value="{{ old('nama', $periode->nama) }}"
                                placeholder="Contoh: Penerimaan Mahasiswa Baru 2026" id="namaPeriode">
                            @error('nama')
                                <div class="input-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-hint">
                            <i class="bi bi-lightbulb"></i>
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
                                value="{{ old('tahun', $periode->tahun) }}" min="2020" max="2100"
                                id="tahunPeriode">
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
                                value="{{ old('tanggal_mulai', $periode->tanggal_mulai->format('Y-m-d')) }}">
                            @error('tanggal_mulai')
                                <div class="input-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-hint">
                            <i class="bi bi-clock"></i>
                            Mengubah ke tanggal yang sudah lewat tidak diizinkan
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
                                value="{{ old('tanggal_selesai', $periode->tanggal_selesai->format('Y-m-d')) }}">
                            @error('tanggal_selesai')
                                <div class="input-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-hint" id="dateHint">
                            <i class="bi bi-info-circle"></i>
                            Harus setelah tanggal mulai
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-group-full">
                        <label class="form-label">
                            <span class="label-text">Status</span>
                            <span class="label-required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="bi bi-circle-fill input-icon"></i>
                            <select name="status" class="form-input form-select @error('status') is-error @enderror"
                                id="select_status">
                                @foreach (['nonaktif' => 'Nonaktif', 'aktif' => 'Aktif', 'selesai' => 'Selesai'] as $val => $label)
                                    <option value="{{ $val }}"
                                        {{ old('status', $periode->status) === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="input-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Peringatan inline saat pilih aktif --}}
                        <div id="info_aktif" class="status-warning d-none">
                            <i class="bi bi-exclamation-triangle"></i>
                            Mengaktifkan periode ini akan <strong>menonaktifkan periode lain yang sedang aktif</strong>.
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-warning btn-submit" id="submitBtn">
                        <i class="bi bi-save"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                    <a href="{{ route('admin.periode.index') }}" class="btn btn-outline-cancel">
                        <i class="bi bi-arrow-left"></i>
                        <span>Kembali</span>
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
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
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
            border-color: #f59e0b;
            color: #f59e0b;
            background: #fffbeb;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.1);
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
            position: relative;
        }

        .alert-premium-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
        }

        .alert-premium-warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
        }

        .alert-premium .alert-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .alert-premium-danger .alert-icon {
            background: #fee2e2;
            color: #dc2626;
        }

        .alert-premium-warning .alert-icon.warning {
            background: #fef3c7;
            color: #d97706;
        }

        .alert-premium .alert-content {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .alert-premium .alert-title {
            font-weight: 600;
            font-size: 14px;
        }

        .alert-premium-danger .alert-title {
            color: #991b1b;
        }

        .alert-premium-warning .alert-title {
            color: #92400e;
        }

        .alert-premium .alert-message {
            font-size: 13px;
            margin-top: 2px;
            line-height: 1.5;
        }

        .alert-premium-danger .alert-message {
            color: #7f1d1d;
        }

        .alert-premium-warning .alert-message {
            color: #78350f;
        }

        .alert-premium .alert-message strong {
            font-weight: 700;
        }

        .alert-premium .alert-close {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            font-size: 14px;
            opacity: 0.6;
            transition: opacity 0.2s ease;
            flex-shrink: 0;
            margin-top: 4px;
        }

        .alert-premium-danger .alert-close {
            color: #991b1b;
        }

        .alert-premium-warning .alert-close {
            color: #92400e;
        }

        .alert-premium .alert-close:hover {
            opacity: 1;
        }

        .alert-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .alert-actions .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 16px;
            border-radius: 40px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-family: inherit;
        }

        .alert-actions .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .alert-actions .btn-warning:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .alert-actions .btn-outline-secondary {
            background: transparent;
            color: #64748b;
            border: 1.5px solid #e2e8f0;
        }

        .alert-actions .btn-outline-secondary:hover {
            background: #f1f5f9;
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
            color: #f59e0b;
            font-size: 18px;
        }

        .form-card-header-left strong {
            color: #0f172a;
        }

        .form-card-badge {
            padding: 4px 14px;
            border-radius: 40px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .form-card-badge.status-aktif {
            background: #d1fae5;
            color: #065f46;
        }

        .form-card-badge.status-nonaktif {
            background: #f1f5f9;
            color: #475569;
        }

        .form-card-badge.status-selesai {
            background: #e2e8f0;
            color: #1e293b;
        }

        .form-card-body {
            padding: 32px 28px 36px;
        }

        /* ============================================ */
        /* FORM GROUP */
        /* ============================================ */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group-full {
            margin-bottom: 0;
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
            appearance: auto;
        }

        .form-input:focus {
            border-color: #f59e0b;
            background: white;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.08);
        }

        .form-input:focus+.input-icon,
        .form-input:focus~.input-icon {
            color: #f59e0b;
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
            padding-right: 40px;
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
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
        /* STATUS WARNING */
        /* ============================================ */
        .status-warning {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 10px;
            margin-top: 8px;
            font-size: 13px;
            color: #92400e;
        }

        .status-warning i {
            font-size: 16px;
            color: #d97706;
            flex-shrink: 0;
        }

        .status-warning strong {
            color: #78350f;
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

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        }

        .btn-warning:active {
            transform: translateY(0);
        }

        .btn-warning i {
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

            .form-card-header-left i {
                font-size: 16px;
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

            .form-group-full {
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

            .alert-premium {
                flex-direction: column;
                gap: 12px;
                padding: 14px 16px;
            }

            .alert-premium .alert-icon {
                width: 32px;
                height: 32px;
                font-size: 16px;
            }

            .alert-premium .alert-title {
                font-size: 13px;
            }

            .alert-premium .alert-message {
                font-size: 12px;
            }

            .status-warning {
                font-size: 12px;
                padding: 8px 12px;
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

            .alert-actions .btn {
                font-size: 12px;
                padding: 6px 14px;
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
                flex-wrap: wrap;
                gap: 8px;
            }

            .form-card-header-left {
                font-size: 13px;
                gap: 8px;
            }

            .form-card-header-left i {
                font-size: 14px;
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

            .form-hint i {
                font-size: 12px;
            }

            .status-warning {
                font-size: 11px;
                padding: 8px 10px;
            }

            .btn {
                font-size: 13px;
                padding: 10px 16px;
            }

            .form-actions .btn {
                padding: 10px 16px;
                font-size: 13px;
            }

            .alert-premium {
                padding: 12px 14px;
                gap: 10px;
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

            .form-card-header-left {
                font-size: 12px;
            }

            .form-card-badge {
                font-size: 9px;
                padding: 2px 8px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mulaiInput = document.getElementById('tanggal_mulai');
            const selesaiInput = document.getElementById('tanggal_selesai');
            const dateHint = document.getElementById('dateHint');
            const selectStatus = document.getElementById('select_status');
            const infoAktif = document.getElementById('info_aktif');
            const submitBtn = document.getElementById('submitBtn');
            const form = document.getElementById('periodeForm');

            // Tampilkan peringatan saat pilih status aktif
            function toggleStatusWarning() {
                if (selectStatus.value === 'aktif') {
                    infoAktif.classList.remove('d-none');
                } else {
                    infoAktif.classList.add('d-none');
                }
            }

            selectStatus.addEventListener('change', toggleStatusWarning);

            // Toggle warning on load if status is aktif
            if (selectStatus.value === 'aktif') {
                infoAktif.classList.remove('d-none');
            }

            // Validasi frontend: tanggal selesai harus setelah tanggal mulai
            function validateDates() {
                const mulai = mulaiInput.value;
                const selesai = selesaiInput.value;

                if (mulai && selesai) {
                    if (selesai <= mulai) {
                        selesaiInput.classList.add('is-error');
                        dateHint.innerHTML =
                            '<i class="bi bi-exclamation-circle"></i> Tanggal selesai harus setelah tanggal mulai';
                        dateHint.style.color = '#dc2626';
                        return false;
                    } else {
                        selesaiInput.classList.remove('is-error');
                        dateHint.innerHTML = '<i class="bi bi-check-circle"></i> Tanggal valid';
                        dateHint.style.color = '#059669';
                        return true;
                    }
                }

                if (selesai && !mulai) {
                    dateHint.innerHTML = '<i class="bi bi-info-circle"></i> Pilih tanggal mulai terlebih dahulu';
                    dateHint.style.color = '#94a3b8';
                } else {
                    dateHint.innerHTML = '<i class="bi bi-info-circle"></i> Harus setelah tanggal mulai';
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

            // Prevent double submit
            form.addEventListener('submit', function(e) {
                if (submitBtn.disabled) {
                    e.preventDefault();
                    return;
                }

                // Check if trying to activate and warning is showing
                if (selectStatus.value === 'aktif') {
                    e.preventDefault();
                    if (!validateDates()) {
                        selesaiInput.focus();
                        return;
                    }
                    swalConfirm('⚠️ Apakah Anda yakin ingin mengaktifkan periode ini?\n\n' +
                        'Periode lain yang sedang aktif akan otomatis dinonaktifkan.').then(function(
                    ok) {
                        if (!ok) return;
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="bi bi-spinner bi-spin"></i> Menyimpan...';
                        e.target.submit();
                    });
                    return;
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
        });
    </script>
@endsection
