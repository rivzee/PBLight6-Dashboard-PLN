@extends('layouts.app')

@section('title', 'Detail Indikator')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/dataKinerja.css') }}">
@endsection

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="breadcrumb-custom">
                <a href="{{ route('dataKinerja.pilar', $indikator->pilar_id) }}">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Perspektif
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
                        <div class="info-card-label">Perspektif</div>
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