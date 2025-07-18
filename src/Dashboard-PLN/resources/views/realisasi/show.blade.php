@extends('layouts.app')

@section('title', 'Detail Realisasi KPI')
@section('page_title', 'DETAIL REALISASI KPI')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/realisasi.css') }}">
@endsection

@section('content')
<div class="dashboard-content">
    <!-- Modern Page Header -->
    <div class="page-header">
        <div>
            <h2><i class="fas fa-chart-bar me-2"></i>Detail Realisasi KPI</h2>
            <div class="page-header-subtitle">
                Informasi lengkap realisasi bulanan untuk periode:
                {{ \Carbon\Carbon::create(null, $nilaiKPI->bulan, 1)->locale('id')->monthName }} {{ $nilaiKPI->tahun }}
            </div>
        </div>
        <div class="page-header-actions">
            @if($nilaiKPI->diverifikasi)
                <div class="page-header-badge">
                    <i class="fas fa-check-circle"></i> Sudah Diverifikasi
                </div>
            @else
                <div class="page-header-badge">
                    <i class="fas fa-clock"></i> Menunggu Verifikasi
                </div>
            @endif
            <a href="{{ route('realisasi.index', ['tahun' => $nilaiKPI->tahun, 'bulan' => $nilaiKPI->bulan]) }}" class="btn btn-light">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    @include('components.alert')

    <div class="detail-card">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold">{{ $nilaiKPI->indikator->kode }} - {{ $nilaiKPI->indikator->nama }}</h6>
            @if($nilaiKPI->diverifikasi)
                <span class="status-badge verified">
                    <i class="fas fa-check-circle"></i> Sudah Diverifikasi
                </span>
            @else
                <span class="status-badge pending">
                    <i class="fas fa-clock"></i> Menunggu Verifikasi
                </span>
            @endif
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-section">
                        <h4><i class="fas fa-info-circle me-2"></i>Informasi Indikator</h4>
                        <table class="info-table">
                            <tr>
                                <th>Kode</th>
                                <td>{{ $nilaiKPI->indikator->kode }}</td>
                            </tr>
                            <tr>
                                <th>Nama Indikator</th>
                                <td>{{ $nilaiKPI->indikator->nama }}</td>
                            </tr>
                            <tr>
                                <th>Bidang</th>
                                <td>{{ $nilaiKPI->indikator->bidang->nama }}</td>
                            </tr>
                            <tr>
                                <th>Target Bulanan</th>
                                <td>{{ number_format($realisasi->target ?? 0, 2) }} {{ $nilaiKPI->indikator->satuan }}</td>
                            </tr>
                            <tr>
                                <th>Jenis Polaritas</th>
                                <td>
                                    @if($realisasi->jenis_polaritas == 'positif')
                                        <span class="badge bg-success">Positif</span>
                                    @else
                                        <span class="badge bg-warning">Negatif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Nilai Polaritas</th>
                                <td>{{ number_format($realisasi->nilai_polaritas ?? 0, 2) }}%</td>
                            </tr>
                            <tr>
                                <th>Arah Pencapaian</th>
                                <td>
                                    @php
                                        $arrow = '→';
                                        if ($realisasi->jenis_polaritas == 'positif') {
                                            $arrow = ($nilaiKPI->nilai / $realisasi->target) * 100 >= 100 ? '↑' : '↓';
                                        } elseif ($realisasi->jenis_polaritas == 'negatif') {
                                            $arrow = $nilaiKPI->nilai <= $realisasi->target ? '↑' : '↓';
                                        } elseif ($realisasi->jenis_polaritas == 'netral') {
                                            $arrow = abs($nilaiKPI->nilai - $realisasi->target) / $realisasi->target <= 0.05 ? '→' : '↓';
                                        }

                                        $arrowIcon = $arrow == '↑' ? '<i class="fas fa-arrow-up text-success"></i>' :
                                                    ($arrow == '↓' ? '<i class="fas fa-arrow-down text-danger"></i>' :
                                                    '<i class="fas fa-arrows-alt-h text-info"></i>');
                                    @endphp
                                    {!! $arrowIcon !!}
                                </td>
                            </tr>

                            @if($nilaiKPI->indikator->deskripsi)
                            <tr>
                                <th>Deskripsi</th>
                                <td>{{ $nilaiKPI->indikator->deskripsi }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="info-section">
                        <h4><i class="fas fa-chart-line me-2"></i>Informasi Realisasi</h4>
                        <table class="info-table">
                            <tr>
                                <th>Periode</th>
                                <td>
                                    {{ \Carbon\Carbon::create(null, $nilaiKPI->bulan, 1)->locale('id')->monthName }} {{ $nilaiKPI->tahun }}
                                </td>
                            </tr>
                            <tr>
                                <th>Realisasi Bulanan</th>
                                <td>{{ number_format($nilaiKPI->nilai, 2) }} {{ $nilaiKPI->indikator->satuan }}</td>
                            </tr>
                            <tr>
                                <th>Persentase</th>
                                <td>
                                    @php
                                        $percentage = $nilaiKPI->persentase;
                                        $progressClass = 'bg-danger';
                                        if ($percentage >= 90) {
                                            $progressClass = 'bg-success';
                                        } elseif ($percentage >= 70) {
                                            $progressClass = 'bg-warning';
                                        }
                                    @endphp
                                    <div class="progress-visual">
                                        <div class="progress-bar-custom {{ $progressClass }}" style="width: {{ min($percentage, 100) }}%"></div>
                                        <div class="progress-label">{{ number_format($percentage, 2) }}%</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if($nilaiKPI->diverifikasi)
                                        <span class="status-badge verified">
                                            <i class="fas fa-check-circle"></i> Diverifikasi
                                        </span>
                                    @else
                                        <span class="status-badge pending">
                                            <i class="fas fa-clock"></i> Belum Diverifikasi
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Keterangan</th>
                                <td>{{ $nilaiKPI->keterangan ?: '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="info-section">
                        <h4><i class="fas fa-history me-2"></i>Riwayat</h4>
                        <table class="info-table">
                            <tr>
                                <th>Diinput Oleh</th>
                                <td>{{ $nilaiKPI->user->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Input</th>
                                <td>{{ $nilaiKPI->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @if($nilaiKPI->diverifikasi)
                            <tr>
                                <th>Diverifikasi Oleh</th>
                                <td>{{ $nilaiKPI->verifikator->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Verifikasi</th>
                                <td>{{ $nilaiKPI->verifikasi_pada ? $nilaiKPI->verifikasi_pada->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <div class="detail-actions">
                <a href="{{ route('realisasi.edit', ['id' => $nilaiKPI->id, 'tahun' => $nilaiKPI->tahun, 'bulan' => $nilaiKPI->bulan]) }}" class="btn btn-warning btn-action">
                    <i class="fas fa-edit"></i> Edit Realisasi
                </a>
                @if(auth()->user()->isMasterAdmin() && !$nilaiKPI->diverifikasi)
                    <a href="{{ route('realisasi.verify', $nilaiKPI->id) }}" class="btn btn-success btn-action">
                        <i class="fas fa-check-circle"></i> Verifikasi
                    </a>
                @endif
                @if(auth()->user()->isMasterAdmin() && $nilaiKPI->diverifikasi)
                    <a href="{{ route('realisasi.unverify', $nilaiKPI->id) }}" class="btn btn-danger btn-action">
                        <i class="fas fa-times-circle"></i> Batalkan Verifikasi
                    </a>
                @endif
                <a href="{{ route('realisasi.index', ['tahun' => $nilaiKPI->tahun, 'bulan' => $nilaiKPI->bulan]) }}" class="btn btn-secondary btn-action">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
