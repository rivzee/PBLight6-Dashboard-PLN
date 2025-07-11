@extends('layouts.app')

@section('title', 'Data Kinerja - Analisis Bidang')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/dataKinerja.css') }}">
@endsection

@section('content')
<div class="container-fluid dashboard-content">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Analisis Kinerja Berdasarkan Bidang</h1>
        <div class="d-flex">
            <form action="{{ route('dataKinerja.bidang') }}" method="GET" class="d-flex align-items-center">
                <select name="tahun" class="form-control form-control-sm mr-2">
                    @foreach(range(date('Y') - 5, date('Y') + 1) as $year)
                        <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
                <select name="bulan" class="form-control form-control-sm mr-2">
                    @foreach(range(1, 12) as $month)
                        <option value="{{ $month }}" {{ $bulan == $month ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-filter fa-sm"></i> Filter
                </button>
            </form>
        </div>
    </div>

    @include('components.alert')

    <!-- Ringkasan Bidang Chart -->
    <div class="chart-card">
        <h3 class="chart-title"><i class="fas fa-chart-bar"></i> Perbandingan Nilai Bidang</h3>
        <div id="bidangComparisonChart" class="chart-container">
            <div class="loading-chart">
                <i class="fas fa-circle-notch fa-spin fa-3x mb-3"></i>
                <span>Memuat data...</span>
            </div>
        </div>
    </div>

    <!-- Bidang Cards -->
    <div class="dashboard-grid">
        @foreach($bidangs as $bidang)
        <div class="bidang-card">
            <div class="bidang-header">
                <div>
                    <h3 class="bidang-title">{{ $bidang->nama }}</h3>
                    <span class="bidang-code">{{ $bidang->kode }}</span>
                </div>
                <div class="bidang-icon">
                    <i class="fas fa-building"></i>
                </div>
            </div>
            <div class="bidang-value">{{ number_format($bidang->nilai, 2) }}%</div>
            <p class="bidang-description">{{ $bidang->deskripsi ?? 'Bidang Fungsional PLN' }}</p>
            <div class="bidang-progress">
                <div class="bidang-progress-bar" style="width: {{ $bidang->nilai }}%"></div>
            </div>
            <div class="bidang-indicator-count">
                <div class="indicator-stat">
                    <div class="indicator-stat-label">Indikator</div>
                    <div class="indicator-stat-value">{{ $bidang->indikators_count }}</div>
                </div>
                <div class="indicator-stat">
                    <div class="indicator-stat-label">Status</div>
                    <div class="indicator-stat-value">
                        @if($bidang->nilai >= 90)
                            <span class="badge bg-success text-white">Baik</span>
                        @elseif($bidang->nilai >= 70)
                            <span class="badge bg-warning text-dark">Cukup</span>
                        @else
                            <span class="badge bg-danger text-white">Kurang</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-3 text-center">
                <a href="{{ route('dataKinerja.bidang.detail', $bidang->id) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-search-plus fa-sm"></i> Detail
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Tabel Bidang -->
    <div class="chart-card">
        <h3 class="chart-title"><i class="fas fa-list"></i> Daftar Kinerja Bidang</h3>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Bidang</th>
                        <th>PIC</th>
                        <th>Jumlah Indikator</th>
                        <th>Nilai Kinerja</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bidangs as $bidang)
                    <tr>
                        <td><strong>{{ $bidang->kode }}</strong></td>
                        <td>{{ $bidang->nama }}</td>
                        <td>{{ $bidang->pic_name ?? '-' }}</td>
                        <td>{{ $bidang->indikators_count }}</td>
                        <td>{{ number_format($bidang->nilai, 2) }}%</td>
                        <td>
                            @if($bidang->nilai >= 90)
                                <span class="badge bg-success text-white">Baik</span>
                            @elseif($bidang->nilai >= 70)
                                <span class="badge bg-warning text-dark">Cukup</span>
                            @else
                                <span class="badge bg-danger text-white">Kurang</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('dataKinerja.bidang.detail', $bidang->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-search-plus fa-sm"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data untuk chart
        const bidangData = @json($bidangChartData);

        // Chart Perbandingan Bidang
        if (bidangData && bidangData.length > 0 && typeof ApexCharts !== 'undefined') {
            const bidangOptions = {
                series: [{
                    name: 'Nilai',
                    data: bidangData.map(item => item.nilai)
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    fontFamily: 'Poppins, sans-serif',
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#36b9cc'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 6,
                        dataLabels: {
                            position: 'top'
                        }
                    },
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return val.toFixed(1) + "%";
                    },
                    offsetY: -20,
                    style: {
                        fontSize: '12px',
                        colors: ["#304758"]
                    }
                },
                xaxis: {
                    categories: bidangData.map(item => item.nama),
                    position: 'bottom',
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        show: true,
                        rotate: -45,
                        rotateAlways: false,
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    min: 0,
                    max: 100,
                    labels: {
                        formatter: function(val) {
                            return val.toFixed(0) + '%';
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val.toFixed(2) + '%';
                        }
                    }
                },
                grid: {
                    borderColor: '#e0e0e0',
                    strokeDashArray: 4
                }
            };

            const bidangChart = new ApexCharts(document.querySelector('#bidangComparisonChart'), bidangOptions);
            bidangChart.render();
            document.querySelector('#bidangComparisonChart .loading-chart').style.display = 'none';
        }
    });
</script>
@endsection
