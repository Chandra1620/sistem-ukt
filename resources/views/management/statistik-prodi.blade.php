@extends('layouts.app')
@section('title', 'Statistik Program Studi')
@section('page-title', 'Statistik Program Studi')

@section('content')
    <div class="mb-5">
        <h5 class="fw-bold mb-1"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            Statistik Program Studi
        </h5>
        <p class="text-muted small mb-0">Analisis jumlah pendaftar dan distribusi UKT per program studi</p>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-white-50 small mb-1">Total Program Studi</p>
                            <h2 class="fw-bold text-white mb-0">{{ $programStudi->count() }}</h2>
                        </div>
                        <div class="rounded-circle p-3 bg-white bg-opacity-25">
                            <i class="bi bi-building fs-4 text-white"></i>
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
        <div class="col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-white-50 small mb-1">Total Pendaftar</p>
                            <h2 class="fw-bold text-white mb-0">{{ $programStudi->sum('pendaftaran_count') }}</h2>
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
        <div class="col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-white-50 small mb-1">Rata-rata per Prodi</p>
                            <h2 class="fw-bold text-white mb-0">
                                {{ $programStudi->count() > 0 ? round($programStudi->sum('pendaftaran_count') / $programStudi->count(), 1) : 0 }}
                            </h2>
                        </div>
                        <div class="rounded-circle p-3 bg-white bg-opacity-25">
                            <i class="bi bi-calculator fs-4 text-white"></i>
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
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mb-5">
        <div class="col-lg-8 col-md-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle p-2"
                            style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                            <i class="bi bi-bar-chart-fill text-white"></i>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-0">Visualisasi Pendaftar</h6>
                            <small class="text-muted">Grafik pendaftar per program studi</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="chart-prodi" style="height: 420px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle p-2"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="bi bi-pie-chart-fill text-white"></i>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-0">Distribusi Prodi</h6>
                            <small class="text-muted">Persentase per program studi</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="chart-pie-prodi" style="height: 380px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle p-2" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <i class="bi bi-table text-white"></i>
                    </div>
                    <div>
                        <h6 class="fw-semibold mb-0">Detail Program Studi</h6>
                        <small class="text-muted">Data lengkap per program studi</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="searchProdi" class="form-control border-start-0"
                            placeholder="Cari program studi...">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="prodiTable">
                    <thead style="background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);">
                        <tr>
                            <th class="border-0 py-3 px-4">
                                <i class="bi bi-book me-2"></i> Program Studi
                            </th>
                            <th class="border-0 py-3">
                                <i class="bi bi-mortarboard me-2"></i> Jenjang
                            </th>
                            <th class="border-0 py-3">
                                <i class="bi bi-people me-2"></i> Total Pendaftar
                            </th>
                            <th class="border-0 py-3">
                                <i class="bi bi-check-circle me-2"></i> Sudah Final UKT
                            </th>
                            <th class="border-0 py-3 pe-4">
                                <i class="bi bi-graph-up me-2"></i> Persentase
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($distribusiPerProdi as $d)
                            @php
                                $total = $programStudi->sum('pendaftaran_count');
                                $pct = $total > 0 ? round(($d['total'] / $total) * 100, 1) : 0;
                                $finalCount = $d['sudah_final'];
                                $finalPct = $d['persen_final'];
                                $colors = ['#667eea', '#28a745', '#fd7e14', '#dc3545', '#17a2b8', '#6f42c1'];
                            @endphp
                            <tr class="prodi-row" data-prodi="{{ strtolower($d['nama']) }}">
                                <td class="py-3 px-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle"
                                            style="width: 8px; height: 8px; background: {{ $colors[$loop->index % count($colors)] }};">
                                        </div>
                                        <span class="fw-semibold">{{ $d['nama'] }}</span>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="badge rounded-pill px-3 py-2"
                                        style="background: {{ $colors[$loop->index % count($colors)] }}20; color: {{ $colors[$loop->index % count($colors)] }};">
                                        {{ $d['jenjang'] }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <span class="badge rounded-pill px-3 py-2"
                                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="bi bi-people me-1"></i> {{ $d['total'] }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge rounded-pill px-3 py-2"
                                            style="background: #28a74520; color: #28a745;">
                                            <i class="bi bi-check-circle me-1"></i> {{ $finalCount }}
                                        </span>
                                        <small class="text-muted">({{ $finalPct }}%)</small>
                                    </div>
                                </td>
                                <td class="py-3 pe-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1"
                                            style="height: 8px; background: #e9ecef; border-radius: 10px;">
                                            <div class="progress-bar rounded-pill"
                                                style="width: {{ $pct }}%; background: linear-gradient(90deg, {{ $colors[$loop->index % count($colors)] }} 0%, {{ $colors[$loop->index % count($colors)] }}80 100%);">
                                            </div>
                                        </div>
                                        <small class="fw-semibold" style="min-width: 45px;">{{ $pct }}%</small>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot style="background: #f8f9fa; border-top: 2px solid #e9ecef;">
                        <tr>
                            <td class="py-3 px-4 fw-bold">Total Keseluruhan</td>
                            <td class="py-3 fw-bold">-</td>
                            <td class="py-3">
                                <span class="badge bg-dark rounded-pill px-3 py-2">
                                    <i class="bi bi-database me-1"></i> {{ $programStudi->sum('pendaftaran_count') }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-dark rounded-pill px-3 py-2">
                                    <i class="bi bi-database me-1"></i>
                                    {{ collect($distribusiPerProdi)->sum('sudah_final') }}
                                </span>
                            </td>
                            <td class="py-3 pe-4 fw-bold">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                const res = await fetch('/api/statistik/per-prodi');
                const data = await res.json();

                if (data && data.length > 0) {
                    // Bar Chart Horizontal
                    new ApexCharts(document.querySelector('#chart-prodi'), {
                        series: [{
                            name: 'Jumlah Pendaftar',
                            data: data.map(d => d.total)
                        }],
                        chart: {
                            type: 'bar',
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
                            },
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 800
                            }
                        },
                        xaxis: {
                            categories: data.map(d => d.prodi),
                            labels: {
                                style: {
                                    fontSize: '11px',
                                    fontFamily: 'Poppins, sans-serif',
                                    fontWeight: 500
                                },
                                rotate: -45,
                                trim: true
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    fontSize: '11px',
                                    fontFamily: 'Poppins, sans-serif'
                                }
                            }
                        },
                        colors: ['#28a745'],
                        plotOptions: {
                            bar: {
                                borderRadius: 8,
                                borderRadiusApplication: 'end',
                                horizontal: true,
                                dataLabels: {
                                    position: 'top'
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            style: {
                                fontSize: '11px',
                                fontFamily: 'Poppins, sans-serif',
                                fontWeight: 600,
                                colors: ['#28a745']
                            },
                            formatter: function(val) {
                                return val + ' Mahasiswa'
                            },
                            offsetX: 10
                        },
                        tooltip: {
                            y: {
                                formatter: function(val) {
                                    return val + ' Mahasiswa'
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
                        title: {
                            text: 'Jumlah Pendaftar per Program Studi',
                            align: 'center',
                            style: {
                                fontSize: '14px',
                                fontFamily: 'Poppins, sans-serif',
                                fontWeight: 500
                            }
                        },
                        responsive: [{
                            breakpoint: 768,
                            options: {
                                plotOptions: {
                                    bar: {
                                        horizontal: false
                                    }
                                },
                                dataLabels: {
                                    offsetX: 0
                                }
                            }
                        }]
                    }).render();

                    // Pie Chart
                    new ApexCharts(document.querySelector('#chart-pie-prodi'), {
                        series: data.map(d => d.total),
                        labels: data.map(d => d.prodi),
                        chart: {
                            type: 'donut',
                            height: '100%',
                            toolbar: {
                                show: true
                            }
                        },
                        colors: ['#667eea', '#28a745', '#fd7e14', '#dc3545', '#17a2b8', '#6f42c1',
                            '#20c997', '#e83e8c'
                        ],
                        legend: {
                            position: 'bottom',
                            fontSize: '10px',
                            fontFamily: 'Poppins, sans-serif',
                            markers: {
                                radius: 12,
                                width: 10,
                                height: 10
                            }
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '60%',
                                    labels: {
                                        show: true,
                                        name: {
                                            show: true,
                                            fontSize: '14px',
                                            fontFamily: 'Poppins, sans-serif',
                                            fontWeight: 600
                                        },
                                        value: {
                                            show: true,
                                            fontSize: '16px',
                                            fontFamily: 'Poppins, sans-serif',
                                            fontWeight: 'bold',
                                            formatter: function(val) {
                                                return val + ' Mahasiswa'
                                            }
                                        },
                                        total: {
                                            show: true,
                                            label: 'Total',
                                            fontSize: '12px',
                                            fontFamily: 'Poppins, sans-serif',
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
                                fontSize: '10px',
                                fontFamily: 'Poppins, sans-serif'
                            },
                            formatter: function(val, opts) {
                                return opts.w.config.series[opts.seriesIndex] + ' Mhs'
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: function(val) {
                                    return val + ' Mahasiswa'
                                }
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
                                    fontSize: '9px'
                                }
                            }
                        }]
                    }).render();
                }

                // Search functionality
                const searchInput = document.getElementById('searchProdi');
                if (searchInput) {
                    searchInput.addEventListener('keyup', function() {
                        const searchTerm = this.value.toLowerCase();
                        const rows = document.querySelectorAll('.prodi-row');

                        rows.forEach(row => {
                            const prodiName = row.getAttribute('data-prodi');
                            if (prodiName.includes(searchTerm)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    });
                }
            } catch (error) {
                console.error('Error loading charts:', error);
                const chartContainer = document.querySelector('#chart-prodi');
                if (chartContainer) {
                    chartContainer.innerHTML =
                        '<div class="text-center text-muted py-5">Gagal memuat grafik</div>';
                }
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

        .input-group-text {
            background-color: white;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #667eea;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 1rem !important;
            }

            h2 {
                font-size: 1.5rem;
            }

            .table-responsive {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 576px) {
            .badge {
                font-size: 0.7rem;
            }

            .fw-semibold {
                font-size: 0.85rem;
            }
        }
    </style>
@endpush
