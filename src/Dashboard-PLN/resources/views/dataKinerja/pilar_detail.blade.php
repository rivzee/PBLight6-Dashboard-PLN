@extends('layouts.app')

@section('title', 'Detail Pilar - ' . $pilar->nama)
@section('styles')
<link rel="stylesheet" href="{{ asset('css/dataKinerja.css') }}">
@endsection


@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="breadcrumb-custom">
                <a href="{{ route('dataKinerja.pilar') }}" >
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
            <h1 class="page-title">Detail Perspektif {{ $pilar->nama }}</h1>
            <p class="page-subtitle">
                <i class="fas fa-landmark mr-2"></i>{{ $pilar->nama ?? '-' }}
            </p>
        </div>
    </div>

    <!-- Filter Panel -->
    <div class="filter-panel">
        <div class="filter-title">
            <i class="fas fa-filter"></i> Filter Data
        </div>
        <form action="{{ route('dataKinerja.pilar', $pilar->id) }}" method="GET" class="filter-form" id="filterForm">
            <div class="filter-group">
                <label class="filter-label">Tahun</label>
                <select name="tahun" class="filter-select" id="tahunSelect">
                    @foreach(range(date('Y') + 1, date('Y') - 5) as $year)
                        <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>
                            {{ $year }}{{ $year == date('Y') ? ' (Tahun Ini)' : '' }}
                        </option>
                    @endforeach
                </select>
                <label class="filter-label ml-3">Bulan</label>
                <select name="bulan" class="filter-select" id="bulanSelect">
                    @foreach(range(1, 12) as $month)
                        <option value="{{ $month }}" {{ $bulan == $month ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $month, 1)) }}
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


    @include('components.alert')

    <div class="pilar-header-card pilar-{{ strtolower($pilar->kode) }}">
        <div class="pilar-icon-large">
            <i class="fas fa-chart-pie"></i>
        </div>
        <div class="pilar-info">
            <h1 class="pilar-name">{{ $pilar->nama }}</h1>
            <div class="pilar-code">Perspektif {{ $pilar->kode }}</div>
            <p class="pilar-description">{{ $pilar->deskripsi ?? 'Pilar Strategis PLN' }}</p>
            <div class="pilar-stats">
                <div class="pilar-stat-item">
                    <div class="pilar-value-large">{{ number_format($pilar->nilai ?? 0, 2) }}%</div>
                    <div class="pilar-stat-label">Nilai Kinerja</div>
                </div>
                <div class="pilar-stat-item">
                    <div class="pilar-stat-value">{{ $pilar->indikators_count ?? 0 }}</div>
                    <div class="pilar-stat-label">Total Indikator</div>
                </div>
                <div class="pilar-stat-item">
                    <div class="pilar-stat-value">{{ $pilar->indikators_tercapai ?? 0 }}</div>
                    <div class="pilar-stat-label">Indikator Tercapai</div>
                </div>
                <div class="pilar-stat-item">
                    <div class="pilar-stat-value">{{ ($pilar->indikators_count ?? 0) - ($pilar->indikators_tercapai ?? 0) }}</div>
                    <div class="pilar-stat-label">Belum Tercapai</div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="grid-span-6">
            <div class="chart-card">
                <h3 class="chart-title"><i class="fas fa-chart-bar"></i> Perbandingan Indikator</h3>
                <div id="indikatorComparisonChart" class="chart-container">
                    <div class="loading-chart">
                        <i class="fas fa-circle-notch fa-spin fa-3x"></i>
                        <span>Memuat data...</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid-span-6">
            <div class="chart-card">
                <h3 class="chart-title"><i class="fas fa-chart-line"></i> Trend Bulanan Perspektif {{ $tahun }}</h3>
                <div id="trendBulananChart" class="chart-container">
                    <div class="loading-chart">
                        <i class="fas fa-circle-notch fa-spin fa-3x"></i>
                        <span>Memuat data...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid-span-12">
        <h3 class="mb-4"><i class="fas fa-list-alt mr-2"></i> Daftar Indikator</h3>
    </div>

    <div class="row">
        @foreach($indikators as $indikator)
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm indikator-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0 font-weight-bold">{{ $indikator->nama }}</h5>
                        <span class="badge badge-light px-2 py-1">{{ $indikator->kode }}</span>
                    </div>
                    <div class="text-muted mb-2">
                        <i class="fas fa-building mr-1"></i> {{ $indikator->bidang->nama ?? '-' }}
                    </div>
@php
    $persen = $indikator->persentase ?? 0;
    $warna = $persen >= 100 ? '#1cc88a' : ($persen >= 95 ? '#f6c23e' : '#e74a3b');
@endphp

<div class="progress mb-3" style="height: 8px;">
    <div class="progress-bar"
        role="progressbar"
        style="width: {{ $persen }}%; background-color: {{ $warna }};"
        aria-valuenow="{{ $persen }}"
        aria-valuemin="0"
        aria-valuemax="110">
    </div>
</div>

                    <div class="d-flex justify-content-between text-center mb-3">
                        <div>
                            <div class="small text-muted">Target Bulanan</div>
                            <div class="font-weight-bold">{{ number_format($indikator->target_bulanan ?? 0, 2) }}</div>
                        </div>
                        <div>
                            <div class="small text-muted">Realisasi</div>
                            <div class="font-weight-bold">{{ number_format($indikator->nilai_aktual ?? 0, 2) }}</div>
                        </div>
                        <div>
                            <div class="small text-muted">Pencapaian</div>
                            <div class="font-weight-bold">{{ number_format($indikator->persentase ?? 0, 2) }}%</div>
                        </div>
                    </div>
                    <a href="{{ route('dataKinerja.indikator', $indikator->id) }}" class="btn btn-block btn-primary">
                        <i class="fas fa-search-plus fa-sm"></i> Detail
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const indikatorData = @json($indikatorChartData);
        const trendData = @json($trendBulanan);

        // Tentukan warna berdasarkan nilai pencapaian
        const getColor = (val) => {
            if (val >= 100) return '#1cc88a';       // hijau
            if (val >= 95) return '#f6c23e';       // kuning
            return '#e74a3b';                      // merah
        };

        // Chart Perbandingan Indikator
        if (indikatorData && indikatorData.length > 0 && typeof ApexCharts !== 'undefined') {
            const indikatorOptions = {
                series: [{
                    name: 'Pencapaian',
                    data: indikatorData.map(item => item.persentase)
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: { show: false },
                    fontFamily: 'Poppins, sans-serif'
                },
                colors: indikatorData.map(item => getColor(item.persentase)),
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 6
                    },
                },
                dataLabels: {
                    enabled: true,
                    formatter: val => val.toFixed(1) + "%",
                    offsetY: -20,
                    style: { fontSize: '12px', colors: ["#304758"] }
                },
                xaxis: {
                    categories: indikatorData.map(item => item.kode),
                    labels: { style: { fontSize: '12px' } }
                },
                yaxis: {
                    max: 100,
                    labels: {
                        formatter: val => val.toFixed(0) + '%'
                    }
                },
                tooltip: {
                    y: { formatter: val => val.toFixed(2) + '%' }
                },
                grid: {
                    borderColor: '#e0e0e0',
                    strokeDashArray: 4
                }
            };

            const indikatorChart = new ApexCharts(
                document.querySelector('#indikatorComparisonChart'),
                indikatorOptions
            );
            indikatorChart.render();
            document.querySelector('#indikatorComparisonChart .loading-chart')?.remove();
        }

        // Chart Trend Bulanan
        if (trendData && trendData.length > 0 && typeof ApexCharts !== 'undefined') {
            const trendOptions = {
                series: [{
                    name: 'rata-rata persentase capaian',
                    data: trendData.map(item => item.nilai)
                }],
                chart: {
                    type: 'line',
                    height: 300,
                    toolbar: { show: false },
                    fontFamily: 'Poppins, sans-serif'
                },
                colors: ['#007bff'],
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: trendData.map(item => item.bulan),
                    labels: {
                        rotate: -45,
                        style: { fontSize: '10px' }
                    }
                },
                yaxis: {
                    max: 100,
                    labels: {
                        formatter: val => val.toFixed(0) + '%'
                    }
                },
                tooltip: {
                    y: { formatter: val => val.toFixed(2) + '%' }
                },
                markers: {
                    size: 5,
                    colors: ['#fff'],
                    strokeColors: '#007bff',
                    strokeWidth: 2
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: 'vertical',
                        shadeIntensity: 0.3,
                        opacityFrom: 0.7,
                        opacityTo: 0.2,
                        stops: [0, 100]
                    }
                },
                grid: {
                    borderColor: '#e0e0e0',
                    strokeDashArray: 4,
                    xaxis: { lines: { show: true } }
                }
            };

            const trendChart = new ApexCharts(
                document.querySelector('#trendBulananChart'),
                trendOptions
            );
            trendChart.render();
            document.querySelector('#trendBulananChart .loading-chart')?.remove();
        }
    });
</script>
@endsection
