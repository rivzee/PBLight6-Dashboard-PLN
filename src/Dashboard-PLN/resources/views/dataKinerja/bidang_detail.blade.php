@extends('layouts.app')

@section('title', 'Detail Bidang')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/dataKinerja.css') }}">
@endsection

@section('content')
<div class="dashboard-content">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <a href="{{ route('dataKinerja.bidang') }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <h1 class="h3 mb-0 text-gray-800">Detail Bidang: {{ $bidang->nama }}</h1>
        </div>
        <div class="d-flex">
            <form action="{{ route('dataKinerja.bidang', $bidang->id) }}" method="GET" class="d-flex align-items-center">
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

    <!-- Info Cards -->
    <div class="dashboard-grid">
        <div class="grid-span-4">
            <div class="info-card primary">
                <div class="info-card-body">
                    <div class="info-card-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div class="info-card-content">
                        <div class="info-card-label">Kode Bidang</div>
                        <div class="info-card-value">{{ $bidang->kode }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-span-4">
            <div class="info-card success">
                <div class="info-card-body">
                    <div class="info-card-icon">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="info-card-content">
                        <div class="info-card-label">Jumlah Indikator</div>
                        <div class="info-card-value">{{ $bidang->indikators->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-span-4">
            <div class="info-card info">
                <div class="info-card-body">
                    <div class="info-card-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="info-card-content">
                        <div class="info-card-label">Nilai Rata-rata</div>
                        <div class="info-card-value">{{ number_format($bidang->getNilaiRata($tahun, $bulan), 2) }}%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Card -->
    <div class="dashboard-grid">
        <div class="grid-span-12">
            <div class="chart-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="chart-title mb-0">
                        <i class="fas fa-chart-line mr-2"></i> Trend Bulanan Tahun {{ $tahun }}
                    </div>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Pilih Tahun:</div>
                            @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                <a class="dropdown-item" href="{{ route('dataKinerja.bidang', ['id' => $bidang->id, 'tahun' => $i]) }}">{{ $i }}</a>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="chart-container">
                    <div class="loading-chart">
                        <div class="spinner-border text-success" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div id="trendChart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Indikator Table -->
    <div class="dashboard-grid">
        <div class="grid-span-12">
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fas fa-table mr-2"></i> Daftar Indikator
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Indikator</th>
                                <th>Pilar</th>
                                <th>Target</th>
                                <th>Realisasi</th>
                                <th>Pencapaian</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bidang->indikators as $indikator)
                                <tr>
                                    <td>{{ $indikator->kode }}</td>
                                    <td>{{ $indikator->nama }}</td>
                                    <td>{{ $indikator->pilar->nama }}</td>
                                    <td>{{ number_format($indikator->target, 2) }} {{ $indikator->satuan }}</td>
                                    <td>{{ number_format($indikator->nilai ?? 0, 2) }} {{ $indikator->satuan }}</td>
                                    <td>{{ number_format($indikator->nilai ?? 0, 2) }}%</td>
                                    <td>
                                        @if (($indikator->nilai ?? 0) >= 90)
                                            <span class="badge badge-success">Tercapai</span>
                                        @elseif (($indikator->nilai ?? 0) >= 70)
                                            <span class="badge badge-warning">Perlu Perhatian</span>
                                        @else
                                            <span class="badge badge-danger">Tidak Tercapai</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('dataKinerja.indikator', $indikator->id) }}" class="btn btn-sm btn-primary">
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
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data untuk chart
        const trendData = @json($trendBidang);

        if (trendData && trendData.length > 0 && typeof ApexCharts !== 'undefined') {
            const options = {
                series: [{
                    name: 'Nilai Bidang',
                    data: trendData.map(item => item.nilai)
                }],
                chart: {
                    type: 'line',
                    height: 350,
                    fontFamily: 'Poppins, sans-serif',
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                colors: ['#1cc88a'],
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val.toFixed(1) + '%';
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: trendData.map(item => item.bulan),
                    labels: {
                        rotate: -45,
                        rotateAlways: false,
                        style: {
                            fontSize: '10px'
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
                    strokeDashArray: 4,
                    xaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                markers: {
                    size: 5,
                    colors: ['#fff'],
                    strokeColors: '#1cc88a',
                    strokeWidth: 2,
                    hover: {
                        size: 7
                    }
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
                }
            };

            const chart = new ApexCharts(document.querySelector('#trendChart'), options);
            chart.render();
            document.querySelector('.loading-chart').style.display = 'none';
        }

        // Inisialisasi DataTable
        if ($.fn.dataTable) {
            $('#dataTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
                }
            });
        }
    });
</script>
@endsection
