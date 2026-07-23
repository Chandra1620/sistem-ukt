@extends('layouts.app')
@section('title', 'Detail Kriteria SAW')
@section('page-title', 'Detail Kriteria SAW')

@section('content')
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card premium-card info-card">
                <div class="card-header premium-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon primary-icon">
                            <i class="bi bi-sliders2"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Info Kriteria</h6>
                    </div>
                    <a href="{{ route('admin.kriteria.edit', $kriteria) }}" class="btn-edit">
                        <i class="bi bi-pencil-square"></i> Edit Kriteria
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="info-list">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-hash"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Kode Kriteria</div>
                                <div class="info-value">
                                    <span class="kode-badge">{{ $kriteria->kode }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-textarea"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Nama Kriteria</div>
                                <div class="info-value fw-bold">{{ $kriteria->nama }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-percent"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Bobot Kriteria</div>
                                <div class="info-value">
                                    <div class="bobot-display">
                                        <div class="progress-modern">
                                            <div class="progress-bar-modern" style="width: {{ $kriteria->bobot }}%"></div>
                                        </div>
                                        <span class="bobot-value">{{ $kriteria->bobot }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-arrow-left-right"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Tipe Kriteria</div>
                                <div class="info-value">
                                    @if ($kriteria->tipe === 'cost')
                                        <span class="tipe-badge tipe-cost">
                                            <i class="bi bi-arrow-down-short"></i> Cost
                                        </span>
                                        <div class="tipe-description">
                                            <i class="bi bi-info-circle"></i> Semakin kecil nilainya semakin baik
                                        </div>
                                    @else
                                        <span class="tipe-badge tipe-benefit">
                                            <i class="bi bi-arrow-up-short"></i> Benefit
                                        </span>
                                        <div class="tipe-description">
                                            <i class="bi bi-info-circle"></i> Semakin besar nilainya semakin baik
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pb-4">
                    <a href="{{ route('admin.kriteria.index') }}" class="btn-back">
                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Kriteria
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card premium-card subkriteria-card">
                <div class="card-header premium-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon info-icon">
                            <i class="bi bi-diagram-3"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">
                            Sub Kriteria
                            <span class="subkriteria-count">{{ $kriteria->subKriteria->count() }}</span>
                        </h6>
                    </div>
                    <a href="{{ route('admin.sub-kriteria.create', ['kriteria_id' => $kriteria->id]) }}" class="btn-add">
                        <i class="bi bi-plus-circle"></i> Tambah Sub Kriteria
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($kriteria->subKriteria->count() > 0)
                        <div class="table-responsive">
                            <table class="table premium-table">
                                <thead>
                                    <tr>
                                        <th><i class="bi bi-tag"></i> Label</th>
                                        <th><i class="bi bi-sliders2"></i> Range Min</th>
                                        <th><i class="bi bi-sliders2"></i> Range Max</th>
                                        <th><i class="bi bi-star"></i> Nilai</th>
                                        <th><i class="bi bi-gear"></i> Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kriteria->subKriteria as $sub)
                                        <tr class="subkriteria-row">
                                            <td>
                                                <div class="label-badge">
                                                    <i class="bi bi-bookmark-fill"></i>
                                                    {{ $sub->label }}
                                                </div>
                                            </td>
                                            <td>
                                                @if ($sub->range_min)
                                                    <span class="range-value">{{ $sub->range_min }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($sub->range_max)
                                                    <span class="range-value">{{ $sub->range_max }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="nilai-badge">
                                                    <i class="bi bi-calculator-fill"></i>
                                                    {{ $sub->nilai }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="{{ route('admin.sub-kriteria.edit', $sub) }}"
                                                        class="btn-action edit" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <form action="{{ route('admin.sub-kriteria.destroy', $sub) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirmDelete(event)">
                                                        @csrf @method('DELETE')
                                                        <button class="btn-action delete" title="Hapus">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-subkriteria">
                            <div class="empty-icon">
                                <i class="bi bi-diagram-3"></i>
                            </div>
                            <h6 class="mt-3 mb-1">Belum Ada Sub Kriteria</h6>
                            <p class="text-muted small mb-3">Kriteria ini belum memiliki sub kriteria</p>
                            <a href="{{ route('admin.sub-kriteria.create', ['kriteria_id' => $kriteria->id]) }}"
                                class="btn-add-subkriteria">
                                <i class="bi bi-plus-circle"></i> Tambah Sub Kriteria Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Premium Cards */
        .premium-card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
        }

        .premium-card:hover {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        /* Card Header */
        .premium-card-header {
            background: white;
            border-bottom: 1px solid #f0f2f5;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .header-icon {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .primary-icon {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #3b82f6;
        }

        .info-icon {
            background: linear-gradient(135deg, #cffafe, #bae6fd);
            color: #06b6d4;
        }

        /* Buttons */
        .btn-edit {
            background: transparent;
            border: 1px solid #f59e0b;
            color: #f59e0b;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-edit:hover {
            background: #f59e0b;
            color: white;
            transform: translateY(-2px);
        }

        .btn-add {
            background: transparent;
            border: 1px solid #10b981;
            color: #10b981;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-add:hover {
            background: #10b981;
            color: white;
            transform: translateY(-2px);
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f1f5f9;
            color: #475569;
            padding: 8px 20px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-back:hover {
            background: #e2e8f0;
            transform: translateX(-4px);
            color: #1e293b;
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
            padding-bottom: 16px;
            border-bottom: 1px solid #f0f2f5;
        }

        .info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background: #f8fafc;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3b82f6;
            font-size: 1.2rem;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 1rem;
            color: #1e293b;
        }

        /* Kode Badge */
        .kode-badge {
            display: inline-block;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            padding: 6px 16px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 700;
            font-family: monospace;
            box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3);
        }

        /* Bobot Display */
        .bobot-display {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .progress-modern {
            flex: 1;
            height: 8px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar-modern {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #34d399);
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        .bobot-value {
            font-size: 0.9rem;
            font-weight: 700;
            +++++++color: #10b981;
            min-width: 45px;
        }

        /* Tipe Badge */
        .tipe-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .tipe-cost {
            background: #fee2e2;
            color: #dc2626;
        }

        .tipe-benefit {
            background: #d1fae5;
            color: #059669;
        }

        .tipe-description {
            font-size: 0.7rem;
            color: #64748b;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Subkriteria Count */
        .subkriteria-count {
            background: #eef2ff;
            color: #3b82f6;
            padding: 2px 10px;
            border-radius: 40px;
            font-size: 0.7rem;
            margin-left: 8px;
        }

        /* Premium Table */
        .premium-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .premium-table thead th {
            background: #f8fafc;
            padding: 14px 20px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }

        .premium-table thead th i {
            margin-right: 6px;
        }

        .premium-table tbody tr {
            transition: all 0.2s;
            border-bottom: 1px solid #f1f5f9;
        }

        .premium-table tbody tr:hover {
            background: #fafcff;
        }

        .premium-table tbody td {
            padding: 14px 20px;
            vertical-align: middle;
        }

        /* Label Badge */
        .label-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f1f5f9;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #1e293b;
        }

        .label-badge i {
            color: #8b5cf6;
            font-size: 0.8rem;
        }

        /* Range Value */
        .range-value {
            background: #f8fafc;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 500;
            color: #475569;
            font-family: monospace;
        }

        /* Nilai Badge */
        .nilai-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #3b82f6;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .btn-action.edit {
            background: #fef3c7;
            color: #f59e0b;
        }

        .btn-action.edit:hover {
            background: #f59e0b;
            color: white;
            transform: translateY(-2px);
        }

        .btn-action.delete {
            background: #fee2e2;
            color: #ef4444;
        }

        .btn-action.delete:hover {
            background: #ef4444;
            color: white;
            transform: translateY(-2px);
        }

        /* Empty State */
        .empty-subkriteria {
            text-align: center;
            padding: 48px 24px;
        }

        .empty-icon {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 2rem;
            color: #94a3b8;
        }

        .btn-add-subkriteria {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #10b981;
            color: white;
            padding: 10px 20px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-add-subkriteria:hover {
            background: #059669;
            transform: translateY(-2px);
            color: white;
        }

        /* Animations */
        .subkriteria-row {
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

        .subkriteria-row:nth-child(1) {
            animation-delay: 0.03s;
        }

        .subkriteria-row:nth-child(2) {
            animation-delay: 0.06s;
        }

        .subkriteria-row:nth-child(3) {
            animation-delay: 0.09s;
        }

        .subkriteria-row:nth-child(4) {
            animation-delay: 0.12s;
        }

        .subkriteria-row:nth-child(5) {
            animation-delay: 0.15s;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .premium-card-header {
                padding: 14px 18px;
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .card-body {
                padding: 20px !important;
            }

            .info-item {
                gap: 12px;
            }

            .info-icon {
                width: 36px;
                height: 36px;
                font-size: 1rem;
            }

            .premium-table thead th {
                padding: 10px 12px;
                font-size: 0.65rem;
            }

            .premium-table tbody td {
                padding: 10px 12px;
            }

            .action-buttons {
                gap: 4px;
            }

            .btn-action {
                width: 28px;
                height: 28px;
                font-size: 0.8rem;
            }

            .bobot-display {
                flex-direction: column;
                align-items: flex-start;
            }

            .progress-modern {
                width: 100%;
            }
        }
    </style>

    <script>
        function confirmDelete(event) {
            event.preventDefault();
            const form = event.target;

            swalConfirm(
                '⚠️ Apakah Anda yakin ingin menghapus sub kriteria ini?\n\nData yang terkait akan dihapus secara permanen.'
                ).then(function(ok) {
                if (!ok) return;
                form.submit();
            });
            return false;
        }

        // Add tooltips
        document.querySelectorAll('.btn-action, .btn-edit, .btn-add, .btn-back').forEach(btn => {
            btn.addEventListener('mouseenter', function(e) {
                const title = this.getAttribute('title');
                if (title) {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'custom-tooltip';
                    tooltip.textContent = title;
                    tooltip.style.position = 'absolute';
                    tooltip.style.background = '#1e293b';
                    tooltip.style.color = 'white';
                    tooltip.style.padding = '4px 10px';
                    tooltip.style.borderRadius = '8px';
                    tooltip.style.fontSize = '0.7rem';
                    tooltip.style.zIndex = '1000';

                    const rect = this.getBoundingClientRect();
                    tooltip.style.top = (rect.top - 30) + 'px';
                    tooltip.style.left = (rect.left + rect.width / 2 - 30) + 'px';

                    document.body.appendChild(tooltip);

                    this.addEventListener('mouseleave', function() {
                        tooltip.remove();
                    }, {
                        once: true
                    });
                }
            });
        });
    </script>
@endsection
