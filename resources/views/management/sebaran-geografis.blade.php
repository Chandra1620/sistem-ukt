@extends('layouts.app')
@section('title', 'Sebaran Geografis')
@section('page-title', 'Sebaran Geografis')

@section('content')
    <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1"
                style="background: linear-gradient(135deg, #0ba360 0%, #3cba92 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                Sebaran Geografis
            </h5>
            <p class="text-muted small mb-0" style="max-width: 660px;">
                Asal daerah mahasiswa beserta dimensi ekonomi &amp; UKT per wilayah. Membantu memetakan
                dari provinsi mana pendaftar berasal dan bagaimana profil subsidinya.
            </p>
        </div>
        <form method="GET" class="d-flex align-items-center gap-2">
            <label class="small text-muted mb-0">Periode:</label>
            <select name="periode_id" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="">Semua Periode</option>
                @foreach ($daftarPeriode as $per)
                    <option value="{{ $per->id }}" {{ (string) $periodeId === (string) $per->id ? 'selected' : '' }}>
                        {{ $per->nama }} ({{ $per->tahun }})
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if ($totalMahasiswa === 0)
        <div class="alert alert-warning rounded-4 border-0 shadow-sm">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Belum ada data pendaftar untuk dipetakan pada periode ini.
        </div>
    @else
        <!-- KPI Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #0ba360 0%, #3cba92 100%);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-white-50 small mb-1">Provinsi Asal</p>
                                <h4 class="fw-bold text-white mb-0">{{ number_format($totalProvinsi, 0, ',', '.') }}</h4>
                                <small class="text-white">provinsi berbeda</small>
                            </div>
                            <div class="rounded-circle p-3 bg-white bg-opacity-25">
                                <i class="bi bi-globe-asia-australia fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-white-50 small mb-1">Provinsi Terbanyak</p>
                                <h5 class="fw-bold text-white mb-0">{{ $provinsiTerbanyak['provinsi'] ?? '-' }}</h5>
                                <small
                                    class="text-white">{{ number_format($provinsiTerbanyak['jumlah'] ?? 0, 0, ',', '.') }}
                                    mahasiswa</small>
                            </div>
                            <div class="rounded-circle p-3 bg-white bg-opacity-25">
                                <i class="bi bi-geo-alt-fill fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-white-50 small mb-1">Kota/Kab Terbanyak</p>
                                <h5 class="fw-bold text-white mb-0">{{ $kotaTerbanyak['daerah'] ?? '-' }}</h5>
                                <small class="text-white">{{ number_format($kotaTerbanyak['jumlah'] ?? 0, 0, ',', '.') }}
                                    mahasiswa</small>
                            </div>
                            <div class="rounded-circle p-3 bg-white bg-opacity-25">
                                <i class="bi bi-buildings-fill fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-white-50 small mb-1">Total Mahasiswa</p>
                                <h4 class="fw-bold text-white mb-0">{{ number_format($totalMahasiswa, 0, ',', '.') }}</h4>
                                <small class="text-white">seluruh pendaftar</small>
                            </div>
                            <div class="rounded-circle p-3 bg-white bg-opacity-25">
                                <i class="bi bi-people-fill fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row g-4 mb-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h6 class="fw-semibold mb-0"><i class="bi bi-map-fill me-2 text-success"></i>Peta Sebaran per
                            Provinsi</h6>
                        <small class="text-muted">Ukuran kotak = jumlah mahasiswa</small>
                    </div>
                    <div class="card-body p-4">
                        <div id="chart-treemap" style="height: 420px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h6 class="fw-semibold mb-0"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>10 Provinsi
                            Teratas</h6>
                        <small class="text-muted">Berdasarkan jumlah mahasiswa</small>
                    </div>
                    <div class="card-body p-4">
                        <div id="chart-bar-provinsi" style="height: 420px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel per provinsi -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h6 class="fw-semibold mb-0"><i class="bi bi-table me-2 text-success"></i>Rincian per Provinsi</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background: linear-gradient(135deg, #f0fff7 0%, #eefdf5 100%);">
                            <tr>
                                <th class="border-0 py-3 px-4">Provinsi</th>
                                <th class="border-0 py-3">Mahasiswa</th>
                                <th class="border-0 py-3">KIP</th>
                                <th class="border-0 py-3">Rata-rata Penghasilan</th>
                                <th class="border-0 py-3">Rata-rata UKT</th>
                                <th class="border-0 py-3 pe-4">Total UKT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($perProvinsi as $prov)
                                <tr>
                                    <td class="py-3 px-4 fw-semibold">{{ $prov['provinsi'] }}</td>
                                    <td class="py-3">{{ number_format($prov['jumlah'], 0, ',', '.') }}</td>
                                    <td class="py-3">
                                        <span class="badge rounded-pill px-3 py-2"
                                            style="background:#0ba36020;color:#0ba360;">{{ number_format($prov['kip'], 0, ',', '.') }}</span>
                                    </td>
                                    <td class="py-3">Rp {{ number_format($prov['rata_income'], 0, ',', '.') }}</td>
                                    <td class="py-3">Rp {{ number_format($prov['rata_ukt'], 0, ',', '.') }}</td>
                                    <td class="py-3 pe-4 fw-semibold">Rp
                                        {{ number_format($prov['total_ukt'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tabel kota/kabupaten -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h6 class="fw-semibold mb-0"><i class="bi bi-buildings me-2 text-warning"></i>15 Kota/Kabupaten Terbanyak
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background: linear-gradient(135deg, #fffdf0 0%, #fffaed 100%);">
                            <tr>
                                <th class="border-0 py-3 px-4">#</th>
                                <th class="border-0 py-3">Kota/Kabupaten</th>
                                <th class="border-0 py-3 pe-4">Jumlah Mahasiswa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($perKota as $i => $kota)
                                <tr>
                                    <td class="py-3 px-4 text-muted">{{ $i + 1 }}</td>
                                    <td class="py-3 fw-semibold">{{ $kota['daerah'] }}</td>
                                    <td class="py-3 pe-4">{{ number_format($kota['jumlah'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const treemapData = @json($treemapData ?? []);
            const barLabels = @json($barTop['labels'] ?? []);
            const barData = @json($barTop['data'] ?? []);

            const elTree = document.querySelector('#chart-treemap');
            if (elTree && treemapData.length > 0) {
                new ApexCharts(elTree, {
                    series: [{
                        data: treemapData
                    }],
                    chart: {
                        type: 'treemap',
                        height: '100%',
                        fontFamily: 'Poppins, sans-serif',
                        toolbar: {
                            show: false
                        }
                    },
                    colors: ['#0ba360', '#3cba92', '#4facfe', '#667eea', '#fa709a', '#fee140', '#f5576c',
                        '#764ba2'
                    ],
                    plotOptions: {
                        treemap: {
                            distributed: true,
                            enableShades: false
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: '12px'
                        }
                    },
                    legend: {
                        show: false
                    }
                }).render();
            } else if (elTree) {
                elTree.innerHTML = '<div class="text-center text-muted py-5">Belum ada data</div>';
            }

            const elBar = document.querySelector('#chart-bar-provinsi');
            if (elBar && barLabels.length > 0) {
                new ApexCharts(elBar, {
                    series: [{
                        name: 'Mahasiswa',
                        data: barData
                    }],
                    chart: {
                        type: 'bar',
                        height: '100%',
                        fontFamily: 'Poppins, sans-serif',
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            borderRadius: 6,
                            borderRadiusApplication: 'end'
                        }
                    },
                    colors: ['#0ba360'],
                    dataLabels: {
                        enabled: true
                    },
                    xaxis: {
                        categories: barLabels
                    },
                    grid: {
                        borderColor: '#e9ecef',
                        strokeDashArray: 5
                    }
                }).render();
            } else if (elBar) {
                elBar.innerHTML = '<div class="text-center text-muted py-5">Belum ada data</div>';
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
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
@endpush
