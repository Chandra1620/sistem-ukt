@extends('layouts.app')
@section('title', 'Proyeksi Pendapatan UKT')
@section('page-title', 'Proyeksi Pendapatan UKT')

@section('content')
    <!-- Header Section dengan efek glassmorphism -->
    <div class="header-section mb-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
            <div>
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="icon-wrapper">
                        <i class="bi bi-graph-up-arrow fs-3 text-white"></i>
                    </div>
                </div>
                <p class="text-muted mb-0" style="font-size: 0.95rem; max-width: 600px;">
                    <i class="bi bi-info-circle me-1"></i>
                    Estimasi total pemasukan UKT satu angkatan beserta besaran subsidi yang diberikan.
                    Acuan UKT penuh: <strong class="text-success">Rp {{ number_format($uktPenuh, 0, ',', '.') }}</strong>
                    (golongan tertinggi).
                </p>
            </div>
            <form method="GET" class="d-flex align-items-center gap-2">
                <label class="small text-muted mb-0 fw-medium">
                    <i class="bi bi-calendar3 me-1"></i>Periode:
                </label>
                <select name="periode_id" class="form-select form-select-sm border-0 shadow-sm"
                    style="width: auto; background: #f8f9fa; border-radius: 12px; padding: 0.5rem 2rem 0.5rem 1rem;"
                    onchange="this.form.submit()">
                    <option value="">Semua Periode</option>
                    @foreach ($daftarPeriode as $per)
                        <option value="{{ $per->id }}"
                            {{ (string) $periodeId === (string) $per->id ? 'selected' : '' }}>
                            {{ $per->nama }} ({{ $per->tahun }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <!-- KPI Cards dengan efek hover dan gradient yang lebih menarik -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-body p-4 position-relative"
                    style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="z-1 position-relative">
                            <p class="text-white-50 small mb-1 fw-medium">Total Proyeksi Pendapatan</p>
                            <h4 class="fw-bold text-white mb-0" style="font-size: 1.75rem;">Rp
                                {{ number_format($totalPendapatan, 0, ',', '.') }}</h4>
                        </div>
                        <div class="icon-circle">
                            <i class="bi bi-cash-stack fs-4 text-white"></i>
                        </div>
                    </div>
                    <div class="kpi-progress"
                        style="position: absolute; bottom: 0; left: 0; right: 0; height: 4px; background: rgba(255,255,255,0.2);">
                        <div class="h-100"
                            style="width: 100%; background: rgba(255,255,255,0.6); border-radius: 0 4px 4px 0;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-body p-4 position-relative"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="z-1 position-relative">
                            <p class="text-white-50 small mb-1 fw-medium">Rata-rata UKT / Mahasiswa</p>
                            <h4 class="fw-bold text-white mb-0" style="font-size: 1.75rem;">Rp
                                {{ number_format($rataUkt, 0, ',', '.') }}</h4>
                        </div>
                        <div class="icon-circle">
                            <i class="bi bi-calculator fs-4 text-white"></i>
                        </div>
                    </div>
                    <div class="kpi-progress"
                        style="position: absolute; bottom: 0; left: 0; right: 0; height: 4px; background: rgba(255,255,255,0.2);">
                        <div class="h-100"
                            style="width: 75%; background: rgba(255,255,255,0.6); border-radius: 0 4px 4px 0;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-body p-4 position-relative"
                    style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="z-1 position-relative">
                            <p class="text-white-50 small mb-1 fw-medium">Total Subsidi Diberikan</p>
                            <h4 class="fw-bold text-white mb-0" style="font-size: 1.75rem;">Rp
                                {{ number_format($totalSubsidi, 0, ',', '.') }}</h4>
                            <small class="text-white-50">{{ $persenSubsidi }}% dari potensi maksimum</small>
                        </div>
                        <div class="icon-circle">
                            <i class="bi bi-heart-fill fs-4 text-white"></i>
                        </div>
                    </div>
                    <div class="kpi-progress"
                        style="position: absolute; bottom: 0; left: 0; right: 0; height: 4px; background: rgba(255,255,255,0.2);">
                        <div class="h-100"
                            style="width: {{ $persenSubsidi }}%; background: rgba(255,255,255,0.6); border-radius: 0 4px 4px 0;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-body p-4 position-relative"
                    style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="z-1 position-relative">
                            <p class="text-white-50 small mb-1 fw-medium">Mahasiswa Ber-UKT</p>
                            <h4 class="fw-bold text-white mb-0" style="font-size: 1.75rem;">
                                {{ number_format($totalMahasiswa, 0, ',', '.') }}</h4>
                            <small class="text-white-50">{{ $totalKip }} penerima KIP</small>
                        </div>
                        <div class="icon-circle">
                            <i class="bi bi-people-fill fs-4 text-white"></i>
                        </div>
                    </div>
                    <div class="kpi-progress"
                        style="position: absolute; bottom: 0; left: 0; right: 0; height: 4px; background: rgba(255,255,255,0.2);">
                        <div class="h-100"
                            style="width: 100%; background: rgba(255,255,255,0.6); border-radius: 0 4px 4px 0;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts dengan desain yang lebih elegan -->
    <div class="row g-4 mb-5">
        <div class="col-lg-7">
            <div class="card chart-card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="chart-icon bg-success bg-opacity-10 p-2 rounded-3">
                            <i class="bi bi-bar-chart-fill text-success"></i>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-0">Pendapatan per Program Studi</h6>
                            <small class="text-muted">10 prodi dengan kontribusi pendapatan tertinggi</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="chart-pendapatan-prodi" style="height: 420px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card chart-card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="chart-icon bg-primary bg-opacity-10 p-2 rounded-3">
                            <i class="bi bi-pie-chart-fill text-primary"></i>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-0">Komposisi per Golongan</h6>
                            <small class="text-muted">Sumbangan pendapatan tiap golongan UKT</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="chart-pendapatan-golongan" style="height: 380px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel per golongan dengan desain modern -->
    <div class="card table-card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-header bg-transparent border-0 pt-4 px-4">
            <div class="d-flex align-items-center gap-2">
                <div class="table-icon bg-primary bg-opacity-10 p-2 rounded-3">
                    <i class="bi bi-layers-fill text-primary"></i>
                </div>
                <div>
                    <h6 class="fw-semibold mb-0">Rincian per Golongan</h6>
                    <small class="text-muted">Distribusi pendapatan berdasarkan golongan UKT</small>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="border-0 py-3 px-4">Golongan</th>
                            <th class="border-0 py-3">Jumlah Mahasiswa</th>
                            <th class="border-0 py-3">Total Pendapatan</th>
                            <th class="border-0 py-3 pe-4">Kontribusi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($perGolongan as $g)
                            <tr>
                                <td class="py-3 px-4 fw-semibold">
                                    <span class="badge rounded-pill px-3 py-2"
                                        style="background: {{ $g['golongan'] === 0 ? '#10b98120' : '#3b82f620' }}; color: {{ $g['golongan'] === 0 ? '#10b981' : '#3b82f6' }};">
                                        {{ $g['golongan'] === 0 ? 'KIP' : 'Golongan ' . $g['golongan'] }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <span class="fw-semibold">{{ number_format($g['jumlah'], 0, ',', '.') }}</span>
                                    <small class="text-muted ms-1">mahasiswa</small>
                                </td>
                                <td class="py-3 fw-semibold">Rp {{ number_format($g['total'], 0, ',', '.') }}</td>
                                <td class="py-3 pe-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar"
                                                style="width: {{ $totalPendapatan > 0 ? round(($g['total'] / $totalPendapatan) * 100, 1) : 0 }}%; background: linear-gradient(90deg, #11998e, #38ef7d);">
                                            </div>
                                        </div>
                                        <span
                                            class="fw-semibold small">{{ $totalPendapatan > 0 ? round(($g['total'] / $totalPendapatan) * 100, 1) : 0 }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="fw-bold"
                        style="background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%); border-top: 2px solid #e9ecef;">
                        <tr>
                            <td class="py-3 px-4">Total</td>
                            <td class="py-3">{{ number_format($totalMahasiswa, 0, ',', '.') }}</td>
                            <td class="py-3 text-success">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                            <td class="py-3 pe-4">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Tabel per prodi dengan desain modern -->
    <div class="card table-card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-transparent border-0 pt-4 px-4">
            <div class="d-flex align-items-center gap-2">
                <div class="table-icon bg-success bg-opacity-10 p-2 rounded-3">
                    <i class="bi bi-table text-success"></i>
                </div>
                <div>
                    <h6 class="fw-semibold mb-0">Rincian per Program Studi</h6>
                    <small class="text-muted">Distribusi pendapatan berdasarkan program studi</small>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="border-0 py-3 px-4">Program Studi</th>
                            <th class="border-0 py-3">Jenjang</th>
                            <th class="border-0 py-3">Mahasiswa</th>
                            <th class="border-0 py-3">Rata-rata UKT</th>
                            <th class="border-0 py-3 pe-4">Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($perProdi as $d)
                            <tr>
                                <td class="py-3 px-4 fw-semibold">
                                    <i class="bi bi-mortarboard-fill text-success me-2"></i>
                                    {{ $d['nama'] }}
                                </td>
                                <td class="py-3">
                                    <span class="badge rounded-pill px-3 py-2"
                                        style="background: #11998e20; color: #11998e;">
                                        {{ $d['jenjang'] }}
                                    </span>
                                </td>
                                <td class="py-3">{{ number_format($d['jumlah'], 0, ',', '.') }}</td>
                                <td class="py-3">Rp {{ number_format($d['rata'], 0, ',', '.') }}</td>
                                <td class="py-3 pe-4 fw-semibold text-success">Rp
                                    {{ number_format($d['total'], 0, ',', '.') }}</td>
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
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const prodiLabels = @json($prodiChart['labels']);
            const prodiTotals = @json($prodiChart['totals']);
            const golLabels = @json($golonganChart['labels']);
            const golTotals = @json($golonganChart['totals']);

            function rupiah(v) {
                return 'Rp ' + Number(v).toLocaleString('id-ID');
            }

            // Chart 1: Pendapatan per Prodi (Horizontal Bar)
            if (prodiLabels.length > 0) {
                new ApexCharts(document.querySelector('#chart-pendapatan-prodi'), {
                    series: [{
                        name: 'Pendapatan',
                        data: prodiTotals
                    }],
                    chart: {
                        type: 'bar',
                        height: '100%',
                        fontFamily: 'Poppins, sans-serif',
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
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            borderRadius: 8,
                            borderRadiusApplication: 'end',
                            barHeight: '60%',
                            colors: {
                                ranges: [{
                                    from: 0,
                                    to: 0,
                                    color: '#11998e'
                                }]
                            }
                        }
                    },
                    colors: ['#11998e'],
                    dataLabels: {
                        enabled: true,
                        formatter: function(v) {
                            return rupiah(v);
                        },
                        style: {
                            fontSize: '10px',
                            fontFamily: 'Poppins, sans-serif',
                            fontWeight: 500
                        },
                        offsetX: 5
                    },
                    xaxis: {
                        categories: prodiLabels,
                        labels: {
                            formatter: function(v) {
                                return rupiah(v);
                            },
                            style: {
                                fontSize: '10px',
                                fontFamily: 'Poppins, sans-serif'
                            }
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                fontSize: '10px',
                                fontFamily: 'Poppins, sans-serif',
                                fontWeight: 500
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(v) {
                                return rupiah(v);
                            }
                        },
                        theme: 'dark',
                        style: {
                            fontSize: '12px',
                            fontFamily: 'Poppins, sans-serif'
                        }
                    },
                    grid: {
                        borderColor: '#e9ecef',
                        strokeDashArray: 5,
                        padding: {
                            left: 10,
                            right: 10
                        }
                    },
                    fill: {
                        opacity: 1,
                        gradient: {
                            shade: 'light',
                            type: "horizontal",
                            shadeIntensity: 0.1,
                            gradientToColors: ['#38ef7d'],
                            inverseColors: false,
                            opacityFrom: 1,
                            opacityTo: 1,
                            stops: [0, 100]
                        }
                    }
                }).render();
            } else {
                document.querySelector('#chart-pendapatan-prodi').innerHTML =
                    '<div class="text-center text-muted py-5"><i class="bi bi-bar-chart fs-1 d-block mb-2"></i>Belum ada data</div>';
            }

            // Chart 2: Komposisi per Golongan (Donut)
            if (golLabels.length > 0) {
                const colors = ['#10b981', '#3b82f6', '#8b5cf6', '#06b6d4', '#f59e0b', '#ef4444', '#ec4899',
                    '#6366f1'
                ];
                new ApexCharts(document.querySelector('#chart-pendapatan-golongan'), {
                    series: golTotals,
                    labels: golLabels,
                    chart: {
                        type: 'donut',
                        height: '100%',
                        fontFamily: 'Poppins, sans-serif',
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800
                        }
                    },
                    colors: colors.slice(0, golLabels.length),
                    legend: {
                        position: 'bottom',
                        fontSize: '11px',
                        fontFamily: 'Poppins, sans-serif',
                        fontWeight: 500,
                        markers: {
                            radius: 8
                        },
                        itemMargin: {
                            horizontal: 8,
                            vertical: 4
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val.toFixed(1) + '%';
                        },
                        style: {
                            fontSize: '11px',
                            fontFamily: 'Poppins, sans-serif',
                            fontWeight: 600
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(v) {
                                return rupiah(v);
                            }
                        },
                        theme: 'dark',
                        style: {
                            fontSize: '12px',
                            fontFamily: 'Poppins, sans-serif'
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        formatter: function(w) {
                                            return rupiah(w.globals.seriesTotals.reduce((a, b) => a + b,
                                                0));
                                        },
                                        fontSize: '13px',
                                        fontFamily: 'Poppins, sans-serif',
                                        fontWeight: 600
                                    }
                                }
                            }
                        }
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['#fff']
                    }
                }).render();
            } else {
                document.querySelector('#chart-pendapatan-golongan').innerHTML =
                    '<div class="text-center text-muted py-5"><i class="bi bi-pie-chart fs-1 d-block mb-2"></i>Belum ada data</div>';
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f8f9fc;
        }

        /* Header Section */
        .header-section {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 1.5rem 2rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        }

        .icon-wrapper {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
        }

        /* KPI Cards */
        .kpi-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-radius: 20px !important;
        }

        .kpi-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
        }

        .icon-circle {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .kpi-card:hover .icon-circle {
            transform: rotate(10deg) scale(1.1);
        }

        .kpi-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        .kpi-progress div {
            transition: width 1.5s ease;
        }

        /* Chart Cards */
        .chart-card,
        .table-card {
            transition: all 0.3s ease;
            border-radius: 20px !important;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .chart-card:hover,
        .table-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08) !important;
        }

        .chart-icon,
        .table-icon {
            transition: all 0.3s ease;
        }

        .chart-card:hover .chart-icon,
        .table-card:hover .table-icon {
            transform: scale(1.1);
        }

        /* Table Styling */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: rgba(17, 153, 142, 0.03);
        }

        .table tbody td {
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .table tfoot {
            font-weight: 600;
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
        }

        /* Badge Styling */
        .badge {
            font-weight: 500;
            font-size: 0.75rem;
            letter-spacing: 0.3px;
        }

        /* Progress Bar */
        .progress {
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(90deg, #11998e, #38ef7d);
            border-radius: 10px;
            transition: width 1s ease;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .header-section {
                padding: 1rem;
            }

            .icon-wrapper {
                width: 40px;
                height: 40px;
            }

            .kpi-card .card-body {
                padding: 1.25rem !important;
            }

            .kpi-card h4 {
                font-size: 1.25rem !important;
            }

            .table thead th,
            .table tbody td {
                font-size: 0.8rem;
                padding: 0.75rem 0.5rem;
            }

            .badge {
                font-size: 0.65rem;
                padding: 0.25rem 0.5rem;
            }
        }

        @media (max-width: 576px) {
            .header-section .d-flex {
                flex-direction: column;
                align-items: stretch !important;
            }

            .header-section form {
                width: 100%;
            }

            .header-section select {
                width: 100% !important;
            }

            .kpi-card {
                margin-bottom: 0.5rem;
            }

            .chart-card .card-body,
            .table-card .card-body {
                padding: 1rem !important;
            }
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #11998e, #38ef7d);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #0d7a72;
        }

        /* Smooth Animation for Numbers */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .kpi-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .kpi-card:nth-child(2) {
            animation-delay: 0.1s;
        }

        .kpi-card:nth-child(3) {
            animation-delay: 0.2s;
        }

        .kpi-card:nth-child(4) {
            animation-delay: 0.3s;
        }

        .chart-card {
            animation: fadeInUp 0.6s ease 0.2s forwards;
            opacity: 0;
        }

        .table-card {
            animation: fadeInUp 0.6s ease 0.3s forwards;
            opacity: 0;
        }
    </style>
@endpush
