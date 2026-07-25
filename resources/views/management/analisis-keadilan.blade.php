@extends('layouts.app')
@section('title', 'Analisis Subsidi & Keadilan')
@section('page-title', 'Analisis Subsidi & Keadilan')

@section('content')
    <!-- Header dengan efek glassmorphism -->
    <div class="header-section mb-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
            <div>
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="icon-wrapper" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="bi bi-shield-check fs-3 text-white"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0"
                            style="font-size: 1.75rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                            Analisis Subsidi &amp; Keadilan
                        </h5>
                    </div>
                </div>
                <p class="text-muted mb-0" style="font-size: 0.95rem; max-width: 660px;">
                    <i class="bi bi-info-circle me-1"></i>
                    Mengukur seberapa adil penetapan UKT. Idealnya, mahasiswa dengan penghasilan keluarga lebih rendah
                    membayar UKT lebih kecil. Analisis ini membandingkan <strong>penghasilan keluarga</strong> dengan
                    <strong>nominal UKT</strong> yang ditetapkan.
                </p>
            </div>
            <form method="GET" class="d-flex align-items-center gap-2 flex-shrink-0">
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

    @if ($n === 0)
        <div class="alert-modern alert alert-warning border-0 rounded-4 shadow-sm p-4">
            <div class="d-flex align-items-start gap-3">
                <div class="alert-icon bg-warning bg-opacity-10 p-3 rounded-3">
                    <i class="bi bi-exclamation-triangle-fill fs-4 text-warning"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">Belum Ada Data</h6>
                    <p class="mb-0 text-muted">Belum ada data lengkap (butuh data ekonomi &amp; hasil UKT) untuk dianalisis
                        pada periode ini.</p>
                </div>
            </div>
        </div>
    @else
        <!-- KPI Cards dengan desain premium -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="card kpi-card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-body p-4 position-relative" style="background: {{ $warnaKeadilan }};">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="z-1 position-relative">
                                <p class="text-white-50 small mb-1 fw-medium">Skor Keadilan</p>
                                <h3 class="fw-bold text-white mb-0" style="font-size: 2rem;">{{ $skorKeadilan }}<span
                                        class="fs-6 opacity-75">/100</span></h3>
                                <span
                                    class="badge bg-white bg-opacity-25 text-white mt-1 px-3 py-1 rounded-pill">{{ $labelKeadilan }}</span>
                            </div>
                            <div class="icon-circle-glow">
                                <i class="bi bi-shield-check fs-4 text-white"></i>
                            </div>
                        </div>
                        <div class="kpi-progress"
                            style="position: absolute; bottom: 0; left: 0; right: 0; height: 4px; background: rgba(255,255,255,0.2);">
                            <div class="h-100"
                                style="width: {{ $skorKeadilan }}%; background: rgba(255,255,255,0.7); border-radius: 0 4px 4px 0;">
                            </div>
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
                                <p class="text-white-50 small mb-1 fw-medium">Koefisien Korelasi</p>
                                <h3 class="fw-bold text-white mb-0" style="font-size: 2rem;">{{ number_format($r, 3) }}</h3>
                                <small class="text-white-50">penghasilan vs UKT</small>
                            </div>
                            <div class="icon-circle-glow">
                                <i class="bi bi-graph-up-arrow fs-4 text-white"></i>
                            </div>
                        </div>
                        <div class="kpi-progress"
                            style="position: absolute; bottom: 0; left: 0; right: 0; height: 4px; background: rgba(255,255,255,0.2);">
                            <div class="h-100"
                                style="width: {{ min(abs($r) * 100, 100) }}%; background: rgba(255,255,255,0.7); border-radius: 0 4px 4px 0;">
                            </div>
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
                                <p class="text-white-50 small mb-1 fw-medium">UKT Terlalu Tinggi</p>
                                <h3 class="fw-bold text-white mb-0" style="font-size: 2rem;">
                                    {{ $uktTerlaluTinggi->count() }}</h3>
                                <small class="text-white-50">ekonomi lemah, UKT besar</small>
                            </div>
                            <div class="icon-circle-glow">
                                <i class="bi bi-arrow-up-circle fs-4 text-white"></i>
                            </div>
                        </div>
                        <div class="kpi-progress"
                            style="position: absolute; bottom: 0; left: 0; right: 0; height: 4px; background: rgba(255,255,255,0.2);">
                            <div class="h-100"
                                style="width: {{ min(($uktTerlaluTinggi->count() / max($n, 1)) * 100, 100) }}%; background: rgba(255,255,255,0.7); border-radius: 0 4px 4px 0;">
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
                                <p class="text-white-50 small mb-1 fw-medium">UKT Terlalu Rendah</p>
                                <h3 class="fw-bold text-white mb-0" style="font-size: 2rem;">
                                    {{ $uktTerlaluRendah->count() }}</h3>
                                <small class="text-white-50">ekonomi kuat, UKT kecil</small>
                            </div>
                            <div class="icon-circle-glow">
                                <i class="bi bi-arrow-down-circle fs-4 text-white"></i>
                            </div>
                        </div>
                        <div class="kpi-progress"
                            style="position: absolute; bottom: 0; left: 0; right: 0; height: 4px; background: rgba(255,255,255,0.2);">
                            <div class="h-100"
                                style="width: {{ min(($uktTerlaluRendah->count() / max($n, 1)) * 100, 100) }}%; background: rgba(255,255,255,0.7); border-radius: 0 4px 4px 0;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interpretasi dengan desain modern -->
        <div class="card interpretasi-card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row align-items-md-start gap-4">
                    <div class="interpretasi-icon flex-shrink-0"
                        style="background: {{ $warnaKeadilan }}20; padding: 1rem; border-radius: 16px;">
                        <i class="bi bi-info-circle-fill fs-3" style="color: {{ $warnaKeadilan }};"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-2" style="font-size: 1.1rem;">
                            <i class="bi bi-lightbulb me-2" style="color: {{ $warnaKeadilan }};"></i>
                            Interpretasi Hasil Analisis
                        </h6>
                        <p class="text-muted mb-0" style="font-size: 0.95rem; line-height: 1.8;">
                            Koefisien korelasi <strong class="text-primary">{{ number_format($r, 3) }}</strong>
                            menunjukkan sistem tergolong
                            <strong style="color: {{ $warnaKeadilan }};">{{ $labelKeadilan }}</strong>.
                            Nilai mendekati <strong>+1</strong> berarti semakin tinggi penghasilan keluarga, semakin tinggi
                            UKT
                            (subsidi tepat sasaran). Nilai mendekati <strong>0</strong> berarti UKT kurang mencerminkan
                            kemampuan ekonomi. Terdapat
                            <strong
                                class="text-danger">{{ $uktTerlaluTinggi->count() + $uktTerlaluRendah->count() }}</strong>
                            kasus anomali yang sebaiknya ditinjau ulang.
                        </p>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <span class="badge rounded-pill px-3 py-2"
                                style="background: {{ $warnaKeadilan }}20; color: {{ $warnaKeadilan }};">
                                <i class="bi bi-check-circle me-1"></i> {{ $labelKeadilan }}
                            </span>
                            <span class="badge rounded-pill px-3 py-2" style="background: #667eea20; color: #667eea;">
                                <i class="bi bi-people me-1"></i> {{ $n }} Mahasiswa Dianalisis
                            </span>
                            <span class="badge rounded-pill px-3 py-2" style="background: #f59e0b20; color: #f59e0b;">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                {{ $uktTerlaluTinggi->count() + $uktTerlaluRendah->count() }} Anomali
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts dengan desain elegan -->
        <div class="row g-4 mb-5">
            <div class="col-lg-6">
                <div class="card chart-card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="chart-icon bg-primary bg-opacity-10 p-2 rounded-3">
                                <i class="bi bi-bar-chart-line-fill text-primary"></i>
                            </div>
                            <div>
                                <h6 class="fw-semibold mb-0">UKT vs Tingkat Ekonomi</h6>
                                <small class="text-muted">Rata-rata UKT &amp; penghasilan per kelompok ekonomi</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div id="chart-tier" style="height: 380px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card chart-card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="chart-icon bg-danger bg-opacity-10 p-2 rounded-3">
                                <i class="bi bi-diagram-3-fill text-danger"></i>
                            </div>
                            <div>
                                <h6 class="fw-semibold mb-0">Sebaran Penghasilan vs UKT</h6>
                                <small class="text-muted">Titik merah = anomali (perlu ditinjau)</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div id="chart-scatter" style="height: 380px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel tingkatan ekonomi -->
        <div class="card table-card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="table-icon bg-primary bg-opacity-10 p-2 rounded-3">
                        <i class="bi bi-ladder text-primary"></i>
                    </div>
                    <div>
                        <h6 class="fw-semibold mb-0">Rincian per Tingkat Ekonomi</h6>
                        <small class="text-muted">Distribusi mahasiswa berdasarkan kelompok ekonomi</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="border-0 py-3 px-4">Kelompok</th>
                                <th class="border-0 py-3">Jumlah</th>
                                <th class="border-0 py-3">Rata-rata Penghasilan</th>
                                <th class="border-0 py-3">Rata-rata UKT</th>
                                <th class="border-0 py-3 pe-4">Rata-rata Golongan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($perTier as $t)
                                <tr>
                                    <td class="py-3 px-4">
                                        <span class="badge rounded-pill px-3 py-2"
                                            style="background: {{ $t['warna'] ?? '#667eea' }}20; color: {{ $t['warna'] ?? '#667eea' }};">
                                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                                            {{ $t['label'] }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="fw-semibold">{{ number_format($t['jumlah'], 0, ',', '.') }}</span>
                                        <small class="text-muted ms-1">mahasiswa</small>
                                    </td>
                                    <td class="py-3">Rp {{ number_format($t['rata_income'], 0, ',', '.') }}</td>
                                    <td class="py-3">
                                        <span class="fw-semibold">Rp
                                            {{ number_format($t['rata_nominal'], 0, ',', '.') }}</span>
                                    </td>
                                    <td class="py-3 pe-4">
                                        <span class="badge rounded-pill px-3 py-2"
                                            style="background: #11998e20; color: #11998e;">
                                            Golongan {{ $t['rata_golongan'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tabel anomali dengan desain premium -->
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card anomaly-card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="anomaly-icon bg-danger bg-opacity-10 p-2 rounded-3">
                                <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                            </div>
                            <div>
                                <h6 class="fw-semibold mb-0 text-danger">UKT Terlalu Tinggi</h6>
                                <small class="text-muted">Ekonomi lemah (penghasilan &le; Rp
                                    {{ number_format($q1Income, 0, ',', '.') }}) tapi UKT tinggi</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0">
                                <thead style="background: #fef2f2;">
                                    <tr>
                                        <th class="border-0 py-3 px-4">Nama</th>
                                        <th class="border-0 py-3">Prodi</th>
                                        <th class="border-0 py-3">Penghasilan</th>
                                        <th class="border-0 py-3 pe-4">UKT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($uktTerlaluTinggi as $a)
                                        <tr>
                                            <td class="py-2 px-4 fw-semibold">{{ $a['nama'] }}</td>
                                            <td class="py-2 small text-muted">{{ $a['prodi'] }}</td>
                                            <td class="py-2">Rp {{ number_format($a['income'], 0, ',', '.') }}</td>
                                            <td class="py-2 pe-4">
                                                <span class="badge rounded-pill px-3 py-2"
                                                    style="background: #ef444420; color: #ef4444;">
                                                    Rp {{ number_format($a['nominal'], 0, ',', '.') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="bi bi-check-circle fs-3 d-block mb-2 text-success"></i>
                                                Tidak ada anomali
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card anomaly-card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="anomaly-icon bg-info bg-opacity-10 p-2 rounded-3">
                                <i class="bi bi-cash-coin text-info"></i>
                            </div>
                            <div>
                                <h6 class="fw-semibold mb-0 text-info">UKT Terlalu Rendah</h6>
                                <small class="text-muted">Ekonomi kuat (penghasilan &ge; Rp
                                    {{ number_format($q3Income, 0, ',', '.') }}) tapi UKT rendah</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0">
                                <thead style="background: #eff6ff;">
                                    <tr>
                                        <th class="border-0 py-3 px-4">Nama</th>
                                        <th class="border-0 py-3">Prodi</th>
                                        <th class="border-0 py-3">Penghasilan</th>
                                        <th class="border-0 py-3 pe-4">UKT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($uktTerlaluRendah as $a)
                                        <tr>
                                            <td class="py-2 px-4 fw-semibold">{{ $a['nama'] }}</td>
                                            <td class="py-2 small text-muted">{{ $a['prodi'] }}</td>
                                            <td class="py-2">Rp {{ number_format($a['income'], 0, ',', '.') }}</td>
                                            <td class="py-2 pe-4">
                                                <span class="badge rounded-pill px-3 py-2"
                                                    style="background: #3b82f620; color: #3b82f6;">
                                                    Rp {{ number_format($a['nominal'], 0, ',', '.') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="bi bi-check-circle fs-3 d-block mb-2 text-success"></i>
                                                Tidak ada anomali
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tierLabels = @json($tierChart['labels'] ?? []);
            const tierNominal = @json($tierChart['nominal'] ?? []);
            const tierIncome = @json($tierChart['income'] ?? []);
            const scatterNormal = @json($scatterNormal ?? []);
            const scatterAnomali = @json($scatterAnomali ?? []);

            function rupiah(v) {
                return 'Rp ' + Number(v).toLocaleString('id-ID');
            }

            function rupiahShort(v) {
                v = Number(v);
                if (v >= 1000000) return 'Rp ' + (v / 1000000).toFixed(1) + ' jt';
                if (v >= 1000) return 'Rp ' + (v / 1000).toFixed(0) + ' rb';
                return 'Rp ' + v;
            }

            const elTier = document.querySelector('#chart-tier');
            if (elTier && tierLabels.length > 0) {
                new ApexCharts(elTier, {
                    series: [{
                        name: 'Rata-rata UKT',
                        type: 'column',
                        data: tierNominal
                    }, {
                        name: 'Rata-rata Penghasilan',
                        type: 'line',
                        data: tierIncome
                    }],
                    chart: {
                        height: '100%',
                        type: 'line',
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
                    stroke: {
                        width: [0, 3],
                        curve: 'smooth'
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 8,
                            columnWidth: '50%',
                            colors: {
                                ranges: [{
                                    from: 0,
                                    to: 0,
                                    color: '#667eea'
                                }]
                            }
                        }
                    },
                    colors: ['#667eea', '#f59e0b'],
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: tierLabels,
                        labels: {
                            style: {
                                fontSize: '11px',
                                fontFamily: 'Poppins, sans-serif'
                            }
                        }
                    },
                    yaxis: [{
                        title: {
                            text: 'Rata-rata UKT',
                            style: {
                                fontSize: '11px',
                                fontFamily: 'Poppins, sans-serif'
                            }
                        },
                        labels: {
                            formatter: function(v) {
                                return rupiahShort(v);
                            }
                        }
                    }, {
                        opposite: true,
                        title: {
                            text: 'Penghasilan',
                            style: {
                                fontSize: '11px',
                                fontFamily: 'Poppins, sans-serif'
                            }
                        },
                        labels: {
                            formatter: function(v) {
                                return rupiahShort(v);
                            }
                        }
                    }],
                    tooltip: {
                        shared: true,
                        theme: 'dark',
                        style: {
                            fontSize: '12px',
                            fontFamily: 'Poppins, sans-serif'
                        },
                        y: {
                            formatter: function(v) {
                                return rupiah(v);
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        fontFamily: 'Poppins, sans-serif'
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
                            type: "vertical",
                            shadeIntensity: 0.1,
                            gradientToColors: ['#764ba2'],
                            inverseColors: false,
                            opacityFrom: 1,
                            opacityTo: 1,
                            stops: [0, 100]
                        }
                    }
                }).render();
            } else if (elTier) {
                elTier.innerHTML =
                    '<div class="text-center text-muted py-5"><i class="bi bi-bar-chart-line fs-1 d-block mb-2"></i>Belum ada data</div>';
            }

            const elScatter = document.querySelector('#chart-scatter');
            if (elScatter && (scatterNormal.length > 0 || scatterAnomali.length > 0)) {
                new ApexCharts(elScatter, {
                    series: [{
                        name: 'Normal',
                        data: scatterNormal
                    }, {
                        name: 'Anomali',
                        data: scatterAnomali
                    }],
                    chart: {
                        height: '100%',
                        type: 'scatter',
                        fontFamily: 'Poppins, sans-serif',
                        zoom: {
                            enabled: true,
                            type: 'xy'
                        },
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
                    colors: ['#94a3b8', '#ef4444'],
                    markers: {
                        size: [6, 8],
                        strokeColors: ['#94a3b8', '#ef4444'],
                        strokeWidth: [1, 1],
                        hover: {
                            size: [8, 10]
                        }
                    },
                    xaxis: {
                        title: {
                            text: 'Penghasilan Keluarga / bulan',
                            style: {
                                fontSize: '11px',
                                fontFamily: 'Poppins, sans-serif'
                            }
                        },
                        tickAmount: 6,
                        labels: {
                            formatter: function(v) {
                                return rupiahShort(v);
                            },
                            style: {
                                fontSize: '10px',
                                fontFamily: 'Poppins, sans-serif'
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Nominal UKT',
                            style: {
                                fontSize: '11px',
                                fontFamily: 'Poppins, sans-serif'
                            }
                        },
                        labels: {
                            formatter: function(v) {
                                return rupiahShort(v);
                            },
                            style: {
                                fontSize: '10px',
                                fontFamily: 'Poppins, sans-serif'
                            }
                        }
                    },
                    tooltip: {
                        theme: 'dark',
                        style: {
                            fontSize: '12px',
                            fontFamily: 'Poppins, sans-serif'
                        },
                        x: {
                            formatter: function(v) {
                                return rupiah(v);
                            }
                        },
                        y: {
                            formatter: function(v) {
                                return rupiah(v);
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        fontFamily: 'Poppins, sans-serif'
                    },
                    grid: {
                        borderColor: '#e9ecef',
                        strokeDashArray: 5,
                        padding: {
                            left: 10,
                            right: 10
                        }
                    }
                }).render();
            } else if (elScatter) {
                elScatter.innerHTML =
                    '<div class="text-center text-muted py-5"><i class="bi bi-diagram-3 fs-1 d-block mb-2"></i>Belum ada data</div>';
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
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        /* Alert Modern */
        .alert-modern {
            background: rgba(255, 193, 7, 0.08);
            border: 1px solid rgba(255, 193, 7, 0.2);
            backdrop-filter: blur(10px);
        }

        .alert-icon {
            border-radius: 12px;
        }

        /* KPI Cards */
        .kpi-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-radius: 20px !important;
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
        }

        .kpi-card:nth-child(1) {
            animation-delay: 0s;
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

        .kpi-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
        }

        .icon-circle-glow {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.15);
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .kpi-card:hover .icon-circle-glow {
            transform: rotate(10deg) scale(1.1);
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.2);
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
            transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Interpretasi Card */
        .interpretasi-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .interpretasi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08) !important;
        }

        .interpretasi-icon {
            transition: all 0.3s ease;
        }

        .interpretasi-card:hover .interpretasi-icon {
            transform: scale(1.05) rotate(-5deg);
        }

        /* Chart Cards */
        .chart-card,
        .table-card,
        .anomaly-card {
            transition: all 0.3s ease;
            border-radius: 20px !important;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
        }

        .chart-card {
            animation-delay: 0.2s;
        }

        .table-card {
            animation-delay: 0.3s;
        }

        .anomaly-card {
            animation-delay: 0.4s;
        }

        .chart-card:hover,
        .table-card:hover,
        .anomaly-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08) !important;
        }

        .chart-icon,
        .table-icon,
        .anomaly-icon {
            transition: all 0.3s ease;
        }

        .chart-card:hover .chart-icon,
        .table-card:hover .table-icon,
        .anomaly-card:hover .anomaly-icon {
            transform: scale(1.1) rotate(-5deg);
        }

        /* Table Styling */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: rgba(102, 126, 234, 0.03);
        }

        .table tbody td {
            font-size: 0.9rem;
            vertical-align: middle;
        }

        /* Badge Styling */
        .badge {
            font-weight: 500;
            font-size: 0.75rem;
            letter-spacing: 0.3px;
        }

        /* Animations */
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

            .kpi-card h3 {
                font-size: 1.5rem !important;
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

            .interpretasi-card .d-flex {
                flex-direction: column;
                align-items: stretch !important;
            }

            .interpretasi-icon {
                align-self: flex-start;
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
            .table-card .card-body,
            .anomaly-card .card-body {
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
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #5a6fd6;
        }

        /* Loading Skeleton */
        .skeleton {
            animation: shimmer 1.5s ease-in-out infinite;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }
    </style>
@endpush
