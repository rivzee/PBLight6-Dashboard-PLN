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
    .pilar-card {
    background: #f1f6fd;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 30px;
    }

    .pilar-card h3.card-title {
    font-size: 18px;
    font-weight: 600;
    color: #007bff;
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    }

    .pilar-card h3.card-title i {
    margin-right: 10px;
    }

    .pillar-container {
    display: flex;
    justify-content: space-between; /* GANTI INI */
    flex-wrap: nowrap;
    margin: 15px 0;
    gap: 15px;
    overflow-x: auto;
    padding-bottom: 5px;
    }


    .pillar-item-link {
    text-decoration: none;
    color: inherit;
    }

    .pillar-item {
    flex: 0 0 auto;
    width: 170px;
    background: #e9f1fa;
    border-radius: 16px;
    padding: 16px 12px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
    transition: transform 0.3s ease;
    position: relative;
    cursor: pointer;
    }
    .pillar-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    height: 4px;
    width: 100%;
    background: linear-gradient(90deg, #007bff, #00c6ff); /* 🔵 Biru gradasi */
    z-index: 2;
    }
    .pillar-item:hover {
    transform: translateY(-5px);
    }

    .pillar-title {
    font-size: 14px;
    font-weight: 700;
    color: #333;
    margin-bottom: 8px;
    line-height: 1.4;
    min-height: 35px;
    }

    .pillar-value {
    font-size: 13px;
    font-weight: 700;
    color: #00aaff;
    margin-bottom: 6px;
    }

    .circle-progress {
    position: relative;
    width: 100px;
    height: 100px;
    margin: 0 auto;
    }

    .circle-progress canvas {
    width: 100px;
    height: 100px;
    }

    .circle-progress-value {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 18px;
    font-weight: bold;
    color: #333;
    }
    .section-wrapper {
    background: #f4f8fd;
    padding: 20px;
    border-radius: 18px;
    border: 1px solid var(--pln-border, #dbe6f5);
    box-shadow: 0 5px 15px rgba(0, 123, 255, 0.05);
    margin-bottom: 30px;

    }

    .section-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    }

    .section-header h3 {
    font-size: 16px;
    font-weight: 600;
    color: var(--pln-text, #333);
    position: relative;
    padding-left: 20px;
    }

    .section-header h3::before {
    content: '';
    position: absolute;
    left: 0;
    top: 3px;
    width: 5px;
    height: 80%;
    background: var(--pln-light-blue, #00c6ff);
    border-radius: 4px;
    }
/* Tabel Perkembangan Bulanan - sama seperti tabel indikator */
.perkembangan-table-container {
    padding: 0;
    overflow: auto;
    border-radius: 16px;
    border: 1px solid var(--pln-border, #e8e8e8);
    background: var(--pln-accent-bg, #ffffff);
}

.perkembangan-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.perkembangan-table thead th {
    background: linear-gradient(90deg, var(--pln-blue, #007bff), var(--pln-light-blue, #00c6ff));
    color: #fff;
    font-weight: 600;
    text-align: left;
    padding: 14px;
    font-size: 14px;
    position: sticky;
    top: 0;
    z-index: 5;
}

.perkembangan-table tbody tr {
    background-color: var(--pln-accent-bg, #ffffff);
    transition: all 0.2s ease;
}

.perkembangan-table tbody tr:hover {
    background-color: rgba(0, 156, 222, 0.05);
}

/* Rata tengah isi TABEL saja */
.perkembangan-table td {
    padding: 12px 14px;
    border-top: 1px solid var(--pln-border, #e8e8e8);
    font-size: 14px;
    vertical-align: middle;
    text-align: center; /* Ini yang bikin isi tabel di tengah */
}

</style>
@endsection

@section('content')
<div class="dashboard-content">
<!-- Filter -->
<div class="row mb-4">
    <!-- Filter Form -->
    <div class="col-md-8">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <form action="{{ route('dataKinerja.index') }}" method="get">
                    <div class="row g-3 align-items-end">
                        <!-- Tahun -->
                        <div class="col-md-4">
                            <label for="tahun" class="form-label">Tahun</label>
                            <select name="tahun" id="tahun" class="form-control">
                                @php
                                    $currentYear = date('Y');
                                    $startYear = $currentYear - 5;
                                @endphp
                                @for($year = $currentYear; $year >= $startYear; $year--)
                                    <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Bulan -->
                        <div class="col-md-4">
                            <label for="bulan" class="form-label">Bulan</label>
                            <select name="bulan" id="bulan" class="form-control">
                                @php
                                    $namaBulan = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ];
                                @endphp
                                @foreach($namaBulan as $value => $nama)
                                    <option value="{{ $value }}" {{ $bulan == $value ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>


                        <!-- Tombol -->
                        <div class="col-md-4 d-flex justify-content-start">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('dataKinerja.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-sync-alt me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Ringkasan Bulan -->
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex flex-column justify-content-center h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Data Bulan:
                        <strong>{{ date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun)) }}</strong>
                    </h6>
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
{{-- <div class="grid-span-12">
    <div class="card chart-card">
        <h3 class="chart-title">
            <i class="fas fa-chart-line"></i> Tren NKO {{ $tahun }}
        </h3>
        <div class="chart-container large position-relative">
            <!-- Elemen canvas Chart -->
            <canvas id="nkoTrendChart" height="300"></canvas>
        </div>
    </div>
</div> --}}


<!-- Section: Analisis Tren & Prediksi -->
<div class="section-divider">
    <h2><i class="fas fa-chart-line"></i> Tren & Prediksi</h2>
</div>

<div class="dashboard-grid">
    <!-- Tren Historis -->
    <div class="grid-span-6">
        <div class="card chart-card">
            <h3 class="chart-title"><i class="fas fa-history"></i> Tren Historis {{ $tahun }}</h3>
            <div class="chart-container medium position-relative">
                <canvas id="nkoTrendChart" height="280"></canvas>
            </div>
        </div>
    </div>

<!-- Forecast / Prediksi -->
<div class="grid-span-6">
    <div class="card chart-card h-100">
        <h3 class="chart-title">
            <i class="fas fa-chart-line text-warning"></i> Prediksi NKO (Forecast)
        </h3>
        <div class="chart-container medium position-relative" style="height: 280px;">
            <canvas id="forecastChart" height="280"></canvas>
        </div>
    </div>
</div>
</div>


<!-- Section: Analisis Per-Pilar -->
<div class="section-divider">
    <h2><i class="fas fa-layer-group"></i> Analisis Per-Pilar</h2>
</div>

<div class="section-wrapper">
    <div class="section-header">
    </div>

    <div class="card pilar-card">
        <h3 class="chart-title"><i class="fas fa-chart-pie"></i> Nilai Kinerja Per-Pilar</h3>

        @if($pilars->isEmpty())
            <div class="text-center text-muted py-4">
                <em>Belum ada data pilar untuk periode ini.</em>
            </div>
        @else
            @foreach($pilars->chunk(3) as $chunk)
                <div class="pillar-container">
                    @foreach($chunk as $index => $pilar)
                        @php
                            $globalIndex = $loop->parent->index * 3 + $index;
                        @endphp
                        <a href="{{ route('dataKinerja.pilar', $pilar->id) }}" class="pillar-item-link">
                            <div class="pillar-item">
                                <div class="pillar-title">{{ strtoupper($pilar->nama) }}</div>
                                <div class="pillar-value text-primary">{{ number_format($pilar->nilai, 1) }}%</div>
                                <div class="circle-progress">
                                    <canvas id="pilarChart{{ $globalIndex }}"></canvas>
                                    <div class="circle-progress-value">{{ number_format($pilar->nilai, 1) }}%</div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endforeach
        @endif
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
            <div class="perkembangan-table-container">
                <table class="perkembangan-table">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>NKO</th>
                            <th>Indikator Tercapai</th>
                            <th>Total Indikator</th>
                            <th>Persentase Tercapai (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($analisisData['perkembangan'] as $data)
                            <tr>
                                <td>{{ $data['bulan'] }}</td>
                                <td>{{ $data['nko'] }}</td>
                                <td>{{ $data['tercapai'] }}</td>
                                <td>{{ $data['total'] }}</td>
                                <td>{{ $data['persentase'] }}%</td>
                            </tr>
                        @endforeach
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
        const perkembanganPerBulan = @json($analisisData['perkembangan']);

        console.log({ trendNKOData, indikatorCompositionData, statusMappingData });

        initNkoTrendChart(trendNKOData);
        initIndikatorCompositionChart(indikatorCompositionData);
        initStatusMappingChart(statusMappingData);
        initHistoricalTrendChart(historicalTrendData);
        initForecastChart(forecastData);
        initPilarChart(pilarData);
        initBidangChart(bidangData);
        initPilarProgressCards(pilarProgressData);
        initPerkembanganChart(perkembanganPerBulan);
    });
    function initPilarProgressCards(data) {
        if (!Array.isArray(data)) return;

        data.forEach((pilar, index) => {
            const canvas = document.getElementById(`pilarChart${index}`);
            if (!canvas || typeof pilar.nilai !== 'number') return;

            new Chart(canvas, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [pilar.nilai, 100 - pilar.nilai],
                        backgroundColor: ['#e74a3b', '#e6e6e6'],
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '80%',
                    responsive: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false }
                    }
                }
            });
        });
    }

    // function initNkoTrendChart(data) {
    //     const ctx = document.getElementById('nkoTrendChart');
    //     if (!ctx || !data.length) return;

    //     const labels = data.map(item => item.bulan);
    //     const values = data.map(item => item.nko);

    //     new Chart(ctx, {
    //         type: 'line',
    //         data: {
    //             labels,
    //             datasets: [{
    //                 label: 'NKO',
    //                 data: values,
    //                 borderColor: '#4e73df',
    //                 backgroundColor: 'rgba(78,115,223,0.1)',
    //                 borderWidth: 2,
    //                 fill: true,
    //                 tension: 0.3
    //             }]
    //         },
    //         options: {
    //             responsive: true,
    //             scales: {
    //                 y: { beginAtZero: true, max: 100 },
    //                 x: { grid: { display: false } }
    //             }
    //         }
    //     });
    // }
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
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    }
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
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, max: 110 },
                    x: { grid: { display: false } }
                },
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: ctx => `Prediksi: ${ctx.parsed.y.toFixed(2)}%`
                        }
                    }
                }
            }
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
                maintainAspectRatio: false, // agar menyesuaikan container
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });
    }
function initPerkembanganChart(data) {
    const ctx = document.getElementById('perkembanganChart');
    if (!ctx || !data.length) return;

    const labels = data.map(p => p.bulan);
    const values = data.map(p => p.persentase);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: '% Tercapai',
                data: values,
                backgroundColor: '#1e90ff',
                borderRadius: 5,
                maxBarThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 110,
                    ticks: {
                        callback: (value) => `${value}%`
                    }
                },
                x: {
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { display: true }
            }
        }
    });
}



</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvasList = document.querySelectorAll('.circle-progress canvas');

    canvasList.forEach(canvas => {
        const ctx = canvas.getContext('2d');
        const valueText = canvas.parentElement.querySelector('.circle-progress-value')?.innerText || '0';
        const nilai = parseFloat(valueText.replace('%', '')) || 0;

        const width = canvas.width = 80;
        const height = canvas.height = 80;
        const radius = 35;
        const centerX = width / 2;
        const centerY = height / 2;
        const lineWidth = 6;
        const startAngle = -0.5 * Math.PI;
        const endAngle = startAngle + (2 * Math.PI * (nilai / 100));

        // Tentukan warna berdasarkan nilai
        let color = '#dc3545'; // Merah (default)
        if (nilai >= 80) {
            color = '#28a745'; // Hijau
        } else if (nilai >= 60) {
            color = '#ffc107'; // Kuning
        }

        // Background lingkaran abu-abu
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
        ctx.strokeStyle = '#e6ecf2';
        ctx.lineWidth = lineWidth;
        ctx.stroke();

        // Progress
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, startAngle, endAngle);
        ctx.strokeStyle = color;
        ctx.lineWidth = lineWidth;
        ctx.lineCap = 'round';
        ctx.stroke();
    });
});
</script>

@endsection
