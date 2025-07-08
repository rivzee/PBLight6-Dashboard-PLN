@extends('layouts.app')

@section('title', 'Realisasi KPI')
@section('page_title', 'REALISASI KPI')

@section('styles')
<style>
    /* --- Styles mirip sebelumnya --- */
    .dashboard-content {
        max-width: 1800px;
        margin: 0 auto;
        padding: 0 15px;
    }
    .page-header {
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-light-blue));
        color: white;
        border-radius: 12px;
        padding: 20px 25px;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(0,123,255,0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .page-header h2 { font-size: 1.5rem; font-weight: 600; margin: 0; }
    .page-header-subtitle { margin-top: 5px; font-weight: 400; font-size: 0.9rem; opacity: 0.9; }
    .page-header-actions { display: flex; gap: 10px; }
    .page-header-badge {
        background: rgba(255,255,255,0.2);
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 0.8rem;
        display: flex; align-items: center;
    }
    .page-header-badge i { margin-right: 5px; }
    .filter-card, .table-card {
        background: var(--pln-surface);
        border-radius: 16px;
        margin-bottom: 25px;
        position: relative;
        overflow: hidden;
        border: 1px solid var(--pln-border);
        box-shadow: 0 8px 20px var(--pln-shadow);
        padding: 20px;
    }
    .filter-card::before, .table-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 4px;
        background: linear-gradient(90deg, var(--pln-blue), var(--pln-light-blue));
    }
    .table-card .card-header {
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-light-blue));
        color: white;
        border: none;
        border-radius: 16px 16px 0 0;
        padding: 15px 20px;
        margin: -20px -20px 15px -20px;
        font-weight: 600;
        font-size: 1rem;
    }
    .data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        color: var(--pln-text);
    }
    .data-table th, .data-table td {
        padding: 15px;
        border-bottom: 1px solid var(--pln-border);
        vertical-align: middle;
    }
    .data-table th {
        background-color: var(--pln-accent-bg);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }
    .data-table tbody tr:hover {
        background-color: var(--pln-accent-bg);
    }
    .progress-wrapper { display: flex; flex-direction: column; gap: 5px; }
    .progress {
        height: 8px;
        border-radius: 4px;
        background-color: var(--pln-accent-bg);
        overflow: hidden;
    }
    .progress-bar.bg-danger { background-color: #dc3545; }
    .progress-bar.bg-warning { background-color: #ffc107; }
    .progress-bar.bg-success { background-color: #28a745; }
    .progress-value {
        font-weight: 600;
        font-size: 0.9rem;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .status-badge i { margin-right: 5px; }
    .status-badge.verified {
        background-color: rgba(40,167,69,0.15);
        color: #28a745;
    }
    .status-badge.pending {
        background-color: rgba(255,193,7,0.15);
        color: #ffc107;
    }
    .status-badge.not-input {
        background-color: rgba(220,53,69,0.15);
        color: #dc3545;
    }
    .action-buttons {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }
    .action-buttons .btn {
        border-radius: 50px;
        padding: 5px 15px;
        font-size: 0.75rem;
        white-space: nowrap;
    }
    .pagination {
        display: flex;
        justify-content: center;
        list-style: none;
        padding-left: 0;
        gap: 6px;
        margin-top: 1rem;
    }

    .pagination li {
        display: inline;
    }

    .pagination a,
    .pagination span {
        padding: 6px 12px;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        text-decoration: none;
        font-size: 14px;
        color: #0d6efd;
        transition: background-color 0.2s, color 0.2s;
    }

    .pagination a:hover {
        background-color: #e9ecef;
        color: #0a58ca;
    }

    .pagination .current {
        background-color: #0d6efd;
        color: #fff;
        font-weight: bold;
        border-color: #0d6efd;
    }

    .pagination .disabled {
        color: #6c757d;
        cursor: not-allowed;
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }

    .pagination .dots {
        color: #6c757d;
        background-color: transparent;
        border: none;
        padding: 6px 12px;
    }

    .pagination .prev,
    .pagination .next {
        font-weight: bold;
    }
    @media (max-width: 992px) {
        .filter-group { flex-direction: column; }
    }
    @media (max-width: 768px) {
        .page-header { flex-direction: column; align-items: flex-start; gap: 10px; }
        .page-header-actions { width: 100%; justify-content: flex-start; margin-top: 10px; }
    }
</style>
@endsection

@section('content')
<div class="dashboard-content">
    <!-- Header -->
    <div class="page-header">
        <div>
            <h2><i class="fas fa-chart-line me-2"></i>Realisasi KPI</h2>
            <div class="page-header-subtitle">
                Pengelolaan data realisasi kinerja {{ $isMaster ? 'per pilar' : 'per bidang' }}
            </div>
        </div>
    </div>

    @include('components.alert')

    <!-- Filter Form -->
    <div class="filter-card">
        <h5><i class="fas fa-filter me-2"></i>Filter Data Realisasi</h5>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Informasi:</strong>
            <ul class="mb-0 mt-2">
                <li><strong>Target Kumulatif:</strong> Target yang harus dicapai dari Januari hingga bulan yang dipilih</li>
                <li><strong>Realisasi Harian:</strong> Data realisasi yang diinput untuk tanggal yang dipilih (bukan kumulatif)</li>
                <li><strong>Capaian:</strong> Persentase realisasi harian terhadap target kumulatif</li>
            </ul>
        </div>
        <form method="GET" action="{{ route('realisasi.index') }}" class="mb-3">
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <label for="tanggal" class="col-form-label">Pilih Tanggal</label>
                </div>
                <div class="col-auto">
                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $tanggal }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Data Tabel -->
    @foreach($grouped as $kode => $group)
        <div class="table-card mt-4">
            <div class="card-header">
                {{ $isMaster ? 'Pilar' : 'Bidang' }} {{ $kode }} - {{ $group['nama'] }}
                <small class="float-end">Target Kumulatif s.d {{ \Carbon\Carbon::parse($tanggal)->locale('id')->monthName }} {{ \Carbon\Carbon::parse($tanggal)->year }}</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Indikator</th>
                                @if($isMaster)
                                    <th>Bidang</th>
                                @endif
                                <th>Target Kumulatif</th>
                                <th>Realisasi Harian</th>
                                <th>Capaian</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($group['indikators'] as $index => $indikator)
                                @php
                                    $realisasi = $indikator->firstRealisasi;
                                    $target = $indikator->target_nilai;
                                    $nilaiRealisasi = $realisasi?->nilai ?? 0;

                                    $persentaseAsli = $target > 0 ? ($nilaiRealisasi / $target) * 100 : 0;
                                    $persentase = min($persentaseAsli, 110);

                                    if ($persentase < 95) {
                                        $progressClass = 'bg-danger';
                                    } elseif ($persentase >= 95 && $persentase < 100) {
                                        $progressClass = 'bg-warning';
                                    } else {
                                        $progressClass = 'bg-success';
                                    }

                                    $query = ['tanggal' => $tanggal];
                                @endphp

                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $indikator->kode }}</td>
                                    <td>{{ $indikator->nama }}</td>
                                    @if($isMaster)
                                        <td>{{ $indikator->bidang->nama ?? '-' }}</td>
                                    @endif
                                    <td>{{ number_format($target, 2) }}</td>
                                    <td>
                                        {{ number_format($nilaiRealisasi, 2) }}
                                        @if($realisasi)
                                            <div class="small text-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Data tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}
                                            </div>
                                        @else
                                            <div class="small text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Belum ada data
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="progress-wrapper">
                                            <div class="progress">
                                                <div class="progress-bar {{ $progressClass }}" style="width: {{ min($persentase, 100) }}%;"></div>
                                            </div>
                                            <div class="progress-value">{{ number_format($persentase, 2) }}%</div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($realisasi)
                                            @if($realisasi->diverifikasi)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Terverifikasi
                                                </span>
                                                <div class="small text-muted">
                                                    {{ \Carbon\Carbon::parse($realisasi->updated_at)->format('d M Y H:i') }}
                                                </div>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Belum Diverifikasi
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-minus-circle me-1"></i>
                                                Belum Input
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @if($realisasi)
                                                <a href="{{ route('realisasi.edit', array_merge(['indikator' => $indikator->id], $query)) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            @else
                                                <a href="{{ route('realisasi.create', array_merge(['indikator' => $indikator->id], $query)) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-plus"></i> Input
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach

    @if(empty($grouped))
        <div class="table-card">
            <div class="card-body text-center">
                <i class="fas fa-info-circle text-muted mb-3" style="font-size: 3rem;"></i>
                <h5 class="text-muted">Tidak ada data</h5>
                <p class="text-muted">Belum ada indikator yang tersedia untuk tanggal yang dipilih.</p>
            </div>
        </div>
    @endif
</div>
@endsection
