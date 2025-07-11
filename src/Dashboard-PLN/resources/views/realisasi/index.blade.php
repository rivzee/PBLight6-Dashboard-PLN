@extends('layouts.app')

@section('title', 'Realisasi KPI')
@section('page_title', 'REALISASI KPI')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/realisasi.css') }}">
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
                                <th>Bobot</th>
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
                                        $nilaiAkhir = $realisasi?->nilai_akhir ?? 0;


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
                                        <strong>{{ $bobot }}</strong>
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
