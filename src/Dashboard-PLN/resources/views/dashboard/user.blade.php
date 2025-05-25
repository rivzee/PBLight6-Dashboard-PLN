{{-- resources/views/dashboard/user.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Kinerja - Karyawan')
@section('page_title', 'DASHBOARD KARYAWAN')

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
    <!-- Header with Filters -->
    <div class="filter-bar">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Kinerja</h1>
        <form action="{{ route('dashboard') }}" method="GET" class="filter-form">
            <select name="tahun" class="form-control form-control-sm">
                @foreach(range(date('Y') - 5, date('Y') + 1) as $year)
                    <option value="{{ $year }}" {{ (isset($tahun) && $tahun == $year) ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
            <select name="bulan" class="form-control form-control-sm">
                @foreach(range(1, 12) as $month)
                    <option value="{{ $month }}" {{ (isset($bulan) && $bulan == $month) ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="fas fa-filter fa-sm"></i> Filter
            </button>
        </form>
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
                <div class="stat-value">{{ $nilaiNKO ?? '-' }}</div>
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
                <div class="stat-value">{{ $totalIndikatorTercapai ?? 0 }}/{{ $totalIndikator ?? 0 }}</div>
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
                <div class="stat-value">{{ $persenTercapai ?? 0 }}%</div>
                <div class="progress">
                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $persenTercapai ?? 0 }}%"></div>
                </div>
                <p class="stat-description">Pencapaian KPI</p>
            </div>
        </div>

        <div class="grid-span-3">
            <div class="card stat-card">
                <div class="stat-header">
                    <h3 class="stat-title">Target {{ $tahun ?? date('Y') }}</h3>
                    <div class="stat-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                </div>
                <div class="stat-value">100%</div>
                <p class="stat-description">Target Kinerja</p>
            </div>
        </div>
    </div>

    <!-- Chart Tren NKO -->
    <div class="dashboard-grid">
        <div class="grid-span-12">
            <div class="card chart-card">
                <h3 class="chart-title"><i class="fas fa-chart-line"></i> Tren NKO {{ $tahun ?? date('Y') }}</h3>
                <div id="nkoTrendChart" class="chart-container large">
                    <div class="loading-chart">
                        <i class="fas fa-circle-notch fa-spin fa-3x mb-3"></i>
                        <span>Memuat data...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Analisis Status Indikator -->
    <div class="section-divider">
        <h2><i class="fas fa-chart-pie"></i>Status & Komposisi Indikator</h2>
    </div>

    <!-- Grid untuk chart status -->
    <div class="dashboard-grid">
        <div class="grid-span-6">
            <div class="card chart-card">
                <h3 class="chart-title"><i class="fas fa-chart-pie"></i> Komposisi Indikator</h3>
                <div id="indikatorCompositionChart" class="chart-container medium">
                    <div class="loading-chart">
                        <i class="fas fa-circle-notch fa-spin fa-3x mb-3"></i>
                        <span>Memuat data...</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-span-6">
            <div class="card chart-card">
                <h3 class="chart-title"><i class="fas fa-map"></i> Pemetaan Status Indikator</h3>
                <div id="statusMappingChart" class="chart-container medium">
                    <div class="loading-chart">
                        <i class="fas fa-circle-notch fa-spin fa-3x mb-3"></i>
                        <span>Memuat data...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Analisis Tren & Prediksi -->
    <div class="section-divider">
        <h2><i class="fas fa-chart-line"></i>Tren & Prediksi</h2>
    </div>

    <div class="dashboard-grid">
        <div class="grid-span-6">
            <div class="card chart-card">
                <h3 class="chart-title"><i class="fas fa-history"></i> Tren Historis</h3>
                <div id="historicalTrendChart" class="chart-container medium">
                    <div class="loading-chart">
                        <i class="fas fa-circle-notch fa-spin fa-3x mb-3"></i>
                        <span>Memuat data...</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-span-6">
            <div class="card chart-card">
                <h3 class="chart-title"><i class="fas fa-chart-line"></i> Proyeksi {{ $tahun ?? date('Y') }}</h3>
                <div id="forecastChart" class="chart-container medium">
                    <div class="loading-chart">
                        <i class="fas fa-circle-notch fa-spin fa-3x mb-3"></i>
                        <span>Memuat data...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Analisis Per-Pilar -->
    <div class="section-divider">
        <h2><i class="fas fa-layer-group"></i>Analisis Per-Pilar</h2>
    </div>

    <div class="dashboard-grid">
        <div class="grid-span-12">
            <div class="card chart-card">
                <h3 class="chart-title"><i class="fas fa-chart-bar"></i> Kinerja Per-Pilar</h3>
                <div id="pilarChart" class="chart-container medium">
                    <div class="loading-chart">
                        <i class="fas fa-circle-notch fa-spin fa-3x mb-3"></i>
                        <span>Memuat data...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Analisis Per-Bidang -->
    <div class="section-divider">
        <h2><i class="fas fa-building"></i>Analisis Per-Bidang</h2>
    </div>

    <div class="dashboard-grid">
        <div class="grid-span-12">
            <div class="card chart-card">
                <h3 class="chart-title"><i class="fas fa-chart-bar"></i> Kinerja Per-Bidang</h3>
                <div id="bidangChart" class="chart-container medium">
                    <div class="loading-chart">
                        <i class="fas fa-circle-notch fa-spin fa-3x mb-3"></i>
                        <span>Memuat data...</span>
                    </div>
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
                            @if(!empty($analisisData['tertinggi']) && is_array($analisisData['tertinggi']))
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
                            @if(!empty($analisisData['terendah']) && is_array($analisisData['terendah']))
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
</div>

@endsection

@section('scripts')
<script>
// Script untuk inisialisasi seluruh chart
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi Chart Tren NKO
    initNkoTrendChart();

    // Inisialisasi Chart Komposisi Indikator
    initIndikatorCompositionChart();

    // Inisialisasi Chart Status Mapping
    initStatusMappingChart();

    // Inisialisasi Chart Tren Historis
    initHistoricalTrendChart();

    // Inisialisasi Chart Forecast
    initForecastChart();

    // Inisialisasi Chart Per-Pilar
    initPilarChart();

    // Inisialisasi Chart Per-Bidang
    initBidangChart();
});

function initNkoTrendChart() {
    const ctx = document.getElementById('nkoTrendChart');

    // Hapus loading indicator
    if (ctx.querySelector('.loading-chart')) {
        ctx.querySelector('.loading-chart').remove();
    }

    const trendData = @json($trendNKO ?? []);

    // Validasi data
    if (!trendData || trendData.length === 0) {
        showNoDataMessage(ctx, 'Tidak ada data tren NKO');
        return;
    }

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: trendData.map(item => item.bulan),
            datasets: [{
                label: 'Nilai NKO',
                data: trendData.map(item => item.nilai),
                backgroundColor: 'rgba(0, 156, 222, 0.2)',
                borderColor: '#009cde',
                borderWidth: 3,
                pointRadius: 5,
                pointBackgroundColor: '#009cde',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false,
                    min: Math.max(0, Math.min(...trendData.map(item => item.nilai)) - 10),
                    max: 100,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    padding: 10,
                    cornerRadius: 6,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 14
                    }
                }
            }
        }
    });
}

// Fungsi untuk menampilkan pesan tidak ada data
function showNoDataMessage(container, message) {
    const messageElement = document.createElement('div');
    messageElement.className = 'text-center py-5';
    messageElement.innerHTML = `
        <i class="fas fa-chart-bar fa-3x text-secondary mb-3"></i>
        <p class="text-secondary">${message}</p>
    `;
    container.appendChild(messageElement);
}

// Implementasi fungsi untuk chart lainnya dengan validasi data
function initIndikatorCompositionChart() {
    const ctx = document.getElementById('indikatorCompositionChart');
    if (ctx.querySelector('.loading-chart')) {
        ctx.querySelector('.loading-chart').remove();
    }

    const compositionData = @json($indikatorComposition ?? []);

    // Validasi data
    if (!compositionData || compositionData.length === 0) {
        showNoDataMessage(ctx, 'Tidak ada data komposisi indikator');
        return;
    }

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: compositionData.map(item => item.status),
            datasets: [{
                data: compositionData.map(item => item.count),
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
}

// Fungsi untuk Status Mapping Chart dengan validasi data
function initStatusMappingChart() {
    const ctx = document.getElementById('statusMappingChart');
    if (ctx.querySelector('.loading-chart')) {
        ctx.querySelector('.loading-chart').remove();
    }

    const mappingData = @json($statusMapping ?? []);

    // Validasi data
    if (!mappingData || mappingData.length === 0) {
        showNoDataMessage(ctx, 'Tidak ada data status mapping');
        return;
    }

    new Chart(ctx, {
        type: 'polarArea',
        data: {
            labels: mappingData.map(item => item.status),
            datasets: [{
                data: mappingData.map(item => item.count),
                backgroundColor: [
                    'rgba(78, 115, 223, 0.8)',
                    'rgba(28, 200, 138, 0.8)',
                    'rgba(54, 185, 204, 0.8)',
                    'rgba(246, 194, 62, 0.8)',
                    'rgba(231, 74, 59, 0.8)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    ticks: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
}

// Fungsi untuk Historical Trend Chart dengan validasi data
function initHistoricalTrendChart() {
    const ctx = document.getElementById('historicalTrendChart');
    if (ctx.querySelector('.loading-chart')) {
        ctx.querySelector('.loading-chart').remove();
    }

    const historicalData = @json($historicalTrend ?? []);

    // Validasi data
    if (!historicalData || historicalData.length === 0) {
        showNoDataMessage(ctx, 'Tidak ada data tren historis');
        return;
    }

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: historicalData.map(item => item.tahun || item.label),
            datasets: [{
                label: 'Tren Historis',
                data: historicalData.map(item => item.nilai || item.value),
                backgroundColor: 'rgba(78, 115, 223, 0.2)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 3,
                pointRadius: 5,
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false,
                    min: 0,
                    max: 100,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
}

// Fungsi untuk Forecast Chart dengan validasi data
function initForecastChart() {
    const ctx = document.getElementById('forecastChart');
    if (ctx.querySelector('.loading-chart')) {
        ctx.querySelector('.loading-chart').remove();
    }

    const forecastData = @json($forecastData ?? []);

    // Validasi data
    if (!forecastData || forecastData.length === 0) {
        showNoDataMessage(ctx, 'Tidak ada data proyeksi');
        return;
    }

    // Pisahkan data aktual dan proyeksi
    const labels = forecastData.map(item => item.bulan || item.label);
    const actualData = forecastData.map(item => {
        if (item.tipe === 'Aktual' || item.type === 'actual') {
            return item.nilai || item.value;
        }
        return null;
    });
    const projectionData = forecastData.map(item => {
        if (item.tipe === 'Forecast' || item.type === 'forecast') {
            return item.nilai || item.value;
        }
        return null;
    });

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Aktual',
                    data: actualData,
                    backgroundColor: 'rgba(78, 115, 223, 0.2)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 3,
                    pointRadius: 5,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    tension: 0.3,
                    fill: false
                },
                {
                    label: 'Proyeksi',
                    data: projectionData,
                    backgroundColor: 'rgba(246, 194, 62, 0.2)',
                    borderColor: 'rgba(246, 194, 62, 1)',
                    borderWidth: 3,
                    borderDash: [5, 5],
                    pointRadius: 5,
                    pointBackgroundColor: 'rgba(246, 194, 62, 1)',
                    tension: 0.3,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false,
                    min: 0,
                    max: 100,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
}

// Fungsi untuk Pilar Chart dengan validasi data
function initPilarChart() {
    const ctx = document.getElementById('pilarChart');
    if (ctx.querySelector('.loading-chart')) {
        ctx.querySelector('.loading-chart').remove();
    }

    const pilarData = @json($pilarData ?? []);

    // Validasi data
    if (!pilarData || pilarData.length === 0) {
        showNoDataMessage(ctx, 'Tidak ada data pilar');
        return;
    }

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: pilarData.map(item => item.nama),
            datasets: [{
                label: 'Nilai Pilar',
                data: pilarData.map(item => item.nilai),
                backgroundColor: [
                    'rgba(78, 115, 223, 0.8)',
                    'rgba(28, 200, 138, 0.8)',
                    'rgba(54, 185, 204, 0.8)',
                    'rgba(246, 194, 62, 0.8)',
                    'rgba(231, 74, 59, 0.8)',
                    'rgba(111, 66, 193, 0.8)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

// Fungsi untuk Bidang Chart dengan validasi data
function initBidangChart() {
    const ctx = document.getElementById('bidangChart');
    if (ctx.querySelector('.loading-chart')) {
        ctx.querySelector('.loading-chart').remove();
    }

    const bidangData = @json($bidangData ?? []);

    // Validasi data
    if (!bidangData || bidangData.length === 0) {
        showNoDataMessage(ctx, 'Tidak ada data bidang');
        return;
    }

    new Chart(ctx, {
        type: 'horizontalBar',
        data: {
            labels: bidangData.map(item => item.nama),
            datasets: [{
                label: 'Nilai Bidang',
                data: bidangData.map(item => item.nilai),
                backgroundColor: 'rgba(54, 185, 204, 0.8)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        drawBorder: false
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}
</script>
@endsection

