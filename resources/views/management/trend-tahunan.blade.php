@extends('layouts.app')
@section('title', 'Trend Tahunan')
@section('page-title', 'Trend Pendaftar Tahunan')

@section('content')
    <div class="mb-5">
        <h5 class="fw-bold mb-1"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            Trend Tahunan
        </h5>
        <p class="text-muted small mb-0">Analisis perbandingan pendaftar antar periode seleksi dan proyeksi trend</p>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-white-50 small mb-1">Total Periode</p>
                            <h2 class="fw-bold text-white mb-0">{{ $periodeList->count() }}</h2>
                        </div>
                        <div class="rounded-circle p-3 bg-white bg-opacity-25">
                            <i class="bi bi-calendar3 fs-4 text-white"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress bg-white bg-opacity-25" style="height: 4px;">
                            <div class="progress-bar bg-white" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-white-50 small mb-1">Total Pendaftar</p>
                            <h2 class="fw-bold text-white mb-0">{{ $periodeList->sum('pendaftaran_count') }}</h2>
                        </div>
                        <div class="rounded-circle p-3 bg-white bg-opacity-25">
                            <i class="bi bi-people fs-4 text-white"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress bg-white bg-opacity-25" style="height: 4px;">
                            <div class="progress-bar bg-white" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-white-50 small mb-1">Rata-rata per Periode</p>
                            <h2 class="fw-bold text-white mb-0">
                                {{ $periodeList->count() > 0 ? round($periodeList->sum('pendaftaran_count') / $periodeList->count(), 0) : 0 }}
                            </h2>
                        </div>
                        <div class="rounded-circle p-3 bg-white bg-opacity-25">
                            <i class="bi bi-graph-up fs-4 text-white"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress bg-white bg-opacity-25" style="height: 4px;">
                            <div class="progress-bar bg-white" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-white-50 small mb-1">Tren Pertumbuhan</p>
                            @php
                                // PERBAIKAN: Handle jika periodeList kosong
                                $firstItem = $periodeList->first();
                                $lastItem = $periodeList->last();
                                $firstTotal = $firstItem ? $firstItem->pendaftaran_count : 0;
                                $lastTotal = $lastItem ? $lastItem->pendaftaran_count : 0;
                                $periodeCount = $periodeList->count();
                                $growth =
                                    $firstTotal > 0 && $periodeCount > 1
                                        ? round((($lastTotal - $firstTotal) / $firstTotal) * 100, 1)
                                        : 0;
                            @endphp
                            <h2 class="fw-bold text-white mb-0">{{ $growth >= 0 ? '+' : '' }}{{ $growth }}%</h2>
                        </div>
                        <div class="rounded-circle p-3 bg-white bg-opacity-25">
                            <i class="bi bi-arrow-{{ $growth >= 0 ? 'up' : 'down' }}-short fs-4 text-white"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress bg-white bg-opacity-25" style="height: 4px;">
                            <div class="progress-bar bg-white" style="width: {{ min(100, abs($growth)) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="row g-4 mb-5">
        <div class="col-lg-8 col-md-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle p-2"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="bi bi-graph-up text-white"></i>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-0">Grafik Trend Pendaftar</h6>
                            <small class="text-muted">Perbandingan antar periode seleksi</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="chart-trend" style="height: 420px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle p-2"
                            style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                            <i class="bi bi-trophy text-white"></i>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-0">Statistik Periode</h6>
                            <small class="text-muted">Periode terbaik dan terendah</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    @php
                        $maxPeriode = $periodeList->sortByDesc('pendaftaran_count')->first();
                        $minPeriode = $periodeList->sortBy('pendaftaran_count')->first();
                    @endphp
                    <div class="mb-4 p-3 rounded-3"
                        style="background: linear-gradient(135deg, #28a74510 0%, #20c99710 100%); border-left: 4px solid #28a745;">
                        <div class="text-muted small mb-1">Periode Tertinggi</div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold fs-5">{{ $maxPeriode->nama ?? '-' }}</span>
                                <div class="text-success fw-semibold">{{ $maxPeriode->pendaftaran_count ?? 0 }} Pendaftar
                                </div>
                            </div>
                            <div class="rounded-circle p-2" style="background: #28a74520;">
                                <i class="bi bi-arrow-up-short text-success fs-4"></i>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4 p-3 rounded-3"
                        style="background: linear-gradient(135deg, #dc354510 0%, #e83e8c10 100%); border-left: 4px solid #dc3545;">
                        <div class="text-muted small mb-1">Periode Terendah</div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold fs-5">{{ $minPeriode->nama ?? '-' }}</span>
                                <div class="text-danger fw-semibold">{{ $minPeriode->pendaftaran_count ?? 0 }} Pendaftar
                                </div>
                            </div>
                            <div class="rounded-circle p-2" style="background: #dc354520;">
                                <i class="bi bi-arrow-down-short text-danger fs-4"></i>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 rounded-3" style="background: linear-gradient(135deg, #667eea10 0%, #764ba210 100%);">
                        <div class="text-muted small mb-1">Proyeksi Periode Berikutnya</div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold fs-5">{{ $periodeList->count() + 1 }}</span>
                                <div class="text-primary fw-semibold">
                                    @php
                                        // PERBAIKAN: Handle jika hanya 1 periode
                                        $proyeksi =
                                            $periodeCount > 1
                                                ? round(
                                                    $lastTotal + ($lastTotal - $firstTotal) / ($periodeCount - 1),
                                                    0,
                                                )
                                                : ($periodeCount == 1
                                                    ? $lastTotal
                                                    : 0);
                                    @endphp
                                    ~{{ $proyeksi }} Pendaftar
                                </div>
                            </div>
                            <div class="rounded-circle p-2" style="background: #667eea20;">
                                <i class="bi bi-eye text-primary fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle p-2"
                        style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <i class="bi bi-table text-white"></i>
                    </div>
                    <div>
                        <h6 class="fw-semibold mb-0">Detail per Periode</h6>
                        <small class="text-muted">Data lengkap setiap periode seleksi</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary active" id="viewAllBtn">
                            <i class="bi bi-table"></i> Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" id="viewActiveBtn">
                            <i class="bi bi-check-circle"></i> Aktif
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="periodeTable">
                    <thead style="background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);">
                        <tr>
                            <th class="border-0 py-3 px-4">
                                <i class="bi bi-calendar-event me-2"></i> Periode
                            </th>
                            <th class="border-0 py-3">
                                <i class="bi bi-calendar me-2"></i> Tahun
                            </th>
                            <th class="border-0 py-3">
                                <i class="bi bi-people me-2"></i> Total Pendaftar
                            </th>
                            <th class="border-0 py-3">
                                <i class="bi bi-check-circle me-2"></i> Sudah Final
                            </th>
                            <th class="border-0 py-3">
                                <i class="bi bi-info-circle me-2"></i> Status
                            </th>
                            <th class="border-0 py-3 pe-4">
                                <i class="bi bi-pie-chart me-2"></i> Distribusi Golongan
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trendData as $index => $t)
                            @php
                                $statusClass = '';
                                $statusIcon = '';
                                if ($t['status'] === 'aktif') {
                                    $statusClass = 'success';
                                    $statusIcon = 'bi-play-circle-fill';
                                } elseif ($t['status'] === 'selesai') {
                                    $statusClass = 'dark';
                                    $statusIcon = 'bi-check-circle-fill';
                                } else {
                                    $statusClass = 'secondary';
                                    $statusIcon = 'bi-pause-circle-fill';
                                }
                                $colors = ['#667eea', '#28a745', '#fd7e14', '#dc3545', '#17a2b8', '#6f42c1'];
                            @endphp
                            <tr class="periode-row" data-status="{{ $t['status'] }}">
                                <td class="py-3 px-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle"
                                            style="width: 8px; height: 8px; background: {{ $colors[$index % count($colors)] }};">
                                        </div>
                                        <span class="fw-semibold">{{ $t['periode'] }}</span>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="badge rounded-pill px-3 py-2"
                                        style="background: {{ $colors[$index % count($colors)] }}20; color: {{ $colors[$index % count($colors)] }};">
                                        {{ $t['tahun'] }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <span class="badge rounded-pill px-3 py-2"
                                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="bi bi-people me-1"></i> {{ number_format($t['total']) }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <span class="badge rounded-pill px-3 py-2"
                                        style="background: #28a74520; color: #28a745;">
                                        <i class="bi bi-check-circle me-1"></i> {{ number_format($t['final']) }}
                                    </span>
                                    @php $finalPct = $t['total'] > 0 ? round(($t['final'] / $t['total']) * 100, 1) : 0; @endphp
                                    <small class="text-muted">({{ $finalPct }}%)</small>
                                </td>
                                <td class="py-3">
                                    <span class="badge rounded-pill px-3 py-2 bg-{{ $statusClass }}">
                                        <i class="bi {{ $statusIcon }} me-1"></i> {{ ucfirst($t['status']) }}
                                    </span>
                                </td>
                                <td class="py-3 pe-4">
                                    @if ($t['distribusi']->count() > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($t['distribusi'] as $d)
                                                <span class="badge rounded-pill"
                                                    style="background: {{ $colors[$loop->index % count($colors)] }}20; color: {{ $colors[$loop->index % count($colors)] }}; font-size:0.7rem;">
                                                    Gol {{ $d->golongan_final }}: {{ $d->total }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted small">
                                            <i class="bi bi-inbox"></i> Belum ada data
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                    Belum ada data periode
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot style="background: #f8f9fa; border-top: 2px solid #e9ecef;">
                        <tr>
                            <td class="py-3 px-4 fw-bold" colspan="2">Total Keseluruhan</td>
                            <td class="py-3">
                                <span class="badge bg-dark rounded-pill px-3 py-2">
                                    <i class="bi bi-database me-1"></i>
                                    {{ number_format($periodeList->sum('pendaftaran_count')) }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-dark rounded-pill px-3 py-2">
                                    <i class="bi bi-database me-1"></i>
                                    {{ number_format(collect($trendData)->sum('final')) }}
                                </span>
                            </td>
                            <td class="py-3" colspan="2">-</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const labels = @json($periodeList->pluck('nama'));
            const data = @json($periodeList->pluck('pendaftaran_count'));
            const finalData = @json(collect($trendData)->pluck('final'));

            if (labels.length > 0) {
                // Area Chart dengan dual series
                new ApexCharts(document.querySelector('#chart-trend'), {
                    series: [{
                            name: 'Total Pendaftar',
                            data: data,
                            type: 'area'
                        },
                        {
                            name: 'Sudah Final UKT',
                            data: finalData,
                            type: 'line'
                        }
                    ],
                    chart: {
                        type: 'area',
                        height: '100%',
                        toolbar: {
                            show: true,
                            tools: {
                                download: true,
                                selection: true,
                                zoom: true,
                                zoomin: true,
                                zoomout: true,
                                pan: true,
                                reset: true
                            }
                        },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800
                        }
                    },
                    xaxis: {
                        categories: labels,
                        labels: {
                            style: {
                                fontSize: '11px',
                                fontFamily: 'Poppins, sans-serif',
                                fontWeight: 500
                            },
                            rotate: -45
                        },
                        title: {
                            text: 'Periode Seleksi',
                            style: {
                                fontSize: '12px',
                                fontFamily: 'Poppins, sans-serif'
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Jumlah Pendaftar',
                            style: {
                                fontSize: '12px',
                                fontFamily: 'Poppins, sans-serif'
                            }
                        },
                        labels: {
                            formatter: function(val) {
                                return val.toLocaleString('id-ID')
                            }
                        }
                    },
                    colors: ['#667eea', '#28a745'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
                            opacityTo: 0.05
                        }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: [2, 3]
                    },
                    markers: {
                        size: [4, 5],
                        strokeWidth: 0,
                        hover: {
                            size: 7
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: '10px',
                            fontFamily: 'Poppins, sans-serif',
                            fontWeight: 600
                        },
                        formatter: function(val) {
                            return val.toLocaleString('id-ID')
                        },
                        offsetY: -10
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return val.toLocaleString('id-ID') + ' Mahasiswa'
                            }
                        },
                        style: {
                            fontSize: '12px',
                            fontFamily: 'Poppins, sans-serif'
                        }
                    },
                    grid: {
                        borderColor: '#e9ecef',
                        strokeDashArray: 5
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'center',
                        fontSize: '12px',
                        fontFamily: 'Poppins, sans-serif',
                        markers: {
                            radius: 12,
                            width: 12,
                            height: 12
                        }
                    },
                    responsive: [{
                        breakpoint: 768,
                        options: {
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                width: [2, 2]
                            }
                        }
                    }]
                }).render();
            }

            // Filter functionality
            let currentFilter = 'all';
            const viewAllBtn = document.getElementById('viewAllBtn');
            const viewActiveBtn = document.getElementById('viewActiveBtn');
            const rows = document.querySelectorAll('.periode-row');

            function filterTable(status) {
                rows.forEach(row => {
                    if (status === 'all' || row.getAttribute('data-status') === status) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Update active button style
                if (status === 'all') {
                    viewAllBtn.classList.add('active');
                    viewActiveBtn.classList.remove('active');
                } else {
                    viewAllBtn.classList.remove('active');
                    viewActiveBtn.classList.add('active');
                }
            }

            if (viewAllBtn) {
                viewAllBtn.addEventListener('click', () => filterTable('all'));
            }
            if (viewActiveBtn) {
                viewActiveBtn.addEventListener('click', () => filterTable('aktif'));
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            font-family: 'Poppins', sans-serif;
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
        }

        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }

        .progress {
            overflow: hidden;
        }

        .progress-bar {
            transition: width 1s ease-in-out;
        }

        .btn-group .btn {
            transition: all 0.3s ease;
        }

        .btn-group .btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: white;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 1rem !important;
            }

            h2 {
                font-size: 1.5rem;
            }

            .table-responsive {
                font-size: 0.8rem;
            }

            .badge {
                font-size: 0.7rem;
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: slideIn 0.5s ease-out;
        }
    </style>
@endpush
