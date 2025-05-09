@extends('layouts.app')

@section('title', 'Data KPI - Karyawan')
@section('page_title', 'DATA KPI')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Data Kinerja Organisasi</h5>
            <div>
                <form method="GET" action="{{ route('kpi.index') }}" class="d-flex">
                    <select name="tahun" class="form-control form-control-sm mr-2">
                        @for($i = date('Y'); $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ request('tahun', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                    <select name="bulan" class="form-control form-control-sm mr-2">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('bulan', date('m')) == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">Ringkasan Kinerja</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="mb-0">{{ number_format($bidangData->avg('nilai'), 2) }}%</h2>
                                    <p class="text-muted">NKO Rata-rata</p>
                                </div>
                                <div>
                                    <canvas id="overallChart" width="150" height="150"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0">Bidang Terbaik</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $bestBidang = $bidangData->sortByDesc('nilai')->first();
                            @endphp
                            @if($bestBidang)
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="mb-0">{{ $bestBidang['nama'] }}</h2>
                                    <p class="text-success">{{ number_format($bestBidang['nilai'], 2) }}%</p>
                                </div>
                                <div class="trophy">
                                    <i class="fas fa-trophy fa-3x text-warning"></i>
                                </div>
                            </div>
                            @else
                            <p>Tidak ada data</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Bidang</th>
                            <th>Nilai Kinerja</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bidangData as $item)
                            <tr>
                                <td>{{ $item['nama'] }}</td>
                                <td>
                                    <div class="progress">
                                        @php
                                            $progressClass = 'bg-danger';
                                            if ($item['nilai'] >= 70) {
                                                $progressClass = 'bg-success';
                                            } elseif ($item['nilai'] >= 50) {
                                                $progressClass = 'bg-warning';
                                            }
                                        @endphp
                                        <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $item['nilai'] }}%" aria-valuenow="{{ $item['nilai'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    {{ number_format($item['nilai'], 2) }}%
                                </td>
                                <td>
                                    @if($item['nilai'] >= 70)
                                        <span class="badge bg-success">Baik</span>
                                    @elseif($item['nilai'] >= 50)
                                        <span class="badge bg-warning">Cukup</span>
                                    @else
                                        <span class="badge bg-danger">Perlu Perhatian</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const avgValue = {{ $bidangData->avg('nilai') }};

        // Determine color based on value
        let gaugeColor = '#F44336'; // Red for low values
        if (avgValue >= 70) {
            gaugeColor = '#4CAF50'; // Green for high values
        } else if (avgValue >= 50) {
            gaugeColor = '#FFC107'; // Yellow for medium values
        }

        // Overall performance chart
        const ctx = document.getElementById('overallChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [avgValue, 100 - avgValue],
                    backgroundColor: [
                        gaugeColor,
                        'rgba(200, 200, 200, 0.1)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            }
        });
    });
</script>
@endsection
