@extends('layouts.app')

@section('title', 'Data Kinerja')

@section('styles')
<style>
    /* Main Container */
    .dashboard-content {
        max-width: 1800px;
        margin: 0 auto;
        padding: 0 15px;
    }

    /* Grid System */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        grid-gap: 20px;
        margin-bottom: 30px;
    }

    .grid-span-3 {
        grid-column: span 3;
    }

    .grid-span-4 {
        grid-column: span 4;
    }

    .grid-span-6 {
        grid-column: span 6;
    }

    .grid-span-8 {
        grid-column: span 8;
    }

    .grid-span-12 {
        grid-column: span 12;
    }

    @media (max-width: 1200px) {
        .grid-span-3 {
            grid-column: span 6;
        }
        .grid-span-4 {
            grid-column: span 6;
        }
        .grid-span-6 {
            grid-column: span 12;
        }
        .grid-span-8 {
            grid-column: span 12;
        }
    }

    @media (max-width: 768px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
        .grid-span-3, .grid-span-4, .grid-span-6, .grid-span-8, .grid-span-12 {
            grid-column: span 1;
        }
    }

    /* Section Divider */
    .section-divider {
        display: flex;
        align-items: center;
        margin: 40px 0 20px;
        position: relative;
    }

    .section-divider h2 {
        font-size: 18px;
        font-weight: 600;
        color: var(--pln-text, #333);
        margin: 0;
        padding-right: 15px;
        background: #f8f9fc;
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
    }

    .section-divider h2 i {
        margin-right: 10px;
        color: var(--pln-light-blue, #00c6ff);
    }

    .section-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e3e6f0;
    }

    /* Card Components */
    .card {
        background: var(--pln-accent-bg, #ffffff);
        border-radius: 16px;
        border: 1px solid var(--pln-border, #e8e8e8);
        box-shadow: 0 8px 20px var(--pln-shadow, rgba(0,0,0,0.1));
        position: relative;
        overflow: hidden;
        height: 100%;
        transition: all 0.3s ease;
    }

    .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--pln-blue, #007bff), var(--pln-light-blue, #00c6ff));
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px var(--pln-shadow, rgba(0,0,0,0.15));
    }

    /* Stat Card */
    .stat-card {
        padding: 20px;
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .stat-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--pln-text, #333);
        margin: 0;
    }

    .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--pln-blue, #007bff), var(--pln-light-blue, #00c6ff));
        color: white;
        font-size: 20px;
        box-shadow: 0 5px 15px rgba(0, 156, 222, 0.3);
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--pln-text, #333);
        margin: 15px 0 5px;
    }

    .stat-description {
        font-size: 13px;
        color: var(--pln-text-secondary, #6c757d);
        margin: 0;
    }

    /* Chart Card */
    .chart-card {
        padding: 25px;
    }

    .chart-title {
        font-size: 18px;
        color: var(--pln-light-blue, #00c6ff);
        margin-bottom: 20px;
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .chart-title i {
        margin-right: 10px;
    }

    /* Chart Container */
    .chart-container {
        position: relative;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    .chart-container.large {
        height: 450px;
    }

    .chart-container.medium {
        height: 400px;
    }

    .chart-container.small {
        height: 300px;
    }

    .loading-chart {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100%;
        color: var(--pln-text-secondary, #6c757d);
        padding: 20px;
    }

    /* Table Styling */
    .data-table-container {
        padding: 0;
        overflow: hidden;
    }

    .data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
    }

    .data-table thead th {
        background: linear-gradient(90deg, var(--pln-blue, #007bff), var(--pln-light-blue, #00c6ff));
        color: #fff;
        font-weight: 600;
        text-align: left;
        padding: 15px;
        font-size: 14px;
        border: none;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .data-table tbody tr {
        background-color: var(--pln-accent-bg, #ffffff);
        transition: all 0.2s ease;
    }

    .data-table tbody tr:hover {
        background-color: rgba(0, 156, 222, 0.05);
    }

    .data-table td {
        padding: 12px 15px;
        border-top: 1px solid var(--pln-border, #e8e8e8);
        font-size: 14px;
        vertical-align: middle;
    }

    .data-table .badge {
        padding: 5px 10px;
        border-radius: 12px;
        font-weight: 500;
        font-size: 12px;
    }

    /* Progress Bar */
    .progress {
        height: 8px;
        background-color: var(--pln-surface, #f0f0f0);
        border-radius: 4px;
        overflow: hidden;
        margin: 10px 0;
    }

    .progress-bar {
        height: 100%;
        border-radius: 4px;
        transition: width 0.5s ease-in-out;
    }

    /* Card with scrollable content */
    .scrollable-card {
        max-height: 500px;
        overflow-y: auto;
    }

    .scrollable-card::-webkit-scrollbar {
        width: 8px;
    }

    .scrollable-card::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 10px;
    }

    .scrollable-card::-webkit-scrollbar-thumb {
        background: var(--pln-light-blue, #00c6ff);
        border-radius: 10px;
    }

    /* Filter Bar */
    .filter-bar {
        background: var(--pln-accent-bg, #ffffff);
        border-radius: 16px;
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid var(--pln-border, #e8e8e8);
        box-shadow: 0 4px 15px var(--pln-shadow, rgba(0,0,0,0.1));
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .filter-form {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-form select {
        min-width: 120px;
    }
</style>
@endsection

@section('content')
<div class="dashboard-content">
    <!-- Filter -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('dataKinerja.index') }}" method="get" class="form-inline">
                        <div class="row align-items-center">
                            <div class="col-md-4 mb-2 mb-md-0">
                                <label for="tahun" class="mr-2">Tahun:</label>
                                <select name="tahun" id="tahun" class="form-control">
                                    @php
                                        $currentYear = date('Y');
                                        $startYear = $currentYear - 5;
                                        $endYear = $currentYear;
                                    @endphp

                                    @for($year = $endYear; $year >= $startYear; $year--)
                                        <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-4 mb-2 mb-md-0">
                                <label for="bulan" class="mr-2">Bulan:</label>
                                <select name="bulan" id="bulan" class="form-control">
                                    @php
                                        $namaBulan = [
                                            1 => 'Januari',
                                            2 => 'Februari',
                                            3 => 'Maret',
                                            4 => 'April',
                                            5 => 'Mei',
                                            6 => 'Juni',
                                            7 => 'Juli',
                                            8 => 'Agustus',
                                            9 => 'September',
                                            10 => 'Oktober',
                                            11 => 'November',
                                            12 => 'Desember'
                                        ];
                                    @endphp

                                    @foreach($namaBulan as $value => $nama)
                                        <option value="{{ $value }}" {{ $bulan == $value ? 'selected' : '' }}>{{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-2 mb-md-0">
                                <label for="status_verifikasi" class="mr-2">Status:</label>
                                <select name="status_verifikasi" id="status_verifikasi" class="form-control">
                                    <option value="all" {{ $statusVerifikasi == 'all' ? 'selected' : '' }}>Semua Data</option>
                                    <option value="verified" {{ $statusVerifikasi == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                                    <option value="unverified" {{ $statusVerifikasi == 'unverified' ? 'selected' : '' }}>Belum Terverifikasi</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter mr-1"></i> Filter
                                </button>
                                <a href="{{ route('dataKinerja.index') }}" class="btn btn-outline-secondary ml-2">
                                    <i class="fas fa-sync-alt mr-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Data Bulan: <strong>{{ date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun)) }}</strong></h5>
                        <span class="badge bg-primary text-white">{{ $totalIndikator }} Indikator</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.alert')

    <!-- Ringkasan Statistik -->
    <div class="dashboard-grid">
        <div class="grid-span-3">
            <div class="card stat-card">
                <div class="stat-header">
                    <h3 class="stat-title">NKO Score</h3>
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($nilaiNKO, 2) }}%</div>
                <p class="stat-description">Nilai Kinerja Organisasi</p>
            </div>
        </div>

        <div class="grid-span-3">
            <div class="card stat-card">
                <div class="stat-header">
                    <h3 class="stat-title">Indikator</h3>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $totalIndikatorTercapai }}/{{ $totalIndikator }}</div>
                <p class="stat-description">Total Indikator Tercapai</p>
            </div>
        </div>

        <div class="grid-span-3">
            <div class="card stat-card">
                <div class="stat-header">
                    <h3 class="stat-title">Persentase</h3>
                    <div class="stat-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $persenTercapai }}%</div>
                <div class="progress">
                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $persenTercapai }}%"></div>
                </div>
                <p class="stat-description">Pencapaian KPI</p>
            </div>
        </div>

        <div class="grid-span-3">
            <div class="card stat-card">
                <div class="stat-header">
                    <h3 class="stat-title">Target {{ $tahun }}</h3>
                    <div class="stat-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($target, 0) }}%</div>
                <p class="stat-description">Target Kinerja</p>
            </div>
        </div>
    </div>

    <!-- Chart Tren NKO -->
<div class="grid-span-12">
    <div class="card chart-card">
        <h3 class="chart-title">
            <i class="fas fa-chart-line"></i> Tren NKO {{ $tahun }}
        </h3>
        <div class="chart-container large position-relative">
            <!-- Elemen canvas Chart -->
            <canvas id="nkoTrendChart" height="300"></canvas>
        </div>
    </div>
</div>


<!-- Section: Analisis Status Indikator -->
<div class="section-divider">
    <h2><i class="fas fa-chart-pie"></i> Status & Komposisi Indikator</h2>
</div>

<!-- Grid untuk chart status -->
<div class="dashboard-grid">
    <!-- Komposisi Indikator -->
    <div class="grid-span-6">
        <div class="card chart-card">
            <h3 class="chart-title"><i class="fas fa-chart-pie"></i> Komposisi Indikator</h3>
            <div class="chart-container medium position-relative">

                <!-- Canvas untuk Chart -->
                <canvas id="indikatorCompositionChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Status Mapping -->
    <div class="grid-span-6">
        <div class="card chart-card">
            <h3 class="chart-title"><i class="fas fa-map"></i> Pemetaan Status Indikator</h3>
            <div class="chart-container medium position-relative">
                <!-- Canvas untuk Chart -->
                <canvas id="statusMappingChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Section: Analisis Tren & Prediksi -->
<div class="section-divider">
    <h2><i class="fas fa-chart-line"></i> Tren & Prediksi</h2>
</div>

<div class="dashboard-grid">
    <!-- Tren Historis -->
    <div class="grid-span-6">
        <div class="card chart-card">
            <h3 class="chart-title"><i class="fas fa-history"></i> Tren Historis</h3>
            <div class="chart-container medium position-relative">
                <canvas id="historicalTrendChart" height="280"></canvas>
            </div>
        </div>
    </div>

    <!-- Forecast -->
    <div class="grid-span-6">
        <div class="card chart-card">
            <h3 class="chart-title"><i class="fas fa-chart-line"></i> Proyeksi {{ $tahun }}</h3>
            <div class="chart-container medium position-relative">
                <canvas id="forecastChart" height="280"></canvas>
            </div>
        </div>
    </div>
</div>


<!-- Section: Analisis Per-Pilar -->
<div class="section-divider">
    <h2><i class="fas fa-layer-group"></i> Analisis Per-Pilar</h2>
</div>

<div class="dashboard-grid">
    <div class="grid-span-12">
        <div class="card chart-card">
            <h3 class="chart-title"><i class="fas fa-chart-bar"></i> Kinerja Per-Pilar</h3>
            <div class="chart-container medium position-relative">
                <canvas id="pilarChart" height="280"></canvas>
            </div>
        </div>
    </div>
</div>


<!-- Section: Analisis Per-Bidang -->
<div class="section-divider">
    <h2><i class="fas fa-building"></i> Analisis Per-Bidang</h2>
</div>

<div class="dashboard-grid">
    <div class="grid-span-12">
        <div class="card chart-card">
            <h3 class="chart-title"><i class="fas fa-chart-bar"></i> Kinerja Per-Bidang</h3>
            <div class="chart-container medium position-relative">

                <canvas id="bidangChart" height="280"></canvas>
            </div>
        </div>
    </div>
</div>

    <!-- Section: Top & Bottom Indikator -->
    <div class="section-divider">
        <h2><i class="fas fa-sort-amount-down"></i>Indikator Tertinggi & Terendah</h2>
    </div>

    <div class="dashboard-grid">
        <div class="grid-span-6">
            <div class="card chart-card">
                <h3 class="chart-title"><i class="fas fa-arrow-up"></i> Indikator Tertinggi</h3>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Indikator</th>
                                <th>Bidang</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($analisisData['tertinggi']) && is_array($analisisData['tertinggi']))
                                @foreach($analisisData['tertinggi'] as $indikator)
                                <tr>
                                    <td>{{ $indikator['kode'] ?? '-' }}</td>
                                    <td>{{ $indikator['nama'] ?? '-' }}</td>
                                    <td>{{ $indikator['bidang'] ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-success text-white">{{ $indikator['nilai'] ?? 0 }}%</span>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data tersedia</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid-span-6">
            <div class="card chart-card">
                <h3 class="chart-title"><i class="fas fa-arrow-down"></i> Indikator Terendah</h3>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Indikator</th>
                                <th>Bidang</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($analisisData['terendah']) && is_array($analisisData['terendah']))
                                @foreach($analisisData['terendah'] as $indikator)
                                <tr>
                                    <td>{{ $indikator['kode'] ?? '-' }}</td>
                                    <td>{{ $indikator['nama'] ?? '-' }}</td>
                                    <td>{{ $indikator['bidang'] ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-danger text-white">{{ $indikator['nilai'] ?? 0 }}%</span>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data tersedia</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Perkembangan Bulanan -->
    <div class="section-divider">
        <h2><i class="fas fa-calendar-alt"></i>Perkembangan Bulanan</h2>
    </div>

    <div class="dashboard-grid">
        <div class="grid-span-12">
            <div class="card chart-card scrollable-card">
                <h3 class="chart-title"><i class="fas fa-calendar-week"></i> Perkembangan Per Bulan</h3>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th>NKO</th>
                                <th>Status</th>
                                <th>Tercapai</th>
                                <th>Total</th>
                                <th>Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($analisisData['perkembangan']) && is_array($analisisData['perkembangan']))
                                @foreach($analisisData['perkembangan'] as $perkembangan)
                                <tr>
                                    <td>{{ $perkembangan['bulan'] ?? '-' }}</td>
                                    <td>{{ $perkembangan['nko'] ?? 0 }}</td>
                                    <td>
                                        @if(isset($perkembangan['nko']))
                                            @if($perkembangan['nko'] >= 90)
                                                <span class="badge bg-success text-white">Sangat Baik</span>
                                            @elseif($perkembangan['nko'] >= 80)
                                                <span class="badge bg-info text-white">Baik</span>
                                            @elseif($perkembangan['nko'] >= 70)
                                                <span class="badge bg-warning text-white">Cukup</span>
                                            @else
                                                <span class="badge bg-danger text-white">Perlu Perhatian</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary text-white">Tidak Ada Data</span>
                                        @endif
                                    </td>
                                    <td>{{ $perkembangan['tercapai'] ?? 0 }}</td>
                                    <td>{{ $perkembangan['total'] ?? 0 }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $perkembangan['persentase'] ?? 0 }}%"></div>
                                        </div>
                                        <small>{{ $perkembangan['persentase'] ?? 0 }}%</small>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data perkembangan tersedia</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM Loaded - Init charts');

    const trendNKOData = @json($trendNKO ?? []);
    const indikatorCompositionData = @json($indikatorComposition ?? []);
    const statusMappingData = @json($statusMapping ?? []);
    const historicalTrendData = @json($historicalTrend ?? []);
    const forecastData = @json($forecastData ?? []);
    const pilarData = @json($pilarData ?? []);
    const bidangData = @json($bidangData ?? []);

    console.log({ trendNKOData, indikatorCompositionData, statusMappingData });

    initNkoTrendChart(trendNKOData);
    initIndikatorCompositionChart(indikatorCompositionData);
    initStatusMappingChart(statusMappingData);
    initHistoricalTrendChart(historicalTrendData);
    initForecastChart(forecastData);
    initPilarChart(pilarData);
    initBidangChart(bidangData);
});

function initNkoTrendChart(data) {
    const ctx = document.getElementById('nkoTrendChart');
    if (!ctx || !data.length) return;

    const labels = data.map(item => item.bulan);
    const values = data.map(item => item.nko);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'NKO',
                data: values,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78,115,223,0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, max: 100 },
                x: { grid: { display: false } }
            }
        }
    });
}

function initIndikatorCompositionChart(data) {
    const ctx = document.getElementById('indikatorCompositionChart');
    if (!ctx || !data) return;

    const labels = Object.keys(data);
    const values = Object.values(data);

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

function initStatusMappingChart(data) {
    const ctx = document.getElementById('statusMappingChart');
    if (!ctx || !data.length) return;

    new Chart(ctx, {
        type: 'polarArea',
        data: {
            labels: data.map(d => d.status),
            datasets: [{
                data: data.map(d => d.count),
                backgroundColor: [
                    'rgba(78, 115, 223, 0.8)',
                    'rgba(28, 200, 138, 0.8)',
                    'rgba(246, 194, 62, 0.8)',
                    'rgba(231, 74, 59, 0.8)'
                ]
            }]
        },
        options: { responsive: true }
    });
}

function initHistoricalTrendChart(data) {
    const ctx = document.getElementById('historicalTrendChart');
    if (!ctx || !data.length) return;

    const labels = data.map(item => item.bulan);
    const values = data.map(item => item.nko);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Tren Historis',
                data: values,
                backgroundColor: 'rgba(78,115,223,0.2)',
                borderColor: 'rgba(78,115,223,1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: { responsive: true }
    });
}

function initForecastChart(data) {
    const ctx = document.getElementById('forecastChart');
    if (!ctx || !data.length) return;

    const labels = data.map(d => d.bulan);
    const values = data.map(d => d.nko);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Forecast',
                data: values,
                borderColor: '#f6c23e',
                backgroundColor: 'rgba(246, 194, 62, 0.2)',
                borderDash: [5, 5],
                borderWidth: 2,
                tension: 0.3,
                fill: false
            }]
        },
        options: { responsive: true }
    });
}

function initPilarChart(data) {
    const ctx = document.getElementById('pilarChart');
    if (!ctx || Object.keys(data).length === 0) return;

    const labels = Object.keys(data);
    const values = Object.values(data);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Nilai Pilar',
                data: values,
                backgroundColor: '#36b9cc'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, max: 100 }
            }
        }
    });
}

function initBidangChart(data) {
    const ctx = document.getElementById('bidangChart');
    if (!ctx || Object.keys(data).length === 0) return;

    const labels = Object.keys(data);
    const values = Object.values(data);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Nilai Bidang',
                data: values,
                backgroundColor: '#4e73df'
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            scales: {
                x: { beginAtZero: true, max: 100 }
            }
        }
    });
}
</script>

@endsection
