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
        font-size: 0.9rem;
    }
    .data-table th, .data-table td {
        padding: 12px 8px;
        border-bottom: 1px solid var(--pln-border);
        vertical-align: middle;
    }
    .data-table th {
        background-color: var(--pln-accent-bg);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.05em;
        text-align: center;
        white-space: nowrap;
    }
    .data-table tbody tr:hover {
        background-color: var(--pln-accent-bg);
    }
    .data-table tbody tr:nth-child(even) {
        background-color: rgba(0,0,0,0.02);
    }

    /* Specific column widths */
    .data-table th:nth-child(1), .data-table td:nth-child(1) { width: 40px; text-align: center; }
    .data-table th:nth-child(2), .data-table td:nth-child(2) { width: 80px; text-align: center; }
    .data-table th:nth-child(3), .data-table td:nth-child(3) { min-width: 200px; }
    .data-table th:nth-child(4), .data-table td:nth-child(4) { width: 120px; text-align: center; }
    .data-table th:nth-child(5), .data-table td:nth-child(5) { width: 60px; text-align: center; }
    .data-table th:nth-child(6), .data-table td:nth-child(6) { width: 80px; text-align: center; }
    .data-table th:nth-child(7), .data-table td:nth-child(7) { width: 100px; text-align: right; }
    .data-table th:nth-child(8), .data-table td:nth-child(8) { width: 100px; text-align: right; }
    .data-table th:nth-child(9), .data-table td:nth-child(9) { width: 120px; text-align: center; }
    .data-table th:nth-child(10), .data-table td:nth-child(10) { width: 80px; text-align: center; }
    .data-table th:nth-child(11), .data-table td:nth-child(11) { width: 130px; text-align: center; }
    .data-table th:nth-child(12), .data-table td:nth-child(12) { width: 120px; text-align: center; }
    .data-table th:nth-child(13), .data-table td:nth-child(13) { width: 100px; text-align: center; }
    .progress-wrapper {
        display: flex;
        flex-direction: column;
        gap: 3px;
        min-width: 100px;
    }
    .progress {
        height: 6px;
        border-radius: 3px;
        background-color: var(--pln-accent-bg);
        overflow: hidden;
    }
    .progress-value {
        font-weight: 600;
        font-size: 0.8rem;
        text-align: center;
    }

    /* Badge improvements */
    .badge {
        font-size: 0.7rem;
        padding: 0.3em 0.6em;
        white-space: nowrap;
    }

    /* Button improvements */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.7rem;
        border-radius: 0.2rem;
    }

    /* Polaritas icon improvements */
    .polaritas-icon {
        font-size: 1.1rem;
        margin-bottom: 0;
    }

    /* Polaritas icon styling */
    .polaritas-icon {
        font-size: 1.2rem;
        margin-bottom: 2px;
    }

    /* Badge styling untuk keterangan */
    .badge.rounded-pill {
        padding: 0.4em 0.8em;
        font-size: 0.75rem;
        font-weight: 500;
    }

    /* Nilai calculation styling */
    .nilai-detail {
        font-size: 0.7rem;
        color: #6c757d;
        margin-top: 2px;
    }

    /* Icon colors for polaritas */
    .text-success { color: #28a745 !important; }
    .text-danger { color: #dc3545 !important; }
    .text-info { color: #17a2b8 !important; }
    .progress-bar.bg-danger { background-color: #dc3545; }
    .progress-bar.bg-warning { background-color: #ffc107; }
    .progress-bar.bg-success { background-color: #28a745; }
    .progress-bar.bg-secondary { background-color: #6c757d; }
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
        .data-table { font-size: 0.8rem; }
        .data-table th, .data-table td { padding: 8px 4px; }
    }
    @media (max-width: 768px) {
        .page-header { flex-direction: column; align-items: flex-start; gap: 10px; }
        .page-header-actions { width: 100%; justify-content: flex-start; margin-top: 10px; }
        .data-table { font-size: 0.75rem; }
        .data-table th, .data-table td { padding: 6px 3px; }
        .progress-wrapper { min-width: 80px; }
        .badge { font-size: 0.6rem; }
    }
</style>
@endsection

@section('content')
<div class="dashboard-content">
    <!-- Header -->
    <div class="page-header">
        <div>
            <h2><i class="fas fa-chart-line me-2"></i>Realisasi KPI</h2>
        </div>
    </div>

    @include('components.alert')

    <!-- Filter Form -->
    <div class="filter-card">
        <form method="GET" action="{{ route('realisasi.index') }}" class="mb-3">
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <label for="tahun" class="col-form-label">Tahun</label>
                </div>
                <div class="col-auto">
                    <select name="tahun" id="tahun" class="form-select">
                        @foreach(\App\Http\Controllers\RealisasiController::getDaftarTahun() as $key => $value)
                            <option value="{{ $key }}" {{ $key == $tahun ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label for="bulan" class="col-form-label">Bulan</label>
                </div>
                <div class="col-auto">
                    <select name="bulan" id="bulan" class="form-select">
                        @foreach(\App\Http\Controllers\RealisasiController::getDaftarBulan() as $key => $value)
                            <option value="{{ $key }}" {{ $key == $bulan ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Cari
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Data Tabel -->
    @foreach($grouped as $kode => $group)
        <div class="table-card mt-4">
            <div class="card-header">
                {{ $isMaster ? 'Pilar' : 'Bidang' }} {{ $kode }} - {{ $group['nama'] }}
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
                                <th>Polaritas</th>
                                <th>Bobot (%)</th>
                                <th>Target</th>
                                <th>Realisasi</th>
                                <th>Capaian</th>
                                <th>Nilai</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($group['indikators'] as $index => $indikator)
                                @php
                                    $realisasi = $indikator->firstRealisasi;
                                    $target = $indikator->target_nilai;
                                    $nilaiRealisasi = $realisasi?->nilai ?? 0;
                                    $bobot = $indikator->bobot ?? 0;

                                    // Ambil polaritas dari data realisasi yang sudah diinput
                                    // Default netral jika tidak ada realisasi
                                    $polaritas = 'netral';

                                    if ($realisasi && $realisasi->jenis_polaritas) {
                                        if ($realisasi->jenis_polaritas === 'positif') {
                                            $polaritas = 'up'; // naik = bagus
                                        } elseif ($realisasi->jenis_polaritas === 'negatif') {
                                            $polaritas = 'down'; // turun = bagus
                                        } else {
                                            $polaritas = 'flat'; // netral
                                        }
                                    }


                                    if ($target > 0 && $nilaiRealisasi >= 0) {
                                        if ($polaritas === 'up') {
                                            $persentaseAsli = ($nilaiRealisasi / $target) * 100;
                                        } elseif ($polaritas === 'down') {
                                            $persentaseAsli = (2 - ($nilaiRealisasi / $target)) * 100;
                                        } else { // netral
                                            $deviasi = abs($nilaiRealisasi - $target) / $target;
                                            $persentaseAsli = $deviasi <= 0.05 ? 100 : 0;
                                        }
                                    } else {
                                        $persentaseAsli = 0;
                                    }

                                    $persentase = min(max($persentaseAsli, 0), 110); // nilai minimum 0 dan maksimum 110

                                    // Hitung nilai berdasarkan polaritas
                                    $nilaiIndikator = 0;
                                    $nilaiAkhir = 0;
                                    $keterangan = 'Masalah';
                                    $keteranganClass = 'bg-danger';

                                    if ($target > 0 && $nilaiRealisasi >= 0) {
                                        if ($polaritas === 'up') {
                                            // Positif → realisasi / target
                                            $nilaiIndikator = min(max($nilaiRealisasi / $target, 0), 1.1);
                                        } elseif ($polaritas === 'down') {
                                            // Negatif → (2 - realisasi/target)
                                            $nilaiIndikator = min(max(2 - ($nilaiRealisasi / $target), 0), 1.1);
                                        } else { // flat (netral)
                                            $nilaiIndikator = ($nilaiRealisasi == $target) ? 1 : 0;
                                        }

                                        // Nilai akhir dikalikan bobot
                                        $nilaiAkhir = $nilaiIndikator * $bobot;

                                        // Tentukan keterangan berdasarkan nilai indikator
                                        // Tentukan keterangan berdasarkan persentase capaian
                                        if ($persentase < 95) {
                                            $keterangan = 'Masalah';
                                            $keteranganClass = 'bg-danger';
                                        } elseif ($persentase >= 95 && $persentase < 100) {
                                            $keterangan = 'Hati-hati';
                                            $keteranganClass = 'bg-warning';
                                        } else {
                                            $keterangan = 'Baik';
                                            $keteranganClass = 'bg-success';
                                        }

                                    } else {
                                        $persentase = 0;
                                    }

                                    // Warna progress bar berdasarkan ketentuan NKO (untuk konsistensi visual)
                                    if ($nilaiRealisasi <= 0) {
                                        $progressClass = 'bg-secondary'; // Abu-abu untuk belum diukur
                                    } elseif ($persentase >= 100) {
                                        $progressClass = 'bg-success'; // Hijau untuk tercapai
                                    } elseif ($persentase >= 95) {
                                        $progressClass = 'bg-warning'; // Kuning untuk hampir tercapai
                                    } else {
                                        $progressClass = 'bg-danger'; // Merah untuk perlu peningkatan
                                    }

                                    $query = ['tahun' => $tahun, 'bulan' => $bulan];
                                @endphp

                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $indikator->kode }}</td>
                                    <td>{{ $indikator->nama }}</td>
                                    @if($isMaster)
                                        <td>{{ $indikator->bidang->nama ?? '-' }}</td>
                                    @endif
                                   @php
                                        $ikonArah = '<i class="fas fa-arrows-alt-h text-info" title="Capaian stabil/netral"></i>'; // default

                                        if ($realisasi && $target > 0) {
                                            $persen = ($nilaiRealisasi / $target) * 100;
                                            $deviasi = abs($nilaiRealisasi - $target) / $target;

                                            if ($realisasi->jenis_polaritas === 'positif') {
                                                if ($persen >= 100) {
                                                    $ikonArah = '<i class="fas fa-arrow-up text-success" title="Capaian naik (bagus)"></i>';
                                                } else {
                                                    $ikonArah = '<i class="fas fa-arrow-down text-danger" title="Capaian turun (buruk)"></i>';
                                                }
                                            } elseif ($realisasi->jenis_polaritas === 'negatif') {
                                                if ($nilaiRealisasi <= $target) {
                                                    $ikonArah = '<i class="fas fa-arrow-down text-success" title="Capaian turun (lebih kecil lebih baik)"></i>';
                                                } else {
                                                    $ikonArah = '<i class="fas fa-arrow-up text-danger" title="Capaian naik (lebih besar lebih buruk)"></i>';
                                                }
                                            } elseif ($realisasi->jenis_polaritas === 'netral') {
                                                if ($deviasi <= 0.05) {
                                                    $ikonArah = '<i class="fas fa-arrows-alt-h text-info" title="Capaian stabil (netral)"></i>';
                                                } else {
                                                    $ikonArah = '<i class="fas fa-arrow-down text-danger" title="Capaian menyimpang dari target (buruk)"></i>';
                                                }
                                            }
                                        }
                                    @endphp


                                    <td class="text-center">{!! $ikonArah !!}</td>

                                    <td class="text-center">
                                        <strong>{{ $bobot }}%</strong>
                                    </td>
                                    <td class="text-right">{{ number_format($target, 2, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($nilaiRealisasi, 2, ',', '.') }}</td>
                                    <td>
                                        <div class="progress-wrapper">
                                            <div class="progress">
                                                <div class="progress-bar {{ $progressClass }}" style="width: {{ min($persentase, 100) }}%;"></div>
                                            </div>
                                            <div class="progress-value">{{ number_format($persentase, 1) }}%</div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="font-weight-bold">{{ number_format($nilaiAkhir, 2, ',', '.') }}</div>
                                    </td>
                                    <td>
                                        @if($realisasi)
                                            @if($realisasi->diverifikasi)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Terverifikasi
                                                </span>
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
                                    <td class="text-center">
                                        <span class="badge {{ $keteranganClass }} rounded-pill">{{ $keterangan }}</span>
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
            </div>
        </div>
    @endif
</div>
@endsection
