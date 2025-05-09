@extends('layouts.app')

@section('title', 'Detail KPI')
@section('page_title', 'DETAIL KPI')

@section('content')
<div class="container">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Detail Indikator KPI</h5>
            <div>
                <a href="{{ route('kpi.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
        <div class="card-body">
            @if(isset($indikator))
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-striped">
                            <tr>
                                <th>Kode</th>
                                <td>{{ $indikator->kode }}</td>
                            </tr>
                            <tr>
                                <th>Nama Indikator</th>
                                <td>{{ $indikator->nama }}</td>
                            </tr>
                            <tr>
                                <th>Bidang</th>
                                <td>{{ $indikator->bidang->nama }}</td>
                            </tr>
                            <tr>
                                <th>Pilar</th>
                                <td>{{ $indikator->pilar->nama }}</td>
                            </tr>
                            <tr>
                                <th>Deskripsi</th>
                                <td>{{ $indikator->deskripsi }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-striped">
                            <tr>
                                <th>Target</th>
                                <td>{{ $indikator->target }} {{ $indikator->satuan }}</td>
                            </tr>
                            <tr>
                                <th>Satuan</th>
                                <td>{{ $indikator->satuan }}</td>
                            </tr>
                            <tr>
                                <th>Tipe</th>
                                <td>{{ ucfirst($indikator->tipe) }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>{{ $indikator->aktif ? 'Aktif' : 'Non-aktif' }}</td>
                            </tr>
                            <tr>
                                <th>Bobot</th>
                                <td>{{ $indikator->bobot }}%</td>
                            </tr>
                        </table>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    Data indikator tidak ditemukan.
                </div>
            @endif
        </div>
    </div>

    @if(isset($nilaiKPIs) && $nilaiKPIs->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Histori Nilai KPI</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Periode</th>
                                <th>Nilai</th>
                                <th>Persentase</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Diperbarui Oleh</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($nilaiKPIs as $nilai)
                                <tr>
                                    <td>
                                        @if($nilai->periode_tipe == 'bulanan')
                                            {{ date('F Y', mktime(0, 0, 0, $nilai->bulan, 1, $nilai->tahun)) }}
                                        @else
                                            Minggu {{ $nilai->minggu }}, {{ date('F Y', mktime(0, 0, 0, $nilai->bulan, 1, $nilai->tahun)) }}
                                        @endif
                                    </td>
                                    <td>{{ $nilai->nilai }} {{ $indikator->satuan }}</td>
                                    <td>
                                        <div class="progress">
                                            @php
                                                $progressClass = 'bg-danger';
                                                if ($nilai->persentase >= 70) {
                                                    $progressClass = 'bg-success';
                                                } elseif ($nilai->persentase >= 50) {
                                                    $progressClass = 'bg-warning';
                                                }
                                            @endphp
                                            <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $nilai->persentase }}%" aria-valuenow="{{ $nilai->persentase }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        {{ number_format($nilai->persentase, 2) }}%
                                    </td>
                                    <td>
                                        @if($nilai->diverifikasi)
                                            <span class="badge bg-success">Diverifikasi</span>
                                        @else
                                            <span class="badge bg-warning">Belum Diverifikasi</span>
                                        @endif
                                    </td>
                                    <td>{{ $nilai->keterangan ?: '-' }}</td>
                                    <td>{{ $nilai->user->name }}</td>
                                    <td>{{ $nilai->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            Belum ada data nilai KPI untuk indikator ini.
        </div>
    @endif
</div>
@endsection
