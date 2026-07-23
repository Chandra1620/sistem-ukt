@extends('layouts.app')
@section('title', 'Kelola Sub Kriteria')
@section('page-title', 'Sub Kriteria SAW')

@section('content')
    <div class="container-fluid px-2 px-sm-3 px-lg-4">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-block d-sm-flex justify-content-between align-items-center">
                    <div class="mb-3 mb-sm-0">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="rounded-3 d-flex align-items-center justify-content-center"
                                style="background: #eef2ff; width: 52px; height: 52px;">
                                <i class="bi bi-table fs-3 text-primary"></i>
                            </div>
                            <div>
                                <h1 class="h3 fw-bold mb-0" style="color: #1a1a2e;">Sub Kriteria SAW</h1>
                                <p class="text-muted small mb-0 mt-1">Kelola detail sub kriteria untuk setiap kriteria</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('admin.sub-kriteria.create') }}"
                            class="btn btn-primary px-4 py-2 rounded-3 shadow-sm-hover"
                            style="border-radius: 12px!important;">
                            <i class="bi bi-plus-lg me-2"></i>Tambah Sub Kriteria
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @forelse($kriteria as $k)
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <!-- Card Header -->
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-2 d-flex align-items-center justify-content-center"
                                style="background: #eef2ff; width: 48px; height: 48px;">
                                <span class="fw-bold fs-6 text-primary">{{ $k->kode }}</span>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1" style="color: #1a1a2e;">{{ $k->nama }}</h5>
                                <div class="d-flex gap-2">
                                    <span
                                        class="badge px-3 py-1 rounded-pill small {{ $k->tipe === 'cost' ? 'badge-cost' : 'badge-benefit' }}">
                                        <i class="bi bi-{{ $k->tipe === 'cost' ? 'arrow-down' : 'arrow-up' }} me-1"></i>
                                        {{ ucfirst($k->tipe) }}
                                    </span>
                                    <span class="badge px-3 py-1 rounded-pill small"
                                        style="background: #eef2ff; color: #4f46e5;">
                                        <i class="bi bi-percent me-1"></i> Bobot: {{ $k->bobot }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.sub-kriteria.create') }}?kriteria_id={{ $k->id }}"
                            class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Sub Kriteria
                        </a>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="card-body p-0">
                    @if ($k->subKriteria->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="bg-light-50">
                                    <tr class="small text-muted" style="border-bottom: 1px solid #e9ecef;">
                                        <th class="ps-4 py-3 fw-semibold">LABEL</th>
                                        <th class="py-3 fw-semibold">RANGE MIN</th>
                                        <th class="py-3 fw-semibold">RANGE MAX</th>
                                        <th class="py-3 fw-semibold">NILAI</th>
                                        <th class="pe-4 py-3 text-end fw-semibold">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($k->subKriteria as $sub)
                                        <tr class="data-row" style="border-bottom: 1px solid #f0f2f5;">
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="rounded d-flex align-items-center justify-content-center"
                                                        style="width: 28px; height: 28px; background: #eef2ff;">
                                                        <i class="bi bi-bookmark-fill text-primary"
                                                            style="font-size: 0.7rem;"></i>
                                                    </div>
                                                    <span class="fw-medium">{{ $sub->label }}</span>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                @if ($sub->range_min)
                                                    <code class="px-2 py-1 rounded"
                                                        style="background: #f1f5f9; font-size: 0.75rem;">
                                                        {{ number_format((float) $sub->range_min, 0, ',', '.') }}
                                                    </code>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="py-3">
                                                @if ($sub->range_max)
                                                    <code class="px-2 py-1 rounded"
                                                        style="background: #f1f5f9; font-size: 0.75rem;">
                                                        {{ number_format((float) $sub->range_max, 0, ',', '.') }}
                                                    </code>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="py-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress rounded-2"
                                                        style="width: 60px; height: 4px; background: #e2e8f0;">
                                                        <div class="progress-bar rounded-2" role="progressbar"
                                                            style="width: {{ ($sub->nilai / 100) * 100 }}%; background: linear-gradient(90deg, #667eea, #764ba2);">
                                                        </div>
                                                    </div>
                                                    <span class="fw-bold"
                                                        style="color: #4f46e5;">{{ $sub->nilai }}</span>
                                                </div>
                                            </td>
                                            <td class="pe-4 py-3 text-end">
                                                <div class="d-flex gap-2 justify-content-end">
                                                    <a href="{{ route('admin.sub-kriteria.edit', $sub) }}"
                                                        class="btn-action edit" data-bs-toggle="tooltip"
                                                        title="Edit Sub Kriteria">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <form action="{{ route('admin.sub-kriteria.destroy', $sub) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirmDelete(event, '{{ $sub->label }}')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn-action delete"
                                                            data-bs-toggle="tooltip" title="Hapus Sub Kriteria">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                            </td>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                style="width: 64px; height: 64px; background: #f1f5f9;">
                                <i class="bi bi-inbox fs-2 text-muted"></i>
                            </div>
                            <p class="text-muted mb-2">Belum ada sub kriteria untuk <span
                                    class="fw-semibold text-primary">{{ $k->nama }}</span></p>
                            <a href="{{ route('admin.sub-kriteria.create') }}?kriteria_id={{ $k->id }}"
                                class="btn btn-primary btn-sm rounded-pill px-3 mt-2">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Sub Kriteria
                            </a>
                        </div>
                    @endif
                </div>

                @if ($k->subKriteria->count() > 0)
                    <div class="card-footer bg-white border-top py-3 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="small text-muted">
                                <i class="bi bi-info-circle me-1"></i> Total {{ $k->subKriteria->count() }} sub kriteria
                            </div>
                            <div class="small text-muted">
                                <i class="bi bi-clock me-1"></i> {{ $k->updated_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body text-center py-5">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4"
                        style="width: 80px; height: 80px; background: #f1f5f9;">
                        <i class="bi bi-diagram-3 fs-1 text-muted"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color: #1a1a2e;">Belum Ada Kriteria</h5>
                    <p class="text-muted mb-4">Buat kriteria terlebih dahulu sebelum menambahkan sub kriteria</p>
                    <a href="{{ route('admin.kriteria.create') }}" class="btn btn-primary rounded-pill px-4 py-2">
                        <i class="bi bi-plus-lg me-2"></i> Buat Kriteria Sekarang
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <style>
        /* Custom Styles */
        .bg-light-50 {
            background: #fafbfc;
        }

        .btn-primary {
            background: #1e293b;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background: #0f172a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-outline-primary {
            border-color: #e2e8f0;
            color: #475569;
            transition: all 0.2s ease;
        }

        .btn-outline-primary:hover {
            background: #1e293b;
            border-color: #1e293b;
            color: white;
            transform: translateY(-1px);
        }

        .badge-cost {
            background: #fee2e2;
            color: #dc2626;
        }

        .badge-benefit {
            background: #d1fae5;
            color: #059669;
        }

        .btn-action {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: transparent;
            border: none;
            transition: all 0.2s ease;
            cursor: pointer;
            font-size: 1rem;
            color: #94a3b8;
        }

        .btn-action.edit:hover {
            background: #eef2ff;
            color: #4f46e5;
            transform: translateY(-2px);
        }

        .btn-action.delete {
            color: #f87171;
        }

        .btn-action.delete:hover {
            background: #fef2f2;
            color: #dc2626;
            transform: translateY(-2px);
        }

        .data-row {
            transition: background 0.2s ease;
        }

        .data-row:hover {
            background: #fafbff;
        }

        code {
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 0.75rem;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 8px;
            color: #475569;
        }

        .progress {
            background: #e2e8f0;
        }

        /* Animations */
        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: fadeSlideUp 0.3s ease backwards;
        }

        .card:nth-child(1) {
            animation-delay: 0.03s;
        }

        .card:nth-child(2) {
            animation-delay: 0.06s;
        }

        .card:nth-child(3) {
            animation-delay: 0.09s;
        }

        .card:nth-child(4) {
            animation-delay: 0.12s;
        }

        .card:nth-child(5) {
            animation-delay: 0.15s;
        }

        .card:nth-child(6) {
            animation-delay: 0.18s;
        }

        .card:nth-child(7) {
            animation-delay: 0.21s;
        }

        .card:nth-child(8) {
            animation-delay: 0.24s;
        }

        .card:nth-child(9) {
            animation-delay: 0.27s;
        }

        .card:nth-child(10) {
            animation-delay: 0.30s;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .card-header .d-flex {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .btn-action {
                width: 30px;
                height: 30px;
                font-size: 0.85rem;
            }

            .table td,
            .table th {
                padding: 0.75rem 0.5rem;
            }

            code {
                font-size: 0.65rem;
            }
        }

        @media (max-width: 576px) {
            .badge {
                font-size: 0.65rem;
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    <script>
        function confirmDelete(event, subKriteriaName) {
            event.preventDefault();
            const form = event.target.closest('form');

            swalConfirm(`⚠️ Hapus Sub Kriteria "${subKriteriaName}"?\n\nData sub kriteria akan dihapus secara permanen.`)
                .then(function(ok) {
                    if (!ok) return;
                    form.submit();
                });
            return false;
        }

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(el) {
                new bootstrap.Tooltip(el);
            });
        });
    </script>
@endsection
