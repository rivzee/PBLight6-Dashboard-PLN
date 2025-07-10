@extends('layouts.app')

@section('title', 'Detail Indikator')

@section('styles')
<style>
    .dashboard-content {
        max-width: 1800px;
        margin: 0 auto;
        padding: 0 15px;
    }

    /* Dashboard Layout */
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
    }

    @media (max-width: 768px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
            grid-gap: 15px;
        }
        .grid-span-3, .grid-span-4, .grid-span-6 {
            grid-column: span 1;
        }
    }

    /* Header Styling */
    .page-header {
        background: var(--pln-header-bg, linear-gradient(90deg, var(--pln-blue, #1e40af), var(--pln-light-blue, #3b82f6)));
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="1" fill="white" opacity="0.1"/><circle cx="10" cy="90" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        animation: float 20s infinite linear;
        pointer-events: none;
    }

    @keyframes float {
        0% { transform: translate(0, 0) rotate(0deg); }
        100% { transform: translate(-50px, -50px) rotate(360deg); }
    }

    .page-header-content {
        position: relative;
        z-index: 2;
    }

    .breadcrumb-custom {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 25px;
        padding: 8px 20px;
        margin-bottom: 20px;
        backdrop-filter: blur(10px);
        display: inline-flex;
        align-items: center;
    }

    .breadcrumb-custom a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .breadcrumb-custom a:hover {
        color: white;
        transform: translateX(-2px);
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .page-subtitle {
        font-size: 16px;
        opacity: 0.9;
        margin: 10px 0 0;
    }

    /* Filter Panel */
    .filter-panel {
        background: white;
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        border: 1px solid #e8e8e8;
        position: relative;
        overflow: hidden;
    }

    .filter-panel::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #4e73df, #36b9cc);
    }

    .filter-title {
        font-size: 18px;
        font-weight: 600;
        color: #4e73df;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }

    .filter-title i {
        margin-right: 10px;
        background: linear-gradient(45deg, #4e73df, #36b9cc);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .filter-form {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        min-width: 150px;
    }

    .filter-label {
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-select {
        border: 2px solid #e8e8e8;
        border-radius: 12px;
        padding: 12px 15px;
        font-size: 14px;
        font-weight: 500;
        color: #333;
        background: white;
        transition: all 0.3s ease;
        appearance: none;
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%23333" stroke-width="2"><polyline points="6,9 12,15 18,9"></polyline></svg>');
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px;
        padding-right: 40px;
    }

    .filter-select:focus {
        outline: none;
        border-color: #4e73df;
        box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
        transform: translateY(-1px);
    }

    .filter-actions {
        display: flex;
        gap: 10px;
        margin-left: auto;
    }

    .btn-filter {
        background: linear-gradient(45deg, #4e73df, #36b9cc);
        border: none;
        border-radius: 12px;
        padding: 12px 20px;
        color: white;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
    }

    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(78, 115, 223, 0.4);
        color: white;
    }

    .btn-reset {
        background: #f8f9fc;
        border: 2px solid #e8e8e8;
        border-radius: 12px;
        padding: 12px 20px;
        color: #6c757d;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-reset:hover {
        background: #e74a3b;
        border-color: #e74a3b;
        color: white;
        transform: translateY(-1px);
    }

    /* Card Styling */
    .info-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 25px;
        position: relative;
        overflow: hidden;
        border: 1px solid #e8e8e8;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        height: 100%;
        transition: all 0.4s ease;
    }

    .info-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .info-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 6px;
        border-radius: 20px 20px 0 0;
    }

    .info-card.primary::before {
        background: linear-gradient(90deg, #4e73df, #224abe);
    }

    .info-card.success::before {
        background: linear-gradient(90deg, #1cc88a, #17a673);
    }

    .info-card.info::before {
        background: linear-gradient(90deg, #36b9cc, #2c9faf);
    }

    .info-card.warning::before {
        background: linear-gradient(90deg, #f6c23e, #dda20a);
    }

    .info-card.danger::before {
        background: linear-gradient(90deg, #e74a3b, #c0392b);
    }

    .info-card-body {
        display: flex;
        align-items: center;
    }

    .info-card-icon {
        width: 70px;
        height: 70px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 28px;
        margin-right: 20px;
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
    }

    .info-card-icon::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: rgba(255, 255, 255, 0.1);
        transform: rotate(45deg);
        transition: all 0.3s ease;
    }

    .info-card:hover .info-card-icon::before {
        transform: rotate(45deg) translate(100%, 100%);
    }

    .info-card.primary .info-card-icon {
        background: linear-gradient(135deg, #4e73df, #224abe);
    }

    .info-card.success .info-card-icon {
        background: linear-gradient(135deg, #1cc88a, #17a673);
    }

    .info-card.info .info-card-icon {
        background: linear-gradient(135deg, #36b9cc, #2c9faf);
    }

    .info-card.warning .info-card-icon {
        background: linear-gradient(135deg, #f6c23e, #dda20a);
    }

    .info-card.danger .info-card-icon {
        background: linear-gradient(135deg, #e74a3b, #c0392b);
    }

    .info-card-content {
        flex: 1;
    }

    .info-card-label {
        font-size: 13px;
        font-weight: 700;
        color: #6c757d;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .info-card-value {
        font-size: 24px;
        font-weight: 800;
        color: #333;
        margin: 0;
        line-height: 1.2;
    }

    /* Chart Card */
    .chart-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 30px;
        transition: all 0.4s ease;
        border: 1px solid #e8e8e8;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        position: relative;
        overflow: hidden;
        margin-bottom: 30px;
        height: 100%;
    }

    .chart-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 6px;
        background: linear-gradient(90deg, #4e73df, #36b9cc);
        border-radius: 20px 20px 0 0;
    }

    .chart-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .chart-title {
        font-size: 20px;
        color: #4e73df;
        margin-bottom: 25px;
        font-weight: 700;
        display: flex;
        align-items: center;
    }

    .chart-title i {
        margin-right: 12px;
        background: linear-gradient(45deg, #4e73df, #36b9cc);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .chart-container {
        position: relative;
        height: 350px;
        width: 100%;
    }

    /* Table Styling */
    .table {
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
        font-size: 14px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .table thead th {
        background: linear-gradient(135deg, #f8f9fc, #e9ecef);
        color: #4e73df;
        font-weight: 700;
        text-transform: uppercase;
        padding: 18px 15px;
        border-bottom: 3px solid #4e73df;
        font-size: 12px;
        letter-spacing: 1px;
    }

    .table tbody td {
        padding: 18px 15px;
        vertical-align: middle;
        border-top: 1px solid #e3e6f0;
        transition: all 0.3s ease;
    }

    .table tbody tr {
        transition: all 0.3s ease;
    }

    .table tbody tr:hover {
        background: linear-gradient(135deg, #f8f9fc, #e9ecef);
        transform: scale(1.01);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .badge {
        font-size: 12px;
        padding: 8px 15px;
        border-radius: 25px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        overflow: hidden;
    }

    .badge::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s ease;
    }

    .badge:hover::before {
        left: 100%;
    }

    .badge-success {
        background: linear-gradient(135deg, #1cc88a, #17a673);
        color: #fff;
        box-shadow: 0 4px 15px rgba(28, 200, 138, 0.3);
    }

    .badge-warning {
        background: linear-gradient(135deg, #f6c23e, #dda20a);
        color: #212529;
        box-shadow: 0 4px 15px rgba(246, 194, 62, 0.3);
    }

    .badge-danger {
        background: linear-gradient(135deg, #e74a3b, #c0392b);
        color: #fff;
        box-shadow: 0 4px 15px rgba(231, 74, 59, 0.3);
    }

    /* Loading indicator */
    .loading-chart {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        width: 100%;
        color: #4e73df;
    }

    .spinner-border {
        width: 3rem;
        height: 3rem;
        border-width: 0.3em;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            padding: 20px;
            text-align: center;
        }

        .page-title {
            font-size: 22px;
        }

        .filter-form {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-actions {
            margin-left: 0;
            justify-content: center;
        }

        .info-card-body {
            flex-direction: column;
            text-align: center;
        }

        .info-card-icon {
            margin-right: 0;
            margin-bottom: 15px;
        }
    }

    /* Animation */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dashboard-grid > * {
        animation: slideInUp 0.6s ease forwards;
    }

    .dashboard-grid > *:nth-child(2) { animation-delay: 0.1s; }
    .dashboard-grid > *:nth-child(3) { animation-delay: 0.2s; }
    .dashboard-grid > *:nth-child(4) { animation-delay: 0.3s; }
</style>
@endsection

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="breadcrumb-custom">
                <a href="{{ route('dataKinerja.pilar', $indikator->pilar_id) }}">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Pilar
                </a>
            </div>
            <h1 class="page-title">{{ $indikator->nama }}</h1>
            <p class="page-subtitle">
                <i class="fas fa-tag mr-2"></i>{{ $indikator->kode }} •
                <i class="fas fa-landmark mr-2"></i>{{ $indikator->pilar->nama ?? '-' }} •
                <i class="fas fa-building mr-2"></i>{{ $indikator->bidang->nama ?? '-' }}
            </p>
        </div>
    </div>

    <!-- Filter Panel -->
    <div class="filter-panel">
        <div class="filter-title">
            <i class="fas fa-filter"></i> Filter Data
        </div>
        <form action="{{ route('dataKinerja.indikator', $indikator->id) }}" method="GET" class="filter-form" id="filterForm">
            <div class="filter-group">
                <label class="filter-label">Tahun</label>
                <select name="tahun" class="filter-select" id="tahunSelect">
                    @foreach(range(date('Y') + 1, date('Y') - 5) as $year)
                        <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>
                            {{ $year }}{{ $year == date('Y') ? ' (Tahun Ini)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <i class="fas fa-search"></i> Terapkan Filter
                </button>
                <button type="button" class="btn-reset" onclick="resetFilter()">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>
        </form>
    </div>

    <!-- Info Cards -->
    <div class="dashboard-grid">
        <div class="grid-span-3">
            <div class="info-card primary">
                <div class="info-card-body">
                    <div class="info-card-icon">
                        <i class="fas fa-landmark"></i>
                    </div>
                    <div class="info-card-content">
                        <div class="info-card-label">Pilar</div>
                        <div class="info-card-value">{{ $indikator->pilar->nama ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-span-3">
            <div class="info-card success">
                <div class="info-card-body">
                    <div class="info-card-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="info-card-content">
                        <div class="info-card-label">Bidang</div>
                        <div class="info-card-value">{{ $indikator->bidang->nama ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-span-3">
            <div class="info-card info">
                <div class="info-card-body">
                    <div class="info-card-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <div class="info-card-content">
                        <div class="info-card-label">Target Tahunan</div>
                        <div class="info-card-value">{{ number_format($indikator->target ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-span-3">
            <div class="{{ $statusClass }}">
                <div class="info-card-body">
                    <div class="info-card-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="info-card-content">
                        <div class="info-card-label">
                            Status ({{ $realisasiTerakhir ? \Carbon\Carbon::create(null, $realisasiTerakhir->bulan, 1)->locale('id')->monthName : '-' }})
                        </div>
                        <div class="info-card-value">{{ $statusText }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deskripsi Indikator -->
    {{-- <div class="dashboard-grid">
        <div class="grid-span-12">
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fas fa-info-circle mr-2"></i> Deskripsi Indikator
                </div>
                <p>{{ $indikator->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Formula Pengukuran:</h6>
                        <p>{{ $indikator->formula ?? 'Tidak ada formula' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Sumber Data:</h6>
                        <p>{{ $indikator->sumber_data ?? 'Tidak ada sumber data' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Trend Chart -->
    <div class="dashboard-grid">
        <div class="grid-span-12">
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fas fa-chart-line"></i> Trend Pencapaian Bulanan {{ $tahun }}
                </div>
                <div class="chart-container">
                    <div class="loading-chart" id="chartLoading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Memuat data trend...</p>
                    </div>
                    <div id="trendChart" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Realisasi Table -->
    <div class="dashboard-grid">
        <div class="grid-span-12">
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fas fa-table"></i> Data Realisasi Bulanan {{ $tahun }}
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-calendar mr-2"></i>Bulan</th>
                                <th><i class="fas fa-bullseye mr-2"></i>Target Bulanan</th>
                                <th><i class="fas fa-chart-bar mr-2"></i>Nilai Realisasi</th>
                                <th><i class="fas fa-percentage mr-2"></i>Pencapaian</th>
                                <th><i class="fas fa-flag mr-2"></i>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($realisasi as $data)
                                @php
                                    $targetBulan = $data->target_bulanan ?? 0;
                                    $nilai = $data->nilai ?? 0;
                                    $persentase = $data->persentase ?? 0;

                                    if ($persentase >= 90) {
                                        $statusText = 'Tercapai';
                                        $statusClass = 'badge badge-success';
                                        $statusIcon = 'fas fa-check-circle';
                                    } elseif ($persentase >= 70) {
                                        $statusText = 'Perlu Perhatian';
                                        $statusClass = 'badge badge-warning';
                                        $statusIcon = 'fas fa-exclamation-triangle';
                                    } else {
                                        $statusText = 'Tidak Tercapai';
                                        $statusClass = 'badge badge-danger';
                                        $statusIcon = 'fas fa-times-circle';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ Carbon\Carbon::create(null, $data->bulan, 1)->locale('id')->monthName }}</strong>
                                        <small class="d-block text-muted">{{ Carbon\Carbon::create(null, $data->bulan, 1)->format('M Y') }}</small>
                                    </td>
                                    <td>
                                        <span class="font-weight-bold">{{ number_format($targetBulan, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="font-weight-bold">{{ number_format($nilai, 2) }}</span>
                                        @if($nilai > 0)
                                            <small class="d-block text-success">
                                                <i class="fas fa-arrow-up"></i> Ada Data
                                            </small>
                                        @else
                                            <small class="d-block text-muted">
                                                <i class="fas fa-minus"></i> Belum Ada Data
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="font-weight-bold mr-2">{{ number_format($persentase, 2) }}%</span>
                                            @if($persentase > 0)
                                                <div class="progress" style="width: 50px; height: 8px;">
                                                    <div class="progress-bar {{ $persentase >= 90 ? 'bg-success' : ($persentase >= 70 ? 'bg-warning' : 'bg-danger') }}"
                                                         style="width: {{ min($persentase, 100) }}%"></div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="{{ $statusClass }}">
                                            <i class="{{ $statusIcon }} mr-1"></i>{{ $statusText }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                                        <p class="mb-0">Tidak ada data realisasi untuk tahun {{ $tahun }}</p>
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

<script>
function resetFilter() {
    const currentYear = {{ date('Y') }};
    const tahunSelect = document.getElementById('tahunSelect');
    tahunSelect.value = currentYear;
    document.getElementById('filterForm').submit();
}

// Auto-submit form on select change untuk UX yang lebih baik
document.getElementById('tahunSelect').addEventListener('change', function() {
    const loadingText = document.createElement('small');
    loadingText.className = 'text-muted ml-2';
    loadingText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat...';
    this.parentNode.appendChild(loadingText);

    setTimeout(() => {
        document.getElementById('filterForm').submit();
    }, 300);
});
</script>
@endsection



@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM loaded, initializing chart...');

    // Render Chart
    const chartData = @json($chartData);
    const loadingElement = document.getElementById('chartLoading');
    const chartElement = document.getElementById('trendChart');

    console.log('Chart Data:', chartData);
    console.log('ApexCharts loaded:', typeof ApexCharts !== 'undefined');

    // Short delay to ensure everything is loaded
    setTimeout(() => {
        if (chartData && chartData.length > 0 && typeof ApexCharts !== 'undefined') {
            try {
                const options = {
                    series: [{
                        name: 'Pencapaian (%)',
                        data: chartData.map(item => parseFloat(item.nilai) || 0)
                    }],
                    chart: {
                        type: 'line',
                        height: 350,
                        fontFamily: 'Poppins, sans-serif',
                        toolbar: { show: true }
                    },
                    colors: ['var(--pln-blue, #0a4d85)'],
                    dataLabels: {
                        enabled: true,
                        formatter: val => val.toFixed(1) + '%'
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 4
                    },
                    xaxis: {
                        categories: chartData.map(item => item.bulan)
                    },
                    yaxis: {
                        min: 0,
                        max: 110,
                        labels: {
                            formatter: val => val.toFixed(0) + '%'
                        },
                        title: {
                            text: 'Persentase Pencapaian (%)'
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: val => val.toFixed(2) + '%'
                        }
                    },
                    grid: {
                        borderColor: '#e8e8e8',
                        strokeDashArray: 3
                    },
                    markers: {
                        size: 6,
                        colors: ['#fff'],
                        strokeColors: 'var(--pln-blue, #0a4d85)',
                        strokeWidth: 3
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: 'vertical',
                            gradientToColors: ['var(--pln-light-blue, #009cde)'],
                            opacityFrom: 0.8,
                            opacityTo: 0.1
                        }
                    },
                    annotations: {
                        yaxis: [
                            {
                                y: 70,
                                borderColor: '#f6c23e',
                                borderWidth: 2,
                                strokeDashArray: 5,
                                label: {
                                    text: 'Target Minimum (70%)',
                                    position: 'right'
                                }
                            },
                            {
                                y: 90,
                                borderColor: '#1cc88a',
                                borderWidth: 2,
                                strokeDashArray: 5,
                                label: {
                                    text: 'Target Optimal (90%)',
                                    position: 'right'
                                }
                            }
                        ]
                    }
                };

                const chart = new ApexCharts(chartElement, options);
                chart.render().then(() => {
                    console.log('Chart rendered successfully');
                    if (loadingElement) loadingElement.style.display = 'none';
                    if (chartElement) chartElement.style.display = 'block';
                });

            } catch (error) {
                console.error('Error creating chart:', error);
                if (loadingElement) {
                    loadingElement.innerHTML = `
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3 text-warning"></i>
                            <h5>Error Memuat Chart</h5>
                            <p>Gagal membuat chart: ${error.message}</p>
                        </div>
                    `;
                }
            }
        } else {
            // Show no data message
            if (loadingElement) {
                loadingElement.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-chart-line fa-3x mb-3"></i>
                        <h5>Tidak Ada Data Chart</h5>
                        <p>Data trend untuk tahun {{ $tahun }} tidak tersedia</p>
                        <small class="text-muted">ApexCharts: ${typeof ApexCharts !== 'undefined' ? 'Loaded' : 'Not loaded'}</small>
                    </div>
                `;
            }
            console.log('No chart data or ApexCharts not loaded');
        }
    }, 500);
});
</script>
@endsection



