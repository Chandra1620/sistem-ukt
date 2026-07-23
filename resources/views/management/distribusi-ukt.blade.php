@extends('layouts.app')
@section('title', 'Distribusi UKT')
@section('page-title', 'Distribusi Golongan UKT')

@section('content')
    <div class="mb-5">
        <h5 class="fw-bold mb-1"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            Distribusi Golongan UKT
        </h5>
        <p class="text-muted small mb-0">Analisis sebaran mahasiswa berdasarkan golongan UKT dan KIP</p>
    </div>

    <!-- Cards Statistik -->
    <div class="row g-4 mb-5">
        @foreach ($dataLengkap as $d)
            <div class="col-md-3 col-sm-6 col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 
                    {{ $d['is_kip'] ? 'border-warning' : '' }}"
                    style="{{ $d['is_kip'] ? 'border-left: 4px solid #f59e0b !important;' : '' }}">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #fff 0%, #f8f9ff 100%);">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                @if ($d['is_kip'])
                                    <span class="badge px-3 py-2 rounded-pill"
                                        style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);">
                                        KIP
                                    </span>
                                @else
                                    <span class="badge px-3 py-2 rounded-pill"
                                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        Golongan {{ $d['golongan'] }}
                                    </span>
                                @endif
                            </div>
                            <div class="rounded-circle p-2" style="background: rgba(102, 126, 234, 0.1);">
                                <i class="bi bi-people-fill text-primary"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <h2 class="fw-bold mb-2" style="color: {{ $d['is_kip'] ? '#f59e0b' : '#667eea' }};">
                                {{ $d['is_kip'] ? 'KIP' : $d['golongan'] }}
                            </h2>
                            @if (!$d['is_kip'])
                                <div class="mb-2">
                                    <span class="text-muted small">Nominal UKT</span>
                                    <h6 class="fw-bold text-success mb-0">
                                        Rp {{ number_format($d['nominal'], 0, ',', '.') }}
                                    </h6>
                                </div>
                            @else
                                <div class="mb-2">
                                    <span class="text-muted small">Status</span>
                                    <h6 class="fw-bold text-warning mb-0">
                                        <i class="bi bi-check-circle-fill me-1"></i> Penerima KIP
                                    </h6>
                                </div>
                            @endif
                            <div class="border-top pt-2 mt-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Total Mahasiswa</span>
                                    <span class="fw-bold fs-5">{{ $d['total'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Charts & Tables Section -->
    <div class="row g-4">
        <!-- Chart Card -->
        <div class="col-lg-6 col-md-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle p-2"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="bi bi-pie-chart-fill text-white"></i>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-0">Visualisasi Distribusi</h6>
                            <small class="text-muted">Grafik persentase per golongan UKT & KIP</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="chart-distribusi" style="height: 400px;"></div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="col-lg-6 col-md-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle p-2"
                            style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                            <i class="bi bi-table text-white"></i>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-0">Detail Distribusi</h6>
                            <small class="text-muted">Data lengkap per golongan UKT & KIP</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);">
                                <tr>
                                    <th class="border-0 py-3 px-4">Golongan / Status</th>
                                    <th class="border-0 py-3">Jumlah Mahasiswa</th>
                                    <th class="border-0 py-3">Persentase</th>
                                    <th class="border-0 py-3 pe-4">Nominal UKT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dataLengkap as $d)
                                    @php
                                        $colors = ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#43e97b', '#fa709a'];
                                        $badgeColors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                                        $isKip = $d['is_kip'] ?? false;
                                    @endphp
                                    <tr>
                                        <td class="py-3 px-4">
                                            <div class="d-flex align-items-center gap-2">
                                                @if ($isKip)
                                                    <div class="rounded-circle"
                                                        style="width: 10px; height: 10px; background: #f59e0b;"></div>
                                                    <span class="fw-semibold" style="color: #f59e0b;">
                                                        {{ $d['label'] }}
                                                    </span>
                                                @else
                                                    <div class="rounded-circle"
                                                        style="width: 10px; height: 10px; background: {{ $colors[$loop->index % count($colors)] }};">
                                                    </div>
                                                    <span class="fw-semibold">{{ $d['label'] }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <span class="badge rounded-pill px-3 py-2"
                                                style="background: {{ $isKip ? '#f59e0b20' : $colors[$loop->index % count($colors)] . '20' }}; 
                                                        color: {{ $isKip ? '#f59e0b' : $colors[$loop->index % count($colors)] }};">
                                                <i class="bi bi-people me-1"></i> {{ $d['total'] }} Mahasiswa
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1"
                                                    style="height: 8px; background: #e9ecef; border-radius: 10px;">
                                                    <div class="progress-bar rounded-pill"
                                                        style="width: {{ $d['persentase'] }}%; 
                                                            background: {{ $isKip ? 'linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%)' : 'linear-gradient(90deg, ' . $colors[$loop->index % count($colors)] . ' 0%, ' . $colors[$loop->index % count($colors)] . '80 100%)' }};">
                                                    </div>
                                                </div>
                                                <small class="fw-semibold"
                                                    style="min-width: 45px;">{{ $d['persentase'] }}%</small>
                                            </div>
                                        </td>
                                        @if (!$isKip)
                                            <td class="py-3 pe-4 text-success fw-medium">
                                                Rp {{ number_format($d['nominal'], 0, ',', '.') }}
                                            </td>
                                        @else
                                            <td class="py-3 pe-4 text-muted">
                                                <em>-</em>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot style="background: #f8f9fa; border-top: 2px solid #e9ecef;">
                                <tr>
                                    <td class="py-3 px-4 fw-bold">Total Keseluruhan</td>
                                    <td class="py-3">
                                        <span class="badge bg-dark rounded-pill px-3 py-2">
                                            <i class="bi bi-database me-1"></i> {{ $totalFinalisasi }} Mahasiswa
                                        </span>
                                    </td>
                                    <td class="py-3 fw-bold">
                                        {{ collect($dataLengkap)->sum('persentase') }}%
                                    </td>
                                    <td class="py-3 pe-4 text-muted">-</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Cards -->
    <div class="row g-4 mt-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small mb-1">Total Mahasiswa</p>
                            <h5 class="fw-bold mb-0" style="color: #667eea;">
                                {{ $totalFinalisasi }}
                            </h5>
                        </div>
                        <div class="rounded-circle p-3"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="bi bi-people-fill text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small mb-1">Penerima KIP</p>
                            <h5 class="fw-bold mb-0" style="color: #f59e0b;">
                                {{ $dataLengkap->firstWhere('is_kip', true)['total'] ?? 0 }}
                            </h5>
                        </div>
                        <div class="rounded-circle p-3"
                            style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);">
                            <i class="bi bi-star-fill text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small mb-1">UKT Tertinggi</p>
                            <h5 class="fw-bold mb-0" style="color: #dc3545;">
                                Rp
                                {{ number_format(collect($dataLengkap)->where('is_kip', false)->max('nominal') ?? 0, 0, ',', '.') }}
                            </h5>
                        </div>
                        <div class="rounded-circle p-3"
                            style="background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);">
                            <i class="bi bi-graph-up-arrow text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small mb-1">UKT Terendah</p>
                            <h5 class="fw-bold mb-0" style="color: #17a2b8;">
                                Rp
                                {{ number_format(collect($dataLengkap)->where('is_kip', false)->min('nominal') ?? 0, 0, ',', '.') }}
                            </h5>
                        </div>
                        <div class="rounded-circle p-3"
                            style="background: linear-gradient(135deg, #17a2b8 0%, #4facfe 100%);">
                            <i class="bi bi-graph-down-arrow text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                const dataAll = @json($dataLengkap);

                // Filter hanya golongan yang punya data (total > 0)
                const data = dataAll.filter(d => d.total > 0);

                if (data && data.length > 0) {
                    const palette = ['#3b82f6', '#8b5cf6', '#06b6d4', '#10b981', '#ef4444', '#ec489a',
                        '#6366f1'
                    ];
                    let idx = 0;

                    new ApexCharts(document.querySelector('#chart-distribusi'), {
                        series: data.map(d => d.total),
                        labels: data.map(d => d.label),
                        chart: {
                            type: 'donut',
                            height: '100%',
                            toolbar: {
                                show: true,
                                tools: {
                                    download: true,
                                    selection: false,
                                    zoom: false,
                                    zoomin: false,
                                    zoomout: false,
                                    pan: false,
                                    reset: false
                                }
                            }
                        },
                        colors: data.map(d => d.is_kip ? '#f59e0b' : palette[idx++ % palette.length]),
                        legend: {
                            position: 'bottom',
                            horizontalAlign: 'center',
                            fontSize: '12px',
                            fontFamily: 'Poppins, sans-serif',
                            markers: {
                                radius: 12,
                                width: 12,
                                height: 12
                            },
                            itemMargin: {
                                horizontal: 10,
                                vertical: 5
                            }
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '65%',
                                    labels: {
                                        show: true,
                                        name: {
                                            show: true,
                                            fontSize: '16px',
                                            fontFamily: 'Poppins, sans-serif',
                                            fontWeight: 600,
                                            offsetY: -10
                                        },
                                        value: {
                                            show: true,
                                            fontSize: '20px',
                                            fontFamily: 'Poppins, sans-serif',
                                            fontWeight: 'bold',
                                            offsetY: 10,
                                            formatter: function(val) {
                                                return val + ' Mahasiswa'
                                            }
                                        },
                                        total: {
                                            show: true,
                                            label: 'Total',
                                            fontSize: '14px',
                                            fontFamily: 'Poppins, sans-serif',
                                            fontWeight: 500,
                                            formatter: function(w) {
                                                return w.globals.seriesTotals.reduce((a, b) => a +
                                                    b, 0) + ' Mahasiswa'
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            style: {
                                fontSize: '12px',
                                fontFamily: 'Poppins, sans-serif',
                                fontWeight: 500
                            },
                            formatter: function(val, opts) {
                                return opts.w.config.series[opts.seriesIndex] + ' Mahasiswa'
                            },
                            dropShadow: {
                                enabled: false
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: function(val) {
                                    return val + ' Mahasiswa'
                                },
                                title: {
                                    formatter: function(seriesName) {
                                        return seriesName
                                    }
                                }
                            },
                            theme: 'dark',
                            style: {
                                fontSize: '12px',
                                fontFamily: 'Poppins, sans-serif'
                            }
                        },
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: {
                                    width: '100%'
                                },
                                legend: {
                                    position: 'bottom',
                                    fontSize: '10px'
                                }
                            }
                        }]
                    }).render();
                }
            } catch (error) {
                console.error('Error loading chart:', error);
                document.querySelector('#chart-distribusi').innerHTML =
                    '<div class="text-center text-muted py-5">Gagal memuat grafik</div>';
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

        @media (max-width: 768px) {
            .card-body {
                padding: 1rem !important;
            }

            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
@endpush
