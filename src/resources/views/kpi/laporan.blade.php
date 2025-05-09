@extends('layouts.app')

@section('title', 'Laporan KPI')
@section('page_title', 'LAPORAN KPI')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Laporan Kinerja</h5>
            <div>
                <button class="btn btn-success" id="exportBtn">Ekspor Excel</button>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('kpi.laporan') }}" id="filterForm">
                        <div class="input-group">
                            <select name="tahun" class="form-control">
                                @for($i = date('Y'); $i >= 2020; $i--)
                                    <option value="{{ $i }}" {{ request('tahun', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                            <select name="bulan" class="form-control">
                                <option value="">-- Semua Bulan --</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                                @endfor
                            </select>
                            <select name="bidang_id" class="form-control">
                                <option value="">-- Semua Bidang --</option>
                                @foreach($bidangs ?? [] as $bidang)
                                    <option value="{{ $bidang->id }}" {{ request('bidang_id') == $bidang->id ? 'selected' : '' }}>{{ $bidang->nama }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="laporanTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pilar</th>
                            <th>Bidang</th>
                            <th>Indikator</th>
                            <th>Target</th>
                            <th>Realisasi</th>
                            <th>Persentase</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporanData ?? [] as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item['pilar'] }}</td>
                                <td>{{ $item['bidang'] }}</td>
                                <td>{{ $item['kode'] }} - {{ $item['indikator'] }}</td>
                                <td>{{ $item['target'] }} {{ $item['satuan'] }}</td>
                                <td>{{ $item['realisasi'] }} {{ $item['satuan'] }}</td>
                                <td>
                                    <div class="progress">
                                        @php
                                            $progressClass = 'bg-danger';
                                            if ($item['persentase'] >= 70) {
                                                $progressClass = 'bg-success';
                                            } elseif ($item['persentase'] >= 50) {
                                                $progressClass = 'bg-warning';
                                            }
                                        @endphp
                                        <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $item['persentase'] }}%" aria-valuenow="{{ $item['persentase'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    {{ number_format($item['persentase'], 2) }}%
                                </td>
                                <td>
                                    @if($item['diverifikasi'])
                                        <span class="badge bg-success">Diverifikasi</span>
                                    @else
                                        <span class="badge bg-warning">Belum Diverifikasi</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data laporan untuk ditampilkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Export to Excel
        document.getElementById('exportBtn').addEventListener('click', function() {
            const form = document.getElementById('filterForm');
            const url = new URL(form.action);

            // Get form values
            const formData = new FormData(form);
            for (const [key, value] of formData.entries()) {
                if (value) url.searchParams.append(key, value);
            }

            // Add export parameter
            url.searchParams.append('export', 'excel');

            // Redirect to export URL
            window.location.href = url.toString();
        });
    });
</script>
@endsection
