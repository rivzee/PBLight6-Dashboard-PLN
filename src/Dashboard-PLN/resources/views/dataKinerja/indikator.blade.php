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
    <!-- Trend Chart -->
    <div class="dashboard-grid">
        <div class="grid-span-12">
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fas fa-chart-line mr-2"></i> Trend Bulanan Tahun {{ $tahun }}
                </div>
                <div class="chart-container">
                    <div class="loading-chart">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div id="trendChart"></div>
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
                                                    <div class="progress-bar {{ $persentase >= 100 ? 'bg-success' : ($persentase >= 95 ? 'bg-warning' : 'bg-danger') }}"
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
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartData = @json($chartData);
        const chartEl = document.querySelector('#trendChart');
        let chart = null;

        function renderChart() {
            if (chart) chart.destroy();

            if (chartData && chartData.length > 0 && typeof ApexCharts !== 'undefined' && chartEl) {
                const options = {
                    series: [{
                        name: 'Pencapaian',
                        data: chartData.map(item => item.nilai)
                    }],
                    chart: {
                        type: 'line',
                        height: chartEl.offsetWidth < 600 ? 280 : 400,
                        fontFamily: 'Poppins, sans-serif',
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        animations: { enabled: true }
                    },
                    responsive: [{
                        breakpoint: 600,
                        options: {
                            chart: { height: 260 },
                            xaxis: { labels: { rotate: -30, style: { fontSize: '10px' } } }
                        }
                    }],
                    colors: ['#4e73df'],
                    dataLabels: {
                        enabled: true,
                        formatter: val => val.toFixed(1) + '%',
                        style: {
                            fontSize: chartEl.offsetWidth < 600 ? '10px' : '12px',
                            fontWeight: '600'
                        }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    xaxis: {
                        categories: chartData.map(item => item.bulan),
                        labels: {
                            rotate: -45,
                            style: {
                                fontSize: chartEl.offsetWidth < 600 ? '10px' : '11px',
                                fontWeight: '500'
                            }
                        }
                    },
                    yaxis: {
                        min: 0,
                        max: 120,
                        tickAmount: 6,
                        labels: {
                            formatter: val => val.toFixed(0) + '%',
                            style: {
                                fontSize: '11px'
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: val => val.toFixed(2) + '%'
                        }
                    },
                    grid: {
                        borderColor: '#e0e0e0',
                        strokeDashArray: 4,
                        xaxis: { lines: { show: true } },
                        yaxis: { lines: { show: true } }
                    },
                    markers: {
                        size: 5,
                        colors: ['#fff'],
                        strokeColors: '#4e73df',
                        strokeWidth: 2,
                        hover: { size: 7 }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: 'vertical',
                            shadeIntensity: 0.3,
                            opacityFrom: 0.8,
                            opacityTo: 0.2,
                            stops: [0, 100]
                        }
                    }
                };

                chart = new ApexCharts(chartEl, options);
                chart.render();
                document.querySelector('.loading-chart')?.remove();
            }
        }

        renderChart();
        window.addEventListener('resize', renderChart);

        // Status badge
        document.querySelectorAll('tbody tr').forEach(function (row) {
            const percentCell = row.querySelector('.nilai-persentase');
            const statusCell = row.querySelector('.status-badge');
            if (percentCell && statusCell) {
                const val = parseFloat(percentCell.dataset.persentase || percentCell.textContent.replace('%', '')) || 0;
                let label = '';
                let className = '';
                if (val >= 100) {
                    label = 'Tercapai';
                    className = 'badge badge-success';
                } else if (val >= 95) {
                    label = 'Perlu Perhatian';
                    className = 'badge badge-warning';
                } else {
                    label = 'Tidak Tercapai';
                    className = 'badge badge-danger';
                }
                statusCell.innerHTML = `<span class="${className}">${label}</span>`;
            }
        });
    });
</script>

@endsection
