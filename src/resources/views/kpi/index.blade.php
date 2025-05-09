@extends('layouts.app')

@section('title', 'Data KPI')
@section('page_title', 'DATA KPI')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Data Kinerja</h5>
            <div>
                <a href="{{ route('kpi.create') }}" class="btn btn-primary">Tambah Data</a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('kpi.index') }}">
                        <div class="input-group">
                            <select name="tahun" class="form-control">
                                @for($i = date('Y'); $i >= 2020; $i--)
                                    <option value="{{ $i }}" {{ request('tahun', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                            <select name="bulan" class="form-control">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ request('bulan', date('m')) == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                                @endfor
                            </select>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Bidang</th>
                            <th>Indikator</th>
                            <th>Target</th>
                            <th>Realisasi</th>
                            <th>Persentase</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pilars ?? [] as $pilar)
                            @foreach($pilar->indikators as $indikator)
                                <tr>
                                    <td>{{ $indikator->bidang->nama }}</td>
                                    <td>{{ $indikator->kode }} - {{ $indikator->nama }}</td>
                                    <td>{{ $indikator->target }} {{ $indikator->satuan }}</td>
                                    <td>{{ $indikator->nilai_absolut }} {{ $indikator->satuan }}</td>
                                    <td>
                                        <div class="progress">
                                            @php
                                                $progressClass = 'bg-danger';
                                                if ($indikator->nilai_persentase >= 70) {
                                                    $progressClass = 'bg-success';
                                                } elseif ($indikator->nilai_persentase >= 50) {
                                                    $progressClass = 'bg-warning';
                                                }
                                            @endphp
                                            <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $indikator->nilai_persentase }}%" aria-valuenow="{{ $indikator->nilai_persentase }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        {{ number_format($indikator->nilai_persentase, 2) }}%
                                    </td>
                                    <td>
                                        @if($indikator->diverifikasi)
                                            <span class="badge bg-success">Diverifikasi</span>
                                        @else
                                            <span class="badge bg-warning">Belum Diverifikasi</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('kpi.show', $indikator->id) }}" class="btn btn-sm btn-info">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data KPI untuk ditampilkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
